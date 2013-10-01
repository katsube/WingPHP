<?php

function smarty_block_form($params, $content, &$smarty, &$repeat){
	//id属性は必須
	if( !array_key_exists('id', $params) )
		return(null);
	$id = $params['id'];

	if($repeat){
		global $Scratch;
		$Scratch['form']['id'] = $id;

		if( array_key_exists('validation', $Scratch['form'][$id]) ){
			$smarty->assign('validation', true);
		}
	}
	else{
		global $Conf;
		$attrs = array('action', 'method', 'name', 'id', 'class', 'target', 'accept', 'accept-charset', 'enctype');
		$attr  = array();

		foreach($attrs as $name){
			if(array_key_exists($name, $params)){
				$attr[$name] = $params[$name];
			}
		}

		$tag = '<form';
		foreach($attr as $name => $value){
			$name  = htmlspecialchars($name);
			$value = htmlspecialchars($value);

			$tag .= sprintf(' %s="%s"', $name, $value);
		}
		$tag .= '>';
		$tag .= sprintf('<input type="hidden" name="%s" value="%s">', $Conf['validation']['form']['idname'], $id);

		return($tag . $content . '</form>');
	}
}
