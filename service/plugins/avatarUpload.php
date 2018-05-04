<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/common/include.php';
$result = array();
$systemConfig = \config\SystemConfig::getInstance();
$charset = $systemConfig['charset'];
$oscharset = $systemConfig['os_charset'];
$ds = DIRECTORY_SEPARATOR;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maxsize = floatval($_POST['maxFilesize']) * 1024;
    $fileid = $_POST['fileid'];
    $fileinfo = $_FILES[$fileid];
    if ($fileinfo["error"] > 0) {
        switch ($fileinfo["error"]) {
            case 1:
                $result = \service\tools\ToolsClass::buildJSONError("文件大小超出了服务器的空间大小！");
                break;
            case 2:
                $result = \service\tools\ToolsClass::buildJSONError("要上传的文件大小超出浏览器限制！");
                break;
            case 3:
                $result = \service\tools\ToolsClass::buildJSONError("文件仅部分被上传！");
                break;
            case 4:
                $result = \service\tools\ToolsClass::buildJSONError("没有找到要上传的文件！");
                break;
            case 5:
                $result = \service\tools\ToolsClass::buildJSONError("服务器临时文件夹丢失！");
                break;
            case 6:
                $result = \service\tools\ToolsClass::buildJSONError("文件写入到临时文件夹出错！");
                break;
        }
    } else {
        $filenames = explode('.', $fileinfo["name"]);
        if (count($filenames) > 2) {
            $tmp = '';
            for ($in = 0; $in < count($filenames) - 1; $in++) {
                if ($tmp !== '') {
                    $tmp = $tmp . '.';
                }
                $tmp = $tmp . $filenames[$in];
            }
            $filenames = array(
                $tmp,
                $filenames[count($filenames) - 1]
            );
        }
        $filename = $filenames[0] . time() . '.' . $filenames[1];
        $document_root = $_SERVER['DOCUMENT_ROOT'];
        $document_root = str_replace("/", $ds, $document_root);
        $document_root = str_replace("\\", $ds, $document_root);
        $folder = $document_root . $_REQUEST['path'];
        $folder = str_replace("/", $ds, $folder);
        $folder = str_replace("\\", $ds, $folder);
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $filepath = iconv($charset, $oscharset, $folder . $ds . $filename);
        if (move_uploaded_file($fileinfo['tmp_name'], $filepath)) {
            $type = getimagesize($filepath);
            $fp = fopen($filepath, "r");
            if ($fp !== false) {
                $filesize = filesize($filepath);
                if ($filesize <= $maxsize) {
                    $file_content = base64_encode(fread($fp, $filesize)); // base64编码
                    switch ($type[2]) {
                        case 1:
                            $img_type = "gif";
                            break;
                        case 2:
                            $img_type = "jpg";
                            break;
                        case 3:
                            $img_type = "png";
                            break;
                    }
                    $img = 'data:image/' . $img_type . ';base64,' . $file_content; // 合成图片的base64编码
                    $result['filePathName'] = $img;
                } else {
                    $result = \service\tools\ToolsClass::buildJSONError("图片大小请不要超过 " . ($maxsize / 1024) . "kp!");
                }
            } else {
                $result = \service\tools\ToolsClass::buildJSONError("上传失败！");
            }
            fclose($fp);
            \service\tools\ToolsClass::deleteDirOrFile($filepath);
        } else {
            $result = \service\tools\ToolsClass::buildJSONError("上传失败！");
        }
    }
    echo json_encode($result);
}
?>