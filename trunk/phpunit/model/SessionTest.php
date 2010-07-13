<?php
require_once('PHPUnit/Framework.php');
require_once('../../model/base.php');
require_once('../../model/session.php');

/**
 * @backupGlobals disabled
 */
class SessionModelTest extends PHPUnit_Framework_TestCase{
	/**
	 * 最初に一度だけ実行
	 */
	public static function setUpBeforeClass(){
	}

	/**
	 * @dataProvider provider
	 */
	function testSet($a, $b){
		$s = new SessionModel();
		$s->set($a, $b);
	}

	function provider(){
		return(array(
			  array('age',        30)
			, array('weight',     78)
			, array('name',       'katsube')
			, array('birthday',   '1979-10-05')
			, array('love beer?', 'Yes!')
		));
	}

	/**
	 * 取得する
	 *
     * @depends testSet
     */
	function testGet(){
		$s = new SessionModel();
		
		//存在する
		$this->assertEquals($s->get('age'), 30);
		
		//存在しない
		$this->assertEquals($s->get('foobar'), null);
	}

	/**
	 * 削除する
	 *
     * @depends testSet
     */
	function testDel(){
		$s = new SessionModel();
		
		//削除
		$s->del('age');
		$this->assertEquals($s->get('age'), null);
	}

	/**
	 * 存在確認
	 *
     * @depends testSet
     */
	function testExistkey(){
		$s = new SessionModel();
		
		//存在する
		$this->assertTrue($s->exists('name'));

		//存在しない
		$this->assertFalse($s->exists('age'));
	}
	
	/**
	 * セッション破棄
	 *
     * @depends testSet
     */
	function testDestroy(){
		$s = new SessionModel();
		$s->destroy();
		
		//セッション破棄済みなのですべて消えている
		$this->assertEquals($s->get('name'),     null);
		$this->assertEquals($s->get('birthday'), null);
		$this->assertEquals($s->get('weight'),   null);
	}
}
?>
