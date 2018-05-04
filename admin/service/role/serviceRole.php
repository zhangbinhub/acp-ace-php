<?php
require dirname(__FILE__) . '/../../view/common/serviceHead.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $commonTools = new \service\tools\ToolsClass(0);
    $connection = $commonTools->getDBConnection();
    $currUser = admin\service\tools\ToolsClass::getUser();
    $result = array();
    if (isset($_POST['cmd'])) {
        $cmd = $_POST['cmd'];
        switch ($cmd) {
            case "addRole":
                $id = \service\tools\common\UUIDClass::getUUID();
                $newlevel = 1;
                $update_str = "insert into t_Role(id,appid,name,levels,sort) values('" . $id . "','" . $_POST['appid'] . "','新建角色'," . $newlevel . ",0)";
                if (!$connection->doExcute($update_str)) {
                    $result = \service\tools\ToolsClass::buildJSONError("添加角色失败！");
                } else {
                    $result['id'] = $id;
                    $result['appid'] = $_POST['appid'];
                    $result['pId'] = $_POST['appid'];
                    $result['name'] = '新建角色';
                    $result['iconSkin'] = "roletree";
                    $result['powerlevel'] = $newlevel;
                }
                break;
            case "delRole":
                $id = $_POST['id'];
                $search_str = "select count(*) as count from t_User_Role_Set where roleid='" . $id . "'";
                $ucount = intval($connection->doQuery($search_str)->fetchAll()[0]['count']);
                if ($ucount > 0) {
                    $result = \service\tools\ToolsClass::buildJSONError("该角色有关联用户，不能删除！");
                    break;
                }
                $search_str = "select levels from t_Role where id='" . $id . "'";
                $ucount = intval($connection->doQuery($search_str)->fetchAll()[0]['levels']);
                if ($ucount == 0) {
                    $result = \service\tools\ToolsClass::buildJSONError("不能删除更高级别的角色！");
                    break;
                }
                $delete_str1 = "delete from t_Role_Menu_Set where roleid='" . $id . "'";
                $delete_str2 = "delete from t_Role_Module_Set where roleid='" . $id . "'";
                $delete_str3 = "delete from t_Role_Module_Func_Set where roleid='" . $id . "'";
                $delete_str4 = "delete from t_Role where id='" . $id . "'";
                $connection->addBatch($delete_str1);
                $connection->addBatch($delete_str2);
                $connection->addBatch($delete_str3);
                $connection->addBatch($delete_str4);
                if (!$connection->doExecBatch()) {
                    $result = \service\tools\ToolsClass::buildJSONError("删除角色失败！");
                }
                break;
            case "searchRole":
                $tree = array();
                $i = 0;
                $search_str = "select id,appname,case when id in (select st.appid from t_Role st) then 'true' else 'false' end as isParent from t_application order by sort asc";
                $result_search = $connection->doQuery($search_str)->fetchAll();
                foreach ($result_search as $row) {
                    $tree[$i]['id'] = $row['id'];
                    $tree[$i]['appid'] = $row['id'];
                    $tree[$i]['name'] = '角色（' . $row['appname'] . '）';
                    $tree[$i]['pId'] = '0';
                    $tree[$i]['iconSkin'] = "roletree";
                    $tree[$i]['isParent'] = true;
                    if ($row['isparent'] === 'true') {
                        $tree[$i]['open'] = true;
                    } else {
                        $tree[$i]['open'] = false;
                    }
                    $i++;
                }
                $search_str = "select t.id,t.name,t.levels,t.sort,t.appid from t_Role t order by t.levels asc,t.sort asc";
                $result_search = $connection->doQuery($search_str)->fetchAll();
                foreach ($result_search as $row) {
                    $tree[$i]['id'] = $row['id'];
                    $tree[$i]['appid'] = $row['appid'];
                    $tree[$i]['name'] = $row['name'];
                    $tree[$i]['powerlevel'] = $row['levels'];
                    $tree[$i]['pId'] = $row['appid'];
                    $tree[$i]['iconSkin'] = "roletree";
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
                $search_str = "select * from t_Role where id='" . $_POST['roleid'] . "'";
                $result_search = $connection->doQuery($search_str)->fetchAll();
                if (count($result_search) > 0) {
                    $result['role_name'] = $result_search[0]['name'];
                    $result['role_level'] = $result_search[0]['levels'];
                    $result['role_sort'] = $result_search[0]['sort'];

                    $search_user_role_set = $connection->doQuery("select ur.userid from t_User_Role_Set ur,t_User u where ur.userid=u.id and ur.roleid='" . $_POST['roleid'] . "' and u.levels>" . $currUser->getLevels())->fetchAll();
                    $select_users = "";
                    foreach ($search_user_role_set as $user) {
                        if ($select_users != "") {
                            $select_users = $select_users . ',';
                        }
                        $select_users = $select_users . $user['userid'];
                    }
                    $result['select_users'] = $select_users;

                    $search_str = "select id,appname from t_application where id='" . $_POST['appid'] . "'";
                    $result_search_app = $connection->doQuery($search_str)->fetchAll();

                    $search_str = "select t.id,t.name,t.parentid as pid,
                        case when t.id in (select st.parentid from t_Menu st where st.parentid=t.id) then 'true' else 'false' end as isParent,
                        case when t.id in (select menuid from t_Role_Menu_Set where roleid='" . $_POST['roleid'] . "') then 'true' else 'false' end as checked
                            from t_Menu t where appid='" . $_POST['appid'] . "' order by sort asc";
                    $result_search = $connection->doQuery($search_str)->fetchAll();
                    $menutree = array();
                    $i = 0;
                    foreach ($result_search_app as $row) {
                        $menutree[$i]['id'] = $row['id'];
                        $menutree[$i]['appid'] = $row['id'];
                        $menutree[$i]['name'] = '菜单（' . $row['appname'] . '）';
                        $menutree[$i]['pId'] = '0';
                        $menutree[$i]['iconSkin'] = "menutree";
                        $menutree[$i]['isParent'] = true;
                        if (count($result_search) > 0) {
                            $menutree[$i]['open'] = true;
                        } else {
                            $menutree[$i]['open'] = false;
                        }
                        $menutree[$i]['nocheck'] = true;
                        $i++;
                    }
                    foreach ($result_search as $row) {
                        $menutree[$i]['id'] = $row['id'];
                        $menutree[$i]['name'] = $row['name'];
                        $menutree[$i]['pId'] = $row['pid'];
                        $menutree[$i]['iconSkin'] = "menutree";
                        $menutree[$i]['isParent'] = $row['isparent'] === 'true' ? true : false;
                        $menutree[$i]['checked'] = $row['checked'] === 'true' ? true : false;
                        if ($menutree[$i]['isParent']) {
                            $menutree[$i]['open'] = true;
                        } else {
                            $menutree[$i]['open'] = false;
                        }
                        $i++;
                    }
                    $result['menutree'] = $menutree;

                    $search_str = "select * from ((select t.id,t.name,t.code,t.parentid as pid,'0' as type,'true' as isParent,
                        case when t.id in (select moduleid from t_Role_Module_Set where roleid='" . $_POST['roleid'] . "') then 'true' else 'false' end as checked
                            from t_Module t where t.appid='" . $_POST['appid'] . "')";
                    $search_str = $search_str . 'union all ';
                    $search_str = $search_str . "(select t.id,t.name,t.code,t.moduleid as pid,'1' as type,'false' as isParent,
                        case when t.id in (select funcid from t_Role_Module_Func_Set where roleid='" . $_POST['roleid'] . "') then 'true' else 'false' end as checked
                            from t_Module_Func t where t.appid='" . $_POST['appid'] . "'))t order by t.code asc";
                    $result_search = $connection->doQuery($search_str)->fetchAll();
                    $functree = array();
                    $i = 0;
                    foreach ($result_search_app as $row) {
                        $functree[$i]['id'] = $row['id'];
                        $functree[$i]['appid'] = $row['id'];
                        $functree[$i]['name'] = '模块（' . $row['appname'] . '）';
                        $functree[$i]['pId'] = '0';
                        $functree[$i]['iconSkin'] = "moduletree";
                        $functree[$i]['isParent'] = true;
                        if (count($result_search) > 0) {
                            $functree[$i]['open'] = true;
                        } else {
                            $functree[$i]['open'] = false;
                        }
                        $functree[$i]['nocheck'] = true;
                        $i++;
                    }
                    foreach ($result_search as $row) {
                        $functree[$i]['id'] = $row['id'];
                        $functree[$i]['name'] = $row['name'];
                        $functree[$i]['pId'] = $row['pid'];
                        $functree[$i]['type'] = $row['type'];
                        $functree[$i]['iconSkin'] = "moduletree";
                        $functree[$i]['isParent'] = $row['isparent'] === 'true' ? true : false;
                        $functree[$i]['checked'] = $row['checked'] === 'true' ? true : false;
                        if ($functree[$i]['isParent']) {
                            $functree[$i]['open'] = true;
                        } else {
                            $functree[$i]['open'] = false;
                        }
                        $i++;
                    }
                    $result['functree'] = $functree;
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("查询失败，找不到信息！");
                }
                break;
            case "saveInfo":
                $roleid = $_POST['roleid'];
                $currLevel = intval($_POST['currNodePowerLevel']);
                if ($currLevel > 0) {
                    $connection->addBatch("update t_Role set name='" . $_POST['role_name'] . "',levels='" . $_POST['role_level'] . "',sort='" . $_POST['role_sort'] . "' where id='" . $roleid . "'");
                }
                $connection->addBatch("delete from t_User_Role_Set where roleid='" . $roleid . "' and userid in (select id from t_User where levels>" . $currUser->getLevels() . ")");
                if (!empty($_POST['select_users']) && $_POST['select_users'] != "") {
                    $select_users = $_POST['select_users'];
                    foreach ($select_users as $userid) {
                        $insert_str = "insert into t_User_Role_Set(userid,roleid) values('" . $userid . "','" . $roleid . "')";
                        $connection->addBatch($insert_str);
                    }
                }
                $connection->addBatch("delete from t_Role_Menu_Set where roleid='" . $roleid . "'");
                if (!empty($_POST['select_menus']) && $_POST['select_menus'] != "") {
                    $select_menus = $_POST['select_menus'];
                    foreach ($select_menus as $menuid) {
                        $insert_str = "insert into t_Role_Menu_Set(roleid,menuid) values('" . $roleid . "','" . $menuid . "')";
                        $connection->addBatch($insert_str);
                    }
                }
                $connection->addBatch("delete from t_Role_Module_Set where roleid='" . $roleid . "'");
                $connection->addBatch("delete from t_Role_Module_Func_Set where roleid='" . $roleid . "'");
                if (!empty($_POST['select_modulefuncs']) && $_POST['select_modulefuncs'] != "") {
                    $select_modulefuncs = $_POST['select_modulefuncs'];
                    foreach ($select_modulefuncs as $modulefunc) {
                        if ($modulefunc['type'] == '0') {
                            $connection->addBatch("insert into t_Role_Module_Set(roleid,moduleid) values('" . $roleid . "','" . $modulefunc['id'] . "')");
                        } else {
                            $connection->addBatch("insert into t_Role_Module_Func_Set(roleid,funcid) values('" . $roleid . "','" . $modulefunc['id'] . "')");
                        }
                    }
                }
                if (!$connection->doExecBatch()) {
                    $result = \service\tools\ToolsClass::buildJSONError("保存信息失败！");
                    break;
                }
                break;
        }
    }
    echo json_encode($result);
}
?>