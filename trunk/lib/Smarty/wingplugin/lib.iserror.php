<?php
/* [WingPHP]
 *  - Smarty/wingplugin/block.iserror.php
 *
 * The MIT License
 * Copyright (c) 2013 WingPHP < http://wingphp.net >
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
 * Validation用エラー判定
 *
 * block.iserror, block.isnoterror で利用されるValidationのエラー判定の
 * 実処理を行う。
 *
 * @param  string  $name 対象項目名
 * @return string
 * @access public
 */
 function smarty_wgplugin_iserror($name = null){
	global $Scratch;
	$is_error = false;

	if( $name === null ){	// $name未指定時は全エラー
		$is_error = (array_key_exists('error', $Scratch['form']) && count($Scratch['form']['error']) >= 1);
	}
	else{
		$is_error = (array_key_exists('error', $Scratch['form']) &&  array_key_exists($name, $Scratch['form']['error']));
	}

	return($is_error);
}
