<?php
/* [WingPHP]
 *  - global configuration file.
 *  
 * The MIT License
 * Copyright (c) 2009 WingPHP < http://wingphp.net >
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
  * システム全体で使用する設定変数
  *
  * @global array $GLOBALS['Conf']
  * @name $Conf
  */
$Conf = array(
	//■データベース設定
	'DB' => array(
			  'DSN'      =>'mysql:dbname=test;host=localhost'		//PDO
			, 'USER'     => 'username'
			, 'PASSWORD' => 'password'
	)
	
	//■秘密鍵
	//適当な文字列に変更してください。
	, 'Secret' => array(
			'key' => 'aqwsdertyhjiolpzsxcfvgbnjmk,l.;/:'
	)

	//■セッション設定
	, 'Session' => array(
			'name' => 'SESSID'
	)
	
	//■View設定
	, 'Smarty' => array(
		  'tmpl'   => '../view/'
		, 'tmpl_c' => '../lib/smarty/templates_c/'
		, 'config' => '../lib/smarty/configs/'
		, 'cache'  => '../lib/smarty/cache/'
		
		, 'is_cache'   => false
		, 'cache_life' => 0
		//, 'cache_life' => 60 * 60	//秒
	)
);
?>