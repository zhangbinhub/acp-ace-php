<?php
header('Expires: -1');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/skin.php';
$GLOBALS['body_class'] = 'page-body ' . $GLOBALS['skin_colorpicker'];
header("X-UA-compatible: IE=edge,chrome=1");
$doctype = '<!DOCTYPE html>';
echo $doctype;

if ($GLOBALS['curr_user'] != null) {
    $currUrl = substr($_SERVER['REDIRECT_URL'], strlen($GLOBALS['webroot']));
    $userid = $GLOBALS['curr_user']->getId();
    $appid = $GLOBALS['application']->getId();
    $commonTools = new \service\tools\ToolsClass(0);
    $menu = $commonTools->getDatasBySQL("select * from t_menu where appid='" . $appid . "' and page_url='" . $currUrl . "' and status=1");
    $menuids = "";
    if (count($menu) > 0) {
        foreach ($menu as $m) {
            if ($menuids != "") {
                $menuids = $menuids . ",";
            }
            $menuids = "'" . $m['id'] . "'";
        }
        $search = $commonTools->getDatasBySQL("select * from t_role_menu_set rm inner join t_role r on rm.roleid=r.id inner join t_user_role_set ur on ur.roleid=r.id where r.appid='" . $appid . "' and ur.userid='" . $userid . "' and rm.menuid in (" . $menuids . ")");
        if (count($search) == 0) {
            header("HTTP/1.1 403 no permissions!");
            header("status: 403 no permissions!");
            echo("<span style=\"color:red\">no permissions!</span>");
            exit();
        }
    }
}