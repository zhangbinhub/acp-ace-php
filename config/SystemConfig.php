<?php
namespace config;

class SystemConfig implements base\IBaseConfig
{

    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            $instance = self::generateConfigInfo();
            self::$instance = $instance;
        }
        return self::$instance;
    }

    private static function generateConfigInfo()
    {
        $config = array();

        /**
         * 操作系统字符集
         */
        $config["os_charset"] = "gbk";

        /**
         * 页面字符集
         */
        $config["charset"] = "utf-8";

        /**
         * 日志文件所在目录
         */
        $config["logger"]["folder"] = "/logs";
        /**
         * 日志文件名规则
         */
        $config["logger"]["logname"] = "Y-m-d";
        /**
         * 每条日志开头的时间格式
         */
        $config["logger"]["loghead"] = "Y-m-d H:i:s";

        /**
         * RSA公钥路径
         */
        $config["security"]["rsa"]["publickey"] = "/service/tools/security/RSA/rsa_public_key.pem";
        /**
         * RSA私钥路径
         */
        $config["security"]["rsa"]["privatekey"] = "/service/tools/security/RSA/rsa_private_key.pem";

        /**
         * session超时时间，单位：秒，0为不超时
         */
        $config["session"]["timeout"] = 3600;

        return $config;
    }
}

?>