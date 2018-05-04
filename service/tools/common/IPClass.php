<?php
namespace service\tools\common;

class IPClass
{

    /**
     * 获取客户端真实IP
     *
     * @return string $ip
     */
    public static function getRemoteIP()
    {
        if (! empty($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
        } else {
            $ip = "unknown";
        }
        if (strpos($ip, ',') != false) {
            $ip = substr($ip, 0, strpos($ip, ','));
        }
        return $ip;
    }
}
?>