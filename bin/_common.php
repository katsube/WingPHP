<?php
$scriptpath = dirname($argv[0]);
init($scriptpath);

require_once('../conf.php');
require_once('../lib/global.php');
require_once('../lib/wsexception.php');

/**
 * Initialize
 */
function init($dir){
    //---------------------------------
    // change current diretory
    //---------------------------------
    if( is_dir($dir) && chdir($dir) ){
        ;
    }
    else{
        errormsg("Can not change current directory ($shelldir)");
        exit;
    }
}

/**
 * Error
 */
function errormsg($msg){
    echo "*** Error\n";
    echo "$msg\n";
}
