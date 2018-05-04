(function ($) {

    funcpowerconfig = function (id, appid) {

        var funcid = id;

        var appid = appid;

        /**
         * 初始化角色列表
         */
        this.initRoleList = function () {
            AUI.showProcess(undefined, $("#powerconfig_funcconfig_funcpower_rolelist").closest(".page-content"));
            admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceFunc",
                {
                    cmd: "searchFuncRoles",
                    appid: appid,
                    funcid: funcid
                }, function (data) {
                    AUI.closeProcess($("#powerconfig_funcconfig_funcpower_rolelist").closest(".page-content"));
                    if (data.errmsg) {
                        AUI.dialog.alert(data.errmsg, null, 3);
                    } else {
                        AUI.element.updateSelectItem("powerconfig_funcconfig_funcpower_rolelist", data.roles, data.select_roles);
                        AUI.element.refreshDuallist("powerconfig_funcconfig_funcpower_rolelist");
                    }
                }, "POST", false, "json", true, function (obj, message, exception) {
                    AUI.dialog.alert(message, function () {
                        AUI.closeProcess($("#powerconfig_funcconfig_funcpower_rolelist").closest(".page-content"));
                    }, 3);
                });
        };

        /**
         * 保存配置信息
         *
         * @param callBackFunc
         */
        this.doSave = function (callBackFunc) {
            AUI.showProcess(undefined, $("#powerconfig_funcconfig_funcpower_rolelist").closest(".page-content"));
            admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceFunc", {
                cmd: "saveFuncRoles",
                appid: appid,
                funcid: funcid,
                select_roles: $("#powerconfig_funcconfig_funcpower_rolelist").val()
            }, function (data) {
                AUI.closeProcess($("#powerconfig_funcconfig_funcpower_rolelist").closest(".page-content"));
                if (data.errmsg) {
                    AUI.dialog.alert(data.errmsg, null, 3);
                } else {
                    AUI.dialog.alert("保存成功！", function () {
                        if (typeof (callBackFunc) == "function") {
                            callBackFunc();
                        }
                    }, 1);
                }
            }, "POST", false, "json", true, function (obj, message, exception) {
                AUI.dialog.alert(message, function () {
                    AUI.closeProcess($("#powerconfig_funcconfig_funcpower_rolelist").closest(".page-content"));
                }, 3);
            });
        };
    };

    $(function () {
        var obj = new funcpowerconfig($("#powerconfig_funcconfig_funcpower_funcid").val(), $("#powerconfig_funcconfig_funcpower_appid").val());
        AUI.element.initDuallist("powerconfig_funcconfig_funcpower_rolelist",
            {
                selectorMinimalHeight: 270,
                selectedListLabel: '<div class="col-sm-12 well well-sm center">已授权角色</div>',
                nonSelectedListLabel: '<div class="col-sm-12 well well-sm center">备选角色</div>'
            });
        $("#powerconfig_funcconfig_funcpower_reset_btn").click(function () {
            obj.initRoleList();
        });
        $("#powerconfig_funcconfig_funcpower_cancle_btn").click(function () {
            AUI.dialog.closeDialog($("#powerconfig_funcconfig_funcpower_rolelist"));
        });
        $("#powerconfig_funcconfig_funcpower_saveFuncpower_btn").click(function () {
            obj.doSave(function () {
                AUI.dialog.closeDialog($("#powerconfig_funcconfig_funcpower_rolelist"), true);
            });
        });
        obj.initRoleList();
    });
})($);