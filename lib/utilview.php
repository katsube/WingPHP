<?php
/* [WingPHP]
 *  - utilview class
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
class utilview{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	private $flag_runvalid;
	
	//--------------------------------------------
	// コンストラクタ
	//--------------------------------------------
	function __construct(){
		$this->flag_runvalid = false;
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
	 * - run_valid
	 * - is_error
	 *--------------------------------------------*/	 
	//--------------------------------------------
	// validation実行した
	//--------------------------------------------
	public function run_valid(){
		$this->flag_runvalid = true;
	}

	//--------------------------------------------
	// エラーか確認(validation)
	//--------------------------------------------
	public function is_error($val){
		if($this->flag_runvalid && !$val)
			return(true);
		else
			return(false);
	}

	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - 
	 *--------------------------------------------*/
	 
}
?>