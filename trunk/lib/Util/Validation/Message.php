<?php
/* [WingPHP]
 *  - lib/Util/Validation/Message.php
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
 * ValidationMessageクラス
 * 
 * validation実行後に表示するメッセージクラス。
 * 
 * example.<code>
 *     uselib('Util/Validation/Message');
 *     
 * </code>
 *
 * @package    ValidationMessage
 * @copyright  2013 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class ValidationMessage{
	private $lang  = 'ja';
	private $error = array(
			'ja' => array(
				  'url'   => 'URLの書式ではありません'
				, 'email' => 'メールアドレスの書式ではありません'
				, 'ip4'   => 'IPアドレスの書式ではありません'
				, 'post'  => '郵便番号の書式ではありません'
				, 'post7' => '郵便番号の書式ではありません'
				, 'tel'   => '電話番号の書式ではありません'
				, 'num'   => '半角の数字ではありません'
				, 'alpha' => '半角の英字ではありません'
				, 'alnum' => '半角の英数字ではありません'

				, 'require' => '必須項目が入力されていません'
				, 'bytemax' => '最大byte数を超過しています'
				, 'bytemin' => '最小byte数に達していません'
				, 'max'     => '最大値を超過しています'
				, 'min'     => '最小値に達していません'
				
				, 'match' => '内容が一致しません'
				, 'eq'    => '内容が一致しません'
				, 'ne'    => '内容が一致しません'
				, 'in'    => '内容が一致しません'
			)
	);


	public function setLanguage($lang){
		if( array_key_exists($lang, $this->error) ){
			$this->lang = $lang;
			return(true);
		}
		else{
			return(false);
		}
	}

	public function get($cd=null){
		$lang = $this->lang;

		if( $cd === null ){
			return( $this->error[$lang] );
		}
		else if( array_key_exists($cd, $this->error[$lang]) ){
			return( $this->error[$lang][$cd] );
		}
		else{
			return(null);
		}
	}

	public function set($cd, $msg){
		$lang = $this->lang;
		$this->error[$lang][$cd] = $msg;
	}
}
