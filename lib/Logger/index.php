<?php
/* [WingPHP]
 *  - lib/Logger/index.php
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
 
/**
 * Loggerクラス
 * 
 * example.<code>
 *     uselib('Logger');
 *
 *     $log = new Logger();   // new Logger(['level'=>Logger::WARNING, 'storage'=>Logger:FILE]);
 *
 *     // config
 *     $log->setName('foobar');
 *     $log->setWriteLevel(Logger::DEBUG);  //DEBUG | INFO | WARNING | ERROR
 *     $log->setStorage(Logger::FILE);      //FILE
 *                                          //sorry, other options do not exist now.
 * 
 *     // write log
 *     $log->debug("debug message");
 *     $log->info("information message");
 *     $log->warning("warning message");
 *     $log->error("error message");
 * </code>
 *
 * @package    Logger
 * @copyright  2016 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class Logger {
    const LV_DEBUG   = 1;
    const LV_INFO    = 2;
    const LV_WARNING = 3;
    const LV_ERROR   = 4;

    const FILE = 101;

    private $log_name = 'common';

    private $write_lv      = self::LV_WARNING;
    private $write_storage = self::FILE;
    private $write_handle  = null;


	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	public function __construct($opt=null){
	    if($opt !== null && is_array($opt) ){
	        if( array_key_exists('level', $opt) ){
	            $ret = $this->setWriteLevel($opt['lv']);
	            if($ret === false)
	                throw new WsException("illegal arguments to level");
	        }
	        if( array_key_exists('storage', $opt) ){
	            $ret = $this->setStorage($opt['storage']);
	            if($ret === false)
	                throw new WsException("illegal arguments to storage");
	        }
	        if( array_key_exists('name', $opt) ){
	            $ret = $this->setName($opt['name']);
	            if($ret === false)
	                throw new WsException("illegal arguments to name");
	        }
	    }
	}


	/*--------------------------------------------
	 * ■ Public ■
	 *--------------------------------------------
	 * - debug
	 * - info
	 * - warning
	 * - error
	 * - setName
	 * - setWriteLevel
	 * - setStorage
	 *--------------------------------------------*/
    /**
     * add Debug Message.
     * 
     * @param  string  $msg
     * @return boolean
     * @access public
     */
    public function debug($msg){
        return( $this->_write($msg, self::LV_DEBUG) );
    }

    /**
     * add Information Message.
     * 
     * @param  string  $msg
     * @return boolean
     * @access public
     */
    public function info($msg){
        return( $this->_write($msg, self::LV_INFO) );
    }

    /**
     * add Warning Message.
     * 
     * @param  string  $msg
     * @return boolean
     * @access public
     */
    public function warning($msg){
        return( $this->_write($msg, self::LV_WARNING) );
    }

    /**
     * add Error Message.
     * 
     * @param  string  $msg
     * @return boolean
     * @access public
     */
    public function Error($msg){
       return( $this->_write($msg, self::LV_ERROR) );
    }

    /**
     * set LogName
     * 
     * @param  string  $name
     * @return boolean
     * @access public
     */
    public function setName($name){
        if( !empty($name) ){
            $this->log_name = $name;
            return(true);
        }
        
        return(false);
    }

    /**
     * set WriteLevel
     * 
     * @param  integer  $lv
     * @return boolean
     * @access public
     */
    public function setWriteLevel($lv){
        switch($lv){
            case self::LV_DEBUG:
            case self::LV_INFO:
            case self::LV_WARNING:
            case self::LV_ERROR:
                $this->write_lv = $lv;
                return(true);
            
            default:
                return(false);
        }
    }

    /**
     * set Storage type
     * 
     * @param  integer  $type
     * @return boolean
     * @access public
     */
    public function setStorage($type){
        switch($type){
            case self::FILE:
                $this->write_storage = $type;
                return(true);
            
            default:
                return(false);
        }
    }


	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _writeLog
	 *--------------------------------------------*/
    /**
     * write Message
     * 
     * @param  string  $msg
     * @param  integer $lv
     * @return mixed   null  = not enough write level.
     *                 true  = success
     *                 false = failed
     * @access public
     */
    private function _write($msg, $lv){
        if( $this->write_lv > $lv ){
            return(null);
        }
    }
}
