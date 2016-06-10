 <?php
require_once('define.php');
require_once('../lib/Util/Validation/index.php');

class UtilValidationRuleClosureUnitTest extends PHPUnit_Framework_TestCase
{
    private $rule;
    
    /**
     * SetUp
     * 
     * @covers Validation::__construct
     * @uses Validation::getRule
     */
    function setUp(){
        $v = new Validation();
        $this->rule = $v->getRule();
    }


    /**
     * Test Rule - url
     *
     * @dataProvider UrlProvider
     */
    public function testUrl($url, $expected){
        $rule = $this->rule['url'];
        $this->assertEquals( $expected, $rule($url) );
    }

    /**
     * Test Rule - email
     * 
     * @dataProvider EmailProvider
     */
    public function testEmail($email, $expected){
        $rule = $this->rule['email'];
        $this->assertEquals( $expected, $rule($email) );
    }

    /**
     * Test Rule - ip4
     * 
     * @dataProvider IP4Provider
     */
    public function testIP4($ip4, $expected){
        $rule = $this->rule['ip4'];
        $this->assertEquals( $expected, $rule($ip4) );
    }

    /**
     * Test Rule - postcd
     * 
     * @dataProvider PostcdProvider
     */
    public function testPostcd($postcd, $expected){
        $rule = $this->rule['postcd'];
        $this->assertEquals( $expected, $rule($postcd) );
    }

    /**
     * Test Rule - tel
     * 
     * @dataProvider TelProvider
     */
    public function testTEL($tel, $expected){
        $rule = $this->rule['tel'];
        $this->assertEquals( $expected, $rule($tel) );
    }

    /**
     * Test Rule - num
     * 
     * @dataProvider NumProvider
     */
    public function testNum($num, $expected){
        $rule = $this->rule['num'];
        $this->assertEquals( $expected, $rule($num) );
    }

    /**
     * Test Rule - alpha
     * 
     * @dataProvider AlphaProvider
     */
    public function testAlpha($alpha, $expected){
        $rule = $this->rule['alpha'];
        $this->assertEquals( $expected, $rule($alpha) );
    }
        
    /**
     * Test Rule - alnum
     * 
     * @dataProvider AlnumProvider
     */
    public function testAlnum($alnum, $expected){
        $rule = $this->rule['alnum'];
        $this->assertEquals( $expected, $rule($alnum) );
    }

    /**
     * Test Rule - require
     * 
     * @dataProvider RequireProvider
     */
    public function testRequire($require, $expected){
        $rule = $this->rule['require'];
        $this->assertEquals( $expected, $rule($require) );
    }

    /**
     * Test Rule - bytemax
     * 
     * @dataProvider ByteMaxProvider
     */
    public function testByteMax($target, $max, $expected){
        $rule = $this->rule['bytemax'];
        $this->assertEquals( $expected, $rule($target, [$max]) );
    }

    /**
     * Test Rule - bytemin
     * 
     * @dataProvider ByteMinProvider
     */
    public function testByteMin($target, $min, $expected){
        $rule = $this->rule['bytemin'];
        $this->assertEquals( $expected, $rule($target, [$min]) );
    }

    /**
     * Test Rule - max
     * 
     * @dataProvider MaxProvider
     */
    public function testMax($target, $max, $expected){
        $rule = $this->rule['max'];
        $this->assertEquals( $expected, $rule($target, [$max]) );
    }

    /**
     * Test Rule - min
     * 
     * @dataProvider MinProvider
     */
    public function testMin($target, $min, $expected){
        $rule = $this->rule['min'];
        $this->assertEquals( $expected, $rule($target, [$min]) );
    }


    /**
     * Test Rule - match
     * 
     * @dataProvider MatchProvider
     */
    public function testMatch($target, $pattern, $expected){
        $rule = $this->rule['match'];
        $this->assertEquals( $expected, $rule($target, [$pattern]) );
    }

    /**
     * Test Rule - eq
     * 
     * @dataProvider EqProvider
     */
    public function testEq($target1, $target2, $expected){
        $rule = $this->rule['eq'];
        $this->assertEquals( $expected, $rule($target1, [$target2]) );
    }

    /**
     * Test Rule - ne
     * 
     * @dataProvider NeProvider
     */
    public function testNe($target1, $target2, $expected){
        $rule = $this->rule['ne'];
        $this->assertEquals( $expected, $rule($target1, [$target2]) );
    }

    /**
     * Test Rule - in
     * 
     * @dataProvider InProvider
     */
    public function testIn($target, $arr, $expected){
        $rule = $this->rule['in'];
        $this->assertEquals( $expected, $rule($target, $arr) );
    }

    /**
     * Test Rule - date
     * 
     * @dataProvider DateProvider
     */
    public function testDate($yyyy, $mm, $dd, $expected){
        $rule = $this->rule['date'];
        $this->assertEquals( $expected, $rule($yyyy, [$mm, $dd]) );
    }


    /**
     * Test Rule - time
     * 
     * @dataProvider TimeProvider
     */
    public function testTime($hh, $mm, $ss, $expected){
        $rule = $this->rule['time'];
        $this->assertEquals( $expected, $rule($hh, [$mm, $ss]) );
    }    

    /**
     * Test Rule - grequire1
     * 
     * @dataProvider Grequire1Provider
     */
    public function testGrequire1($target, $expected){
        $rule = $this->rule['grequire1'];
        $this->assertEquals( $expected, $rule($target) );
    }

    /**
     * Test Rule - gin
     * 
     * @dataProvider GinProvider
     */
    public function testGin($target, $reference, $expected){
        $rule = $this->rule['gin'];
        $this->assertEquals( $expected, $rule($target, $reference) );
    }





    public function UrlProvider(){
        return(array(
              array('http://wingphp.net', true)
            , array('http://wingphp.net/', true)
            , array('http://www.wingphp.net/', true)
            , array('http://www.wingphp.net/?q=hello', true)
            , array('http://www.wingphp.net/?q=hello&lang=ja', true)
            , array('https://www.wingphp.net/', true)
            , array('http://localhost/', true)
            , array('http://127.0.0.1/', true)
            , array('', true)
            , array(null, true)
            
            , array('http//www.wingphp.net/?q=hello', false)
            , array('http:/www.wingphp.net/?q=hello', false)
            , array('//www.wingphp.net/?q=hello', false)
            , array('www.wingphp.net', false)
            , array('http://日本語ドメイン.net/', false)
        ));
    }

    public function EmailProvider(){
        return(array(
              array('katsubemakito@gmail.com', true)
            , array('katsube.makito@gmail.com', true)
            , array('katsube.makito+wingphp@gmail.com', true)
            , array('katsube.makito+1234@gmail.com', true)
            , array('katsube.makito+_-/@gmail.com', true)
            , array('+@gmail.com', true)
            , array('katsubemakito@example_-.com', true)
            , array('katsubemakito@example.co.jp', true)

            , array('', true)
            , array(null, true)

            , array('katsubemakito@gmail', false)
            , array('katsubemakito@', false)
            , array('katsubemakito', false)
            , array('@gmail.com', false)
            , array('@gmail', false)
            , array('@', false)
            , array('かつべ@gmail.com', false)
            , array('katsubemakito@じーめーる.com', false)
        ));
    }    

    public function IP4Provider(){
        return(array(
              array('0.0.0.0', true)
            , array('127.0.0.1', true)
            , array('255.255.255.255', true)
            
            , array('', true)
            , array(null, true)

            , array('256.255.255.255', false)
            , array('255.256.255.255', false)
            , array('255.255.256.255', false)
            , array('255.255.255.256', false)
            , array('256.256.256.256', false)
            , array('266.266.266.266', false)
            , array('.0.0.0', false)
            , array('0..0.0', false)
            , array('0.0..0', false)
            , array('0.0.0.', false)
            , array('0.0.', false)
            , array('0.', false)
            , array('0', false)
        ));
    }

    public function PostcdProvider(){
        return(array(
              array('1000002', true)
            , array(1000002, true)
            , array('100-0002', true)

            , array('', true)
            , array(null, true)

            , array('-1000002', false)
            , array('1-000002', false)
            , array('10-00002', false)
            , array('1000-002', false)
            , array('10000-02', false)
            , array('100000-2', false)
            , array('1000002-', false)
            , array('１００−０００２', false)
        ));
    }

    public function TelProvider(){
        return(array(
            array('090-1234-5678', true)
        ));
    }
    
    public function NumProvider(){
        return(array(
            array(123456789, true)
        ));
    }

    public function AlphaProvider(){
        return(array(
            array('Hello', true)
        ));
    }

    public function AlnumProvider(){
        return(array(
            array('Hello123', true)
        ));
    }

    public function RequireProvider(){
        return(array(
            array('foobar', true)
        ));
    }

    public function ByteMaxProvider(){
        return(array(
            array('foobar', 100, true)
        ));
    }

    public function ByteMinProvider(){
        return(array(
            array('foobar', 1, true)
        ));
    }

    public function MaxProvider(){
        return(array(
            array(10, 100, true)
        ));
    }

    public function MinProvider(){
        return(array(
            array(10, 1, true)
        ));
    }

    public function MatchProvider(){
        return(array(
            array('HelloWorld', '/^Hello/', true)
        ));
    }

    public function EqProvider(){
        return(array(
            array('Hello', 'Hello', true)
        ));
    }

    public function NeProvider(){
        return(array(
            array('Hello', 'World', true)
        ));
    }

    public function InProvider(){
        return(array(
            array('fizz', ['foo', 'bar', 'fizz', 'buzz'], true)
        ));
    }

    public function DateProvider(){
        return(array(
              array( 2016, 10,  5,  true)
            , array( 2016, 10,  5,  true)
            , array(    1,  1,  1,  true)
            , array(32767, 12, 31,  true)
            , array( 2015,  2, 29, false)
            , array( 2015, 15,  1, false)
            , array( 2015,  1, 32, false)
        ));
    }

    public function TimeProvider(){
        return(array(
              array( 0,  0,  0,  true)
            , array(23, 59, 59,  true)
            , array(24,  0,  0, false)
            , array( 0, 60,  0, false)
            , array( 0,  0, 60, false)
        ));
    }

    public function Grequire1Provider(){
        return(array(
              array([1,2,3], true)
            , array([null,1,null], true)
            , array([null,1,null], true)
            , array('not array', false)
            , array([], false)
            , array([null,null,null], false)
            , array([''], false)
        ));
    }

    public function GinProvider(){
        return(array(
              array([], [], true)
            , array([1], [1], true)
            , array([''], [''], true)
            , array(['Hello'], ['Hello'], true)
            , array([0], [0], true)
            , array([false], [false], true)
            , array([null], [null], true)
            , array([123.456], [123.456], true)
            , array([1,2,3], [1,2,3], true)
            , array(['Hello', 'World', 'Foo', 'Bar'], ['Hello', 'World', 'Foo', 'Bar'], true)
            , array(['Foo'], ['Bar', 'Hello', 'World', 'Foo'], true)

            , array([1], [0], false)
            , array(['Hello'], ['hello'], false)
            , array(['Hello'], ['World'], false)
            , array([false], [true], false)
            , array([null], [''], false)
            , array([123.4567], [123.456], false)
            , array([1,2,3], [4,5,6], false)
            , array(['Hello', 'World', 'Foo', 'Bar'], ['Hello', 'World', 'Foo'], false)
        ));
    }


}