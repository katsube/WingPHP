<?php
require_once('define.php');

class confUnitTest extends PHPUnit_Framework_TestCase
{
    const SECRET_KEYLEN_MIN   = 16;
    const SESSION_NAMELEN_MIN =  1;
    const CACHE_DBPRELEN_MIN  =  1;
    
    /**
     * test $Conf
     * 
     */
    public function testConf_1st(){
        global $Conf;
        $this->assertTrue( is_array($Conf) );
    }

    /**
     * test $Conf - DB
     * 
     */
    public function testConf_DB(){
        global $Conf;
        $fetchstyle = array(
                              PDO::FETCH_ASSOC
                            , PDO::FETCH_BOTH
                            , PDO::FETCH_BOUND
                            , PDO::FETCH_CLASS
                            , PDO::FETCH_INTO
                            , PDO::FETCH_LAZY
                            , PDO::FETCH_NAMED
                            , PDO::FETCH_NUM
                            , PDO::FETCH_OBJ
                        );
    
        //DB設定が存在するか
        $this->assertArrayHasKey('DB', $Conf);
        $this->assertTrue( is_array($Conf['DB']) );
  
        //master設定チェック
        $this->assertArrayHasKey('master', $Conf['DB']);
        $this->assertTrue( is_array($Conf['DB']['master']) );
        $this->assertArrayHasKey('DSN',         $Conf['DB']['master']);
        $this->assertArrayHasKey('USER',        $Conf['DB']['master']);
        $this->assertArrayHasKey('PASSWORD',    $Conf['DB']['master']);
        $this->assertArrayHasKey('fetch_style', $Conf['DB']['master']);
        $this->assertArrayHasKey('persistent',  $Conf['DB']['master']);
    
        $this->assertTrue( is_string($Conf['DB']['master']['DSN']) );
        $this->assertTrue( is_string($Conf['DB']['master']['USER']) );
        $this->assertTrue( is_string($Conf['DB']['master']['PASSWORD']) );
        $this->assertContains( $Conf['DB']['master']['fetch_style'], $fetchstyle );
        $this->assertTrue( is_bool($Conf['DB']['master']['persistent']) );
    }

    
    /**
     * test $Conf - Secret
     * 
     */
    public function testConf_Secret(){
        global $Conf;

        //Secret設定が存在するか
        $this->assertArrayHasKey('Secret', $Conf);
        $this->assertTrue( is_array($Conf['Secret']) );
        
        //keyが設定されているか
        $this->assertArrayHasKey('key', $Conf['Secret']);
        $this->assertTrue(is_string($Conf['Secret']['key']));                           //型が文字列か
        $this->assertTrue(self::SECRET_KEYLEN_MIN <= strlen($Conf['Secret']['key']));   //最低文字列長以上か
    }

    /**
     * test $Conf - Secret
     * 
     */
    public function testConf_Session(){
        global $Conf;

        //Session設定が存在するか
        $this->assertArrayHasKey('Session', $Conf);
        $this->assertTrue( is_array($Conf['Session']) );
        
        //nameが設定されているか
        $this->assertArrayHasKey('name', $Conf['Session']);
        $this->assertTrue(is_string($Conf['Session']['name']));                             //型が文字列か
        $this->assertTrue(ctype_alnum($Conf['Session']['name']));                           //英数字のみか
        $this->assertTrue(self::SESSION_NAMELEN_MIN <= strlen($Conf['Session']['name']));   //最低文字列長以上か
    }

    /**
     * test $Conf - Lib
     * 
     */
    public function testConf_Lib(){
        global $Conf;

        //Lib設定が存在するか
        $this->assertArrayHasKey('Lib', $Conf);
        $this->assertTrue( is_array($Conf['Lib']) );
        
        //dirが設定されているか
        $this->assertArrayHasKey('dir', $Conf['Lib']);
        $this->assertTrue(is_string($Conf['Lib']['dir']));      //型が文字列か
        $this->assertFileExists($Conf['Lib']['dir']);           //ディレクトリが存在するか                    
    }

    /**
     * test $Conf - Cache
     * 
     */
    public function testConf_Cache(){
        global $Conf;
        $storagetype = array('File', 'MemCache');

        //Cache設定が存在するか
        $this->assertArrayHasKey('Cache', $Conf);
        $this->assertTrue( is_array($Conf['Cache']) );
        
        //strage
        $this->assertArrayHasKey('strage', $Conf['Cache']);
        $this->assertContains($Conf['Cache']['strage'], $storagetype);
        
        //expire
        $this->assertArrayHasKey('expire', $Conf['Cache']);
        $this->assertTrue( is_integer($Conf['Cache']['expire']) );
    
        //db_use
        $this->assertArrayHasKey('db_use', $Conf['Cache']);
        $this->assertTrue( is_bool($Conf['Cache']['db_use']) );
        
        //db_pre
        $this->assertArrayHasKey('db_pre', $Conf['Cache']);
        $this->assertTrue( is_string($Conf['Cache']['db_pre']) );
        $this->assertTrue(self::CACHE_DBPRELEN_MIN <= strlen($Conf['Cache']['db_pre']));   //最低文字列長以上か
   
        //db_expire
        $this->assertArrayHasKey('db_expire', $Conf['Cache']);
        $this->assertTrue( is_integer($Conf['Cache']['db_expire']) );
    }

    /**
     * test $Conf - Log
     * 
     */
    public function testConf_Log(){
        global $Conf;
        
        //Log設定が存在するか
        $this->assertArrayHasKey('Log', $Conf);
        $this->assertTrue( is_array($Conf['Log']) );
        
        //dir
        $this->assertArrayHasKey('dir', $Conf['Log']);
        $this->assertTrue( is_string($Conf['Log']['dir']) );
        $this->assertFileExists($Conf['Log']['dir']);
    
        //file
        $this->assertArrayHasKey('file', $Conf['Log']);
        $this->assertTrue( is_array($Conf['Log']['file']) );
        foreach($Conf['Log']['file'] as $key => $value){
            $this->assertEquals(preg_match('/^[a-zA-Z0-9_.-]{1,}$/', $key), 1);
            $this->assertEquals(preg_match('/^[a-zA-Z0-9_.-]{1,}$/', $value), 1);
        }
    
        //add
        $this->assertArrayHasKey('add', $Conf['Log']);
        $this->assertTrue( is_string($Conf['Log']['add']) );
        $this->assertEquals(preg_match('/^[d|D|j|l|N|S|w|z|W|F|m|M|n|t|L|o|Y|y|aA|B|g|G|h|H|i|s|u|e|I|O|P|T|Z|c|r|U]{1,}$/', $Conf['Log']['add']), 1);
    
        //ext
        $this->assertArrayHasKey('ext', $Conf['Log']);
        $this->assertTrue( is_string($Conf['Log']['ext']) );
        
        //separate
        $this->assertArrayHasKey('separate', $Conf['Log']);
        $this->assertTrue( is_string($Conf['Log']['separate']) );
        
        //addtrace
        $this->assertArrayHasKey('addtrace', $Conf['Log']);
        $this->assertTrue( is_bool($Conf['Log']['addtrace']) );
    }

    /**
     * test $Conf - Log
     * 
     */
    public function testConf_Logger(){
        global $Conf;
        $alerttype = array('email');
    
        //Logger設定が存在するか
        $this->assertArrayHasKey('Logger', $Conf);
        $this->assertTrue( is_array($Conf['Logger']) );

        //storage
        $this->assertArrayHasKey('storage', $Conf['Logger']);
        $this->assertArrayHasKey('file', $Conf['Logger']['storage']);
        
        //alert
        $this->assertArrayHasKey('alert', $Conf['Logger']);
        $this->assertArrayHasKey('on', $Conf['Logger']['alert']);
        $this->assertTrue(is_bool($Conf['Logger']['alert']['on']));

        $this->assertArrayHasKey('type', $Conf['Logger']['alert']);
        $this->assertContains($Conf['Logger']['alert']['type'], $alerttype);
        
        $this->assertArrayHasKey('email', $Conf['Logger']['alert']);
        $this->assertTrue(is_array($Conf['Logger']['alert']['email']));
        
        $this->assertArrayHasKey('To', $Conf['Logger']['alert']['email']);
        $this->assertTrue(is_string($Conf['Logger']['alert']['email']['To']));
        
        $this->assertArrayHasKey('From', $Conf['Logger']['alert']['email']);
        $this->assertTrue(is_string($Conf['Logger']['alert']['email']['From']));

        $this->assertArrayHasKey('Subject', $Conf['Logger']['alert']['email']);
        $this->assertTrue(is_string($Conf['Logger']['alert']['email']['Subject']));

        //Slack
         $this->assertArrayHasKey('slack', $Conf['Logger']['alert']);
    }

    /**
     * test $Conf - AutoLogging
     * 
     */
    public function testConf_AutoLogging(){
        global $Conf;

        //AutoLogging設定が存在するか
        $this->assertArrayHasKey('AutoLogging', $Conf);
        $this->assertTrue( is_array($Conf['AutoLogging']) );
        
        //error
        $this->assertArrayHasKey('error', $Conf['AutoLogging']);
        $this->assertTrue(is_bool($Conf['AutoLogging']['error']));
    }

    /**
     * test $Conf - Smarty
     * 
     */
    public function testConf_Smarty(){
        global $Conf;
    
        //Smarty設定が存在するか
        $this->assertArrayHasKey('Smarty', $Conf);
        $this->assertTrue( is_array($Conf['Smarty']) );
        
        //version
        $this->assertArrayHasKey('version', $Conf['Smarty']);
        $this->assertEquals($Conf['Smarty']['version'], '3.1');
        
        //tmpl
        $this->assertArrayHasKey('tmpl', $Conf['Smarty']);
        $this->assertFileExists($Conf['Smarty']['tmpl']);

        //tmpl_c
        $this->assertArrayHasKey('tmpl_c', $Conf['Smarty']);
        $this->assertFileExists($Conf['Smarty']['tmpl_c']);

        //plugin
        $this->assertArrayHasKey('plugin', $Conf['Smarty']);
        $this->assertTrue(is_array($Conf['Smarty']['plugin']));
        foreach($Conf['Smarty']['plugin'] as $file){
            $this->assertFileExists($file);
        }

        //config
        $this->assertArrayHasKey('config', $Conf['Smarty']);
        $this->assertFileExists($Conf['Smarty']['config']);

        //cache
        $this->assertArrayHasKey('cache', $Conf['Smarty']);
        $this->assertFileExists($Conf['Smarty']['cache']);

        //is_cache
        $this->assertArrayHasKey('is_cache', $Conf['Smarty']);
        $this->assertTrue(is_bool($Conf['Smarty']['is_cache']));

        //cache_life
        $this->assertArrayHasKey('cache_life', $Conf['Smarty']);
        $this->assertTrue(is_integer($Conf['Smarty']['cache_life']));
    }
   
   
    /**
     * test $Conf - SmartyDirect
     * 
     */
    public function testConf_SmartyDirect(){
        global $Conf;
    
        //SmartyDirect設定が存在するか
        $this->assertArrayHasKey('SmartyDirect', $Conf);
        $this->assertTrue( is_array($Conf['SmartyDirect']) );
        
        //run
        $this->assertArrayHasKey('run', $Conf['SmartyDirect']);
        $this->assertTrue( is_bool($Conf['SmartyDirect']['run']) );

        //root
        $this->assertArrayHasKey('root', $Conf['SmartyDirect']);
        $docroot = sprintf('%s', $Conf['Smarty']['tmpl'], $Conf['SmartyDirect']['root']);
        $this->assertFileExists($docroot);

        //default
        $this->assertArrayHasKey('default', $Conf['SmartyDirect']);
        $this->assertTrue( is_string($Conf['SmartyDirect']['default']) );
    }

    /**
     * test $Conf - Sendmail
     * 
     */
    public function testConf_Sendmail(){
        global $Conf;
        $languagetype = array("Japanese", "ja", "English", "en", "uni");
        
        //Sendmail設定が存在するか
        $this->assertArrayHasKey('Sendmail', $Conf);
        $this->assertTrue( is_array($Conf['Sendmail']) );
        
        //language
        $this->assertArrayHasKey('language', $Conf['Sendmail']);
        $this->assertContains($Conf['Sendmail']['language'], $languagetype);

        //encode
        $this->assertArrayHasKey('encode', $Conf['Sendmail']);
        $encode_cur = mb_internal_encoding();                                   //いったん退避
        $this->assertTrue(mb_internal_encoding($Conf['Sendmail']['encode']));
        mb_internal_encoding($encode_cur);                                      //復旧

        //header
        $this->assertArrayHasKey('header', $Conf['Sendmail']);
        $this->assertTrue(is_array($Conf['Sendmail']['header']));
        $sendmail = new Sendmail();
        $this->assertTrue( $sendmail->headers($Conf['Sendmail']['header']) );

        //log
        $this->assertArrayHasKey('log', $Conf['Sendmail']);
        $this->assertArrayHasKey('on', $Conf['Sendmail']['log']);
        $this->assertTrue(is_bool($Conf['Sendmail']['log']['on']));
        
        $this->assertArrayHasKey('snapshot', $Conf['Sendmail']['log']);
        $this->assertTrue(is_bool($Conf['Sendmail']['log']['snapshot']));

        $this->assertArrayHasKey('snapdir', $Conf['Sendmail']['log']);
        $this->assertFileExists($Conf['Sendmail']['log']['snapdir']);
    }

    /**
     * test $Conf - validaiton
     * 
     */
    public function testConf_validation(){
        global $Conf;

        //validation設定が存在するか
        $this->assertArrayHasKey('validation', $Conf);
        $this->assertTrue( is_array($Conf['validation']) );
        
        //jscheck
        $this->assertArrayHasKey('jscheck', $Conf['validation']);
        $this->assertTrue( is_bool($Conf['validation']['jscheck']) );
        
        //form
        $this->assertArrayHasKey('form', $Conf['validation']);
        $this->assertTrue(is_array($Conf['validation']['form']));
        $this->assertArrayHasKey('idname', $Conf['validation']['form']);
        $this->assertTrue(is_string($Conf['validation']['form']['idname']));
    }


    /**
     * test $Scratch
     * 
     */
    public function testConf_Scratch(){
        global $Scratch;
        
        $this->assertTrue( is_array($Scratch) );
        $this->assertArrayHasKey('form', $Scratch);
        $this->assertTrue( is_array($Scratch['form']) );
    }
}