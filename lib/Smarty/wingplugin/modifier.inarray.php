<?php
/* [WingPHP]
 *  - Smarty/wingplugin/modifier.inarray.php
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
 * in_array関数のラッパー
 *
 * Smartyテンプレート上でin_array関数を利用するためのラッパー。
 * 引数なし、または期待する物でなかった場合は一律falseを返す。
 *
 *   Example.<code>
 *      {* $foo配列の要素に 'bar' が存在すればtrue *}
 *      {if $foo|inarray:'bar'}
 *          bar in foo
 *      {/if}
 *   </code>
 *
 * @param  array   $array 検索対象の配列
 * @param  string  $key   検索キー用の文字列
 * @return boolean
 * @access public
 */
function smarty_modifier_inarray($array, $key){
	if( is_array($array) && !is_null($key) && in_array($key, $array) )
		return(true);
	else
		return(false);
}
