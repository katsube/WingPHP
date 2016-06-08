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
        
    }
    
    /**
     * Test cearList()
     * 
     * @covers Validation::clearList
     */
    public function testClearList(){
        
    }
    
    /**
     * Test getList()
     * 
     * @covers Validation::getList
     */
    public function testGetList(){
        
    }
    
    /**
     * Test addRule()
     * 
     * @covers Validation::addRule
     */
    public function testAddRule(){
        
    }
    
    /**
     * Test getRule()
     * 
     * @covers Validation::getRule
     */
    public function testGetRule(){
        
    }
    
    /**
     * Test addData()
     * 
     * @covers Validation::addData
     */
    public function testAddData(){
        
    }
    
    /**
     * Test getData()
     * 
     * @covers Validation::getData
     */
    public function testGetData(){
        
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
}