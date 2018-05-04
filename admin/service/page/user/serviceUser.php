<?php
require dirname(__FILE__) . '/../../../view/common/serviceHead.php';
require $_SERVER['DOCUMENT_ROOT'] . '/service/base/baseQuery.php';

/**
 * 获取当前登录用户所属机构ID
 *
 * @return array:
 */
function getCurrDepartmentIds()
{
    $currUser = admin\service\tools\ToolsClass::getUser();
    $commonTools = new \service\tools\ToolsClass(0);
    return $commonTools->getDatasBySQL("select departmentid as id from t_User_Department_Set where userid='" . $currUser->getId() . "'");
}

/**
 * 构建机构树可编辑和打开状态
 *
 * @param array $src_array
 * @return array
 */
function buildTreeArray($src_array)
{
    $editableids = array();
    $openids = array();
    $count = count($src_array);
    for ($i = 0; $i < $count; $i++) {
        $checked = $src_array[$i]['checked'];
        if (in_array($src_array[$i]['id'], $openids)) {
            $src_array[$i]['isopen'] = 'true';
            array_push($openids, $src_array[$i]['pid']);
        }
        if ($checked === 'true') {
            array_push($openids, $src_array[$i]['pid']);
        }

        $editable = $src_array[$count - 1 - $i]['editable'];
        if (in_array($src_array[$count - 1 - $i]['pid'], $editableids)) {
            $src_array[$count - 1 - $i]['editable'] = 'true';
            array_push($editableids, $src_array[$count - 1 - $i]['id']);
        }
        if ($editable === 'true') {
            array_push($editableids, $src_array[$count - 1 - $i]['id']);
        }
    }
    return $src_array;
}

/**
 * 校验是否有权限更改机构人员信息
 *
 * @param int $highRoleLevel
 * @param array $departmentids
 * @return boolean
 */
function validateDepPower($highRoleLevel, $departmentids)
{
    $validateDepRes = true;
    if ($highRoleLevel < 1) {
        $validateDepRes = true;
    } else {
        $currDepartmentIds = getCurrDepartmentIds();
        if (count($currDepartmentIds) > 0) {
            $currDepartmentId = array();
            foreach ($currDepartmentIds as $departmentobj) {
                array_push($currDepartmentId, $departmentobj['id']);
            }
            $commonTools = new service\tools\ToolsClass(0);
            $search_str = "select * from t_Department order by levels asc,sort asc";
            $departments = $commonTools->getDatasBySQL($search_str);
            $editableids = array();
            foreach ($departments as $department) {
                if (in_array($department['parentid'], $editableids)) {
                    array_push($editableids, $department['id']);
                }
                if (in_array($department['id'], $currDepartmentId)) {
                    array_push($editableids, $department['id']);
                }
            }
            $editableids = array_unique($editableids);
            foreach ($departmentids as $departmentid) {
                if (!in_array($departmentid, $editableids)) {
                    $validateDepRes = false;
                    break;
                }
            }
        } else {
            $validateDepRes = false;
        }
    }
    return $validateDepRes;
}

/**
 * 获取机构名称
 *
 * @param string $departmentid
 * @return string
 */
function getDepartmentName($departmentid)
{
    $commonTools = new service\tools\ToolsClass(0);
    $searchResult = $commonTools->getDatasBySQL("select d.parentid,d.name from t_Department d where d.id='" . $departmentid . "'");
    $parentid = $searchResult[0]['parentid'];
    if ($parentid == 'root') {
        return $searchResult[0]['name'];
    } else {
        return getDepartmentName($parentid) . '→' . $searchResult[0]['name'];
    }
}

$result = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $commonTools = new \service\tools\ToolsClass(0);
    $connection = $commonTools->getDBConnection();
    $currUser = admin\service\tools\ToolsClass::getUser();
    $highRoleLevel = \service\user\UserManagerClass::getHighestRoleLevel($currUser->getId());
    if ($highRoleLevel == 0) {
        $highRoleLevel = $highRoleLevel - 1;
    }
    if (isset($_POST['oper'])) {
        $oper = $_POST['oper'];
        switch ($oper) {
            case "seachDepartment":
                $userid = $_POST['id'];
                $search_str = "select t.id,t.name,t.levels,t.parentid as pid,
                    case when t.id in (select st.parentid from t_Department st) then 'true' else 'false' end as isParent,
                    case when t.id in (select departmentid from t_User_Department_Set ud where ud.userid='" . $userid . "') then 'true' else 'false' end as checked,
                        'false' as isopen,";
                if ($highRoleLevel < 1) {
                    $search_str = $search_str . "'true' as editable ";
                    $currDepartmentIds = array();
                } else {
                    $currDepartmentIds = getCurrDepartmentIds();
                    $wherestr = '';
                    if (count($currDepartmentIds) > 0) {
                        foreach ($currDepartmentIds as $departmentid) {
                            if ($wherestr !== '') {
                                $wherestr = $wherestr . ',';
                            }
                            $wherestr = $wherestr . "'" . $departmentid['id'] . "'";
                        }
                    } else {
                        $wherestr = "''";
                    }
                    $search_str = $search_str . "case when t.id in (" . $wherestr . ") then 'true' else 'false' end as editable ";
                }
                $search_str = $search_str . "from t_Department t order by t.levels desc,t.sort asc";
                $result_search = $connection->doQuery($search_str)->fetchAll();
                $result_search = buildTreeArray($result_search);

                $tree = array();
                $tree[0]['id'] = 'root';
                $tree[0]['name'] = '机构';
                $tree[0]['pid'] = '0';
                $tree[0]['powerlevel'] = '0';
                $tree[0]['iconSkin'] = "departmenttree";
                $tree[0]['isParent'] = true;
                $tree[0]['nocheck'] = true;
                if (count($result_search) > 0) {
                    $tree[0]['open'] = true;
                } else {
                    $tree[0]['open'] = false;
                }
                $i = 1;
                foreach ($result_search as $row) {
                    $tree[$i]['id'] = $row['id'];
                    $tree[$i]['name'] = $row['name'];
                    $tree[$i]['powerlevel'] = $row['levels'];
                    $tree[$i]['pid'] = $row['pid'];
                    $tree[$i]['iconSkin'] = "departmenttree";
                    $tree[$i]['isParent'] = $row['isparent'] === 'true' ? true : false;
                    $tree[$i]['open'] = $row['isopen'] === 'true' ? true : false;
                    $tree[$i]['nocheck'] = $row['editable'] === 'true' ? false : true;
                    $tree[$i]['checked'] = $row['checked'] === 'true' ? true : false;
                    $i++;
                }
                $result['tree'] = $tree;

                echo json_encode($result);
                break;
            case "saveinfo":
                $currLevel = intval($currUser->getLevels());
                if (intval($_POST['level']) > $currLevel) {
                    $countnumber = intval($connection->doQuery("select count(*) as count from T_user where loginno='" . $_POST['loginno'] . "' and id<>'" . $_POST['id'] . "'")->fetchAll()[0]['count']);
                    if ($countnumber <= 0) {
                        $userid = '';
                        if (isset($_POST['id']) && $_POST['id'] && $_POST['id'] != null && $_POST['id'] != '') {
                            $userid = $_POST['id'];
                            $sqlupdate = "update T_user set name='" . $_POST['name'] . "',loginno='" . $_POST['loginno'] . "',levels=" . $_POST['level'] . ",sort=" . $_POST['sort'] . ",status=" . $_POST['status'] . " where id='" . $userid . "'";
                            $connection->addBatch($sqlupdate);
                            $connection->addBatch("delete from T_user_role_set where userid='" . $userid . "' and roleid in (select id from T_Role where levels>" . $highRoleLevel . ")");
                            if ($highRoleLevel < 1) {
                                $connection->addBatch("delete from T_user_department_set where userid='" . $userid . "'");
                            } else {
                                $currdepartmentids = \service\user\UserManagerClass::getDepartments($currUser->getId(), 0);
                                if ($currdepartmentids != '') {
                                    $currdepartmentids = "'" . str_replace(",", "','", $currdepartmentids) . "'";
                                    $connection->addBatch("delete from T_user_department_set where userid='" . $userid . "' and departmentid in (" . $currdepartmentids . ")");
                                }
                            }
                        } else {
                            $userid = \service\tools\common\UUIDClass::getUUID();
                            $adminconfig = \admin\config\AdminConfig::getInstance();
                            $sqlupdate = "insert into T_user(id,name,loginno,password,levels,sort,status) values('" . $userid . "','" . $_POST['name'] . "','" . $_POST['loginno'] . "','" . strtolower(md5(strtolower(md5($adminconfig['defaultPassword'])) . $_POST['loginno'])) . "'," . $_POST['level'] . "," . $_POST['sort'] . "," . $_POST['status'] . ")";
                            $connection->addBatch($sqlupdate);
                        }
                        if (isset($_POST['roleids']) && $_POST['roleids'] != '') {
                            foreach ($_POST['roleids'] as $roleid) {
                                $connection->addBatch("insert into T_user_role_set(userid,roleid) values('" . $userid . "','" . $roleid . "')");
                            }
                        }
                        if (isset($_POST['departmentids']) && $_POST['departmentids'] != '') {
                            $departmentids = $_POST['departmentids'];
                            if (validateDepPower($highRoleLevel, $departmentids)) {
                                foreach ($departmentids as $departmentid) {
                                    $connection->addBatch("insert into T_user_department_set(userid,departmentid) values('" . $userid . "','" . $departmentid . "')");
                                }
                            } else {
                                $result = \service\tools\ToolsClass::buildJSONError("权限不足，不能编辑机构内人员，保存失败！");
                            }
                        }
                        if ($connection->doExecBatch()) {
                            $result['result'] = "保存成功！";
                        } else {
                            $result = \service\tools\ToolsClass::buildJSONError("保存失败！");
                        }
                    } else {
                        $result = \service\tools\ToolsClass::buildJSONError("保存失败，账号已存在！");
                    }
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("保存失败，无法编辑更高级别的用户！");
                }
                echo json_encode($result);
                die();
                break;
            case "del":
                $currLevel = intval($currUser->getLevels());
                $userlist = json_decode($_POST['users'], true);
                foreach ($userlist as $user) {
                    if ($user['level'] == '0') {
                        $connection->clearBatch();
                        $result = \service\tools\ToolsClass::buildJSONError("超级管理员用户【" . $user['name'] . "】不允许删除！");
                        echo json_encode($result);
                        die();
                    }
                    if ($user['level'] > $currLevel) {
                        $connection->addBatch("delete from T_user_department_set where userid='" . $user['id'] . "'");
                        $connection->addBatch("delete from T_user_role_set where userid='" . $user['id'] . "'");
                        $connection->addBatch("delete from T_user_info where userid='" . $user['id'] . "'");
                        $connection->addBatch("delete from T_user_LoginRecord where userid='" . $user['id'] . "'");
                        $connection->addBatch("delete from T_user_Configuration where userid='" . $user['id'] . "'");
                        $connection->addBatch("delete from T_user where id='" . $user['id'] . "'");
                    } else {
                        $connection->clearBatch();
                        $result = \service\tools\ToolsClass::buildJSONError("更高级别用户【" . $user['name'] . "】不允许删除！");
                        echo json_encode($result);
                        die();
                    }
                }
                if ($connection->doExecBatch()) {
                    $result['result'] = "删除成功！";
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("删除失败！");
                }
                echo json_encode($result);
                die();
                break;
            case "rpw":
                $adminconfig = \admin\config\AdminConfig::getInstance();
                $currLevel = intval($currUser->getLevels());
                $userinfo = $connection->doQuery("select name,loginno,levels from T_user where id='" . $_POST['id'] . "'")->fetchAll()[0];
                $level = intval($userinfo['levels']);
                if ($level > $currLevel) {
                    if (\service\user\UserManagerClass::resetPassword($_POST['id'], $userinfo['loginno'], $adminconfig['defaultPassword'])) {
                        $result['result'] = "重置成功！";
                    } else {
                        $result = \service\tools\ToolsClass::buildJSONError("重置失败！");
                    }
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("更高级别用户【" . $userinfo['name'] . "】不允许编辑！");
                }
                echo json_encode($result);
                die();
                break;
        }
    } else {
        $departmentids = \service\user\UserManagerClass::getDepartmentsForChildren($currUser->getId(), 0);
        if ($departmentids != '') {
            $departmentids = "'" . str_replace(",", "','", $departmentids) . "'";
        }
        $search_username = $_POST['search_username'];
        $search_loginno = $_POST['search_loginno'];
        $search_level = $_POST['search_level'];
        $search_status = $_POST['search_status'];
        $search_departmentname = $_POST['search_departmentname'];
        $search_roleids = $_POST['search_roleids'];
        $sqlArray = array();
        $sqlArray[0] = "*";
        $sqlArray[1] = "(select u.id,u.name,u.loginno,u.levels,u.sort,u.status from T_user u where u.levels>" . $currUser->getLevels() . " ";
        if ($search_username != '') {
            $sqlArray[1] = $sqlArray[1] . "and u.name like '%$search_username%' ";
        }
        if ($search_loginno != '') {
            $sqlArray[1] = $sqlArray[1] . "and u.loginno like '%$search_loginno%' ";
        }
        if ($search_level != '') {
            $sqlArray[1] = $sqlArray[1] . "and u.levels=$search_level ";
        }
        if ($search_status != '') {
            $sqlArray[1] = $sqlArray[1] . "and u.status=$search_status ";
        }
        if ($search_departmentname != '') {
            $sqlArray[1] = $sqlArray[1] . "and u.id in(select ud.userid from T_user_department_set ud,T_department d where d.id=ud.departmentid and d.name like '%" . $search_departmentname . "%') ";
        }
        if ($highRoleLevel > 0 && $departmentids != '') {
            $sqlArray[1] = $sqlArray[1] . "and u.id in(select ud.userid from T_user_department_set ud,T_department d where d.id=ud.departmentid and d.id in (" . $departmentids . ")) ";
        }
        if ($search_roleids != '') {
            $roleids = explode(',', $search_roleids);
            foreach ($roleids as $roleid) {
                $sqlArray[1] = $sqlArray[1] . "and u.id in(select ur.userid from T_user_role_set ur where ur.roleid='" . $roleid . "') ";
            }
        }
        $sqlArray[1] = $sqlArray[1] . ")t";
        $result = doQuery($sqlArray);
        for ($i = 0; $i < count($result['rows']); $i++) {
            $userid = $result['rows'][$i]['cell']['id'];
            $rolenames = '';
            $rolefetch = $connection->doQuery("select r.name,a.appname from T_user_role_set ur,T_role r,t_application a
                where ur.roleid=r.id and ur.userid='" . $userid . "' and a.id=r.appid order by a.sort asc,r.levels asc,r.sort asc")->fetchAll();
            foreach ($rolefetch as $rolename) {
                if ($rolenames != '') {
                    $rolenames = $rolenames . '，';
                }
                $rolenames = $rolenames . $rolename['appname'] . '----' . $rolename['name'];
            }
            $result['rows'][$i]['cell']['rolenams'] = $rolenames;

            $departmentnames = '';
            $departmentfetch = $connection->doQuery("select d.id from T_user_department_set ud,T_department d where ud.departmentid=d.id and ud.userid='" . $userid . "' order by d.levels asc,d.sort asc")->fetchAll();
            foreach ($departmentfetch as $department) {
                if ($departmentnames != '') {
                    $departmentnames = $departmentnames . '，';
                }
                $departmentnames = $departmentnames . '【' . getDepartmentName($department['id']) . '】';
            }
            $result['rows'][$i]['cell']['departmentnames'] = $departmentnames;
        }
        echo json_encode($result);
        die();
    }
}