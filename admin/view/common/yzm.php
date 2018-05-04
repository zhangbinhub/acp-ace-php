<?php
header('Content-Type: image/jpeg');
/* 参数 */
$image_width = 220; // 图片宽度
$image_height = 80; // 图片高度
$characters_on_image = 4; // 验证字符个数
$font = './font/ADOBEFANHEITISTD-BOLD.OTF'; // 验证字符字体
$possible_letters = '23456789abcdefghjkmnpqrstuvwxyz'; // 候选验证字符
$random_dots = 50; // 随机干扰点的个数
$random_lines = 20; // 随机干扰线的个数
$random_arcs = 20; // 随机干扰弧线的个数

$code = ''; // 生成的验证码
for ($i = 0; $i < $characters_on_image; $i ++) {
    $code .= substr($possible_letters, mt_rand(0, strlen($possible_letters) - 1), 1);
}
$font_size = $image_height * 0.75; // 字体大小
$image = imagecreate($image_width, $image_height); // 创建图片对象
imagecolorallocate($image, 255, 255, 255); // 设置背景颜色

/* 生成随机干扰点 */
for ($i = 0; $i < $random_dots; $i ++) {
    imagefilledellipse($image, mt_rand(0, $image_width), mt_rand(0, $image_height), 2, 3, imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)));
}

/* 生成随机干扰直线 */
for ($i = 0; $i < $random_lines; $i ++) {
    imageline($image, mt_rand(0, $image_width), mt_rand(0, $image_height), mt_rand(0, $image_width), mt_rand(0, $image_height), imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)));
}

/* 生成随机干扰弧线 */
for ($i = 0; $i < $random_arcs; $i ++) {
    imagearc($image, mt_rand(0, $image_width), mt_rand(0, $image_height), mt_rand(0, $image_width), mt_rand(0, $image_height), mt_rand(- 360, 360), mt_rand(- 360, 360), imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)));
}

/* 生成验证字符 */
$code_len = strlen($code);
$char_maxwidth = $image_width / ($code_len + 1); // 计算每个字符最大有多宽
for ($i = 0; $i < $code_len; $i ++) {
    $text = mt_rand(- 1, 1) > 0 ? strtoupper($code[$i]) : strtolower($code[$i]);
    $textbox = imagettfbbox($font_size, 0, $font, $text); // 0-左下角X，1-左下角Y，2-右下角X，3-右下角Y，4-右上角X，5-右上角Y，6-左上角X，7-左上角Y
    $x = $char_maxwidth * ($i + 0.5);
    $y = ($image_height - ($textbox[3] - $textbox[5])) / 2 - $textbox[5];
    imagettftext($image, $font_size, 0, $x, $y, imagecolorallocate($image, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150)), $font, $text);
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/common/include.php';
session_start();
$_SESSION[admin\service\tools\ToolsClass::$LOGIN_YZM_STR] = $code;
imagejpeg($image);
imagedestroy($image);
die();