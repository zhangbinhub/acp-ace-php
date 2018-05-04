<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/common/include.php';
$adminConfig = admin\config\AdminConfig::getInstance();
$backService = array();
$backService['httphostIn'] = $adminConfig['backService']['httphostIn'];
$backService['httptimeout'] = intval($adminConfig['backService']['httptimeout']);
$backService['charset'] = $adminConfig['backService']['charset'];
$GLOBALS['backservice'] = $backService;
$GLOBALS['webroot'] = '/' . $adminConfig['webroot'];
$GLOBALS['application'] = service\tools\ToolsClass::getApplicationInfo($adminConfig['webroot']);
$GLOBALS['app_dbno'] = $GLOBALS['application']->getDbno();
$GLOBALS['html_attr'] = ' xmlns="http://www.w3.org/1999/xhtml" lang="' . $GLOBALS['application']->getLanguage() . '" xml:lang="' . $GLOBALS['application']->getLanguage() . '"';
$GLOBALS['loginpage_url'] = $adminConfig['page-url']['login'];
$GLOBALS['logoutpage_url'] = $adminConfig['page-url']['logout'];
$GLOBALS['timeoutpage_url'] = $adminConfig['page-url']['timeout'];
$GLOBALS['mainpage_url'] = $adminConfig['page-url']['main'];
$GLOBALS['homepage_url'] = $adminConfig["page-url"]["home"];

require dirname(__FILE__) . '/session.php';
if ($GLOBALS['session_timeout']) {
    header("HTTP/1.1 403 login timeout!");
    header("status: 403 login timeout!");
    echo ("<span style=\"color:red\">login timeout!</span> <script language='javascript'>window.top.location.href=\"" . $GLOBALS['timeoutpage_url'] . "\"</script>");
    exit();
}

$GLOBALS['curr_user'] = admin\service\tools\ToolsClass::getUser();