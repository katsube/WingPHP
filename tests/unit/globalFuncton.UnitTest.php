<?php
require_once('../conf.php');
require_once('../lib/global.php');

class globalFunctionUnitTest extends PHPUnit_Framework_TestCase
{
    
    const LOCKFWRITE_TMPNAME = '__TEST_PHPUNIT_';
    const GENUNIQID_LEN      = 40;                  //gen_uniqid() 生成される文字列長
    const GENUNIQID_LOOP     = 10000;               //gen_uniqid() ユニーク性を検証する個数

    /**
     * test uselib()
     */
    public function testFunction_uselib(){
        
    }

    /**
     * test location()
     */
    public function testFunction_location(){
        
    }

    /**
     * test http_error()
     */
    public function testFunction_http_error(){
        //$_SERVER['REQUEST_URI'] = '/hoge';
        
        //$this->expectOutputString('/message/error/500/hoge');
        //http_error(500);
    }

    /**
     * test addlogfile() - 1
     */
    public function testFunction_addlogfile1(){
        global $Conf;
        $time = time();

        //$Confにない識別子を与えると(もしくは何も与えないと)falseが返されるか
        $this->assertFalse( addlogfile() );
        $this->assertFalse( addlogfile('%%__FOOBAR__%%', $time) );
        
        //ログファイルが正しく生成されるか
        $filename = $this->_addlogfile_makefilename($time);
        $this->assertTrue( addlogfile('TEST', $str1) );
        $this->assertFileExists($filename);

        unlink($filename);
    }

    /**
     * test addlogfile() - 2
     */
    public function testFunction_addlogfile2(){
        global $Conf;
        $time     = time();
        $filename = $this->_addlogfile_makefilename($time);
        $str1     = 'HelloWorld';
        
        //ログファイルに記録されるか
        addlogfile('TEST', $str1);
        $buff = explode($Conf['Log']['separate'], rtrim(file_get_contents($filename)) );
        $this->assertEquals($buff[2], $str1);
        unlink($filename);
    }
    
    /**
     * test addlogfile() - 3
     */
    public function testFunction_addlogfile3(){
        global $Conf;
        $time     = time();
        $filename = $this->_addlogfile_makefilename($time);
        $str1     = 'HelloWorld';
        $str2     = 'fizzbuzz';
        $str3     = 'foobar';

        //ログファイルに記録されるか (可変長引数)
        addlogfile('TEST', $str1, $str2, $str3);
        $buff = explode($Conf['Log']['separate'], rtrim(file_get_contents($filename)) );
        $this->assertEquals($buff[2], $str1);
        $this->assertEquals($buff[3], $str2);
        $this->assertEquals($buff[4], $str3);
        unlink($filename);
    }
    
    /**
     * test addlogfile() - 4
     */
    public function testFunction_addlogfile4(){
        global $Conf;
        $time     = time();
        $filename = $this->_addlogfile_makefilename($time);
        $str1     = 'HelloWorld';
        $str2     = 'fizzbuzz';
        $str3     = 'foobar';
        $arr      = array('apple', 'banana', 'muscat');
    
        //ログファイルに記録されるか (配列が展開されるか)
        addlogfile('TEST', $str1, $arr, $str2, $str3);
        $buff = explode($Conf['Log']['separate'], rtrim(file_get_contents($filename)) );
        $this->assertEquals($buff[2], $str1);
        $this->assertEquals($buff[3], $arr[0]);
        $this->assertEquals($buff[4], $arr[1]);
        $this->assertEquals($buff[5], $arr[2]);
        $this->assertEquals($buff[6], $str2);
        $this->assertEquals($buff[7], $str3);
        unlink($filename);
    }

    /**
     * test addlogfile() - 5
     */
    public function testFunction_addlogfile5(){
        global $Conf;
        $time     = time();
        $filename = $this->_addlogfile_makefilename($time);
        $str1     = sprintf("Hello%sWorld", $Conf['Log']['separate']);
        $str2     = "fizz\nbuzz";
        $str3     = "foo\rbar";

        //ログファイルに記録される際に特定文字列が削除されるか
        addlogfile('TEST', $str1, $str2, $str3);
        $buff = explode($Conf['Log']['separate'], rtrim(file_get_contents($filename)) );
        $this->assertEquals($buff[2], str_replace($Conf['Log']['separate'], '', $str1) );
        $this->assertEquals($buff[3], str_replace("\n", '', $str2) );
        $this->assertEquals($buff[4], str_replace("\r", '', $str3) );
        unlink($filename);
    }

    /**
     * test addlogfile() - 6
     */
    public function testFunction_addlogfile6(){
        global $Conf;
        $time     = time();
        $filename = $this->_addlogfile_makefilename($time);
        $str1     = 'HelloWorld';
        $str2     = 'fizzbuzz';
        $str3     = 'foobar';

        //ログファイルに追記されるか
        addlogfile('TEST', $str1, $str2);
        addlogfile('TEST', $str3);
        addlogfile('TEST', $str2, $str1);
        
        $tmp   = file($filename);
        $buff1 = explode($Conf['Log']['separate'], rtrim($tmp[0]) );
        $buff2 = explode($Conf['Log']['separate'], rtrim($tmp[1]) );
        $buff3 = explode($Conf['Log']['separate'], rtrim($tmp[2]) );

        $this->assertEquals($buff1[2], $str1);
        $this->assertEquals($buff1[3], $str2);
        $this->assertEquals($buff2[2], $str3);
        $this->assertEquals($buff3[2], $str2);
        $this->assertEquals($buff3[3], $str1);
        unlink($filename);
    }

    private function _addlogfile_makefilename($time){
        global $Conf;
        return(
            sprintf('%s/%s%s.%s', $Conf['Log']['dir'], $Conf['Log']['file']['TEST'], date($Conf['Log']['add'], $time), $Conf['Log']['ext'])
        );
    }
    
    
    

    /**
     * test lockfwrite()
     */
    public function testFunction_lockfwrite(){
        $str  = 'Hello!';
        $dir  = sys_get_temp_dir();
        $file = tempnam($dir, self::LOCKFWRITE_TMPNAME);
        
        //ファイルが生成され内容が書き込まれる
        lockfwrite($file, $str);
        $this->assertFileExists($file);
        $this->assertEquals(file_get_contents($file), $str);
        
        //ファイルに追記される
        lockfwrite($file, $str);
        $this->assertEquals(file_get_contents($file), $str.$str);

        //ファイルを真っ白にして書き込む
        lockfwrite($file, $str, true);
        $this->assertEquals(file_get_contents($file), $str);

        unlink($file);
    }

    /**
     * test array_end()
     */
    public function testFunction_array_end(){
        $arr   = array("apple", "banana", "muscat");
        $count = count($arr);
        
        //取り出した要素の値が正しいか
        $this->assertTrue(array_end($arr) === "muscat");
        
        //要素数に変化がない
        $this->assertEquals(count($arr), $count);
        $this->assertEquals($arr, array("apple", "banana", "muscat"));
    }


    /**
     * test gen_uniqid() - 生成文字列の妥当性
     */
    public function testFunction_gen_uniqid1(){
        $id1 = gen_uniqid();
        $id2 = gen_uniqid(M_PI);    //円周率をseedに与える
        
        //40byteの文字列が生成されるか
        $this->assertTrue(is_string($id1));
        $this->assertEquals(strlen($id1), self::GENUNIQID_LEN);

        $this->assertTrue(is_string($id2));
        $this->assertEquals(strlen($id2), self::GENUNIQID_LEN);
    }

    /**
     * test gen_uniqid() - ランダム性の検証
     */
    public function testFunction_gen_uniqid2(){
        $id1 = array();
        $id2 = array();

        for($i=0; $i<self::GENUNIQID_LOOP; $i++){
            $id1[] = gen_uniqid();
            $id2[] = gen_uniqid(M_PI);          //円周率をseedに与える
        }
    
        //生成された文字列がユニークか
        $this->assertEquals( count(array_unique($id1)), self::GENUNIQID_LOOP );
        $this->assertEquals( count(array_unique($id2)), self::GENUNIQID_LOOP );
    }

}