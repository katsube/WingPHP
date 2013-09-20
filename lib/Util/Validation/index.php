<?php
/* [WingPHP]
 *  - lib/Util/Validation/index.php
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


uselib('Util/Regex');
uselib('Util/Validation/Message');


/**
 * Validationクラス
 * 
 * 入力値のチェックを汎用的に行うクラス。
 *   ToDo: 若干煩雑なのでもう少しコード量を減らせないか…。
 *
 * example.<code>
 * <?
 * //---------------------
 * // Controller
 * //---------------------
 * class FooController extends BaseController{
 *   const MODE = 'form';
 * 
 *   public function form(){
 *     $this->display('foo/form.html');
 *   }
 * 
 *   public function check(){
 *     uselib('Util/Validation');
 * 
 *     // インスタンス生成
 *     $v = new Validation(self::MODE);   // mode に"form" を指定＝クエリー値が対象になる
 * 
 *     // 独自のルールを追加する場合は事前に定義する
 *     $v->addRule('hoge', function($userid){             // 必ずbooleanを返す無名関数を渡す
 *                                    if(...)
 *                                        return(true);   // trueで通過
 *                                    else
 *                                        return(false);  // falseでエラー
 *                                }
 *     );
 *
 *     // 検証リストを設定
 *     $v->addList(array(
 *              'bar'    => array('require', 'num')                       // 必須, 数値
 *            , 'postcd' => array('post')                                 // 郵便番号
 *            , 'userid' => array('hoge')                                 // 独自ルールも同様に指定できる
 *            , 'name'   => array(['minlen',3], ['maxlen',10], 'alnum')   // 値指定
 *     ));
 *     $v->addList(array('hoge'=>array('require')));      //setRuleを再び呼ぶと既存の検証リストに追加される
 *
 *     // $v->clearList();   //現在定義されている検証リストをクリアしたい場合はこちら
 * 
 *
 *     // チェックするデータを定義（省略可）
 *     // $q = new QueryModel();
 *     // $v->addData(array('userid' => $q->data('bar')));    //addDataでvalidation対象を追加する
 * 
 *     if ( $v->check() ){
 *       // 成功時処理
 *       $m = new FooModel();
 *       $m->add();
 *     }
 *     else{
 *       // エラー時処理
 *       $v->setError2Scratch();                   //$Scratch['form']['foo']['error'] 以下にエラー情報を格納
 *       $this->display('foo/form.html');
 *     }
 *   }
 * }
 * 
 * //---------------------
 * // View
 * //---------------------
 * <form action="/foo/check">
 *   {input type="text" name="bar" errclass="warning"} <!-- error時にclassを追加する -->
 *   {input type="text" name="bar" notsetvalue=true}   <!-- 自動的に入力値をセットしない -->
 *   
 *   {iserror name="bar"}<p>{errormsg name="bar"}</p>{/iserror}
 *   {iserror name="bar"}<p>独自のメッセージを使いたい場合はこんな感じで</p>{/iserror}
 * 
 *   <input type="submit">
 * </form>
 * </code>
 *
 * @package    Validation
 * @copyright  2013 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class Validation{
	//---------------------------------------------
	// メンバ変数
	//---------------------------------------------
	private $mode = null;				// 動作モード

	private $list   = array();			// 検証ルール格納用
	private $target = array();			// 検証データ格納用
	
	private $vmsg      = null;			// ValidationMessageオブジェクト入れ
	private $error     = array();		// エラーコード格納用
	private $errormsg  = array();		// エラーメッセージ格納用

	private $rule = array();			// 評価用のClosure格納用


	/**
	 * コンストラクタ
	 * 
	 * @param  string $mode 動作モード 'self' or 'form'
	 * @param  string $lang エラーメッセージ用言語 'ja'固定
	 * @return void
	 * @access public
	 */
	function __construct($mode='self', $lang='ja'){
		$this->mode     = $mode;

		//-------------------------------
		// 検証ルールを差込む
		//-------------------------------
		//ToDo: 気持ち悪いのであとで他ファイルに分割したい。
		$this->rule     = array(
			  'url'   => function($val){ return(preg_match(Regex::URL,   $val)); }		// 書式 URL
			, 'email' => function($val){ return(preg_match(Regex::EMAIL, $val)); }		// 書式 メールアドレス
			, 'ip4'   => function($val){ return(preg_match(Regex::IP4,   $val)); }		// 書式 IPv4形式
			, 'post'  => function($val){ return(preg_match(Regex::POST,  $val)); }		// 書式 郵便番号 000-0000
			, 'post7' => function($val){ return(preg_match(Regex::POST7, $val)); }		// 書式 7桁数値  0000000
			, 'tel'   => function($val){ return(preg_match(Regex::TEL,   $val)); }		// 書式 電話番号 0123-12-1234, 03-12-1234, 090-1234-1234
			, 'num'   => function($val){ return(preg_match(Regex::NUM,   $val)); }		// 書式 半角数字(文字列としての数字も真)
			, 'alpha' => function($val){ return(preg_match(Regex::ALPHA, $val)); }		// 書式 半角英字
			, 'alnum' => function($val){ return(preg_match(Regex::ALNUM, $val)); }		// 書式 半角英数字
	
			, 'require' => function($val){ return( isset($val) ); }						// 必須項目
			, 'bytemax' => function($val, $opt){ return( strlen($val) <= $opt[0] );  }	// 最大バイト長
			, 'bytemin' => function($val, $opt){ return( strlen($val) >= $opt[0] ); }	// 最小バイト長
			, 'max'     => function($val, $opt){ return( $val <= $opt[0] ); }			// 最大値
			, 'min'     => function($val, $opt){ return( $val >= $opt[0] ); }			// 最小値

			, 'match' => function($val, $opt){ return(preg_match($opt[0], $val)); }		// 指定した正規表現にマッチするか
			, 'eq'    => function($val, $opt){ return( $val === $opt[0] ); }			// 指定した文字列と同じか
			, 'ne'    => function($val, $opt){ return( $val !== $opt[0] ); }			// 指定した文字列と違うか
			, 'in'    => function($val, $opt){ return( in_array($val, $opt)); }			// 指定したリスト内のいずれかと合致するか
		);

		//-------------------------------
		// デフォルトの検証データ差込み
		//-------------------------------
		if( $mode === 'form' ){
			$this->addData( array_merge($_GET, $_POST) );
		}

		//-------------------------------
		// エラーメッセージ準備
		//-------------------------------
		$vmsg = new ValidationMessage();
		$vmsg->setLanguage($lang);
		$this->vmsg = $vmsg;
	}


	/**
	 * 検証リストに追加する
	 * 
	 * 次のように第一引数にルールを指定する。
	 *   array(
	 *        '名前1' => array('検証名1', '検証名2' ... '検証名n');
	 *      , '名前2' => array(callback($name));
	 *   );
	 * 検証名は複数記入できる。
	 *   - 複数記入した場合はand条件になる。
	 * 独自の関数を指定することができる
	 *   - 検証名との併記も可能
	 *   - 独自関数を複数併記することも可能
	 * 2回目以降呼び出された場合は、既存のリストに追加される。 
	 *
	 * @param  array $rule ルール格納用
	 * @return void
	 * @access public
	 */
	public function addList($list){
		if( is_array($list) )
			$this->list = array_merge($this->list, $list);
	}

	/**
	 * 検証リストをリセットする
	 * 
	 * setRuleで定義されたルールをすべてリセットします
	 * 
	 * @return void
	 * @access public
	 */
	public function clearList(){
		$this->list = array();
	}

	/**
	 * 検証リストを取得する
	 * 
	 * 現状オブジェクト内にある検証リストを取得する。
	 * $nameを未指定の場合はすべての、
	 * $nameを指定した場合は該当する項目を返却する。
	 *
	 * @param  array $name 取得したい項目名(任意)
	 * @return mixed $name未指定時:全リスト, $name指定時:個別, $name指定時未存在:false  
	 * @access public
	 */
	public function getList($name=null){
		if( $name === null  ){
			return( $this->list );
		}
		else if( array_key_exists($name, $this->list) ){
			return( $this->list[$name] );
		}
		else{
			return(false);
		}
	}


	/**
	 * 検証ルールを追加する
	 * 
	 * ルールに独自関数を追加します。
	 * 同名のルールがある場合は上書きされます。
	 * 
	 * @param  string  $name ルール名
	 * @param  object  $func 実行する無名関数(ClosureObject)
	 * @param  string  $msg  エラー時のメッセージ(任意)
	 * @return void
	 * @access public
	 */
	public function addRule($name, $func, $msg=null){
		$this->rule[$name] = $func;
		$this->vmsg->set($name, $msg);
	}
	
	/**
	 * 検証ルールを取得する
	 * 
	 * 現状オブジェクト内にある検証ルールを取得する。
	 * $nameを未指定の場合はすべての、
	 * $nameを指定した場合は該当するルールを返却する。
	 *
	 * @param  array $name 取得したいルール名(任意)
	 * @return mixed $name未指定時:全ルール, $name指定時:個別, $name指定時未存在:false  
	 * @access public
	 */
	public function getRule($name=null){
		if( $name === null  ){
			return( $this->rule );
		}
		else if( array_key_exists($name, $this->rule) ){
			return( $this->rule[$name] );
		}
		else{
			return(false);
		}
	}

	/**
	 * 検証用データを追加する
	 * 
	 * 検証データを既存のデータ一覧に追加する。
	 * 同名のデータが存在する場合は上書きされる。
	 *
	 * @param  array $data 検証用データ格納用
	 * @return void
	 * @access public
	 */
	public function addData($target){
		if( is_array($target) )
			$this->target = array_merge($this->target, $target);
	}

	/**
	 * 検証用データを取得する
	 * 
	 * 現状オブジェクト内にある検証データを取得する。
	 * $nameを未指定の場合はすべての、
	 * $nameを指定した場合は該当するデータを返却する。
	 *
	 * @param  array $name 取得したいデータ名(任意)
	 * @return mixed $name未指定時:全データ, $name指定時:個別データ, $name指定時未存在:false  
	 * @access public
	 */
	public function getData($name=null){
		if( $name === null  ){
			return( $this->target );
		}
		else if( array_key_exists($name, $this->target) ){
			return( $this->target[$name] );
		}
		else{
			return(false);
		}
	}

	/**
	 * 検証用データをリセットする
	 * 
	 * 現状オブジェクト内にある検証データをすべて削除します。
	 *
	 * @return void 
	 * @access public
	 */
	public function clearData(){
		$this->target = array();
	}



	/**
	 * 検証を実施する
	 * 
	 * 定義された検証用ルールに従って、データを検証する。
	 * 途中でルールに反した項目があった場合でもすべてのルールを
	 * 最後までチェックする。
	 * 
	 * @return boolean 全項目をクリアでtrue, エラーがあればfalse
	 * @access public
	 */
	public function check(){
		$list       = $this->list;
		$target     = $this->target;
		$rule       = $this->rule;
		$flag_error = false;

		//------------------------------
		// ○データ項目数分回す
		//------------------------------
		foreach($list as $name => $array){
			$data = $target[$name];

			//------------------------------
			// ○ルール数分回す
			//------------------------------
			$len = count($array);
			for($i=0; $i<$len; $i++){
				$cur = $array[$i];
				$ret = true;

				//------------------------------
				// ◇オプション付き既存定義
				//------------------------------
				if(is_array($cur)){
					$func = $cur[0];
					$opt  = array_slice($cur, 1);

					if( array_key_exists($func, $rule) ){
						$ret = $rule[$func]($data, $opt);
					}
					else{
						$ret = false;
					}
				}
				//------------------------------
				// ◇既存定義
				//------------------------------
				else if( array_key_exists($cur, $rule) ){
					$func = $cur;
					$ret  = $rule[$cur]($data);
				}
				//------------------------------
				// ◇謎
				//------------------------------
				else{
					$func = '_404';
					$ret  = false;
				}

				//------------------------------
				// エラー時処理
				//------------------------------
				if(!$ret){
					$flag_error = true;
					$this->addError($name, $func);
				}
			}
		}

		return(!$flag_error);
	}

	/**
	 * エラー内容を$Scratchにセットする
	 * 
	 * check実行後にエラー結果を次の場所にセットする。
	 *  $Scratch[モード][フォーム名]['error']
	 *
	 * @return void
	 * @access public
	 */
	public function setError2Scratch(){
		global $Scratch;
		$mode  = $this->mode;
		$error = $this->error;
		$msg   = $this->errormsg;
		
		$Scratch[$mode]['error']    = $error; 
		$Scratch[$mode]['errormsg'] = $this->vmsg->gets(array_keys($msg));
	}

	/**
	 * エラー内容を取得する
	 * 
	 * 現状のエラー内容を返却する。
	 * 自分自身でエラー表示をコントロールしたい場合などに利用する。
	 *
	 * @return array  エラー内容
	 * @access public
	 */
	public function getError(){
		return( $this->error );
	}

	/**
	 * エラー内容を追加する
	 * 
	 * クラス内変数にエラー内容を追加する。
	 *
	 * @param  $name
	 * @param  $cd
	 * @return void
	 * @access public
	 */
	public function addError($name, $cd){
		if( ! array_key_exists($name,  $this->error) )
			$this->error[$name] = array();

		array_push( $this->error[$name], $cd);
		$this->errormsg[$cd] = 1;
	}
}


