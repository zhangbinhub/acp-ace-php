<?php
namespace service\tools\common;

class UUIDClass
{

    /**
     * 获取UUIDClass
     *
     * @return string
     */
    public static function getUUID()
    {
        $valueBeforeMD5 = UUIDClass::getLocalHost() . ':' . UUIDClass::currentTimeMillis() . ':' . UUIDClass::nextLong();
        $valueAfterMD5 = strtoupper(md5($valueBeforeMD5));
        return substr($valueAfterMD5, 0, 8) . '-' . substr($valueAfterMD5, 8, 4) . '-' . substr($valueAfterMD5, 12, 4) . '-' . substr($valueAfterMD5, 16, 4) . '-' . substr($valueAfterMD5, 20);
    }

    /**
     * 获取服务器地址
     *
     * @return string
     */
    private static function getLocalHost()
    {
        return $_SERVER["SERVER_ADDR"];
    }

    private static function currentTimeMillis()
    {
        list ($usec, $sec) = explode(" ", microtime());
        return $sec . substr($usec, 2, 3);
    }

    /**
     * 获取随机数
     *
     * @return string
     */
    private static function nextLong()
    {
        $tmp = rand(0, 1) ? '-' : '';
        return $tmp . rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999) . rand(100, 999) . rand(100, 999);
    }
}

?>