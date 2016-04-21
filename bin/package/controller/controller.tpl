<?php
/**
 * {$name}Controller Class
 *
 * [[[ description here ]]]
 *
 * @package    {$name}Controller
 * @copyright  {$smarty.now|date_format:'%Y'} {#COPYRIGHT#}. Powerd by WingPHP
 * @author     {#AUTHOR_NAME#} < {#AUTHOR_EMAIL#} >
 * @license    {#LICENSE#}
 * @access     public
 */
class {$name}Controller extends BaseController{
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
	private function _foobar(){
		;
	}
}