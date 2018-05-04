/**
 * 组件页面对象
 */
var UploadFilePluginObj = function (options) {

    this.myDropzone = null;
    /**
     * 配置参数
     */
    var options = options;

    /**
     * 文件数组
     */
    var successFilePathes = [];

    var addCount = 0;

    var completeCount = 0;

    var processFlag = 0;

    /**
     * 查看上传进度
     */
    function checkProcess() {
        _global_tools_obj.doAjax("/service/plugins/uploadFile", {
            oper: "checkprocess",
            filename: "_uploadfile_plugin_oldfile",
            processFlag: processFlag
        }, function (data) {
            var process = parseInt(data);
            var prostr = (process / 10) + "%";
            setTimeout(function () {
                $("#_uploadfile_plugin_processid").attr("data-percent", prostr);
                $("#_uploadfile_plugin_processid").children().eq(0).width(prostr);
            }, 1);
            processFlag++;
            checkProcess();
        }, "POST", false, "text");
    }

    /**
     * 初始化上传组件
     */
    function initfileinput() {
        AUI.element.initFileInput("_uploadfile_plugin_oldfile", {
            no_file: '请选择文件',
            btn_choose: '选择',
            btn_change: "重新选择",
            no_icon: 'ace-icon fa fa-cloud-upload',
            droppable: false,
            thumbnail: false,
            preview_error: function (filename, error_code) {
                switch (error_code) {
                    case 1:
                        AUI.dialog.alert("文件加载失败", null, 3);
                        break;
                    case 2:
                        AUI.dialog.alert("图片加载失败", null, 3);
                        break;
                    case 3:
                        AUI.dialog.alert("临时文件生成失败", null, 3);
                        break;
                    default:
                        AUI.dialog.alert("未知错误", null, 3);
                }
            }
        });
    }

    /**
     * 显示老版本浏览器上传组件
     */
    function showOldUpload() {
        var size = options.maxFilesize;
        if (size / 1024 > 1) {
            size = (size / 1024) + " MB";
        } else {
            size = size + " KB";
        }
        $("#_uploadfile_plugin_custom_size").html("文件大小不得超过 <span class='red'>" + size + "</span> 否则将无法成功上传");
        var filetypetext = "";
        if (options.acceptedFiles != null) {
            var filetypes = options.acceptedFiles.split(",");
            var tmptypes = [];
            for (var i = 0; i < filetypes.length; i++) {
                if (filetypes[i].indexOf(".") > -1 && filetypes[i].indexOf(".") == 0) {
                    tmptypes.push(filetypes[i]);
                }
            }
            if (tmptypes.length == 0) {
                filetypetext = "无";
            } else {
                filetypetext = tmptypes.join(",");
            }
        } else {
            filetypetext = "全部";
        }
        $("#_uploadfile_plugin_custom_type").html("允许上传的文件类型：" + filetypetext);
        $("#_uploadfile_plugin_newBrower").addClass("hidden");
        $("#_uploadfile_plugin_oldBrower").removeClass("hidden");
        initfileinput();
        $("#_uploadfile_plugin_upload_btn").click(function () {
            if ($.trim($("#_uploadfile_plugin_oldfile").val()) == "") {
                AUI.dialog.alert("请选择需要上传的文件！", null, 2);
                return;
            }
            if (validateFileType()) {
                checkProcess();
                $(this).prop("disabled", true);
                $("#_uploadfile_plugin_processid").removeClass("hidden");
                _tools_file_obj.doUploadFileForAjax("_uploadfile_plugin_oldfile", "/service/plugins/uploadFile",
                    {
                        oper: "upload",
                        fileid: "_uploadfile_plugin_oldfile",
                        path: options.path,
                        maxFilesize: options.maxFilesize
                    },
                    function (url) {
                        setTimeout(function () {
                            AUI.dialog.closeDialog($("#_uploadfile_plugin_newBrower"), url);
                        }, 500);
                    },
                    function (message) {
                        $("#_uploadfile_plugin_processid").addClass("hidden");
                        $("#_uploadfile_plugin_upload_btn").prop("disabled", false);
                        AUI.dialog.alert(message, function () {
                            AUI.dialog.closeDialog($("#_uploadfile_plugin_newBrower"));
                        }, 3);
                    });
            }
        });
    }

    /**
     * 文件类型校验
     */
    function validateFileType() {
        if (options.acceptedFiles == null) {
            return true;
        } else {
            var acceptedFiles = options.acceptedFiles.split(",");
            for (var i = 0; i < acceptedFiles.length; i++) {
                if (acceptedFiles[i].indexOf(".") > -1 && acceptedFiles[i].indexOf(".") == 0) {
                    var filename = $("#_uploadfile_plugin_oldfile").val();
                    var ext = filename.substring(filename.lastIndexOf("."));
                    if (ext == acceptedFiles[i]) {
                        return true;
                    }
                }
            }
            AUI.dialog.alert("该文件类型不允许上传！", null, 3);
            return false;
        }
    }

    /**
     * 显示新版浏览器上传组件
     */
    this.showUpload = function () {
        try {
            Dropzone.autoDiscover = false;
            var dropzone_options = {
                url: "/service/plugins/uploadFile?path=" + options.path,
                maxFilesize: options.maxFilesize / 1024,
                filesizeBase: 1024,
                addRemoveLinks: false,
                parallelUploads: options.parallelUploads,
                maxFiles: options.maxFiles,
                acceptedFiles: options.acceptedFiles,
                dictDefaultMessage: "<span class=\"bigger-150 bolder\">"
                + "<i class=\"ace-icon fa fa-caret-right red\"></i> "
                + "拖拽文件至此</span> 进行上传 "
                + "<span class=\"smaller-80 grey\">(或点击选择)</span> <br /> "
                + "<i class=\"upload-icon ace-icon fa fa-cloud-upload blue fa-3x\"></i>",
                dictResponseError: "上传文件出错！",
                dictInvalidFileType: "该类型文件不允许上传",
                dictFileTooBig: "文件大小{{filesize}} MB，超过最大限制{{maxFilesize}} MB",
                dictCancelUpload: "取消上传",
                dictCancelUploadConfirmation: "确定取消上传该文件？",
                dictRemoveFile: "删除文件",
                dictMaxFilesExceeded: "最大允许上传 " + options.maxFiles + " 个文件",
                previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n  "
                + "<div class=\"dz-details\">\n    "
                + "<div class=\"dz-filename\"><span data-dz-name></span></div>\n    "
                + "<div class=\"dz-size\" data-dz-size></div>\n    "
                + "<img data-dz-thumbnail />\n  </div>\n  "
                + "<div class=\"progress progress-small progress-striped active\">"
                + "<div class=\"progress-bar progress-bar-success\" "
                + "data-dz-uploadprogress></div></div>\n  "
                + "<div class=\"dz-success-mark\"><span></span></div>\n  "
                + "<div class=\"dz-error-mark\"><span></span></div>\n  "
                + "<div class=\"dz-error-message\">"
                + "<span data-dz-errormessage></span></div>\n</div>",
                init: function () {
                    this.on("addedfile", function () {
                        addCount++;
                        $("#_uploadfile_plugin_contrl_upload").removeClass("btn-success").addClass("btn-danger").text("终止上传");
                    });
                    this.on("success", function (file, responseText) {
                        var filepath = $.parseJSON(responseText);
                        if (filepath.errmsg) {
                            this.emit("error", file, filepath.errmsg);
                        } else {
                            file.path = filepath.filePathName;
                            successFilePathes.push(filepath.filePathName);
                            AUI.dialog.setDialogReturn($("#_uploadfile_plugin_newBrower"), successFilePathes.join(";"));
                        }
                    });
                    this.on("complete", function (file) {
                        completeCount++;
                        if (completeCount != addCount) {
                            $("#_uploadfile_plugin_contrl_upload").removeClass("btn-success").addClass("btn-danger").text("终止上传");
                        } else {
                            $("#_uploadfile_plugin_contrl_upload").removeClass("btn-danger").addClass("btn-success").text("完成");
                            $("#_uploadfile_plugin_contrl_upload").prop("disabled", false);
                        }
                    });
                },
                fallback: function () {
                    showOldUpload();
                },
                canceled: function (file) {
                    return this.emit("error", file, "文件上传被取消");
                }
            };
            this.myDropzone = new Dropzone("#_uploadfile_plugin_dropzone", dropzone_options);
        } catch (e) {
            showOldUpload();
        }
    }
};

$(function () {
    var obj = new UploadFilePluginObj(AUI.dialog.getParams($("#_uploadfile_plugin_newBrower")));
    $("#_uploadfile_plugin_contrl_upload").click(function () {
        var classname = $(this).attr("class");
        if (classname.indexOf("btn-danger") > -1) {
            AUI.dialog.confirm("确定终止未完成的任务？", function (data) {
                if (data) {
                    var files = obj.myDropzone.getUploadingFiles();
                    if (files.length > 0) {
                        $("#_uploadfile_plugin_contrl_upload").prop("disabled", true);
                    }
                    for (var i = 0; i < files.length; i++) {
                        obj.myDropzone.cancelUpload(files[i]);
                    }
                }
            }, true);
        } else {
            AUI.dialog.closeDialog($("#_uploadfile_plugin_newBrower"));
        }
    });
    obj.showUpload();
});