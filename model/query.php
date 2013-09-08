<?php
/* [WingPHP]
 *  - QueryModel class
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
 * QueryModelクラス
 * 
 * クエリーをやりとりする。$_REQUESTのラッパーのようなもの。
 * 将来的な変更などを考えてこのクラス経由でクエリーの操作を行う
 * ことが望ましい。
 *
 * @package    QueryModel
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class QueryModel extends BaseModel{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	private $q;
	
	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	function __construct(){
		$this->q = $_REQUEST;
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
	 * - data
	 *--------------------------------------------*/

	/**
	 * クエリー返却
	 *
	 * 指定されたクエリーを返却する。
	 * 存在しない場合はfalseを、未指定の場合は全ての
	 * クエリーを返却する。
	 *
	 * @param  string  $name
	 * @return mixed
	 * @access public
	 */
	public function data($name=false){
		if(! $name )
			return( $this->q );
		else
			if(array_key_exists($name, $this->q))
				return( $this->q[$name] );
			else
				return( false );
	}

	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - 
	 *--------------------------------------------*/
}
