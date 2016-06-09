<?php
require_once('define.php');
require_once('../lib/Util/Validation/index.php');

class UtilValidationUnitTest extends PHPUnit_Framework_TestCase
{
 
    /**
     * Test Constructer
     * 
     * @covers Validation::__construct
     * @dataProvider ConstructProvider
     */
    public function testConstruct($mode, $lang){
        $keys = ['url', 'email', 'ip4', 'postcd', 'tel', 'alpha', 'alnum', 'require', 'bytemax', 'bytemin', 'max', 'min', 'match', 'eq', 'ne', 'in', 'date', 'time', 'grequire1', 'gin'];
        
        $v = new Validation($mode, $lang);
        $rule = $v->getRule();
        foreach( $keys as $key){
            $this->assertArrayHasKey($key,  $rule);
        }
    }

    /**
     * Test addList()
     * 
     * @covers Validation::addList
     */
    public function testAddList(){
        $v = new Validation();
        $list = array(
              'foo' => ['require']
            , 'bar' => ['require', ['max', 15]]
        );
        $list2 = array(
              'foo' => ['alnum']
            , 'bar' => ['require', ['max', 15]]
        );
        
        //登録されているか
        $v->addList($list);
        $this->assertEquals($list, $v->getList());

        //上書きされるか
        $v->addList(['foo' => ['alnum']]);
        $this->assertEquals($list2, $v->getList());
        
        //無効なリストを渡すとfalse
        $this->assertFalse( $v->addList() );
        $this->assertFalse( $v->addList('foo') );
        $this->assertFalse( $v->addList([123]) );
        $this->assertFalse( $v->addList([123=>456]) );
        $this->assertFalse( $v->addList(['foo'=>456]) );
        $this->assertFalse( $v->addList(['foo'=>['NotFound']]) );
    }

    /**
     * Test _validList()
     * 
     * @covers Validation::_validList
     * @dataProvider ValidListProvider
     */
    public function testValidationList($list, $expected){
        $v = TestPrivate::on( new Validation() );
        $this->assertEquals($expected, $v->_validList($list) );
    }
    
    /**
     * Test cearList()
     * 
     * @covers Validation::clearList
     */
    public function testClearList(){
        $v = new Validation();
        $v->addList(['foo'=>['max', 100]]);
        $v->clearList();

        $this->assertEquals(array(), $v->getList());
    }
    
    /**
     * Test getList()
     * 
     * @covers Validation::getList
     */
    public function testGetList(){
        $v    = new Validation();
        $list = array(
              'foo' => ['require']
            , 'bar' => ['require', ['max', 15]]
        );
        
        $v->addList($list);

        //すべて返却
        $this->assertEquals($list, $v->getList());
        
        //キー指定
        $this->assertEquals($list['foo'], $v->getList('foo'));
        $this->assertEquals($list['bar'], $v->getList('bar'));
        $this->assertFalse($v->getList('NotFound'));
    }
    
    /**
     * Test addRule()
     * 
     * @covers Validation::addRule
     */
    public function testAddRule(){
        $v    = new Validation();
        $func = function($val){ return(true); };

        //正常に登録
        $this->assertTrue($v->addRule('foo', $func));
        $this->assertEquals($v->getRule('foo'), $func);
        
        //登録されない
        $this->assertFalse($v->addRule('bar', 'string') );
        $this->assertFalse($v->addRule(12345, $func));
    }
    
    /**
     * Test getRule()
     * 
     * @covers Validation::getRule
     */
    public function testGetRule(){
        $v    = new Validation();
        $func1 = function($val){ return(true); };
        $func2 = function($val){ return(true); };
        $v->addRule('foo', $func1);
        $v->addRule('bar', $func2);
        
        //全件取得
        $rules = $v->getRule();
        $this->assertArrayHasKey('foo', $rules);
        $this->assertArrayHasKey('bar', $rules);
        $this->assertEquals($func1, $rules['foo']);
        $this->assertEquals($func2, $rules['bar']);
    
        //個別に取得
        $this->assertEquals( $func1, $v->getRule('foo') );
        $this->assertEquals( $func2, $v->getRule('bar') );
    
        //取得できない
        $this->assertFalse( $v->getRule('NotFound') );
    }
    
    /**
     * Test addData()
     * 
     * @covers Validation::addData
     * @dataProvider AddDataProvider
     */
    public function testAddData($data, $ret, $expected){
        $v = new Validation();

        $this->assertEquals($ret,      $v->addData($data));
        $this->assertEquals($expected, $v->getData());
    }

    /**
     * Test _validData()
     * 
     * @covers Validation::_validData
     * @dataProvider ValidDataProvider
     */
    public function testValidData($data, $expected){
        $v = TestPrivate::on( new Validation() );
        $this->assertEquals($expected, $v->_validData($data) );
    }
    
    /**
     * Test getData()
     * 
     * @covers Validation::getData
     */
    public function testGetData(){
        $data = array(
                      'foo'   => 'hello'
                    , 'bar'   => 12345
                    , 'hoge'  => true
                    , 'huga'  => false
                    , 'munya' => null
                    , 'arr'   => [1,2,3,4,5]
                );
        
        $v = new Validation();
        $v->addData($data);
    
        //全件取得
        $this->assertEquals($data, $v->getData());
        
        //個別に取得
        foreach($data as $key => $value){
            $this->assertEquals($value, $v->getData($key));
        }
        
        //存在しない
        $this->assertFalse( $v->getData('404') );
    }
    
    /**
     * Test clearData()
     * 
     * @covers Validation::clearData
     */
    public function testClearData(){
        
    }
    
    /**
     * Test check()
     * 
     * @covers Validation::check
     */
    public function testCheck(){
        
    }
    
    /**
     * Test setError2Scratch()
     * 
     * @covers Validation::setError2Scratch
     */
    public function testSetError2Scratch(){
        
    }
    
    /**
     * Test getError()
     * 
     * @covers Validation::getError
     */
    public function testGetError(){
        
    }
    
    /**
     * Test addError()
     * 
     * @covers Validation::addError
     */
    public function testAddError(){
        
    }

    
    
    
    
    
    public function ConstructProvider(){
        return(array(
              array(null,   null)
            , array('self', null)
            , array('form', null)
            , array(null,   'ja')
            , array('self', 'ja')
            , array('form', 'ja')
        ));
    }
    
    public function ValidListProvider(){
        return(array(
              array(['foo'=>['require']], true)
            , array(['foo'=>['require', ['max', 15]]], true)
            , array(['foo'=>['NotFound']], false)
            , array(['foo'=>12345], false)
            , array(null,   false)
            , array('foo',  false)
            , array(12345,  false)
        ));
    }
    
    public function ValidDataProvider(){
        return(array(
                  array([],       false)
                , array(null,     false)
                , array('foobar', false)
                , array(['foo', 'bar', 'hoge'], false)
                , array([1234=>'foo'],    false)
                , array(['1234'=>'foo'],    false)

                , array(['foo'=>1234],    true)
                , array(['foo'=>'Hello'], true)
                , array(['foo'=>null],    true)
                , array(['foo'=>''],      true)
                , array(['foo'=>false],   true)
                , array(['foo'=>true],    true)
                , array(['foo'=>1234, 'bar'=>'Hello'], true)
                , array(['foo'=>1234, 'bar'=>'Hello', 'hoge'=>null], true)
                , array(['日本語'=>1234], true)
        ));
    }

    public function AddDataProvider(){
        return(array(
                  array([], false, [])
                , array(null, false, [])
                , array('foo', false, [])
                , array(['foo','bar','hoge'], false, [])
                , array([1234=>'foo'], false, [])
                , array(['1234'=>'foo'], false, [])
                
                , array(['foo'=>1234], true, ['foo'=>1234])
                , array(['foo'=>'hello'], true, ['foo'=>'hello'])
                , array(['foo'=>null],    true, ['foo'=>null])
                , array(['foo'=>''],      true, ['foo'=>''])
                , array(['foo'=>false],   true, ['foo'=>false])
                , array(['foo'=>true],    true, ['foo'=>true])
                , array(['foo'=>1234, 'bar'=>'Hello'], true, ['foo'=>1234, 'bar'=>'Hello'])
                , array(['foo'=>1234, 'bar'=>'Hello', 'hoge'=>null], true, ['foo'=>1234, 'bar'=>'Hello', 'hoge'=>null])
                , array(['日本語'=>1234], true, ['日本語'=>1234])
        ));
        
    }
}