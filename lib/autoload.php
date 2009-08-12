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

function __autoload($className){
	if( preg_match('/^(.*)(Model|Controller)$/', $className, $match) ){
		$file = strtolower($match[1]) . '.php';
		$dir  = strtolower($match[2]);
		
		if( is_file("../$dir/$file") )
			include_once("../$dir/$file");
		else{
			echo "can not read file ../$dir/$file";
			die();
		}
			
	}
	else if( strcmp($className, 'Smarty') == 0){
		include_once("../lib/smarty/Smarty.class.php");
	}
	else{
		$file = strtolower($className);
		if( is_file("../lib/$file.php") )
			include_once("../lib/$file.php");
		else{
			echo "can not read file ../lib/$file.php";
			die();
		}
	}
}
?>