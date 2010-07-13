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

class SessionModel extends BaseModel{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------

	//--------------------------------------------
	// コンストラクタ
	//--------------------------------------------
	function __construct(){
		;
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
	 * - get
	 * - set
	 * - del
	 * - exists
	 * - destroy
	 *--------------------------------------------*/

	//--------------------------------------------
	// 各データ処理
	//--------------------------------------------
	public function get($name){
		if(array_key_exists($name, $_SESSION))
			return( $_SESSION[$name] );
		else
			return( null );
	}

	public function set($name, $value){
		$_SESSION[$name] = $value;
	}

	public function del($name){
		unset($_SESSION[$name]);
	}
	
	public function exists($name){
		return( array_key_exists($name, $_SESSION) );
	}
	
	
	//--------------------------------------------
	// セッションを破棄
	//--------------------------------------------
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