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
     * @covers framewing::go
     * @dataProvider GoProvider
     * @runInSeparateProcess
     */
    public function testGo($ctrl, $method, $param, $code){
        $wing = new framewing();
        $wing->ctrl_name   = $ctrl;
        $wing->method_name = $method;
        $wing->param       = $param;
        
        ob_start();
        $ret = $wing->go();
        ob_end_clean();
        
        $this->assertEquals($ret, $code);
    }

    /**
     * test Framewing go() - SmartyDirect
     * 
     * @covers framewing::go
     * @dataProvider GoSmartyDirectProvider
     * @runInSeparateProcess
     */
    public function testGo_Smartydirect($url, $code){
        $_REQUEST['_q'] = $url;
        $wing = new framewing();
        
        ob_start();
        $ret  = $wing->go();
        ob_end_clean();
        
        $this->assertEquals($ret, $code);
    }
    
    /**
     * test Framewing _parse()
     * 
     * @covers framewing::_parse
     * @dataProvider URLProvider
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
     * @covers framewing::_exists_view
     */
    public function testExistsView_run(){
        global $Conf;
        $Conf['SmartyDirect']['run'] = false;

        $wing = TestPrivate::on( new framewing() );
        $ret  = $wing->_exists_view();
        $this->assertFalse($ret);
    }

    /**
     * test Framewing _exists_view()
     * 
     * @covers framewing::_exists_view
     * @dataProvider ExistsViewProvider
     */
    public function testExistsView_exists($url, $result){
        global $Conf;
        $Conf['SmartyDirect']['run'] = true;
        $_REQUEST['_q'] = $url;
    
        $wing = TestPrivate::on( new framewing() );
        $ret  = $wing->_exists_view();
        $this->assertEquals($ret, $result);
    }





    /**
     * [DataProvider] testConstruct(), testParse()
     * 
     * @param void
     * @return array
     * @access public
     */
    public function URLProvider(){
        return(array(
              array(null,                  ['ctrl'=>'IndexController', 'method'=>'index', 'param'=>[]])
            , array('',                    ['ctrl'=>'IndexController', 'method'=>'index', 'param'=>[]])

            , array('/',                   ['ctrl'=>'IndexController', 'method'=>'index', 'param'=>[]])
            , array('/foo' ,               ['ctrl'=>'FooController',   'method'=>'index', 'param'=>[]])
            , array('/foo/',               ['ctrl'=>'FooController',   'method'=>'index', 'param'=>[]])
            , array('/foo/bar',            ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>[]])
            , array('/foo/bar/',           ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['']])
            , array('/foo/bar/hoge',       ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['hoge']])
            , array('/foo/bar/hoge/',      ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['hoge', '']])
            , array('/foo/bar/hoge/huga',  ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['hoge', 'huga']])
            , array('/foo/bar/hoge/huga/', ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['hoge', 'huga', '']])
            
            , array('foo' ,               ['ctrl'=>'FooController',   'method'=>'index', 'param'=>[]])
            , array('foo/',               ['ctrl'=>'FooController',   'method'=>'index', 'param'=>[]])
            , array('foo/bar',            ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>[]])
            , array('foo/bar/',           ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['']])
            , array('foo/bar/hoge',       ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['hoge']])
            , array('foo/bar/hoge/',      ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['hoge', '']])
            , array('foo/bar/hoge/huga',  ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['hoge', 'huga']])
            , array('foo/bar/hoge/huga/', ['ctrl'=>'FooController',   'method'=>'bar',   'param'=>['hoge', 'huga', '']])
        ));
    }
    
    /**
     * [DataProvider] testGo()
     * 
     * @param void
     * @return array
     * @access public
     */
    public function GoProvider(){
        return(array(
              array('UndefinedController', 'index',         array(), 404)       //存在しない
            , array('BaseController',      'display',       array(), 404)       //スーパークラス
            , array('TestsController',     '_cannotaccess', array(), 404)       //先頭に'_'がつくpublicなメソッド
            , array('TestsController',     'foobar',        array(), 404)       //privateなメソッド
            , array('TestsController',     'canaccess',     array(), 200)       //正常な呼び出し
        ));
    }
    
    /**
     * [DataProvider] testGo_smarty()
     * 
     * @param void
     * @return array
     * @access public
     */
    public function GoSmartyDirectProvider(){
        return(array(
              array('/smartydirect/index.html', 201)
            , array('/smartydirect/', 201)
        ));
    }

    /**
     * [DataProvider] testExistsView_exists()
     * 
     * @param void
     * @return array
     * @access public
     */
    public function ExistsViewProvider(){
        return(array(
              array('/smartydirect/index.html', true)
            , array('/smartydirect/', true)                     //デフォルトファイル名
            , array('/smartydirect/404.html', false)            //存在しない
            , array('/smartydirect/index.html | ls', false)     //汚染
        ));
    }
    
}