<?php

require_once('conf.php');
require_once('lib/global.php');

class globalTest extends \Codeception\TestCase\Test
{
    use \Codeception\Specify;

    const LOCKFWRITE_TMPNAME = '__TEST_CODECPTION_';
    const GENUNIQID_LEN  = 40;      //gen_uniqid() 生成される文字列長
    const GENUNIQID_LOOP = 100;     //gen_uniqid() ユニーク性を検証する個数

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before(){
        ;
    }

    protected function _after(){
        ;
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
        $this->assertTrue(file_get_contents($file) === $str);
        
        //ファイルに追記される
        lockfwrite($file, $str);
        $this->assertTrue(file_get_contents($file) === $str.$str);

        //ファイルを真っ白にして書き込む
        lockfwrite($file, $str, true);
        $this->assertTrue(file_get_contents($file) === $str);

        unlink($file);
    }



    /**
     * test array_end()
     */
    public function testFunction_array_end(){
        $arr   = array("apple", "banana", "muscat");
        $count = count($arr);
        
        //取り出した要素の値
        $this->assertTrue(array_end($arr) === "muscat");
        
        //要素数に変化がない
        $this->assertTrue(count($arr) === $count);
        $this->assertTrue($arr === array("apple", "banana", "muscat"));
    }


    /**
     * test gen_uniqid() - 生成文字列の妥当性
     */
    public function testFunction_gen_uniqid1(){
        $id1 = gen_uniqid();
        $id2 = gen_uniqid(M_PI);    //円周率をseedに与える
        
        //40byteの文字列が生成されるか
        $this->assertTrue(is_string($id1));
        $this->assertTrue(strlen($id1) === self::GENUNIQID_LEN);

        $this->assertTrue(is_string($id2));
        $this->assertTrue(strlen($id2) === self::GENUNIQID_LEN);
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
    
        $this->assertTrue( count(array_unique($id1)) === self::GENUNIQID_LOOP );
        $this->assertTrue( count(array_unique($id2)) === self::GENUNIQID_LOOP );
    }

}