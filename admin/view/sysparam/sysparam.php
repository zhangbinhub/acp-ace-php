<?php
require dirname(__FILE__) . '/../common/pageHead.php';
$user = admin\service\tools\ToolsClass::getUser();
$sysparamconfig = service\user\UserManagerClass::validatePermissions($GLOBALS['application']->getId(), $user->getId(), 'sysparamconfig');
if(!$sysparamconfig){
    header("HTTP/1.1 403 no permissions!");
    header("status: 403 no permissions!");
    echo ("<span style=\"color:red\">no permissions!</span>");
    exit();
}
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>系统参数配置</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php'; ?>
</head>
<body class="<?php echo $GLOBALS['body_class']; ?>">
<div class="page-content no-padding-top">
    <div id="sysparam_config_page">
        <div class="page-header">
            <h1>系统参数配置</h1>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <table id="sysparam-grid-table"></table>
                <div id="sysparam-grid-pager"></div>
            </div>
        </div>
    </div>
</div>
<?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/foot.php'; ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="<?php echo $_SERVER['REDIRECT_URL'] . '.js?v=1.0.0'; ?>"></script>
</body>
</html>