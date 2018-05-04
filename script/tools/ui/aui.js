(function ($) {

    var ui = {};

    /** jquery process start */
    /**
     * 打开遮蔽层动画
     *
     * @param str
     *            需要显示的文本内容
     * @param srcobj 页面中任意对象
     * @param isclarity 遮蔽层是否为透明
     */
    ui.showProcess = function (str, srcobj, isclarity) {
        setTimeout(function () {
            var message = "<h3 class=\"smaller lighter grey\" style=\"margin: 0px\"><i class=\"ace-icon fa fa-spinner fa-spin orange bigger-125\"></i>";
            if (str) {
                message += "<span>" + str + "</span>";
            }
            message += "</h3>";
            if (srcobj) {
                var obj = srcobj;
                if ($.isFunction($(obj).maskloading)) {
                    $(obj).maskloading(message);
                    if (isclarity) {
                        $(obj).find("div.loadmask").addClass("clarity");
                    }
                }
            } else {
                var tmp = window.top;
                var obj = tmp.document.getElementsByTagName("body");
                if (tmp.$.isFunction(tmp.$(obj).maskloading)) {
                    tmp.$(obj).maskloading(message);
                    if (isclarity) {
                        tmp.$(obj).find("div.loadmask").addClass("clarity");
                    }
                }
            }
        }, 0);
    };

    /**
     * 关闭遮蔽层动画
     */
    ui.closeProcess = function (srcobj) {
        setTimeout(function () {
            if (srcobj) {
                var obj = srcobj;
                if ($.isFunction($(obj).unmaskloading)) {
                    $(obj).unmaskloading();
                }
            } else {
                var tmp = window.top;
                var obj = tmp.document.getElementsByTagName("body");
                if (tmp.$.isFunction(tmp.$(obj).unmaskloading)) {
                    tmp.$(obj).unmaskloading();
                }
            }
        }, 0);
    };
    /** jquery process end */

    AUI = ui;
})($);
