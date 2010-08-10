<?php
/* [WingPHP]
 *  - lib/Google/Search.php
 *  
 * The MIT License
 * Copyright (c) 2009 WingPHP < http://wingphp.net >
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * GoogleSearchクラス
 * 
 * Google AJAX Search APIのラッパークラス。
 * メソッド名を web|local|video|blogs|news|books|images|patent から選択することで
 * サーチャーを変更できる。
 *   ※引数などはどれも同じ。
 *
 * 任意ではあるがGoogleに登録しAPIキーを取得。指定することが望ましい。
 * 参照元(REFERER)の指定はドキュメント上は必須とされている。
 *
 * example.<code>
 *     uselib('Google/Search');
 *
 *     $referer = 'http://wingphp.net/';  					//参照元
 *     $apikey  = 'youre google api key (optional)';		//APIキー
 *
 *     $gsearch = GoogleSearch($referer, $apikey);
 *     $ret     = $gsearch->web(
 *						  '検索ワード'
 *						, array('hl'=>'ja', 'start'=>10)	//日本語で10件目から取得(オプション)
 *                );
 * </code>
 *
 * @package    GoogleSearch
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class GoogleSearch{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	private $base_url = "http://ajax.googleapis.com/ajax/services/search/";
	private $ver      = '1.0';
	private $use_curl = true;		//cURL利用フラグ
	private $referer;
	private $apikey;

	/**
	 * コンストラクタ
	 *
	 * @param  string  $referer  参照元
	 * @param  string  $key      Google API Key
	 * @access public
	 * @link   http://code.google.com/intl/ja/apis/ajaxsearch/signup.html
	 * @todo   $refererが未指定の場合、自動で現在URLをセットする
	 */
	function __construct($referer, $key=null){
		$this->referer = $referer;
		$this->apikey  = $key;
	}
	
	/**
	 * メソッドのオーバーライド
	 */
	 function __call($name, $param){
		if(preg_match('/^(web|local|video|blogs|news|books|images|patent)$/', $name) > 0){
			$q   = $param[0];
			$opt = empty($param[1])?  array():$param[1];
			
			return( $this->request($name, $q, $opt) );
		}
		else{
			die();
		}
	}


	//--------------------------------------------
	// Public
	//--------------------------------------------

	/**
	 * APIへリクエストし結果返却
	 *
	 * @param  string  $type  サーチャーの種類を指定。web|local|video|blogs|news|books|images|patent
	 * @param  string  $q     検索文字列
	 * @param  array   $opt   オプション
	 * @return array   成功した場合、連想配列を返却。
	 *                 リクエストに失敗、もしくはレスポンス結果が200でない場合は false
	 * @link   http://code.google.com/intl/ja/apis/ajaxsearch/documentation/#fonje
	 * @access public
	 */
	function request($type, $q, $opt=array()){
		//URL作成
		$url = $this->_makeUrl($type, $q, $opt);

		//取得
		if( $this->use_curl ){
			$ret = $this->_fetchUrl($url, $this->referer);
		}
		else{
			$context = $this->_getContext();
			$ret = @file_get_contents($url, false, $context);
		}
		
		//エラーチェック
		if($ret === false )
			return( false );
		
		//結果返却
		$json = json_decode($ret, true);
		if( array_key_exists('responseStatus', $json) && $json['responseStatus'] === 200 )
			return($json);
		else
			return(false);
	}

	/**
	 * cURL利用有無変更
	 *
	 * @param  bool   $flag  trueなら利用、falseならfile_get_contents
	 * @access public
	 */	
	function curl_use($flag){
		$this->use_curl = $flag;
	}


	//--------------------------------------------
	// Private
	//--------------------------------------------
	/**
	 * HTTPヘッダ作成
	 *
	 * @return Object コンテキスト
	 * @access public
	 */
	private function _getContext(){
		return(
			stream_context_create(array(
						'http' => array(
							  'method'  => 'GET'
							, 'header'  => sprintf('Referer: %s', $this->referer)
						))
			)
		);
	}
	
	/**
	 * リクエスト用URL作成
	 *
	 * @param  string  $type  サーチャーの種類を指定。web|local|video|blogs|news|books|images|patent
	 * @param  string  $q     検索文字列
	 * @param  array   $opt   オプション
	 * @return string  リクエスト用URL
	 * @access public
	 */
	private function _makeUrl($type, $q, $opt=array()){
		//APIキーが指定されていればセット
		if( !is_null($this->apikey) && !array_key_exists('key', $opt) )		//$optに'key'がある場合はスルー
			$opt['key'] = $this->apikey;
	
		//オプション値からクエリー作成
		$query = '';
		if(count($opt) > 0)
			foreach( $opt as $key=>$val )
				$query .= sprintf('&%s=%s', $key, urlencode($val) );
	
		//URL生成してから返却
		return(
			sprintf('%s%s?v=%s&q=%s%s'
						, $this->base_url
						, $type
						, urlencode($this->ver)
						, urlencode($q)
						, $query
			)
		);
	}

	/**
	 * 指定URLの内容を取得
	 *
	 * @param  string $url     APIのURL
	 * @param  string $referer REFERER
	 * @return string 取得したURLを文字列で返却。失敗時はfalse。
	 * @access private
	 */
	private function _fetchUrl($url, $referer){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		
		$ret = curl_exec($ch);
		if(curl_errno($ch))
			return(false);	//メッセージはcurl_error($ch);
		curl_close($ch);
	
		return($ret);
	}
}
?>