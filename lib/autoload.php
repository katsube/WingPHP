<?php
/* [WingPHP]
 *  - autoload
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
 * autoload
 * 
 * @param  string  $className
 * @return mixed   Model|Controller  string  ロードしたファイルパス
 *                 Smarty            boolean false固定
 *                 lib               array   uselibの戻り値をそのまま
 * @access public
 */
function _wingAutoload($className){
	if( preg_match('/^(.*)(Model|Controller)$/', $className, $match) > 0 ){
		$file = strtolower($match[1]) . '.php';
		$dir  = strtolower($match[2]);

		if( is_file("../$dir/$file") ){
			include_once("../$dir/$file");
			return("../$dir/$file");
		}
		else{
			return(false);
		}
		// framewing::go() の is_callable() 時に例外が発生してしまうのでコメントアウト
		//else{
		//	throw new WsException("Can not open Model|Controller $className", 500);
		//}
	}
	else if( preg_match('/^Smarty_/', $className) > 0 ){		//Smarty3.1
		return(false);
	}
	else{
		$ret = uselib($className);
		return($ret);
	}
}

spl_autoload_register('_wingAutoload');
