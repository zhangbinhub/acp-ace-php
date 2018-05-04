(function ($) {

    var loginno = "";

    $(function () {
        loginno = $.trim($("#curruser_profile_loginno").val());

        _tools_file_obj.initUpLoadAvatar("curruser_profile_avatar");

        AUI.element.initValidate("curruser_profile_form",
            {
                curruser_profile_username: "请输入姓名",
                curruser_profile_opassword: {
                    minlength: "至少输入6位密码"
                },
                curruser_profile_password1: {
                    minlength: "至少输入6位密码"
                },
                curruser_profile_password2: {
                    minlength: "至少输入6位密码",
                    equalTo: "两次输入的密码不相同"
                }
            }, {
                curruser_profile_opassword: {
                    minlength: 6
                },
                curruser_profile_password1: {
                    minlength: 6
                },
                curruser_profile_password2: {
                    minlength: 6,
                    equalTo: "#curruser_profile_password1"
                }
            }, function (form) {
                if ($.trim($("#curruser_profile_opassword").val()) != "" || $.trim($("#curruser_profile_password1").val()) != "") {
                    if ($.trim($("#curruser_profile_opassword").val()) == "" || $.trim($("#curruser_profile_password1").val()) == "") {
                        AUI.dialog.alert("请正确填写密码！", null, 3);
                        return;
                    }
                }
                var avatar = $.trim($("#curruser_profile_avatar").attr("src"));
                var username = $.trim($("#curruser_profile_username").val());
                var data = null;
                if ($.trim($("#curruser_profile_password1").val()) != "") {
                    var opassword = _tools_security_obj.encryptMD5(_tools_security_obj.encryptMD5($.trim($("#curruser_profile_opassword").val())).toLowerCase() + loginno).toLowerCase();
                    var password = _tools_security_obj.encryptMD5(_tools_security_obj.encryptMD5($.trim($("#curruser_profile_password1").val())).toLowerCase() + loginno).toLowerCase();
                    data = {
                        avatar: avatar,
                        username: username,
                        opassword: opassword.toString(),
                        password: password.toString()
                    }
                } else {
                    data = {
                        avatar: avatar,
                        username: username
                    }
                }
                portal_tools_obj.doAjax(G_webrootPath + "/service/profile/doSaveProfile", data, function (result) {
                    if (result.errmsg) {
                        AUI.dialog.alert(result.errmsg, null, 3);
                    } else {
                        AUI.dialog.alert("保存成功！", function () {
                            AUI.showProcess();
                            window.location.href = G_mainpage_url;
                        }, 1);
                    }
                });
            });
    });
})($);