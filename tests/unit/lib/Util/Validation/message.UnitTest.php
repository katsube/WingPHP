<?php
require_once('define.php');
require_once('../lib/Util/Validation/Message.php');

class UtilValidationMessageUnitTest extends PHPUnit_Framework_TestCase
{
    private $errormsg = array(
                			  'url'    => 'URLの書式ではありません'
                			, 'email'  => 'メールアドレスの書式ではありません'
                			, 'ip4'    => 'IPアドレスの書式ではありません'
                			, 'postcd' => '郵便番号の書式ではありません'
                			, 'tel'    => '電話番号の書式ではありません'
                			, 'num'    => '半角の数字ではありません'
                			, 'alpha'  => '半角の英字ではありません'
                			, 'alnum'  => '半角の英数字ではありません'
                
                			, 'require' => '必須項目が入力されていません'
                			, 'bytemax' => '最大byte数を超過しています'
                			, 'bytemin' => '最小byte数に達していません'
                			, 'max'     => '最大値を超過しています'
                			, 'min'     => '最小値に達していません'
                
                			, 'match' => '内容が一致しません'
                			, 'eq'    => '内容が一致しません'
                			, 'ne'    => '内容が一致しません'
                			, 'in'    => '内容が一致しません'
                
                			, 'date' => '有効な日付ではありません'
                			, 'time' => '有効な時間ではありません'
                
                			, 'grequire1' => '最低でも1つの入力が必要です'
                			, 'gin' => '内容が一致しません'
                
                			, '_404' => ''
                		);
    
    

    /**
     * test Util/Validation/Message - __construct()
     * 
     * 現状何もしないのでテストもなし。
     * 
     * @covers ValidationMessage::__construct
     */
    public function testConstruct(){
        $msg = new ValidationMessage();
    }


    /**
     * test Util/Validation/Message - get()
     * 
     * @covers ValidationMessage::get
     */
    public function testGet(){
        $msg  = new ValidationMessage();

        //正常取得    
        $this->assertEquals( $msg->get('url'), 'URLの書式ではありません' );
        $this->assertEquals( $msg->get('email'), 'メールアドレスの書式ではありません' );
        $this->assertEquals( $msg->get('require'), '必須項目が入力されていません' );
    
        //全部取得
        $this->assertEquals($msg->get(), $this->errormsg);

        //NotFound
        $this->assertEquals($msg->get('404'), null);
    }
    
    /**
     * test Util/Validation/Message - gets()
     * 
     * @covers ValidationMessage::gets
     */
    public function testGets(){
        $msg = new ValidationMessage();
        
        //未指定
        $this->assertEquals($msg->gets(), array() );
        $this->assertEquals($msg->gets([]), array() );
        
        //正常取得
        $this->assertEquals($msg->gets(['url']), array('url'=>'URLの書式ではありません') );
        $this->assertEquals($msg->gets(['email', 'require']), array('email'=>'メールアドレスの書式ではありません', 'require'=>'必須項目が入力されていません') );
    
        //NotFound
        $this->assertEquals($msg->gets(['404']), array('404'=>null) );
        $this->assertEquals($msg->gets(['url', '404']), array('url'=>'URLの書式ではありません', '404'=>null) );
    }
    
    /**
     * test Util/Validation/Message - set()
     * 
     * @covers ValidationMessage::set
     */
    public function testSet(){
        $msg = new ValidationMessage();

        //新規追加
        $msg->set('foo', 'barbarbar');
        $this->assertEquals($msg->get('foo'), 'barbarbar');
    
        //上書き
        $msg->set('url', 'barbarbar');
        $this->assertEquals($msg->get('url'), 'barbarbar');

        //引数不足
        $msg->set('foo');
        $this->assertEquals($msg->get('foo'), '');              //空文字
    
    
        //言語指定
        $msg->set('lang', 'Test Lang', 'en');
        $this->assertEquals($msg->get('lang'), null);               //jaにはない
        $this->assertEquals($msg->get('lang', 'en'), 'Test Lang');  //enにある
    }
    
    /**
     * test Util/Validation/Message - set()
     * 
     * @covers ValidationMessage::setLanguage
     */
    public function testSetLanguage(){
        $msg = new ValidationMessage();
        
        //存在する言語CD
        $this->assertTrue( $msg->setLanguage('ja') );

        //存在しない言語CD
        $this->assertFalse( $msg->setLanguage('xx') );
    }

}