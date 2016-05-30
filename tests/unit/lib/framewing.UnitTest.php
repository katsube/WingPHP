<?php
require_once('define.php');

class FramewingUnitTest extends PHPUnit_Framework_TestCase
{
    /**
     * test Framewing __construct
     * 
     * @covers framewing::__construct
     * @dataProvider URLProvider
     */
    public function testConstruct($url, $expected){
        if( $url !== null )
            $_REQUEST['_q'] = $url;
    
        $wing = new framewing();
        
        $this->assertEquals($expected['ctrl'],   $wing->ctrl_name);
        $this->assertEquals($expected['method'], $wing->method_name);
        $this->assertEquals($expected['param'],  $wing->param);
    }

    /**
     * test Framewing __destruct
     * 
     * 現状処理がないのでテストは行わない。
     * 
     * @covers framewing::__destruct
     */
    public function testDestruct(){
        $wing = new framewing();
        $wing->__destruct();
    }

    /**
     * test Framewing go()
     * 
     *@covers framewing::go
     */
    public function testGo(){
        
    }
    
    /**
     * test Framewing _parse()
     * 
     *@covers framewing::_parse
     *@dataProvider URLProvider
     */
    public function testParse($url, $expected){
        if( $url !== null )
            $_REQUEST['_q'] = $url;

        $wing = new framewing();
        
        $this->assertEquals($expected['ctrl'],   $wing->ctrl_name);
        $this->assertEquals($expected['method'], $wing->method_name);
        $this->assertEquals($expected['param'],  $wing->param);
    }
    
     /**
     * test Framewing _exists_view()
     * 
     *@covers framewing::_exists_view
     */
    public function testExistsView(){
        
    }

    
    public function URLProvider(){
        return(array(
              array(null,                  ['ctrl'=>'IndexController', 'method'=>'index', 'param'=>[]])
            , array('/',                   ['ctrl'=>'IndexController', 'method'=>'index', 'param'=>[]])
            , array('/foo' ,               ['ctrl'=>'FooController',   'method'=>'index', 'param'=>[]])
            , array('/foo/',               ['ctrl'=>'FooController',   'method'=>'index', 'param'=>[]])
            , array('/foo/bar',            ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>[]])
            , array('/foo/bar/',           ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['']])
            , array('/foo/bar/hoge',       ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['hoge']])
            , array('/foo/bar/hoge/',      ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['hoge', '']])
            , array('/foo/bar/hoge/huga',  ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['hoge', 'huga']])
            , array('/foo/bar/hoge/huga/', ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['hoge', 'huga', '']])
        ));
    }
    
}