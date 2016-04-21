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

	const DB_TABLE   = 'user';		//Table name
	const DB_PK      = 'id';		//PrimaryKey column name
	const DB_LOGINID = 'login_id';	//LoginID column name. uniq key.
	
	private static $column = array(
									  'id'       => ['integer',    'AUTO_INCREMENT']
									, 'login_id' => ['varchar(32)','NOT NULL']
									, 'name'     => ['varchar(64)']
									, 'email'    => ['varchar(255)']
									, 'status'   => ['integer',    'DEFAULT 0']		//0=regist, 1=activate, 9=user remove 
									, 'regdate'  => ['datetime']
									, 'upddate'  => ['datetime']
								);

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
	 * - exists
	 * - regist
	 * - remove
	 * - activate
	 * - auth
	 * - get
	 * - set
	 *--------------------------------------------*/
	/**
	 * Check for the record exists
	 * 
	 * @param   integer $login_id
	 * @return  boolean
	 * @access  public
	 */
	public function exists($login_id){
		try{
			$sql  = sprintf('select count(*) as cnt from %s where %s=?', self::DB_TABLE, self::DB_LOGINID);
			$buff = $this->select1($sql, array($login_id));
			
			return(
				($buff !=== false) && ($buff['cnt'] > 0)
			);
		}
		catch(WsException $we){
			return(false);
		}
	}


	/**
	 * User registration
	 * 
	 * @param   object  $param = [
	 *	                                'login_id'
	 *                                , 'name'
	 *                                , 'email'
	 *                            ]
	 * @return  boolean
	 * @access  public
	 */
	public function regist($param){
		try{
			if( $this->validation() ){
				$sql = sprintf('insert into %s(login_id,name,email,regdate,upddate) values(?,?,?,NOW(),NOW())', self::DB_TABLE);
	
				$this->begin();
				$this->exec($sql, array(
										  $param['login_id']
										, $param['name']
										, $param['email']));
				$this->commit();

				return(true);
			}
			else{
				return(false);
			}
			
		}
		catch(WsException $we){
			return(false);
		}
		
	}

	public function remove(){
		
	}
	
	public function activate(){
		
	}
	
	public function auth(){
		
	}
	

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
	 * - _validation
	 *--------------------------------------------*/
	/**
	 * validation
	 *
	 * @return void
	 * @access private
	 */
	private function _validation(){
		return(true);
	}

}