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
                if ($_POST['status'] == "启用") {
                    $_POST['status'] = 1;
                } else {
                    $_POST['status'] = 0;
                }
                $oldconfname = $connection->doQuery("select confname from T_RuntimeConfig where id='" . $_POST['id'] . "'")->fetchAll()[0]['confname'];
                if ($oldconfname != $_POST['confname']) {
                    echo '参数名不能更改！';
                    break;
                }
                $update_str = "update T_RuntimeConfig set confvalue='" . $_POST['confvalue'] . "',confdes='" . $_POST['confdes'] . "',status=" . $_POST['status'] . "
                    where id='" . $_POST['id'] . "'";
                $connection->addBatch($update_str);
                if ($connection->doExecBatch()) {
                    echo 'true';
                } else {
                    echo '保存失败！';
                }
                break;
            case "add":
                $oldconf = $connection->doQuery("select * from T_RuntimeConfig where confname='" . trim($_POST['confname']) . "'")->fetchAll();
                if (count($oldconf) > 0) {
                    echo '参数名已存在，不允许重复！';
                    break;
                }
                if ($_POST['status'] == "启用") {
                    $_POST['status'] = 1;
                } else {
                    $_POST['status'] = 0;
                }
                $insert_str = "insert into T_RuntimeConfig(id,confname,confvalue,confdes,status,type)
                    values('" . service\tools\common\UUIDClass::getUUID() . "','" . trim($_POST['confname']) . "','" . $_POST['confvalue'] . "','" . $_POST['confdes'] . "'," . $_POST['status'] . ",1)";
                $connection->addBatch($insert_str);
                if ($connection->doExecBatch()) {
                    echo 'true';
                } else {
                    echo '添加失败！';
                }
                break;
            case "del":
                $id = '\'' . str_replace(',', '\',\'', $_POST['id']) . '\'';
                $search_result = $connection->doQuery("select confname from T_RuntimeConfig where id in(" . $id . ") and type=0")->fetchAll();
                if (count($search_result) > 0) {
                    $ret = '参数：';
                    foreach ($search_result as $row) {
                        $ret = $ret . $row['confname'] . ',';
                    }
                    $ret = substr($ret, 0, strlen($ret) - 1) . ' 不可删除！';
                    echo $ret;
                    break;
                }
                $connection->addBatch("delete from T_RuntimeConfig where id in (" . $id . ")");
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
        $sqlArray[1] = "(select id,confname,confvalue,confdes,case status when 1 then '启用' else '禁用' end as status from T_RuntimeConfig)t";
        $result = doQuery($sqlArray);
        echo json_encode($result);
        die();
    }
}
?>