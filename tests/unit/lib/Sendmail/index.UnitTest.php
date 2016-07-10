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
                , 'header'   => ['From'=>'foo@example.com']
                , 'log'      => ['on'=>false]
            ])
            , array([
                  'language' => 'English'
                , 'encode'   => 'UTF-8'
                , 'header'   => ['From'=>'bar@example.com']
                , 'log'      => ['on'=>true]
            ])
        ));
    }


    /**
     * Test headers()
     * 
     * @covers Sendmail::headers
     * @dataProvider HeadersProvider
     */
    public function testHeaders($setting, $expected){
        if(is_hash($setting)){
            //conf
            global $Conf;
            if( array_key_exists('Sendmail', $Conf) && array_key_exists('header', $Conf['Sendmail']) && is_array($Conf['Sendmail']['header']) ){
                $setting = array_merge($setting, $Conf['Sendmail']['header']);
            }
        }
    
        $mail   = new Sendmail();
        $ret    = $mail->headers($setting);
        $header = $mail->getHeaders();
        
        $this->assertEquals($expected, $ret);
        if($ret)
            $this->assertEquals($setting, $header);
    }
    
    public function HeadersProvider(){
        return(array(
            //----------------------
            // Single
            //----------------------
              array(['From'        => 'foo@example.com'], true)
            , array(['Sender'      => 'foo@example.com'], true)
            , array(['To'          => 'foo@example.com'], true)
            , array(['To'          => 'foo@example.com, info@example.net'], true)
            , array(['Cc'          => 'foo@example.com'], true)
            , array(['Cc'          => 'foo@example.com, foo@example.net'], true)
            , array(['Bcc'         => 'foo@example.com'], true)
            , array(['Bcc'         => 'foo@example.com, foo@example.net'], true)
            , array(['Subject'     => 'foo!baaaaaaaaaaaaaaaa!'], true)
            , array(['Reply-to'    => 'foo@example.com'], true)
            , array(['Return-Path' => 'foo@example.com'], true)
            , array(['Errors-To'   => 'foo@example.com'], true)
            , array(['Date'        => '2016/07/01 00:00:00'], true)
            , array(['In-Reply-To' => 'xxxx.xxxx.xxxx@mail.example.com'], true)
            , array(['References'  => 'xxxx.xxxx.xxxx@mail.example.com'], true)
            , array(['Message-ID'  => 'xxxx.xxxx.xxxx@mail.example.com'], true)
            , array(['Precedence'  => 'list'], true)
            , array(['Precedence'  => 'junk'], true)
            , array(['Precedence'  => 'bulk'], true)

            //----------------------
            // Multiple
            //----------------------
            , array([
                      'From' => 'foo@example.com'
                    , 'To'   => 'info@example.net'
                ], true)
            , array([
                      'From'   => 'foo@example.net'
                    , 'To'     => 'info@example.com'
                    , 'Cc'     => 'cc@example.net'
                    , 'Bcc'    => 'bcc@example.net'
                    , 'Sender' => 'sender@example.net'
                ], true)

            //----------------------
            // Error
            //----------------------
            , array([],                false)
            , array([1,2,3,4,5],       false)
            , array(['Foobar'=>12345], false)
            , array('foobar',          false)
            , array(12345,             false)
            , array(1.2345,            false)
            , array(true,              false)
            , array(false,             false)
            , array(null,              false)
        ));
    }


    /**
     * Test get headers()
     * 
     * @covers Sendmail::getHeaders
     * @dataProvider GetHeadersProvider
     */
    public function testGetHeaders($setting, $name, $expected){
        $mail   = new Sendmail();
        $mail->headers($setting);

        $this->assertEquals($expected, $mail->getHeaders($name));
    }
    
    public function GetHeadersProvider(){
        return(array(
            //----------------------
            // Single
            //----------------------
              array(['From'        => 'foo@example.com'],                   'From',        'foo@example.com')
            , array(['Sender'      => 'foo@example.com'],                   'Sender',      'foo@example.com')
            , array(['To'          => 'foo@example.com'],                   'To',          'foo@example.com')
            , array(['To'          => 'foo@example.com, info@example.net'], 'To',          'foo@example.com, info@example.net')
            , array(['Cc'          => 'foo@example.com'],                   'Cc',          'foo@example.com')
            , array(['Cc'          => 'foo@example.com, foo@example.net'],  'Cc',          'foo@example.com, foo@example.net')
            , array(['Bcc'         => 'foo@example.com'],                   'Bcc',         'foo@example.com')
            , array(['Bcc'         => 'foo@example.com, foo@example.net'],  'Bcc',         'foo@example.com, foo@example.net')
            , array(['Subject'     => 'foo!baaaaaaaaaaaaaaaa!'],            'Subject',     'foo!baaaaaaaaaaaaaaaa!')
            , array(['Reply-to'    => 'foo@example.com'],                   'Reply-to',    'foo@example.com')
            , array(['Return-Path' => 'foo@example.com'],                   'Return-Path', 'foo@example.com')
            , array(['Errors-To'   => 'foo@example.com'],                   'Errors-To',   'foo@example.com')
            , array(['Date'        => '2016/07/01 00:00:00'],               'Date',        '2016/07/01 00:00:00')
            , array(['In-Reply-To' => 'xxxx.xxxx.xxxx@mail.example.com'],   'In-Reply-To', 'xxxx.xxxx.xxxx@mail.example.com')
            , array(['References'  => 'xxxx.xxxx.xxxx@mail.example.com'],   'References',  'xxxx.xxxx.xxxx@mail.example.com')
            , array(['Message-ID'  => 'xxxx.xxxx.xxxx@mail.example.com'],   'Message-ID',  'xxxx.xxxx.xxxx@mail.example.com')
            , array(['Precedence'  => 'list'],                              'Precedence',  'list')
            , array(['Precedence'  => 'junk'],                              'Precedence',  'junk')
            , array(['Precedence'  => 'bulk'],                              'Precedence',  'bulk')

            //----------------------
            // Multiple
            //----------------------

            //----------------------
            // Error
            //----------------------

        ));
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
