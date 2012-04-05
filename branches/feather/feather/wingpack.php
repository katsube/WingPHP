<?php
/* [WingPHP]
 *  - WingPackController
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
 * WingPackControllerクラス
 *
 * Google CloserCompiler API を利用し、指定されたJavaScriptの圧縮を行う。
 * viewで <script src="/wingpack/js?src=xxx"> などとするだけで自動的に圧縮が
 * 行われるため、手間がかからない。
 *
 * 概要。
 *  - ファイルは複数指定でき、ひとつのファイルとして結合される。
 *  - 一度APIを通したソースは、オリジナルのファイルが更新されるまでの間
 *    キャッシュされる。
 *  - ファイル内のソースに何らかのエラー(Syntaxなど)があった場合は
 *    オリジナルのファイルを返却する。
 *
 * 注意点。
 *  - オリジナルのファイルは wingphp/htdocs/js 配下に設置。
 *  - wingphp/htdocs/js に書き込み権限を付与してください。
 *    (配下のディレクトリも同様)
 *  - 拡張子は指定しないでください。
 *    (validation簡略化のため)
 *  - expires, gzip圧縮などは行わない。必要であればWebサーバで設定するか、
 *    ソースを書き足してください。
 *
 * Tips
 *  - クエリーに nocache=1 を付与するとキャッシュが利用されない。
 *    (Googleのサーバに負荷がかかるので開発時以外は使用しないこと)
 *
 *
 * example.
 *   <!-- htdocs/js/foo.js を呼ぶ -->
 *   <script src="/wingpack/js?src=foo.js"></script>
 *
 *   <!-- htdocs/js/hoge/foo.js を呼ぶ -->
 *   <script src="/wingpack/js?src=hoge/foo.js"></script>
 *
 *   <!-- foo.js と htdocs/js/hoge/bar.js を呼ぶ -->
 *   <script src="/wingpack/js?src=foo.js,hoge/bar.js"></script>
 *
 *   <!-- キャッシュしない -->
 *   <script src="/wingpack/js?src=foo.js&nocache=1"></script>
 *
 *
 * @package    WingPackController
 * @copyright  2012 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class WingPackController extends BaseController{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	private $js_basedir  = 'js/';		//JS保持ディレクトリ
	private $js_ext      = 'js';		//JS拡張子(オリジナル)
	private $js_extcache = 'min.js';	//JS拡張子(キャッシュ用)

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	function __construct(){
		;
	}

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	function __destruct(){
		;
	}

	/*--------------------------------------------
	 * ■ Public ■
	 *--------------------------------------------
	 * - index
	 *--------------------------------------------*/
	/**
	 * トップ表示
	 *
	 * @access public
	 */
	public function index(){
		;
	}

	/**
	 * JavaScriptを圧縮(最適化)
	 *
	 * @access public
	 */
	public function js(){
		$q       = new QueryModel();
		$src     = $q->data('src');
		$nocache = $q->data('nocache');
	
		//-------------------------
		// validation
		//-------------------------
		//src, 入力チェック
		if( $src === false ||  preg_match('/^([0-9a-zA-Z\,\_\-,\/]{1,})$/', $src) === 0  ){
			echo "<!-- validation error, src -->";
			return(false);
		}

		//-------------------------
		// JSの内容を取得
		//-------------------------
		$files = explode(',', $src);
		foreach($files as $file){
			//パス作成
			$file_path  = sprintf('%s/%s.%s', $this->js_basedir, $file, $this->js_ext);
		
			//存在チェック
			if( ! -f $file_path){
				echo "<!-- no such file. ". htmlentities($file_path) ." -->";
				return(false);
			}
		
			//ファイル内容取得
			
		}
	
	
		uselib('Google/ClosureCompiler');
		$gcc = new GoogleClosureCompiler();
		$gcc->curl_use(false);
	
		if( $gcc->request('(function(){alert(\'Hello\');})();') ){
			echo $gcc->getSourceLastRequest();
		}
		else
			var_dump( $gcc->getErrorMessageLastRequest() );
	}
	 
	/*--------------------------------------------
	 * ■ Private ■
	 *--------------------------------------------
	 * - 
	 *--------------------------------------------*/
}
?>