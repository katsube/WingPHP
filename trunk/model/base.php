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
	private $db_location = 'master';

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
	 * - beginTransaction
	 * - commit
	 * - rollback
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
		else
			die();		//confに未登録のアカウントが指定された場合は死ぬ
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
	 *
	 * @param  string $sql       SQL文を直書き。
	 * @param  array  $bind      SQL文内でプレースホルダを利用している場合は配列で渡す。順番考慮。
	 * @param  bool   $is_tra    トランザクションを利用する場合はtrue
	 * @param  bool   $is_commit 処理終了後にcommit(rollback)する場合はtrue
	 * @return bool
	 * @access public
	 */
	public function exec($sql, $bind=array(), $is_tra=true, $is_commit=true){
		if(!$this->dbh)
			$this->dbh = $this->_connect();
		
		//トランザクションスタート
		if($is_tra)
			$this->beginTransaction();
		
		//SQL実行
		$ret = $this->_runsql($sql, $bind, 'exec');

		//確定 or 巻戻し
		if($is_commit){
			if($ret)
				$this->commit();
			else
				$this->rollback();
		}

		return( $ret );
	}
	
	/**
	 * トランザクションを開始する
	 *
	 * @return bool
	 * @access public
	 */
	public function beginTransaction(){
		return( $this->dbh->beginTransaction() );
	}
	
	/**
	 * commitする
	 *
	 * @return bool
	 * @access public
	 */
	public function commit(){
		return( $this->dbh->commit() );
	}

	/**
	 * rollbackする
	 *
	 * @return bool
	 * @access public
	 */
	public function rollback(){
		return( $this->dbh->rollBack() );
	}
	

	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _connect
	 * - _select
	 * - _runsql
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
								, array( PDO::ATTR_PERSISTENT => $account['persistent'] )
						);
			
			return( $dbh );
		}
		catch(PDOException $e){
			print('Error:'.$e->getMessage());
			die();
		}

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

		$st  = $this->dbh->prepare($sql);
		$ret = $st->execute($bind);
		if(!$ret){
			return(false);
		}
		else{
			switch($type){
				case 'all': return( $st->fetchAll($account['fetch_style']) );
				case 'one': return( $st->fetch($account['fetch_style']) );
				   default: return( $ret );
			}	
		}
	}
}
?>