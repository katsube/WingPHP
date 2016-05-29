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

	public function location1(){
		location('/tests/msg/location1');
	}


	public function msg($arg){
		$str = $arg[0];
		echo $str;
	}
	 
	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _foobar
	 *--------------------------------------------*/
	/**
	 * _foobar
	 *
	 * [[_foobar method description.]]
	 *
	 * @param  string   $hoge
	 * @return void
	 * @access private
	 */
	public function _foobar(){
		;
	}
}