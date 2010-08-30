<?php

/**
 * 指定URLの内容を取得
 *
 * @param  string $url     APIのURL
 * @param  array  $opt     オプション
 * @param  bool   $cache   キャッシュ機構を使うか
 * @return string 取得した内容を返却。失敗時はfalse。
 * @access private
 */
function net_fetchUrl($url, $opt=array(), $use_cache=false){
	global $Conf;
	$ret = null;
	
	//------------------------
	// キャッシュ考慮
	//------------------------
	if($use_cache === true || $Conf['Cache']['api_use']){
		uselib('Cache');
		$cache = new Cache($Conf['Cache']['strage']);
		$key   = sprintf('%s.%s', $Conf['Cache']['api_pre'], sha1($url . (serialize($opt))));
		
		//キャッシュが存在するならそのまま返却
		if( $cache->exists($key) ){
			$ret = $cache->get($key);
		}
		//キャッシュが無いなら新規に取得
		else{
			$ret = net_fetchcurl($url, $opt);
			
			//キャッシュにセット
			$cache->expire($Conf['Cache']['api_expire']);
			$cache->set($key, $ret);
		}
	}
	//------------------------
	// 強制取得
	//------------------------
	else{
		$ret = net_fetchcurl($url, $opt);
	}

	return($ret);
}

/**
 * 指定URLの内容をcURLで取得
 *
 * @param  string $url     APIのURL
 * @param  array  $opt     オプション
 * @return string 取得した内容を返却。失敗時はfalse。
 * @access private
 */
function net_fetchcurl($url, $opt){
	$ch = curl_init();

	//必須オプションセット
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	//任意オプションセット
	if( is_array($opt) && count($opt) > 0 )
		curl_setopt_array($ch, $opt);

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