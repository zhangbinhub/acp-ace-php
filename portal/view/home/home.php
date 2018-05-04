<?php
require dirname(__FILE__) . '/../common/pageHead.php';
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>首页</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php'; ?>
</head>
<body class="<?php echo $GLOBALS['body_class']; ?>">
<div class="page-content no-padding-top">
    <!-- 内容 start -->
    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-block alert-success">
                <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                </button>
                <i class="ace-icon fa fa-check green"></i> 欢迎使用 <strong
                    class="green"> <?php if ($GLOBALS['application']->getAppname() != null) {
                        echo $GLOBALS['application']->getAppname();
                    }
                    if ($GLOBALS['application']->getVersion() != null) {
                        echo '<small>(v' . $GLOBALS['application']->getVersion() . ')</small>';
                    } ?>
                </strong>
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-12">
            <div class="widget-box col-xs-12">
                <div class="widget-header widget-header-flat widget-header-small">
                    <h5 class="widget-title">
                        <i class="ace-icon fa fa-bar-chart-o"></i> 在线用户统计
                    </h5>
                    <div class="widget-toolbar">
                        <a href="#" data-action="fullscreen"
                           id="homepage_charts_fullscreen" class="orange2"> <i class="ace-icon fa fa-expand"></i>
                        </a> <a href="javascript:void(0)" data-action
                                id="homepage_charts_reload" style="color: #ACD392"> <i
                                class="ace-icon fa fa-refresh"></i>
                        </a> <a href="javascript:void(0)" data-action="collapse"
                                id="homepage_charts_collapse"> <i class="ace-icon fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <div id="homepage_chart_onlineuser" style="height: 400px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 内容 end -->
</div>
<?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/foot.php'; ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="<?php echo $_SERVER['REDIRECT_URL'] . '.js?v=1.0.0'; ?>"></script>
</body>
</html>