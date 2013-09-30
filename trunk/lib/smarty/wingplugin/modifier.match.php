<?php
function smarty_modifier_match($subject, $pattern){
	if( preg_match($pattern, $subject) )
		return(true);
	else
		return(false);
}
