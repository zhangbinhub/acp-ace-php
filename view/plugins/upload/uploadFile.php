<?php
require $_SERVER['DOCUMENT_ROOT'] . '/view/common/pageHead.php';
?>
<html>
<head>
    <title>上传</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/view/common/head.php'; ?>
</head>
<body class="page-body content" style="min-height: 200px">
<div class="page-body content">
    <div id="_uploadfile_plugin_newBrower">
        <div class="row no-margin">
            <button type="button" class="btn btn-success btn-block"
                    id="_uploadfile_plugin_contrl_upload">完成
            </button>
            <form action="/service/plugins/uploadFile"
                  id="_uploadfile_plugin_dropzone" class="dropzone"></form>
        </div>
    </div>
    <div id="_uploadfile_plugin_oldBrower" class="hidden">
        <div class="col-xs-12">
            <div class="widget-box transparent">
                <div class="widget-header">
                    <h3 class="widget-title" id="_uploadfile_plugin_custom_size"></h3>
                    <h5 class="smaller lighter green"
                        id="_uploadfile_plugin_custom_type"></h5>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <div class="form-group" style="margin-top: 15px">
                            <div class="col-xs-12">
                                <input type="file" id="_uploadfile_plugin_oldfile"
                                       name="_uploadfile_plugin_oldfile"/>
                            </div>
                        </div>
                        <div class="form-group align-center no-margin">
                            <div class="col-xs-12" style="height: 25px; margin-top: 5px">
                                <div id="_uploadfile_plugin_processid"
                                     class="progress progress-striped active hidden"
                                     data-percent="0%">
                                    <div class="progress-bar progress-bar-purple"
                                         style="width: 0%"></div>
                                </div>
                            </div>
                            <button class="btn btn-info" type="button"
                                    id="_uploadfile_plugin_upload_btn">
                                <i class="ace-icon fa fa-upload bigger-110"></i> 上传
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/view/common/foot.php'; ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="/script/plugins/upload/uploadFile.js?<?php echo '?v=1.0.0'; ?>"></script>
</body>
</html>