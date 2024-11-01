<?php
/******************************************************************************
 * wg_admin 管理画面クラス
 * 
 * @author		Iwakura
 * @version	1.0.2
 * 
 *****************************************************************************/
include_once ( WG_ABSPATH  . 'widget_custom_util.php' );
Class wg_admin extends wg_util {
	// ------------------------------
	// Wordpress DBのOptionキー
	// ------------------------------
	var $wg_key = WG_OPT_KEY;
	var $head_message = "";

	/*********************************************
	 * コンストラクタ
	 ********************************************/
	function wg_admin() {
		// default テーマでWidgetが固定されているので削除する
		//add_action( 'after_setup_theme', 'child_theme_setup' );

		// -----------------------------
		// イベント,フックの登録
		// -----------------------------
		//$locale = get_locale();
		
		// 管理画面style処理 登録
		add_action('admin_head', array(&$this, 'wg_options_style'));
		
		// 管理画面menuのページ表示処理 登録
		add_action('admin_menu', array(&$this, 'wg_add_options_panel'));
		//add_action( 'admin_menu',  array(&$this, 'wg_disp') );

		// -----------------------------
		// ウィジェット用処理 登録
		// -----------------------------

		// ウィジェット画面表示時
		add_action( 'admin_menu',  array(&$this, 'wg_disp') );
	}
	/*********************************************
	 * 管理画面(外観-ウィジェット)表示時
	 * 
	 * @return none
	 ********************************************/
	function wg_disp() {
		$list = $this->getList("name"); // Get WidgetName
		$pagesNo = $this->getList(""); // PagesNo
		if($list == "" || count($list) <= 0){
			return;
		}
		//global $wp_registered_sidebars;
		if ( function_exists('register_sidebar') ) {
			foreach($list as $k => $v){
				$no = $k + OPT_INDEX ; //count(wp_get_sidebars_widgets()) ;
				// 登録
				register_sidebar(array(
						'name' => "$v [sidebar-$no]",
						'id'   => "sidebar-$no",
						'before_widget' => '<li>',
						'after_widget'  => "</li>\n",
						'before_title'  => '<h2>',
						'after_title'   =>"</h2>\n",
						));
			}
		}
	}
	/**********************************************
	 * 管理画面 メニュー追加
	 **********************************************/
	function wg_add_options_panel() {
		add_options_page('Widget', __('Widget Custom','wp-widget-custom'), 8, 'widget_custom', array(&$this, 'wg_option_page'));
	}
	/**********************************************
	 * 管理メニュー[widget custom]のページ内容
	 **********************************************/
	function wg_option_page() {
		// ----------------------------------------
		// POSTによるAction処理
		// ----------------------------------------
		if($_POST['action'] == "add"){
			// ---------------------
			// 追加時
			// ---------------------

			// 入力チェック
			if(empty($_POST['widgetName'])){
				$msg = __('Update ERROR !!!', 'wp-widget-custom');
			}else if(!ereg("[_a-zA-Z0-9]",$_POST['widgetName'])){
				// 記号,スペースチェック
				$msg = __('[Widget Name] Validation error. You can use only a-z,A-Z,0-9.', 'wp-widget-custom');
			}else if(!ereg("^([0-9]*(,[0-9]*)*)$",$_POST['pageNo'])){
				// pageNoリストのチェック(??,??,...)
				$msg = __('[PageNo] Validation error. This is PostID of Page. You can use only number with 0-9.', 'wp-widget-custom');
			}else{
				// 追加登録
				// FORMAT : key1,val1:key2,val2:...
				$this->addOpt($_POST['widgetName'].OPT_SEPARATOR.$_POST['pageNo'].OPT_PIREOD);
				$msg = __('Your default widgets have been updated by WidgetCustom.' , 'wp-widget-custom');
			}
		}else if($_POST['action'] == "del"){
			// ---------------------
			// 削除時
			// ---------------------
			$delval='';
			foreach($_POST as $k => $v){
				if(substr($k,0,3) == "txt"){
					$delval = $_POST[$k].OPT_SEPARATOR;
					$no = str_replace("txt", "", $k); // Get Pages No
					break;
				}
			}
			$this->delList($delval,$no);
			$msg = __('Widget is Deleted. ', 'wp-widget-custom');
		}
		if(!empty($msg)){
			$this->head_message = <<<_HEAD_MSG_
				<div class="updated fade" id="limitcatsupdatenotice">
					<p>{$msg}</p>
				</div>
_HEAD_MSG_;
		}
		// ----------------------------------------
		// 表示処理
		// ----------------------------------------
		$title = __('Widget Custom Version:', 'wp-widget-custom'). WG_CUSTOM_VER; // タイトル
		$col1  = __('Widget ID', 'wp-widget-custom'); // ウィジェットID
		$col2  = __('Widget Name', 'wp-widget-custom'); // ウィジェット名
		$col3  = __('PostId1,PostId2,...'    , 'wp-widget-custom'); // ページNo
		$col4  = __('Action'  , 'wp-widget-custom'); // ページ名
		$alt_col2 = __('Page Name'  , 'wp-widget-custom');
		$alt_col3 = __('Page Name'  , 'wp-widget-custom');
		$BtnAdd = __('Addtional', 'wp-widget-custom');
		$BtnRst = __('Reset Button', 'wp-widget-custom');
		$HowTo = __('How to use Widget Custom.', 'wp-widget-custom');
		$imgHelp = get_bloginfo('url'). "/wp-content/plugins/" . WG_KEY ;
		$wgNameHelp = __('Set Sidebar name of [Appearance]-[widget].', 'wp-widget-custom');
		$wgPostIdHelp = __("Post ID on the page that uses the sidebar is specified. Separate ','", 'wp-widget-custom');
		
		// Help url
		$HoToUrl = "http://zamuu.net/products/widgetcustom/";
		if(strtoupper(get_locale()) != "JA"){
			$HoToUrl = "http://zamuu.net/products/widgetcustom-en/";
		}

		// 登録一覧取得
		$WidgetList = $this->CreateList();
		
		$body =<<<_BODY_
			<div class="wrap">
			<h2>{$title}</h2>
				{$this->head_message}
				<table class="widefat fixed">
					<thead><tr class="title">
						<th scope="col" class="manage-column">{$col1}</th>
						<th scope="col" class="manage-column">{$col2}</th>
						<th scope="col" class="manage-column">{$col3}</th>
						<th scope="col" class="manage-column">{$col4}</th>
					</thead></tr>
					{$WidgetList}
				<form method="post" >
					<tr class="mainrow"> 
						<td class="titledesc">{$col2}</td>
						<td class="forminp">
							<input type="text" name="widgetName" value="" maxlength="20" />
							<img src="{$imgHelp}/help.png" width="15px" heigh="15px" title="{$wgNameHelp}" />
						</td>
						<td class="forminp">
							<input type="text" name="pageNo" value="" maxlength="50" />
							<img src="{$imgHelp}/help.png" width="15px" heigh="15px" title="{$wgPostIdHelp}" />
						</td>
						<td class="forminp">
							<input type="hidden" name="action" value="add" />
							<input type="submit" value="{$BtnAdd}" />
						</td>
					</tr>
				</form>
				</table>

				<div class="info">
					Developed by <a href="http://zamuu.net" title="zamuu" target="_blank">zamuu</a>.
					<a href="{$HoToUrl}" title="help" target="_blank">{$HowTo}</a>
				</div>
			</div>
_BODY_;
		echo $body;
	}

	/*********************************************
	 * 管理画面ヘッダー スタイル設定
	 ********************************************/
	function wg_options_style() {
		echo <<<_STYLE_
			<style type="text/css" media="screen">
				.titledesc {width:50px;}
				.thanks {width:200px; }
				.thanks p {padding-left:20px; padding-right:20px;}
				.info { background: #FFFFCC; border: 1px dotted #D8D2A9; padding: 10px; color: #333; }
				.info a { color: #333; text-decoration: none; border-bottom: 1px dotted #333 }
				.info a:hover { color: #666; border-bottom: 1px dotted #666; }
			</style>
_STYLE_;
	}
	/*********************************************
	 * 管理画面 一覧生成
	 ********************************************/
	 function CreateList(){
		$l_bc=""; // Sidebar-xx
		$l_wg_name=$this->getList("name"); // Widget Name
		$l_pages = $this->getList(""); // Pages List
		$data_list="";
		if(!empty($l_wg_name)){
			// WidgetName数分Loop
			for($i = 0 ; $i < count($l_wg_name) ; $i++){
				$l_bc="Sidebar-" . ($i + OPT_INDEX) ;
				$act = __('Delete', 'wp-widget-custom');
		$data_list .=<<<_DATA_LIST_
			<form method="post" >
				<tr class="mainrow"> 
					<td class="titledesc">$l_bc</td>
					<td class="forminp">
						<input type="text" name="txt$i" value="$l_wg_name[$i]" readonly /></td>
					<td class="forminp">
						<input type="text" name="page$i" value="$l_pages[$i]" readonly /></td>
					<td class="forminp">
						<input type="hidden" name="action" value="del" />
						<input type="submit" value="$act" /></td>
				</tr>
			</form>
_DATA_LIST_;
			} // end for
		}
		return $data_list;
	 }
	/*********************************************
	 * WP3.1対応 default theme 削除用
	 ********************************************/
	function child_theme_setup () {
		remove_action( 'widgets_init', 'twentyten_widgets_init' );
	}
}

?>
