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
                    , 'float' => 12.345
                    , 'hoge'  => true
                    , 'huga'  => false
                    , 'munya' => null
                    , 'arr1'  => [1,2,3,4,5]
                    , 'arr2'  => ['foo'=>'bar']
                    , 'func'  => function(){ return(true); }
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
        $data = array(
                      'foo' => 'hello'
                    , 'bar' => 12345
                );
        
        $v = new Validation();
        $v->addData($data);
        $v->clearData();
        
        $this->assertEquals(array(), $v->getData());
    }
    
    /**
     * Test check() - 404
     * 
     * @covers Validation::check
     * @dataProvider CheckProvider404
     */
    public function testCheck404($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Require
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderRequire
     */
    public function testCheckRequire($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - URL
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderURL
     */
    public function testCheckURL($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Email
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderEmail
     */
    public function testCheckEmail($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - IP4
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderIP4
     */
    public function testCheckIP4($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - PostCD
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderPostCD
     */
    public function testCheckPostCD($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Tel
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderTel
     */
    public function testCheckTel($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Num
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderNum
     */
    public function testCheckNum($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Alpha
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderAlpha
     */
    public function testCheckAlpha($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Alnum
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderAlnum
     */
    public function testCheckAlnum($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - ByteMax
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderByteMax
     */
    public function testCheckByteMax($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - ByteMin
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderByteMin
     */
    public function testCheckByteMin($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Max
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderMax
     */
    public function testCheckMax($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Min
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderMin
     */
    public function testCheckMin($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Match
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderMatch
     */
    public function testCheckMatch($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Eq
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderEq
     */
    public function testCheckEq($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Ne
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderNe
     */
    public function testCheckNe($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - In
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderIn
     */
    public function testCheckIn($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Date
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderDate
     */
    public function testCheckDate($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - Time
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderTime
     */
    public function testCheckTime($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - GRequire1
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderGRequire1
     */
    public function testCheckGRequire1($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - GIn
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderGIn
     */
    public function testCheckGIn($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    /**
     * Test check() - 複合テスト
     * 
     * @covers Validation::check
     * @dataProvider CheckProviderComposite
     */
    public function testCheckComposite($list, $data, $expected_flag, $expected_error){
        $this->_CheckLogic($list, $data, $expected_flag, $expected_error);
    }

    private function _CheckLogic($list, $data, $expected_flag, $expected_error){
        $v = new Validation();
        $v->addList($list);
        $v->addData($data);
        $result = $v->check();
        $error  = $v->getError();
        
        $this->assertEquals( $expected_flag, $result, print_r($result, true) );
        $this->assertEquals( $expected_error, $error, print_r($error, true) );
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

    public function CheckProvider404(){
        // 無効な検証名を渡すと、検証リストに登録されないため
        // その項目はtrueとなる
        return(array(
              array(['foo'=>['NotFound']],      ['foo'=>1], true, [])
            , array(['foo'=>[['NotFound', 1]]], ['foo'=>1], true, [])
        ));
    }

    public function CheckProviderRequire(){
        return(array(
            //------------------------
            // require            
            //------------------------
              array(['foo'=>['require']], ['foo'=>1],                          true, [])

            , array(['foo'=>['require']], ['foo'=>0],                          true, [])
            , array(['foo'=>['require']], ['foo'=>0.12345],                    true, [])
            , array(['foo'=>['require']], ['foo'=>'Hello'],                    true, [])
            , array(['foo'=>['require']], ['foo'=>true],                       true, [])
            , array(['foo'=>['require']], ['foo'=>false],                      true, [])
            , array(['foo'=>['require']], ['foo'=>[1,2,3,4,5]],                true, [])
            , array(['foo'=>['require']], ['foo'=>['hoge'=>123, 'huga'=>456]], true, [])

            , array(['foo'=>['require']], ['foo'=>null],  false, ['foo'=>['require']])
            , array(['foo'=>['require']], ['foo'=>''],    false, ['foo'=>['require']])
            , array(['foo'=>['require']], ['foo'=>[]],    false, ['foo'=>['require']])
        ));
    }

    public function CheckProviderURL(){
        return(array(
            //------------------------
            // url
            //------------------------
              array(['foo'=>['url']], ['foo'=>'http://wingphp.net/'],  true, [])
            , array(['foo'=>['url']], ['foo'=>'https://wingphp.net/'], true, [])
            , array(['foo'=>['url']], ['foo'=>null],                   true, [])
            , array(['foo'=>['url']], ['foo'=>''],                     true, [])

            , array(['foo'=>['url']], ['foo'=>'foobar'],    false, ['foo'=>['url']])
            , array(['foo'=>['url']], ['foo'=>1],           false, ['foo'=>['url']])
            , array(['foo'=>['url']], ['foo'=>0],           false, ['foo'=>['url']])
            , array(['foo'=>['url']], ['foo'=>0.12345],     false, ['foo'=>['url']])
            , array(['foo'=>['url']], ['foo'=>[]],          false, ['foo'=>['url']])
            , array(['foo'=>['url']], ['foo'=>[1,2,3,4,5]], false, ['foo'=>['url']])
            , array(['foo'=>['url']], ['foo'=>true],        false, ['foo'=>['url']])
            , array(['foo'=>['url']], ['foo'=>false],       false, ['foo'=>['url']])

        ));
    }

    public function CheckProviderEmail(){
        return(array(
            //------------------------
            // email
            //------------------------
              array(['foo'=>['email']], ['foo'=>'katsubemakito@gmail.com'], true, [])
            , array(['foo'=>['email']], ['foo'=>null],                      true, [])
            , array(['foo'=>['email']], ['foo'=>''],                        true, [])

            , array(['foo'=>['email']], ['foo'=>'foobar'],     false, ['foo'=>['email']])
            , array(['foo'=>['email']], ['foo'=>'foobar@'],    false, ['foo'=>['email']])
            , array(['foo'=>['email']], ['foo'=>'@gmail.com'], false, ['foo'=>['email']])
            , array(['foo'=>['email']], ['foo'=>1],            false, ['foo'=>['email']])
            , array(['foo'=>['email']], ['foo'=>0],            false, ['foo'=>['email']])
            , array(['foo'=>['email']], ['foo'=>0.12345],      false, ['foo'=>['email']])
            , array(['foo'=>['email']], ['foo'=>[]],           false, ['foo'=>['email']])
            , array(['foo'=>['email']], ['foo'=>[1,2,3,4,5]],  false, ['foo'=>['email']])
            , array(['foo'=>['email']], ['foo'=>true],         false, ['foo'=>['email']])
            , array(['foo'=>['email']], ['foo'=>false],        false, ['foo'=>['email']])
        ));
    }

    public function CheckProviderIP4(){
        return(array(
            //------------------------
            // ip4
            //------------------------
              array(['foo'=>['ip4']], ['foo'=>'192.168.0.1'], true, [])
            , array(['foo'=>['ip4']], ['foo'=>null],          true, [])
            , array(['foo'=>['ip4']], ['foo'=>''],            true, [])

            , array(['foo'=>['ip4']], ['foo'=>'192.168.'],    false, ['foo'=>['ip4']])
            , array(['foo'=>['ip4']], ['foo'=>1],             false, ['foo'=>['ip4']])
            , array(['foo'=>['ip4']], ['foo'=>0],             false, ['foo'=>['ip4']])
            , array(['foo'=>['ip4']], ['foo'=>0.12345],       false, ['foo'=>['ip4']])
            , array(['foo'=>['ip4']], ['foo'=>[]],            false, ['foo'=>['ip4']])
            , array(['foo'=>['ip4']], ['foo'=>[1,2,3,4,5]],   false, ['foo'=>['ip4']])
            , array(['foo'=>['ip4']], ['foo'=>true],          false, ['foo'=>['ip4']])
            , array(['foo'=>['ip4']], ['foo'=>false],         false, ['foo'=>['ip4']])
        ));
    }


    public function CheckProviderPostCD(){
        return(array(
            //------------------------
            // postcd
            //------------------------
              array(['foo'=>['postcd']], ['foo'=>'123-4567'],  true, [])
            , array(['foo'=>['postcd']], ['foo'=>'1234567'],   true, [])
            , array(['foo'=>['postcd']], ['foo'=>null],        true, [])
            , array(['foo'=>['postcd']], ['foo'=>''],          true, [])

            , array(['foo'=>['postcd']], ['foo'=>'123'],       false, ['foo'=>['postcd']])
            , array(['foo'=>['postcd']], ['foo'=>1],           false, ['foo'=>['postcd']])
            , array(['foo'=>['postcd']], ['foo'=>0],           false, ['foo'=>['postcd']])
            , array(['foo'=>['postcd']], ['foo'=>0.12345],     false, ['foo'=>['postcd']])
            , array(['foo'=>['postcd']], ['foo'=>[]],          false, ['foo'=>['postcd']])
            , array(['foo'=>['postcd']], ['foo'=>[1,2,3,4,5]], false, ['foo'=>['postcd']])
            , array(['foo'=>['postcd']], ['foo'=>true],        false, ['foo'=>['postcd']])
            , array(['foo'=>['postcd']], ['foo'=>false],       false, ['foo'=>['postcd']])
        ));
    }

    public function CheckProviderTel(){
        return(array(
            //------------------------
            // tel
            //------------------------
              array(['foo'=>['tel']], ['foo'=>'03-1111-1111'], true, [])
            , array(['foo'=>['tel']], ['foo'=>null],           true, [])
            , array(['foo'=>['tel']], ['foo'=>''],             true, [])

            , array(['foo'=>['tel']], ['foo'=>'1234567890'],   false, ['foo'=>['tel']])
            , array(['foo'=>['tel']], ['foo'=>1],              false, ['foo'=>['tel']])
            , array(['foo'=>['tel']], ['foo'=>0],              false, ['foo'=>['tel']])
            , array(['foo'=>['tel']], ['foo'=>0.12345],        false, ['foo'=>['tel']])
            , array(['foo'=>['tel']], ['foo'=>[]],             false, ['foo'=>['tel']])
            , array(['foo'=>['tel']], ['foo'=>[1,2,3,4,5]],    false, ['foo'=>['tel']])
            , array(['foo'=>['tel']], ['foo'=>true],           false, ['foo'=>['tel']])
            , array(['foo'=>['tel']], ['foo'=>false],          false, ['foo'=>['tel']])
        ));
    }

    public function CheckProviderNum(){
        return(array(
            //------------------------
            // num
            //------------------------
              array(['foo'=>['num']], ['foo'=>'1234567890'], true, [])
            , array(['foo'=>['num']], ['foo'=>'1'],          true, [])
            , array(['foo'=>['num']], ['foo'=>'0'],          true, [])
            , array(['foo'=>['num']], ['foo'=>'0.12345'],    true, [])
            , array(['foo'=>['num']], ['foo'=>'-10'],        true, [])
            , array(['foo'=>['num']], ['foo'=>'-10.5'],      true, [])
            , array(['foo'=>['num']], ['foo'=>1],            true, [])
            , array(['foo'=>['num']], ['foo'=>0],            true, [])
            , array(['foo'=>['num']], ['foo'=>0.12345],      true, [])
            , array(['foo'=>['num']], ['foo'=>-10],          true, [])
            , array(['foo'=>['num']], ['foo'=>-10.5],        true, [])
            , array(['foo'=>['num']], ['foo'=>null],         true, [])
            , array(['foo'=>['num']], ['foo'=>''],           true, [])

            , array(['foo'=>['num']], ['foo'=>'10,000'],     false, ['foo'=>['num']])
            , array(['foo'=>['num']], ['foo'=>[]],           false, ['foo'=>['num']])
            , array(['foo'=>['num']], ['foo'=>[1,2,3,4,5]],  false, ['foo'=>['num']])
            , array(['foo'=>['num']], ['foo'=>true],         false, ['foo'=>['num']])
            , array(['foo'=>['num']], ['foo'=>false],        false, ['foo'=>['num']])
        ));
    }

    public function CheckProviderAlpha(){
        return(array(
            //------------------------
            // alpha
            //------------------------
              array(['foo'=>['alpha']], ['foo'=>'abcdefg'],     true, [])
            , array(['foo'=>['alpha']], ['foo'=>'ABCDEFG'],     true, [])
            , array(['foo'=>['alpha']], ['foo'=>'HelloWorld'],  true, [])
            , array(['foo'=>['alpha']], ['foo'=>null],          true, [])
            , array(['foo'=>['alpha']], ['foo'=>''],            true, [])

            , array(['foo'=>['alpha']], ['foo'=>'HelloWorld!'], false, ['foo'=>['alpha']])
            , array(['foo'=>['alpha']], ['foo'=>'1234567890'],  false, ['foo'=>['alpha']])
            , array(['foo'=>['alpha']], ['foo'=>'10,000'],      false, ['foo'=>['alpha']])
            , array(['foo'=>['alpha']], ['foo'=>1],             false, ['foo'=>['alpha']])
            , array(['foo'=>['alpha']], ['foo'=>0],             false, ['foo'=>['alpha']])
            , array(['foo'=>['alpha']], ['foo'=>0.12345],       false, ['foo'=>['alpha']])
            , array(['foo'=>['alpha']], ['foo'=>[]],            false, ['foo'=>['alpha']])
            , array(['foo'=>['alpha']], ['foo'=>[1,2,3,4,5]],   false, ['foo'=>['alpha']])
            , array(['foo'=>['alpha']], ['foo'=>true],          false, ['foo'=>['alpha']])
            , array(['foo'=>['alpha']], ['foo'=>false],         false, ['foo'=>['alpha']])
        ));
    }

    public function CheckProviderAlnum(){
        return(array(
            //------------------------
            // alnum
            //------------------------
              array(['foo'=>['alnum']], ['foo'=>'abcdefg'],       true, [])
            , array(['foo'=>['alnum']], ['foo'=>'ABCDEFG'],       true, [])
            , array(['foo'=>['alnum']], ['foo'=>'HelloWorld'],    true, [])
            , array(['foo'=>['alnum']], ['foo'=>'123abcdefg'],    true, [])
            , array(['foo'=>['alnum']], ['foo'=>'123ABCDEFG'],    true, [])
            , array(['foo'=>['alnum']], ['foo'=>'123HelloWorld'], true, [])
            , array(['foo'=>['alnum']], ['foo'=>'1234567890'],    true, [])
            , array(['foo'=>['alnum']], ['foo'=>1234567890],      true, [])
            , array(['foo'=>['alnum']], ['foo'=>1],               true, [])
            , array(['foo'=>['alnum']], ['foo'=>0],               true, [])
            , array(['foo'=>['alnum']], ['foo'=>0.12345],         true, [])
            , array(['foo'=>['alnum']], ['foo'=>-10],             true, [])
            , array(['foo'=>['alnum']], ['foo'=>-10.5],           true, [])
            , array(['foo'=>['alnum']], ['foo'=>null],            true, [])
            , array(['foo'=>['alnum']], ['foo'=>''],              true, [])

            , array(['foo'=>['alnum']], ['foo'=>'HelloWorld!'],   false, ['foo'=>['alnum']])
            , array(['foo'=>['alnum']], ['foo'=>'10,000'],        false, ['foo'=>['alnum']])
            , array(['foo'=>['alnum']], ['foo'=>[]],              false, ['foo'=>['alnum']])
            , array(['foo'=>['alnum']], ['foo'=>[1,2,3,4,5]],     false, ['foo'=>['alnum']])
            , array(['foo'=>['alnum']], ['foo'=>true],            false, ['foo'=>['alnum']])
            , array(['foo'=>['alnum']], ['foo'=>false],           false, ['foo'=>['alnum']])
        ));
    }


    public function CheckProviderByteMax(){
        return(array(
            //------------------------
            // bytemax
            //------------------------
              array(['foo'=>[['bytemax',10]]], ['foo'=>'Hello'], true, [])
            , array(['foo'=>[['bytemax', 5]]], ['foo'=>'Hello'], true, [])
            , array(['foo'=>[['bytemax',10]]], ['foo'=>null],    true, [])
            , array(['foo'=>[['bytemax',10]]], ['foo'=>''],      true, [])

            , array(['foo'=>[['bytemax', 5]]], ['foo'=>'HelloWorld!'],   false, ['foo'=>['bytemax']])
            , array(['foo'=>[['bytemax',10]]], ['foo'=>[]],              false, ['foo'=>['bytemax']])
            , array(['foo'=>[['bytemax',10]]], ['foo'=>[1,2,3,4,5]],     false, ['foo'=>['bytemax']])
            , array(['foo'=>[['bytemax',10]]], ['foo'=>true],            false, ['foo'=>['bytemax']])
            , array(['foo'=>[['bytemax',10]]], ['foo'=>false],           false, ['foo'=>['bytemax']])
        ));
    }

    public function CheckProviderByteMin(){
        return(array(
            //------------------------
            // bytemin
            //------------------------
              array(['foo'=>[['bytemin', 1]]], ['foo'=>'Hello'], true, [])
            , array(['foo'=>[['bytemin', 5]]], ['foo'=>'Hello'], true, [])
            , array(['foo'=>[['bytemin', 1]]], ['foo'=>null],    true, [])
            , array(['foo'=>[['bytemin', 1]]], ['foo'=>''],      true, [])

            , array(['foo'=>[['bytemin',99]]], ['foo'=>'HelloWorld!'],   false, ['foo'=>['bytemin']])
            , array(['foo'=>[['bytemin', 1]]], ['foo'=>[]],              false, ['foo'=>['bytemin']])
            , array(['foo'=>[['bytemin', 1]]], ['foo'=>[1,2,3,4,5]],     false, ['foo'=>['bytemin']])
            , array(['foo'=>[['bytemin', 1]]], ['foo'=>true],            false, ['foo'=>['bytemin']])
            , array(['foo'=>[['bytemin', 1]]], ['foo'=>false],           false, ['foo'=>['bytemin']])

        ));
    }

    public function CheckProviderMax(){
        return(array(
            //------------------------
            // max
            //------------------------
              array(['foo'=>[['max',10]]], ['foo'=>5],       true, [])
            , array(['foo'=>[['max', 5]]], ['foo'=>5],       true, [])
            , array(['foo'=>[['max',10]]], ['foo'=>null],    true, [])
            , array(['foo'=>[['max',10]]], ['foo'=>''],      true, [])

            , array(['foo'=>[['max', 5]]], ['foo'=>'HelloWorld!'],   false, ['foo'=>['max']])
            , array(['foo'=>[['max',10]]], ['foo'=>[]],              false, ['foo'=>['max']])
            , array(['foo'=>[['max',10]]], ['foo'=>[1,2,3,4,5]],     false, ['foo'=>['max']])
            , array(['foo'=>[['max',10]]], ['foo'=>true],            false, ['foo'=>['max']])
            , array(['foo'=>[['max',10]]], ['foo'=>false],           false, ['foo'=>['max']])
        ));
    }

    public function CheckProviderMin(){
        return(array(
            //------------------------
            // min
            //------------------------
              array(['foo'=>[['min', 1]]], ['foo'=>10],      true, [])
            , array(['foo'=>[['min', 5]]], ['foo'=> 5],      true, [])
            , array(['foo'=>[['min', 1]]], ['foo'=>null],    true, [])
            , array(['foo'=>[['min', 1]]], ['foo'=>''],      true, [])

            , array(['foo'=>[['min',99]]], ['foo'=>'HelloWorld!'],   false, ['foo'=>['min']])
            , array(['foo'=>[['min', 1]]], ['foo'=>[]],              false, ['foo'=>['min']])
            , array(['foo'=>[['min', 1]]], ['foo'=>[1,2,3,4,5]],     false, ['foo'=>['min']])
            , array(['foo'=>[['min', 1]]], ['foo'=>true],            false, ['foo'=>['min']])
            , array(['foo'=>[['min', 1]]], ['foo'=>false],           false, ['foo'=>['min']])
        ));
    }

    public function CheckProviderMatch(){
        return(array(
            //------------------------
            // match
            //------------------------
              array(['foo'=>[['match', '/^Hello/']]],  ['foo'=>'HelloWorld'],   true, [])
            , array(['foo'=>[['match', '/^Hello$/']]], ['foo'=>'Hello'],        true, [])
            , array(['foo'=>[['match', '/foo/']]],     ['foo'=>null],           true, [])
            , array(['foo'=>[['match', '/bar/']]],     ['foo'=>''],             true, [])

            , array(['foo'=>[['match', '/^Hello/']]],  ['foo'=>'bar'],           false, ['foo'=>['match']])
            , array(['foo'=>[['match', []]]],          ['foo'=>[]],              false, ['foo'=>['match']])
            , array(['foo'=>[['match', [1,2,3,4,5]]]], ['foo'=>[1,2,3,4,5]],     false, ['foo'=>['match']])
            , array(['foo'=>[['match', true]]],        ['foo'=>true],            false, ['foo'=>['match']])
            , array(['foo'=>[['match', false]]],       ['foo'=>false],           false, ['foo'=>['match']])
        ));
    }

    public function CheckProviderEq(){
        return(array(
            //------------------------
            // eq
            //------------------------
              array(['foo'=>[['eq', 'Hello']]],       ['foo'=>'Hello'],         true, [])
            , array(['foo'=>[['eq', 'foo']]],         ['foo'=>null],            true, [])
            , array(['foo'=>[['eq', 'bar']]],         ['foo'=>''],              true, [])
            , array(['foo'=>[['eq', []]]],            ['foo'=>[]],              true, [])
            , array(['foo'=>[['eq', [1,2,3,4,5]]]],   ['foo'=>[1,2,3,4,5]],     true, [])
            , array(['foo'=>[['eq', true]]],          ['foo'=>true],            true, [])
            , array(['foo'=>[['eq', false]]],         ['foo'=>false],           true, [])

            , array(['foo'=>[['eq', 'Hello']]],       ['foo'=>'World'],         false, ['foo'=>['eq']])
            , array(['foo'=>[['eq', []]]],            ['foo'=>[1,2,3,4,5]],     false, ['foo'=>['eq']])
            , array(['foo'=>[['eq', [1,2,3,4,5]]]],   ['foo'=>[]],              false, ['foo'=>['eq']])
            , array(['foo'=>[['eq', true]]],          ['foo'=>false],           false, ['foo'=>['eq']])
            , array(['foo'=>[['eq', false]]],         ['foo'=>true],            false, ['foo'=>['eq']])
        ));
    }

    public function CheckProviderNe(){
        return(array(
            //------------------------
            // ne
            //------------------------
              array(['foo'=>[['ne', 'Hello']]],       ['foo'=>'World'],         true, [])
            , array(['foo'=>[['ne', []]]],            ['foo'=>[1,2,3,4,5]],     true, [])
            , array(['foo'=>[['ne', [1,2,3,4,5]]]],   ['foo'=>[]],              true, [])
            , array(['foo'=>[['ne', true]]],          ['foo'=>false],           true, [])
            , array(['foo'=>[['ne', false]]],         ['foo'=>true],            true, [])
            , array(['foo'=>[['ne', 'foo']]],         ['foo'=>null],            true, [])
            , array(['foo'=>[['ne', 'bar']]],         ['foo'=>''],              true, [])

            , array(['foo'=>[['ne', 'Hello']]],       ['foo'=>'Hello'],         false, ['foo'=>['ne']])
            , array(['foo'=>[['ne', []]]],            ['foo'=>[]],              false, ['foo'=>['ne']])
            , array(['foo'=>[['ne', [1,2,3,4,5]]]],   ['foo'=>[1,2,3,4,5]],     false, ['foo'=>['ne']])
            , array(['foo'=>[['ne', true]]],          ['foo'=>true],            false, ['foo'=>['ne']])
            , array(['foo'=>[['ne', false]]],         ['foo'=>false],           false, ['foo'=>['ne']])
        ));
    }

    public function CheckProviderIn(){
        return(array(
            //------------------------
            // in
            //------------------------
              array(['foo'=>[['in', [1,2,3]]]],             ['foo'=>2],         true, [])
            , array(['foo'=>[['in', [1,2,3]]]],             ['foo'=>3],         true, [])
            , array(['foo'=>[['in', [1.2,2.3,3.5]]]],       ['foo'=>1.2],       true, [])
            , array(['foo'=>[['in', [1.2,2.3,3.5]]]],       ['foo'=>2.3],       true, [])
            , array(['foo'=>[['in', [1.2,2.3,3.5]]]],       ['foo'=>3.5],       true, [])
            , array(['foo'=>[['in', ['Hello', 'World']]]],  ['foo'=>'Hello'],   true, [])
            , array(['foo'=>[['in', ['Hello', 'World']]]],  ['foo'=>'World'],   true, [])
            , array(['foo'=>[['in', [true, false]]]],       ['foo'=>true],      true, [])
            , array(['foo'=>[['in', [true, false]]]],       ['foo'=>false],     true, [])
            , array(['foo'=>[['in', 1]]],                   ['foo'=>1],         true, [])

            , array(['foo'=>[['in', [1,2,3]]]],             ['foo'=>100],       false, ['foo'=>['in']])
            , array(['foo'=>[['in', [1,2,3]]]],             ['foo'=>200],       false, ['foo'=>['in']])
            , array(['foo'=>[['in', [1,2,3]]]],             ['foo'=>300],       false, ['foo'=>['in']])
            , array(['foo'=>[['in', [1.2,2.3,3.5]]]],       ['foo'=>1.21],      false, ['foo'=>['in']])
            , array(['foo'=>[['in', [1.2,2.3,3.5]]]],       ['foo'=>2.31],      false, ['foo'=>['in']])
            , array(['foo'=>[['in', [1.2,2.3,3.5]]]],       ['foo'=>3.51],      false, ['foo'=>['in']])
            , array(['foo'=>[['in', ['Hello', 'World']]]],  ['foo'=>'foo'],     false, ['foo'=>['in']])
            , array(['foo'=>[['in', ['Hello', 'World']]]],  ['foo'=>'bar'],     false, ['foo'=>['in']])
            , array(['foo'=>[['in', [true, false]]]],       ['foo'=>1],         false, ['foo'=>['in']])
            , array(['foo'=>[['in', [true, false]]]],       ['foo'=>'foo'],     false, ['foo'=>['in']])
            , array(['foo'=>[['in', 1]]],                   ['foo'=>2],         false, ['foo'=>['in']])
        ));
    }

    public function CheckProviderDate(){
        return(array(
            //------------------------
            // date
            //------------------------
              array(['foo'=>[['date', 10,  5]]],  ['foo'=> 1979], true, [])
            , array(['foo'=>[['date',  1,  1]]],  ['foo'=>    1], true, [])
            , array(['foo'=>[['date', 12, 31]]],  ['foo'=>32767], true, [])
            , array(['foo'=>[['date',  2, 29]]],  ['foo'=> 2016], true, [])
            , array(['foo'=>[['date',  1,  1]]],  ['foo'=> null], true, [])
            , array(['foo'=>[['date',  1,  1]]],  ['foo'=>   ''], true, [])

            , array(['foo'=>[['date',    2,   29]]],   ['foo'=> 1979],   false, ['foo'=>['date']])
            , array(['foo'=>[['date',   10,    5]]],   ['foo'=>'1979'],  false, ['foo'=>['date']])
            , array(['foo'=>[['date',  '10',   5]]],   ['foo'=> 1979],   false, ['foo'=>['date']])
            , array(['foo'=>[['date',   10,   '5']]],  ['foo'=> 1979],   false, ['foo'=>['date']])
            , array(['foo'=>[['date',   10,    5]]],   ['foo'=> true],   false, ['foo'=>['date']])
            , array(['foo'=>[['date', true,    5]]],   ['foo'=> 1979],   false, ['foo'=>['date']])
            , array(['foo'=>[['date',   10, true]]],   ['foo'=> 1979],   false, ['foo'=>['date']])
            , array(['foo'=>[['date',   10,    5]]],   ['foo'=>   []],   false, ['foo'=>['date']])
            , array(['foo'=>[['date',   [],    5]]],   ['foo'=> 1979],   false, ['foo'=>['date']])
            , array(['foo'=>[['date',   10,    []]]],  ['foo'=> 1979],   false, ['foo'=>['date']])
            , array(['foo'=>[['date',   10,    5]]],   ['foo'=> 1979.0], false, ['foo'=>['date']])
            , array(['foo'=>[['date',   10.0,  5]]],   ['foo'=> 1979],   false, ['foo'=>['date']])
            , array(['foo'=>[['date',   10,    5.0]]], ['foo'=> 1979],   false, ['foo'=>['date']])
        ));
    }

    public function CheckProviderTime(){
        return(array(
            //------------------------
            // time
            //------------------------
              array(['foo'=>[['time', 10,  5]]],  ['foo'=>   10], true, [])
            , array(['foo'=>[['time',  0,  0]]],  ['foo'=>    0], true, [])
            , array(['foo'=>[['time', 59, 59]]],  ['foo'=>   23], true, [])
            , array(['foo'=>[['time',  1,  1]]],  ['foo'=> null], true, [])
            , array(['foo'=>[['time',  1,  1]]],  ['foo'=>   ''], true, [])

            , array(['foo'=>[['time',    0,    0]]],   ['foo'=>   24],   false, ['foo'=>['time']])
            , array(['foo'=>[['time',   10,    5]]],   ['foo'=>  '10'],  false, ['foo'=>['time']])
            , array(['foo'=>[['time',  '10',   5]]],   ['foo'=>   10],   false, ['foo'=>['time']])
            , array(['foo'=>[['time',   10,   '5']]],  ['foo'=>   10],   false, ['foo'=>['time']])
            , array(['foo'=>[['time',   10,    5]]],   ['foo'=> true],   false, ['foo'=>['time']])
            , array(['foo'=>[['time', true,    5]]],   ['foo'=>   10],   false, ['foo'=>['time']])
            , array(['foo'=>[['time',   10, true]]],   ['foo'=>   10],   false, ['foo'=>['time']])
            , array(['foo'=>[['time',   10,    5]]],   ['foo'=>   []],   false, ['foo'=>['time']])
            , array(['foo'=>[['time',   [],    5]]],   ['foo'=>   10],   false, ['foo'=>['time']])
            , array(['foo'=>[['time',   10,    []]]],  ['foo'=>   10],   false, ['foo'=>['time']])
            , array(['foo'=>[['time',   10,    5]]],   ['foo'=>   10.0], false, ['foo'=>['time']])
            , array(['foo'=>[['time',   10.0,  5]]],   ['foo'=>   10],   false, ['foo'=>['time']])
            , array(['foo'=>[['time',   10,    5.0]]], ['foo'=>   10],   false, ['foo'=>['time']])
        ));
    }

    public function CheckProviderGRequire1(){
        return(array(
            //------------------------
            // grequire1
            //------------------------
              array(['foo'=>['grequire1']], ['foo'=>[   1,     2,    3]], true, [])
            , array(['foo'=>['grequire1']], ['foo'=>[   1,  null, null]], true, [])
            , array(['foo'=>['grequire1']], ['foo'=>[   1,     1, null]], true, [])
            , array(['foo'=>['grequire1']], ['foo'=>[null,     1,    1]], true, [])
            , array(['foo'=>['grequire1']], ['foo'=>[null,  null,    1]], true, [])
            , array(['foo'=>['grequire1']], ['foo'=>[true]],              true, [])
            , array(['foo'=>['grequire1']], ['foo'=>[false]],             true, [])
            , array(['foo'=>['grequire1']], ['foo'=>[[]]],                true, [])
            , array(['foo'=>['grequire1']], ['foo'=>null],                true, [])
            , array(['foo'=>['grequire1']], ['foo'=>''],                  true, [])
            
            , array(['foo'=>['grequire1']], ['foo'=>[]],                 false, ['foo'=>['grequire1']])
            , array(['foo'=>['grequire1']], ['foo'=>[null]],             false, ['foo'=>['grequire1']])
            , array(['foo'=>['grequire1']], ['foo'=>[null, null, null]], false, ['foo'=>['grequire1']])
            , array(['foo'=>['grequire1']], ['foo'=>'string'],           false, ['foo'=>['grequire1']])
            , array(['foo'=>['grequire1']], ['foo'=>true],               false, ['foo'=>['grequire1']])
            , array(['foo'=>['grequire1']], ['foo'=>false],              false, ['foo'=>['grequire1']])
        ));
    }

    public function CheckProviderGIn(){
        return(array(
            //------------------------
            // gin
            //------------------------
              array(['foo'=>[['gin', [1,2,3]]]], ['foo'=>[1]],     true, [])
            , array(['foo'=>[['gin', [1,2,3]]]], ['foo'=>[2]],     true, [])
            , array(['foo'=>[['gin', [1,2,3]]]], ['foo'=>[3]],     true, [])
            , array(['foo'=>[['gin', [1,2,3]]]], ['foo'=>[1,2]],   true, [])
            , array(['foo'=>[['gin', [1,2,3]]]], ['foo'=>[2,3]],   true, [])
            , array(['foo'=>[['gin', [1,2,3]]]], ['foo'=>[1,3]],   true, [])
            , array(['foo'=>[['gin', [1,2,3]]]], ['foo'=>[1,2,3]], true, [])

            , array(['foo'=>[['gin', ['foo', 'bar', 'hoge']]]], ['foo'=>['foo']],                true, []) 
            , array(['foo'=>[['gin', ['foo', 'bar', 'hoge']]]], ['foo'=>['bar']],                true, [])
            , array(['foo'=>[['gin', ['foo', 'bar', 'hoge']]]], ['foo'=>['hoge']],               true, [])
            , array(['foo'=>[['gin', ['foo', 'bar', 'hoge']]]], ['foo'=>['foo', 'bar']],         true, [])
            , array(['foo'=>[['gin', ['foo', 'bar', 'hoge']]]], ['foo'=>['bar', 'hoge']],        true, [])
            , array(['foo'=>[['gin', ['foo', 'bar', 'hoge']]]], ['foo'=>['foo', 'hoge']],        true, [])
            , array(['foo'=>[['gin', ['foo', 'bar', 'hoge']]]], ['foo'=>['foo', 'bar', 'hoge']], true, [])

            , array(['foo'=>[['gin', []]]],       ['foo'=>[]],       true, [])
            , array(['foo'=>[['gin', [0]]]],      ['foo'=>[0]],      true, [])
            , array(['foo'=>[['gin', [1]]]],      ['foo'=>[1]],      true, [])
            , array(['foo'=>[['gin', [1.2345]]]], ['foo'=>[1.2345]], true, [])
            , array(['foo'=>[['gin', [true]]]],   ['foo'=>[true]],   true, [])
            , array(['foo'=>[['gin', [false]]]],  ['foo'=>[false]],  true, [])
            , array(['foo'=>[['gin', [null]]]],   ['foo'=>[null]],   true, [])
 
            , array(['foo'=>[['gin', [1,2,3]]]], ['foo'=>null],  true, [])
            , array(['foo'=>[['gin', [1,2,3]]]], ['foo'=>''],    true, [])
        
            , array(['foo'=>[['gin', [1,2,3]]]],              ['foo'=>[4,5,6]],                false, ['foo'=>['gin']])
            , array(['foo'=>[['gin', ['foo','bar','hoge']]]], ['foo'=>['xxx', 'yyy', 'zzzz']], false, ['foo'=>['gin']])

            , array(['foo'=>[['gin', [1]]]],       ['foo'=>[0]],       false, ['foo'=>['gin']])
            , array(['foo'=>[['gin', [0]]]],       ['foo'=>[1]],       false, ['foo'=>['gin']])
            , array(['foo'=>[['gin', [1.23]]]],    ['foo'=>[1.234]],   false, ['foo'=>['gin']])
            , array(['foo'=>[['gin', [1.234]]]],   ['foo'=>[1.23]],    false, ['foo'=>['gin']])
            , array(['foo'=>[['gin', ['hello']]]], ['foo'=>['Hello']], false, ['foo'=>['gin']])
            , array(['foo'=>[['gin', [true]]]],    ['foo'=>[false]],   false, ['foo'=>['gin']])
            , array(['foo'=>[['gin', [false]]]],   ['foo'=>[true]],    false, ['foo'=>['gin']])
            , array(['foo'=>[['gin', [null]]]],    ['foo'=>['']],      false, ['foo'=>['gin']])
        ));
    }
    
    
    public function CheckProviderComposite(){
        return(array(
              array(['foo'=>['require', 'alnum']], ['foo'=>'HelloWorld'], true,  [])
            , array(['foo'=>['require', 'alnum']], ['foo'=>null],         false, ['foo'=>['require']])

            , array(['foo'=>['alnum', ['bytemin', 5], ['bytemax', 10]]], ['foo'=>'abcdefg'], true, [])
            , array(['foo'=>['alnum', ['bytemin', 5], ['bytemax', 10]]], ['foo'=>'abcdefghijklmn'], false, ['foo'=>['bytemax']])
            , array(['foo'=>['alnum', ['bytemin', 5], ['bytemax', 10]]], ['foo'=>'abc'], false, ['foo'=>['bytemin']])
            , array(['foo'=>['alnum', ['bytemin', 5], ['bytemax', 10]]], ['foo'=>'あ'], false, ['foo'=>['alnum', 'bytemin']])
        ));
    }
}