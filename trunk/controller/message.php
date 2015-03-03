<?php
/* [WingPHP]
 *  - MessageController
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
 * MessageControllerクラス
 *
 * 汎用エラー表示クラス。
 * WingPHPでは何らかのエラーが生じた際に、特定のURLへ遷移させる
 * ことで処理を簡素化させている。
 *
 * ※以前はErrorControllerとしていたが、
 *   汎用性とApacheのデフォルト設定との衝突を避けるため名称変更。
 *
 * Example.
 *   404 Not Found → /message/error/404/[リクエストURI]
 *   共通エラー    → /message/error/[リクエストURI]
 *
 * @package    MessageController
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class MessageController extends BaseController{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	private $exts = array(				//HTML出力対象の拡張子
						''
						, 'html', 'htm'
						, 'txt'
						, 'css', 'js'
					);

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	function __construct(){
		parent::__construct();
	}

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	function __destruct(){
		;
	}

	/*--------------------------------------------
	 * ■ Public ■
	 *--------------------------------------------
	 * - index
	 * - msg
	 *--------------------------------------------*/
	/**
	 * エラートップ
	 *
	 * @access public
	 */
	public function index(){
		location('/message/error/common');
	}

	/**
	 * エラー表示
	 *
	 * @param  array   $argv   $argv[0] ... 404|common
	 * @access public
	 */
	public function error($argv){
		$code    = $argv[0];
		$org_url = '/' . implode('/', array_slice($argv, 1));		//アクセス元URL
		$org_ext = pathinfo($org_url, PATHINFO_EXTENSION);			//↑その拡張子

		$file   = '';
		$status = '';
		switch($code){
			case '404':
				$file   = 'message/error/404.html';
				$status = '404 Not Found';
				break;

			default:
				$file   = 'message/error/common.html';
				$status = '500 Internal Server Error';
				break;
		}

		header("HTTP/1.1 $status");
		if( $this->_isPutHTML($org_ext) ){
			$this->assign('request_url', $org_url);
			$this->display($file);
		}
	}

	/* ToDo:
	 * public function setExts(){
	 * }
	 * public function addExts(){
	 * }
	 * public function getExts(){
	 * }
	 * public function delExts(){
	 * }
	 */

	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _isPutHTML
	 *--------------------------------------------*/
	/**
	 * HTML出力判定
	 *
	 * 指定された拡張子でHTMLを出力する可の判定を行う。
	 * クエリーanに1がセットされている場合は強制的に行う。
	 *
	 * @param  string   $ext   拡張子
	 * @access public
	 */
	 private function _isPutHTML($ext){
		$q = new QueryModel();
		return(
			$q->an == 1 || in_array($ext, $this->exts)
		);
	}

}
