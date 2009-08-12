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

class BaseModel{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	private $dbh = false;

	//--------------------------------------------
	// コンストラクタ
	//--------------------------------------------
	function __construct(){
		;
	}

	//--------------------------------------------
	// デストラクタ
	//--------------------------------------------
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
	//------------
	//全件返却
	//------------
	public function select($sql, $bind=array()){
		return( $this->_runsql($sql, $bind, 'all') );
	}

	//------------
	//1件返却
	//------------
	public function select1($sql, $bind=array()){
		return( $this->_runsql($sql, $bind, 'one') );
	}

	//------------
	//更新
	//------------
	public function exec($sql, $bind=array()){
		if(!$this->dbh)
			$this->dbh = $this->_connect();

		//実行
		$this->dbh->beginTransaction();
		$ret = $this->_runsql($sql, $bind, 'exec');

		//確定 or 巻戻し
		if($ret) $this->dbh->commit();
		else $this->dbh->rollBack();

		return( $ret );
	}

	//--------------------------------------------
	// SQL作成用 便利関数
	//--------------------------------------------
	//updateのsetを作成(bind考慮)
	public function makeUpdateSet($arr){
		$result = array();
		foreach($arr as $key => $val){
			array_push($result, sprintf('%s=?', $key));
		}
		
		return( join(', ', $result) );
	}

	//update set ～ のbind値を抜出し 
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
	//--------------------------------------------
	// 接続
	//--------------------------------------------
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

	//--------------------------------------------
	// SQL実行
	//--------------------------------------------
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