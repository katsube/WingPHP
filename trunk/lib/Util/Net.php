<?php
/**
 * 指定URLの内容を取得
 *
 * @param  string $url     APIのURL
 * @return string 取得したURLを文字列で返却。失敗時はfalse。
 * @access private
 */
function net_fetchUrl($url, $opt=array()){
	$ch = curl_init();

	//必須オプションセット
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	//任意オプションセット
	if( is_array($opt) && count($opt) > 0 )
		foreach( $opt as $key => $val )
			curl_setopt($ch, $key, $val);
	
	//実行
	$ret = curl_exec($ch);
	if(curl_errno($ch))
		return(false);	//メッセージはcurl_error($ch);
	
	curl_close($ch);

	return($ret);
}

/**
 * HTTPヘッダ作成
 *
 * @param  array  HTTPヘッダ
 * @param  string プロトコル
 * @return Object コンテキスト
 * @access public
 */
function net_getContext($opt=array(), $pro='http') {
	return(
		stream_context_create( array( $pro => $opt) )
	);
}
?>