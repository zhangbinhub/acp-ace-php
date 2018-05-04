<?php
namespace service\tools\security;

use config\SystemConfig;

class RSAUtilsClass
{

    /**
     * 获取公钥信息
     */
    public static function _getpublickeydetail()
    {
        $pubkeyid = self::_getpublickey();
        $publicdetail = openssl_pkey_get_details($pubkeyid);
        $result = array();
        $result['modulus'] = bin2hex($publicdetail['rsa']['n']);
        $result['exponent'] = bin2hex($publicdetail['rsa']['e']);
        return $result;
    }

    /**
     * 获取证书中的公钥
     * @return resource
     */
    public static function _getpublickey()
    {
        $systemConfig = SystemConfig::getInstance();
        $filepath = $systemConfig['security']['rsa']['publickey'];
        if (stripos($filepath, '/') == 0) {
            $key_content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $filepath);
        } else {
            $key_content = file_get_contents($filepath);
        }
        $pubkeyid = openssl_get_publickey($key_content);
        return $pubkeyid;
    }

    /**
     * 获取证书中的私钥
     * @return bool|resource
     */
    public static function _getprivatekey()
    {
        $systemConfig = SystemConfig::getInstance();
        $filepath = $systemConfig['security']['rsa']['privatekey'];
        if (stripos($filepath, '/') == 0) {
            $key_content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $filepath);
        } else {
            $key_content = file_get_contents($filepath);
        }
        $prikeyid = openssl_get_privatekey($key_content);
        return $prikeyid;
    }

    /**
     * 公钥加密 PKCS1_PADDING
     *
     * @param
     *            string 明文
     * @param
     *            int 补位方式，默认OPENSSL_PKCS1_PADDING
     * @return string 密文（base64编码）
     */
    public static function _encrypt_public($sourcestr, $padding = OPENSSL_PKCS1_PADDING)
    {
        $crypttext = '';
        $pubkeyid = self::_getpublickey();
        if (openssl_public_encrypt($sourcestr, $crypttext, $pubkeyid, $padding)) {
            return base64_encode($crypttext);
        } else {
            return '';
        }
    }

    /**
     * 私钥解密 PKCS1_PADDING
     *
     * @param
     *            string 密文（base64编码）
     * @param
     *            int 补位方式，默认OPENSSL_PKCS1_PADDING
     * @return string 明文
     */
    public static function _decrypt_private($crypttext, $padding = OPENSSL_PKCS1_PADDING)
    {
        $sourcestr = '';
        $prikeyid = self::_getprivatekey();
        $crypttext = base64_decode($crypttext);
        if (openssl_private_decrypt($crypttext, $sourcestr, $prikeyid, $padding)) {
            return $sourcestr;
        } else {
            return '';
        }
    }

    /**
     * 私钥加密 PKCS1_PADDING
     *
     * @param
     *            string 明文
     * @param
     *            int 补位方式，默认OPENSSL_PKCS1_PADDING
     * @return string 密文（base64编码）
     */
    public static function _encrypt_private($sourcestr, $padding = OPENSSL_PKCS1_PADDING)
    {
        $crypttext = '';
        $prikeyid = self::_getprivatekey();
        if (openssl_private_encrypt($sourcestr, $crypttext, $prikeyid, $padding)) {
            return base64_encode($crypttext);
        } else {
            return '';
        }
    }

    /**
     * 公钥解密 PKCS1_PADDING
     *
     * @param
     *            string 密文（base64编码）
     * @param
     *            int 补位方式，默认OPENSSL_PKCS1_PADDING
     * @return string 明文
     */
    public static function _decrypt_public($crypttext, $padding = OPENSSL_PKCS1_PADDING)
    {
        $sourcestr = '';
        $pubkeyid = self::_getpublickey();
        $crypttext = base64_decode($crypttext);
        if (openssl_public_decrypt($crypttext, $sourcestr, $pubkeyid, $padding)) {
            return $sourcestr;
        } else {
            return '';
        }
    }
}

?>