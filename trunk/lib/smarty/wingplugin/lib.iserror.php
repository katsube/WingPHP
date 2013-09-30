<?php
function smarty_wgplugin_iserror($name = null){
	global $Scratch;
	$is_error = false;

	if( $name === null ){	// $name未指定時は全エラー
		$is_error = (array_key_exists('error', $Scratch['form']) && count($Scratch['form']['error']) >= 1);
	}
	else{
		$is_error = (array_key_exists('error', $Scratch['form']) &&  array_key_exists($name, $Scratch['form']['error']));
	}

	return($is_error);
}
