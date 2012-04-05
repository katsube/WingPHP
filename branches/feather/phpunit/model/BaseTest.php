<?php
require_once('PHPUnit/Framework.php');
require_once('../../model/base.php');

class BaseModelTest extends PHPUnit_Framework_TestCase{
	/**
	 * 最初に一度だけ実行
	 */
	public static function setUpBeforeClass(){
		//DBへの接続設定
		$GLOBALS['Conf'] = array(
			'DB' => array(
				  'DSN'      => 'mysql:dbname=test;host=localhost'
				, 'USER'     => 'user'
				, 'PASSWORD' => '*****'
			)
		);
	}

	/**
	 * 最後に一度だけ実行
	 */
	public static function tearDownAfterClass(){
		$model = new BaseModel();
		$model->exec("drop table _wingphp");
	}


	/**
	 * テーブル作成テスト
	 */
	function testExecCreateTable(){
		$model = new BaseModel();
		
		//テーブル作成
		$ret = $model->exec("create table _wingphp(id integer, name varchar(32));");
		$this->assertTrue($ret);
	}

	/**
	 * テーブルへ挿入テスト
	 *
     * @depends testExecCreateTable
	 * @dataProvider provider
     */
	function testExecInsert($a, $b){
		$model = new BaseModel();

		//挿入
		$ret = $model->exec("insert into _wingphp(id, name) values(?, ?)", array($a, $b));
		$this->assertTrue($ret);
	}

	function provider(){
		return(array(
			  array(1, 'katsube')
			, array(2, 'tanaka')
			, array(3, 'satoh')
			, array(4, 'suzuki')
			, array(5, 'mazda')
		));
	}

	/**
	 * 一行だけ抽出
	 *
     * @depends testExecInsert
     */
	function testSelect1(){
		$model = new BaseModel();

		//一行だけ抽出
		$ret = $model->select1("select count(*) as cnt from _wingphp");
		$this->assertEquals($ret['cnt'], 5);
	}

	/**
	 * すべて抽出
	 *
     * @depends testSelect1
     */
	function testSelect(){
		$model = new BaseModel();

		//全て抽出
		$ret = $model->select("select * from _wingphp");
		$this->assertEquals($ret[2]['name'], 'satoh');
	}
	
}
