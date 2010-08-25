-----------------------------------------------------------------------------
layoutディレクトリ
-----------------------------------------------------------------------------
○これは何？
  ・layout機能を利用する際に、layoutファイルをこのディレクトリに
    保存してください。

	
○layout機能の利用方法
  コントローラに以下のように記述します。
  
	  function foobar(){
		$this->layout('layout/layout.html');   //displayより前ならどこでもOKです
		$this->assign('hoge', 'world');

		$this->display('foobar/index.html');
	  }
 
  ビューは以下
	  <!-- layout.html -->
	  <html>
	  <head><title>テスト</title></head>
	  <body>
		{$CONTENT}
	  </body>
	  
	  <!-- index.html -->
	  <h1>Hello!</h1>
	  {$hoge}
  
  出力結果は以下
  	  <html>
	  <head><title>テスト</title></head>
	  <body>
	  <h1>Hello!</h1>
	  world
	  </body>
