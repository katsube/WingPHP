<?php


init($argv[1], $argv[2]);


/**
 * Initialize
 */
function init($shelldir, $mode){
    //---------------------------------
    // change current diretory
    //---------------------------------
    if( is_dir($shelldir) && chdir($shelldir) ){
        ;
    }
    else{
        error"Can not change current directory ($shelldir)");
        exit;
    }

    //---------------------------------
    // Do it
    //---------------------------------
    switch( $mode ){
        case 'SQL':
            run_sql();
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
function run_sql(){
    
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
