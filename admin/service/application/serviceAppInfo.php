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
                $update_str = "update T_Application_Info set info_name='" . $_POST['info_name'] . "',info_value='" . $_POST['info_value'] . "',isEnabled='" . $_POST['isenabled'] . "' where id='" . $_POST['id'] . "'";
                $connection->addBatch($update_str);
                if ($connection->doExecBatch()) {
                    echo 'true';
                } else {
                    echo '保存失败！';
                }
                break;
            case "add":
                $insert_str = "insert into T_Application_Info(id,appid,info_name,info_value,isEnabled) values('" . service\tools\common\UUIDClass::getUUID() . "','" . $_GET['appid'] . "','" . $_POST['info_name'] . "','" . $_POST['info_value'] . "','" . $_POST['isenabled'] . "')";
                $connection->addBatch($insert_str);
                if ($connection->doExecBatch()) {
                    echo 'true';
                } else {
                    echo '添加失败！';
                }
                break;
            case "del":
                $id = '\'' . str_replace(',', '\',\'', $_POST['id']) . '\'';
                $connection->addBatch("delete from T_Application_Info where id in (" . $id . ")");
                if ($connection->doExecBatch()) {
                    echo 'true';
                } else {
                    echo '删除失败！';
                }
                break;
        }
    } else {
        $sqlArray = array();
        $sqlArray[0] = "*";
        $sqlArray[1] = "T_Application_Info";
        $sqlArray[2] = "where appid='" . $_POST['appid'] . "'";
        $result = doQuery($sqlArray);
        echo json_encode($result);
        die();
    }
}
?>