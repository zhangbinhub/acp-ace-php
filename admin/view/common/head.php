<?php
require dirname(__FILE__) . '/beforehead.php';
require $_SERVER['DOCUMENT_ROOT'] . '/view/common/head.php';
if ($GLOBALS['alone_page']) {
    echo '<link rel="stylesheet" href="' . $GLOBALS['webroot'] . '/style/customStyle.css?v=1.0.0" />';
    echo '<script type="text/javascript">';
    echo 'var G_webrootPath = "' . $GLOBALS['webroot'] . '";';
    echo 'var G_backService = {httphostIn:"' . $GLOBALS['backService']['httphostIn'] . '",httptimeout:' . $GLOBALS['backService']['httptimeout'] . ',
            charset:"' . $GLOBALS['backService']['charset'] . '"};';
    echo 'var G_logoutpage_url = "' . $GLOBALS['logoutpage_url'] . '";';
    echo 'var G_timeoutpage_url = "' . $GLOBALS['timeoutpage_url'] . '";';
    echo 'var G_mainpage_url = "' . $GLOBALS['mainpage_url'] . '";';
    echo 'var G_homepage_url = "' . $GLOBALS['homepage_url'] . '";';
    echo 'var G_settings_use_tabs = ' . ($GLOBALS['settings_use_tabs'] === 1 ? 'true' : 'false') . ';';
    echo 'var G_curr_user = null;';
    if ($GLOBALS['curr_user'] != null) {
        echo 'G_curr_user = {id:"' . $GLOBALS['curr_user']->getId() . '",name:"' . $GLOBALS['curr_user']->getName() . '",loginno:"' . $GLOBALS['curr_user']->getLoginno() . '",level:' . $GLOBALS['curr_user']->getLevels() . '};';
    }
    echo '</script>';
    require dirname(__FILE__) . '/afterhead.php';
}