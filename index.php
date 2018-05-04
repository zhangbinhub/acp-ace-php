<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/common/include.php';
$commonTools = new service\tools\ToolsClass(0);
$defaultApp = $commonTools->getDatasBySQL("select webroot from t_application where defaultApp=1");
if (count($defaultApp) > 0) {
    header('Location: ' . '/' . $defaultApp[0]['webroot']);
    exit();
}