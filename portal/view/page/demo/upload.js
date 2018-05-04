(function($) {

	$(function() {
		var upload_p;
		var options = {
			path : "/portal/files/upload",
			files : "/portal/files/upload/123123123131.zip",
			acceptedFiles : '.doc,.docx',
			maxFiles : 5
		// afterCompleteFun : function(filepath) {
		// AUI.dialog.alert("afterCompleteFun:" + filepath);
		// },
		// afterDeleteFun : function(result, filepath) {
		// AUI.dialog.alert("afterDeleteFun:" + filepath);
		// showFileNames();
		// }
		};
		upload_p = new _tools_file_obj.UpLoadPlugin("test_uploaddiv",
				"test_uploadplugin", options);
		upload_p.show();
		_tools_file_obj.initUpLoadImage("test_avatar", null, null, null, true);

		$("#test_viewFileList").click(function() {
			AUI.dialog.alert($("#test_uploadplugin").val());
		});
	});
})($);