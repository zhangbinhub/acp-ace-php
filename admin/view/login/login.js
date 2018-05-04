(function ($) {
    /**
     * 键盘事件
     *
     * @param event
     */
    function keydownfun(event) {
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if (e && e.keyCode == 13) {
            doLogin();
        }
    }

    /**
     * 获取验证码
     */
    function changeYZM() {
        var $jobj = $(this);
        var srcStr = $jobj.attr("src").substring(0,
            $jobj.attr("src").indexOf("?"));
        $jobj.attr("src", srcStr + "?" + Math.random());
    }

    /**
     * 工具栏点击事件
     *
     * @param event
     */
    function toolbarfun(event) {
        event.preventDefault();
        var target = $(this).data('target');
        $('.widget-box.visible').removeClass('visible');
        $(target).addClass('visible');
    }

    /**
     * 密码信息加密
     */
    function encryptPassword(username, password, code) {
        var str1 = _tools_security_obj.encryptMD5(password).toLowerCase();
        var str2 = _tools_security_obj.encryptMD5(str1 + username).toLowerCase();
        return _tools_security_obj.encryptMD5(str2 + code.toLowerCase()).toLowerCase();
    }

    /**
     * 登录
     */
    function doLogin() {
        $(document).unbind("keydown", keydownfun);
        $("#username").prop("readonly", true);
        $("#password").prop("readonly", true);
        $("#yzm").prop("readonly", true);
        $("#yzm_img").unbind("click", changeYZM);
        $("#forgot_p").prop("disabled", true);
        $("#RememberInfo").bind("click", function () {
            return false;
        });
        $("#login_btn").prop("disabled", true);
        $("#login_btn").text("正在登录...");
        var username = $.trim($("#username").val());
        var password = $.trim($("#password").val());
        var code = $.trim($("#yzm").val());
        if (username == "输入用户名") {
            $("#username").val("");
        }
        if (code == "输入验证码") {
            $("#yzm").val("");
        }
        if (password != "") {
            $("#password").val("");
            password = encryptPassword(username, password, code);
        }
        $("#loginform").append(
            "<input type='hidden' id='cmd' name='cmd' value='login'/>");
        $("#loginform").append(
            "<input type='hidden' id='pen' name='pen' value='" + password
            + "'/>");
        setTimeout(function () {
            $("#loginform").submit();
        }, 0);
    }

    $(function () {
        $(document).bind("keydown", keydownfun);
        $(document).on("click", ".toolbar a[data-target]", toolbarfun);
        $("#yzm_img").bind("click", changeYZM);
        $("#login_btn").click(function () {
            doLogin();
        });
        $("#getPassword_btn").click(function () {
            AUI.dialog.alert("待开发！", null, 2);
        });
    });
})($);