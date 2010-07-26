<?php
/* [WingPHP]
 *  - BaseController
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
 * BaseControllerクラス
 * 
 * 各コントローラーのスーパークラス。
 * 以下のような処理を受け持つ。
 *  - ビュー周りの処理
 *  - リダイレクトなどHTTPヘッダ周り
 *  - バリデーション
 *  - メール送信
 *
 * @package    BaseController
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class BaseController{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	private $smarty   = false;
	private $utilview = null;
	private $run_validation = false;
	
	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	function __construct(){
		$this->utilview = new utilview();
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
	 * - smarty
	 * - location
	 * - check
	 * - sendmail
	 *--------------------------------------------*/
	/**
	 * view用のSmartyオブジェクトを返却
	 *
	 * @return mixed Smartyオブジェクト
	 * @access public
	 */
	public function smarty(){
		if(!$this->smarty){
			$this->_setSmarty();
		}
		
		return($this->smarty);
	}
	
	/**
	 * 指定URL(パス)へ遷移する 
	 *
	 * HTTPヘッダがまだ送信されていない場合はLocationヘッダで、
	 * すでに送信されている場合はmeta要素を出力する
	 *
	 * @param  string  $url   URL(パス)
	 * @param  int     $sec   遷移までの秒数(meta出力時のみ有効)
	 * @access public
	 */
	public function location($url, $sec=0){
		if( ! headers_sent() ){
			$head = sprintf('Location: %s', $url);
			header($head);
		}
		else{
			$meta = sprintf('<meta http-equiv="refresh" content="%d;url=%s">', $sec, $url);
			echo $meta;
		}
	}

	/**
	 * バリデーションを行う 
	 *
	 * key-value型の配列のvalueに対してバリデーションを実施する。
	 * 第二引数 $ruleも同様にkey-value型の配列であり、このvalueにルールを
	 * 記述する。
	 *   ※つまり第一引数と第二引数のキーはリンクしている
	 *
	 * Example. <code>
	 *   $q   = new QueryModel();
	 *   $ret = $this->check(
	 *               $q->data()
	 *             , array('key1'=>'ALNUM', 'key2'=>'/^([0-9a-zA-Z]{1,})$/')
	 *          );
	 *
	 *   $this->smarty()->assign('validation', $ret);
	 * </code>
	 *
	 * @param  array  $data  array(key1=>'value1', key2=>'value2' ...)
	 * @param  array  $rule  array(key1=>'ALNUM',  key2=>'/^([0-9a-zA-Z]{1,})$/' ...)
	 * @return array  array( result=>true|false, data=>array( key1=>true|false, key2=>true|false ... ) )
	 * @access public
	 */
	public function check($data, $rule){
		$v = new validation();				// lib/validation.php
		$this->utilview->run_valid();
		
		return( $v->check($data, $rule) );
	}


	/**
	 * メール送信ラッパー
	 *
	 * mb_send_mailの簡易ラッパー。将来的にもろもろ拡張予定。
	 *
	 * Example. <code>
	 *   $this->sendmail(array(
	 *        'subject' => 'メールの件名'
	 *      , 'from'    => 'from@example.com'
	 *      , 'to'      => 'to@example.com'
	 *      , 'body'    => $body
	 *	));
	 * </code>
	 *
	 * @param  array  $data  array('subject'=>'件名', 'from'=>'送信者', 'to'=>'宛先', 'body'=>'本文')
	 * @return bool
	 * @access public
	 */
	public function sendmail($data){
		$subject = $data['subject'];
		$from    = $data['from'];
		$to      = $data['to'];
		$body    = $data['body'];
		
		//ヘッダー作成
		$header = sprintf('From: %s', $from);
		
		//送信
		return(
			mb_send_mail($to, $subject, $body, $header)
		);
	}
	
	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _setSmarty
	 *--------------------------------------------*/

	/**
	 * Smartyインスタンスを作成、設定を行う 
	 *
	 * Smartyインスタンスを作成し、$GLOBALS['Conf']の内容に
	 * したがって各種設定を反映する。
	 *
	 * @access private
	 */
	private function _setSmarty(){
		global $Conf;
		$smarty = new Smarty;

		//ディレクトリ設定
		$smarty->template_dir = $Conf['Smarty']['tmpl'];
		$smarty->compile_dir  = $Conf['Smarty']['tmpl_c'];
		$smarty->config_dir   = $Conf['Smarty']['config'];
		$smarty->cache_dir    = $Conf['Smarty']['cache'];
		
		//キャッシュ
		$smarty->caching = $Conf['Smarty']['is_cache'];
		$smarty->cache_lifetime = $Conf['Smarty']['cache_life'];
		
		//便利関数
		$smarty->assign('view', $this->utilview);

		//セット
		$this->smarty = $smarty;
	}
}
?>