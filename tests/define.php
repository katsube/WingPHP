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