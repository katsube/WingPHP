<?php
/**
 * {$name}Model Class
 *
 * [[[ description here ]]]
 *
 * @package    {$name}Model
 * @copyright  {$smarty.now|date_format:'%Y'} {#COPYRIGHT#}. Powerd by WingPHP
 * @author     {#AUTHOR_NAME#} < {#AUTHOR_EMAIL#} >
 * @license    {#LICENSE#}
 * @access     public
 */
class {$name}Model extends BaseModel{

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
	 * - get
	 * - set
	 *--------------------------------------------*/
	/**
	 * get
	 *
	 * [[description here]]
	 *
	 * @param  string  $id
	 * @return void
	 * @access public
	 */
	public function get($id){
		try{
			$buff = $this->select('select * from test where id=?', array($id));
			return($buff);
		}
		catch(WsException $we){
			return(false);
		}
	}

	/**
	 * set
	 *
	 * [[description here]]
	 *
	 * @param  string  $id
	 * @param  string  $name
	 * @return void
	 * @access public
	 */
	public function set($id, $name){
		try{
			$this->begin();
			$this->exec('insert into test(id,name) values(?, ?)', array($id, $name));
			$this->commit();
		
			return(true);
		}
		catch(WsException $we){
			$this->rollback();
			return(false);
		}
	}


	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _foobar
	 *--------------------------------------------*/
	/**
	 * _foobar
	 *
	 * [[description here.]]
	 *
	 * @return void
	 * @access private
	 */
	public function _foobar(){
		;
	}

}