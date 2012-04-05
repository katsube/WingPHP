<?php
/* [WingPHP]
 *  - BaseFeather
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
 * BaseFeatherクラス
 * 
 * 各Featherのスーパークラス。
 *
 * @package    BaseFeather
 * @copyright  2012 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class BaseFeather{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	//インスタンス格納用
	private $model = null;
	private $ctrl  = null;

	//メソッド一覧
	private $methods = null;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	function __construct(){
		;
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
	 * - __call
	 *--------------------------------------------*/

	/**
	 * Model,Controllerのメソッド実行
	 *
	 * @param  string メソッド名
	 * @oaram  array  パラメーター
	 * @return mixed  メソッドの実行結果
	 * @access public
	 */
	function __call($name, $param){
		//------------------------
		//初期化
		//------------------------
		if( $this->model === null || $this->ctrl === null )
			$this->_init();
	
		//------------------------
		//メソッド呼出し
		//------------------------
		if( array_key_exists($name, $this->methods) ){
			//Controller
			if($this->methods[$name] === 'c')
				return( call_user_func_array(array($this->ctrl, $name), $param) );
			//Model
			else if($this->methods[$name] === 'm')
				return( call_user_func_array(array($this->model, $name), $param) );
		}
		else{
			die();
		}
	}


	/**
	 * クラス変数の初期化
	 *
	 * インスタンス生成、method一覧を作成する
	 *
	 * @return void
	 * @access private
	 */
	private function _init(){
		//------------------------
		//インスタンス生成
		//------------------------
		$this->model = new BaseModel();
		$this->ctrl  = new BaseController();
	
		//------------------------
		//method一覧
		//------------------------
		$this->methods = array(
			//Model
			  'usedb'            => 'm'
			, 'select'           => 'm'
			, 'select1'          => 'm'
			, 'exec'             => 'm'
			, 'beginTransaction' => 'm'
			, 'commit'           => 'm'
			, 'rollback'         => 'm'

			//Controller
			, 'smarty'           => 'c'
			, 'layout'           => 'c'
			, 'assign'           => 'c'
			, 'display'          => 'c'
			, 'location'         => 'c'
			, 'check'            => 'c'
			, 'sendmail'         => 'c'
		);
	}
}
?>