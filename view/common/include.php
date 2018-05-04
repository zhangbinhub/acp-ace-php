<?php
require dirname(__FILE__) . '/config.php';

/**
 * 自动加载函数
 * @param $className
 */
function autoload($className)
{
    $className = str_replace('\\', '/', $className);
    $className = str_replace('_', '/', $className);
    $path = $_SERVER['DOCUMENT_ROOT'] . '/' . $className . '.php';
    if (file_exists($path)) {
        include_once($_SERVER['DOCUMENT_ROOT'] . '/' . $className . '.php');
    } else {
        exit($className . ' is no exist！');
    }
}

spl_autoload_register('autoload');

/**
 * 请求参数转码，防止脚本及SQL注入
 */
foreach ($_REQUEST as $key => $value) {
    if (is_string($value)) {
        if (is_null(json_decode($value))) {
            if (strpos($value, '/') !== 0 || strrchr($value, '/') !== '/') {
                $value_tmp = htmlspecialchars($value);
                $value_tmp = addslashes($value_tmp);
                $_REQUEST[$key] = $value_tmp;
            }
        }
    }
}

/**
 * GET转码，防止脚本及SQL注入
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    foreach ($_GET as $key => $value) {
        if (is_string($value)) {
            if (is_null(json_decode($value))) {
                if (strpos($value, '/') !== 0 || strrchr($value, '/') !== '/') {
                    $value_tmp = htmlspecialchars($value);
                    $value_tmp = addslashes($value_tmp);
                    $_GET[$key] = $value_tmp;
                }
            }
        }
    }
}

/**
 * POST转码，防止脚本及SQL注入
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $key => $value) {
        if (is_string($value)) {
            if (is_null(json_decode($value))) {
                if (strpos($value, '/') !== 0 || strrchr($value, '/') !== '/') {
                    $value_tmp = htmlspecialchars($value);
                    $value_tmp = addslashes($value_tmp);
                    $_POST[$key] = $value_tmp;
                }
            }
        }
    }
}