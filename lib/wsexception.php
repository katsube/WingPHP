<?php
/* [WingPHP]
 *  - WsException/index.php
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

class WsException extends Exception{
    public function __construct($message = null, $code = 0, Exception $previous = null){
        global $Conf;
        parent::__construct($message, $code, $previous);
    
        if ( array_key_exists('AutoLogging', $Conf) && array_key_exists('error', $Conf['AutoLogging']) && $Conf['AutoLogging']['error'] ){
            $message = $this->getMessage();
            $file    = $this->getFile();
            $line    = $this->getLine();
            $code    = $this->getCode();
            $trace   = $this->getTraceAsString();
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $ua = $_SERVER['HTTP_USER_AGENT'];
            $referer = $_SERVER['HTTP_REFERER'];
            
            
            addlogfile('ERROR', $file, $code, $line, $message, $trace, $ip, $ua, $referer);
        }
    }
    
}
