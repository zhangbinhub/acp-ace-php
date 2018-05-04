(function($) {

	$(function() {
		AUI.element.initColorSelect("settings_skin_colorpicker");
		$("#settings_breadcrumbs").click(function() {
			if ($(this).prop("checked")) {
				$("#settings_navbar").prop("checked", true);
				$("#settings_sidebar").prop("checked", true);
			}
		});
		$("#settings_sidebar").click(function() {
			if (this.checked) {
				$("#settings_navbar").prop("checked", true);
			} else {
				$("#settings_breadcrumbs").prop("checked", false);
			}
		});
		$("#settings_navbar").click(function() {
			if (!this.checked) {
				$("#settings_sidebar").prop("checked", false);
				$("#settings_breadcrumbs").prop("checked", false);
			}
		});
		$("#settings_compact").click(function() {
			if (this.checked) {
				$("#settings_hover").prop("checked", true);
			}
		});
		$("#settings_hover").click(function() {
			if (!this.checked) {
				$("#settings_compact").prop("checked", false);
			}
		});
		$("#savesetting_btn").click(
				function() {
					$(this).prop("disabled", true);
					AUI.showProcess(undefined, $("#settings_form"));
					admin_tools_obj.doAjax($("#settings_form").attr("action"),
							{
								cmd : "save",
								skin_colorpicker : function() {
									return $("#settings_skin_colorpicker")
											.find("option:selected").attr(
													"data-skin");
								},
								settings_navbar : $("#settings_navbar").prop(
										"checked"),
								settings_sidebar : $("#settings_sidebar").prop(
										"checked"),
								settings_breadcrumbs : $(
										"#settings_breadcrumbs")
										.prop("checked"),
								settings_hover : $("#settings_hover").prop(
										"checked"),
								settings_compact : $("#settings_compact").prop(
										"checked"),
								settings_highlight : $("#settings_highlight")
										.prop("checked"),
								settings_add_container : $(
										"#settings_add_container").prop(
										"checked"),
								settings_use_tabs : $("#settings_use_tabs")
										.prop("checked")
							}, function(data) {
								AUI.closeProcess($("#settings_form"));
								if (data.errmsg) {
									AUI.dialog.alert(data.errmsg, null, 3);
								} else if (data.result == true) {
									AUI.dialog.alert("保存成功！", function() {
										AUI.dialog.closeDialog(
												$("#settings_form"), true);
									}, 1);
								} else {
									AUI.dialog.alert("保存失败！", null, 3);
								}
								$("#savesetting_btn").removeAttr("disabled");
							}, "POST", false, "json", true, function(obj,
									message, exception) {
								AUI.dialog.alert(message, function() {
									AUI.closeProcess($("#settings_form"));
								}, 3);
							});
				});
	});
})($);