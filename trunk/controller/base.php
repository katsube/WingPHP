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

class BaseController{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	private $smarty   = false;
	private $utilview = null;
	private $run_validation = false;
	
	//--------------------------------------------
	// コンストラクタ
	//--------------------------------------------
	function __construct(){
		$this->utilview = new utilview();
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
	 * - smarty
	 * - location
	 * - check
	 * - sendmail
	 *--------------------------------------------*/
	//viewはSmartyの機能をそのまま利用
	public function smarty(){
		if(!$this->smarty){
			$this->_setSmarty();
		}
		
		return($this->smarty);
	}
	
	//移動
	public function location($url){
		$head = sprintf('Location: %s', $url);
		header($head);
	}

	//validation
	public function check($data, $rule){
		$v = new validation();
		$this->utilview->run_valid();
		
		return( $v->check($data, $rule) );		//{ 'result'=>true|false, 'data'=>{'name1'=>true|false, 'name2'=>true|false, ...} }
	}
	
	//メール送信
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