<?php
/* [WingPHP]
 *  - lib/ShortenURL/bitly.php
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

uselib('ShortenURL/if.shortenurlapi');
uselib('Util/Net');
 
/**
 * bit.ly API ラッパー
 * 
 * example.<code>
 *   uselib('ShortenURL/bitly);
 *   
 *   $bitly = new Bitly('user_id', 'apikey');
 *   $url   = $bitly->shorten('http://www.google.com/');
 *
 *   if($url === false)
 *       die('error');
 * </code>
 *
 * @author M.Katsube
 * @package    BaseModel
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 * @see        http://code.google.com/p/bitly-api/wiki/ApiDocumentation
 */
class Bitly implements ShortenURLAPI{
	private $id;			//ユーザーID
	private $apikey;		//APIキー
	private $use_curl;		//cURL利用フラグ

	/**
	 * コンストラクタ
	 *
	 * @param  string   $id       bit.ly ユーザーID
	 * @param  string   $apikey   bit.ly APIキー
	 * @param  bool     $curl     cURLを利用するか。falseだとfile_get_contents。
	 * @access public
	 */
	function __construct($id, $apikey, $curl=true){
		$this->id       = $id;
		$this->apikey   = $apikey;
		$this->use_curl = $curl;
	}


	//---------------------------------------------------------
	// Public
	//---------------------------------------------------------
	/**
	 * URL短縮
	 *
	 * @param  string $url  短縮したいURL
	 * @return string 短縮後のURL。失敗時はfalse。
	 * @access public
	 */
	public function shorten($url){
		//URLチェック
		uselib('Util/Regex');
		if( empty($url) || preg_match(Regex::URL, $url) === 0 )
			return(false);
	
		//URL作成
		$url = sprintf('http://api.bit.ly/v3/shorten?login=%s&apiKey=%s&longUrl=%s&format=json'
							, urlencode( $this->id )
							, urlencode( $this->apikey )
							, urlencode( $url )
				);
		
		//取得
		if($this->use_curl)
			$buff = net_fetchUrl($url);
		else
			$buff = @file_get_contents($url);
		
		//エラーチェック
		if($buff === false )
			return(false);
	
		//URL取出し
		$buff_j = json_decode($buff, true);
		if( !is_array($buff_j) || $buff_j['status_code'] !== 200  ||  !array_key_exists('data', $buff_j) || !array_key_exists('url', $buff_j['data']) )
			return(false);
		
		return( $buff_j['data']['url'] );
	}

}
?>