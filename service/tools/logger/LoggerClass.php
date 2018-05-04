<?php
namespace service\tools\logger;

use config\SystemConfig;
use service\tools\common\IPClass;

class LoggerClass
{

    public static function info($message)
    {
        self::writeLog("INFO", $message);
    }

    public static function debug($message)
    {
        self::writeLog("DEBUG", $message);
    }

    public static function error($message)
    {
        self::writeLog("ERROR", $message);
    }

    /**
     * @param $message
     * @param \Exception $e
     */
    public static function error_e($message, $e)
    {
        self::writeLog("ERROR", $message . "\n" . $e->getTraceAsString());
    }

    private static function writeLog($level, $message)
    {
        $config = self::getConfig();
        if ($config != null) {
            $ROOT = $_SERVER["DOCUMENT_ROOT"];
            $filename = $ROOT . $config["folder"] . "/log_" . date($config["logname"]) . ".log";
            if (! file_exists($filename)) {
                $myfile = fopen($filename, "w");
                fclose($myfile);
            }
            error_log(date($config["loghead"]) . " [" . IPClass::getRemoteIP() . "]  [" . $level . "]  :" . $message . "\n", 3, $filename);
        }
    }

    private static function getConfig()
    {
        $config = SystemConfig::getInstance();
        $logger = $config['logger'];
        if ($logger != null) {
            return array(
                "folder" => $logger['folder'],
                "logname" => $logger['logname'],
                "loghead" => $logger['loghead']
            );
        } else {
            return array(
                "folder" => "/cms/logs",
                "logname" => "Y-m-d",
                "loghead" => "Y-m-d H:i:s"
            );
        }
    }
}

?>