<?php
session_start();
$systemConfig = \config\SystemConfig::getInstance();
$timeout = intval($systemConfig['session']['timeout']);
if (isset($_SESSION['expiretime'])) {
    if ($_SESSION['expiretime'] < time()) {
        session_unset();
        session_destroy();
    } else {
        $_SESSION['expiretime'] = time() + $timeout;
    }
} else {
    $_SESSION['expiretime'] = time() + $timeout;
}