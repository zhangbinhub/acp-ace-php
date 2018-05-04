<?php
require dirname(__FILE__) . '/../../common/pageHead.php';
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>首页</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php'; ?>
</head>
<body class="<?php echo $GLOBALS['body_class']; ?>">
<div class="page-content no-padding-top">
    <div class="row"><?php var_dump($_SERVER); ?></div>
    <div class="row"><?php echo '_type=' . $_REQUEST['_type']; ?></div>
    <!-- 内容 start -->
    <div class="row">
        <table class='table table-striped table-bordered table-hover'>
            <thead>
            <tr>
                <td style="width: 20%">名称</td>
                <td>内容
                    <button type="button" id="test_viewFileList">查看</button>
                </td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="align-right">上传：</td>
                <td class="align-left">
                    <div id="test_uploaddiv"></div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;"><img id="test_avatar"
                                                                 class="editable img-responsive editable-click editable-empty"
                                                                 alt="头像"/></td>
            </tr>
            </tbody>
        </table>
    </div>
    <!-- 内容 end -->
</div>
<?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/foot.php'; ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="<?php echo $_SERVER['REDIRECT_URL'] . '.js?v=1.0.0'; ?>"></script>
</body>
</html>