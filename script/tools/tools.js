(function ($) {

    $.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
        _title: function (title) {
            var $title = this.options.title || '&nbsp;';
            if (("title_html" in this.options)
                && this.options.title_html == true)
                title.html($title);
            else
                title.text($title);
        }
    }));
    $.fn.editable.defaults.mode = 'inline';
    $.fn.editableform.loading = "<div class='editableform-loading'><i class='ace-icon fa fa-spinner fa-spin fa-2x light-blue'></i></div>";
    $.fn.editableform.buttons = '<button type="submit" class="btn btn-info editable-submit"><i class="ace-icon fa fa-check"></i></button>'
        + '<button type="button" class="btn editable-cancel"><i class="ace-icon fa fa-times"></i></button>';

    $.ajax({
        async: true
    });

    /**
     * 装载页面
     */
    $.fn.loadPage = function (url) {
        var realurl = url;
        var opentype = 0;
        var bin = url.indexOf("?");
        if (bin > -1) {
            var opin = url.indexOf("_opentype");
            if (opin > -1 && opin > bin) {
                var params = url.split("?")[1].split("&");
                for (var param in params) {
                    if (params[param].indexOf("_opentype") > -1) {
                        var value = params[param].split("=")[1];
                        opentype = parseInt(value);
                        if (opentype != 0 && opentype != 1 && opentype != 2
                            && opentype != 3) {
                            AUI.dialog.alert("load page faild \""
                                + params[param] + "\" :" + url, null, 3);
                            opentype = -1;
                            break;
                        }
                    }
                }
            } else {
                url += "&_opentype=0";
            }
        } else {
            url += "?_opentype=0";
        }
        $(this).data("page_url", realurl);
        if (opentype == 0 || opentype == 2) {
            $(this).load(url);
        } else if (opentype == 1 || opentype == 3) {
            $(this).addClass("overflow-hidden");
            $(this)
                .html(
                    '<iframe class="page_content_iframe" frameborder="0" border="0"></iframe>');
            $(this).find("iframe").attr("src", url);
            if (opentype == 1) {
                var size = _global_tools_obj.getBrowserSize();
                $(this).find("iframe").height(size.height);
            }
        }
    };

    $.fn.reloadThisPage = function () {
        if (window != window.top) {
            window.location.reload();
        } else {
            var url = $(this).data("page_url");
            $(this).loadPage(url);
        }
    };

    /**
     * 当前元素移动至可视范围内
     */
    $.fn.focusInView = function (offset) {
        var obj = window.top;
        var offset = $.extend({
            top: 0,
            left: 0
        }, offset);
        var y = $(this).offset().top + offset.top;
        var x = $(this).offset().left + offset.left;
        var size = _global_tools_obj.getBrowserSize();
        if (y > size.height) {
            obj.$("html,body").animate({
                scrollTop: y
            }, 500);
        }
        if (x > size.width) {
            obj.$("html,body").animate({
                scrollLeft: x
            }, 500);
        }
    };

    /**
     * 多次用到字符串连接，+效率比较低，构建StringBuffer字符串连接方法
     *
     * @returns {StringBuffer}
     */
    StringBuffer = function () {
        this._strings_ = new Array;
        this.append = function (str) {
            this._strings_.push(str);
        };
        this.toString = function (mode) {
            if (mode == null || mode == undefined)
                mode = "";
            return this._strings_.join(mode);
        };
    };

    _global_tools_obj = function () {
    };

    /**
     * 获取浏览器版本号
     *
     * @returns {String}
     */
    _global_tools_obj.getBrowser = function () {
        var agent = navigator.userAgent.toLowerCase();
        if (agent.indexOf("msie") > -1 || agent.indexOf("rv:11.0") > -1) {
            if (agent.indexOf("msie 10.0") > -1) {
                return "ie10";
            } else if (agent.indexOf("msie 9.0") > -1) {
                return "ie9";
            } else if (agent.indexOf("msie 8.0") > -1) {
                return "ie8";
            } else if (agent.indexOf("msie 7.0") > -1) {
                return "ie7";
            } else if (agent.indexOf("msie 6.0") > -1) {
                return "ie6";
            } else {
                return "ie11";
            }
        } else if (agent.indexOf("firefox") > -1) {
            return "firefox";
        } else if (agent.indexOf("chrome") > -1) {
            return "chrome";
        } else if (agent.indexOf("safari") > -1 && agent.indexOf("chrome") < 0) {
            return "safari";
        } else {
            return "unknown";
        }
    };

    /**
     * 判断浏览器是否是IE
     *
     * @returns {Boolean}
     */
    _global_tools_obj.isIE = function () {
        var browser = _global_tools_obj.getBrowser();
        return browser.indexOf("ie") > -1;
    };

    /**
     * 判断浏览器是否是老版本IE
     */
    _global_tools_obj.isOldIE = function () {
        var browser = _global_tools_obj.getBrowser();
        return !!(browser == "ie8" || browser == "ie7" || browser == "ie6");
    };

    /**
     * 获取当前窗口的有效可视宽度和高度
     */
    _global_tools_obj.getBrowserSize = function () {
        var obj = window.top;
        var winW = obj.$(obj).width();
        var winH = obj.$(obj).height();
        return {
            width: winW,
            height: winH
        };
    };

    /**
     * 获取URL参数
     *
     * @param name
     * @returns
     */
    _global_tools_obj.getUrlParam = function (name) {
        var result = "";
        var url = window.location.href;
        if (url.indexOf("?") > -1) {
            var params = url.split("?")[1];
            var subparam = params.split("&");
            for (var i = 0; i < subparam.length; i++) {
                var subp = subparam[i].split("=");
                if (subp.length == 2 && subp[0] == name) {
                    result = subp[1];
                    break;
                }
            }
            return decodeURI(result).replace(/#/ig, "");
        } else {
            return "";
        }
    };

    /**
     * 删除左边的空格
     *
     * @param str
     * @returns
     */
    _global_tools_obj.ltrim = function (str) {
        return str.replace(/(^\s*)/g, "");
    };

    /**
     * 删除右边的空格
     *
     * @param str
     * @returns
     */
    _global_tools_obj.rtrim = function (str) {
        return str.replace(/(\s*$)/g, "");
    };

    /**
     * 判断字符串是否在数组中
     *
     * @param str
     * @param array
     * @returns {Boolean}
     */
    _global_tools_obj.strInArray = function (str, array) {
        for (var i = 0; i < array.length; i++) {
            if (str == array[i]) {
                return true;
            }
        }
        return false;
    };

    /**
     * 对象深度克隆
     * @param obj
     * @returns {*}
     */
    _global_tools_obj.objClone = function (obj) {
        if (obj === null) return null;
        var o = Object.prototype.toString.apply(obj) === "[object Array]" ? [] : {};
        for (var i in obj) {
            o[i] = (obj[i] instanceof Date) ? new Date(obj[i].getTime()) : (typeof obj[i] === "object" ? _global_tools_obj.objClone(obj[i]) : obj[i]);
        }
        return o;
    };

    /**
     * 执行ajax调用
     *
     * @param url
     *            必选。调用地址
     * @param param
     *            必选。参数：json对象
     * @param succesFun
     *            可选。调用成功时执行的函数 function(result)
     * @param type
     *            可选。请求类型"GET"或"POST"，默认"POST"
     * @param isShowProcess
     *            可选。是否开启等待动画，默认true
     * @param dataType
     *            可选。服务器返回类型，默认"json"
     * @param async
     *            可选。是否进行异步调用，默认 true
     * @param errorFun
     *            可选。调用失败时执行的函数 function(obj, message, exception)
     * @param timeOut
     *            可选。超时时间，单位：毫秒，默认0（不超时）
     */
    _global_tools_obj.doAjax = function (url, param, succesFun, type, isShowProcess, dataType, async, errorFun, timeOut) {
        var isAsync = true;
        if (async != undefined) {
            isAsync = async;
        }
        var datatype = dataType || "json";
        var isshowProcess = true;
        if (isShowProcess != undefined) {
            isshowProcess = isShowProcess;
        }
        var requestType = type || "POST";
        var timeout = timeOut || 0;
        if (isAsync && isshowProcess) {
            AUI.showProcess();
        }
        $.ajax({
            type: requestType,
            async: isAsync,
            url: url,
            data: param,
            dataType: datatype,
            timeout: timeout,
            success: function (data) {
                if (typeof (succesFun) == "function") {
                    succesFun(data);
                }
                if (isAsync && isshowProcess) {
                    AUI.closeProcess();
                }
            },
            error: function (obj, message, exception) {
                if (typeof (errorFun) == "function") {
                    errorFun(obj, message, exception);
                } else {
                    AUI.dialog.alert(message, null, 3);
                }
                if (isAsync && isshowProcess) {
                    AUI.closeProcess();
                }
            }
        });
    };

    /**
     * 执行ajax POST 调用后台
     *
     * @param comurl
     *            必选。后台http请求转发页面
     * @param url
     *            必选。调用地址
     * @param charset
     *            字符集
     * @param param
     *            必选。参数：json对象
     * @param succesFun
     *            可选。调用成功时执行的函数 function(result)
     * @param timeOut
     *            可选。超时时间，单位：毫秒，默认0（不超时）
     * @param isShowProcess
     *            是否显示等待动画。默认true
     * @param dataType
     *            可选。服务器返回类型，默认"json"
     * @param errorFun
     *            可选。调用失败时执行的函数 function(obj, message, exception)
     * @param postType
     *            post数据类型namal|json|xml|byte，默认"nomal"键值对
     *
     */
    _global_tools_obj.doAjaxToServer = function (comurl, url, charset, param, succesFun, timeOut, isShowProcess, dataType, errorFun, postType) {
        var params = $.extend(param, {
            url: url,
            comType: 0,
            timeOut: timeOut,
            dataType: postType || "nomal",
            charset: charset || "utf-8"
        });
        _global_tools_obj.doAjax(comurl, params, succesFun, "POST", isShowProcess, dataType, true, errorFun, timeOut);
    };

    /**
     * 无刷新发送POST请求
     *
     * @param url
     * @param param
     */
    _global_tools_obj.doPost = function (url, param) {
        var timestamp = new Date().getTime();
        var body = $("body");
        body.append("<form class='hidden' id='form_" + timestamp
            + "' method='post' action='" + url + "'></form>");
        var form = $("#form_" + timestamp);
        for (var para in param) {
            form.append("<input type='text' id='" + para + "' name='" + para
                + "' class='hidden' value='" + param[para] + "'/>");
        }
        form.submit();
        form.remove();
    };

    /**
     * 生成随机数
     * @param min
     * @param max
     * @returns {Number}
     */
    _global_tools_obj.random = function (min, max) {
        var n1 = Math.random() * (max - min);
        return parseInt(min + n1);
    };

    /**
     * 金额转大写
     * @param n 金额（字符串，toFixed(2)）
     * @returns 大写
     */
    _global_tools_obj.moneyToCNMontrayUnit = function (n) {
        var fraction = ['角', '分'];
        var digit = [
            '零', '壹', '贰', '叁', '肆',
            '伍', '陆', '柒', '捌', '玖'
        ];
        var unit = [
            ['元', '万', '亿'],
            ['', '拾', '佰', '仟']
        ];
        var head = n < 0 ? '欠' : '';
        n = Math.abs(n);
        var s = '';
        for (var i = 0; i < fraction.length; i++) {
            s += (digit[Math.floor(n * 10 * Math.pow(10, i)) % 10] + fraction[i]).replace(/零./, '');
        }
        s = s || '整';
        n = Math.floor(n);
        for (var i = 0; i < unit[0].length && n > 0; i++) {
            var p = '';
            for (var j = 0; j < unit[1].length && n > 0; j++) {
                p = digit[n % 10] + unit[1][j] + p;
                n = Math.floor(n / 10);
            }
            s = p.replace(/(零.)*零$/, '').replace(/^$/, '零') + unit[0][i] + s;
        }
        return head + s.replace(/(零.)*零元/, '元')
                .replace(/(零.)+/g, '零')
                .replace(/^整$/, '零元整');
    };
})($);