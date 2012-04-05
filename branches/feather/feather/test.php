<?php
class TestFeather extends BaseFeather{

	function index(){
		$this->layout('layout/base_xhtml1str.html');
		$this->assign('PAGE_TITLE', $this->select1());
		
		$this->display('index.html');
	}
}
?>