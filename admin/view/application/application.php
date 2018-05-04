<?php
require dirname(__FILE__) . '/../common/pageHead.php';
$user = admin\service\tools\ToolsClass::getUser();
$appconfig = service\user\UserManagerClass::validatePermissions($GLOBALS['application']->getId(), $user->getId(), 'appconfig');
if (!$appconfig) {
    header("HTTP/1.1 403 no permissions!");
    header("status: 403 no permissions!");
    echo("<span style=\"color:red\">no permissions!</span>");
    exit();
}
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>应用配置</title>
    <?php
    require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php';
    $dbconfig = \config\DataBaseConfig::getInstance();
    $dbstr = '';
    foreach ($dbconfig as $key => $dbinfo) {
        if ($dbstr !== '') {
            $dbstr = $dbstr . ';';
        }
        $dbstr = $dbstr . $key . ':' . $dbinfo['name'];
    }
    ?>
    <script type="text/javascript">
        var dbresourceStr = '<?php echo $dbstr;?>';
    </script>
</head>
<body class="<?php echo $GLOBALS['body_class']; ?>">
<div class="page-content no-padding-top">
    <div id="application_appconfig_page">
        <div class="page-header">
            <h1>应用配置</h1>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <table id="application-grid-table"></table>
                <div id="application-grid-pager"></div>
            </div>
        </div>
    </div>
    <div id="application_infoconfig_page" class="hidden">
        <div class="page-header">
				<span class="label label-lg label-info arrowed-in-right arrowed"
                      style="float: left; margin-right: 16px; cursor: pointer;">返回</span>
            <h1>
                相关信息配置
                <small id="application_info_title"></small>
            </h1>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                <table id="application-info-grid-table"></table>
                <div id="application-info-grid-pager"></div>
            </div>
            <div class="col-xs-12 form-group">
                <table id="application-link-grid-table"></table>
                <div id="application-link-grid-pager"></div>
            </div>
        </div>
    </div>
    <div id="application_statistical_page" class="hidden">
        <div class="page-header">
				<span class="label label-lg label-info arrowed-in-right arrowed"
                      style="float: left; margin-right: 16px; cursor: pointer;">返回</span>
            <h1>
                统计信息
                <small id="application_charts_title"></small>
            </h1>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                <div class="col-xs-12">
                    <div class="widget-box">
                        <div class="widget-header widget-header-flat widget-header-small">
                            <h5 class="widget-title">
                                <i class="ace-icon fa fa-bar-chart-o"></i> 登录情况
                            </h5>
                            <div class="widget-toolbar">
                                <a href="#" data-action="fullscreen"
                                   id="application_charts_fullscreen" class="orange2"> <i
                                            class="ace-icon fa fa-expand"></i>
                                </a> <a href="javascript:void(0)" data-action
                                        id="application_charts_reload" style="color: #ACD392"> <i
                                            class="ace-icon fa fa-refresh"></i>
                                </a> <a href="javascript:void(0)" data-action="collapse"
                                        id="application_charts_collapse"> <i class="ace-icon fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main">
                                <div id="application_charts_info_logininfo"
                                     style="height: 400px"></div>
                            </div>
                        </div>
                    </div>
                </div>
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