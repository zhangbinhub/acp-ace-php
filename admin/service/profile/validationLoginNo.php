<?php
require dirname(__FILE__) . '/../../view/common/serviceHead.php';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = admin\service\tools\ToolsClass::getUser()->getId();
    $loginno = $_POST['loginno'];
    $commonTools = new service\tools\ToolsClass(0);
    $result = $commonTools->getDatasBySQL("select * from t_user where loginno='" . $loginno . "' and id<>'" . $id . "'");
    if (count($result) > 0) {
        echo "false";
    } else {
        echo "true";
    }
} else {
    echo "false";
}
?>