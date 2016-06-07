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
 * validation時に利用するメッセージ管理クラス。
 *
 * example.<code>
 *     uselib('Util/Validation/Message');
 *
 *     $vmsg = new ValidationMessage();
 *     $vmsg->setLanguage('ja');
 *     $vmsg->set($cd, $message);                // メッセージを新規作成or上書き
 *
 *     $msg  = $vmsg->get($cd);                  // メッセージ取得
 *     $msgs = $vmsg->gets(array('url', $cd));   // メッセージまとめて取得
 * </code>
 *
 * @package    ValidationMessage
 * @copyright  2013 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class ValidationMessage{
	//---------------------------------------------
	// メンバ変数
	//---------------------------------------------
	private $lang = 'ja';

	//ToDo: これも気持ち悪いので別ファイルに分離したい。
	private $msg  = array(
		'ja' => array(
			  'url'    => 'URLの書式ではありません'
			, 'email'  => 'メールアドレスの書式ではありません'
			, 'ip4'    => 'IPアドレスの書式ではありません'
			, 'postcd' => '郵便番号の書式ではありません'
			, 'tel'    => '電話番号の書式ではありません'
			, 'num'    => '半角の数字ではありません'
			, 'alpha'  => '半角の英字ではありません'
			, 'alnum'  => '半角の英数字ではありません'

			, 'require' => '必須項目が入力されていません'
			, 'bytemax' => '最大byte数を超過しています'
			, 'bytemin' => '最小byte数に達していません'
			, 'max'     => '最大値を超過しています'
			, 'min'     => '最小値に達していません'

			, 'match' => '内容が一致しません'
			, 'eq'    => '内容が一致しません'
			, 'ne'    => '内容が一致しません'
			, 'in'    => '内容が一致しません'

			, 'date' => '有効な日付ではありません'
			, 'time' => '有効な時間ではありません'

			, 'grequire1' => '最低でも1つの入力が必要です'
			, 'gin' => '内容が一致しません'

			, '_404' => ''
		)
	);

	/**
	 * コンストラクタ
	 *
	 * @return void
	 * @access public
	 */
	function __construct(){
		;
	}


	/**
	 * メッセージを取得する
	 *
	 * メッセージを取得する。
	 * $cdを未指定の場合はすべての、
	 * $cdを指定した場合は該当する項目を返却する。
	 *
	 * @param  string $cd メッセージCD
	 * @return string $cd未指定:すべてのメッセージ, $cd指定時:個別, $cd指定時未存在:null
	 * @access public
	 */
	public function get($cd=null){
		$lang = $this->lang;

		if( $cd === null ){
			return( $this->msg[$lang] );
		}
		else if( array_key_exists($cd, $this->msg[$lang]) ){
			return( $this->msg[$lang][$cd] );
		}
		else{
			return(null);
		}
	}

	/**
	 * メッセージをまとめて取得する
	 *
	 * メッセージをまとめて取得する。
	 * 第一引数$cd配列内で指定された項目を配列として返却する。
	 *
	 * @param  string $cd メッセージCD
	 * @return string $cd未指定:すべてのメッセージ, $cd指定時:個別, $cd指定時未存在:null
	 * @access public
	 */
	public function gets($cds){
		$result = array();

		$len = count($cds);
		for($i=0; $i<$len; $i++){
			$cd     = $cds[$i];
			$result = array_merge($result, array($cd => $this->get($cd)));
		}

		return($result);
	}

	/**
	 * メッセージをセットする
	 *
	 * メッセージをオブジェクト内にセットする。
	 * 同一の$CDが存在する場合は上書きされる。
	 *
	 * @param  string $cd  メッセージCD
	 * @param  string $msg メッセージ内容
	 * @return void
	 * @access public
	 */
	public function set($cd, $msg){
		$lang = $this->lang;
		$this->msg[$lang][$cd] = $msg;
	}

	/**
	 * 言語種別をセットする
	 *
	 * どの言語を使用するかをオブジェクト内にセットする。
	 *
	 * @param  string $lang  言語CD
	 * @return void
	 * @access public
	 */
	public function setLanguage($lang){
		if( array_key_exists($lang, $this->msg) ){
			$this->lang = $lang;
			return(true);
		}
		else{
			return(false);
		}
	}
}

