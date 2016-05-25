<?php


class globalTest extends \Codeception\TestCase\Test
{
    use \Codeception\Specify;

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

    public function testGenUniqID(){
        $this->specify("generate 40byte strings", function() {
            $id = gen_uniqid();
            $this->assertTrue(is_string($id));
            $this->assertTrue(strlen($id) === 40);
        });
    }
}