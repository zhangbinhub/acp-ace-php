/** jquery dialog start */
(function ($) {

    /**
     * 对象是否在对话框中
     */
    $.fn.isInDialog = function () {
        var top = window.top;
        var dlgid = _global_tools_obj.getUrlParam("_dialogid");
        var dialog = top.$("#" + dlgid);
        if (dialog.length > 0) {
            return true;
        } else {
            dialog = $(this).closest(".ui-acp-dialog");
            if (dialog.length > 0) {
                return true;
            }
        }
        return false;
    };

    var dialogobj = {};

    dialogobj.$overflow = '';

    /**
     * 提示对话框
     *
     * @param message
     *            提示信息
     * @param callBackFun
     *            对话框关闭时的回调函数 function()
     * @param type
     *            对话框类型（默认普通）：0-普通信息，1-成功信息，2-警告信息，3-错误信息
     * @param noeffect
     *            不使用动画效果
     */
    dialogobj.alert = function (message, callBackFun, type, noeffect) {
        var type_s = type || 0;
        var title = "";
        var contentclass = "";
        switch (type_s) {
            case 0:
                title = "<h4 class='smaller'><i class='ace-icon fa fa-info'></i> 消息</h4>";
                contentclass = "alert-info";
                break;
            case 1:
                title = "<h4 class='smaller text-success'><i class='ace-icon fa fa-check'></i> 成功</h4>";
                contentclass = "alert-success";
                break;
            case 2:
                title = "<h4 class='smaller text-warning'><i class='ace-icon fa fa-exclamation-triangle bigger-110'></i> 警告</h4>";
                contentclass = "alert-warning";
                break;
            case 3:
                title = "<h4 class='smaller text-danger'><i class='ace-icon fa fa-times-circle'></i> 错误</h4>";
                contentclass = "alert-danger";
                break;
            default:
                title = "<h4 class='smaller'><i class='ace-icon fa fa-info'></i> 消息</h4>";
                contentclass = "alert-info";
                break;
        }
        if (_global_tools_obj.isOldIE()) {
            noeffect = true;
        }
        var top = window.top;
        var dialog = top.$('<div class="hide"><div class="alert no-margin-bottom div-wrap ' + contentclass + '">' + message + '</div></div>');
        var obj = top.document.getElementsByTagName("body");
        top.$(obj).append(dialog);
        dialog.removeClass('hide').dialog({
            closeText: "关闭",
            resizable: false,
            width: '320',
            modal: true,
            title: "<div class='widget-header widget-header-small'>" + title + "</div>",
            title_html: true,
            show: (!noeffect || noeffect == undefined) ? {
                    effect: 'drop',
                    direction: 'up'
                } : undefined,
            hide: (!noeffect || noeffect == undefined) ? {
                    effect: 'drop',
                    direction: 'down'
                } : undefined,
            buttons: [{
                text: "确定",
                "class": "btn btn-primary btn-minier",
                click: function () {
                    top.$(this).dialog("close");
                }
            }],
            close: function () {
                var thisdiag = top.$(this);
                setTimeout(function () {
                    thisdiag.remove();
                }, 1);
                if (typeof (callBackFun) == "function") {
                    callBackFun();
                }
            }
        });
    };

    /**
     * 询问对话框
     *
     * @param message
     *            提示信息
     * @param callBackFun
     *            对话框关闭时的回调函数 function(true|false)
     * @param noeffect
     *            是否禁用动画
     */
    dialogobj.confirm = function (message, callBackFun, noeffect) {
        if (_global_tools_obj.isOldIE()) {
            noeffect = true;
        }
        var top = window.top;
        var dialog = top.$('<div class="hide"><div class="alert alert-warning no-margin-bottom div-wrap">' + message + '</div></div>');
        var obj = top.document.getElementsByTagName("body");
        top.$(obj).append(dialog);
        dialog.removeClass('hide').dialog({
            closeText: "关闭",
            resizable: false,
            width: '320',
            modal: true,
            show: (!noeffect || noeffect == undefined) ? {
                    effect: 'drop',
                    direction: 'up'
                } : undefined,
            hide: (!noeffect || noeffect == undefined) ? {
                    effect: 'drop',
                    direction: 'down'
                } : undefined,
            title: "<div class='widget-header'><h4 class='smaller text-warning'><i class='ace-icon fa fa-question'></i> 询问</h4></div>",
            title_html: true,
            buttons: [{
                html: "<i class='ace-icon fa fa-check bigger-110'></i>&nbsp; 确定",
                "class": "btn btn-primary btn-minier",
                click: function () {
                    top.$(this).dialog("close");
                    if (typeof (callBackFun) == "function") {
                        callBackFun(true);
                    }
                }
            }, {
                html: "<i class='ace-icon fa fa-times bigger-110'></i>&nbsp; 取消",
                "class": "btn btn-minier",
                click: function () {
                    top.$(this).dialog("close");
                    if (typeof (callBackFun) == "function") {
                        callBackFun(false);
                    }
                }
            }],
            close: function () {
                var thisdiag = top.$(this);
                setTimeout(function () {
                    thisdiag.remove();
                }, 1);
            }
        });
    };

    /**
     * 对话框中显示页面
     *
     * @param width
     *            对话框宽度
     * @param height
     *            对话框高度
     * @param title
     *            对话框标题
     * @param param
     *            显示的页面地址 | 参数对象
     * @param buttons
     *            按钮：json数组对象
     * @param callBackFun
     *            对话框关闭时的回调函数 function()
     */
    dialogobj.inDialog = function (width, height, title, param, buttons, callBackFun) {
        var top = window.top;
        var $overflow;
        if (typeof (param) == "string") {
            param = {
                iframeHtml: true,
                url: param
            };
        }
        param = $.extend({
            iframeHtml: false,// 是否是内嵌iframe模式
            url: undefined,// iframe地址
            innerUrl: undefined,// 内部地址
            innerHtml: undefined,// 内部html字符串
            params: undefined,// 传参对象
            resizable: false,// 是否可变尺寸
            closeOnEscape: true,// 是否支持esc关闭对话框
            modal: true, // 是否模式对话框
            fullscreen: false, // 是否全屏显示
            dialogClass: ""
        }, param);
        if (param.fullscreen) {
            width = height = "max";
            param.draggable = false;
            if (param.dialogClass == "") {
                param.dialogClass = "fullscreen";
            } else {
                param.dialogClass += " fullscreen";
            }
            if (param.iframeHtml) {
                $overflow = top.document.body.style.overflow;
                window.top.document.body.style.overflow = 'hidden';
            } else {
                $overflow = document.body.style.overflow;
                document.body.style.overflow = 'hidden';
            }
        }
        var maxWidth = width;
        var maxHeight = height;
        var size = _global_tools_obj.getBrowserSize();
        if (width == "auto") {
            maxWidth = size.width;
        } else if (width == "max") {
            maxWidth = width = size.width;
        } else if (typeof(width) == "number") {
            if (size.width < width) {
                width = size.width;
            }
        }
        if (height == "auto") {
            maxHeight = size.height;
        } else if (height == "max") {
            maxHeight = height = size.height;
        } else if (typeof(height) == "number") {
            if (size.height < height) {
                height = size.height;
            }
        }
        var dialog = null;
        if (param.iframeHtml) {
            dialog = top.$('<div class="ui-acp-dialog hide" style="padding:0px;overflow:hidden"></div>');
        } else {
            dialog = top.$('<div class="ui-acp-dialog hide"></div>');
        }
        var obj = top.document.getElementsByTagName("body");
        top.$(obj).append(dialog);
        dialog.removeClass('hide').dialog({
            closeText: "关闭",
            closeOnEscape: param.closeOnEscape,
            resizable: param.resizable,
            width: width,
            height: height,
            maxWidth: maxWidth,
            maxHeight: maxHeight,
            zIndex: param.zIndex || 1000,
            modal: param.modal,
            draggable: param.draggable != undefined ? param.draggable : true,
            dialogClass: param.dialogClass || "",
            title: "<div class='widget-header'><h4 class='smaller'> " + title + "</h4></div>",
            title_html: true,
            buttons: buttons,
            resize: function () {
                var width = $(this).parent().width();
                var height = $(this).parent().height();
                if (param.iframeHtml) {
                    $(this).width(width);
                    $(this).height(height - 41);
                    $(this).find("iframe.page_content_iframe").width(width);
                } else {
                    $(this).width(width - 24);
                    $(this).height(height - 51);
                }
            },
            resizeStart: function () {
                var width = $(this).parent().width();
                var height = $(this).parent().height();
                $(this).width(width);
                if (param.iframeHtml) {
                    $(this).height(height - 41);
                } else {
                    $(this).height(height - 51);
                }
            },
            resizeStop: function () {
                var width = $(this).parent().width();
                var height = $(this).parent().height();
                if (param.iframeHtml) {
                    $(this).width(width);
                    $(this).height(height - 41);
                } else {
                    $(this).width(width - 24);
                    $(this).height(height - 51);
                }
            },
            close: function () {
                var thisdiag = top.$(this);
                if (!param.resizable) {
                    top.$(top).unbind("resize", thisdiag.data("resizefun"));
                }
                var rtn = thisdiag.data("returninfo");
                if (param.iframeHtml) {
                    if (param.fullscreen) {
                        window.top.document.body.style.overflow = thisdiag.data("overflow");
                    }
                    setTimeout(function () {
                        thisdiag.find("iframe").attr("src", "");
                        thisdiag.remove();
                    }, 0);
                } else {
                    if (param.fullscreen) {
                        document.body.style.overflow = thisdiag.data("overflow");
                    }
                    setTimeout(function () {
                        thisdiag.remove();
                    }, 0);
                }
                if (typeof (callBackFun) == "function") {
                    callBackFun(rtn);
                }
            }
        });
        dialog.data("params", param.params);
        dialog.data("overflow", $overflow);
        var dlgid = dialog.attr("id");
        if (param.iframeHtml || param.innerUrl) {
            var url = "";
            if (param.iframeHtml) {
                if (param.url.indexOf("?") > -1) {
                    url = param.url + "&_dialogid=" + dlgid + "&_opentype=3";
                } else {
                    url = param.url + "?_dialogid=" + dlgid + "&_opentype=3";
                }
            } else if (param.innerUrl) {
                if (param.innerUrl.indexOf("?") > -1) {
                    url = param.innerUrl + "&_dialogid=" + dlgid + "&_opentype=2";
                } else {
                    url = param.innerUrl + "?_dialogid=" + dlgid + "&_opentype=2";
                }
            }
            dialog.loadPage(url);
        } else if (param.innerHtml) {
            dialog.html(param.innerHtml);
        }
        if (!param.resizable) {
            var resizefun = function () {
                var width = dialog.dialog("option", "width");
                var height = dialog.dialog("option", "height");
                var maxWidth = dialog.dialog("option", "maxWidth");
                var maxHeight = dialog.dialog("option", "maxHeight");
                var size = _global_tools_obj.getBrowserSize();
                if (width > size.width || maxWidth > size.width) {
                    dialog.dialog("option", "width", size.width);
                } else if (maxWidth <= size.width) {
                    dialog.dialog("option", "width", maxWidth);
                }
                if (height > size.height || maxHeight > size.height) {
                    dialog.dialog("option", "height", size.height);
                } else if (maxHeight <= size.height) {
                    dialog.dialog("option", "height", maxHeight);
                }
            };
            dialog.data("resizefun", resizefun);
            top.$(top).bind("resize", resizefun);
        }
    };

    /**
     * 获取传递参数
     * @returns {*}
     */
    dialogobj.getParamsIF = function () {
        var top = window.top;
        var dlgid = _global_tools_obj.getUrlParam("_dialogid");
        var dialog = top.$("#" + dlgid);
        return dialog.data("params");
    };

    /**
     * 设置当前对话框返回值，iframe模式有效
     *
     * @param rtn
     */
    dialogobj.setDialogReturnIF = function (rtn) {
        if (rtn != undefined) {
            var top = window.top;
            var dlgid = _global_tools_obj.getUrlParam("_dialogid");
            var dialog = top.$("#" + dlgid);
            if (dialog.length > 0) {
                dialog.data("returninfo", rtn);
            }
        }
    };

    /**
     * 关闭当前对话框，iframe模式有效
     *
     * @param rtn
     */
    dialogobj.closeDialogIF = function (rtn) {
        dialogobj.setDialogReturnIF(rtn);
        var top = window.top;
        var dlgid = _global_tools_obj.getUrlParam("_dialogid");
        var dialog = top.$("#" + dlgid);
        if (dialog.length > 0) {
            dialog.dialog("close");
        }
    };

    /**
     * 获取传递参数
     * @param obj
     *            必选 当前对话框页面中的任意组件对象
     * @returns {*}
     */
    dialogobj.getParams = function (obj) {
        var dialog = $(obj).closest(".ui-acp-dialog");
        return dialog.data("params");
    };

    /**
     * 关闭当前对话框，非iframe模式有效
     *
     * @param obj
     *            必选 当前对话框页面中的任意组件对象
     * @param returninfo
     *            可选 需要传入回调函数的返回值
     */
    dialogobj.setDialogReturn = function (obj, returninfo) {
        var dialog = $(obj).closest(".ui-acp-dialog");
        dialog.data("returninfo", returninfo);
    };

    /**
     * 关闭当前对话框，非iframe模式有效
     *
     * @param obj
     *            必选 当前对话框页面中的任意组件对象
     * @param returninfo
     *            可选 需要传入回调函数的返回值
     */
    dialogobj.closeDialog = function (obj, returninfo) {
        dialogobj.setDialogReturn(obj, returninfo);
        var dialog = $(obj).closest(".ui-acp-dialog");
        dialog.dialog("close");
    };

    AUI.dialog = dialogobj;
})($);
/** jquery dialog end */
