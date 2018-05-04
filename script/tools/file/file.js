(function ($) {

    var file = {};

    /**
     * 后台文件下载
     *
     * @param comurl
     *            必选。后台http请求转发页面
     * @param backhostIn
     *            后台主机地址（内部访问）
     * @param charset
     *            请求字符集
     * @param filename
     *            文件路径
     * @param isdelete
     *            是否在下载完成后删除文件
     * @param issec
     *            是否进行加密下载
     */
    file.doDownloadFromBack = function (comurl, backhostIn, charset, filename, isdelete, issec) {
        var tmpname = filename.split("/");
        var name = tmpname[tmpname.length - 1];
        if (issec) {
            _tools_security_obj.doEncryptToBack(comurl, filename, backhostIn, charset, null, function (result) {
                var fileencrypt = result;
                _global_tools_obj.doPost(comurl, {
                    url: backhostIn,
                    comType: 1,
                    timeOut: 0,
                    charset: charset || "utf-8",
                    filename: fileencrypt,
                    issec: true,
                    isdelete: isdelete == undefined ? false : isdelete,
                    name: name
                });
            });
        } else {
            _global_tools_obj.doPost(comurl, {
                url: backhostIn,
                comType: 1,
                timeOut: 0,
                charset: charset || "utf-8",
                filename: filename,
                issec: false,
                isdelete: isdelete == undefined ? false : isdelete,
                name: name
            });
        }
    };

    /**
     * ajax异步无刷新上传文件
     *
     * @param inputid
     * @param url
     * @param datas
     *            参数
     * @param succesFunc
     * @param errorFunc
     */
    file.doUploadFileForAjax = function (inputid, url, datas, succesFunc, errorFunc) {
        var param = $.extend({
            PHP_SESSION_UPLOAD_PROGRESS: "_uploadfile_plugin_oldfile"
        }, datas);
        $.ajaxFileUpload({
            url: url,
            secureuri: true,
            fileElementId: inputid,
            dataType: "json",
            data: param,
            success: function (data, status) {
                if (data.errmsg) {
                    if (typeof (errorFunc) == "function") {
                        errorFunc(data.errmsg);
                    } else {
                        AUI.dialog.alert(data.errmsg, null, 3);
                    }
                    return;
                }
                if (data.filePathName) {
                    if (typeof (succesFunc) == "function") {
                        succesFunc(data.filePathName);
                    }
                } else {
                    if (typeof (errorFunc) == "function") {
                        errorFunc("上传出错");
                    } else {
                        AUI.dialog.alert("上传出错", null, 3);
                    }
                }
            },
            error: function (data, status, e) {
                if (typeof (errorFunc) == "function") {
                    errorFunc("上传出错");
                } else {
                    AUI.dialog.alert("上传出错", null, 3);
                }
            }
        });
    };

    /** photo start */
    /**
     * 初始化图片上传组件
     *
     * @param imageId
     *            图片ID
     * @param maxSize
     *            单位：KB，默认 20MB
     * @param url
     *            上传服务地址
     * @param path
     *            上传路径
     * @path 路径
     * @param autoWidth
     */
    file.initUpLoadImage = function (imageId, maxSize, url, path, autoWidth) {
        try {
            var maxsize = 20480;// kb
            if (maxSize && !isNaN(Number(maxSize))) {
                maxsize = maxSize;
            }
            var url = url || "/service/plugins/uploadFile";
            var path = path || "/files/upload";
            var thumbnail = 'large';
            if (autoWidth) {
                thumbnail = 'fit';
            }
            try {
                document.createElement('img').appendChild(document.createElement('b'));
            } catch (e) {
                Image.prototype.appendChild = function (el) {
                }
            }
            var last_gritter;
            $('#' + imageId).editable({
                type: 'image',
                name: imageId,
                value: null,
                image: {
                    btn_choose: '上传图片',
                    droppable: true,
                    maxSize: (maxsize * 1024),
                    name: imageId,
                    thumbnail: thumbnail,
                    on_error: function (error_type) {
                        if (last_gritter)
                            $.gritter.remove(last_gritter);
                        if (error_type == 1) {// 图片格式不正确
                            last_gritter = $.gritter.add({
                                title: '不是图片文件！',
                                text: '请选择图片文件（jpg|gif|png）！',
                                class_name: 'gritter-error gritter-center'
                            });
                        } else if (error_type == 2) {// 图片大小超过限制
                            last_gritter = $.gritter.add({
                                title: '图片文件太大！',
                                text: '图片大小请不要超过 '
                                + maxsize
                                + 'kp!',
                                class_name: 'gritter-error gritter-center'
                            });
                        } else {// 其他错误
                            AUI.dialog.alert("错误代码：" + error_type, null, 3);
                        }
                    },
                    on_success: function () {
                        $.gritter.removeAll();
                    }
                },
                url: function (params) {
                    var deferred = new $.Deferred;
                    var value = $('#' + imageId).next().find('input[type=hidden]:eq(0)').val();
                    if (!value || value.length == 0) {
                        deferred.resolve();
                        return deferred.promise();
                    }
                    setTimeout(function () {
                            if ("FileReader" in window) {
                                var thumb = $('#' + imageId).next().find('img').data('thumb');
                                if (thumb)
                                    $('#' + imageId).get(0).src = thumb;
                                deferred.resolve({
                                    'status': 'OK'
                                });
                                if (last_gritter)
                                    $.gritter.remove(last_gritter);
                                last_gritter = $.gritter.add({
                                    title: '图片上传成功！',
                                    text: '',
                                    class_name: 'gritter-info gritter-center'
                                });
                            } else {
                                if ($("input[type='file'][name='" + imageId + "']").val() == "") {
                                    if (last_gritter)
                                        $.gritter.remove(last_gritter);
                                } else {
                                    $("input[type='file'][name='" + imageId + "']").attr("id", "input_" + imageId);
                                    file.doUploadFileForAjax("input_" + imageId, url,
                                        {
                                            oper: "upload",
                                            fileid: imageId,
                                            path: path,
                                            maxFilesize: maxsize
                                        }, function (filepath) {
                                            if (last_gritter)
                                                $.gritter.remove(last_gritter);
                                            $('#' + imageId).get(0).src = filepath;
                                            deferred.resolve({'status': 'OK'});
                                            last_gritter = $.gritter.add({
                                                title: '图片上传成功！',
                                                text: '',
                                                class_name: 'gritter-info gritter-center'
                                            });
                                        }, function (message) {
                                            deferred.resolve();
                                            $.gritter.removeAll();
                                            last_gritter = $.gritter.add({
                                                title: '图片上传失败！',
                                                text: message,
                                                class_name: 'gritter-error gritter-center'
                                            });
                                        });
                                }
                            }
                        },
                        parseInt(Math.random() * 800 + 800));
                    return deferred.promise();
                },
                success: function (response, newValue) {
                }
            })
        } catch (e) {
        }
    };
    /**
     * 初始化上传头像组件
     *
     * @param imageId
     *            图片ID
     */
    file.initUpLoadAvatar = function (imageId) {
        file.initUpLoadImage(imageId, 32, "/service/plugins/avatarUpload", "/files/tmp");
    };
    /** portrait photo end */

    /** upload start */
    /**
     * 上传组件
     *
     * @param areaid
     * @param pluginid
     * @param options
     */
    file.UpLoadPlugin = function (areaid, pluginid, options) {

        var areaid = areaid;

        var pluginid = pluginid;

        var pluginDivId = pluginid + "_uploaddiv_" + _global_tools_obj.random(1000, 9999);

        this.options = $.extend({
            title: "文件上传",
            readonly: false,
            path: "/files/upload",// 上传文件夹
            maxFilesize: 10240,// 单位KB
            parallelUploads: 10,// 最大同时上传文件数
            maxFiles: undefined,// 最大上传文件数，null表示不限制
            acceptedFiles: null,// exp: image/*,application/pdf,.psd
            onlyimg: false,
            imgwidth: null,
            imgheight: null,
            imgmaxwidth: null,
            imgmaxheight: null,
            files: "",
            afterCompleteFun: undefined,// 上传完成后执行的回调函数 function(filepathes)
            afterDeleteFun: undefined // 删除完成后执行的回调函数 function(result, filepath)，result="true"成功;else失败
        }, options);

        /**
         * 更新文件列表
         *
         * @param url
         */
        this.appendFile = function (url) {
            var obj = this;
            var innerHTML = new StringBuffer();
            var files = url.split(";");
            var filepathes = $("#" + pluginid).val();
            if (filepathes != "" && url != "") {
                filepathes += ";" + url;
            } else {
                filepathes = url;
            }
            $("#" + pluginid).val(filepathes);
            for (var i = 0; i < files.length; i++) {
                var filepath = files[i];
                var filename = filepath.substring(filepath.lastIndexOf("/") + 1);
                if (obj.options.onlyimg) {
                    innerHTML.append('<li><a href="' + filepath + '" data-rel="colorbox"><img ');
                    if (obj.options.imgwidth != null) {
                        innerHTML.append('width="' + obj.options.imgwidth + '" ');
                    }
                    if (obj.options.imgheight != null) {
                        innerHTML.append('height="' + obj.options.imgheight + '" ');
                    }
                    if (obj.options.imgmaxwidth != null && obj.options.imgmaxheight != null) {
                        innerHTML.append('style="max-width: ' + obj.options.imgmaxwidth + 'px;max-height: ' + obj.options.imgmaxheight + 'px"');
                    } else if (obj.options.imgmaxwidth != null) {
                        innerHTML.append('style="max-width: ' + obj.options.imgmaxwidth + 'px;"');
                    } else if (obj.options.imgmaxheight != null) {
                        innerHTML.append('style="max-height: ' + obj.options.imgmaxheight + 'px"');
                    }
                    innerHTML.append(' src="' + filepath + '" alt="' + filename + '"/><div class="text"><div class="inner">' + filename + '</div></div></a>');
                    if (!obj.options.readonly) {
                        innerHTML.append('<div class="tools tools-bottom"><a href="#" onclick="_tools_file_obj.UpLoadPlugin.deleteAttach(\'' + pluginid + '\',\'' + pluginDivId + '\',\'' + filepath + '\')">' +
                            '<i class="ace-icon fa fa-times red"></i></a></div>');
                    }
                    innerHTML.append('</li>');
                } else {
                    innerHTML.append("<div class='no-padding no-margin'><a href='" + filepath + "' target='_blank'><i class='ace-icon fa " + obj.getFileTypeIcon(filename) +
                        "' style='margin-right:2px;'></i>" + "<span class='attached-name'>" + filename + "</span></a>");
                    if (!obj.options.readonly) {
                        innerHTML.append("<span class='action-buttons'>" + "<a title='删除' href='javascript:void(0)' " +
                            "onclick=\"_tools_file_obj.UpLoadPlugin.deleteAttach('" + pluginid + "','" + pluginDivId + "','" + filepath + "')\" style=\"margin-left: 5px\">" +
                            "<i class='ace-icon fa fa-trash-o bigger-125 red'>" + "</i></a></span>");
                    }
                    innerHTML.append("</div>");
                }
            }
            if (obj.options.onlyimg) {
                var $ul = $("#" + pluginDivId).children().eq(0);
                $ul.append(innerHTML.toString());
                AUI.element.appendColorbox($ul.children(":last").find('[data-rel="colorbox"]'), {hideOverFlow: true});
            } else {
                $("#" + pluginDivId).append(innerHTML.toString());
            }
        };

        /**
         * 上传
         */
        this.uploadAttach = function () {
            var obj = this;
            if (obj.options.maxFiles != undefined
                && obj.options.maxFilesDEF != undefined) {
                var filepathes = $("#" + pluginid).val();
                if (filepathes != "") {
                    var filecounts = filepathes.split(";").length;
                    obj.options.maxFiles = obj.options.maxFilesDEF - filecounts;
                } else {
                    obj.options.maxFiles = obj.options.maxFilesDEF;
                }
            }
            if (obj.options.maxFiles == undefined || obj.options.maxFiles > 0) {
                AUI.dialog.inDialog(850, 462, "上传", {
                    innerUrl: "/view/plugins/upload/uploadFile",
                    params: obj.options
                }, null, function (filepath) {
                    if (filepath) {
                        obj.appendFile(filepath);
                    }
                    if (typeof (obj.options.afterCompleteFun) == "function") {
                        obj.options.afterCompleteFun(filepath);
                    }
                });
            } else {
                AUI.dialog.alert("文件已达最大上传数【" + obj.options.maxFilesDEF + "】，请先删除现有文件", null, 3);
            }
        };

        /**
         * 获取文件类型图标
         */
        this.getFileTypeIcon = function (fileName) {
            var fileTypeIcon = "fa-file-o";
            var ext = fileName.substring(fileName.lastIndexOf(".") + 1).toLowerCase();
            if (ext == "xls" || ext == "xlsx") {
                fileTypeIcon = "fa-file-excel-o";
            } else if (ext == "doc" || ext == "docx") {
                fileTypeIcon = "fa-file-word-o";
            } else if (ext == "pdf") {
                fileTypeIcon = "fa-file-pdf-o";
            } else if (ext == "jpg" || ext == "jpeg" || ext == "jpe"
                || ext == "bmp" || ext == "png" || ext == "gif") {
                fileTypeIcon = "fa-file-image-o";
            } else if (ext == "txt") {
                fileTypeIcon = "fa-file-text-o";
            } else if (ext == "zip" || ext == "rar") {
                fileTypeIcon = "fa-file-zip-o";
            }
            return fileTypeIcon;
        };

        /**
         * 创建文件上传组件
         */
        this.show = function () {
            var obj = this;
            obj.options.maxFilesDEF = obj.options.maxFiles;
            var area = $("#" + areaid);
            var innerHTML = new StringBuffer();
            innerHTML.append('<div class="widget-box"><div class="widget-header">');
            innerHTML.append('<h4 class="widget-title">' + obj.options.title + '</h4>');
            innerHTML.append('<div class="widget-toolbar"><a href="#" data-action="collapse"><i class="ace-icon fa fa-chevron-up"></i></a></div>');
            innerHTML.append('</div>');
            innerHTML.append('<div class="widget-body"><div class="widget-main">');
            innerHTML.append("<input class='hidden' id='" + pluginid + "' value='" + obj.options.files + "'/>");
            if (!obj.options.readonly) {
                innerHTML.append("<div class='action-buttons'><a title='全部删除' href='javascript:void(0)' "
                    + "onclick=\"_tools_file_obj.UpLoadPlugin.deleteAttach('" + pluginid + "','" + pluginDivId + "','all')\">"
                    + "<i class='ace-icon fa fa-trash-o bigger-125 red'></i></a>"
                    + "<a id='" + pluginDivId + "_upload_doUpLoadBtn' title='上传' href='javascript:void(0)'>"
                    + "<i class='ace-icon fa fa-upload bigger-125 blue'></i></a></div>");
                innerHTML.append("<hr class='hr-2'/>");
            }
            innerHTML.append("<div id='" + pluginDivId + "' class='no-padding no-margin'>");
            if (obj.options.onlyimg) {
                innerHTML.append('<ul class="ace-thumbnails clearfix">');
            }
            if (obj.options.files != "") {
                var files = obj.options.files.split(";");
                for (var i = 0; i < files.length; i++) {
                    var filepath = files[i];
                    var filename = filepath.substring(filepath.lastIndexOf("/") + 1);
                    if (obj.options.onlyimg) {
                        innerHTML.append('<li><a href="' + filepath + '" data-rel="colorbox"><img ');
                        if (obj.options.imgwidth != null) {
                            innerHTML.append('width="' + obj.options.imgwidth + '" ');
                        }
                        if (obj.options.imgheight != null) {
                            innerHTML.append('height="' + obj.options.imgheight + '" ');
                        }
                        if (obj.options.imgmaxwidth != null && obj.options.imgmaxheight != null) {
                            innerHTML.append('style="max-width: ' + obj.options.imgmaxwidth + 'px;max-height: ' + obj.options.imgmaxheight + 'px"');
                        } else if (obj.options.imgmaxwidth != null) {
                            innerHTML.append('style="max-width: ' + obj.options.imgmaxwidth + 'px;"');
                        } else if (obj.options.imgmaxheight != null) {
                            innerHTML.append('style="max-height: ' + obj.options.imgmaxheight + 'px"');
                        }
                        innerHTML.append(' src="' + filepath + '" alt="' + filename + '"/><div class="text"><div class="inner">' + filename + '</div></div></a>');
                        if (!obj.options.readonly) {
                            innerHTML.append('<div class="tools tools-bottom"><a href="#" onclick="_tools_file_obj.UpLoadPlugin.deleteAttach(\'' + pluginid + '\',\'' + pluginDivId + '\',\'' + filepath + '\')">' +
                                '<i class="ace-icon fa fa-times red"></i></a></div>');
                        }
                        innerHTML.append('</li>');
                    } else {
                        innerHTML.append("<div class='no-padding no-margin'><a href='" + filepath + "' target='_blank'><i class='ace-icon fa " + obj.getFileTypeIcon(filename) +
                            "' style='margin-right:2px;'></i>" + "<span class='attached-name'>" + filename + "</span></a>");
                        if (!obj.options.readonly) {
                            innerHTML.append("<span class='action-buttons'>" + "<a title='删除' href='javascript:void(0)'" +
                                "onclick=\"_tools_file_obj.UpLoadPlugin.deleteAttach('" + pluginid + "','" + pluginDivId + "','" + filepath + "')\" style=\"margin-left: 5px\">" +
                                "<i class='ace-icon fa fa-trash-o bigger-125 red'>" + "</i></a></span>");
                        }
                        innerHTML.append("</div>");
                    }
                }
            }
            if (obj.options.onlyimg) {
                innerHTML.append("</ul>");
            }
            innerHTML.append("</div>");
            innerHTML.append("</div></div>");
            area.append(innerHTML.toString());
            if (obj.options.onlyimg) {
                AUI.element.initColorbox($('#' + pluginDivId).find('.ace-thumbnails [data-rel="colorbox"]'), {hideOverFlow: true});
            }
            $("#" + pluginDivId).data("uploadObj", obj);
            $("#" + pluginDivId + "_upload_doUpLoadBtn").click(function () {
                obj.uploadAttach();
            });
        };

        /**
         * 销毁上传组件
         */
        this.destroy = function () {
            var area = $("#" + areaid);
            area.children().remove();
        };
    };

    /**
     * 删除
     *
     * @param pluginid
     * @param pluginDivId
     * @param filepath
     */
    file.UpLoadPlugin.deleteAttach = function (pluginid, pluginDivId, filepath) {
        if ($("#" + pluginid).val() != "") {
            var confirmStr = "";
            if (filepath == "all") {
                confirmStr = "确定删除所有文件？";
            } else {
                confirmStr = "确定删除文件【" + filepath.substring(filepath.lastIndexOf("/") + 1) + "】？";
            }
            AUI.dialog.confirm(confirmStr, function (data) {
                if (data) {
                    var path = filepath == "all" ? $("#" + pluginid).val() : filepath;
                    AUI.showProcess(null, $("#" + pluginid).parent().parent());
                    _global_tools_obj.doAjax("/service/plugins/uploadFile", {
                            oper: "delfile",
                            path: path
                        },
                        function (result) {
                            var uploadObj = $("#" + pluginDivId).data("uploadObj");
                            var plugin = $("#" + pluginid);
                            if (filepath == "all") {
                                plugin.val("");
                                if (uploadObj.options.onlyimg) {
                                    $("#" + pluginDivId).children(0).children().remove();
                                } else {
                                    $("#" + pluginDivId).children().remove();
                                }
                            } else {
                                var files = plugin.val().split(";");
                                for (var i = 0; i < files.length; i++) {
                                    if (files[i] == path) {
                                        files.splice(i, 1);
                                        i--;
                                    }
                                }
                                $("#" + pluginid).val(files.join(";"));
                                $("#" + pluginDivId).find("a[href='" + path + "']").parent().remove();
                            }
                            AUI.closeProcess($("#" + pluginid).parent().parent());
                            if (typeof (uploadObj.options.afterDeleteFun) == "function") {
                                uploadObj.options.afterDeleteFun(result, path);
                            }
                        }, 'POST', false, 'text', true, function (obj, message, exception) {
                            AUI.closeProcess($("#" + pluginid).parent().parent());
                            var afterDeleteFun = $("#" + pluginDivId).data("afterDeleteFun");
                            if (typeof (afterDeleteFun) == "function") {
                                afterDeleteFun(message, path);
                            }
                        });
                }
            });
        }
    };

    _tools_file_obj = file;
})($);
