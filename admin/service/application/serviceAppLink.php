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
                $update_str = "update T_Application_Link set link_type='" . $_POST['link_type'] . "',link_name='" . $_POST['link_name'] . "',link_url='" . $_POST['link_url'] . "',link_image_url='" . $_POST['link_image_url'] . "',isEnabled='" . $_POST['isenabled'] . "' where id='" . $_POST['id'] . "'";
                $connection->addBatch($update_str);
                if ($connection->doExecBatch()) {
                    echo 'true';
                } else {
                    echo '保存失败！';
                }
                break;
            case "add":
                $insert_str = "insert into T_Application_Link(id,appid,link_type,link_name,link_url,link_image_url,isEnabled) values('" . service\tools\common\UUIDClass::getUUID() . "','" . $_GET['appid'] . "','" . $_POST['link_type'] . "','" . $_POST['link_name'] . "','" . $_POST['link_url'] . "','" . $_POST['link_image_url'] . "','" . $_POST['isenabled'] . "')";
                $connection->addBatch($insert_str);
                if ($connection->doExecBatch()) {
                    echo 'true';
                } else {
                    echo '添加失败！';
                }
                break;
            case "del":
                $id = '\'' . str_replace(',', '\',\'', $_POST['id']) . '\'';
                $connection->addBatch("delete from T_Application_Link where id in (" . $id . ")");
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
        $sqlArray[1] = "T_Application_Link";
        $sqlArray[2] = "where appid='" . $_POST['appid'] . "'";
        $result = doQuery($sqlArray);
        echo json_encode($result);
        die();
    }
}
?>