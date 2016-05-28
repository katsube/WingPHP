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
	private $layout   = null;
	private $layout_marker = null;
	private $run_validation = false;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	function __construct(){
		;
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
	 * - layout
	 * - assign
	 * - display
	 * - caching
	 *--------------------------------------------*/
	/**
	 * view用のSmartyオブジェクトを返却
	 *
	 * @return mixed Smartyオブジェクト
	 * @access public
	 */
	public function smarty(){
		if(!$this->smarty)
			$this->_setSmarty();

		return($this->smarty);
	}

	/**
	 * view(Smarty)のレイアウトファイルを指定
	 *
	 * @param  string $file ファイルパス
	 * @access public
	 */
	public function layout($file, $marker='CONTENT'){
		$this->layout = $file;
		$this->layout_marker = $marker;
	}

	/**
	 * view(Smarty)に値をセットする
	 *
	 * @param  mixed   $key
	 * @param  string  $value
	 * @access public
	 */
	public function assign($key, $value=null){
		if(!$this->smarty)
			$this->_setSmarty();

		if(is_array($key))
			$this->smarty->assign($key);
		else
			$this->smarty->assign($key, $value);
	}

	/**
	 * viewを出力する
	 *
	 * @param  string  $file
	 * @access public
	 * @todo   キャッシュ関連の対応を行う。
	 */
	public function display($file){
		if(!$this->smarty)
			$this->_setSmarty();

		//layout使わない
		if($this->layout === null){
			$this->smarty->display($file);
		}
		//layout使う
		else{
			$this->smarty->assign(
					  $this->layout_marker
					, $this->smarty->fetch($file)
			);

			$this->smarty->display($this->layout);
		}
	}

	/**
	 * View(Smarty)のキャッシュ機能のON/OFF
	 *
	 * @param  boolean $flag
	 * @return boolean  true:設定成功
	 *                 false:設定失敗
	 * @access public
	 */
	public function caching($flag){
		if( !is_bool($flag) ){
			return(false);
		}
		else{
			if(!$this->smarty)
				$this->_setSmarty();
				
			$this->smarty->caching = ($flag ===true)?  1:0;
			return(true);
		}
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

		switch($Conf['Smarty']['version']){
			case '3.1':
				$smarty->plugins_dir = array_merge(array(realpath('../lib/Smarty/3.1/libs/plugins')), $Conf['Smarty']['plugin']);
				break;
			default:
				$smarty->plugins_dir = $Conf['Smarty']['plugin'];
				break;
		}

		//キャッシュ
		$smarty->caching = $Conf['Smarty']['is_cache'];
		$smarty->cache_lifetime = $Conf['Smarty']['cache_life'];

		//セット
		$this->smarty = $smarty;
	}
}
