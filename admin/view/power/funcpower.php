<?php
require dirname(__FILE__) . '/../common/pageHead.php';
$user = admin\service\tools\ToolsClass::getUser();
$powerconfig = service\user\UserManagerClass::validatePermissions($GLOBALS['application']->getId(), $user->getId(), 'powerconfig');
if(!$powerconfig){
    header("HTTP/1.1 403 no permissions!");
    header("status: 403 no permissions!");
    echo ("<span style=\"color:red\">no permissions!</span>");
    exit();
}
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>权限配置</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php'; ?>
</head>
<body class="<?php echo $GLOBALS['body_class']; ?>">
<input class="hidden" id="powerconfig_funcconfig_funcpower_funcid"
       value="<?php if (isset($_REQUEST['funcid'])) echo $_REQUEST['funcid']; ?>"/>
<input class="hidden" id="powerconfig_funcconfig_funcpower_appid"
       value="<?php if (isset($_REQUEST['appid'])) echo $_REQUEST['appid']; ?>"/>
<div class="page-content no-padding-top" style="height: 500px">
    <!-- 内容 start -->
    <div class="row">
        <div class="col-xs-12 form-group">
            <select multiple="multiple" size="10"
                    name="powerconfig_funcconfig_funcpower_rolelist"
                    style="width: 100%" id="powerconfig_funcconfig_funcpower_rolelist">
            </select>
        </div>
        <div class="col-xs-12 form-group center">
            <button type="button"
                    class="btn btn-white btn-warning btn-bold btn-round"
                    id="powerconfig_funcconfig_funcpower_reset_btn">
                <i class="ace-icon fa fa-undo bigger-120 orange2"></i> 撤销
            </button>
            &nbsp; &nbsp;
            <button type="button"
                    class="btn btn-white btn-success btn-bold btn-round"
                    id="powerconfig_funcconfig_funcpower_saveFuncpower_btn">
                <i class="ace-icon fa fa-floppy-o bigger-120 green"></i> 保存
            </button>
            &nbsp; &nbsp;
            <button type="button"
                    class="btn btn-white btn-danger btn-bold btn-round"
                    id="powerconfig_funcconfig_funcpower_cancle_btn">
                <i class="ace-icon fa fa-times bigger-120 red2"></i> 取消
            </button>
        </div>
    </div>
</div>
<?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/foot.php'; ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="<?php echo $_SERVER['REDIRECT_URL'] . '.js?v=1.0.0'; ?>"></script>
</body>
</html>