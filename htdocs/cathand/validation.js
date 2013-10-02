/**
 * helper: Validation
 */
var wingValidation = (function(){
	"use strict";

	var $ = function(id){ return( document.getElementById(id) ); };
	var obj = {};
	obj.rule = {
		  'url'    : function(val){ return(1); }		// 書式 URL
		, 'email'  : function(val){ return(1); }		// 書式 メールアドレス
		, 'ip4'    : function(val){ return(1); }		// 書式 IPv4形式
		, 'postcd' : function(val){ return(1); }		// 書式 郵便番号 000-0000
		, 'tel'    : function(val){ return(1); }		// 書式 電話番号 0123-12-1234, 03-12-1234, 090-1234-1234

		, 'num'   : function(val){ return(1); }		// 書式 半角数字(文字列としての数字も真)
		, 'alpha' : function(val){ return(1); }		// 書式 半角英字
		, 'alnum' : function(val){ return(1); }		// 書式 半角英数字

		, 'require' : function(val){ return(1); }			// 必須項目
		, 'bytemax' : function(val,opt){ return(1); }		// 最大バイト長
		, 'bytemin' : function(val,opt){ return(1); }		// 最小バイト長
		, 'max'     : function(val,opt){ return(1); }		// 最大値
		, 'min'     : function(val,opt){ return(1); }		// 最小値

		, 'match' : function(val,opt){ return(1); }		// 指定した正規表現にマッチするか
		, 'eq'    : function(val,opt){ return(1); }		// 指定した文字列と同じか
		, 'ne'    : function(val,opt){ return(1); }		// 指定した文字列と違うか
		, 'in'    : function(val,opt){ return(1); }		// 指定したリスト内のいずれかと合致するか

		, 'date'  : function(val,opt){ return(1); }		// 日付が妥当な物か
		, 'time'  : function(val,opt){ return(1); }		// 時間が妥当な物か(24時間制)

		, 'grequire1' : function(val){ return(1); }		// 配列の要素中、1つ以上が入力されている
		, 'gin' : function(val,opt){ return(1); }			// 配列の要素が、すべて指定したリスト内のいずれかと合致するか
	};

	/**
	 * Validation実行
	 */
	obj.check = function(){
		;
	};

	/**
	 * Validationまとめて実行
	 */
	 obj.checks = function(){
		;
	};

	return(obj);
})();
