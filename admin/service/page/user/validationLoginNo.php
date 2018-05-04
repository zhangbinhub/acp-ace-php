<?php
require dirname(__FILE__) . '/../../../view/common/serviceHead.php';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $loginno = $_POST['loginno'];
    $commonTools = new service\tools\ToolsClass(0);
    $sqlstr = "select * from t_user where loginno='" . $loginno . "' ";
    if (isset($_POST['id']) && $_POST['id'] && $_POST['id'] != null && $_POST['id'] != '') {
        $sqlstr = $sqlstr . "and id<>'" . $_POST['id'] . "'";
    }
    $result = $commonTools->getDatasBySQL($sqlstr);
    if (count($result) > 0) {
        echo "false";
    } else {
        echo "true";
    }
} else {
    echo "false";
}
?>