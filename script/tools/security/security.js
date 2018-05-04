(function($) {

	var security = {};

	/**
	 * 生成随机字符串
	 *
	 * @param len
	 *            字符串长度，默认36
	 * @returns {String}
	 */
	security.randomString = function(len) {
		var strlen = len || 36;
		var $chars = 'ABCDEFGHJKMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
		var maxPos = $chars.length;
		var str = '';
		for (var i = 0; i < strlen; i++) {
			str += $chars.charAt(Math.floor(Math.random() * maxPos));
		}
		return str;
	};

	/**
	 * 进行RSA加密 pkcs1pad
	 *
	 * @param srcStr
	 * @param modulus
	 *            为空则表示前端直接使用已有公钥加密
	 * @param exponent
	 *            为空则表示前端直接使用已有公钥加密
	 * @returns
	 */
	security.rsa_encrypt = function(srcStr, modulus, exponent) {
		var mod = modulus;
		var e = exponent;
		var rsa = new RSAKey();
		rsa.setPublic(mod, e);
		return hex2b64(rsa.encrypt(srcStr));
	};

	/**
	 * 进行AES加密 CryptoJS.pad.Pkcs7
	 *
	 * @param srcStr
	 * @param keyStr
	 */
	security.aes_encrypt = function(srcStr, keyStr) {
		var key = CryptoJS.enc.Utf8.parse(keyStr);
		var encryptedData = CryptoJS.AES.encrypt(srcStr, key, {
			mode : CryptoJS.mode.ECB,
			padding : CryptoJS.pad.Pkcs7
		});
		return CryptoJS.enc.Base64.stringify(encryptedData.ciphertext);
	};

	/**
	 * MD5加密
	 *
	 * @param str
	 */
	security.encryptMD5 = function(str) {
		return CryptoJS.MD5(str).toString();
	};

	/**
	 * 数据加密
	 *
	 * @param srcStr
	 *            需要进行加密的明文
	 * @param callBackFun
	 * @param isShowProcess
	 */
	security.doEncryptToPhp = function(srcStr, callBackFun, isShowProcess) {
		var isshowprocess = isShowProcess != undefined ? isShowProcess : true;
		_global_tools_obj.doAjax("/view/common/security", {
			cmd : "getPk"
		}, function(data) {
			var modulus = data.modulus;
			var exponent = data.exponent;
			var keyStr = security.randomString(16);
			var encryptedstr = security.aes_encrypt(srcStr, keyStr);
			var encryptkey = security.rsa_encrypt(keyStr, modulus, exponent);
			var result = {
				key : encryptkey,
				encryptedstr : encryptedstr
			};
			if (typeof (callBackFun) == "function") {
				callBackFun(JSON.stringify(result));
			}
		}, "POST", isshowprocess);
	};

	/**
	 * 数据加密
	 *
	 * @param comurl
	 *            必选。后台http请求转发页面
	 * @param srcStr
	 *            需要进行加密的明文
	 * @param host
	 *            后台主机地址
	 * @param charset
	 *            后台请求字符集
	 * @param timOut
	 *            超时时间，单位（毫秒），默认10000毫秒
	 * @param callBackFun
	 * @param isShowProcess
	 */
	security.doEncryptToBack = function(comurl, srcStr, host, charset, timOut,
			callBackFun, isShowProcess) {
		var traitid = security.randomString() + new Date().getTime();
		var timout = timOut || 10000;
		var isshowprocess = isShowProcess != undefined ? isShowProcess : true;
		_global_tools_obj.doAjaxToServer(comurl, host + "getPk", charset, {
			traitid : traitid
		}, function(data) {
			if (data.errmsg) {
				AUI.dialog.alert(data.errmsg, null, 3);
			} else {
				var modulus = data.modulus;
				var exponent = data.exponent;
				var keyStr = security.randomString(16);
				var encryptedstr = security.aes_encrypt(srcStr, keyStr);
				var encryptkey = security
						.rsa_encrypt(keyStr, modulus, exponent);
				var result = {
					traitid : traitid,
					key : encryptkey,
					encryptedstr : encryptedstr
				};
				if (typeof (callBackFun) == "function") {
					callBackFun(JSON.stringify(result));
				}
			}
		}, timout, isshowprocess);
	};

	_tools_security_obj = security;
})($);