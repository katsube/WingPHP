<?php
/**
 * UserModel Class
 *
 * [[[ description here ]]]
 *
 * @package    UserModel
 * @copyright  2016 WinningSection. Powerd by WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class UserModel extends BaseModel{


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