<?php
/* [WingPHP]
 *  - Smarty/wingplugin/block.isnoterror.php
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

require_once('lib.iserror.php');


/**
 * Validation用エラー判定(not)
 *
 * 指定項目がValidationでエラー *ではない* 場合、ブロック内が返却される。
 *
 *   Example.<code>
 *     <inpu type="text" name="foo">
 *     {isnoterror name="foo"}
 *       <p>fooは検証ルールを通過しました</p>
 *     {/iserror}
 *   </code>
 *
 * @param  string  $name 対象項目名
 * @return string
 * @access public
 */
function smarty_block_isnoterror($params, $content, &$smarty, &$repeat){
	if(!$repeat){
		//-----------------------------
		// 引数取得
		//-----------------------------
		$name = isset( $params['name'] )?  $params['name']:null;

		//-----------------------------
		// エラー有無判定
		//-----------------------------
		$is_error = smarty_wgplugin_iserror($name);

		if(!$is_error)
			return($content);
		else
			return(null);
	}
}
