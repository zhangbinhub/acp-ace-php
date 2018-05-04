<?php

namespace admin\service\login;

use admin\config\AdminConfig;
use admin\service\tools\ToolsClass;
use service\tools\logger\LoggerClass;
use service\user\UserManagerClass;

class LoginClass
{

    /**
     * 登录
     * @param string $username
     * @param string $password
     * @param string $yzm
     * @throws \Exception
     * @return array
     */
    public function doLogin($username, $password, $yzm)
    {
        $result = array();
        try {
            if (isset($_SESSION[ToolsClass::$LOGIN_YZM_STR])) {
                $yzm = strtolower($yzm);
                $session_yzm = $_SESSION[ToolsClass::$LOGIN_YZM_STR];
                unset($_SESSION[ToolsClass::$LOGIN_YZM_STR]);
                if ($yzm != $session_yzm) {
                    $result[0] = 1;
                    $result[1] = "验证码输入不正确！";
                } else {
                    $result = UserManagerClass::doLogin($username, $password, $session_yzm, ToolsClass::$LOGIN_USER_STR, $GLOBALS['application'], AdminConfig::getInstance()['singlePoint']);
                }
            } else {
                $result[0] = 3;
                $result[1] = "登录失败：非法登录";
                LoggerClass::error('[应用:' . AdminConfig::getInstance()['webroot'] . ']' . '非法登录');
            }
        } catch (\Exception $e) {
            $result[0] = 3;
            $result[1] = "登录失败：" . $e->getMessage();
            LoggerClass::error_e('[应用:' . AdminConfig::getInstance()['webroot'] . ']' . $e->getMessage(), $e);
        }
        return $result;
    }

    /**
     * 退出登录
     * @param string $userid
     * @return boolean
     */
    public function doLogout($userid)
    {
        if (UserManagerClass::doLogout($userid, ToolsClass::$LOGIN_USER_STR, $GLOBALS['application'])) {
            header('Location: ' . $GLOBALS['loginpage_url']);
            die();
        } else {
            return false;
        }
    }
}

?>