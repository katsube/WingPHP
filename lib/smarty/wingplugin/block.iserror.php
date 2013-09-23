<?php

function smarty_block_iserror($params, $content, &$smarty, &$repeat){
	if(!$repeat){
		//-----------------------------
		// 引数取得
		//-----------------------------
		$name = isset( $params['name'] )?  $params['name']:null;

		//-----------------------------
		// エラー有無判定
		//-----------------------------
		global $Scratch;
		$is_error = false;
		if( $name === null ){	// $name未指定時は全エラー
			$is_error = (array_key_exists('error', $Scratch['form']) && count($Scratch['form']['error']) >= 1);
		}
		else{	
			$is_error = (array_key_exists('error', $Scratch['form']) &&  array_key_exists($name, $Scratch['form']['error']));
		}

		if($is_error)
			return($content);
		else
			return(null);
	}
}
