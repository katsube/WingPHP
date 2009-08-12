<?php
/* [WingPHP]
 *  - framewing class
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

class framewing{
	//--------------------------------------------
	// �����o�ϐ�
	//--------------------------------------------
	private $ctrl_name   = '';
	private $method_name = '';
	private $param = array();
	
	//--------------------------------------------
	// �R���X�g���N�^
	//--------------------------------------------
	function __construct(){
		//�p�[�X
		$this->_parse();
		
		//�Z�b�V�����J�n
		global $Conf;
		session_name($Conf['Session']['name']);
		session_start();
	}

	//--------------------------------------------
	// �f�X�g���N�^
	//--------------------------------------------
	function __destruct(){
		//�Z�b�V��������
		//session_write_close();
	}


	/*--------------------------------------------
	 * �� Public ��
	 *--------------------------------------------
	 * - go
	 *--------------------------------------------*/
	//--------------------------------------------
	// ���s
	//--------------------------------------------
	public function go(){
		$ctrl = $this->ctrl_name;
		$mthd = $this->method_name;
		
		if( is_callable(array($ctrl, $mthd))								//���݃`�F�b�N
						&& (strcmp('BaseController', $ctrl) != 0)			//�X�[�p�[�N���X�͒��ڎ��s���Ȃ�
						&& !preg_match('/^_/', $mthd)){						//�擪�� _ �Ŏn�܂郁�\�b�h�͎��s���Ȃ�
			
			$obj = new $ctrl();
			call_user_func(array($obj, $mthd), $this->param);
		}
		else{
			header('Location: /error/msg/404');
		}
	}


	/*--------------------------------------------
	 * �� Private ��
	 *--------------------------------------------
	 * - _parse
	 *--------------------------------------------*/
	//--------------------------------------------
	// �A�N�V�����Ȃǉ�͂��Z�b�g
	//--------------------------------------------
	private function _parse(){
		//-----------------------
		// �p�[�X
		//-----------------------
		if( array_key_exists('_q', $_REQUEST) ){
			$query = $_REQUEST['_q'];
			$arr   = split('/', $query);
		}
		else{
			$arr = array();
		}

		//-----------------------
		// ���s���e�m��
		//-----------------------
		//�R���g���[���[
		if(!empty($arr[0]))
			$this->ctrl_name = ucfirst($arr[0]) . 'Controller';
		else
			$this->ctrl_name = 'IndexController';

		//���\�b�h
		if(!empty($arr[1]))
			$this->method_name = $arr[1];
		else
			$this->method_name = 'index';
		
		//�p�����[�^�[
		if(count($arr) > 1)
			$this->param = array_slice($arr, 2);
	}
}
?>