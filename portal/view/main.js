(function($) {

	$(function() {
		$("#main_setting_a").click(function() {
			AUI.dialog.inDialog(600, 378, "个性设置", {
				innerUrl : "setting/setting"
			}, null, function(data) {
				if (data) {
					AUI.showProcess();
					window.location.reload();
				}
			});
		});
		$("#main_user_info_a").click(function() {
			portal_tools_obj.loadPageInDiv({
				title : "个人资料",
				path : "profile/profile"
			});
		});
		$("#main_sysparam_conf_btn").click(function() {
			portal_tools_obj.loadPageInDiv({
				title : "系统参数配置",
				path : "sysparam/sysparam"
			});
		});
		$("#main_app_conf_btn").click(function() {
			portal_tools_obj.loadPageInDiv({
				title : "应用配置",
				path : "application/application"
			});
		});
		$("#main_role_conf_btn").click(function() {
			portal_tools_obj.loadPageInDiv({
				title : "角色配置",
				path : "role/role"
			});
		});
		$("#main_power_conf_btn").click(function() {
			portal_tools_obj.loadPageInDiv({
				title : "权限配置",
				path : "power/power"
			});
		});
		$("#main_loginout_a").click(function() {
			AUI.dialog.confirm("确定退出登录？", function(data) {
				if (data) {
					portal_tools_obj.doLogout();
				}
			});
		});

		portal_tools_obj.loadPageInDiv();
	});
})($);