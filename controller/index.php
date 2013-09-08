<?php
/* [WingPHP]
 *  - IndexController
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
 * IndexControllerクラス
 * 
 * サイト総合トップ
 *
 * @package    IndexController
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class IndexController extends BaseController{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------

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
	 *--------------------------------------------*/
	/**
	 * トップ表示
	 *
	 * @access public
	 */
	public function index(){
		$this->display('index.html');
	}

	 
	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - 
	 *--------------------------------------------*/
}
