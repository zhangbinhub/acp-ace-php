<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/common/include.php';
$portalConfig = portal\config\PortalConfig::getInstance();
$backService = array();
$backService['httphostIn'] = $portalConfig['backService']['httphostIn'];
$backService['httptimeout'] = intval($portalConfig['backService']['httptimeout']);
$backService['charset'] = $portalConfig['backService']['charset'];
$GLOBALS['backservice'] = $backService;
$GLOBALS['webroot'] = '/' . $portalConfig['webroot'];
$GLOBALS['application'] = service\tools\ToolsClass::getApplicationInfo($portalConfig['webroot']);
$GLOBALS['app_dbno'] = $GLOBALS['application']->getDbno();
$GLOBALS['html_attr'] = ' xmlns="http://www.w3.org/1999/xhtml" lang="' . $GLOBALS['application']->getLanguage() . '" xml:lang="' . $GLOBALS['application']->getLanguage() . '"';
$GLOBALS['loginpage_url'] = $portalConfig['page-url']['login'];
$GLOBALS['logoutpage_url'] = $portalConfig['page-url']['logout'];
$GLOBALS['timeoutpage_url'] = $portalConfig['page-url']['timeout'];
$GLOBALS['mainpage_url'] = $portalConfig['page-url']['main'];
$GLOBALS['homepage_url'] = $portalConfig["page-url"]["home"];

require dirname(__FILE__) . '/session.php';
if ($GLOBALS['session_timeout']) {
    header("HTTP/1.1 403 login timeout!");
    header("status: 403 login timeout!");
    echo ("<span style=\"color:red\">login timeout!</span> <script language='javascript'>window.top.location.href=\"" . $GLOBALS['timeoutpage_url'] . "\"</script>");
    exit();
}

$GLOBALS['curr_user'] = portal\service\tools\ToolsClass::getUser();