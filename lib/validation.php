<?php
/* [WingPHP]
 *  - validation class
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

class validation{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	private $regexp;
	
	//--------------------------------------------
	// コンストラクタ
	//--------------------------------------------
	function __construct(){
		//正規表現をセット
		$this->regexp = $this->_setreg();
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
	 * - check
	 *--------------------------------------------*/
	 
	//--------------------------------------------
	// チェック
	//--------------------------------------------
	public function check($target, $rule){
		$flag = true;
		$data = array();
	
		foreach($rule as $key => $value){
			$subject = $target[$key];
			$reg     = $value;
			
			//既存の正規表現か？
			if( (!preg_match('/^\//', $reg)) && $this->_regexists($reg) )
				$reg = $this->_getreg($reg);
			
			//チェック
			if(! preg_match($reg, $subject)){
				$data[$key] = false;
				$flag = false;
			}
			else{
				$data[$key] = true;
			}
		}
	
		return( array('result'=>$flag, 'data'=>$data) );
	}

	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _setreg
	 * - _regexists
	 * - _getreg
	 *--------------------------------------------*/
	private function _setreg(){
		uselib('Util/Regex');
		return(array(
			  'POST_CD' => Regex::POST
			, 'EMAIL'   => Regex::EMAIL
			, 'URL'     => Regex::URL
			, 'NUM'     => Regex::NUM
			, 'ALPHA'   => Regex::ALPHA
			, 'ALNUM'   => Regex::ALNUM
		));
	 }
	 
	private function _regexists($key){
		return( array_key_exists($key, $this->regexp) );
	}
	 
	private function _getreg($key){
		return( $this->regexp[$key] );
	}
	 
}
?>