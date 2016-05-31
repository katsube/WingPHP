<?php
require_once('../conf.php');
require_once('../lib/global.php');
require_once('../lib/autoload.php');
require_once('../lib/wsexception.php');

class TestsDefine{
    const DOMAIN = 'wingphp-katsubemakito.c9users.io';
}

class TestsUtil{
    public static function makeURL($path, $http='http'){
        return(
            sprintf('%s://%s/%s', $http, TestsDefine::DOMAIN, $path)
        );
    }
}


// Thanx! http://qiita.com/kumazo@github/items/45d956b0e66cd0b5e0bd
//   使用例
//     $target = TestPrivate::on( new Target() );
//     $actual = $target->anyPrivateMethod();
class TestPrivate {
    private $target;

    private function __construct($target) {
        $this->target = $target;
    }

    public static function on($target) {
        return( new self($target) );
    }

    public function __call($name, $args) {
        $method = new  ReflectionMethod($this->target, $name);
        $method->setAccessible(true);
        return( $method->invokeArgs($this->target, $args) );
    }
}