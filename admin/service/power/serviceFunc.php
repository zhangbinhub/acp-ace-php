<?php
require dirname(__FILE__) . '/../../view/common/serviceHead.php';
require $_SERVER['DOCUMENT_ROOT'] . '/service/base/baseQuery.php';

/**
 * 校验编码是否重复
 * @param $isModule
 * @param $id
 * @param $code
 * @return bool
 */
function validateCode($isModule, $id, $code)
{
    $commonTools = new \service\tools\ToolsClass(0);
    $connection = $commonTools->getDBConnection();
    if ($isModule) {
        $count1 = intval($connection->doQuery("select count(*) as count from t_Module where id<>'" . $id . "' and code='" . $code . "'")->fetchAll()[0]['count']);
        if ($count1 > 0) {
            return false;
        } else {
            $count2 = intval($connection->doQuery("select count(*) as count from t_Module_Func where code='" . $code . "'")->fetchAll()[0]['count']);
            if ($count2 > 0) {
                return false;
            } else {
                return true;
            }
        }
    } else {
        $count1 = intval($connection->doQuery("select count(*) as count from t_Module where code='" . $code . "'")->fetchAll()[0]['count']);
        if ($count1 > 0) {
            return false;
        } else {
            $count2 = intval($connection->doQuery("select count(*) as count from t_Module_Func where id<>'" . $id . "' and code='" . $code . "'")->fetchAll()[0]['count']);
            if ($count2 > 0) {
                return false;
            } else {
                return true;
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $commonTools = new \service\tools\ToolsClass(0);
    $connection = $commonTools->getDBConnection();
    if (isset($_POST['cmd'])) {
        $result = array();
        $cmd = $_POST['cmd'];
        switch ($cmd) {
            case "searchModule":
                $tree = array();
                $i = 0;
                $search_str = "select id,appname,case when id in (select st.appid from t_Module st) then 'true' else 'false' end as isParent from t_application order by sort asc";
                $result_search = $connection->doQuery($search_str)->fetchAll();
                foreach ($result_search as $row) {
                    $tree[$i]['id'] = $row['id'];
                    $tree[$i]['appid'] = $row['id'];
                    $tree[$i]['name'] = '模块（' . $row['appname'] . '）';
                    $tree[$i]['pid'] = '0';
                    $tree[$i]['iconSkin'] = "moduletree";
                    $tree[$i]['isParent'] = true;
                    if ($row['isparent'] === 'true') {
                        $tree[$i]['open'] = true;
                    } else {
                        $tree[$i]['open'] = false;
                    }
                    $i++;
                }
                $search_str = "select t.id,t.appid,t.name,t.parentid as pid,case when t.id in (select st.parentid from t_Module st where st.parentid=t.id) then 'true' else 'false' end as isParent,type from t_Module t order by t.code asc";
                $result_search = $connection->doQuery($search_str)->fetchAll();
                foreach ($result_search as $row) {
                    $tree[$i]['id'] = $row['id'];
                    $tree[$i]['appid'] = $row['appid'];
                    $tree[$i]['name'] = $row['name'];
                    $tree[$i]['pid'] = $row['pid'];
                    $tree[$i]['iconSkin'] = "moduletree";
                    $tree[$i]['isParent'] = $row['isparent'] === 'true' ? true : false;
                    if ($tree[$i]['isParent']) {
                        $tree[$i]['open'] = true;
                    } else {
                        $tree[$i]['open'] = false;
                    }
                    $tree[$i]['type'] = (int)$row['type'];
                    $i++;
                }
                $result['tree'] = $tree;
                break;
            case "addModule":
                $id = \service\tools\common\UUIDClass::getUUID();
                $parentid = $_POST['parentid'];
                $update_str = "insert into t_Module(id,appid,name,code,parentid,type) values('" . $id . "','" . $_POST['appid'] . "','新建模块','','" . $parentid . "',1)";
                if ($connection->doExcute($update_str)) {
                    $result['id'] = $id;
                    $result['appid'] = $_POST['appid'];
                    $result['name'] = '新建模块';
                    $result['pid'] = $parentid;
                    $result['type'] = 1;
                    $result['iconSkin'] = "moduletree";
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("新增模块失败！");
                }
                break;
            case "delModule":
                $id = $_POST['id'];
                $search_str = "select count(*) as count from t_Module where parentid='" . $id . "'";
                $ucount = intval($connection->doQuery($search_str)->fetchAll()[0]['count']);
                if ($ucount > 0) {
                    $result = \service\tools\ToolsClass::buildJSONError("含有下级模块，不能删除！");
                    break;
                }
                $connection->addBatch("delete from t_Role_Module_Func_Set where funcid in (select id from t_Module_Func where moduleid='" . $id . "')");
                $connection->addBatch("delete from t_Module_Func where moduleid='" . $id . "'");
                $connection->addBatch("delete from t_Role_Module_Set where moduleid='" . $id . "'");
                $connection->addBatch("delete from t_Module where id='" . $id . "'");
                if (!$connection->doExecBatch()) {
                    $result = \service\tools\ToolsClass::buildJSONError("删除模块失败！");
                }
                break;
            case "searchModuleInfo":
                $moduleid = $_POST['moduleid'];
                $module = $connection->doQuery("select * from t_Module where id='" . $moduleid . "'")->fetchAll()[0];
                $result['module_name'] = $module['name'];
                $result['module_code'] = $module['code'];

                $search_role = $connection->doQuery("select r.id,r.name,ta.appname,r.appid from t_Role r left join t_application ta on ta.id=r.appid
                        where r.appid='" . $_POST['appid'] . "' order by ta.sort asc,r.levels asc,r.sort asc")->fetchAll();
                $roles = array();
                $i = 0;
                foreach ($search_role as $role) {
                    $roles[$i][0] = $role['id'];
                    $roles[$i][1] = $role['appname'] . '--' . $role['name'];
                    $i++;
                }
                $result['roles'] = $roles;

                $search_role_module_set = $connection->doQuery("select roleid from t_Role_Module_Set where moduleid='" . $_POST['moduleid'] . "'")->fetchAll();
                $select_roles = "";
                foreach ($search_role_module_set as $role) {
                    if ($select_roles != "") {
                        $select_roles = $select_roles . ',';
                    }
                    $select_roles = $select_roles . $role['roleid'];
                }
                $result['select_roles'] = $select_roles;
                break;
            case "saveModuleInfo":
                $moduleid = $_POST['module_id'];
                if (validateCode(true, $moduleid, $_POST['module_code'])) {
                    $connection->addBatch("update t_Module set name='" . $_POST['module_name'] . "',code='" . trim($_POST['module_code']) . "' where id='" . $moduleid . "'");
                    $connection->addBatch("delete from t_Role_Module_Set where moduleid='" . $moduleid . "' and roleid in (select id from t_Role)");
                    if (!empty($_POST['select_roles']) && $_POST['select_roles'] != "") {
                        $select_roles = $_POST['select_roles'];
                        foreach ($select_roles as $roleid) {
                            $connection->addBatch("insert into t_Role_Module_Set(roleid,moduleid) values('" . $roleid . "','" . $moduleid . "')");
                        }
                    }
                    if (!$connection->doExecBatch()) {
                        $result = \service\tools\ToolsClass::buildJSONError("保存信息失败！");
                    }
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("编码重复，请检查！");
                }
                break;
            case "searchFunc":
                $moduleid = $_POST['moduleid'];
                $sqlArray = array();
                $sqlArray[0] = "*";
                $sqlArray[1] = "(select id,appid as applicationid,name,code,case islog when 1 then '是' else '否' end as islog from t_Module_Func where moduleid='" . $moduleid . "' and appid='" . $_POST['appid'] . "')t";
                $result = doQuery($sqlArray);
                break;
            case "searchFuncRoles":
                $search_role = $connection->doQuery("select r.id,r.name,a.appname from t_Role r left join t_application a on a.id=r.appid
                    where r.appid='" . $_POST['appid'] . "' order by a.sort asc,r.levels asc,r.sort asc")->fetchAll();
                $roles = array();
                $i = 0;
                foreach ($search_role as $role) {
                    $roles[$i][0] = $role['id'];
                    $roles[$i][1] = $role['appname'] . '----' . $role['name'];
                    $i++;
                }
                $result['roles'] = $roles;
                $select_role = $connection->doQuery("select r.id from t_Role r where r.id in
                    (select roleid from t_Role_Module_Func_Set where funcid='" . $_POST['funcid'] . "') order by r.levels asc,r.sort asc")->fetchAll();
                $selectroles = '';
                $i = 0;
                foreach ($select_role as $role) {
                    if ($selectroles != '') {
                        $selectroles = $selectroles . ',';
                    }
                    $selectroles = $selectroles . $role['id'];
                    $i++;
                }
                $result['select_roles'] = $selectroles;
                break;
            case "saveFuncRoles":
                $connection->addBatch("delete from t_Role_Module_Func_Set where funcid='" . $_POST['funcid'] . "' and roleid in (select id from t_Role where appid='" . $_POST['appid'] . "')");
                if (!empty($_POST['select_roles']) && $_POST['select_roles'] != "") {
                    $select_roles = $_POST['select_roles'];
                    foreach ($select_roles as $roleid) {
                        $connection->addBatch("insert into t_Role_Module_Func_Set(roleid,funcid) values('" . $roleid . "','" . $_POST['funcid'] . "')");
                    }
                }
                if (!$connection->doExecBatch()) {
                    $result = \service\tools\ToolsClass::buildJSONError("保存信息失败！");
                }
                break;
            case "validationMcode":
                if (validateCode(true, $_POST['module_id'], $_POST['module_code'])) {
                    echo 'true';
                } else {
                    echo 'false';
                }
                die();
                break;
        }
        echo json_encode($result);
    } else {
        if (isset($_POST['oper'])) {
            $moduleid = $_GET['moduleid'];
            $apppid = $_GET['appid'];
            $oper = $_POST['oper'];
            switch ($oper) {
                case "edit":
                    if (validateCode(false, $_POST['id'], $_POST['code'])) {
                        $islog_value = 1;
                        if ($_POST['islog'] == '否') {
                            $islog_value = 0;
                        }
                        $update_str = "update t_Module_Func set name='" . $_POST['name'] . "',code='" . trim($_POST['code']) . "',islog=" . $islog_value . " where id='" . $_POST['id'] . "'";
                        $connection->addBatch($update_str);
                        if ($connection->doExecBatch()) {
                            echo 'true';
                        } else {
                            echo '保存失败！';
                        }
                    } else {
                        echo '此编码已存在，请重新填写！';
                    }
                    break;
                case "add":
                    if (validateCode(false, '', $_POST['code'])) {
                        $islog_value = 1;
                        if ($_POST['islog'] == '否') {
                            $islog_value = 0;
                        }
                        $update_str = "insert into t_Module_Func(id,appid,moduleid,name,code,islog,type) values('" . service\tools\common\UUIDClass::getUUID() . "','" . $apppid . "','" . $moduleid . "','" . $_POST['name'] . "','" . $_POST['code'] . "'," . $islog_value . ",1)";
                        $connection->addBatch($update_str);
                        if ($connection->doExecBatch()) {
                            echo 'true';
                        } else {
                            echo '保存失败！';
                        }
                    } else {
                        echo '此编码已存在，请重新填写！';
                    }
                    break;
                case "del":
                    $id = '\'' . str_replace(',', '\',\'', $_POST['id']) . '\'';
                    $search_result = $connection->doQuery("select name from t_Module_Func where id in(" . $id . ") and type=0")->fetchAll();
                    if (count($search_result) > 0) {
                        $ret = '功能：';
                        foreach ($search_result as $row) {
                            $ret = $ret . $row['name'] . ',';
                        }
                        $ret = substr($ret, 0, strlen($ret) - 1) . ' 不可删除！';
                        echo $ret;
                        break;
                    }
                    $connection->addBatch("delete from t_Role_Module_Func_Set where funcid in(" . $id . ")");
                    $connection->addBatch("delete from t_Module_Func where id in(" . $id . ")");
                    if ($connection->doExecBatch()) {
                        echo 'true';
                    } else {
                        echo '删除失败！';
                    }
                    break;
            }
        }
    }
}
?>