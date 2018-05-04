<?php
require dirname(__FILE__) . '/../../../view/common/serviceHead.php';

/**
 * 获取当前登录用户所属机构ID
 *
 * @return array:
 */
function getCurrDepartmentIds()
{
    $currUser = admin\service\tools\ToolsClass::getUser();
    $commonTools = new service\tools\ToolsClass(0);
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
    $count = count($src_array);
    for ($i = 0; $i < $count; $i++) {
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
 * 校验是否有权限更改机构信息
 *
 * @param int $highRoleLevel
 * @param string $id
 * @param string $targetid
 * @param string $moveType
 * @return boolean
 */
function validateDepPower($highRoleLevel, $id, $targetid = '', $moveType = '')
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
            $search_str = "select * from t_Department order by levels asc";
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
            $validateids = array();
            switch ($moveType) {
                case "inner":
                    array_push($validateids, $id);
                    array_push($validateids, $targetid);
                    break;
                case "prev":
                    $search_str = "select id from t_Department where parentid=(select parentid from t_Department where id='" . $targetid . "')";
                    $ids = $commonTools->getDatasBySQL($search_str);
                    foreach ($ids as $did) {
                        array_push($validateids, $did['id']);
                    }
                    array_push($validateids, $targetid);
                    break;
                case "next":
                    $search_str = "select id from t_Department where parentid=(select parentid from t_Department where id='" . $targetid . "')";
                    $ids = $commonTools->getDatasBySQL($search_str);
                    foreach ($ids as $did) {
                        array_push($validateids, $did['id']);
                    }
                    array_push($validateids, $targetid);
                    break;
                default:
                    array_push($validateids, $id);
                    break;
            }
            foreach ($validateids as $departmentid) {
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
 * 更新子节点level
 * @param \service\tools\connection\ConnectionFactoryClass $connection
 * @param string $parentid 父节点id，多个以“,”分隔
 * @param int $parentlevel 父节点level
 */
function updateChildrenLevels($connection, $parentid, $parentlevel)
{
    $children = $connection->doQuery("select * from t_department where parentid in ('" . str_replace(",", "','", $parentid) . "')")->fetchAll();
    if (count($children) > 0) {
        $levels = $parentlevel + 1;
        $ids = "";
        foreach ($children as $child) {
            if ($ids != "") {
                $ids = $ids . ",";
            }
            $ids = $ids . $child['id'];
        }
        $connection->addBatch("update t_department set levels=" . $levels . " where id in ('" . str_replace(",", "','", $ids) . "')");
        updateChildrenLevels($connection, $ids, $levels);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $commonTools = new \service\tools\ToolsClass(0);
    $connection = $commonTools->getDBConnection();
    $currUser = admin\service\tools\ToolsClass::getUser();
    $highRoleLevel = \service\user\UserManagerClass::getHighestRoleLevel($currUser->getId());
    $result = array();
    if (isset($_POST['cmd'])) {
        $cmd = $_POST['cmd'];
        switch ($cmd) {
            case "addDepartment":
                $currNodeId = $_POST['currNodeId'];
                if (validateDepPower($highRoleLevel, $currNodeId)) {
                    $currNodeLevel = intval($_POST['currNodeLevel']);
                    $id = \service\tools\common\UUIDClass::getUUID();
                    $newlevel = $currNodeLevel + 1;
                    $update_str = "insert into t_Department(id,name,code,levels,parentid,sort) values('" . $id . "','新建机构',''," . $newlevel . ",'" . $currNodeId . "',0)";
                    if (!$connection->doExcute($update_str)) {
                        $result = \service\tools\ToolsClass::buildJSONError("添加机构失败！");
                    } else {
                        $result['id'] = $id;
                        $result['name'] = '新建机构';
                        $result['iconSkin'] = "departmenttree";
                        $result['powerlevel'] = $newlevel;
                        $result['editable'] = "true";
                    }
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("权限不够，不能编辑该机构！");
                    break;
                }
                break;
            case "saveAndResort":
                $id = $_POST['id'];
                $targetid = $_POST['targetid'];
                $moveType = $_POST['moveType'];
                if (validateDepPower($highRoleLevel, $id, $targetid, $moveType)) {
                    $update_str = "";
                    switch ($moveType) {
                        case "inner":
                            $dlevel = 1;
                            if ($targetid != 'root') {
                                $dlevel = intval($connection->doQuery("select levels from t_Department where id='" . $targetid . "'")->fetchAll()[0]['levels']) + 1;
                            }
                            $connection->addBatch("update t_Department set parentid='" . $targetid . "',levels=" . $dlevel . " where id='" . $id . "'");
                            updateChildrenLevels($connection, $id, $dlevel);
                            break;
                        case "prev":
                            $dlevel = intval($connection->doQuery("select levels from t_Department where id='" . $targetid . "'")->fetchAll()[0]['levels']);
                            $dep_parentid = $connection->doQuery("select t.parentid from t_Department t where t.id='" . $targetid . "'")->fetchAll()[0]['parentid'];
                            $connection->addBatch("update t_Department set parentid='" . $dep_parentid . "',levels=" . $dlevel . " where id='" . $id . "'");
                            updateChildrenLevels($connection, $id, $dlevel);
                            break;
                        case "next":
                            $dlevel = intval($connection->doQuery("select levels from t_Department where id='" . $targetid . "'")->fetchAll()[0]['levels']);
                            $dep_parentid = $connection->doQuery("select t.parentid from t_Department t where t.id='" . $targetid . "'")->fetchAll()[0]['parentid'];
                            $connection->addBatch("update t_Department set parentid='" . $dep_parentid . "',levels=" . $dlevel . " where id='" . $id . "'");
                            updateChildrenLevels($connection, $id, $dlevel);
                            break;
                    }
                    if (!$connection->doExecBatch()) {
                        $result = \service\tools\ToolsClass::buildJSONError("保存机构失败！");
                    }
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("没有权限，不能更改该机构信息！");
                }
                break;
            case "delDepartment":
                $id = $_POST['id'];
                if (validateDepPower($highRoleLevel, $id)) {
                    $search_str = "select count(*) as count from t_User_Department_Set where departmentid='" . $id . "'";
                    $ucount = intval($connection->doQuery($search_str)->fetchAll()[0]['count']);
                    if ($ucount > 0) {
                        $result = \service\tools\ToolsClass::buildJSONError("该机构有关联用户，不能删除！");
                        break;
                    }
                    $search_str = "select count(*) as count from t_Department where parentid='" . $id . "'";
                    $ucount = intval($connection->doQuery($search_str)->fetchAll()[0]['count']);
                    if ($ucount > 0) {
                        $result = \service\tools\ToolsClass::buildJSONError("含有下级机构，不能删除！");
                        break;
                    }
                    $delete_str = "delete from t_Department where id='" . $id . "'";
                    $connection->addBatch($delete_str);
                    if (!$connection->doExecBatch()) {
                        $result = \service\tools\ToolsClass::buildJSONError("删除机构失败！");
                    }
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("没有权限，不能删除该机构信息！");
                }
                break;
            case "searchDepartment":
                $search_str = "select t.id,t.name,t.code,t.levels,t.parentid as pid,case when t.id in (select st.parentid from t_Department st) then 'true' else 'false' end as isParent,";
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
                if ($highRoleLevel == 0) {
                    $tree[0]['editable'] = 'true';
                } else {
                    $tree[0]['editable'] = 'false';
                }
                $search_str = "select t.id,t.name,t.levels,t.parentid as pid,case when t.id in (select st.parentid from t_Department st) then 'true' else 'false' end as isParent from t_Department t";
                $count_search = $connection->doQuery($search_str)->fetchAll();
                if (count($count_search) > 0) {
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
                    $tree[$i]['open'] = $row['isparent'] === 'true' ? true : false;
                    if ($highRoleLevel == 0) {
                        $tree[$i]['editable'] = 'true';
                    } else {
                        $tree[$i]['editable'] = $row['editable'];
                    }
                    $i++;
                }
                $result['tree'] = $tree;

                $search_user = $connection->doQuery("select u.id,u.name,u.loginno,case u.status when 1 then '启用' else '禁用' end as status from t_User u where u.levels>" . $currUser->getLevels() . " order by u.levels asc,u.status desc,u.sort asc")->fetchAll();
                $users = array();
                $i = 0;
                foreach ($search_user as $user) {
                    $users[$i][0] = $user['id'];
                    $users[$i][1] = $user['name'] . '（' . $user['status'] . ',' . $user['loginno'] . '）';
                    $i++;
                }
                $result['users'] = $users;
                break;
            case "searchInfo":
                $search_str = "select * from t_Department where id='" . $_POST['departmentid'] . "'";
                $result_search = $connection->doQuery($search_str)->fetchAll();
                if (count($result_search) > 0) {
                    $result['department_name'] = $result_search[0]['name'];
                    $result['department_code'] = $result_search[0]['code'];
                    $result['department_sort'] = $result_search[0]['sort'];
                    $search_user_department_set = $connection->doQuery("select ud.userid from t_User_Department_Set ud,t_User u where ud.userid=u.id and ud.departmentid='" . $_POST['departmentid'] . "' and u.levels>" . $currUser->getLevels())->fetchAll();
                    $select_users = "";
                    foreach ($search_user_department_set as $user) {
                        if ($select_users != "") {
                            $select_users = $select_users . ',';
                        }
                        $select_users = $select_users . $user['userid'];
                    }
                    $result['select_users'] = $select_users;
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("查询失败，找不到信息！");
                }
                break;
            case "saveInfo":
                $departmentid = $_POST['departmentid'];
                if (empty($_POST['department_name']) || $_POST['department_name'] == '') {
                    $result = \service\tools\ToolsClass::buildJSONError("请输入机构名称！");
                    break;
                }
                if (validateDepPower($highRoleLevel, $departmentid)) {
                    $connection->addBatch("update t_Department set name='" . $_POST['department_name'] . "',code='" . $_POST['department_code'] . "',sort='" . $_POST['department_sort'] . "' where id='" . $departmentid . "'");
                    $connection->addBatch("delete from t_User_Department_Set where departmentid='" . $departmentid . "' and userid in (select id from t_User where levels>" . $currUser->getLevels() . ")");
                    if (!empty($_POST['select_users']) && $_POST['select_users'] != "") {
                        $select_users = $_POST['select_users'];
                        foreach ($select_users as $userid) {
                            $insert_str = "insert into t_User_Department_Set(userid,departmentid) values('" . $userid . "','" . $departmentid . "')";
                            $connection->addBatch($insert_str);
                        }
                    }
                    if (!$connection->doExecBatch()) {
                        $result = \service\tools\ToolsClass::buildJSONError("保存信息失败！");
                        break;
                    }
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("权限不够，不能编辑该部门！");
                    break;
                }
                break;
        }
    }
    echo json_encode($result);
}