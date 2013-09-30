<?php
require_once('lib.iserror.php');
function smarty_block_isnoterror($params, $content, &$smarty, &$repeat){
	if(!$repeat){
		//-----------------------------
		// 引数取得
		//-----------------------------
		$name = isset( $params['name'] )?  $params['name']:null;

		//-----------------------------
		// エラー有無判定
		//-----------------------------
		$is_error = smarty_wgplugin_iserror($name);

		if(!$is_error)
			return($content);
		else
			return(null);
	}
}
