<?php
require_once('conf.php');
require_once('lib/global.php');

class globalFunctionUnitTest extends PHPUnit_Framework_TestCase
{
    public function testGenuniqid(){
        $this->assertEquals(40, strlen(gen_uniqid()));
    }

    public function testConf(){
        global $Conf;
        $this->assertTrue(is_array($Conf));
    }
}