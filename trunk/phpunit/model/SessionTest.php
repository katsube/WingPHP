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
	 * テストケースを実行する際に毎回実行：前
	 */
	protected function setUp(){
		$_SESSION = array(
			  'age'        => 30
			, 'weight'     => 78
			, 'name'       => 'katsube'
			, 'birthday'   => '1979-10-05'
			, 'love beer?' => 'Yes!'
		);
	}

	/**
	 * セットする
     */
	function testSet(){
		$s = new SessionModel();
		
		//セットする
		$s->set('foobar', 'ふーばー');
		$s->set('name', 'makito');			//上書き
		
		//存在する
		$this->assertEquals($s->get('age'), 30);
		$this->assertEquals($s->get('foobar'), 'ふーばー');
		$this->assertEquals($s->get('name'), 'makito');			//上書きされてる
		
		//存在しない
		$this->assertEquals($s->get('hogehoge'), null);
	}



	/**
	 * 取得する
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
     */
	function testDel(){
		$s = new SessionModel();
		
		//削除
		$s->del('age');
		$this->assertEquals($s->get('age'), null);
	}

	/**
	 * 存在確認
     */
	function testExistkey(){
		$s = new SessionModel();
		
		//存在する
		$this->assertTrue($s->exists('name'));

		//存在しない
		$this->assertFalse($s->exists('foooooobaaaaaaaaaaar'));
	}
	
	/**
	 * セッション破棄
     */
	function testDestroy(){
		$s = new SessionModel();
		$s->destroy();
		
		//セッション破棄済みなのですべて消えている
		$this->assertEquals($s->get('name'),     null);
		$this->assertEquals($s->get('birthday'), null);
		$this->assertEquals($s->get('weight'),   null);
	}

	/**
	 * 一度に挿入する機能
     */
	function testSetArray(){
		$s = new SessionModel();
		$s->set(array(
			  'apple'  => 'りんご'
			, 'orange' => 'レンジ'
		));

		$this->assertEquals($s->get('apple'), 'りんご');
		$this->assertEquals($s->get('orange'), 'レンジ');
	}
}
?>
