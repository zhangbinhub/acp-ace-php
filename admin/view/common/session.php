<?php
require $_SERVER['DOCUMENT_ROOT'] . '/view/common/session.php';
$GLOBALS['session_timeout'] = false;
if ($_SERVER['REDIRECT_URL'] != $GLOBALS['webroot']
    && $_SERVER['REDIRECT_URL'] != $GLOBALS['webroot'] . "/"
    && $_SERVER['REDIRECT_URL'] != $GLOBALS['webroot'] . "/index"
    && $_SERVER['REDIRECT_URL'] != $GLOBALS['loginpage_url']
    && $_SERVER['REDIRECT_URL'] != $GLOBALS['logoutpage_url']
    && $_SERVER['REDIRECT_URL'] != $GLOBALS['timeoutpage_url']) {
    if (empty($_SESSION) || !isset($_SESSION[admin\service\tools\ToolsClass::$LOGIN_USER_STR])) {
        $GLOBALS['session_timeout'] = true;
    } else {
        $ip = \service\tools\common\IPClass::getRemoteIP();
        $userid = \admin\service\tools\ToolsClass::getUser()->getId();
        $onlineusers = \service\user\UserManagerClass::getOnlineUsers($GLOBALS['application']->getId(), $userid, $ip);
        if (count($onlineusers) > 0) {
            $singlePoint = \admin\config\AdminConfig::getInstance()['singlePoint'];
            \service\user\UserManagerClass::updateOnlineUser($GLOBALS['application']->getId(), $userid, $ip, $singlePoint);
        } else {
            $GLOBALS['session_timeout'] = true;
        }
    }
}