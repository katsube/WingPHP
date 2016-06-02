<?php
require_once('define.php');

class WsExceptiongUnitTest extends PHPUnit_Framework_TestCase
{
    /**
     * test WsException __construct
     * 
     * @covers WsException::__construct
     */
    public function testConstruct(){
        global $Conf;
        $Conf['AutoLogging']['error'] = false;

        $message      = 'Hello Error';
        $code         = 500;

        $prev_message = 'Prev Error';
        $prev_code    = 100;
        $prev         = new Exception($prev_message, $prev_code);
        
        $wsex = new WsException($message, $code, $prev);
        $this->assertEquals($wsex->getMessage(), $message);
        $this->assertEquals($wsex->getCode(), $code);
        $this->assertEquals($wsex->getPrevious(), $prev);
    }

    /**
     * test WsException __construct
     * 
     * @covers WsException::__construct
     */
    public function testConstruct_errorlog(){
        global $Conf;
        $Conf['AutoLogging']['error'] = true;
        $message = 'Hello Error';
        $code    = 500;

        $time = time();
        $file = sprintf('%s/%s%s.%s', $Conf['Log']['dir'], $Conf['Log']['file']['ERROR'], date($Conf['Log']['add'], $time), $Conf['Log']['ext']);

        //もしファイルが存在していれば一時的に移動
        $tmp_file = null;
        if( is_file($file) ){
            $tmp_file = tempnam(sys_get_temp_dir(), 'WsExceptionTest');
            file_put_contents(
                  $tmp_file
                , file_get_contents($file)
            );
        }

        $wsex = new WsException($message, $code);
        $buff = explode($Conf['Log']['separate'], rtrim(file_get_contents($file)) );

        $this->assertFileExists($file);
        $this->assertEquals($buff[3], $code);
        $this->assertEquals($buff[5], $message);

        //後片付け
        unlink($file);
        if( $tmp_file !== null ){
            rename($tmp_file, $file);
       }
    }

}