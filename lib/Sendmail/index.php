<?php
/* [WingPHP]
 *  - lib/Sendmail/index.php
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
 * Sendmailクラス
 * 
 * example.<code>
 *     uselib('Sendmail');
 *
 *     $mail = new Sendmail();
 *     $mail->headers([
 *          'To'      => 'bar@example.net'
 *        , 'From'    => 'foo@example.com'
 *        , 'Subject' => 'Hello!'
 *     ]);
 *     $mail->body($string);
 *     $mail->doit();
 * </code>
 *
 * @package    Sendmail
 * @copyright  2016 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class Sendmail {
    private $language = 'Japanese';     //mb_language
    private $encode   = 'UTF-8';        //mb_internal_encoding

    private $header = array(
                              'From'         => null
                            , 'Sender'       => null
                            , 'To'           => null
                            , 'Cc'           => null
                            , 'Bcc'          => null
                            , 'Subject'      => null
                            , 'Reply-to'     => null
                            , 'Return-Path'  => null
                            , 'Errors-To'    => null
                            , 'Date'         => null
                            , 'In-Reply-To'  => null
                            , 'References'   => null
                            , 'Message-ID'   => null
                            , 'Precedence'   => null                        //list > junk > bulk
                            , 'Content-type' => 'text/plain'
                            , 'MIME-Version' => '1.0'
                            , 'X-Mailer'     => 'WingPHP Sendmail library'
                        );

    private $body = null;
    private $logging = false;


	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	public function __construct(){
        global $Conf;
        
        //----------------------------
        // set default value
        //----------------------------
        if(array_key_exists('Sendmail', $Conf)){
            //mb_language
            if(array_key_exists('language', $Conf['Sendmail']))
                $this->setLanguage($Conf['Sendmail']['language']);

            //mb_internal_encoding
            if(array_key_exists('encode', $Conf['Sendmail']))
                $this->setEncording($Conf['Sendmail']['encode']);
            
            //Header
            if(array_key_exists('header', $Conf['Sendmail']))
                $this->headers($Conf['Sendmail']['header']);
        
            //Logging
            if(array_key_exists('log', $Conf['Sendmail'])){
                if(array_key_exists('on', $Conf['Sendmail']['log'])){
                    $this->setLogging($Conf['Sendmail']['log']['on']);
                }
            }
            
        }
	
	}


	/*--------------------------------------------
	 * ■ Public ■
	 *--------------------------------------------
	 * - headers
	 * - getHeaders
	 * - body
	 * - doit
	 * - setLanguage
	 * - getLanguage
	 * - setEncording
	 * - getEncording
	 * - setLogging
	 * - isLogging
	 *--------------------------------------------*/
    /**
     * set mail headers
     * 
     * @param  array $opt
     * @return boolean
     * @access public
     */
    public function headers($opt){
        if( ! is_array($opt) ){
            return(false);
        }

        foreach($opt as $key => $value){
            if( array_key_exists($key, $this->header) ){
                $this->header[$key] = $value;
            }
            else{
                return(false);
            }
        }
    
        return(true);
    }
    
    /**
     * get mail headers
     * 
     * @param  string  $name  特定の項目がほしい場合に指定
     * @param  boolean $null  null項目
     * @return boolean
     * @access public
     */
    public function getHeaders($name=null, $null=false){
        //-----------------------
        //項目指定
        //-----------------------
        if( $name !== null ){
            if( array_key_exists($name, $this->header) ){
                return($this->header[$name]);
            }
            else{
                return(false);
            }
        }
        //-----------------------
        // 全て取得
        //-----------------------
        else{
            $head = array();
            foreach($this->header as $key => $value){
                if( $null === false && $value === null )
                    continue;
                else
                    $head[$key] = $value;
            }
        
            return($head);
        }
    }

    /**
     * set mail body string
     * 
     * @param  string   $str
     * @return boolean
     * @access public
     */
    public function body($str){
        if( ! is_string($str) ){
            return(false);
        }
        
        $this->body = $str;
        return(true);
    }
    
    /**
     * Sendmail now.
     * 
     * @return boolean
     * @access public
     */
    public function doit(){
        if( ! $this->_checkParam() ){
            throw new WsException('[Sendmail::doit] illegal parameter.', 500);
        }

        mb_language($this->language);
        mb_internal_encoding($this->encode);

        $to      = $this->header['To'];
        $subject = $this->header['Subject'];
        $body    = $this->_makeBody();
        $headers = $this->_makeHeader();

        $ret = mb_send_mail($to, $subject, $body, $headers);
        if(!$ret){
            throw new WsException('[Sendmail::doit] Can not Sendmail', 500);
        }
        
        if($this->logging){
            global $Conf;
            
            uselib('Logger');
            $log = new Logger();
            $log->setName('MAIL');
            $log->info('Sendmail', $to);
            
            if( $Conf['Sendmail']['log']['snapshot'] ){
                $log->snapshot(
                      $Conf['Sendmail']['log']['snapdir']
                    , $headers
                        . "\r\n"
                        . $body
                );
            }
        }
    }

    /**
     * set Language
     * 
     * @param  string  $cd
     * @return boolean
     * @access public
     */
    public function setLanguage($cd){
        if( is_string($cd) ){
            $this->language  = $cd;
            return(true);
        }
    
        return(false);
    }

    /**
     * get Language
     * 
     * @return string
     * @access public
     */
    public function getLanguage(){
        return($this->language);
    }

    /**
     * set Internal Encording
     * 
     * @param  string  $cd
     * @return boolean
     * @access public
     */
    public function setEncording($cd){
        if( is_string($cd) ){
            $this->encode  = $cd;
            return(true);
        }
    
        return(false);
    }

    /**
     * get Internal Encording
     * 
     * @return string
     * @access public
     */
    public function getEncording(){
        return($this->encode);
    }

    /**
     * set Logging Flag
     * 
     * @param  boolean  $flag
     * @return boolean
     * @access public
     */
    public function setLogging($flag){
        if( is_bool($flag) ){
            $this->logging  = $flag;
            return(true);
        }
    
        return(false);
    }

    /**
     * get Logging Flag
     * 
     * @return boolean
     * @access public
     */
    public function isLogging(){
        return($this->logging);
    }



	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - _checkParam
	 * - _makeHeader
	 * - _makeBody
	 *--------------------------------------------*/
    /**
     * Check Hearders and Body
     * 
     * @return boolean
     * @access private
     */
	private function _checkParam(){
	    //check header
	    foreach(array('To', 'From', 'Subject') as $key){
	        if( $this->header[$key] === null ){
	            return(false);
	        }
	    }
	    
	    //check body
        if( $this->body === null ){
	        return(false);
	    }
	    
	    return(true);
	}
	
    /**
     * Create mail header
     * 
     * @return string
     * @access private
     */
    private function _makeHeader(){
        foreach( $this->header as $key => $value ){
            if( $value !== null ){
                $headers .= sprintf("%s: %s\r\n", $key, $value);
            }
        }

        return($headers);
    }
    
    
    /**
     * Create mail body
     * 
     * @return string
     * @access private
     */
    private function _makeBody(){
        $body = $this->body;
        return($body);
    }
}
