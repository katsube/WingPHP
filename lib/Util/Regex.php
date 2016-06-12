<?php
/* [WingPHP]
 *  - lib/Util/Regex.php
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
 * Regexクラス
 * 
 * 正規表現を集めたクラス。
 * preg_matchなどでそのまま利用することを想定して書かれています。
 *
 * example.<code>
 *     uselib('Util/Regex');
 *     echo Regex::URL;
 * </code>
 *
 * @package    Regex
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class Regex{
	//ネット系
	const URL   = '/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/';
	const EMAIL = '/^([a-zA-Z0-9\.\_\-\/\+]+)@([a-zA-Z0-9\._\-]+)\.([a-zA-Z]+)$/';
	const IP4   = '/^(\d|[01]?\d\d|2[0-4]\d|25[0-5])\.(\d|[01]?\d\d|2[0-4]\d|25[0-5])\.(\d|[01]?\d\d|2[0-4]\d|25[0-5])\.(\d|[01]?\d\d|2[0-4]\d|25[0-5])$/';
	
	//ジオ的なもの
	const POST  = '/^[0-9]{3}\-?[0-9]{4}$/';	//7桁郵便番号 000-0000
	const POST7 = '/^[0-9]{7}$/';				//7桁郵便番号 0000000

	//電話
	const TEL = '/^0[0-9]{1,4}\-[0-9]{1,4}\-[0-9]{3,5}$/';		//0123-12-1234, 03-12-1234, 090-1234-1234

	//数値・文字列
	const NUM   = '/^([0-9]+)$/';						//数字のみ(0<=)
	const ALPHA = '/^([a-zA-Z]+)$/';					//アルファベットのみ、1つ以上
	const ALNUM = '/^([a-zA-Z0-9]+|[\-\.0-9]+)$/';		//数字かアルファベットのみ、1つ以上

	//空白
	const BLANK     = '/^\s*$/';				//ホワイトキャラはOK
	const BLANK_ABS = '/^$/';					//ホワイトキャラも何も含まない(	絶対的空白)
	
	//空行
	const BLANK_LINE = '/^\r?\n/';
}
