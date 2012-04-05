<?php
/* [WingPHP]
 *  - lib/Google/Translation.php
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

uselib('Util/Net'); 	//net_fetchUrl, net_getContext

/**
 * GoogleTranslationクラス
 * 
 * Google AJAX Language APIのラッパークラス。
 * メソッドチェーン式に利用できます。ステートメントを分けて
 * 記述することももちろん可能。
 *
 * example.<code>
 *     uselib('Google/Translation');
 *
 *     $referer = 'http://wingphp.net/';  					//参照元
 *     $apikey  = 'youre google api key (optional)';		//APIキー
 *
 *     //インスタンス作成
 *     $gtr = GoogleTranslation($referer, $apikey);
 *
 *     //翻訳
 *     $gtr->set('こんにちは', 'JAPANESE')->translate('ENGLISH');
 *
 *     //言語判定
 *     $gtr->set('こんばんわ')->detect();
 * </code>
 *
 * @package    GoogleTranslation
 * @copyright  2010 WingPHP
 * @author     M.Katsube < katsubemakito@gmail.com >
 * @license    The MIT License
 * @access     public
 */
class GoogleTranslation{
	//--------------------------------------------
	// メンバ変数
	//--------------------------------------------
	private $base_url  = 'http://ajax.googleapis.com/ajax/services/language/';
	private $ver       = '1.0';
	private $use_curl  = true;		//cURL利用フラグ
	private $str       = null;
	private $lang_from = null;		//$strの言語
	private $langs;
	private $referer;
	private $apikey;


	/**
	 * コンストラクタ
	 *
	 * @param  string  $referer  参照元
	 * @param  string  $key      Google API Key
	 * @access public
	 * @link   http://code.google.com/intl/ja/apis/ajaxsearch/signup.html
	 * @todo   $refererが未指定の場合、自動で現在URLをセットする
	 */
	function __construct($referer, $key=null){
		$this->referer = $referer;
		$this->apikey  = $key;
		
		//言語一覧
		$this->langs = $this->_setLang();
	}
	
	/**
	 * メソッドのオーバーライド
	 */
	 function __call($name, $param){
		if(preg_match('/^(translate|detect)$/', $name) > 0){
			$arg1 = empty($param[0])?  null    : $param[0];
			$opt  = empty($param[1])?  array() : $param[1];
			
			return( $this->request($name, $arg1, $opt) );
		}
		else{
			die();
		}
	}


	//--------------------------------------------
	// Public
	//--------------------------------------------
	/**
	 * 対象文字列をセットする
	 *
	 * @param  string  $str  対象文字列
	 * @param  string  $lang 言語
	 * @access public
	 */
	function set($str, $lang=null){
		$this->str       = $str;
		$this->lang_from = $lang;
		
		return($this);
	}

	/**
	 * APIへリクエストし結果返却
	 *
	 * @param  string  $type  サーチャーの種類を指定。translate|detect
	 * @param  string  $arg1  第一引数
	 * @param  array   $opt   オプション
	 * @return array   
	 * @link   http://code.google.com/intl/ja/apis/ajaxlanguage/documentation/reference.html
	 * @access public
	 */
	function request($type, $arg1, $opt=array()){
		//エラーチェック
		if($this->str === null) return(false);
		if($type === 'translate' && empty($arg1)) return(false);
	
		//URL作成
		$url = $this->_makeUrl($type, $arg1, $opt);

		//取得
		if( $this->use_curl ){
			$ret = net_fetchUrl($url, array(CURLOPT_REFERER => $this->referer) );
		}
		else{
			$ret = @file_get_contents($url, false
								, net_getContext(array(
										  'method' => 'GET'
										, 'header' => sprintf('Referer: %s', $this->referer)
									)
								)
					);
		}
		
		//エラーチェック
		if($ret === false )
			return( false );
		
		//結果返却
		$json = json_decode($ret, true);
		if( is_array($json) && array_key_exists('responseStatus', $json) && $json['responseStatus'] === 200 )
			return($json);
		else
			return(false);
	}

	/**
	 * cURL利用有無変更
	 *
	 * @param  bool   $flag  trueなら利用、falseならfile_get_contents
	 * @access public
	 */	
	function curl_use($flag){
		$this->use_curl = $flag;
	}


	//--------------------------------------------
	// Private
	//--------------------------------------------	
	/**
	 * リクエスト用URL作成
	 *
	 * @param  string  $type  サーチャーの種類を指定。web|local|video|blogs|news|books|images|patent
	 * @param  string  $q     検索文字列
	 * @param  array   $opt   オプション
	 * @return string  リクエスト用URL
	 * @access private
	 */
	private function _makeUrl($type, $arg1, $opt=array()){
		//APIキーが指定されていればセット
		if( !is_null($this->apikey) && !array_key_exists('key', $opt) )		//$optに'key'がある場合はスルー
			$opt['key'] = $this->apikey;
	
		//オプション値からクエリー作成
		$query = '';
		if(count($opt) > 0)
			foreach( $opt as $key=>$val )
				$query .= sprintf('&%s=%s', $key, urlencode($val) );
		
		//サーチャー別処理
		if( $type === 'translate' ){
			if(!array_key_exists('langpair', $opt)){
				$from = $this->lang_from;
				$query .= sprintf('&langpair=%s%s%s'
									, ($from===null)?  '':$this->_label2cd($from)
									, urlencode('|')
									, $this->_label2cd($arg1)
				);
			}
		}
		else if( $type === 'detect' ){
			;
		}
	
		//URL生成してから返却
		return(
			sprintf('%s%s?v=%s&q=%s%s'
						, $this->base_url
						, $type
						, urlencode($this->ver)
						, urlencode($this->str)
						, $query
			)
		);
	}

	/**
	 * 言語ラベルをコードに変換する
	 *
	 * @param  string  $label ラベル
	 * @return string  コード
	 * @access private
	 */
	private function _label2cd($label){
		$langs = $this->langs;
	
		if( array_key_exists($label, $langs) )
			return($langs[$label]);
		else
			return('UNKNOWN');
	}
	
	/**
	 * 言語コードをラベルに変換する
	 *
	 * @param  string  $cd コード
	 * @return string  ラベル
	 * @access private
	 */
	private function _cd2label($cd){
		$langs = $this->langs;
	
		$ret = array_search($label, $langs);
		if( $ret !== false )
			return($ret);
		else
			return('');
	}
	
	/**
	 * 対応言語リストを返却する
	 *
	 * @return array 言語一覧
	 * @todo   Googleが更新されたらこのリストも更新する。
	 * @access private
	 */
	private function _setLang(){
		return(array(
			   'AFRIKAANS'   => 'af'
			 , 'ALBANIAN'    => 'sq'
			 , 'AMHARIC'     => 'am'
			 , 'ARABIC'      => 'ar'
			 , 'ARMENIAN'    => 'hy'
			 , 'AZERBAIJANI' => 'az'
			 , 'BASQUE'      => 'eu'
			 , 'BELARUSIAN'  => 'be'
			 , 'BENGALI'     => 'bn'
			 , 'BIHARI'      => 'bh'
			 , 'BULGARIAN'   => 'bg'
			 , 'BURMESE'     => 'my'
			 , 'CATALAN'     => 'ca'
			 , 'CHEROKEE'    => 'chr'
			 , 'CHINESE'     => 'zh'
			 , 'CHINESE_SIMPLIFIED'  => 'zh-CN'
			 , 'CHINESE_TRADITIONAL' => 'zh-TW'
			 , 'CROATIAN'    => 'hr'
			 , 'CZECH'       => 'cs'
			 , 'DANISH'      => 'da'
			 , 'DHIVEHI'     => 'dv'
			 , 'DUTCH'       => 'nl'
			 , 'ENGLISH'     => 'en'
			 , 'ESPERANTO'   => 'eo'
			 , 'ESTONIAN'    => 'et'
			 , 'FILIPINO'    => 'tl'
			 , 'FINNISH'     => 'fi'
			 , 'FRENCH'      => 'fr'
			 , 'GALICIAN'    => 'gl'
			 , 'GEORGIAN'    => 'ka'
			 , 'GERMAN'      => 'de'
			 , 'GREEK'       => 'el'
			 , 'GUARANI'     => 'gn'
			 , 'GUJARATI'    => 'gu'
			 , 'HEBREW'      => 'iw'
			 , 'HINDI'       => 'hi'
			 , 'HUNGARIAN'   => 'hu'
			 , 'ICELANDIC'   => 'is'
			 , 'INDONESIAN'  => 'id'
			 , 'INUKTITUT'   => 'iu'
			 , 'ITALIAN'     => 'it'
			 , 'JAPANESE'    => 'ja'
			 , 'KANNADA'     => 'kn'
			 , 'KAZAKH'      => 'kk'
			 , 'KHMER'       => 'km'
			 , 'KOREAN'      => 'ko'
			 , 'KURDISH'     => 'ku'
			 , 'KYRGYZ'      => 'ky'
			 , 'LAOTHIAN'    => 'lo'
			 , 'LATVIAN'     => 'lv'
			 , 'LITHUANIAN'  => 'lt'
			 , 'MACEDONIAN'  => 'mk'
			 , 'MALAY'       => 'ms'
			 , 'MALAYALAM'   => 'ml'
			 , 'MALTESE'     => 'mt'
			 , 'MARATHI'     => 'mr'
			 , 'MONGOLIAN'   => 'mn'
			 , 'NEPALI'      => 'ne'
			 , 'NORWEGIAN'   => 'no'
			 , 'ORIYA'       => 'or'
			 , 'PASHTO'      => 'ps'
			 , 'PERSIAN'     => 'fa'
			 , 'POLISH'      => 'pl'
			 , 'PORTUGUESE'  => 'pt-PT'
			 , 'PUNJABI'     => 'pa'
			 , 'ROMANIAN'    => 'ro'
			 , 'RUSSIAN'     => 'ru'
			 , 'SANSKRIT'    => 'sa'
			 , 'SERBIAN'     => 'sr'
			 , 'SINDHI'      => 'sd'
			 , 'SINHALESE'   => 'si'
			 , 'SLOVAK'      => 'sk'
			 , 'SLOVENIAN'   => 'sl'
			 , 'SPANISH'     => 'es'
			 , 'SWAHILI'     => 'sw'
			 , 'SWEDISH'     => 'sv'
			 , 'TAJIK'       => 'tg'
			 , 'TAMIL'       => 'ta'
			 , 'TAGALOG'     => 'tl'
			 , 'TELUGU'      => 'te'
			 , 'THAI'        => 'th'
			 , 'TIBETAN'     => 'bo'
			 , 'TURKISH'     => 'tr'
			 , 'UKRAINIAN'   => 'uk'
			 , 'URDU'        => 'ur'
			 , 'UZBEK'       => 'uz'
			 , 'UIGHUR'      => 'ug'
			 , 'VIETNAMESE'  => 'vi'
			 , 'UNKNOWN'     => ''
		));
	}
}
?>