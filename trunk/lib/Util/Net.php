<?php

/**
 * 指定URLの内容を取得
 *
 * @param  string $url     APIのURL
 * @param  array  $opt     オプション
 * @param  bool   $cache   キャッシュ機構を使うか
 * @return string 取得したURLを文字列で返却。失敗時はfalse。
 * @access private
 */
function net_fetchUrl($url, $opt=array(), $use_cache=true){
	global $Conf;
	$ret = null;
	
	//------------------------
	// cURLで取得する
	// (クロージャー)
	//------------------------
	$curl = function($url1, $opt1){
		$ch = curl_init();

		//必須オプションセット
		curl_setopt($ch, CURLOPT_URL, $url1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		//任意オプションセット
		if( is_array($opt1) && count($opt1) > 0 )
			curl_setopt_array($ch, $opt1);

		//実行
		$buff = curl_exec($ch);
		if(curl_errno($ch))
			return(false);	//メッセージはcurl_error($ch);
	
		curl_close($ch);
		
		return($buff);
	};

	//------------------------
	// キャッシュ考慮
	//------------------------
	if($use_cache === true && $Conf['Cache']['api_use']){
		uselib('Cache');
		$cache = new Cache($Conf['Cache']['strage']);
		$key   = sprintf('%s.%s', $Conf['Cache']['api_pre'], ($url . implode('=', $opt)) );
		
		if( $cache->exists($key) ){
			$ret = $cache->get($key);
		}
		else{
			$ret = $curl($url, $opt);
			
			//キャッシュにセット
			$cache->expire($Conf['Cache']['expire']);
			$cache->set($key, $ret);
		}
	}
	//------------------------
	// 強制取得
	//------------------------
	else{
		$ret = $curl($url, $opt);
	}

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