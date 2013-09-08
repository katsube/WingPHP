<?php
/* [WingPHP]
 *  - SessionModel class
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
 * SessionModelクラス
 * 
 * セッション値をやりとりする。$_SESSIONのラッパーのようなもの。
 * 将来的な変更などを考えてこのクラス経由でデータの操作を行う
 * ことが望ましい。
 *
 * @package    QueryModel
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class SessionModel extends BaseModel{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	function __construct($name=null){
		global $Conf;

		//セッション名を設定
		if($name === null)
			$name = $Conf['Session']['name'];
		session_name($name);
		
		//セッションスタート
		$ret = session_start();
		if($ret === false)
			die();
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
	 * - get
	 * - set
	 * - del
	 * - exists
	 * - destroy
	 *--------------------------------------------*/

	/**
	 * セッション値の取得
	 *
	 * 指定されたセッション値を返却する。
	 * 存在しない場合は null を返却する。
	 *
	 * @param  string  $name
	 * @return mixed
	 * @access public
	 */
	public function get($name){
		if(array_key_exists($name, $_SESSION))
			return( $_SESSION[$name] );
		else
			return( null );
	}

	/**
	 * セッション値を保存する
	 *
	 * 指定されたセッション値を保存する。すでに存在する場合は
	 * 上書きされる。第一引数に連想配列がセットされた場合は、
	 * 一度にセットされる。
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @access public
	 */
	public function set($name, $value=null){
		if(is_array($name))
			foreach($name as $key => $val)
				$_SESSION[$key] = $val;
		else
			$_SESSION[$name] = $value;
	}

	/**
	 * セッション値を削除する
	 *
	 * 指定されたセッション値を削除する。
	 * 存在しない場合は何もしない。
	 *
	 * @param  string  $name
	 * @access public
	 */
	public function del($name){
		if( array_key_exists($name, $_SESSION))
			unset($_SESSION[$name]);
	}
	
	/**
	 * セッション名が存在するか確認する
	 *
	 * 指定されたセッション名が存在するか確認する
	 *
	 * @param  string  $name
	 * @return bool
	 * @access public
	 */
	public function exists($name){
		return( array_key_exists($name, $_SESSION) );
	}
	
	
	//--------------------------------------------
	// セッションを破棄
	//--------------------------------------------
	/**
	 * セッションを破棄する
	 *
	 * @access public
	 */
	public function destroy(){
		if( session_id() !== "" )
			session_destroy();
		
		$_SESSION = array();
	}
	

	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - 
	 *--------------------------------------------*/
}
?>