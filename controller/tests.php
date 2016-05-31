<?php
/**
 * TestsController Class
 *
 * ユニット/機能 テスト用コントローラー
 * プロダクション環境にはデプロイしないか、何かしらアクセス制限をかけてください。
 * 
 * @package    TestsController
 * @copyright  2016 WinningSection. Powerd by WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class TestsController extends BaseController{
	/**
	 * constructor
	 *
	 * @access public
	 */
	function __construct(){
		parent::__construct();
	}

	/**
	 * destructor
	 *
	 * @access public
	 */
	function __destruct(){
		parent::__destruct();
	}


	/*--------------------------------------------
	 * ■ Public ■
	 *--------------------------------------------
	 * - index
	 * - location1
	 * - msg
	 * - canaccess
	 * - cannotaccess
	 *--------------------------------------------*/
	/**
	 * index page
	 *
	 * display index page.
	 *
	 * @return void
	 * @access public
	 */
	public function index(){
		;
	}

	/**
	 * location1
	 *
	 * @see tests/unit/globalFunction.UnitTest.php
	 * @return void
	 * @access public
	 */
	public function location1(){
		location('/tests/msg/location1');
	}


	/**
	 * msg
	 *
	 * @see tests/unit/globalFunction.UnitTest.php
	 * @return void
	 * @access public
	 */
	public function msg($arg){
		$str = $arg[0];
		echo $str;
	}

	/**
	 * Can Access
	 *
	 * @return void
	 * @access public
	 */
	public function canaccess(){
		;
	}

	/**
	 * Can **not** Access
	 *
	 * @return void
	 * @access public
	 */
	public function _cannotaccess(){
		;
	}
	 
	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _foobar
	 *--------------------------------------------*/
	/**
	 * foobar
	 *
	 * @param  void
	 * @return void
	 * @access private
	 */
	private function foobar(){
		;
	}
}