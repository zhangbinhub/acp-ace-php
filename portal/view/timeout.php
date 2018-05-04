<?php
require dirname(__FILE__) . '/common/commonHead.php';
header('refresh: 5; url=' . $GLOBALS['loginpage_url']);
require dirname(__FILE__) . '/common/viewHead.php';
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>登录超时</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php'; ?>
</head>
<body class="page-body" style="background-color: #ffffff;">
<div class="page-content no-padding-top">
    <div class="row">
        <div class="col-xs-12">
            <div class="well" style="margin: 100px 400px 0px 400px;">
                <h1 class="grey lighter smaller">
						<span class="red bigger-125"> <i
                                class="ace-icon fa fa-times-circle"></i> 没有访问权限
						</span> 登录超时
                </h1>
                <hr>
                <h3 class="lighter smaller">
                    系统将在5秒后自动跳转至登录界面 <i class="ace-icon fa fa-sign-in bigger-125"></i>
                </h3>
                <div class="space"></div>
                <div>
                    <h4 class="lighter smaller">若系统没有自动跳转，进行下列操作可立即进行登录：</h4>
                    <ul class="list-unstyled spaced inline bigger-110 margin-15">
                        <li><i class="ace-icon fa fa-hand-o-right blue"></i> <a href="#"
                                                                                onclick="portal_tools_obj.doLogout()">点击此处链接</a>
                        </li>
                        <li><i class="ace-icon fa fa-hand-o-right blue"></i> 点击下方按钮</li>
                    </ul>
                </div>
                <hr>
                <div class="space"></div>
                <div class="center">
                    <a href="#" class="btn btn-primary"
                       onclick="portal_tools_obj.doLogout()"> <i
                            class="ace-icon fa fa-sign-in"></i> 登录
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/foot.php' ?>
</body>
</html>