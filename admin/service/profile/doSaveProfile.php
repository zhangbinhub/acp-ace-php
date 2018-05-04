<?php
require dirname(__FILE__) . '/../../view/common/serviceHead.php';
$result = array();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $avatar = $_POST['avatar'];
    $username = $_POST['username'];

    if ($username == '') {
        $result = \service\tools\ToolsClass::buildJSONError('保存失败：姓名不能为空！');
        echo json_encode($result);
        die();
    }

    $opassword = $password = null;
    if (isset($_POST['opassword'])) {
        $opassword = $_POST['opassword'];
    }
    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    }
    $user = admin\service\tools\ToolsClass::getUser();
    $id = $user->getId();
    $commonTools = new service\tools\ToolsClass(0);
    if ($password != null && $opassword == null) {
        $result = \service\tools\ToolsClass::buildJSONError('保存失败：原密码错误');
    } else {
        $connection = $commonTools->getDBConnection();
        if ($password != null) {
            $psw = $commonTools->getDatasBySQL("select * from t_user where id='" . $id . "' and password='" . $opassword . "'");
            if (count($psw) > 0) {
                $connection->addBatch("update t_user set name='" . $username . "',password='" . $password . "' where id='" . $id . "'");
                $connection->addBatch("update t_User_Info set portrait='" . $avatar . "' where userid='" . $id . "'");
            } else {
                $result = \service\tools\ToolsClass::buildJSONError('保存失败：原密码错误');
                echo json_encode($result);
                die();
            }
        } else {
            $connection->addBatch("update t_user set name='" . $username . "' where id='" . $id . "'");
            $connection->addBatch("update t_User_Info set portrait='" . $avatar . "' where userid='" . $id . "'");
        }
        if ($connection->doExecBatch()) {
            $user->setName($username);
            $user->setPortrait($avatar);
            admin\service\tools\ToolsClass::setUser($user);
            $result['result'] = '保存成功';
        } else {
            $result = \service\tools\ToolsClass::buildJSONError('保存失败！');
        }
    }
} else {
    $result = \service\tools\ToolsClass::buildJSONError('保存失败：请求类型出错！');
}
echo json_encode($result);