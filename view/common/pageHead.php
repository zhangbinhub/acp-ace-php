<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/common/include.php';
ob_start();
$systemConfig = \config\SystemConfig::getInstance();
$GLOBALS['charset'] = $systemConfig['charset'];
header("content-type: text/html; charset=" . $GLOBALS['charset']);
$GLOBALS['alone_page'] = true;
if (isset($_REQUEST['_opentype'])) {
    switch ($_REQUEST['_opentype']) {
        case "0":
            $GLOBALS['alone_page'] = false;
            break;
        case "1":
            $GLOBALS['alone_page'] = true;
            break;
        case "2":
            $GLOBALS['alone_page'] = false;
            break;
        case "3":
            $GLOBALS['alone_page'] = true;
            break;
        case "4":
            $GLOBALS['alone_page'] = true;
            break;
        case "5":
            $GLOBALS['alone_page'] = true;
            break;
        default:
            $GLOBALS['alone_page'] = false;
            break;
    }
}