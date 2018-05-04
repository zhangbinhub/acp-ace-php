<?php

namespace service\tools\security;

class AESUtilsClass
{

    private $mode = "aes-128-ecb";

    private $option = OPENSSL_RAW_DATA;

    /**
     * 解密
     *
     * @param string $encryptedText
     * @param string $key
     * @return string
     */
    public function _decrypt($encryptedText, $key)
    {
        $decrypted_text = openssl_decrypt(base64_decode($encryptedText), $this->mode, $key, $this->option);
        return $decrypted_text;
    }

    /**
     * 加密
     *
     * @param string $plainText
     * @param string $key
     * @return string
     */
    public function _encrypt($plainText, $key)
    {
        $cyper_text = openssl_encrypt($plainText, $this->mode, $key, $this->option);
        $rt = base64_encode($cyper_text);
        return $rt;
    }
}

?>