<?php


class globalTest extends \Codeception\TestCase\Test
{
    use \Codeception\Specify;

    const GENUNIQID_LEN  = 40;      //gen_uniqid() 生成される文字列長
    const GENUNIQID_LOOP = 10;      //gen_uniqid() ユニーク性を検証する個数

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before(){
        require_once('conf.php');
        require_once('lib/global.php');
    }

    protected function _after(){
    }
    

    public function test_array_end(){
        $this->specify("value is last value", function() {
            $arr = array("apple", "banana", "muscat");

            $this->assertTrue(array_end($arr) === "muscat");
        });

        $this->specify("not delete", function() {
            $arr = array("apple", "banana", "muscat");
            $tmp = array_end($arr);

            $this->assertTrue(count($arr) === 3);
            $this->assertTrue($arr === array("apple", "banana", "muscat"));
        });
    }

    public function test_gen_uniqid(){
        //------------------------------
        //生成されるIDが40byteの文字列
        //------------------------------
        $this->specify("generate 40byte strings", function() {
            $id1 = gen_uniqid();
            $this->assertTrue(is_string($id1));
            $this->assertTrue(strlen($id1) === self::GENUNIQID_LEN);
        });
        $this->specify("generate 40byte strings (seed)", function() {
            $id2 = gen_uniqid('foobar');
            $this->assertTrue(is_string($id2));
            $this->assertTrue(strlen($id2) === self::GENUNIQID_LEN);
        });

        //------------------------------
        //生成するIDがユニーク
        //------------------------------
        $this->specify("uniq", function() {
            $id = array();
            for($i=0; $i<self::GENUNIQID_LOOP; $i++){
                $id[] = gen_uniqid();
            }
        
            $this->assertTrue( count(array_unique($id)) === self::GENUNIQID_LOOP );
        });
        $this->specify("uniq (seed)", function() {
            $id = array();
            for($i=0; $i<self::GENUNIQID_LOOP; $i++){
                $id[] = gen_uniqid('foobar');
            }
        
            $this->assertTrue( count(array_unique($id)) === self::GENUNIQID_LOOP );
        });
    }
}