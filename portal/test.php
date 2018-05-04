<?php
require $_SERVER['DOCUMENT_ROOT'] . '/view/common/pageHead.php';
echo dirname(__FILE__) . '<br/>';
phpinfo();
$_aryData = array("123", "", "dfsafaf");
sort($_aryData, SORT_STRING);
echo implode("_", $_aryData);
var_dump(time());
var_dump($_SERVER);
$testArray = array('key' => 'key1', 'value' => 'value2');
var_dump(implode("_", $testArray));
var_dump(base64_encode("尼佛那老师的"));
ob_start();

$instance1 = \config\SystemConfig::getInstance();

$GLOBALS['application'] = \service\tools\ToolsClass::getApplicationInfo('portal');
$GLOBALS['app_dbno'] = $GLOBALS['application']->getDbno();
$tools = new service\tools\ToolsClass();

$instance = \config\DataBaseConfig::getInstance();
echo '数据库配置：<br/>';
var_dump($instance);
echo '系统配置：<br/>';
var_dump($instance1);
echo '服务器信息：<br/>';
var_dump($_SERVER);
echo "browser：" . service\tools\ToolsClass::getBrowser() . '<br/>';
session_status();

$key = 'OW9puTMy5WcC9lgp';
$text = "/file/param/secret_key";
$encrypt = $tools->encryptAES($text, $key);
echo 'AES加密解密测试：<br/>';
echo '密文：' . $encrypt . '<br/>';
echo '明文：' . $tools->decryptAES($encrypt, $key) . '<br/>';

$sourcestr = "RSA加密测试明文";
$encrypttext = $tools->encryptRSA_public($sourcestr);
echo '<br/>RSA加密解密测试：<br/>';
echo '密文：' . $encrypttext . '<br/>';
echo '明文：' . $tools->decryptRSA_private($encrypttext);

$sourcestr = "RSA加密测试明文";
$encrypttext = $tools->encryptRSA_private($sourcestr);
echo '<br/>RSA加密解密测试：<br/>';
echo '私钥加密密文：' . $encrypttext . '<br/>';
echo '公钥解密明文：' . $tools->decryptRSA_public($encrypttext);

var_dump(\service\tools\security\RSAUtilsClass::_getpublickeydetail());

var_dump($tools->getDatasBySQL("select * from t_user"));

// $connection0 = new \service\tools\connection\ConnectionFactoryClass(2);
// $file = fopen('E:/11.jpg', 'rb');
// $file = fopen('E:/22.jpg', 'rb');
// $connection0->doExcuteByPre('update t_test_blob1 set blobv=? where id=?', array(
// 'bbbbbbbbbbbbbbbbfsdafdasfsadfdsafafdasfaf',
// 1
// ));
// $connection0->doInsertLOB('t_test_blob', 'id', 1, 'BLOBV', $file);
// $connection0->doUpdateLOB('t_test_blob', 'id', 1, 'BLOBV', $file);
// var_dump($connection0->doQueryLOB('t_test_blob', 'id', 1, 'BLOBV'));
// var_dump($connection0->doQueryLOB('t_test_blob1', 'id', 1, 'BLOBV'));
// $img = $connection0->doQueryLob('t_test_blob', 'id', 1, 'BLOBV');
// ob_clean();
// header("Content-Type: image/jpeg");
// echo $img;
// $connection0->doExcuteByPre('insert into t_test_blob1(id,name,blobv) values(?,?,?)',array(
// 1,'123131','fsdafdasfsadfdsafafdasfaf'
// ));