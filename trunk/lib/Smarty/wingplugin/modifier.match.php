<?php
/* [WingPHP]
 *  - Smarty/wingplugin/modifier.match.php
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
 * preg_match関数のラッパー
 *
 * Smartyテンプレート上でpreg_match関数を利用するためのラッパー。
 *
 *   Example.<code>
 *      {* $foo内の文字列が Hello で始まっているか *}
 *      {if $foo|match:'/^Hello/'}
 *          foo is greeting.
 *      {/if}
 *   </code>
 *
 * @param  string  $subject  調査対象
 * @param  string  $pattern  正規表現
 * @return boolean
 * @access public
 */
function smarty_modifier_match($subject, $pattern){
	if( preg_match($pattern, $subject) )
		return(true);
	else
		return(false);
}
