{literal}
$(document).ready(function(){
	//------------------------------------------------
	// タブをクリックでアンカー移動
	//------------------------------------------------
	$('#myTab a').click(function (e) {
		location.href = $(this).attr('href');
	});

	//----------------------------------------------------------
	// REQUEST URLにアンカーがある場合、デフォルトで開くタブを調整
	//----------------------------------------------------------
	(function(){
		var hash = location.hash;
		switch(hash){
			case '#basictab': 			//ページ中に存在しないアンカー名 (スクロールしないように)
				$('#li-basic').tab('show');
				break;
			case '#formtab':
				$('#li-form').tab('show');
				break;
			case '#extab':
				$('#li-ex').tab('show');
				break;
			default:
				break;
		}
	})();
});
{/literal}
