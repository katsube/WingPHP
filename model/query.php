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
	//---------------------------------------------
	// メンバ変数
	//---------------------------------------------
	private $method = null;			//'POST', 'GET', 'REQUEST'
	private $q      = null;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	function __construct($method='REQUEST'){
		$this->method = $method;
		$this->_setTarget($method);
	}

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	function __destruct(){
		;
	}

	/**
	 * プロパティのオーバーライド
	 *
	 *「$q->foobar」は「$q->data('foobar')」 と同じ意味になる。
	 * 存在していないクエリーは false が返る。
	 *   ※呼び出し元で何度も利用する場合は、ローカル変数に一度代入し
	 *     利用しないとオーバーヘッドがかかる点に注意。
	 */
	function __get($name){
		if(array_key_exists($name, $this->q))
			return($this->q[$name]);
		else
			return(false);
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
	 * 存在しない場合はfalseを、未指定の場合は全てのクエリーを返却する。
	 *
	 * @param  mixed  $name  単体のクエリー名
	 *                       または配列(['name1', 'name2'...])で複数の指定が可
	 * @return mixed
	 * @access public
	 */
	public function data($name=null){
		return(
			$this->_getValue($name)
		);
	}



	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _getValue
	 * - _setTarget
	 *--------------------------------------------*/
	/**
	 * クエリー返却(実作業)
	 *
	 * 指定されたクエリーを返却する。
	 * data,post,getの実作業を行う。
	 *
	 * @param  mixed  $name  単体のクエリー名
	 *                       または配列(['name1', 'name2'...])で複数の指定が可
	 * @return mixed
	 * @access private
	 */
	private function _getValue($name=null, $mode='request'){
		if( $name === null ){
			return( $this->q );
		}
		else if( is_array($name) ){
			$result = array();
			$len    = count($name);
			for($i=0; $i<$len; $i++){
				$key = $name[$i];
				if( array_key_exists($key, $this->q) ){
					$result[$key] = $this->q[$key];
				}
				else{
					$result[$key] = false;
				}
			}

			return($result);
		}
		else{
			if(array_key_exists($name, $this->q))
				return( $this->q[$name] );
			else
				return( false );
		}
	}

	/**
	 * 作業対象をセット(変更する)
	 *
	 * _getValueの作業対象を指定されたものに変更する。
	 *
	 * @param  string  $method GET,POST,REQUEST
	 * @return void
	 * @access private
	 */
	private function _setTarget($method){
		switch($method){
			case 'GET':
				$this->q = $_GET;
				break;

			case 'POST':
				$this->q = $_POST;
				break;

			case 'REQUEST':
			default:
				if($_SERVER['REQUEST_METHOD'] === 'POST')
					$this->q = $_POST;
				else
					$this->q = $_GET;
				break;
		}

		if(array_key_exists('_q', $this->q))
			unset($this->q['_q']);
	}
}
