 <?php
require_once('define.php');
require_once('../lib/Util/Validation/index.php');

class UtilValidationRuleClosureUnitTest extends PHPUnit_Framework_TestCase
{
    private $rule;
    
    /**
     * SetUp
     * 
     */
    function setUp(){
        $v = new Validation();
        $this->rule = $v->getRule();
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
    public function testTel($tel, $expected){
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



    /**
     * DataProvider - Require
     * 
     */
    public function RequireProvider(){
        return(array(
            array('foobar', true)
            , array('a', true)
            , array('1', true)
            , array('12345', true)
            , array(1, true)
            , array(12345, true)
            , array(12.345, true)
            , array(true, true)
            , array(false, true)

            , array('', false)
            , array(null, false)
            , array([], false)
        ));
    }

    /**
     * DataProvider - URL
     * 
     */
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
        
            , array(1, false)
            , array(0, false)
            , array(0.12345, false)
            , array([], false)
            , array(true, false)
            , array(false, false)
        ));
    }

    /**
     * DataProvider - Email
     * 
     */
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

            , array(1, false)
            , array(0, false)
            , array(0.12345, false)
            , array([], false)
            , array(true, false)
            , array(false, false)
        ));
    }    

    /**
     * DataProvider - IP4
     * 
     */
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

            , array(1, false)
            , array(0, false)
            , array(0.12345, false)
            , array([], false)
            , array(true, false)
            , array(false, false)
        ));
    }

    /**
     * DataProvider - PostCD
     * 
     */
    public function PostcdProvider(){
        return(array(
              array('1000002', true)
            , array('100-0002', true)

            , array('', true)
            , array(null, true)

            , array(1000002, false)
            , array('-1000002', false)
            , array('1-000002', false)
            , array('10-00002', false)
            , array('1000-002', false)
            , array('10000-02', false)
            , array('100000-2', false)
            , array('1000002-', false)
            , array('１００−０００２', false)

            , array(1, false)
            , array(0, false)
            , array(0.12345, false)
            , array([], false)
            , array(true, false)
            , array(false, false)
        ));
    }

    /**
     * DataProvider - Tel
     * 
     */
    public function TelProvider(){
        return(array(
            //固定電話等
              array('03-1234-5678', true)       //0A-BCDE-FGHJ
            , array('055-123-4567', true)       //0AB-CDE-FGHJ
            , array('0852-55-4567', true)       //0ABC-DE-FGHJ
            , array('01234-6-7890', true)       //0ABCD-E-FGHJ

            //発信者課金ポケベル電話番号
            , array('020-123-4567', true)       //020-CDE-FGHJK

            //IP電話の電話番号
            , array('050-1234-5678', true)      //050-CDEF-GHJK

            //FMC電話番号
            , array('060-1234-5678', true)      //060-CDEF-GHJK

            //PHS電話番号
            , array('070-123-45678', true)      //070-CDE-FGHJK

            //携帯電話の電話番号
            , array('080-123-45678', true)      //080-CDE-FGHJK
            , array('090-123-45678', true)      //090-CDE-FGHJK
            
            //着信課金用電話番号
            , array('0120-123-456', true)       //0120-DEF-GHJ
            , array('0120-123-4567', true)      //0800-DEF-GHJK

            //統一番号用電話番号
            , array('0570-123-456', true)       //0570-DEF-GHJ

            //情報料代理徴収用電話番号            
            , array('0990-123-456', true)       //0990-DEF-GHJ
            
            , array('', true)
            , array(null, true)
            

            , array('0901234-5678', false)      //書式エラー
            , array('09012345678', false)       //書式エラー
            , array('90-1234-5678', false)      //先頭が0から始まらない
            , array('090-1２34-5678', false)    //全角まじり
            , array('050-CDEF-GHJK', false)     //数字じゃない

            , array(1, false)
            , array(0, false)
            , array(0.12345, false)
            , array([], false)
            , array(true, false)
            , array(false, false)
        ));
    }
    
    /**
     * DataProvider - Num
     * 
     */
    public function NumProvider(){
        return(array(
              array(12345, true)
            , array('12345', true)
            , array(0, true)
            , array(-100, true)
            , array(123.456, true)

            , array('', true)
            , array(null, true)

            , array('1234５', false)
            , array('１２３４５', false)
            , array('abcde', false)
            , array(true, false)
            , array(false, false)
            , array([], false)
        ));
    }

    /**
     * DataProvider - Alpha
     * 
     */
    public function AlphaProvider(){
        return(array(
              array('hello', true)
            , array('Hello', true)
            , array('HELLO', true)
            , array('a', true)

            , array('', true)
            , array(null, true)

            , array('Hello World', false)       //半角スペース
            , array('HelloWorld!', false)       //記号
            , array('12345', false)
            , array('12.345', false)
            , array('Hello1', false)
            , array('Ａbcd', false)             //全角まじり
            , array(true, false)
            , array(false, false)
            , array([], false)
        ));
    }

    /**
     * DataProvider - Alnum
     * 
     */
    public function AlnumProvider(){
        return(array(
              array('hello', true)
            , array('Hello', true)
            , array('HELLO', true)
            , array('a', true)
            , array('1', true)
            , array('12345', true)
            , array(1, true)
            , array(12345, true)
            , array('Hello12345', true)

            , array('', true)
            , array(null, true)

            , array('Hello World', false)       //半角スペース
            , array('HelloWorld!', false)       //記号
            , array('-12345', false)
            , array('12.345', false)
            , array('Ａbcd', false)             //全角まじり
            , array('１2345', false)            //全角まじり
            , array(true, false)
            , array(false, false)
            , array([], false)
        ));
    }

    /**
     * DataProvider - ByteMax
     * 
     */
    public function ByteMaxProvider(){
        return(array(
              array('foobar', 100, true)
            , array('a', 1, true)
            , array('あ', 3, true)          //UTF8前提

            , array('', 1, true)
            , array(null, 1, true)
            
            , array('foobar', 1, false)
            , array(1, 100, false)
            , array(12345, 100, false)
            , array(123.45, 100, false)
            , array([], 100, false)
            , array(true, 100,false)
            , array(false, 100, false)
        ));
    }

    /**
     * DataProvider - ByteMin
     * 
     */
    public function ByteMinProvider(){
        return(array(
              array('foobar', 1, true)
            , array('a', 1, true)
            , array('あ', 3, true)          //UTF8前提

            , array('', 1, true)
            , array(null, 1, true)
            
            , array('foobar', 100, false)
            , array(1, 100, false)
            , array(12345, 100, false)
            , array(123.45, 100, false)
            , array([], 100, false)
            , array(true, 100,false)
            , array(false, 100, false)
        ));
    }

    /**
     * DataProvider - Max
     * 
     */
    public function MaxProvider(){
        return(array(
              array(10, 100, true)
            , array(1, 1, true)
            , array(0, 0, true)
            , array(3.1, 3.2, true)
            , array(3.1, 3.1, true)
            , array(-5, 0, true)
            , array(-10, -5, true)

            , array('', 1, true)
            , array(null, 1, true)
            
            , array(200, 100, false)
            , array('foobar', 100, false)
            , array([], 100, false)
            , array(true, 100,false)
            , array(false, 100, false)
        ));
    }

    /**
     * DataProvider - Min
     * 
     */
    public function MinProvider(){
        return(array(
              array(100, 10, true)
            , array(1, 1, true)
            , array(0, 0, true)
            , array(3.2, 3.1, true)
            , array(3.1, 3.1, true)
            , array(0, -5, true)
            , array(-5, -10, true)

            , array('', 1, true)
            , array(null, 1, true)
            
            , array(100, 200, false)
            , array('foobar', 100, false)
            , array([], 100, false)
            , array(true, 100,false)
            , array(false, 100, false)
        ));
    }

    /**
     * DataProvider - Match
     * 
     */
    public function MatchProvider(){
        return(array(
              array('HelloWorld', '/^Hello/', true)
            , array('HelloWorld', '/^hello/i', true)
            , array('12345', '/^12345$/', true)

            , array('', '//', true)
            , array(null, '//', true)

            , array('HelloWorld!', '/d$/', false)
            , array(12345, '/^12345$/', false)
            , array(1, '', false)
            , array(0, '', false)
            , array(0.12345, '', false)
            , array([], '', false)
            , array(true, '', false)
            , array(false, '', false)
        ));
    }

    /**
     * DataProvider - Eq
     * 
     */
    public function EqProvider(){
        return(array(
              array('HelloWorld', 'HelloWorld', true)
            , array(12345, 12345, true)
            , array(-12345, -12345, true)
            , array(123.45, 123.45, true)
            , array([], [], true)
            , array([1,2,3], [1,2,3], true)
            , array(true, true,true)
            , array(false, false, true)

            , array('', 'foo', true)
            , array(null, 'foo', true)

            , array('HelloWorld!', 'Hello', false)
            , array(12345, '12345', false)
        ));
    }

    /**
     * DataProvider - Ne
     * 
     */
    public function NeProvider(){
        return(array(
              array('Hello', 'World', true)
            , array(12345, '12345', true)
            , array(12345, 54321, true)
            , array(-12345, -54321, true)
            , array(123.45, 123.451, true)
            , array([1,2,3], [4,5,6], true)
            , array(true, false,true)
            , array(false, true, true)

            , array('', 'foo', true)
            , array(null, 'foo', true)

            , array('HelloWorld!', 'HelloWorld!', false)
            , array([], [], false)
        ));
    }

    /**
     * DataProvider - In
     * 
     */
    public function InProvider(){
        return(array(
              array('fizz', ['foo', 'bar', 'fizz', 'buzz'], true)
            , array(128, [1,2,4,8,16,32,64,128,256,512,1024], true)
            , array(true, [true, false], true)
            , array(false, [true, false], true)
            , array([], [[], [1,2,3], ['a','b','c']], true)

            , array('', 'foo', true)
            , array(null, 'foo', true)

            , array('fizz', ['foo', 'bar', 'buzz'], false)
            , array(18, [1,2,4,8,16,32,64,128,256,512,1024], false)
            , array('hoge', [true, false], false)
            , array('huga', [true, false], false)
            , array([], [[1,2,3], ['a','b','c']], false)
        ));
    }

    /**
     * DataProvider - Data
     * 
     */
    public function DateProvider(){
        return(array(
              array( 2016, 10,  5,  true)
            , array( 2016, 10,  5,  true)
            , array(    1,  1,  1,  true)
            , array(32767, 12, 31,  true)

            , array('', 1, 1, true)
            , array(null, 1, 1, true)
            
            , array( 2015,  2, 29, false)
            , array( 2015, 15,  1, false)
            , array( 2015,  1, 32, false)
            
            , array( '2005', 1, 1, false)
            , array( 2005, '1', 1, false)
            , array( 2005, 1, '1', false)
            , array( [], 1, 1, false)
            , array( 2005, [], 1, false)
            , array( 2005, 1, [], false)
            , array( true, 1, 1, false)
            , array( 2005, true, 1, false)
            , array( 2005, 1, true, false)
            , array( false, 1, 1, false)
            , array( 2005, false, 1, false)
            , array( 2005, 1, false, false)
        ));
    }

    /**
     * DataProvider - Time
     * 
     */
    public function TimeProvider(){
        return(array(
              array( 0,  0,  0,  true)
            , array(23, 59, 59,  true)

            , array('', 1, 1, true)
            , array(null, 1, 1, true)

            , array(24,  0,  0, false)
            , array( 0, 60,  0, false)
            , array( 0,  0, 60, false)

            , array( '23', 1, 1, false)
            , array( 23, '1', 1, false)
            , array( 23, 1, '1', false)
            , array( [], 1, 1, false)
            , array( 23, [], 1, false)
            , array( 23, 1, [], false)
            , array( true, 1, 1, false)
            , array( 23, true, 1, false)
            , array( 23, 1, true, false)
            , array( false, 1, 1, false)
            , array( 23, false, 1, false)
            , array( 23, 1, false, false)
        ));
    }

    /**
     * DataProvider - GRequire
     * 
     */
    public function Grequire1Provider(){
        return(array(
              array([1,2,3], true)
            , array([null,1,null], true)
            , array([null,1,null], true)

            , array('', true)
            , array(null, true)

            , array('not array', false)
            , array([], false)
            , array([null,null,null], false)
            , array([''], false)
        ));
    }

    /**
     * DataProvider - GIn
     * 
     */
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

            , array('', [], true)
            , array(null, [], true)

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