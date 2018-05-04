<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/common/include.php';
session_start();
$systemConfig = \config\SystemConfig::getInstance();
$charset = $systemConfig['charset'];
$oscharset = $systemConfig['os_charset'];
$result = array();
$ds = DIRECTORY_SEPARATOR;
if (isset($_REQUEST['oper'])) {
    $oper = $_REQUEST['oper'];
    switch ($oper) {
        case "upload":
            $fileid = $_REQUEST['fileid'];
            $fileinfo = $_FILES[$fileid];
            $maxFilesize = floatval($_REQUEST['maxFilesize']) * 1024;
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
            } else
                if ($fileinfo['size'] > $maxFilesize) {
                    $meg = '';
                    if ($maxFilesize / 1024 / 1024 > 1) {
                        $meg = ($maxFilesize / 1024 / 1024) . ' MB';
                    } else {
                        $meg = $maxFilesize . ' KB';
                    }
                    $result = \service\tools\ToolsClass::buildJSONError("要上传的文件大小超出大小限制：" . $meg);
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
                    if (!is_dir($folder)) {
                        mkdir($folder, 0777, true);
                    }
                    $filepath = iconv($charset, $oscharset, $folder . $ds . $filename);
                    if (move_uploaded_file($fileinfo['tmp_name'], $filepath)) {
                        chmod($filepath, 0777);
                        $fileurl = str_replace($document_root, '', iconv($oscharset, $charset, $filepath));
                        $fileurl = str_replace($ds, "/", $fileurl);
                        $result['filePathName'] = $fileurl;
                    } else {
                        $result = \service\tools\ToolsClass::buildJSONError("上传失败！");
                    }
                }
            echo json_encode($result);
            break;
        case "delfile":
            $filepaths = $_REQUEST['path'];
            if ($filepaths != null && $filepaths != '') {
                $pathes = explode(';', $filepaths);
                foreach ($pathes as $path) {
                    $filepath = $_SERVER['DOCUMENT_ROOT'] . $path;
                    $filepath = str_replace("/", $ds, $filepath);
                    $filepath = str_replace("\\", $ds, $filepath);
                    $filename = iconv($charset, $oscharset, $filepath);
                    \service\tools\ToolsClass::deleteDirOrFile($filename);
                }
            }
            echo "true";
            break;
        case "checkprocess":
            $name = $_REQUEST['filename'];
            $processFlag = intval($_REQUEST['processFlag']);
            $key = ini_get('session.upload_progress.prefix') . $name;
            if (isset($_SESSION[$key])) {
                $content_length = $_SESSION[$key]['content_length'];
                $bytes_processed = $_SESSION[$key]['bytes_processed'];
                echo ($bytes_processed / $content_length) * 1000;
            } else {
                if ($processFlag > 0) {
                    echo 1000;
                } else {
                    echo 0;
                }
            }
            break;
    }
} else {
    $fileinfo = $_FILES["file"];
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
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        $filepath = iconv($charset, $oscharset, $folder . $ds . $filename);
        if (move_uploaded_file($fileinfo['tmp_name'], $filepath)) {
            chmod($filepath, 0777);
            $fileurl = str_replace($document_root, '', iconv($oscharset, $charset, $filepath));
            $fileurl = str_replace($ds, "/", $fileurl);
            $result['filePathName'] = $fileurl;
        } else {
            $result = \service\tools\ToolsClass::buildJSONError("上传失败！");
        }
    }
    echo json_encode($result);
}