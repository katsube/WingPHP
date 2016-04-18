<?php
/* [WingPHP]
 *  - Command line tool 
 *
 * The MIT License
 * Copyright (c) 2016 WingPHP < http://wingphp.net >
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */


//---------------------------------
// arguments
//---------------------------------
$shelldir = $argv[1];
$command  = $argv[2];
$arg1     = $argv[3];
$arg2     = $argv[4];


//---------------------------------
// initialize
//---------------------------------
init($shelldir);

require_once('../../conf.php');
require_once('../../lib/global.php');

//---------------------------------
// Do Command
//---------------------------------
doCommand($command, $arg1, $arg2);


/**
 * Initialize
 */
function init($shelldir){
    //---------------------------------
    // change current diretory
    //---------------------------------
    if( is_dir($shelldir) && chdir($shelldir) ){
        ;
    }
    else{
        error("Can not change current directory ($shelldir)");
        exit;
    }
}


/**
 * Do Command
 */
function doCommand($mode, $arg1, $arg2){
    switch( $mode ){
        case 'SQL':
            run_sql($arg1, $arg2);
            break;
        
        case 'CREATE':
            run_create();
            break;
        
        default:
            error("undefine mode ($mode)");
            exit;
    }
}



/**
 * Run SQL
 */
function run_sql($arg1, $arg2){
    $sql_str = '';
    if( is_file($arg2) ){
        $sql_str = file_get_contents($arg2);
    }
    else{
        $sql_str = $arg2;
    }

    require_once('../../model/base.php');
    $m = new BaseModel();
    
    if($arg1 === 'exec'){
        try{
            $m->begin();
            $m->exec($sql_str);
            $m->commit();
        }
        catch(PDOException $e){
            $m->rollback();
            error("run_sql: ".$e->getMessage());
        }
    }
    else if($arg1 === 'select'){
        try{
            print_r( $m->select($sql_str) );
        }
        catch(Exception $e){
            error("run_sql: ".$e->getMessage());
        }
    }
}

/**
 * Create Template
 */
function run_create(){
    
}


/**
 * Error
 */
function error($msg){
    echo "*** Error\n";
    echo "$msg\n";
}
