<?php
/* [WingPHP]
 *  - BaseModel class
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
 * BaseModelクラス
 * 
 * 各モデルのスーパークラス。
 * 以下のような処理を受け持つ。
 *  - DBへの接続
 *  - SQLの実行、返却
 *
 * @package    BaseModel
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class BaseModel{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	private $dbh = false;

	protected $db_location = 'master';

	protected $table_name   = null;
	protected $table_column = array(
									  'id'       => ['type'=>'integer',  'size'=>null,   'opt'=>['autoincrement'=>true]]
									, 'login_id' => ['type'=>'varchar',  'size'=>32,     'opt'=>['notnull'=>true]]
									, 'login_pw' => ['type'=>'varchar',  'size'=>40,     'opt'=>['notnull'=>true]]
									, 'name'     => ['type'=>'varchar',  'size'=>64]
									, 'email'    => ['type'=>'varchar',  'size'=>255]
									, 'status'   => ['type'=>'integer',  'size'=>null,   'opt'=>['notnull'=>true, 'default'=>0]]		//0=regist, 1=activate, 9=user remove , 99=BAN
									, 'regdate'  => ['type'=>'datetime', 'size'=>null]
									, 'upddate'  => ['type'=>'datetime', 'size'=>null]
								);
	protected $select_limit  = null;
	protected $select_offset = null;

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
	 * - usedb
	 * - select
	 * - select1
	 * - exec
	 * - begin
	 * - isTransaction
	 * - commit
	 * - rollback
	 * - existsRecord
	 * - searchRecord
	 * - insertRecord
	 * - updateRecord
	 * - updateRecordAll
	 * - deleteRecord
	 * - truncate
	 * - setTableName
	 * - setLimit
	 * - setOffSet
	 * - setPaging
	 *--------------------------------------------*/
	//--------------------------------------------
	// DBサーバー周りの設定
	//--------------------------------------------
	/**
	 * DB接続先を切り替える
	 *
	 * 指定されたDBへ接続先を変更する。
	 * 未指定の場合は'master' が呼び出される。Confに存在しない場合は強制終了する。
	 *
	 * @param  mixed $account 
	 * @return void
	 * @access public
	 */
	public function usedb($name='master'){
		// 接続先を決定
		$account = '';
		if(is_array($name)){
			$max = count($name) - 1;
			$i   = rand(0, $max);
			$account = $name[$i];
		}
		else{
			$account = $name;
		}

		// メンバ変数にセットする
		global $Conf;
		if( array_key_exists($account, $Conf['DB']) ){
			//現在と異なるDBへ接続する場合、ハンドラをfalseにする
			if( $this->db_location !== $account ){
				$this->dbh = false;
			}
			
			$this->db_location = $account;
		}
		else{
			throw new WsException('[usedb] 404 configration $Conf[DB]', 404);
		}
	}

	//--------------------------------------------
	// SQL実行
	//--------------------------------------------
	/**
	 * SELECT句実行 全返却
	 *
	 * 指定されたSELECT句を実行し、結果のすべて返却する。
	 *   ※巨大なデータになることが予測される場合は
	 *     必ずoffsetなどを利用すること。
	 *
	 * @param  string $sql   SQL文を直書き。
	 * @param  array  $bind  SQL文内でプレースホルダを利用して
	 *                       いる場合は配列で渡す。順番考慮。
	 * @return array  array(
	 *                     array(col1=>'foo', col2=>'bar')
	 *                   , array(col1=>'hoge',col2=>'fuga')  )
	 * @access public
	 */
	public function select($sql, $bind=array(), $use_cache=false){
		return( $this->_select($sql, $bind, 'all', $use_cache) );
	}

	/**
	 * SELECT句実行 一件返却
	 *
	 * 指定されたSELECT句を実行し、結果の最初の1行目を返却する。
	 *
	 * @param  string $sql   SQL文を直書き。
	 * @param  array  $bind  SQL文内でプレースホルダを利用して
	 *                       いる場合は配列で渡す。順番考慮。
	 * @return array  array(col1=>'foo', col2=>'bar')
	 * @access public
	 */
	public function select1($sql, $bind=array(), $use_cache=false){
		return( $this->_select($sql, $bind, 'one', $use_cache) );
	}

	/**
	 * UPDATE,INSERT,DELETE句実行
	 *
	 * データ更新系のSQLを実行する。
	 * 2013/09/06 is_tra, commit の引数を廃止
	 *
	 * @param  string $sql       SQL文を直書き。
	 * @param  array  $bind      SQL文内でプレースホルダを利用している場合は配列で渡す。順番考慮。
	 * @return bool
	 * @access public
	 */
	public function exec($sql, $bind=array()){
		if(!$this->dbh)
			$this->dbh = $this->_connect();
		
		return( $this->_runsql($sql, $bind, 'exec') );
	}
	
	/**
	 * トランザクションを開始する
	 * 
	 * 2013/09/06 beginTransaction から begin にメソッド名を変更
	 * @return bool
	 * @access public
	 */
	public function begin(){
		if(!$this->dbh)
			$this->dbh = $this->_connect();

		try{
		
			$ret = $this->dbh->beginTransaction();
			return($ret);
		}
		catch( PDOException $e ){
			throw new WsException('[begin]'.$e->getMessage(), $e->getCode());
		}
	}
	
	/**
	 * トランザクション中か判定する
	 *
	 * @return bool
	 * @access public
	 */
	public function isTransaction(){				//PDOは"in", このメソッドは"is"
		if(!$this->dbh)
			$this->dbh = $this->_connect();

		try{
			$ret = $this->dbh->inTransaction();
			return($ret);
		}
		catch( PDOException $e ){
			throw new WsException('[isTransaction]'.$e->getMessage(), $e->getCode());
		}
	}

	/**
	 * commitする
	 *
	 * @return bool
	 * @access public
	 */
	public function commit(){
		try{
			$ret = $this->dbh->commit();
			return($ret);
		}
		catch( PDOException $e ){
			throw new WsException('[commit]'.$e->getMessage(), $e->getCode());
		}
	}

	/**
	 * rollbackする
	 *
	 * @return bool
	 * @access public
	 */
	public function rollback(){
		if(!$this->dbh)
			return(false);
		
		try{
			$ret = $this->dbh->rollBack();
			return($ret);
		}
		catch( PDOException $e ){
			throw new WsException('[rollback]'.$e->getMessage(), $e->getCode());
		}		

	}


	/**
	 * Check for the record exists
	 *
	 * example.
	 *     $ret = $this->existsRecord('id=?', 'foobar');
	 *     if($ret){
	 *        // ....
	 * 	   }
	 * 
	 * @param  string         $key
	 * @param  string|number  $value
	 * @param  string         $table  [option]
	 * @return boolean
	 * @access public
	 */
	public function existsRecord( $where, $value=array(), $table=null ){
		$value = (is_array($value))?  $value:array($value);
		$table = $this->_checkTableName($table);
		
		$sql  = sprintf('SELECT count(*) as cnt FROM %s WHERE %s', $table, $where);
		$buff = $this->select1($sql, $value);
	
		if($buff === false ){
			throw new WsException('[existsRecord] Can not exection SQL: '.$sql);
		}
		else if($buff['cnt'] > 0){
			return(true);
		}
		else{
			return(false);
		}
	}


	/**
	 * Search for the Table
	 *
	 * もしもgroup by句 や having句、複雑なSQL文が必要な場合は、$this->select()を用いてください。
	 * $tableは、$this->setTableName()で、
	 * $limitは、$this->setPagin(), $this->limit(), $this->setOffset()で事前に設定できます。
	 * 
	 * If you need the "group by", "having" and complicated SQL, please using the $this->selet().
	 * 
	 * example.
	 *     $result = $this->searchRecord('id=?', 1);
	 *     $result = $this->searchRecord('id=? and name=?', [1, 'katsube']);
	 *     $result = $this->searchRecord('id=? and name=?', [1, 'katsube'], [0,10]);
	 * 
	 * @param  string                 $where    "name1=? and name2 like '%foo%'"
	 * @param  array|string  [option] $value    array(value1, value2 ... valuen) or value1
	 * @param  string        [option] $limit    array(0,10)
	 * @param  string        [option] $orderby  "id ASC"
	 * @param  string        [option] $table
	 * @return bool
	 * @access public
	 */
	public function searchRecord($where, $value=array(), $orderby=null, $limit=null, $table=null){
		$value   = (is_array($value))?  $value:array($value);
		$orderby = $this->_checkOrderby($orderby);
		$limit   = $this->_checkLimit($limit);
		$table   = $this->_checkTableName($table);
		
		$sql  = sprintf('SELECT * FROM %s WHERE %s%s%s', $table, $where, $orderby, $limit);
		$buff = $this->select($sql, $value);
		
		if($buff === false ){
			throw new WsException('[searchRecord] Can not exection SQL: '.$sql);
		}
		else{
			return($buff);
		}
	}


	/**
	 * Insert for the Table
	 * 
	 * example.
	 *   $this->insertRecord([1, 'katsube']);
	 *   $this->insertRecord([2], 'id');
	 *   
	 * @param  array             $value   array(value1, value2 ... valuen) or value1
	 * @param  string   [option] $column  "id,name,value1,value2" 
	 * @param  string   [option] $table
	 * @return boolean
	 * @access public
	 */
	public function insertRecord($value, $column=null, $table=null){
		$table       = $this->_checkTableName($table);
		$placeholder = rtrim( str_repeat('?,', count($value)), ',' );

		if( $column === null ){
			$sql = sprintf('INSERT INTO %s VALUES(%s)', $table, $placeholder);
		}
		else{
			$sql = sprintf('INSERT INTO %s (%s) VALUES(%s)', $table, $column, $placeholder);
		}
		
		try{
			$this->begin();
			$ret = $this->exec($sql, $value);
			$this->commit();
			
			return($ret);
		}
		catch(WsException $we){
			$this->rollback();
			throw new WsException('[insertRecord] Can not exection SQL: '.$sql);
		}
	}


	/**
	 * Update for the Record
	 * 
	 * example.
	 *    $this->updateRecord($set, $where, $value);
	 *    $this->updateRecord('name=?', 'id=?', [$name, $id]);
	 * 
	 *    //Update for all record
	 *    $this->updateRecord('name=?', null, $name);
	 *    $this->updateRecordAll('name=?', $name);
	 *
	 * @param  string            $set     "name=?"
	 * @param  string            $where   "id=? and name=?"
	 * @param  array             $value   array(value1, value2 ... valuen) or value1
	 * @param  string   [option] $table
	 * @return boolean
	 * @access public
	 */
	public function updateRecord($set, $where, $value, $table=null){
		$table = $this->_checkTableName($table);
		$value = (is_array($value))?  $value:array($value);

		//Build SQL
		$sql = sprintf('UPDATE %s SET %s', $table, $set);
		if( $where!==null ){
			$sql .= sprintf(' WHERE %s', $where);
		}

		try{
			$this->begin();
			$ret = $this->exec($sql, $value);
			$this->commit();
			
			return($ret);
		}
		catch(WsException $we){
			$this->rollback();
			throw new WsException('[updateRecord] Can not exection SQL: '.$sql);
		}
	}


	/**
	 * Update for the All Records
	 * (alias updateRecord())
	 * 
	 * @param  string            $set     "name=?"
	 * @param  array             $value   array(value1, value2 ... valuen) or value1
	 * @param  string   [option] $table
	 * @return boolean
	 * @access public
	 */
	public function updateRecordAll($set, $value, $table=null){
		return(
			$this->updateRecord($set, null, $value, $table)
		);
	}


	/**
	 * Delete for the Records
	 * 
	 * example.
	 *    $this->deleteRecord($where, $value);
	 *    $this->deleteRecord('id=?', 1);
	 * 
	 *    //delete for all records
	 *    $this->deleteRecord('true') //Not recommended
	 *                                //"delete from tablename where true"
	 *    $this->truncate();
	 * 
	 * @param  string            $where   "name=?"
	 * @param  array    [option] $value   array(value1, value2 ... valuen) or value1
	 * @param  string   [option] $table
	 * @return boolean
	 * @access public
	 */
	public function deleteRecord($where, $value=array(), $table=null){
		$table = $this->_checkTableName($table);
		$value = (is_array($value))?  $value:array($value);

		//Build SQL
		$sql = sprintf('DELETE FROM %s WHERE %s', $table, $where);

		try{
			$this->begin();
			$ret = $this->exec($sql, $value);
			$this->commit();
			
			return($ret);
		}
		catch(WsException $we){
			$this->rollback();
			throw new WsException('[deleteRecord] Can not exection SQL: '.$sql);
		}
	}
	
	/**
	 * Truncate Tble
	 * 
	 * example.
	 *    $this->truncate();
	 * 
	 * @param  string   [option] $table
	 * @return boolean
	 * @access public
	 */
	public function truncate($table=null){
		$table = $this->_checkTableName($table);
		
		$sql = sprintf('TRUNCATE TABLE %s', $table);

		try{
			$this->begin();
			$ret = $this->exec($sql);
			$this->commit();
			
			return($ret);
		}
		catch(WsException $we){
			$this->rollback();
			throw new WsException('[truncate] Can not exection SQL: '.$sql);
		}
	}


	public function setTableName($name){
		$this->table_name = $name;
	}

	public function setLimit($limit){
		$this->select_limit = $limit;		
	}
	
	public function setOffSet($offset){
		$this->select_offset = $offset;
	}

	public function setPaging($offset, $limit){
		$this->setOffSet($offset);
		$this->setLimit($limit);
	}


	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _connect
	 * - _select
	 * - _runsql
	 * - _getExceptionMessage
	 * - _checkTableName
	 *--------------------------------------------*/
	/**
	 * DBに接続する
	 *
	 * @global $GLOBALS['Conf']
	 * @access private
	 */
	 private function _connect(){
		global $Conf;
		$account = $Conf['DB'][$this->db_location];

		try{
			$dbh = new PDO( $account['DSN'], $account['USER'], $account['PASSWORD']
								, array(
									  PDO::ATTR_PERSISTENT => $account['persistent']
									, PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION
								  )
						);
			
		}
		catch(PDOException $e){
			$cd  = $e->getCode();
			$msg = $e->getMessage();

			$result = sprintf('[_connect] %s', $msg);
			throw new WsException($result, $cd);
		}

		return( $dbh );
	}

	/**
	 * select実行
	 *
	 * @access private
	 */
	private function _select($sql, $bind=array(), $type='all', $use_cache=false){
		global $Conf;

		$account = $Conf['DB'][$this->db_location];
		if($use_cache === true || $Conf['Cache']['db_use']){
			uselib('Cache');
			$cache = new Cache($Conf['Cache']['strage']);
			$key   = sprintf('%s.%s', $Conf['Cache']['db_pre'], sha1($sql . serialize($bind) . $type) );
			
			//キャッシュが存在するならそのまま返却
			if( $cache->exists($key) ){
				$ret = $cache->get($key);
			}
			//キャッシュが無いなら新規に取得
			else{
				$ret = $this->_runsql($sql, $bind, $type);
			
				//キャッシュにセット
				$cache->expire($Conf['Cache']['db_expire']);
				$cache->set($key, $ret);
			}
		}
		else{
			$ret = $this->_runsql($sql, $bind, $type);
		}
	
		return($ret);
	}

	/**
	 * SQL実行
	 *
	 * @access private
	 */
	private function _runsql($sql, $bind, $type){
		global $Conf;
		$account = $Conf['DB'][$this->db_location];
		
		if(!$this->dbh)
			$this->dbh = $this->_connect();

		try{
			$st  = $this->dbh->prepare($sql);
			$ret = $st->execute($bind);
		}
		catch(PDOException $pe){
			throw new WsException($this->_getExceptionMessage('_runsql', $st ));
		}

		if(!$ret){
			throw new WsException($this->_getExceptionMessage('_runsql', $st ));
		}
		else{
			switch($type){
				case 'all': return( $st->fetchAll($account['fetch_style']) );
				case 'one': return( $st->fetch($account['fetch_style']) );
				   default: return( $ret );
			}	
		}
	}


	/**
	 * エラーメッセージ作成
	 *
	 * @access private
	 */
	private function _getExceptionMessage($name, $st){
		//データベースハンドラ
		$error_cd    = $this->dbh->errorCode();
		$error_info  = $this->dbh->errorInfo();

		//PDOStatement 
		if($error_cd === null || $error_cd === '00000'){
			$error_cd   = $st->errorCode();
			$error_info = $st->errorInfo();
		}

		$result = sprintf('[%s] %s', $name, implode(' ', $error_info));
		return($result);
	}

	/**
	 * 
	 */
	private function _CheckTableName($table){
		if( $table === null ){
			if( $this->table_name === null ){
				$dbg = debug_backtrace();
				$iam = $dbg[1]['function'];
				
				throw new WsException('['.$iam.'] Not Unspecified Table name');
			}
			
			return( $this->table_name );
		}

		return($table);
	}


	private function _checkLimit($limit){
		if($limit === null){
			if ($this->select_limit !== null && $this->select_offset === null){
				return(sprintf(' LIMIT %s', $this->select_limit));
			}
			else if ($this->select_limit !== null && $this->select_offset !== null){
				return(sprintf(' LIMIT %s, %s', $this->select_offset, $this->select_limit));
			}
			else{
				return('');
			}
		}
		else{
			return(sprintf(' LIMIT %d,%d', $limit[0], $limit[1]));
		}
	}

	private function _checkOrderby($orderby){
		if($orderby === null){
			return('');
		}
		else{
			return(sprintf(' ORDER BY %s', $orderby));
		}
	}


}