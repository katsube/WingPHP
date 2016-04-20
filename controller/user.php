<?php
/**
 * UserController Class
 *
 * [[[ description here ]]]
 *
 * @package    UserController
 * @copyright  2016 WinningSection. Powerd by WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class UserController extends BaseController{
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
		$this->layout('layout/base.html');
		$this->display('index.html');
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