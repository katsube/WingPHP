<?php
/** 
 * input - form部品
 *
 * input要素の出力を行う
 * 対応：text, password, submit, reset, button, image, file, hidden
 * 非対応：radio, checkbox
 */
function smarty_function_input($params, &$smarty){
	$attrs = array('type', 'name', 'value', 'id', 'class', 'size', 'disabled', 'checked', 'tabindex', 'maxlength', 'style', 'src', 'align', 'alt', 'ismap', 'usemap');
	$attr  = array();
	if( !(array_key_exists('type', $params)) )
		return(false);

	foreach($attrs as $name){
		if(array_key_exists($name, $params)){
			if( $name === 'checked'  && $params[$name] === true)
				$attr[$name] = 'checked';
			else if ($name === 'disabled' && $params[$name] === true)
				$attr[$name] = 'disabled';
			else
				$attr[$name] = $params[$name];
		}
	}

	$tag = '<input';
	foreach($attr as $name => $value){
		$name  = htmlspecialchars($name);
		$value = htmlspecialchars($value);

		$tag .= sprintf(' %s="%s"', $name, $value);
	}
	$tag .= '>';

	return($tag);
}
