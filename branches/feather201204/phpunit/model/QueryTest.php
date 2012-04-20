<?php
require_once('PHPUnit/Framework.php');
require_once('../../model/base.php');
require_once('../../model/query.php');

class QueryModelTest extends PHPUnit_Framework_TestCase{
	/**
	 * 最初に一度だけ実行
	 */
	public static function setUpBeforeClass(){
		$_REQUEST['str'] = 'hogehoge';
		$_REQUEST['key'] = 'foobar';
	}

	/**
	 * クエリー指定
	 */
	public function testOneData(){
		$q = new QueryModel();
		
		//存在しているキー
		$this->assertEquals($q->data('str'), 'hogehoge');
		
		//存在しないキー
		$this->assertEquals($q->data('notfound'), null);
	}

	/**
	 * 全クエリー取得
	 */
	public function testAllData(){
		$q = new QueryModel();
		
		//すべて返却
		$this->assertEquals(
			  $q->data()
			, array('str'=>'hogehoge', 'key'=>'foobar')
		);
	}	
}
?>
