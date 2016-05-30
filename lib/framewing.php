<?php
/* [WingPHP]
 *  - framewing class
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
 * framewingクラス
 *
 * リクエストURLを解析し指定コントローラーを実行する。
 *
 * @package    framewing
 * @copyright  2013 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class framewing{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	public $ctrl_name   = '';
	public $method_name = '';
	public $param = array();
	
	private $static_file = '';


	//--------------------------------------------
	// コンストラクタ
	//--------------------------------------------
	function __construct(){
		//パース
		$this->_parse();

		// ルーティング
		$rt = new Routing($this);
		$this->ctrl_name   = $rt->ctrl;
		$this->method_name = $rt->method;
		$this->param       = $rt->param;
	}

	//--------------------------------------------
	// デストラクタ
	//--------------------------------------------
	function __destruct(){
		;
	}

	/*--------------------------------------------
	 * ■ Public ■
	 *--------------------------------------------
	 * - go
	 *--------------------------------------------*/
	/**
	 * コントローラーを実行する
	 *
	 * コントローラーとして指定されたクラス(メソッド)を実行する。
	 * 存在しない、何らかの理由により実行できない場合はerrorコントローラーに
	 * リダイレクトする。
	 *
	 * @return integer  200  Controllerを呼び出し
	 *                  201  SmartyDirect
	 *                  404  ファイルが存在しない
	 * @access public
	 */
	public function go(){
		$ctrl = $this->ctrl_name;
		$mthd = $this->method_name;

		//-----------------------------
		// class, methodが存在する
		//-----------------------------
		if( is_callable(array($ctrl, $mthd))								//存在チェック
						&& (strcmp('BaseController', $ctrl) != 0)			//スーパークラスは直接実行しない
						&& !preg_match('/^_/', $mthd)){						//先頭が _ で始まるメソッドは実行しない

			$obj = new $ctrl();
			call_user_func(array($obj, $mthd), $this->param);
		
			return(200);
		}
		//-----------------------------
		// viewが存在する
		//-----------------------------
		else if( $this->_exists_view() ){
			$ctrl = new BaseController();
			$ctrl->display($this->static_file);

			return(201);
		}
		//-----------------------------
		// 404
		//-----------------------------
		else{
			http_error(404);

			return(404);
		}
	}

	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _parse
	 * - _exists_view
	 *--------------------------------------------*/
	/**
	 * リクエストURLをパースする
	 *
	 * リクエストされたURLを '/' で分割し、
	 * 　- クラス
	 *   - メソッド
	 *   - 引数
	 * に分割する。
	 *
	 * @return void
	 * @access private
	 */
	private function _parse(){
		//-----------------------
		// パース
		//-----------------------
		if( array_key_exists('_q', $_REQUEST) ){
			$query = $_REQUEST['_q'];
			$arr   = explode('/', $query);

			// _qの冒頭に'/'がある場合、先頭の空配列を削除
			if(count($arr)>1 && empty($arr[0]))
				 array_splice($arr, 0, 1);
		}
		else{
			$arr = array();
		}

		//-----------------------
		// 実行内容確定
		//-----------------------
		//コントローラー
		if(!empty($arr[0]))
			$this->ctrl_name = ucfirst($arr[0]) . 'Controller';
		else
			$this->ctrl_name = 'IndexController';

		//メソッド
		if(!empty($arr[1]))
			$this->method_name = $arr[1];
		else
			$this->method_name = 'index';

		//パラメーター
		if(count($arr) > 1)
			$this->param = array_slice($arr, 2);
	}

	
	/**
	 * 指定されたViewが存在するかチェック
	 *
	 * @return boolean
	 * @access private
	 */
	private function _exists_view(){
		global $Conf;
		if(!$Conf['SmartyDirect']['run']){
			return(false);
		}
		
		//-----------------------
		//パス作成
		//-----------------------
		$tmpl_dir = $Conf['Smarty']['tmpl'];
		$path     = $Conf['SmartyDirect']['root'] .'/'. $_REQUEST['_q'];
		
		//ファイル名未指定の場合はdefaultIndexを付加
		if( preg_match('/\/$/', $path) )
			$path .= $Conf['SmartyDirect']['default'];

		//-----------------------
		//汚染チェック
		//-----------------------
		if(!preg_match('/^([a-zA-Z0-9_\.\-\/]{1,})$/', $path)){
			return(false);
		}

		//-----------------------
		//存在確認
		//-----------------------
		if(is_file($tmpl_dir.'/'.$path)){
			$this->static_file = $path;
			return(true);
		}
		
		return(false);
	}
	
}
