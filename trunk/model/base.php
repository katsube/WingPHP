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
	 * - select
	 * - select1
	 * - exec
	 * - makeUpdateSet
	 * - makeUpdateSetBind
	 *--------------------------------------------*/

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
	public function select($sql, $bind=array()){
		return( $this->_runsql($sql, $bind, 'all') );
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
	public function select1($sql, $bind=array()){
		return( $this->_runsql($sql, $bind, 'one') );
	}

	/**
	 * UPDATE,INSERT,DELETE句実行
	 *
	 * データ更新系のSQLを実行する。
	 *
	 * @param  string $sql    SQL文を直書き。
	 * @param  array  $bind   SQL文内でプレースホルダを利用して
	 *                        いる場合は配列で渡す。順番考慮。
	 * @param  bool   $is_tra トランザクションを利用する場合はtrue
	 * @return bool
	 * @access public
	 */
	public function exec($sql, $bind=array(), $is_tra=true){
		if(!$this->dbh)
			$this->dbh = $this->_connect();
	
		if($is_tra){
			//実行
			$this->dbh->beginTransaction();
			$ret = $this->_runsql($sql, $bind, 'exec');

			//確定 or 巻戻し
			if($ret) $this->dbh->commit();
			else $this->dbh->rollBack();
		}
		else{
			$ret = $this->_runsql($sql, $bind, 'exec');
		}

		return( $ret );
	}

	//--------------------------------------------
	// SQL作成用 便利関数
	//--------------------------------------------
	/**
	 * UPDATE句のsetを作成
	 *
	 * プレースホルダを考慮しupdate句のset部分を作成する。
	 *
	 * Example.<code>
	 *   $ret = $this->makeUpdateSet(array(
	 *              'name' => 'foo'
	 *            , 'age'  => 19
	 *            , 'addr' => '松江'
	 *          ));
	 *   // $ret === 'name=?, age=?, addr=?'
	 * </code>
	 *
	 * @param  array   $arr  key-value
	 * @return string
	 * @access public
	 */
	 public function makeUpdateSet($arr){
		$result = array();
		foreach($arr as $key => $val){
			array_push($result, sprintf('%s=?', $key));
		}
		
		return( join(', ', $result) );
	}

	/**
	 * UPDATE句のプレースホルダ用の配列を作成
	 *
	 * Example.<code>
	 *   $ret = $this->makeUpdateSetBind(array(
	 *              'name' => 'foo'
	 *            , 'age'  => 19
	 *            , 'addr' => '松江'
	 *          ));
	 *   // $ret === array('foo',19,'松枝')
	 * </code>
	 *
	 * @param  array   $arr  key-value
	 * @return array
	 * @access public
	 */
	public function makeUpdateSetBind($arr){
		$result = array();
		foreach($arr as $key => $val){
			array_push($result, $val);
		}
		
		return( $result );
	}

	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _connect
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
		
		try{
			$dbh = new PDO(
						  $Conf['DB']['DSN']
						, $Conf['DB']['USER']
						, $Conf['DB']['PASSWORD']
					);
			
			return( $dbh );
		}
		catch(PDOException $e){
			print('Error:'.$e->getMessage());
			die();
		}

	}

	/**
	 * SQL実行
	 *
	 * @access private
	 */
	private function _runsql($sql, $bind, $type){		
		if(!$this->dbh)
			$this->dbh = $this->_connect();

		$st  = $this->dbh->prepare($sql);
		$ret = $st->execute($bind);
		
		switch($type){
			case 'all': return( $st->fetchAll() );
			case 'one': return( $st->fetch() );
			   default: return( $ret );
		}
	}
}
?>