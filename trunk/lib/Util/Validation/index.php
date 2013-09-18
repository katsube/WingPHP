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
 *   const MODE     = 'form';
 *   const FORMNAME = 'foo';
 * 
 *   public function form(){
 *     $this->display('foo/form.html');
 *   }
 * 
 *   public function check(){
 *     uselib('Util/Validation');
 * 
 *     // インスタンス生成
 *     $v = new Validation(self::MODE, self::FORMNAME);
 * 
 *     // validationルールを設定
 *     $v->setRule(array(
 *              'bar'    => array('notnull', 'num')           // 必須, 数値
 *            , 'postcd' => array('post')                     // 郵便番号
 *            , 'userid' => array(function($userid){          // 自分で定義した関数(必ずbooleanを返す)
 *                                    if(...)
 *                                        return(true);   //trueで問題なし
 *                                    else
 *                                        return(false);
 *                                })
 *            , 'name' => array(['minlen',3], ['maxlen',10], 'alnum')   // 値指定
 *     ));
 *     $v->addRule(array('hoge'=>array('notnull')));           // ルール追加
 * 
 *     // チェックするデータを定義（省略可）
 *     // $q = new QueryModel();
 *     // $v->setData(json_decode($q->data('foo')));          //setData未実行の場合はクエリーが自動的に採用される
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

uselib('Util/Regex');
class Validation{
	private $mode     = null;
	private $formname = null;

	private $rule   = array();
	private $target = array();
	private $error  = array();

	private $functions = array(
					  'url'   => function($val){}
					, 'email' => function($val){}
					, 'ip4'   => function($val){}
					, 'post'  => function($val){}
					, 'post7' => function($val){}
					, 'tel'   => function($val){}
					, 'num'   => function($val){}
					, 'alpha' => function($val){}
					, 'alnum' => function($val){}
					
					, 'require' => function($val){}
					, 'maxlen'  => function($val, $opt){}
					, 'minlen'  => function($val, $opt){}
				); 


	function __construct($mode, $formname){
		$this->mode     = $mode;
		$this->formname = $formname;
	}


	/**
	 * 検証ルールを設定する
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
	 * 
	 * @param  array $rule ルール格納用
	 * @return void
	 * @access public
	 */
	public function setRule($rule){
		if( is_array($rule) )
			$this->rule = $rule;
	}

	/**
	 * 検証ルールを追加する
	 * 
	 * setRuleと同様の検証ルールを既存のルール一覧に追加する。
	 * 同名のルールが存在する場合は上書きされる。
	 * 
	 * @param  array  $rule  ルール格納用
	 * @return void
	 * @access public
	 */
	public function addRule($rule){
		if( is_array($rule) )
			$this->rule = array_merge($this->rule, $rule);
	}

	/**
	 * 検証用データを格納する
	 * 
	 * 次のように第1引数へ検証用のデータを格納する。
	 * array('name1'=>'value', 'name2'=>value ... 'namen'=>value);
	 * 
	 * @param  array $data 検証用データ格納用
	 * @return void
	 * @access public
	 */
	public function setData($target){
		if( is_array($target) )
			$this->target = $target;
	}

	/**
	 * 検証用データを追加する
	 * 
	 * setDataと同様の検証データを既存のデータ一覧に追加する。
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
		$rule       = $this->rule;
		$target     = $this->target;
		$functions  = $this->functions;
		$flag_error = false;

		//------------------------------
		// ○データ項目数分回す
		//------------------------------
		for($rule as $name => $array){
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

					if( array_key_exists($func, $functions) ){
						$ret = $functions[$func]($data, $opt);
					}
					else{
						$ret = false;
					}
				}
				//------------------------------
				// ◇独自関数
				//------------------------------
				else if( is_object($cur) && ($cur instanceof Closure) ){
					$func = '_closure';
					$ret  = $cur($data);
				}
				//------------------------------
				// ◇既存定義
				//------------------------------
				else if( array_key_exists($cur, $functions) ){
					$func = $cur;
					$ret  = $functions[$cur]($data);
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
					$this->addError($name, $func);
				}
			}
		}

		return($flag_error);
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
		$form  = $this->formname;
		$error = $this->error;
	
		$Scratch[$mode][$form]['error'] = $error; 
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
	}
}

