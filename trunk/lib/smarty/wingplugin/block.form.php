<?php

function smarty_block_form($params, $content, &$smarty, &$repeat){
	if(!$repeat){
		$attrs = array('action', 'method', 'name', 'id', 'class', 'target', 'accept', 'accept-charset', 'enctype');
		$attr  = array();

		foreach($attrs as $name){
			if(array_key_exists($name, $params)){
					$attr[$name] = $params[$name];
			}
		}

		$tag = '<form';
		foreach($attr as $name => $value){
			$name   = htmlspecialchars($name);
			$value  = htmlspecialchars($value);

			$tag   .= sprintf(' %s="%s"', $name, $value);
		}
		$tag .= '>';

		return($tag . $content . '</form>');
	}
}
