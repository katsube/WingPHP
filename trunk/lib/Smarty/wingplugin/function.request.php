<?php
/** 
 * $_REQUEST変数の指定キーの内容を取得（エスケープ付き）
 *
 */
function smarty_function_request($params, &$smarty){
	// クエリ名の指定があるか
	if( array_key_exists('key', $params) ){
		//REQUEST内に存在するか
		if( array_key_exists($params['key'], $_REQUEST) ){
			$value = $_REQUEST[$params['key']];
			
			//エスケープし返却
			if( array_key_exists('escape', $params) ){
				switch($params['escape']){
					case 'url':
						return(urlencode($value));
						break;
					
					case 'html':
					default:
						return(htmlspecialchars($value));
				}
			}
			//そのまま返却
			else{
				return($value);
			}
		}
	}

	return(null);
}
