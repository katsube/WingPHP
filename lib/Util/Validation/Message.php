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
				  'url'   => 'がURLの書式ではありません'
				, 'email' => 'がメールアドレスの書式ではありません'
				, 'ip4'   => 'がIPアドレスの書式ではありません'
				, 'post'  => 'が郵便番号の書式ではありません'
				, 'post7' => 'が郵便番号の書式ではありません'
				, 'tel'   => 'が電話番号の書式ではありません'
				, 'num'   => 'が半角の数字ではありません'
				, 'alpha' => 'が半角の英字ではありません'
				, 'alnum' => 'が半角の英数字ではありません'

				, 'require' => 'が入力されていません'
				, 'maxlen'  => '最大文字数を超過しています'
				, 'minlen'  => '最小文字数に達していません'
			)
	);


	public function setLanguage($lang){
		if( array_key_exists($lang, $error) ){
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
