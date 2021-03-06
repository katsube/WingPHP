<?php
/* [WingPHP]
 *  - lib/global.php
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
 * ライブラリを明示的に読み込む
 *
 * example.<code>
 *   uselib('Math');						// == require_once('../lib/Math.php');
 *   uselib('stdio', 'stdlib');			// == require_once('../lib/stdio.php');
 *										//    require_once('../lib/stdlib.php');
 *   //以下の二つは同じ意味。
 *   uselib('Net/index');
 *   uselib('Net');						//indexは省略可能。Net.phpがある場合はそっちを優先。
 *</code>
 *
 * @param  string ライブラリ名
 * @access private
 */
function uselib(){
	global $Conf;
	$dir  = $Conf['Lib']['dir'];
	$args = func_get_args();

	foreach ($args as $file){
		//通常
		$path = sprintf('%s/%s.php', $dir, $file);
		if( is_file($path) ){
			require_once($path);
			continue;
		}

		//ディレクトリ指定
		$path  = sprintf('%s/%s', $dir, $file);
		$path2 = sprintf('%s/index.php', $path);
		if( is_dir($path) && is_file($path2)){
			require_once($path2);
			continue;
		}

		die("Can not open library $file");
	}
}


/**
 * 指定URL(パス)へ遷移する
 *
 * HTTPヘッダがまだ送信されていない場合はLocationヘッダで、
 * すでに送信されている場合はmeta要素を出力する
 *
 * @param  string  $url   URL(パス)
 * @param  int     $sec   遷移までの秒数(meta出力時のみ有効)
 * @access public
 */
function location($url, $sec=0){
	if( ! headers_sent() ){
		$head = sprintf('Location: %s', $url);
		header($head);
	}
	else{
		$meta = sprintf('<meta http-equiv="refresh" content="%d;url=%s">', $sec, $url);
		echo $meta;
	}
}

/**
 * HTTPエラー表示を行う
 *
 * 標準のErrorControllerへリダイレクトするラッパー関数。
 *
 * @param  int  $code  HTTPステータスコード
 * @access public
 */
function http_error($code){
	$path = sprintf('/message/error/%s%s', $code, $_SERVER['REQUEST_URI']);
	location($path);
}

/**
 * デバグ用の簡易ログを記録する
 *
 *  $Conf で設定された内容に従い、指定されたログファイルに記録する。
 *  第一引数にファイル識別子、それ以降は記録したい文字列を指定する。
 *  (第二引数以降は可変長のため、いくら指定してもよい)
 *
 * example.<code>
 *   addlogfile('ERROR', $userid, $string, $foobar);
 * </code>
 *
 * @param  string   ファイル識別子
 * @param  mix       記録文字列　※可変長
 * @return boolean  成功時:true, 失敗時:false
 * @access public
 */
function addlogfile(){
	global $Conf;
	$args = func_get_args();
	$time = time();

	// ファイル識別子の存在チェック
	$file = $args[0];
	if(!array_key_exists($file, $Conf['Log']['file']))
		return(false);

	// ファイルパス作成
	$path = sprintf('%s/%s%s.%s'
				, $Conf['Log']['dir']
				, $Conf['Log']['file'][$file]
				, ($Conf['Log']['add'] === false)?  '' : date($Conf['Log']['add'], $time)
				, $Conf['Log']['ext']
			);

	// 書き込む文字列のチェック
	$len = count($args);
	$separate = $Conf['Log']['separate'];
	for($i=1; $i<$len; $i++ ){			//args[0] はファイル識別子なので飛ばす
		$args[$i] = preg_replace("/($separate|\r|\n)/", '', $args[$i]);
	}

	// 書き込む文字列を作成
	$str   = implode($separate, array_merge(
						  array( date('Y-m-d', $time), date('H:i:s', $time))
						, array_slice($args, 1)
					));
	$str .= ($Conf['Log']['addtrace'])?   $separate . json_encode(debug_backtrace()):'';
	$str .= "\n";

	// ファイルへ保存
	return(
		lockfwrite($path, $str)
	);
}



/**
 * flockしてファイルに書き込む
 *
 * fopen, flock, fwrite の一連の処理をまとめた便利関数。
 * 常に a モードでファイルをopenする。
 * 真っ白なファイルに戻した上で書き込みたい場合は、$reset に true を指定すると
 * ファイルサイズをゼロにし、ファイルポインタを冒頭に戻し書き込む。
 *
 * @param  string   $path   書き込み先ファイルのパス
 * @param  string   $str    ファイルに書き込む文字列
 * @param  boolean  $reset  ファイルサイズをゼロにするか
 * @return boolean  成功時:true, 失敗時:false
 * @access public
 */
function lockfwrite($path, $str, $reset=false){
	$fp = fopen($path, 'a');
	if( !$fp )
		return(false);

	if (flock($fp, LOCK_EX)){
		if($reset){
			ftruncate($fp, 0);
			rewind($fp);
		}
		fwrite($fp, $str);
		fflush($fp);
		flock($fp, LOCK_UN);
		fclose($fp);

		return(true);
	}
	else{
		fclose($fp);
		return(false);
	}
}


/**
 * 配列の最後の要素を取得する
 *
 * array_popでも最後の値が取得できるが、削除されてしまう。
 * またendはそのまま利用すると内部ポインタが移動してしまうため、
 * 共通関数化を行う。
 *
 * @param  array  対象配列
 * @return mixed
 * @access public
 */
function array_end($array){
	return( end($array) );
}
