<?php
function smarty_modifier_inarray($array, $key){
	if( in_array($key, $array) )
		return(true);
	else
		return(false);
}
