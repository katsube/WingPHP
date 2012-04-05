<?php
class BaseFeather{
	//インスタンス格納用
	private $model = null;
	private $ctrl  = null;

	//メソッド一覧
	private $model_list;
	private $ctrl_list;


	function __call($name, $param){
		//初期化
		if( $this->model === null || $this->ctrl === null )
			$this->_init();
	
		//メソッド呼出し
		if( in_array($name, $this->ctrl_list) ){
			return(
				call_user_func_array(array($this->ctrl, $name), $param)
			);
		}
		else if( in_array($name, $this->model_list) ){
			return(
				call_user_func_array(array($this->model, $name), $param)
			);
		}
		else{
			die();
		}
	}

	private function _init(){
		$this->model = new BaseModel();
		$this->ctrl  = new BaseController();
	
		$this->model_list = get_class_methods('BaseModel');
		$this->ctrl_list  = get_class_methods('BaseController');
	}
}
?>