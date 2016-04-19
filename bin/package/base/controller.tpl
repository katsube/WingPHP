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
	//-------------------------------------------------------------
	// Class constants
	//-------------------------------------------------------------
	const HOGE = 'constant value';		//self::HOGE

	//-------------------------------------------------------------
	// Member variable
	//-------------------------------------------------------------
	/**
	 * member variable
	 * 
	 * @var string
	 */
	public    static $foobar1 = 'hello';	//外部からアクセス可 (インスタンス生成不要)
	protected static $foobar2 = 'hello';	//このクラスとサブクラスからアクセス可(インスタンス生成不要)
	private   static $foobar3 = 'hello';	//このクラスのみアクセス可(インスタンス生成不要)

	public    $foobar4 = 'hello';	//外部からアクセス可 (インスタンス生成必要)
	protected $foobar5 = 'hello';	//このクラスとサブクラスからアクセス可(インスタンス生成必要)
	private   $foobar6 = 'hello';	//このクラスのみアクセス可(インスタンス生成必要)



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