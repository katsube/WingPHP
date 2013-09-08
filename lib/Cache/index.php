<?php
/* [WingPHP]
 *  - lib/Cache/index.php
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
 * Cacheクラス
 * 
 * example.<code>
 *     uselib('Cache');
 *
 *     $cache = new Cache('File');
 *     $cache->expire(60 * 60);           //キャッシュの有効時間を1時間に
 *
 *     if(!$cache->exists('name'))
 *         $cache->set('name', $value);   //保存
 *     else{
 *         echo $cache->get('name');      //取得
 *         $cache->del('name');           //削除
 *     }
 *
 *     //キャッシュを全削除
 *     $cache->flush();
 * </code>
 *
 * @package    Cache
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class Cache {
	private $strage = null;

	/**
	 * コンストラクタ
	 *
	 * @param  array  $strage  
	 * @access public
	 */
	public function __construct($strage, $opt=null){
		switch( $strage ){
			//ファイルキャッシュ
			case 'File':
				uselib('Cache/Strage/File');
				$this->strage = new FileCacheStrage($opt);
				break;

			//MemCached
			case 'MemCache':
				uselib('Cache/Strage/MemCache');
				$this->strage = new MemCacheStrage($opt);
				break;

			//？
			default:
				die("do not support strage $strage");
		}
	}

	/**
	 * メソッドのオーバーライド
	 */
	function __call($name, $param){
		return( 
			call_user_func_array(
				  array($this->strage, $name)
				, $param
			)
		);
	}
}
