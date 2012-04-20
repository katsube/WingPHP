<?php
/* [WingPHP]
 *  - lib/basicauth.php
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
 * BASIC認証クラス
 *
 * シンプルなBASIC認証機能を提供する。
 * アカウント情報はコンストラクタに配列形式で渡すか、無指定の場合は
 * $Conf['Account']ががそのまま利用される。
 *
 * example.<code>
 *    uselib('basicauth');
 *    $ba = new BasicAuth();
 *    $ba->auth();
 *
 *    $ba = new BasicAuth(array(
 *                 'id1' => 'password'
 *               , 'id2' => 'password'
 *          ));
 *    $ba->setMessage('入力されたアカウントが正しくないようです。');
 *    $ba->auth();
 * </code>
 *
 * @package    BasicAuth
 * @copyright  2012 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class BasicAuth{
	private $realm   = 'Please input your id and password';
	private $msg     = '<h2>403 Forbidden</h2><p>You don\'t have permission to access the requested page.</p>';
	private $account = null;

	/**
	 * コンストラクタ
	 *
	 * @param  array  $account 認証に利用するアカウント情報。 array('id'=>array('pw'=>'abcde'), ... )
	 * @return void
	 * @access public
	 */	
	function __construct($account=null){
		if( $account === null ){
			global $Conf;
			$this->account = $Conf['Account'];
		}
		else{
			$this->account = $account;
		}
	}



	/**
	 * BASIC認証を実施
	 *
	 * @param  bool   $died 認証失敗時にdieするか。true=die, false=return
	 * @return bool   認証成功時=true, 認証失敗時=false or die
	 * @access public
	 */	
	public function auth($died=true){
		if( isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) ){
			$user = $_SERVER['PHP_AUTH_USER'];
			$pw   = $_SERVER['PHP_AUTH_PW'];
		
			if( $this->_check($user, $pw) )
				return($user);
		}
	
		$this->_header();

		if($died)
			die($this->msg);
		else
			return(false);
	}

	/**
	 * Realmをセット
	 *
	 * @param  string  $realm
	 * @return void
	 * @access public
	 */	
	public function setRealm($realm){
		$this->realm = $realm;
	}

	/**
	 * 認証失敗時に表示する文字列をセット
	 *
	 * @param  string  $msg
	 * @return void
	 * @access public
	 */	
	public function setMessage($msg){
		$this->msg = $msg;
	}



	/**
	 * 認証ヘッダーを出力
	 *
	 * @return void
	 * @access private
	 */
	private function _header(){
		$realm = $this->realm;
	
		header('WWW-Authenticate: Basic realm="'.$realm.'"');
		header('HTTP/1.0 401 Unauthorized');
		header('Content-type: text/html; charset='.mb_internal_encoding());
	}

	/**
	 * ID,PWが正しいか突き合わせる
	 *
	 * @param  string $id
	 * @param  string $pw
	 * @return bool  認証成功=true, 認証失敗=false
	 * @access private
	 */	
	private function _check($id, $pw){
		$account = $this->account;
		if( array_key_exists($id, $account) )
			return( $account[$id]['pw'] === $pw );
	
		return(false);
	}
}
?>