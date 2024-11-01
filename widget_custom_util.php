<?php
/******************************************************************************
 * wg_util Utilityクラス
 * 
 * @author		Iwakura
 * @version	1.0.2
 * 
 *****************************************************************************/
Class wg_util {
	// ------------------------------
	// Wordpress DBのOptionキー
	// ------------------------------
	var $wg_key = WG_OPT_KEY;

	/*********************************************
	 * コンストラクタ
	 ********************************************/
	//function wg_util() {}
	/*********************************************
	 * Option登録・更新・削除
	 * 
	 * @param newvalue
	 * @return none
	 ********************************************/
	function addOpt($newvalue ) {
		// あれば更新
		$val= get_option($this->wg_key);
		if ($val != "") {
			$val .= $newvalue;
			update_option($this->wg_key, $val);
		} else {
			// なければ追加&更新
			$deprecated='__widget_custom__';
			$autoload='no';
			update_option($this->wg_key, $newvalue);
			//add_option($this->wg_key, $newvalue, $deprecated, $autoload);
		}
	}
	function delOpt($delvalue) {
		// あれば削除
		if ( get_option($this->wg_key)  != "") {
			delete_option($this->wg_key);
		}
	}
	/*********************************************
	 * Optionデータリスト削除
	 * 
	 * @param delvalue:削除対象文字列(,付)
	 * @param no:削除対象配列No
	 * @return 削除後のリストデータ
	 ********************************************/
	function delList($delvalue,$no) {
		// あれば削除
		$str = get_option($this->wg_key);
		$ret="";
		if($str != null && $str != ""){
			$list = explode(OPT_PIREOD,$str);
			for($k = 0 ; $k < count($list) ; $k++){
				if($no == $k){
				}else{
					$ret .= $list[$k].OPT_PIREOD ;
				}
				$ret = str_replace(OPT_WSEPARATOR, OPT_SEPARATOR, $ret); // ",,"が残ってれば","へ置換
				$ret = str_replace(OPT_PIREOD.OPT_PIREOD, OPT_PIREOD, $ret); // "::"が残ってれば":"へ置換
			}
			update_option($this->wg_key, $ret);
		}
		if($ret == "" || $ret==OPT_SEPARATOR || $ret==OPT_PIREOD){
			$str="";
			delete_option($this->wg_key);
		}
	}
	/*********************************************
	 * Optionデータリスト取得
	 * 
	 * @param none
	 * @param type : "name"(WidgetName), Other(PageNo List)
	 * @return リストデータ
	 ********************************************/
	function getList($type) {
		// データ取得
		$list = "";
		$ret = "";
		$str = get_option($this->wg_key);
		if($str != null && $str != ""){
			$list = explode(OPT_PIREOD,$str);
			for($k = 0 ; $k < count($list)-1 ; $k++){
				$tmp = explode(OPT_SEPARATOR,$list[$k]);
				if($type=="name"){ // Get WidgetName
					$ret[$k] = $tmp[0];
				}else{ // Get pages List
					array_shift($tmp);
					$ret[$k] = implode(OPT_SEPARATOR,$tmp);
				}
			}
		}
		return $ret;
	}
	/*********************************************
	 * UTF-8 encoder.
	 * @param $text
	 * @return $text (UTF-8 に変換した文字列)
	 ********************************************/
	function utf8_encode($text) {
		$blog_charset = get_settings('blog_charset');
		if(!preg_match ("/UTF-8/i", $blog_charse)) {
			if(function_exists('mb_convert_encoding')) {
				$text = 
					mb_convert_encoding(
						$text,
						'UTF-8',
						$blog_charse
					);
			}
		}
		
		return $text;
		
	}
}

?>
