<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/common/include.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_REQUEST['cmd'])) {
        $cmd = $_REQUEST['cmd'];
        switch ($cmd) {
            case 'getPk':
                $pkeydetail = service\tools\security\RSAUtilsClass::_getpublickeydetail();
                echo json_encode($pkeydetail);
                break;
        }
    }
}