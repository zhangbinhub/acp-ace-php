(function ($) {

    var oldloginno = "";

    adminuserconfig_userinfo = function (id) {

        var id = id;

        /**
         * 初始化事件
         */
        this.initEvent = function () {
            var configobj = this;
            if (id != "" && $("#userconfig_userinfo_userlevel").val() == "0") {
                AUI.element.initSpinner("userconfig_userinfo_userlevel", {
                    min: 0,
                    max: 999
                });
                AUI.element.isEnabledSpinner("userconfig_userinfo_userlevel", false);
                $("#userconfig_userinfo_userstatus").prop("disabled", true);
                $("#userconfig_userinfo_userrolenames").prop("disabled", true);
            } else {
                AUI.element.initSpinner("userconfig_userinfo_userlevel", {
                    min: currLevel + 1,
                    max: 999
                });
            }
            AUI.element.initSpinner("userconfig_userinfo_usersort", {
                min: 0,
                max: 999
            });
            AUI.element.initValidate("userconfig_userinfo_form", {
                userconfig_userinfo_loginno: {
                    required: "请输入登录账号",
                    remote: "此账号已存在"
                },
                userconfig_userinfo_username: "请输入姓名"
            }, {
                userconfig_userinfo_loginno: {
                    remote: {
                        url: G_webrootPath + "/service/page/user/validationLoginNo",
                        type: "POST",
                        data: {
                            loginno: function () {
                                return $("#userconfig_userinfo_loginno").val();
                            },
                            id: id
                        }
                    }
                }
            });
            $("#userconfig_userinfo_info_a").click(function () {
                AUI.element.resizeChosen();
            });
            if (id != "") {
                $("#userconfig_userinfo_rpw_btn").click(function () {
                    configobj.doResetPassword();
                });
            }
            $("#userconfig_userinfo_reset_btn").click(function () {
                $("#userconfig_userinfo_form")[0].reset();
                AUI.element.resetValidate("userconfig_userinfo_form");
                AUI.element.refreshChosen("userconfig_userinfo_userrolenames");
                configobj.initInfo();
            });
            $("#userconfig_userinfo_cancle_btn").click(function () {
                AUI.dialog.closeDialog($("#userconfig_userinfo_form"));
            });
            $("#userconfig_userinfo_save_btn").click(function () {
                configobj.doSave(function () {
                    AUI.dialog.closeDialog($("#userconfig_userinfo_form"), true);
                });
            });
        };

        /**
         * 初始化用户信息
         */
        this.initInfo = function () {
            var configobj = this;
            admin_tools_obj.doAjax(G_webrootPath + "/service/page/user/serviceUser", {
                oper: "seachDepartment",
                id: id
            }, function (treedata) {
                if (treedata.errmsg) {
                    AUI.dialog.alert(treedata.errmsg, null, 3);
                } else {
                    configobj.generateDepartmentTree(treedata.tree);
                }
            }, 'POST', false);
        };

        /**
         * 保存用户详细信息
         */
        this.doSave = function (callBackFunc) {
            if (AUI.element.doValidate("userconfig_userinfo_form")) {
                var configobj = this;
                var level = $("#userconfig_userinfo_userlevel").val();
                if (level <= currLevel) {
                    AUI.dialog.alert("数据异常，不能编辑更高级别的用户！", null, 3);
                } else {
                    if (id != "" && oldloginno != $.trim($("#userconfig_userinfo_loginno").val())) {
                        AUI.dialog.confirm("变更登录账号之后，将自动重置登录密码，是否继续？", function (data) {
                            if (data) {
                                configobj.saveFunc(function () {
                                    configobj.resetPasswordFunc(callBackFunc);
                                });
                            }
                        });
                    } else {
                        configobj.saveFunc(callBackFunc);
                    }
                }
            }
        };

        /**
         * 执行保存
         * @param callBackFunc
         */
        this.saveFunc = function (callBackFunc) {
            AUI.showProcess(undefined, $("#userconfig_userinfo_form").closest(".widget-main"));
            var departmentids = AUI.tree.getCheckedNodeIds("userconfig_userinfo_userdepartmenttree");
            var level = $("#userconfig_userinfo_userlevel").val();
            admin_tools_obj.doAjax(G_webrootPath + "/service/page/user/serviceUser", {
                oper: "saveinfo",
                id: id,
                name: $("#userconfig_userinfo_username").val(),
                loginno: $.trim($("#userconfig_userinfo_loginno").val()),
                level: level,
                sort: $("#userconfig_userinfo_usersort").val(),
                status: $("#userconfig_userinfo_userstatus").val(),
                roleids: $("#userconfig_userinfo_userrolenames").val(),
                departmentids: departmentids
            }, function (data) {
                AUI.closeProcess($("#userconfig_userinfo_form").closest(".widget-main"));
                if (data.errmsg) {
                    AUI.dialog.alert(data.errmsg, null, 3);
                } else {
                    AUI.dialog.alert(data.result, function () {
                        if (typeof (callBackFunc) == "function") {
                            callBackFunc();
                        }
                    }, 1);
                }
            }, "POST", false, "json", true, function (obj, message, exception) {
                AUI.dialog.alert(message, function () {
                    AUI.closeProcess($("#userconfig_userinfo_form").closest(".widget-main"));
                }, 3);
            });
        };

        /**
         * 重置密码
         */
        this.doResetPassword = function () {
            var level = $("#userconfig_userinfo_userlevel").val();
            if (level <= currLevel) {
                AUI.dialog.alert("数据异常，不能编辑更高级别的用户！", null, 3);
            } else {
                var configobj = this;
                AUI.dialog.confirm("确定重置用户密码？", function (data) {
                    if (data) {
                        configobj.resetPasswordFunc();
                    }
                });
            }
        };

        /**
         * 执行重置密码
         */
        this.resetPasswordFunc = function (callBackFunc) {
            AUI.showProcess(undefined, $("#userconfig_userinfo_form").closest(".widget-main"));
            admin_tools_obj.doAjax(G_webrootPath + "/service/page/user/serviceUser", {
                oper: "rpw",
                id: id
            }, function (data) {
                AUI.closeProcess($("#userconfig_userinfo_form").closest(".widget-main"));
                if (data.errmsg) {
                    AUI.dialog.alert(data.errmsg, null, 3);
                } else {
                    AUI.dialog.alert(data.result, function () {
                        if (typeof (callBackFunc) == "function") {
                            callBackFunc();
                        }
                    }, 1);
                }
            }, "POST", false, "json", true, function (obj, message, exception) {
                AUI.dialog.alert(message, function () {
                    AUI.closeProcess($("#userconfig_userinfo_form").closest(".widget-main"));
                }, 3);
            });
        };

        /**
         * 生成机构选择树
         *
         * @param treeData
         */
        this.generateDepartmentTree = function (treeData) {
            var setting = {
                view: {
                    selectedMulti: true
                },
                check: {
                    enable: true,
                    chkboxType: {
                        "Y": "",
                        "N": ""
                    }
                },
                data: {
                    simpleData: {
                        enable: true,
                        idKey: "id",
                        pIdKey: "pid",
                        rootPId: "0"
                    }
                }
            };
            var departmenttree = $("#userconfig_userinfo_userdepartmenttree");
            AUI.tree.initTree(departmenttree, setting, treeData);
        };
    };

    $(function () {
        oldloginno = $.trim($("#userconfig_userinfo_loginno").val());
        var obj = new adminuserconfig_userinfo($("#userconfig_userinfo_id").val());
        AUI.element.initChosen("#userconfig_userinfo_userrolenames");
        obj.initEvent();
        obj.initInfo();
    });
})($);