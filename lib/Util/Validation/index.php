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
 *     $v->addRule(
 *         'hoge'
 *       , function($userid){        // 必ずbooleanを返す無名関数を渡す
 *           if(...) return(true);   // trueで通過
 *           else    return(false);  // falseでエラー
 *          }
 *       , 'エラーメッセージをここに記入'
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

	private $list   = array();		// 検証ルール格納用
	private $target = array();		// 検証データ格納用

	private $vmsg      = null;		// ValidationMessageオブジェクト入れ
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
		if( $mode === 'self' || $mode === 'form' )
			$this->mode = $mode;
		else
			$this->mode = 'self';

		//-------------------------------
		// 検証ルールを差込む
		//-------------------------------
		//ToDo: 気持ち悪いのであとで他ファイルに分割したい。
		$this->rule = array(
			//---------------------
			// 基本
			//---------------------
			//必須項目
			'require' => function($val){
								return(
										isset($val) && $val !== '' && $val !== []
								);
							}

			//---------------------
			// 書式
			//---------------------
			//URL
			, 'url' => function($val){
			  					return(
			  								$val === ''
			  							|| is_null($val)
			  							|| preg_match(Regex::URL, $val) === 1
			  					);
							}
			//EMail
			, 'email' => function($val){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| preg_match(Regex::EMAIL, $val) === 1
								); 
							}
			//IPv4
			, 'ip4' => function($val){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| preg_match(Regex::IP4, $val) === 1
								);
							}

			//郵便番号(ハイフンあり、なし両対応)
			, 'postcd' => function($val){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| preg_match(Regex::POST, $val) === 1
								);
							}

			//電話番号(ハイフンあり)
			, 'tel'=> function($val){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| preg_match(Regex::TEL, $val) === 1
								);
							}
			
			//---------------------
			// 文字列
			//---------------------
			//半角数字(文字列としての数字も真)
			, 'num' => function($val){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| is_numeric($val)
								);
							}
			
			//半角英字
			, 'alpha' => function($val){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| (is_string($val) && preg_match(Regex::ALPHA, $val) === 1)
								);
							}

			//半角英数字
			, 'alnum' => function($val){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| ( (is_string($val) || is_numeric($val))
														&& preg_match(Regex::ALNUM, $val) === 1 )
								);
							}
			//最大バイト長
			, 'bytemax' => function($val, $opt){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| (is_string($val) && strlen($val) <= $opt[0])
								);
							}

			// 最小バイト長
			, 'bytemin' => function($val, $opt){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| (is_string($val) && strlen($val) >= $opt[0])
								);
			
							}

			//---------------------
			// 数
			//---------------------
			// 最大値
			, 'max' => function($val, $opt){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| (is_numeric($val) && $val <= $opt[0])
								);
				
							}
			// 最小値
			, 'min' => function($val, $opt){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| ( is_numeric($val) && $val >= $opt[0])
								);
							}

			// ToDo:
			//, 'datemax' => function($val, $opt){}
			//, 'datemin' => function($val, $opt){}
			//, 'timemax' => function($val, $opt){}
			//, 'timemin' => function($val, $opt){}
			//, 'datebetween' => function($val, $opt){}

			//---------------------
			// 比較
			//---------------------
			// 指定した正規表現にマッチするか
			, 'match' => function($val, $opt){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| preg_match($opt[0], $val) === 1
								);
							}
			
			// 指定した文字列と同じか
			, 'eq' => function($val, $opt){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| $val === $opt[0]
								);
							}
			
			// 指定した文字列と違うか
			, 'ne' => function($val, $opt){
								return(
			  								$val === ''
			  							|| is_null($val)
										|| $val !== $opt[0]
								);
							}
			
			// 指定したリスト内のいずれかと合致するか
			, 'in'    => function($val, $opt){
				if( is_null($val) || $val === '')
					return(true);
				
				foreach ($opt as $tmp) {
					if( $val === $tmp )
						return(true);
				}
				
				return(false);
			}

			//---------------------
			// 時間
			//---------------------
			// 日付が妥当な物か
			//   $v->addList(array( 'year'=>['date', $q->year, $q->month, $q->day] ));	//yearで引っ掛けてチェックする
			, 'date'  => function($val, $opt){
				$year  = $val;
				$month = $opt[0];
				$day   = $opt[1];

				if( is_null($year) || $year === '' )
					return(true);

				if( is_int($year) && is_int($month) && is_int($day) )
					return( checkdate($month, $day, $year) );
				else
					return(false);
			}

			// 時間が妥当な物か(24時間制)
			, 'time'  => function($val, $opt){
				$hour = $val;
				$min  = $opt[0];
				$sec  = $opt[1];

				if( is_null($hour) || $hour === '' )
					return(true);

				if( is_int($hour) && is_int($min) && is_int($sec) )
					return(
						   ( 0 <= $hour && $hour <= 23 )
						&& ( 0 <= $min  && $min  <= 59 )
						&& ( 0 <= $sec  && $sec  <= 59 )
					);
				else
					return(false);
			}

			//---------------------
			// リスト
			//---------------------
			// 配列の要素中、1つ以上が入力されている
			, 'grequire1' => function($val){
				if( is_null($val) || $val === '' )
					return(true);

				if(!is_array($val))
					return(false);

				$len = count($val);
				for ($i=0; $i < $len; $i++)
					if(isset($val[$i]) && $val[$i] !== '')
						return(true);

				return(false);
			}

			// 配列の要素が、すべて指定したリスト内のいずれかと合致するか
			, 'gin' => function($val, $opt){
				if(is_null($val) || $val === '')
					return(true);

				if(!is_array($val) || !is_array($opt) )
					return(false);

				$len = count($val);
				for ($i=0; $i < $len; $i++){
					$value = $val[$i];
					$flag  = false;
					foreach($opt as $tmp){
						if($tmp === $value){
							$flag = true;
							break;
						}
					}
					
					if(!$flag)
						return(false);
				}

				return(true);
			}
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
	 *        '名前1' => array('ルール名1', 'ルール名2' ... 'ルール名n');
	 *      , '名前2' => array('ルール名n');
	 *   );
	 * 検証名は複数記入できる。
	 *   - 複数記入した場合はand条件になる。
	 * 2回目以降呼び出された場合は、既存のリストに追加される。
	 *
	 * @param  array   $list 追加する検証リスト
	 * @return boolean
	 * @access public
	 */
	public function addList($list=null){
		if($this->_validList($list)){
			$this->list = array_merge($this->list, $list);
			return(true);
		}

		return(false);
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
	 * @return boolean
	 * @access public
	 */
	public function addRule($name, $func, $msg=null, $lang='ja'){
		if( is_string($name) && is_callable($func) ){
			$this->rule[$name] = $func;
			$this->vmsg->set($name, $msg, $lang);
			
			return(true);
		}
		
		return(false);
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
	 * @return boolean
	 * @access public
	 */
	public function addData($target){
		if( $this->_validData($target) ){
			$this->target = array_merge($this->target, $target);
			return(true);
		}
		
		return(false);
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
			$data = (array_key_exists($name, $target))? $target[$name]:null;

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

	
	
	
	
	
	
	/**
	 * Listに追加する配列の検証
	 *
	 * @param  array   $list 検証リスト
	 * @return boolean
	 * @access private
	 */
	private function _validList($list){
		//---------------------------------
		//配列か
		//---------------------------------
		if( !is_array($list) )
			return(false);
		
		//---------------------------------
		//なめる
		//---------------------------------
		foreach($list as $key => $value){
			//---------------------------------
			// 文字列 => 配列 であるか
			//---------------------------------
			if( ! (is_string($key) && is_array($value)) ){
				return(false);
			}
			
			//---------------------------------
			// value検証
			//---------------------------------
			foreach( $value as $tmp ){
				if(    (is_string($tmp) && array_key_exists($tmp, $this->rule))								// ルールに存在するか
					|| (is_array($tmp)  && is_string($tmp[0]) && array_key_exists($tmp[0], $this->rule))){	// ルールに存在するか
					continue;
				}
				else{
					return(false);
				}
			}
		}
					
		return(true);
	}

	/**
	 * Dataに追加する配列の検証
	 *
	 * @param  array   $data 検証リスト
	 * @return boolean
	 * @access private
	 */
	private function _validData($data){
		if( !is_hash($data) || $data === array() )
			return(false);

		foreach($data as $key => $value){
			if(!is_string($key)){
				return(false);
			}
		}

		return(true);
	}
}
