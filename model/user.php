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
	
	const PW_SEED = 'wzxecrvtbynum,./kjuhygtfrd';
	
	private static $column = array(
									  'id'       => ['type'=>'integer',  'size'=>null,   'autoincrement'=>true]
									, 'login_id' => ['type'=>'varchar',  'size'=>32,     'notnull'=>true]
									, 'login_pw' => ['type'=>'varchar',  'size'=>40,     'notnull'=>true]
									, 'name'     => ['type'=>'varchar',  'size'=>64]
									, 'email'    => ['type'=>'varchar',  'size'=>255]
									, 'status'   => ['type'=>'integer',  'size'=>null,   'notnull'=>true, 'default'=>0]		//0=regist, 1=activate, 9=user remove , 99=BAN
									, 'regdate'  => ['type'=>'datetime', 'size'=>null]
									, 'upddate'  => ['type'=>'datetime', 'size'=>null]
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
	 *                                , 'login_pw'
	 *                                , 'name'
	 *                                , 'email'
	 *                            ]
	 * @return  boolean
	 * @access  public
	 */
	public function regist($param){
		try{
			if( $this->validation() ){
				$sql = sprintf('insert into %s(login_id,login_pw,name,email,regdate,upddate) values(?,?,?,?,NOW(),NOW())', self::DB_TABLE);
	
				$this->begin();
				$this->exec($sql, array(
										  $param['login_id']
										  $this->_passwd($param['login_pw'])
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
	 * - _passwd
	 *--------------------------------------------*/
	/**
	 * validation
	 *
	 * @return boolean
	 * @access private
	 */
	private function _validation(){
		uselib('Util/Validation');
		$v = new Validation();
		$v->addList(array(
				  'login_id' => array(['require', 'alnum', ['bytemin', 4], ['bytemax', 4]])
				, 'login_pw' => array(['require'])
				, 'name'     => array(['require'])
				, 'email'    => array(['require', 'email'])
			));
		
		return(true);
	}

	/**
	 * Get table cloumn size
	 *
	 * @param  string  $name
	 * @return boolean
	 * @access private
	 */
	private function _getCloumnSize($name){
		return( $this->column[$name]['size'] );
	}


	/**
	 * Make Passowrd hash
	 *
	 * @param  string  $pw
	 * @return string  sha1
	 * @access private
	 */
	private function _passwd($pw){
		global $Conf;
		$str = sprintf('%s%s%s', $Conf['Secret']['key'], $pw, self::PW_SEED);
		
		return( sha1($str) );
	}
}