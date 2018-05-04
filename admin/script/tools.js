(function ($) {

    $(window).resize(function(){
        admin_tools_obj.updateTabContentTop();
    });

    $(document).on('settings.ace.jqGrid', function (ev, event_name, collapsed) {
        admin_tools_obj.updateTabContentTop();
    });

    admin_tools_obj = function () {

        admin_tools_obj.mainPageDefault = {
            path: G_homepage_url,
            container_id: "main_page_content_div",
            main_breadcrumb_id: "main_breadcrumb",
            main_nav_id: "main_nav_list",
            isAppend: false
        };

        /**
         * 更新导航 menuid 和 title 同时为空则表示清空和重置导航
         *
         * @param menuid
         * @param title
         * @param isAppend
         * @param breadcrumbId
         */
        this.updateBreadcrumb = function (menuid, title, isAppend, breadcrumbId) {
            var breadcrumbid = breadcrumbId || admin_tools_obj.mainPageDefault.main_breadcrumb_id;
            var $breadcrumb = window.top.$("#" + breadcrumbid);
            if ($breadcrumb.length > 0) {
                if (menuid && menuid != "") {
                    var id = "";
                    if (menuid.indexOf("bread_") > -1) {
                        id = menuid.split("_")[1];
                    } else {
                        id = menuid;
                    }
                    admin_tools_obj.doAjax(G_webrootPath + "/service/main/getBreadcrumb", {
                        id: id
                    }, function (data) {
                        $breadcrumb.children(".active").removeClass("active");
                        $breadcrumb.children("li:gt(0)").remove();
                        $breadcrumb.append(data);
                    }, "POST", false, "text");
                } else {
                    if (title && title != "") {
                        if (!isAppend) {
                            $breadcrumb.children(".active").removeClass("active");
                            $breadcrumb.children("li:gt(0)").remove();
                            $breadcrumb.children().eq(0).addClass("active");
                        }
                        admin_tools_obj.appendBreadcrumb(title, breadcrumbid);
                    } else {
                        $breadcrumb.children(".active").removeClass("active");
                        $breadcrumb.children("li:gt(0)").remove();
                        $breadcrumb.children().eq(0).addClass("active");
                    }
                }
            }
        };

        /**
         * 更新选项卡样式
         *
         * @param menuid
         * @param title
         *            选项卡标题，为空则表示重置选项卡
         * @param container_id
         *            选项卡页面div所在的容器id
         * @param breadcrumbId
         */
        this.updateTabsInfo = function (menuid, title, container_id, breadcrumbId) {
            /** 关闭选项卡下拉菜单 */
            closeOtherDropdownMenu();

            /** 生成选项卡页面div */
            var pageid = "";
            var containerid = container_id || admin_tools_obj.mainPageDefault.container_id;
            var container = window.top.$("#" + containerid);
            container.children(".active").removeClass("active");
            if (!title || title == "") {
                container.children("div").remove();
                pageid = containerid + "_content_mainpage";
            } else {
                pageid = containerid + "_content" + (container.children().length + 1);
                var i = 2;
                while (window.top.$("#" + pageid).length > 0) {
                    pageid = containerid + "_content" + (container.children().length + i);
                    i++;
                }
            }
            var div_str = '<div id="' + pageid + '"	class="main-page-container tab-pane active"></div>';
            container.append(div_str);

            /** 生成选项卡标签 */
            var breadcrumbid = breadcrumbId || admin_tools_obj.mainPageDefault.main_breadcrumb_id;
            var ul = window.top.$("#" + breadcrumbid);
            ul.children(".active").removeClass("active");
            var li_str = "";
            if (!title || title == "") {
                ul.children("li").remove();
                li_str = '<li class="active"><a data-toggle="tab" href="#'
                    + pageid
                    + '"><i class="ace-icon fa fa-home home-icon bigger-120"></i>&nbsp;首页&nbsp;&nbsp;'
                    + '<i class="ace-icon fa fa-caret-down bigger-110 width-auto main-tabbable-ul-li-dropdown"></i></a>'
                    + '<ul class="dropdown-menu dropdown-info">'
                    + '<li><a id="' + pageid
                    + '_reload">刷新</a></li><li><a id="' + pageid
                    + '_close">全部关闭</a></li></ul></li>';
            } else {
                var icon = '';
                if (menuid && menuid != "") {
                    var menu_i = window.top.$("#" + menuid).children(".menu-icon");
                    var color = menu_i.css("color");
                    var classname = menu_i.attr("class");
                    if (classname.indexOf("fa-caret-right") < 0) {
                        classname.replace("menu-icon", "ace-icon");
                        icon = '<i class="' + classname + ' bigger-120" style"color:' + color + '"></i>';
                    }
                }
                li_str = '<li class="active"><a data-toggle="tab" href="#'
                    + pageid
                    + '">'
                    + icon
                    + '&nbsp;'
                    + title
                    + '&nbsp;&nbsp;<i class="ace-icon fa fa-caret-down bigger-110 width-auto main-tabbable-ul-li-dropdown"></i></a>'
                    + '<ul class="dropdown-menu dropdown-info">'
                    + '<li><a id="' + pageid
                    + '_reload">刷新</a></li><li><a id="' + pageid
                    + '_close">关闭</a></li></ul></li>';
            }
            ul.append(li_str);
            ul.find("a[href='#" + pageid + "']").unbind("click").bind("click", function () {
                if (!ul.find("a[href='#" + pageid + "']").parent().hasClass("active")) {
                    closeOtherDropdownMenu(this);
                    admin_tools_obj.setMenuActive(menuid);
                    setTimeout(function () {
                        window.top.$(window).trigger('resize.jqGrid');
                        window.top.$(window).trigger('resize.chosen');
                        if (pageid == containerid + "_content_mainpage") {
                            ul.find("a[href='#" + pageid + "']").next().find("a#" + pageid + "_reload").click();
                        } else {
                            window.top.$(window).trigger('resize');
                        }
                    }, 0);
                }
            });
            ul.find("a[href='#" + pageid + "']").children("i.main-tabbable-ul-li-dropdown:last").unbind("click").bind("click", function () {
                if (ul.find("a[href='#" + pageid + "']").parent().hasClass("active")) {
                    showDropdownMenu(this);
                }
            });
            ul.find("a[href='#" + pageid + "']").next().find("a#" + pageid + "_reload").unbind("click").bind("click", function () {
                refreshThisTab(this);
            });
            ul.find("a[href='#" + pageid + "']").next().find("a#" + pageid + "_close").unbind("click").bind("click", function () {
                closeThisTab(this);
            });
            admin_tools_obj.updateTabContentTop(container_id, breadcrumbId);
            return pageid;
        };

        /**
         * 焦点移至指定菜单
         *
         * @param menuid
         * @param main_nav_id 菜单区域id
         */
        admin_tools_obj.setMenuActive = function (menuid, main_nav_id) {
            var main_nav_id = main_nav_id || admin_tools_obj.mainPageDefault.main_nav_id;
            var $nav_list = window.top.$("#" + main_nav_id);
            $nav_list.find("li.active").removeClass("active");
            $nav_list.find("li.open").removeClass("open");
            $nav_list.find("ul.nav-show").removeClass("nav-show").addClass("nav-hide").hide();
            if (menuid && menuid != "") {
                var $menu_a = window.top.$("#" + menuid);
                if ($menu_a.length > 0) {
                    var $menu_li = $menu_a.parent();
                    $menu_li.addClass("active");
                    $menu_li.parentsUntil("#main_nav_list", "li").addClass("open").addClass("active");
                    $menu_li.parentsUntil("#main_nav_list", "ul").removeClass("nav-hide").addClass("nav-show").show();
                }
            }
        };
    };

    /**
     * 退出登录
     */
    admin_tools_obj.doLogout = function () {
        window.top.location.href = G_logoutpage_url;
    };

    /**
     * 登录超时调用页面
     */
    admin_tools_obj.loginTimeout = function () {
        window.top.location.href = G_timeoutpage_url;
    };

    $(document).ajaxError(function (e, xhr, opt) {
        var code = xhr.status;
        if (code == 403) {
            admin_tools_obj.loginTimeout();
        }
    });

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
    admin_tools_obj.doAjax = function (url, param, succesFun, type, isShowProcess, dataType, async, errorFun, timeOut) {
        _global_tools_obj.doAjax(url, param, succesFun, type, isShowProcess, dataType, async, errorFun, timeOut);
    };

    /**
     * 执行ajax POST 调用后台
     *
     * @param urlname
     *            必选。调用地址名
     * @param param
     *            必选。参数：json对象
     * @param succesFun
     *            可选。调用成功时执行的函数 function(result)
     * @param isShowProcess
     *            是否显示等待动画。默认true
     * @param dataType
     *            可选。服务器返回类型，默认"json"
     * @param errorFun
     *            可选。调用失败时执行的函数 function(obj, message, exception)
     *
     */
    admin_tools_obj.doAjaxToServer = function (urlname, param, succesFun, isShowProcess, dataType, errorFun) {
        _global_tools_obj.doAjaxToServer(G_webrootPath + "/view/common/communicationToBack", G_backService.httphostIn + urlname, G_backService.charset, param, succesFun, G_backService.httptimeout, isShowProcess, dataType, errorFun);
    };

    /**
     * 打开菜单指定页面
     *
     * @param menuePath
     *            菜单全路径定位，“/”分隔
     */
    admin_tools_obj.gotoMenuPage = function (menuePath) {
        var top = window.top;
        if (!menuePath || menuePath == "") {
            AUI.dialog.alert("找不到页面或没有权限访问！【" + menuePath + "】", null, 3);
        } else {
            admin_tools_obj.doAjax(G_webrootPath + "/service/main/gotoMenuPage", {
                menuePath: menuePath
            }, function (menuid) {
                if (menuid.indexOf("nosource") > -1) {
                    AUI.dialog.alert("找不到页面或没有权限访问！【" + menuePath + "】", null, 3);
                } else {
                    top.document.getElementById(menuid).click();
                }
            }, "POST", false, 'text');
        }
    };

    /**
     * 顶部面板导航增加路径
     *
     * @param currName
     * @param breadcrumbId
     */
    admin_tools_obj.appendBreadcrumb = function (currName, breadcrumbId) {
        if (!G_settings_use_tabs) {
            var breadcrumbid = breadcrumbId || admin_tools_obj.mainPageDefault.main_breadcrumb_id;
            var $breadcrumb = window.top.$("#" + breadcrumbid);
            if ($breadcrumb.length > 0) {
                $breadcrumb.children(".active").removeClass("active");
                $breadcrumb.append("<li class='active'>" + currName + "</li>");
            }
        }
    };

    /**
     * 顶部面板导航返回一层
     *
     * @param breadcrumbId
     */
    admin_tools_obj.doBackBreadcrumb = function (breadcrumbId) {
        if (!G_settings_use_tabs) {
            var breadcrumbid = breadcrumbId || admin_tools_obj.mainPageDefault.main_breadcrumb_id;
            var $breadcrumb = window.top.$("#" + breadcrumbid);
            if ($breadcrumb.length > 0) {
                if ($breadcrumb.children().length > 1) {
                    $breadcrumb.children(":last").remove();
                    $breadcrumb.children(":last").addClass("active");
                }
            }
        }
    };

    /**
     * 后台文件下载
     *
     * @param filename
     *            文件路径
     * @param isdelete
     *            是否在下载完成后删除文件
     * @param issec
     *            是否进行加密下载
     */
    admin_tools_obj.doDownloadFromBack = function (filename, isdelete, issec) {
        _tools_file_obj.doDownloadFromBack(G_webrootPath + "/view/common/communicationToBack", G_backService.httphostIn, G_backService.charset, filename, isdelete, issec);
    };

    /**
     * 手动加载页面至div
     *
     * @param param 加载参数，为空表示加载默认首页
     *            param{ menuid:菜单id, title:标题, path:页面地址,
	 *            container_id:页面区域divid默认“main_page_content_div”,
	 *            main_breadcrumb_id:顶部导航id默认“main_breadcrumb”,
	 *            main_nav_id:菜单区域id默认“main_nav_list”, isAppend:导航是否追加显示，选项卡模式无效 }
     */
    admin_tools_obj.loadPageInDiv = function (param) {
        var thisobj = new admin_tools_obj();
        var param = $.extend(admin_tools_obj.mainPageDefault, param);

        /** 更新菜单选中状态 */
        admin_tools_obj.setMenuActive(param.menuid, param.main_nav_id);

        /** 生成页面样式 */
        if (G_settings_use_tabs) {
            /** 选项卡样式 */
            if (param.path) {
                var iscontinue = true;
                window.top.$("#" + param.container_id).children().each(
                    function (i, n) {
                        var content = window.top.$(n);
                        var url = content.data("page_url");
                        if (url == param.path) {
                            var id = content.attr("id");
                            iscontinue = false;
                            window.top.$("#" + param.main_breadcrumb_id).find("a[href='#" + id + "']").click();
                            return false;
                        }
                    });
                if (iscontinue) {
                    var pageid = thisobj.updateTabsInfo(param.menuid, param.title, param.container_id, param.main_breadcrumb_id);
                    window.top.$("#" + pageid).loadPage(param.path);
                }
            } else {
                thisobj.updateTabsInfo(param.menuid, param.title, param.container_id, param.main_breadcrumb_id);
            }
        } else {
            /** 顶部导航样式 */
            thisobj.updateBreadcrumb(param.menuid, param.title, param.isAppend, param.main_breadcrumb_id);
            if (param.path) {
                window.top.$("#" + param.container_id).loadPage(param.path);
            }
        }
    };

    /**
     * 更新页面区域顶部距离
     * @param container_id
     *            选项卡页面div所在的容器id
     * @param breadcrumbId
     */
    admin_tools_obj.updateTabContentTop = function (container_id, breadcrumbId) {
        var containerid = container_id || admin_tools_obj.mainPageDefault.container_id;
        var main_breadcrumb_id = breadcrumbId || admin_tools_obj.mainPageDefault.main_breadcrumb_id;
        var breaddiv = window.top.$("#" + main_breadcrumb_id).parent();
        var position = breaddiv.css("position");
        if (position == "fixed") {
            window.top.$("#" + containerid).css("padding-top", Number(breaddiv.height()) + 16 + "px");
        } else {
            window.top.$("#" + containerid).css("padding-top", "");
        }
    };

    /**
     * 关闭当前选项卡
     */
    function closeThisTab(obj, container_id) {
        var containerid = container_id || admin_tools_obj.mainPageDefault.container_id;
        var container = window.top.$("#" + containerid);
        var li = $(window.top.$(obj).parent().parent().parent()[0]);
        var ul = li.parent();
        var a = li.children("a");
        var divid = a.attr("href");
        if (divid == "#" + containerid + "_content_mainpage") {
            ul.children().each(function (i, n) {
                var cli = $(n);
                var cdivid = cli.children("a").attr("href");
                if (cdivid != divid) {
                    window.top.$(cdivid).remove();
                    cli.remove();
                }
            });
            li.removeClass("open");
        } else {
            window.top.$(divid).remove();
            li.remove();
            var lastA = ul.children(":last").children("a");
            lastA.click();
            lastA.addClass("active");
            container.children(".active").removeClass("active");
            container.children(":last").addClass("active");
        }
        admin_tools_obj.updateTabContentTop(container_id, ul.attr("id"));
    }

    /**
     * 刷新当前选项卡页面
     */
    function refreshThisTab(obj) {
        var li = window.top.$(obj).parent().parent().parent();
        li.removeClass("open");
        var a = li.children("a");
        var divid = a.attr("href");
        window.top.$(divid).reloadThisPage();
    }

    /**
     * 显示选项卡下拉菜单
     */
    function showDropdownMenu(obj) {
        var pli = $(obj).closest("li");
        var classname = pli.attr("class");
        if (classname.indexOf("open") > -1) {
            pli.removeClass("open");
        } else {
            pli.addClass("open");
        }
    }

    /**
     * 关闭选项卡下拉菜单
     */
    function closeOtherDropdownMenu(obj, main_breadcrumb_id) {
        var thishref = window.top.$(obj).attr("href");
        var main_breadcrumb_id = main_breadcrumb_id || admin_tools_obj.mainPageDefault.main_breadcrumb_id;
        var breadcrumb = window.top.$("#" + main_breadcrumb_id);
        if (thishref) {
            breadcrumb.children("li.open").each(function (i, n) {
                var li = window.top.$(n);
                if (li.children("a[href!='" + thishref + "']").length > 0) {
                    li.removeClass("open");
                }
            });
        } else {
            breadcrumb.children("li.open").removeClass("open");
        }
    }
})($);
