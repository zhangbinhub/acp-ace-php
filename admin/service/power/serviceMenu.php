<?php
require dirname(__FILE__) . '/../../view/common/serviceHead.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $commonTools = new \service\tools\ToolsClass(0);
    $connection = $commonTools->getDBConnection();
    $result = array();
    if (isset($_POST['cmd'])) {
        $cmd = $_POST['cmd'];
        switch ($cmd) {
            case "add":
                $id = \service\tools\common\UUIDClass::getUUID();
                $parentid = $_POST['parentid'];
                $menu_sort = (int)$connection->doQuery("select max(sort) as sort from t_Menu where parentid='" . $parentid . "'")->fetchAll()[0]['sort'];
                $update_str = "insert into t_Menu(id,appid,name,parentid,status,type,sort,model,opentype,dialog_w,dialog_h)
                    values('" . $id . "','" . $_POST['appid'] . "','新建菜单','" . $parentid . "',1,1," . ($menu_sort + 1) . ",0,0,0,0)";
                if ($connection->doExcute($update_str)) {
                    $result['id'] = $id;
                    $result['appid'] = $_POST['appid'];
                    $result['name'] = '新建菜单';
                    $result['pid'] = $parentid;
                    $result['type'] = 1;
                    $result['iconSkin'] = "menutree";
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("新增菜单失败！");
                }
                break;
            case "saveAndResort":
                $id = $_POST['id'];
                $targetid = $_POST['targetid'];
                $appid = $_POST['appid'];
                $moveType = $_POST['moveType'];
                $update_str = "";
                $oldappid = $connection->doQuery("select t.appid from t_Menu t where t.id='" . $id . "'")->fetchAll()[0]['appid'];
                if ($oldappid != $appid) {
                    $result = \service\tools\ToolsClass::buildJSONError("菜单不能更换应用！");
                    break;
                }
                switch ($moveType) {
                    case "inner":
                        $menu_sort = (int)$connection->doQuery("select max(t.sort)+1 as sort from t_Menu t where t.parentid='" . $targetid . "'")->fetchAll()[0]['sort'];
                        $update_str = "update t_Menu set appid='" . $appid . "',parentid='" . $targetid . "',sort=" . $menu_sort . " where id='" . $id . "'";
                        $connection->addBatch($update_str);
                        break;
                    case "prev":
                        $menu_sort = (int)$connection->doQuery("select sort from t_Menu where id='" . $targetid . "'")->fetchAll()[0]['sort'];
                        $menu_parentid = $connection->doQuery("select t.parentid from t_Menu t where t.id='" . $targetid . "'")->fetchAll()[0]['parentid'];
                        $menus = $connection->doQuery("select * from t_Menu where parentid='" . $menu_parentid . "' and sort>=" . $menu_sort . " order by sort asc")->fetchAll();
                        for ($i = 0; $i < count($menus); $i++) {
                            $connection->addBatch("update t_Menu set sort=" . ($menu_sort + $i + 1) . " where id='" . $menus[$i]['id'] . "'");
                        }
                        $connection->addBatch("update t_Menu set appid='" . $appid . "',parentid='" . $menu_parentid . "',sort=" . $menu_sort . " where id='" . $id . "'");
                        break;
                    case "next":
                        $menu_sort = (int)$connection->doQuery("select sort+1 as sort from t_Menu where id='" . $targetid . "'")->fetchAll()[0]['sort'];
                        $menu_parentid = $connection->doQuery("select t.parentid from t_Menu t where t.id='" . $targetid . "'")->fetchAll()[0]['parentid'];
                        $menus = $connection->doQuery("select * from t_Menu where parentid='" . $menu_parentid . "' and sort>=" . $menu_sort . " order by sort asc")->fetchAll();
                        for ($i = 0; $i < count($menus); $i++) {
                            $connection->addBatch("update t_Menu set sort=" . ($menu_sort + $i + 1) . " where id='" . $menus[$i]['id'] . "'");
                        }
                        $connection->addBatch("update t_Menu set appid='" . $appid . "',parentid='" . $menu_parentid . "',sort=" . $menu_sort . " where id='" . $id . "'");
                        break;
                }
                if (!$connection->doExecBatch()) {
                    $result = \service\tools\ToolsClass::buildJSONError("保存菜单失败！");
                }
                break;
            case "del":
                $id = $_POST['id'];
                $search_str = "select count(*) as count from t_Menu where parentid='" . $id . "'";
                $ucount = intval($connection->doQuery($search_str)->fetchAll()[0]['count']);
                if ($ucount > 0) {
                    $result = \service\tools\ToolsClass::buildJSONError("含有下级菜单，不能删除！");
                    break;
                }
                $connection->beginTransaction();
                $delete_str = "delete from t_Role_Menu_Set where menuid='" . $id . "'";
                if (!$connection->doExcute($delete_str)) {
                    $connection->rollBack();
                    $result = \service\tools\ToolsClass::buildJSONError("删除菜单失败！");
                    break;
                }
                $update_str = "delete from t_Menu where id='" . $id . "'";
                if (!$connection->doExcute($update_str)) {
                    $connection->rollBack();
                    $result = \service\tools\ToolsClass::buildJSONError("删除菜单失败！");
                    break;
                }
                $connection->commit();
                break;
            case "search":
                $tree = array();
                $i = 0;
                $search_str = "select id,appname,case when id in (select st.appid from t_Menu st) then 'true' else 'false' end as isParent from t_application order by sort asc";
                $result_search = $connection->doQuery($search_str)->fetchAll();
                foreach ($result_search as $row) {
                    $tree[$i]['id'] = $row['id'];
                    $tree[$i]['appid'] = $row['id'];
                    $tree[$i]['name'] = '菜单（' . $row['appname'] . '）';
                    $tree[$i]['pid'] = '0';
                    $tree[$i]['iconSkin'] = "menutree";
                    $tree[$i]['isParent'] = true;
                    if ($row['isparent'] === 'true') {
                        $tree[$i]['open'] = true;
                    } else {
                        $tree[$i]['open'] = false;
                    }
                    $i++;
                }
                $search_str = "select t.id,t.appid,t.name,t.parentid as pid,case when t.id in (select st.parentid from t_Menu st) then 'true' else 'false' end as isParent,type from t_Menu t order by sort asc";
                $result_search = $connection->doQuery($search_str)->fetchAll();
                foreach ($result_search as $row) {
                    $tree[$i]['id'] = $row['id'];
                    $tree[$i]['appid'] = $row['appid'];
                    $tree[$i]['name'] = $row['name'];
                    $tree[$i]['pid'] = $row['pid'];
                    $tree[$i]['iconSkin'] = "menutree";
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
            case "searchInfo":
                $search_str = "select * from t_Menu where id='" . $_POST['menuid'] . "'";
                $result_search = $connection->doQuery($search_str)->fetchAll();
                if (count($result_search) > 0) {
                    $result['icon_class'] = $result_search[0]['icon_class'];
                    $result['icon_color'] = $result_search[0]['icon_color'];
                    $result['menu_name'] = $result_search[0]['name'];
                    $result['menu_model'] = (int)$result_search[0]['model'];
                    $result['page_url'] = htmlspecialchars_decode($result_search[0]['page_url']);
                    $result['menu_opentype'] = (int)$result_search[0]['opentype'];
                    $result['menu_sort'] = (int)$result_search[0]['sort'];
                    $result['menu_status'] = (int)$result_search[0]['status'];
                    $result['dialog_w'] = (int)$result_search[0]['dialog_w'];
                    $result['dialog_h'] = (int)$result_search[0]['dialog_h'];

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

                    $search_role_menu_set = $connection->doQuery("select r.id from t_Role_Menu_Set rm,t_role r
                        where r.id=rm.roleid and r.appid='" . $_POST['appid'] . "' and rm.menuid='" . $_POST['menuid'] . "'")->fetchAll();
                    $select_roles = "";
                    foreach ($search_role_menu_set as $role) {
                        if ($select_roles != "") {
                            $select_roles = $select_roles . ',';
                        }
                        $select_roles = $select_roles . $role['id'];
                    }
                    $result['select_roles'] = $select_roles;
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("查询失败，找不到信息！");
                }
                break;
            case "saveInfo":
                $connection->beginTransaction();
                $save_str = "update t_Menu set icon_class='" . $_POST['icon_class'] . "',icon_color='" . $_POST['icon_color'] . "',name='" . $_POST['menu_name'] . "',page_url='" . $_POST['page_url'] . "',sort=" . $_POST['sort'] . ",status=" . $_POST['status'] . ",model=" . $_POST['menu_model'] . ",opentype=" . $_POST['menu_opentype'] . ",dialog_w=" . $_POST['dialog_w'] . ",dialog_h=" . $_POST['dialog_h'] . " where id='" . $_POST['menuid'] . "'";
                if (!$connection->doExcute($save_str)) {
                    $connection->rollBack();
                    $result = \service\tools\ToolsClass::buildJSONError("保存信息失败！");
                    break;
                }
                $delete_str = "delete from t_Role_Menu_Set where menuid='" . $_POST['menuid'] . "' and roleid in (select id from t_Role)";
                if (!$connection->doExcute($delete_str)) {
                    $connection->rollBack();
                    $result = \service\tools\ToolsClass::buildJSONError("保存信息失败！");
                    break;
                }
                if (!empty($_POST['select_roles']) && $_POST['select_roles'] != "") {
                    $select_roles = $_POST['select_roles'];
                    foreach ($select_roles as $roleid) {
                        $insert_str = "insert into t_Role_Menu_Set(roleid,menuid) values('" . $roleid . "','" . $_POST['menuid'] . "')";
                        if (!$connection->doExcute($insert_str)) {
                            $connection->rollBack();
                            $result = \service\tools\ToolsClass::buildJSONError("保存信息失败！");
                            break;
                        }
                    }
                }
                $connection->commit();
                break;
        }
    }
    echo json_encode($result);
}
?>