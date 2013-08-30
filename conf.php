<?php
/* [WingPHP]
 *  - global configuration file.
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
 * システム全体で使用する設定変数
 *
 * @global array $GLOBALS['Conf']
 * @name $Conf
 */
$Conf = array(
	//■データベース設定
	'DB' => array(
		'master' => array(
			  'DSN'         => 'mysql:dbname=test;host=localhost'	//PDO
			, 'USER'        => 'username'
			, 'PASSWORD'    => 'password'
			, 'fetch_style' => PDO::FETCH_ASSOC		//http://www.php.net/manual/ja/pdostatement.fetch.php
			, 'persistent'  => false				//http://php.net/manual/ja/pdo.connections.php
		)

		# 複数のDBを切替えて利用する場合は、あらかじめここで設定を行います。
		#
		# ■モデルでの利用例
		#   $this->userdb('slave1');					//slave1にDB切替
		#   $this->usedb(array('slave1', 'slave2'));	//slave1, slave2にランダム切替
		#
		# ■設定例
		#, 'slave1' => array(
		#	  'DSN'         => 'mysql:dbname=test2;host=localhost'
		#	, 'USER'        => 'username'
		#	, 'PASSWORD'    => 'password'
		#	, 'fetch_style' => PDO::FETCH_ASSOC
		#	, 'persistent'  => false
		#)
	)
	
	//■秘密鍵
	, 'Secret' => array(
		'key' => 'aqwsdertyhjiolpzsxcfvgbnjmk,l.;/:'				//適当な文字列に変更してください。
	)

	//■セッション設定
	, 'Session' => array(
		'name' => 'SESSID'
	)
	
	//■ライブラリ
	, 'Lib' => array(
		'dir' => '../lib/'
	)
	
	//■キャッシュ
	, 'Cache' => array(
		  'strage'  => 'File'				//'File' or 'MemCache'
		, 'expire'  => 3600					//秒数を指定。 0=無期限
		
		//DB系
		, 'db_use'    => false					//ModelのDB系メソッドでキャッシュするか
		, 'db_pre'    => '__lib.dbcache'		//ModelのDB系メソッドのキャッシュIDの先頭文字列
		, 'db_expire' => 3600					//キャッシュ寿命
		
		//WebAPI系
		, 'api_use'    => false					//WebAPI系ライブラリでキャッシュするか
		, 'api_pre'    => '__lib.netapicache'	//WebAPI系ライブラリのキャッシュIDの先頭文字列
		, 'api_expire' => 3600					//キャッシュ寿命
	)
	
	//■View設定
	, 'Smarty' => array(
		  'version' => '3.1'					//'3.1'固定
		, 'tmpl'    => '../view/'
		, 'tmpl_c'  => '../temp/smarty/templates_c/'
		, 'plugin'  => array('plugins', realpath('../lib/smarty/wingplugin'))
		, 'config'  => '../lib/smarty/configs/'
		, 'cache'   => '../temp/smarty/cache/'
		
		, 'is_cache'   => false
		, 'cache_life' => 0
		//, 'cache_life' => 60 * 60	//秒
	)
);


/**
 * Routingクラス
 * 
 * ルーティングクラスです。
 * ルーティングを行う場合は、直接このクラスを書き換えてください。
 *
 * コンストラクタの処理が終了後、最終的にプロパティctrl, method, paramに
 * 入っている名称のクラスとメソッドが実行されます。
 *
 * (現状だと)uselibが使えないので、あくまでルーティングに徹し、複雑な処理は
 * 他にまかせるような実装がオススメです。また全リクエストで実行されるので
 * 最小限に留めないとパフォーマンスに影響します。
 *
 * @package    Routing
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class Routing{
	public $ctrl;		//コントローラー
	public $method;		//メソッド
	public $param;		//パラメーター
	
	function __construct(framewing $obj){
		$this->ctrl   = $obj->ctrl_name;		//FoobarController
		$this->method = $obj->method_name;		//index
		$this->param  = $obj->param;			//array('huga1', 'huga2', ...)
	}
}
?>
