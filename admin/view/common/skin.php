<?php
$GLOBALS['skin_colorpicker'] = "no-skin";
$GLOBALS['settings_navbar'] = $GLOBALS['settings_sidebar'] = $GLOBALS['settings_breadcrumbs'] = $GLOBALS['settings_add_container'] = $GLOBALS['settings_hover'] = $GLOBALS['settings_compact'] = $GLOBALS['settings_highlight'] = $GLOBALS['settings_use_tabs'] = 0;
$user = admin\service\tools\ToolsClass::getUser();
$app = service\tools\ToolsClass::getApplicationInfo(admin\config\AdminConfig::getInstance()['webroot']);
if ($user != null) {
    $userid = $user->getId();
    $appid = $app->getId();
    $commonTools = new service\tools\ToolsClass(0);
    $searchResult = $commonTools->getDatasBySQL("select * from T_User_Configuration where userid='$userid' and appid='$appid'");
    if (count($searchResult) > 0) {
        $row = $searchResult[0];
        $GLOBALS['skin_colorpicker'] = $row['skin_colorpicker'];
        $GLOBALS['settings_navbar'] = (int)$row['settings_navbar'];
        $GLOBALS['settings_sidebar'] = (int)$row['settings_sidebar'];
        $GLOBALS['settings_breadcrumbs'] = (int)$row['settings_breadcrumbs'];
        $GLOBALS['settings_add_container'] = (int)$row['settings_add_container'];
        $GLOBALS['settings_hover'] = (int)$row['settings_hover'];
        $GLOBALS['settings_compact'] = (int)$row['settings_compact'];
        $GLOBALS['settings_highlight'] = (int)$row['settings_highlight'];
        $GLOBALS['settings_use_tabs'] = (int)$row['settings_use_tabs'];
    }
}
?>