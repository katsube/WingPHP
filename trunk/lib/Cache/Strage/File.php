<?php
/* [WingPHP]
 *  - lib/Cache/Strage/File.php
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
 
uselib('Cache/Strage/if.cachestrage.php');

/**
 * FileCacheStrageクラス
 * 
 * example.<code>
 *     uselib('Cache/Strage/FileCacheStrage');
 *
 *     $cache = FileCacheStrage();
 *     $cache->expire = 60 * 60;           //キャッシュの有効時間を1時間に
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
 * @package    FileCacheStrage
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class FileCacheStrage implements CacheStrageIF{
	public $path   = null;
	public $expire = 0;

	/**
	 * コンストラクタ
	 *
	 * @param  string  $path  格納先
	 * @access public
	 */
	public function __construct($path = null){
		//ディレクトリ
		$this->path = $this->_setpath($path);
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
		//ファイル名
		$file = $this->_getfilename($id);
		
		//記録
		$this->_writefile(
			  $file
			, serialize($value)
		);
	}

	/**
	 * ゲッター
	 *
	 * @param  string  $id  キャッシュID
	 * @return mixed   存在しない場合はnull, 存在する場合は値
	 * @access public
	 */
	public function get($id){
		//ファイル名
		$file = $this->_getfilename($id);

		//有効期限チェック
		if( ! $this->_is_expire($file) ){
			unlink($file);
			return(null);
		}
		
		//値を返却
		return( $this->_readfile($file) );
	}

	/**
	 * 指定IDのキャッシュを削除する
	 *
	 * @param  string  $id  キャッシュID
	 * @access public
	 */
	public function del($id){
		$file = $this->_getfilename($id);
		unlink($file);
	}

	/**
	 * 指定IDのキャッシュが存在するか確認する
	 *
	 * @param  string  $id  キャッシュID
	 * @return bool
	 * @access public
	 */
	public function exists($id){
		$file = $this->_getfilename($id);

		if( is_file( $file ) ){
			if( $this->_is_expire($file) )
				return(true);

			unlink($file);
		}
	
		return(false);
	}

	/**
	 * 全てのキャッシュを破棄する
	 *
	 * @access public
	 */
	public function flush(){
		$files = scandir($this->path);
		$len   = count($files);
		
		chdir($this->path);
		for($i=2; $i<$len; $i++){		//'.' '..' をパスし2から始める 
			unlink($files[$i]);
		}
	}



	//--------------------------------------------
	// Private
	//--------------------------------------------
	/**
	 * 保存先のパスを設定
	 *
	 * @param  string  $path  パス
	 * @access private
	 */
	private function _setpath($path=null){
		//パス未指定なら自動生成
		if(is_null($path))
			$path = sprintf('%s/.wingphp-cache/', sys_get_temp_dir());
		
		//ディレクトリが存在しないなら作成
		if( (!is_dir($path)) && !mkdir($path) )
			die('directory, not found and do not create.');		//作成できなければ死
	
		return($path);
	}
	
	/**
	 * ファイル名を作成
	 *
	 * @param  string  $id  キャッシュID
	 * @access private
	 */
	private function _getfilename($id){
		return(
			sprintf('%s/%s.txt', $this->path, sha1($id) )
		);
	}
	
	/**
	 * ファイルに記録
	 *
	 * @param  string  $file   保存先のファイルパス
	 * @param  mixed   $value  保存する値
	 * @access private
	 */
	private function _writefile($file, $value){
		//開いてロック
		$fp = fopen($file, 'a');
		flock($fp, LOCK_EX);
		
		//ファイルを空に
		ftruncate($fp,0);
		rewind($fp);			//ポインタを先頭へ
		
		//保存
		fwrite($fp, $value);
		
		//閉じる
		flock($fp, LOCK_UN);
		fclose($fp);
	}
	
	/**
	 * 有効期限内かチェック
	 *
	 * @param  string  $file ファイルパス
	 * @access private
	 */
	private function _is_expire($file){
		$expire = $this->expire;
	
		//ゼロ＝期間なし
		if($expire === 0)
			return(true);
		
		//期間チェック
		$mtime = filemtime($file);
		$now   = time();

		return( (($now - $mtime) <= $expire) );
	}
	
	/**
	 * ファイルの内容を返却する
	 *
	 * @param  string  $file ファイルパス
	 * @access private
	 */
	private function _readfile($file){
		$buff = file_get_contents($file);
		return(unserialize($buff));
	}
}
?>