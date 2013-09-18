<?php
/* [WingPHP]
 *  - lib/Util/Validation.php
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
 *       $v->setError();                   //$Scratch['form']['foo'] 以下にエラー情報を格納し、
 *                                         //  $this->assign('__wgSYSFORMNAME', 'foo');
 *                                         //を実行する。
 *                                         //インスタンス生成時にmode, form nameを未指定の場合は、$Scratch['_']['_'] に格納される
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
	function __construct(){
	}

	public function setRule(){
	}

	public function addRule(){
	}

	public function setData(){
	}

	public function addData(){
	}

	public function check(){
	}

	public function setError(){
	}
}

