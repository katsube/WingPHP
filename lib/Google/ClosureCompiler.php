<?php
/* [WingPHP]
 *  - lib/Google/ClosureCompiler.php
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

uselib('Util/Net'); 	//net_fetchUrl, net_getContext

/**
 * GoogleClosureCompilerクラス
 *
 * GoogleClosureCompiler APIのラッパークラス。
 * 任意のJavaScriptを圧縮、最適化を行うことで、実行速度を上げつつ転送量を削減できる。
 *
 * example.<code>
 *    uselib('Google/ClosureCompiler');
 *
 *    $gcc = new GoogleClosureCompiler();
 *
 *    if( $gcc->request('(function(){alert(\'Hello\');})();') ){
 *        echo $gcc->getSourceLastRequest();
 *    }
 *    else
 *        var_dump( $gcc->getErrorMessageLastRequest() );
 * </code>
 *
 * @package    GoogleClosureCompiler
 * @copyright  2012 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class GoogleClosureCompiler{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	private $base_url = 'http://closure-compiler.appspot.com/compile';
	private $use_curl = true;			//cURL利用フラグ
	private $method   = 'POST';			//APIが原則POSTのみのようなので現状GETは考慮しない。
										//   Overview Line 1,  "you must send an HTTP POST request"

	//ClosureCompilerオプション
	private $compilation_level = 'SIMPLE_OPTIMIZATIONS';			//最適化レベル(WHITESPACE_ONLY | SIMPLE_OPTIMIZATIONS | ADVANCED_OPTIMIZATIONS)
	private $output_format     = 'json';							//出力形式(xml | json | text)
	private $output_info       = array('compiled_code', 'errors');	//出力情報(compiled_code | warnings | errors | statistics)

	//処理用
	private $result = null;


	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @link   https://developers.google.com/closure/compiler/docs/api-ref?hl=ja
	 */
	function __construct(){
		;
	}


	//--------------------------------------------
	// Public
	//--------------------------------------------

	/**
	 * APIへリクエストし結果返却
	 *
	 * @param  string  $code   最適化を行いたいJavaScriptのコード
	 * @return array   ClosureCompilerからの戻り値をjson_decodeし返却する
	 *                 リクエスト自体が失敗している場合はfalse
	 * @link   https://developers.google.com/closure/compiler/docs/api-ref?hl=ja
	 * @access public
	 */
	function request($code){
		$url   = $this->base_url;					//endpoint
		$param = $this->_makePostParam($code);		//クエリー作成

		//----------------------------
		//リクエスト
		//----------------------------
		if( $this->use_curl ){
			//cURL
			$ret = net_fetchUrl(
							  $url
							, array(
								  CURLOPT_POST       => 1
								, CURLOPT_POSTFIELDS => $param
							)
					);
		}
		else{
			//file_get_contents
			$ret = @file_get_contents($url, false
								, net_getContext(array(
										  'method' => 'POST'
										, 'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
														.  "Content-Length: ". strlen($param)
										, 'content' => $param
									)
								)
					);
		}
		
		//結果返却
		if($ret === false ){
			return( false );
		}
		else{
			$result       = json_decode($ret, true);
			$this->result = $result;
		
			if( $this->isErrorLastRequest() )
				return( false );
			else
				return( true );
		}
	}


	/**
	 * 最適化レベル変更
	 *
	 * @param  string  $level 最適化レベル(WHITESPACE_ONLY | SIMPLE_OPTIMIZATIONS | ADVANCED_OPTIMIZATIONS)
	 * @return bool    変更が成功したらtrue, 失敗したらfalse
	 * @access public
	 */	
	function setCompilationLevel($level){
		if( preg_match('/^(WHITESPACE_ONLY|SIMPLE_OPTIMIZATIONS|ADVANCED_OPTIMIZATIONS)$/', $level) === 1 ){
			$this->compilation_level = $level;
			return(true);
		}
		else{
			return(false);
		}
	}

	/**
	 * 最適化レベル取得
	 *
	 * @return string  現在の最適化レベル
	 * @access public
	 */	
	function getCompilationLevel(){
		return( $this->compilation_level );
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


	/**
	 * エラー判定
	 *
	 * 最後に送信したリクエストがエラーだったか判定する。
	 * 
	 * 未リクエスト時に実行した場合は true が返る。
	 * compiledCodeがレスポンスに含まれていない場合もエラーとなる。
	 *
	 * @return bool
	 * @access public
	 */	
	function isErrorLastRequest(){
		$result = $this->result;

		if( array_key_exists('errors', $result)  ||  !array_key_exists('compiledCode', $result) )
			return(true);
		else
			return(false);
	}

	/**
	 * エラーメッセージを返却
	 *
	 * 最後に送信したリクエストがエラーだった場合、
	 * そのエラーメッセージが含まれる配列を返却する。
	 *
	 * @return mixed   エラー時：エラーメッセージが含まれる配列
	 *                   成功時：false
	 *                   その他：null
	 * @access public
	 */	
	function getErrorMessageLastRequest(){
		$result = $this->result;

		if( !$this->isErrorLastRequest() ){
			return(false);
		}
		else if( array_key_exists('errors', $result) ){
			return($result['errors']);
		}
		else{
			return(null);
		}
	}

	/**
	 * 最適化後のソースコード取得
	 *
	 * 最後に送信したリクエストのソースコードを取得する
	 * 以下の場合には false を返却する。
	 *  - 未送信時
	 *  - エラーだった場合
	 *  - compiledCodeがレスポンス内に含まれていない場合
	 *
	 * @return string|bool 成功時：ソースコード, エラー時:false
	 * @access public
	 */
	function getSourceLastRequest(){
		$result = $this->result;

		if( $result !== null &&  ! $this->isErrorLastRequest() ){
			return($result['compiledCode']);
		}
		else{
			return(false);
		}
	}

	//--------------------------------------------
	// Private
	//--------------------------------------------	
	/**
	 * リクエスト用パラメータ作成
	 *
	 * @param  string  $code   最適化を行いたいJavaScriptのコード
	 * @return string  文字列
	 * @access public
	 */
	private function _makePostParam($code){
		//output_infoは複数指定するので特処理
		$output_info = implode('&', array_map(function($s){return('output_info='.$s);}, $this->output_info) );
	
		return(
			implode('&', array(
				  'js_code='.           urlencode($code)
				, 'compilation_level='. $this->compilation_level
				, 'output_format='.     $this->output_format
				, $output_info
			))
		);
	}
}
?>