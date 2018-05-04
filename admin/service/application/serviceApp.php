<?php
require dirname(__FILE__) . '/../../view/common/serviceHead.php';
require $_SERVER['DOCUMENT_ROOT'] . '/service/base/baseQuery.php';
$result = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['oper'])) {
        $oper = $_POST['oper'];
        $commonTools = new \service\tools\ToolsClass(0);
        $connection = $commonTools->getDBConnection();
        switch ($oper) {
            case "edit":
                $search_result = $connection->doQuery("select appname from T_Application where id='" . $_POST['id'] . "' and type=0")->fetchAll();
                if (count($search_result) > 0 && intval($_POST['dbno']) > 0) {
                    $ret = '应用：' . $search_result[0]['appname'] . ' 只能使用【系统默认数据源】！';
                    echo $ret;
                    break;
                }
                if ($_POST['defaultapp'] == "是") {
                    $sql_str = "update t_application set defaultApp=0";
                    $connection->addBatch($sql_str);
                    $_POST['defaultapp'] = 1;
                } else {
                    $_POST['defaultapp'] = 0;
                }
                $update_str = "update T_Application set webroot='" . $_POST['webroot'] . "',appname='" . $_POST['appname'] . "',dbno=" . $_POST['dbno'] . ",
                    language='" . $_POST['language'] . "',copyright_owner='" . $_POST['copyright_owner'] . "',
                        copyright_begin='" . $_POST['copyright_begin'] . "',copyright_end='" . $_POST['copyright_end'] . "',defaultApp='" . $_POST['defaultapp'] . "',
                            version='" . $_POST['version'] . "',sort=" . $_POST['sort'] . " where id='" . $_POST['id'] . "'";
                $connection->addBatch($update_str);
                if ($connection->doExecBatch()) {
                    echo 'true';
                } else {
                    echo '保存失败！';
                }
                break;
            case "add":
                if ($_POST['defaultapp'] == "是") {
                    $sql_str = "update t_application set defaultApp='0'";
                    $connection->addBatch($sql_str);
                    $_POST['defaultapp'] = 1;
                } else {
                    $_POST['defaultapp'] = 0;
                }
                $insert_str = "insert into T_Application(id,webroot,appname,dbno,language,copyright_owner,copyright_begin,copyright_end,defaultApp,version,type,sort)
                    values('" . service\tools\common\UUIDClass::getUUID() . "','" . $_POST['webroot'] . "','" . $_POST['appname'] . "'," . $_POST['dbno'] . ",
                        '" . $_POST['language'] . "','" . $_POST['copyright_owner'] . "','" . $_POST['copyright_begin'] . "',
                            '" . $_POST['copyright_end'] . "'," . $_POST['defaultapp'] . ",'" . $_POST['version'] . "',1," . $_POST['sort'] . ")";
                $connection->addBatch($insert_str);
                if ($connection->doExecBatch()) {
                    echo 'true';
                } else {
                    echo '添加失败！';
                }
                break;
            case "del":
                $id = '\'' . str_replace(',', '\',\'', $_POST['id']) . '\'';
                $search_result = $connection->doQuery("select appname from T_Application where id in(" . $id . ") and type=0")->fetchAll();
                if (count($search_result) > 0) {
                    $ret = '应用：';
                    foreach ($search_result as $row) {
                        $ret = $ret . $row['appname'] . ',';
                    }
                    $ret = substr($ret, 0, strlen($ret) - 1) . ' 不可删除！';
                    echo $ret;
                    break;
                }
                $connection->addBatch("delete from T_User_Role_Set where roleid in (select id from t_role where appid in (" . $id . "))");
                $connection->addBatch("delete from T_Role_Menu_Set where menuid in (select id from t_menu where appid in (" . $id . "))");
                $connection->addBatch("delete from T_Menu where appid in (" . $id . ")");
                $connection->addBatch("delete from T_Role_Module_Func_Set where funcid in (select id from T_Module_Func where appid in (" . $id . "))");
                $connection->addBatch("delete from T_Module_Func where appid in (" . $id . ")");
                $connection->addBatch("delete from T_Role_Module_Set where moduleid in (select id from T_Module where appid in (" . $id . "))");
                $connection->addBatch("delete from T_Module where appid in (" . $id . ")");
                $connection->addBatch("delete from T_Role where appid in (" . $id . ")");
                $connection->addBatch("delete from T_Application_Info where appid in (" . $id . ")");
                $connection->addBatch("delete from T_Application_Link where appid in (" . $id . ")");
                $connection->addBatch("delete from T_Application where id in (" . $id . ")");
                if ($connection->doExecBatch()) {
                    echo 'true';
                } else {
                    echo '删除失败！';
                }
                break;
            case "getLoginInfo":
                $appid = $_POST['appid'];
                $search_result = $commonTools->getDatasBySQL("select t.login_date,count(t.userid) as count from t_user_loginrecord t where appid='" . $appid . "' group by t.login_date order by t.login_date asc");
                if (count($search_result) > 0) {
                    $begindate = $search_result[0]['login_date'];
                    $enddate = $search_result[count($search_result) - 1]['login_date'];
                    $currdate = $begindate;
                    $count = (strtotime($enddate) - strtotime($begindate)) / 86400;
                    $index = 0;
                    for ($i = 0; $i < $count; $i++) {
                        if ($currdate == $search_result[$index]['login_date']) {
                            $data = array(
                                $search_result[$index]['login_date'],
                                intval($search_result[$index]['count'])
                            );
                            $index++;
                        } else {
                            $data = array(
                                $currdate,
                                0
                            );
                        }
                        $currdate = date("Y-m-d", strtotime($currdate) + 86400);
                        array_push($result, $data);
                    }
                    foreach ($search_result as $row) {
                        $data = array(
                            $row['login_date'],
                            intval($row['count'])
                        );
                        array_push($result, $data);
                    }
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError('没有数据');
                }
                echo json_encode($result);
                break;
        }
    } else {
        $sqlArray = array();
        $sqlArray[0] = "*";
        $sqlArray[1] = "(select id,webroot,appname,dbno,language,copyright_owner,copyright_begin,copyright_end,version,
            case defaultApp when 1 then '是' else '否' end as defaultApp,sort from T_Application)t";
        $result = doQuery($sqlArray);
        echo json_encode($result);
        die();
    }
}
?>