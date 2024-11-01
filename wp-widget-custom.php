<?php
/*
Plugin Name: wp-widget-custom
Plugin URI: http://wppluginsj.sourceforge.jp/wp-widget-custom/
Description: サイドバーを使ってページ毎にそれぞれのウィジェットを設定できます
Author: zamuu
Version: 1.0.2
Author URI: http://zamuu.net/
*/

/*  Copyright 2010/03/01 zamuu (email : info@zamuu.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
/******************************************************************************
 * HowToUse :
 *****************************************************************************/
// http://zamuu.net/products/widgetcustom/
/******************************************************************************
 * function define.
 *****************************************************************************/
 define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
 define( 'WG_OPT_KEY', '__widget_custom_key__' );
 define( 'OPT_SEPARATOR',',' );
 define( 'OPT_WSEPARATOR',',,' );
 define( 'OPT_PIREOD',':' );
 define( 'OPT_INDEX', 10 );
 define( 'WG_KEY', 'wp-widget-custom' );
 define( 'WG_CUSTOM_VER', '1.0.2' );
 define( 'DEBUG', FALSE ); // For Debug

if (!defined('DIRECTORY_SEPARATOR'))
{
	if (strpos(php_uname('s'), 'Win') !== false )
	define('DIRECTORY_SEPARATOR', '\\');
	else
	define('DIRECTORY_SEPARATOR', '/');
}
define('WG_ABSPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

if(class_exists('WpWgMain')) {
	$wph = & new WpWgMain();
}
/******************************************************************************
 * Load Initialize
 *****************************************************************************/
// ---------------------------
// 言語(.mo)ファイルのロード
// ---------------------------
// $OPT_INDEX = count(wp_get_sidebars_widgets());
load_plugin_textdomain('wp-widget-custom', false, 'wp-widget-custom');
/******************************************************************************
 * WpWgMain プラグイン メインクラス
 * 
 * @author		Iwakura
 * @version	1.0.2
 * 
 *****************************************************************************/
class WpWgMain {
	/*****************************************
	 * コンストラクタ
	 * 
	 * @param none
	 * @return Object reference
	 ****************************************/
	function WpWgMain() {
		include_once ( WG_ABSPATH  . 'widget_admin.php' );

		// デフォルトテーマのサイドバー削除
		add_action( 'init', 'child_theme_setup' );
		if ( !function_exists( 'child_theme_setup' ) ):
		function child_theme_setup () {
			remove_action( 'widgets_init', 'twentyten_widgets_init' );
		}
		endif;

		
		if (is_admin()) { // 管理クラス
			$SEAdmin = new wg_admin();
		} else if (is_page()){ // ページ画面
			// ページ出力時
			add_filter('get_pages','wg_dispctl');
			// add_action('publish_page ', array(&$this, 'wg_dispctl'));
		}
	}
}
	/*****************************************
	 * Page表示時処理
	 * 
	 * @return 削除後のリストデータ
	 ****************************************/
	function wg_dispctl() {
		global $post;
		$resp = FALSE ;
		if(!is_page()){
			return $resp;
		}
		// dynamicサイドバーがサポートされてなければ終了
		if (!function_exists('dynamic_sidebar')){
			return $resp;
		}

		include_once ( WG_ABSPATH  . 'widget_custom_util.php' );
		$wgUtil = new wg_util();
		$wg_names = $wgUtil->getList("name"); // Get WidgetArea List
		$wg_pages = $wgUtil->getList(""); // Get WidgetPageNo List
		
		// サイドバーリスト
		$sidebar_list = wp_get_sidebars_widgets();
		
		// --------------------------------
		if(DEBUG){
			foreach($sidebar_list as $k => $v){
				echo $k . "==". $v."<br />";
				foreach($v as $k2 => $v2){
					echo $k2 . "==". $v2."<br />";
				}
			}
		}

		$num="";
		$cnt = 0;
		foreach($sidebar_list as $widget_name => $ArrWidgetVal){
			// 該当のウィジェット名がある場合 (sidebar-である場合)
			if(!ereg("^sidebar-",$widget_name)){
				continue;
			}
			if(DEBUG) echo "Point0 $widget_name<br />"; 

			// PageIDが該当する場合、Widgetのサイドバー出力
			$work = explode(OPT_SEPARATOR, $wg_pages[$cnt]);

			foreach($work as $key => $val){
				// 表示するPOST IDと設定したPageNo一致
				
				if(DEBUG) echo "Point1 PageNo: $val<br />"; 
				if($post->ID == $val){
					// [sidebar-xx]からNoを取り出す
					preg_match("/[0-9]+/", $widget_name, $num);
					$n = intval($num[0]); // 数値化
					
					// 該当のサイドバー登録
					register_sidebar(array(
						'name' => $wg_pages[$cnt]." [". $widget_name . "]",
						'id'   => $widget_name,
						'before_widget' => '<li>',
						'after_widget'  => "</li>\n",
						'before_title'  => '<h2>',
						'after_title'   =>"</h2>\n",
						));
					dynamic_sidebar($widget_name);
					$resp = TRUE;

					if(DEBUG){
						if(!is_dynamic_sidebar( $widget_name)){
							echo "NOT FOUND $widget_name <br />";
						}else{
							echo "FOUND $widget_name <br />";
						}
						if(is_active_sidebar($widget_name)){
							echo "ACTIVE $widget_name <br />";
						}else{
							echo "NOT ACTIVE $widget_name <br />";
						}
					}
				}
			}
			$cnt++;
		} // end foreach
		return $resp;
	}
?>
