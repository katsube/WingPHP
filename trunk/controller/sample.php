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
		$mode = $argv[0];

		switch ($mode) {
			case 'check':
				uselib('Util/Validation');
				$v = new Validation('form');
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
				if( !$v->check() )
					$v->setError2Scratch();
				break;
			case 'check2':
				uselib('Util/Validation');
				$v = new Validation('form');
				$v->addList(array(
				));
				if( !$v->check() )
					$v->setError2Scratch();
			default:
				break;
		}
	
		$this->layout('layout/base.html');
		$this->assign('TITLE', 'Validation');
		$this->display('sample/validation/index.html');
	}
}
