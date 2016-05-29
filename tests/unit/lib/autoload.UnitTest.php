<?php
require_once('define.php');

class autoloadUnitTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * test _wingAutoload()
     * 
     * @covers ::_wingAutoload
     * @runInSeparateProcess
     */
    public function testFunction_wingAutoload1(){
        $ret = _wingAutoload('BaseModel');
        $this->assertEquals('../model/base.php', $ret);

        $ret = _wingAutoload('BaseController');
        $this->assertEquals('../controller/base.php', $ret);
    }

    /**
     * test _wingAutoload()
     * 
     * @covers ::_wingAutoload
     * @runInSeparateProcess
     * @runInSeparateProcess
     * @expectedException WsException
     * @expectedExceptionCode 500
     */
    public function testFunction_wingAutoload2(){
        _wingAutoload('NotFoundTestModel');
    }


    /**
     * test _wingAutoload()
     * 
     * @covers ::_wingAutoload
     * @runInSeparateProcess
     */
    public function testFunction_wingAutoload3(){
        $ret = _wingAutoload('Smarty_Internal_TemplateBase');
        $this->assertFalse($ret);
    }


    /**
     * test _wingAutoload()
     * 
     * @covers ::_wingAutoload
     * @runInSeparateProcess
     */
    public function testFunction_wingAutoload4(){
        global $Conf;
        $path = sprintf('%s/Cache/index.php', $Conf['Lib']['dir']);
        
        $ret = _wingAutoload('Cache');
        $this->assertContains($path, $ret);
    }
    
}