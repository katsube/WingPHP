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
 *     $log = new Logger();
 *
 *     // config
 *     $log->setName('foobar');
 *     $log->setWriteLevel(Logger::LV_DEBUG);       // LV_DEBUG | LV_INFO | LV_WARNING | LV_ERROR | LV_CRITICAL
 *     $log->setAlertLevel(Logger::LV_CRITICAL);    // LV_DEBUG | LV_INFO | LV_WARNING | LV_ERROR | LV_CRITICAL
 *     $log->setAlertDo(false);                     // true | false
 *     $log->setAlertType(Logger::AL_EMAIL);        // AL_EMAIL | AL_SLACK
 *     $log->setStorage(Logger::ST_FILE);           // ST_FILE
 *     $log->setPushSTDOUT(false);                  // true | false
 *     $log->setPushSTDERR(false);                  // true | false
 * 
 *     // write log
 *     $log->debug("debug message");
 *     $log->info("information message");
 *     $log->warning("warning message");
 *     $log->error("error message");
 *     $log->critical("critical erro message");
 * </code>
 *
 * @package    Logger
 * @copyright  2016 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class Logger {
    const LV_DEBUG    = 100;
    const LV_INFO     = 200;
    const LV_WARNING  = 300;
    const LV_ERROR    = 400;
    const LV_CRITICAL = 500;

    const ST_FILE   = 'FILE';

    const AL_EMAIL  = 'email';
    const AL_SLACK  = 'slack';      // not implemented

    private $log_name = 'COMMON';

    private $write_lv      = self::LV_DEBUG;
    private $write_storage = self::ST_FILE;
    private $write_handle  = null;

    private $alert_lv      = self::LV_CRITICAL;
    private $alert_do      = false;
    private $alert_type    = self::AL_EMAIL;

    private $push_stdout  = false;
    private $push_stderr  = false;
    

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	public function __construct(){
        global $Conf;
        if(array_key_exists('Logger', $Conf)){
            if(array_key_exists('alert', $Conf['Logger'])){
                if(array_key_exists('on', $Conf['Logger']['alert'])){
                    $this->setAlertDo($Conf['Logger']['alert']['on']);
                }
                if(array_key_exists('type', $Conf['Logger']['alert'])){
                    $this->setAlertType($Conf['Logger']['alert']['type']);
                }
            }
        }
	}


	/*--------------------------------------------
	 * ■ Public ■
	 *--------------------------------------------
	 * - __call
	 *      debug()
	 *      info()
	 *      warning()
	 *      error()
	 *      critical()
	 * - setName
	 * - setWriteLevel
	 * - setAlertLevel
	 * - setAlertDo
	 * - setAlertType
	 * - setStorage
	 * - setPushSTDOUT
	 * - setPushSTDERR
	 *--------------------------------------------*/

	/**
	 * メソッドのオーバーライド
	 */
	function __call($name, $param){
        switch($name){
            case 'debug':
                return( $this->_write(self::LV_DEBUG, $param) );
                break;
            
            case 'info':
                return( $this->_write(self::LV_INFO, $param) );
                break;

            case 'warning':
                return( $this->_write(self::LV_WARNING, $param) );
                break;

            case 'error':
                return( $this->_write(self::LV_ERROR, $param) );
                break;

            case 'critical':
                return( $this->_write(self::LV_CRITICAL, $param) );
                break;
        
            case 'snapshot':
                return( $this->_writeSnapshot($param) );
                break;
            
            default:
                throw new WsException('Undefined method: '.$name, 404);
                break;
        }
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
            case self::LV_CRITICAL:
                $this->write_lv = $lv;
                return(true);
            
            default:
                return(false);
        }
    }

    /**
     * set AlertLevel
     * 
     * @param  integer  $lv
     * @return boolean
     * @access public
     */
    public function setAlertLevel($lv){
        switch($lv){
            case self::LV_DEBUG:
            case self::LV_INFO:
            case self::LV_WARNING:
            case self::LV_ERROR:
            case self::LV_CRITICAL:
                $this->alert_lv = $lv;
                return(true);
            
            default:
                return(false);
        }
    }

    /**
     * set Alert On/Off
     * 
     * @param  boolean  $flag
     * @return boolean
     * @access public
     */
    public function setAlertDo($flag){
        if( is_bool($flag) ){
            $this->alert_do = $flag;
            return(true);
        }
        
        return(false);
    }

    /**
     * set AlertType
     * 
     * @param  string  $type
     * @return boolean
     * @access public
     */
    public function setAlertType($type){
        switch($lv){
            case self::AL_EMAIL:
            //case self::AL_SLACK:
                $this->alert_type = $type;
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
            case self::ST_FILE:
            case self::ST_STDOUT:
            case self::ST_STDERR:
                $this->write_storage = $type;
                return(true);
            
            default:
                return(false);
        }
    }
    
    /**
     * set PushSTDOUT
     * 
     * @param  boolean  $flag
     * @return boolean
     * @access public
     */
    public function setPushSTDOUT($flag){
        if( is_bool($flag) ){
            $this->push_stdout = $flag;
            return(true);
        }
        
        return(false);
    }

    /**
     * set PushSTDERR
     * 
     * @param  boolean  $flag
     * @return boolean
     * @access public
     */
    public function setPushSTDERR($flag){
        if( is_bool($flag) ){
            $this->push_stderr = $flag;
            return(true);
        }
        
        return(false);
    }

	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _writeLog
	 * _ _genMessage
	 *--------------------------------------------*/
    /**
     * write Message
     * 
     * @param  integer       $lv
     * @param  string|array  $msg
     * @return mixed         null  = not enough write level.
     *                       true  = success
     *                       false = failed
     * @access private
     */
    private function _write($lv, $msg){
        $message   = $this->_genMessage($msg);
        $time      = time();
        $timestamp = sprintf('%s %s', date('Y-m-d', $time), date('H:i:s', $time));

        //-----------------------------
        // Write
        //-----------------------------
        //Write to File
        if( $this->write_lv <= $lv){
            switch( $this->write_storage ){
                case self::ST_FILE:
                    addlogfile($this->log_name, $lv, $message);
                    break;
                
                default:
                    break;
            }
        }

        //Push STDOUT
        if($this->push_stdout){
            file_put_contents('php://stdout', sprintf("[%s] %s\n", $timestamp, $message) );
        }
        
        //Push STDERR
        if($this->push_stderr){
            file_put_contents('php://stderr', sprintf("[%s] %s\n", $timestamp, $message) );
        }

        //-----------------------------
        // Alert
        //-----------------------------
        if( $this->alert_do ){
            if( $this->alert_lv <= $lv ){
                switch($this->alert_type){
                    case self::AL_EMAIL:
                        $this->_sendAlertMail($lv, $timestamp, $message);
                        break;
                    
                    case self::AL_SLACK:
                        //not implemented
                        break;
                
                    default:
                        break;
                    
                }
            }
        }
    }
    
    /**
     * write Snapshot
     * 
     * @param  array   $param   [0] ... filepath or directory
     *                          [1] ... message
     * @return boolean
     * @access private
     */
    private function _writeSnapshot($param){
        if( count($param) !== 2){
            return(false);
        }
        
        $path    = $param[0];
        $message = $param[1];
        
        if( is_dir($path) ){
            $time      = time();
            $yyyymmdd  = date('Ymd', $time);
            $hhmmss    = date('His', $time);
            
            $directory = sprintf('%s/%s', $path, $yyyymmdd);
            $path      = sprintf('%s/%s%s_%s.txt', $directory, $yyyymmdd, $hhmmss, gen_uniqid());
        
            if( ! is_dir($directory) ){
                mkdir($directory);
            }
        }
        
        return( lockfwrite($path, $message, true) );
    }

    
    /**
     * Generate Message
     * 
     * @param  string|array  $msg
     * @return string
     * @access private
     */
    private function _genMessage($msg){
        if(is_array($msg)){
            global $Conf;
            return( implode($Conf['Log']['separate'], $msg) );
        }
        
        return($msg);
    }

    /**
     * Alert Mail
     * 
     * @param  integer  $lv
     * @param  string   $timestamp
     * @param  string   $message
     * @return string
     * @access private
     */
    private function _sendAlertMail($lv, $timestamp, $message){
        global $Conf;
        $body =   "Level: $lv\n"
                . "date: $timestamp\n"
                . "-----------------------\n"
                . $message . "\n"
                . "-----------------------\n"
                . "";

        uselib('Sendmail');
        $mail = new Sendmail();
        $mail->headers($Conf['Logger']['alert']['email']);
        $mail->body($body);
        $mail->doit();
    }
}
