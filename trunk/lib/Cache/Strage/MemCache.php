<?php
/* [WingPHP]
 *  - lib/Cache/Strage/MemCache.php
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
 
uselib('Cache/Strage/if.cachestrage');

/**
 * MemCacheStrageクラス
 * 
 * example.<code>
 *     uselib('Cache/Strage/MemCache');
 *
 *     $cache = MemCacheStrage();
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
 * @package    MemCacheStrage
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class MemCacheStrage implements CacheStrageIF{
	public  $expire   = 0;
	public  $compress = MEMCACHE_COMPRESSED;
	private $memcache = null;

	/**
	 * コンストラクタ
	 *
	 * @param  array  $server  memcached可動サーバ。複数渡すことも可。
	 * @access public
	 */
	public function __construct($server=null){
		if( $server === null )
			$server = array( 'localhost' => 11211 );

		$memcache = new Memcache();
		foreach($server as $host => $port)
			$memcache->addServer($host, $port);
		
		$this->memcache = $memcache;
	}


	//--------------------------------------------
	// Public
	//--------------------------------------------
	/**
	 * セッター
	 *
	 * @param  string  $id     キャッシュID
	 * @param  mixed   $value  値。シリアライズするため配列なども可
	 * @access public
	 */
	public function set($id, $value){
		$this->memcache->set($id, $value, $this->compress, $this->expire);
	}

	/**
	 * ゲッター
	 *
	 * @param  string  $id  キャッシュID
	 * @return mixed   存在しない場合はnull, 存在する場合は値
	 * @access public
	 */
	public function get($id){
		$ret = $this->memcache->get($id);

		if($ret)
			return($ret);
		else
			return(null);
	}

	/**
	 * 指定IDのキャッシュを削除する
	 *
	 * @param  string  $id  キャッシュID
	 * @access public
	 */
	public function del($id){
		$this->memcache->delete($id);
	}

	/**
	 * 指定IDのキャッシュが存在するか確認する
	 *
	 * @param  string  $id  キャッシュID
	 * @return bool
	 * @access public
	 */
	public function exists($id){
		$ret = $this->memcache->get($id);

		if($ret === false)
			return(false);
		else
			return(true);
	}

	/**
	 * 全てのキャッシュを破棄する
	 *
	 * @access public
	 */
	public function flush(){
		$this->memcache->flush();
	}

	/**
	 * 有効期限をセットする
	 *
	 * @access public
	 */
	public function expire($sec){
		$this->expire = $sec;
	}	
}
?>