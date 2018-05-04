<?php
require dirname(__FILE__) . '/../../view/common/serviceHead.php';

$commonTools = new service\tools\ToolsClass(0);
$search_SQL = "select * from t_menu m where m.appid='" . $GLOBALS['application']->getId() . "' and m.name='?' and m.status=1 and m.id in
    (select tm.id from t_menu tm INNER JOIN t_role_menu_set rm on tm.id=rm.menuid
        INNER JOIN t_user_role_set ur on rm.roleid=ur.roleid INNER JOIN t_role r on r.id=ur.roleid
    where r.appid='" . $GLOBALS['application']->getId() . "' and ur.userid='" . portal\service\tools\ToolsClass::getUser()->getId() . "')";

function getLastMenuId($menuenames, $index, $parentid)
{
    global $commonTools, $search_SQL;
    $curr_result = $commonTools->getDatasBySQL(str_replace('?', $menuenames[$index], $search_SQL) . " and parentid='" . $parentid . "'");
    if (empty($curr_result) || count($curr_result) == 0) {
        return 'nosource';
    } else {
        if (count($menuenames) > $index + 1) {
            $resultmenuid = 'nosource';
            foreach ($curr_result as $curr) {
                $lastmenuid = getLastMenuId($menuenames, ++ $index, $curr['id']);
                if ($lastmenuid != 'nosource') {
                    $resultmenuid = $lastmenuid;
                    break;
                }
            }
            return $resultmenuid;
        } else {
            return $curr_result[0]['id'];
        }
    }
}

$menuePath = $_POST['menuePath'];
$menuenames = explode('/', $menuePath);
$search_root_SQL = str_replace('?', $menuenames[0], $search_SQL);
$menu_root_result = $commonTools->getDatasBySQL($search_root_SQL);
if (empty($menu_root_result) || count($menu_root_result) == 0) {
    echo 'nosource';
} else {
    if (count($menuenames) > 1) {
        $resultmenuid = 'nosource';
        foreach ($menu_root_result as $firstmenu) {
            $lastmenuid = getLastMenuId($menuenames, 1, $firstmenu['id']);
            if ($lastmenuid != 'nosource') {
                $resultmenuid = $lastmenuid;
                break;
            }
        }
        echo $resultmenuid;
    } else {
        echo $menu_root_result[0]['id'];
    }
}
?>