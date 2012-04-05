<?php
class TestFeather extends BaseFeather{

	function index(){
		$this->layout('layout/base_xhtml1str.html');
		$this->assign('PAGE_TITLE', 'foobar');
		
		$this->display('index.html');
	}
}
?>