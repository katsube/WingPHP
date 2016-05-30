<?php
require_once('define.php');

class routingUnitTest extends PHPUnit_Framework_TestCase
{
    /**
     * test Routing Class
     * 
     *@covers Routing::__construct
     */
    public function testConstruct(){
        $ctrl   = 'FooController';
        $method = 'bar';
        $param  = array('hoge', 'huga');
        
        $wing = new framewing();
        $wing->ctrl_name   = $ctrl;
        $wing->method_name = $method;
        $wing->param       = $param;

        $route = new Routing($wing);
        $this->assertEquals( $route->ctrl,   $ctrl);
        $this->assertEquals( $route->method, $method);
        $this->assertEquals( $route->param,  $param );
    }
    

}