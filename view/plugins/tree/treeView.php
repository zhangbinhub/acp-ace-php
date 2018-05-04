<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2016/8/11
 * Time: 12:23
 */
require $_SERVER['DOCUMENT_ROOT'] . '/view/common/pageHead.php';
?>
<html>
<head>
    <title>树形控件</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/view/common/head.php'; ?>
</head>
<body class="page-body content" style="min-height: 200px">
<div class="form-horizontal">
    <div class="widget-box transparent">
        <div id="_treeView_searchNodeArea" class="widget-header align-center no-padding-left" style="display: none">
            <div class="form-group no-margin-left no-margin-right no-margin-left">
                <input type='text' class="col-sm-8 input-sm" style="margin-top:5px"
                       onKeyDown='treeViewPluginObj.searchNodeByKey(this)'/>
                <div class="col-sm-4 no-padding-left no-padding-right" style="margin-top:5px">
                    <button type="button" class="btn btn-xs btn-primary"
                            onclick='treeViewPluginObj.searchNode(this)'>
                        <i class="ace-icon fa fa-search"></i> 搜索
                    </button>
                    <button type="button" class="btn btn-xs btn-primary"
                            onclick='treeViewPluginObj.resetTree(this)'>
                        <i class="ace-icon fa fa-search"></i> 重置
                    </button>
                </div>
            </div>
        </div>
        <div class="widget-body">
            <div class="widget-main">
                <div class="zTreeDemoBackground left" style="width:100%;text-align: center">
                    <ul id="_treeView_treeid" class="ztree" style="height: 295px;overflow: auto"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="_treeView_ztreeButtonArea" class="form-actions center no-margin">
    <button type="button" onclick="treeViewPluginObj.save(this)"
            class="btn btn-white btn-success btn-bold btn-round">
        <i class="ace-icon fa fa-floppy-o bigger-120 green"></i> 确定
    </button>
    &nbsp; &nbsp;
    <button type="button" onclick="treeViewPluginObj.cancle(this)"
            class="btn btn-white btn-danger btn-bold btn-round">
        <i class="ace-icon fa fa-times bigger-120 red2"></i> 取消
    </button>
    &nbsp; &nbsp;
    <button type="button" onclick="treeViewPluginObj.clearCheck()"
            class="btn btn-white btn-danger btn-bold btn-round">
        <i class="ace-icon fa fa-times bigger-120 red2"></i> 清除
    </button>
</div>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/view/common/foot.php'; ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="/script/plugins/tree/treeView.js?<?php echo '?v=1.0.0'; ?>"></script>
</body>
</html>