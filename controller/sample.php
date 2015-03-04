<?php
/* [WingPHP]
 *  - SampleController
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
 * SampleControllerクラス
 *
 * 動作サンプル用クラス
 *
 * @package    SampleController
 * @copyright  2013 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class SampleController extends BaseController{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	function __construct(){
		parent::__construct();
	}

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	function __destruct(){
		;
	}

	public function validation($argv){
		$mode = (empty($argv[0]))? null:$argv[0];

		uselib('Util/Validation');
		$v = new Validation('form');

		//------------------------------------------
		// 検証リストを準備
		//------------------------------------------
		switch ($mode) {
			//---------------
			// 基本
			//---------------
			case 'check':
				$v->addList(array(
					  'require' => array('require')
					, 'bytemax' => array(['bytemax', 4])
					, 'bytemin' => array(['bytemin', 4])
					, 'max'     => array(['max', 500])
					, 'min'     => array(['min', 500])
					, 'match'   => array(['match', '/^He/'])
					, 'eq'      => array(['eq', 'XYLITOL'])
					, 'ne'      => array(['ne', 'XYLITOL'])
					, 'in'      => array(['in', '雪', '月', '花'])
					, 'num'     => array('num')
					, 'alpha'   => array('alpha')
					, 'alnum'   => array('alnum')
					, 'url'     => array('url')
					, 'email'   => array('email')
					, 'ip4'     => array('ip4')
					, 'postcd'  => array('postcd')
					, 'tel'     => array('tel')
				));
				break;

			//---------------
			// フォーム部品
			//---------------
			case 'check2':
				$v->addList(array(
					  'select1'   => array('require')
					, 'check1'    => array('grequire1')
					, 'radio1'    => array('grequire1')
					, 'textarea1' => array(['bytemin',2], ['bytemax', 16])
					, 'passwd1'   => array('alnum')
				));
				break;

			//---------------
			// 応用
			//---------------
			case 'check3':
				$q = new QueryModel();
				$v->addList(array(
					  'strcmp1' => array(['eq', $q->strcmp2])			//strcmp1でひっかける
					, 'check2'  => array(['gin', 1, 2, 3])
					, 'mix1'    => array('require', ['bytemin', 5], 'num')
				));
				break;

			//---------------
			// ？
			//---------------
			default:
				break;
		}

		//------------------------------------------
		// validaiton実行
		//------------------------------------------
		if( !$v->check() )
			$v->setError2Scratch();
		//------------------------------------------
		// 表示
		//------------------------------------------
		$this->layout('layout/base.html');
		$this->assign('TITLE', 'Validation');
		$this->display('sample/validation/index.html');
	}


	public function smartytag(){
		//------------------------------------------
		// 表示
		//------------------------------------------
		$this->layout('layout/base.html');
		$this->assign('TITLE', 'Smarty用カスタムプラグイン');
		$this->display('sample/smartytag/index.html');
	
	}
}
