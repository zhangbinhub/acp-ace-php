<?php
require $_SERVER['DOCUMENT_ROOT'] . '/view/common/foot.php';
if ($GLOBALS['alone_page']) {
    $charset = $GLOBALS['charset'];
    // 公共脚本
    echo '<script type="text/javascript" charset="' . $charset . '" src="' . $GLOBALS['webroot'] . '/script/tools.js?v=1.0.0"></script>';
}