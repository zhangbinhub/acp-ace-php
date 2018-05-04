<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/common/include.php';
$currTools = new \service\tools\ToolsClass();
$params = array();
foreach ($_REQUEST as $key => $value) {
    if ($key == "url" || $key == "comType" || $key == "timeOut" || $key == "charset" || $key == "dataType") {
        continue;
    }
    $params[$key] = $value;
}
$timeout = intval($_REQUEST['timeOut']) / 1000;
$charset = $_REQUEST['charset'];
switch (intval($_REQUEST['comType'])) { // 0 一般请求，1 下载请求
    case 0:
        $url = $_REQUEST['url'];
        $result = $currTools->doHttp($url, $params, "POST", $charset, $timeout, $_REQUEST['dataType']);
        if (empty($result)) {
            $result = \service\tools\ToolsClass::buildJSONErrorStr('请求失败！');
        }
        echo $result;
        break;
    case 1:
        $url = $_REQUEST['url'];
        $filename = $_REQUEST['name'];
        $result = $currTools->doHttpDownload($url . "download", $params, $filename, $charset, $timeout);
        if (empty($result)) {
            $result = \service\tools\ToolsClass::buildJSONErrorStr('请求失败！');
        }
        echo $result;
        break;
}