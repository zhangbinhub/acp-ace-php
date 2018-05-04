<?php
require dirname(__FILE__) . '/../../view/common/serviceHead.php';

function getBreadcrumb($menuid)
{
    $result = "";
    global $commonTools;
    $currMenu = $commonTools->getDatasBySQL("select *,case when parentid in (select id from t_menu) then 'false' else 'true' end as isparent
        from T_Menu where status=1 and id='$menuid'")[0];
    if ($currMenu['isparent'] == 'false') {
        $result = getBreadcrumb($currMenu['parentid']);
    }
    $i_class = "ace-icon fa";
    if (! empty($currMenu['icon_class'])) {
        $i_class = $i_class . " " . $currMenu['icon_class'];
    }
    $i_trag = "";
    if ($i_class != "ace-icon fa") {
        if (! empty($currMenu['icon_color'])) {
            $i_trag = '<i class="' . $i_class . '" style="color:' . $currMenu['icon_color'] . '"></i>';
        } else {
            $i_trag = '<i class="' . $i_class . '"></i>';
        }
    }
    $result = $result . '<li>' . $i_trag . ' ' . $currMenu['name'] . '</li>';
    return $result;
}

$result = "";
$commonTools = new service\tools\ToolsClass(0);
$menuid = $_POST['id'];
$currMenu = $commonTools->getDatasBySQL("select *,case when parentid in (select id from t_menu) then 'false' else 'true' end as isparent
    from T_Menu where status=1 and id='$menuid'")[0];
if ($currMenu['isparent'] == 'false') {
    $result = getBreadcrumb($currMenu['parentid']);
}
$i_class = "ace-icon fa";
if (! empty($currMenu['icon_class'])) {
    $i_class = $i_class . " " . $currMenu['icon_class'];
}
$i_trag = "";
if ($i_class != "ace-icon fa") {
    if (! empty($currMenu['icon_color'])) {
        $i_trag = '<i class="' . $i_class . '" style="color:' . $currMenu['icon_color'] . '"></i>';
    } else {
        $i_trag = '<i class="' . $i_class . '"></i>';
    }
}
$url = '';
if (intval($currMenu['model']) == 0) {
    $url = $GLOBALS['webroot'] . $currMenu['page_url'];
    $url = "javascript:admin_tools_obj.loadPageInDiv({menuid:'" . $currMenu['id'] . "',title:'" . $currMenu['name'] . "',path:'" . $url . "'});";
} else {
    $url = $currMenu['page_url'];
}
$result = $result . '<li class="active">' . $i_trag . ' <a href="' . $url . '" class="main_menu" id="bread_' . $currMenu['id'] . '">' . $currMenu['name'] . '</a></li>';
echo $result;
?>