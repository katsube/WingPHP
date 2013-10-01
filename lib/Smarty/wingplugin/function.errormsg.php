<?php

function smarty_function_errormsg($params, $template){
	//-----------------------------
	// 引数取得
	//-----------------------------
	$name = isset( $params['name'] )?  $params['name']:null;
	if($name === null)
		return(null);

	//-----------------------------
	// エラーメッセージ返却
	//-----------------------------
	global $Scratch;
	if( array_key_exists('error', $Scratch['form']) && array_key_exists($name, $Scratch['form']['error'])){
		$msg = null;
		$error = $Scratch['form']['error'][$name];
		$len   = count($error);
		for($i=0; $i < $len; $i++){
			$cd = $error[$i];
			$msg = $Scratch['form']['errormsg'][$cd];
		}
		return($msg);
	}
	else{
		return(null);
	}
}
