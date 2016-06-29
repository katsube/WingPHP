<?php
require_once('define.php');
require_once('../lib/Sendmail/index.php');

class SendmailUnitTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Constructer
     * 
     * @covers Sendmail::__construct
     * @dataProvider ConstructProvider
     */
    public function testConstruct($setting){
        global $Conf;
        $Conf['Sendmail'] = $setting;
    
        $mail = new Sendmail();
        $this->assertEquals($setting['language'],  $mail->getLanguage());
        $this->assertEquals($setting['encode'],    $mail->getEncording());
        $this->assertEquals($setting['header'],    $mail->getHeaders());
        $this->assertEquals($setting['log']['on'], $mail->isLogging());
    }

    public function ConstructProvider(){
        return(array(
            array([
                  'language' => 'Japanese'
                , 'encode'   => 'Shift_JIS'
                , 'header'   => ['From'=>'foo@example.com', 'Content-type'=>'text/plain', 'MIME-Version'=>'1.0', 'X-Mailer'=>'I am Tester']
                , 'log'      => ['on'=>false]
            ])
            , array([
                  'language' => 'English'
                , 'encode'   => 'UTF-8'
                , 'header'   => ['From'=>'bar@example.com', 'Content-type'=>'text/plain', 'MIME-Version'=>'1.0', 'X-Mailer'=>'I am Tester']
                , 'log'      => ['on'=>true]
            ])
        ));
    }


    /**
     * Test headers()
     * 
     * @covers Sendmail::headers
     */
    public function testHeaders(){
        $this->markTestIncomplete('Not imprements');
    }

    /**
     * Test get headers()
     * 
     * @covers Sendmail::getHeaders
     */
    public function testGetHeaders(){
        $this->markTestIncomplete('Not imprements');
    }

    /**
     * Test body()
     * 
     * @covers Sendmail::body
     */
    public function testBody(){
        $this->markTestIncomplete('Not imprements');
    }

    /**
     * Test doit()
     * 
     * @covers Sendmail::doit
     */
    public function testDoit(){
        $this->markTestIncomplete('Not imprements');
    }

    /**
     * Test setLanguage
     * 
     * @covers Sendmail::setLanguage
     */
    public function testSetLanguage(){
        $this->markTestIncomplete('Not imprements');
    }

    /**
     * Test getLanguage
     * 
     * @covers Sendmail::getLanguage
     */
    public function testGetLanguage(){
        $this->markTestIncomplete('Not imprements');
    }

    /**
     * Test setEncording()
     * 
     * @covers Sendmail::setEncording
     */
    public function testSetEncording(){
        $this->markTestIncomplete('Not imprements');
    }

    /**
     * Test getEncording()
     * 
     * @covers Sendmail::getEncording
     */
    public function testGetEncording(){
        $this->markTestIncomplete('Not imprements');
    }

    /**
     * Test setLogging
     * 
     * @covers Sendmail::setLogging
     */
    public function testSetLogging(){
        $this->markTestIncomplete('Not imprements');
    }

    /**
     * Test isLogging
     * 
     * @covers Sendmail::isLogging
     */
    public function testIsLogging(){
        $this->markTestIncomplete('Not imprements');
    }

    /**
     * Test _checkParam()
     * 
     * @covers Sendmail::_checkParam
     */
    public function testCheckParam(){
        $this->markTestIncomplete('Not imprements');
    }

    /**
     * Test _makeHeader()
     * 
     * @covers Sendmail::_makeHeader
     */
    public function testMakeHeader(){
        $this->markTestIncomplete('Not imprements');
    }

    /**
     * Test _makeBody()
     * 
     * @covers Sendmail::_makeBody
     */
    public function testMakeBody(){
        $this->markTestIncomplete('Not imprements');
    }

}
