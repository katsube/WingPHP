<?php
function smarty_function_debugprint($params, $template){
	//出力モード
	$mode = (array_key_exists('mode', $params))?  $params['mode']:'console.log';

	//サーバ変数
	$json_get     = json_encode($_GET);
	$json_post    = json_encode($_POST);
	$json_server  = json_encode($_SERVER);
	$json_env     = json_encode($_ENV);
	$json_cookie  = json_encode($_COOKIE);
	$json_session = (empty($_SESSION))?  '[]':json_encode($_SESSION);

	//出力用JS
	$js = <<< END_OF_HTML
<script type="text/javascript">
if(!('console'    in window)){window.console={}; window.console.log=function(str){return(str);};}
if(!('WingPlugin' in window)){window.WingPlugin={};}
WingPlugin.DebugPrint={
	'data':{
		  'GET':$json_get
		, 'POST':$json_post
		, 'SERVER':$json_server
		, 'ENV':$json_env
		, 'COOKIE':$json_cookie
		, 'SESSION':$json_session
	}
	, 'putConsole': function(){ for( var i in this.data ) console.log(i, this.data[i]); }
};
switch($mode){
	case 'console.log':
	default:
		WingPlugin.DebugPrint.putConsole();
}
</script>
END_OF_HTML;

	return($js);
}
?>