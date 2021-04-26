<?php
/**
 * Plugin Name: WP Triggers
 * Plugin URI: http://www.webdesignersacademy.com/wp-triggers/
 * Description: Creates unique interactive experiences using a simple box.  Use this to create zip code searches, scavenger hunts, collect email addresses, give away free digital products and more.
 * Version: 5.2
 * Author: Web Designers Academy
 * Author URI: http://www.webdesignersacademy.com
 * Text Domain: wp-triggers
 * Domain Path: /languages/
 *
 */

if (! Class_Exists ( 'wpTrigger' )) {
	global $wpdb;

	define( 'WPTGG_VERSION' , '1.6' );
	define( 'WPTGG_PLUGIN_PATH', str_replace("\\", "/", plugin_dir_path(__FILE__) ) ); //use for include files to other files
	define( 'WPTGG_PLUGIN_URL' , plugins_url( '/', __FILE__ ) );
	define( 'WPTGG_TABLE' , $wpdb->prefix . "trigger" );
	define( 'WPTGG_HISTORY_TABLE' , $wpdb->prefix . "trigger_history" );
	define( 'WPTGG_UNINSTALL_DELETE_DATA' , false );

	class wpTrigger	{
		function  __construct()	{
			// run on activation of plugin
			register_activation_hook( __FILE__, array('wpTrigger', 'sc_run_on_activation') );
			// run on deactivation of plugin
			register_deactivation_hook( __FILE__, array('wpTrigger', 'sc_run_on_deactivation') );

			add_action( 'init', array('wpTrigger', 'page_load_control') );
			add_action( 'admin_init', array('wpTrigger', 'register_settings') );
			add_action( 'admin_menu',  array('wpTrigger', 'create_admin_menu') );
			add_action( 'admin_menu',  array('wpTrigger', 'create_admin_sub_menu_guide') );
			add_action( 'admin_print_styles', array('wpTrigger', 'admin_add_css_file') );
			add_action( 'admin_print_scripts', array('wpTrigger', 'admin_add_js_file') );
			add_action( 'plugins_loaded', array('wpTrigger', 'load_textdomain') );

			// --- front end ----
			add_action( 'wp_footer', array('wpTrigger', 'front_include_css_js') );
			add_shortcode( 'wptrigger', array('wpTrigger', 'get_trigger_process') );

			// ----- Gutenberg -----
			include 'includes/gutenberg/wp-trigger.php';
		}

		function load_textdomain() {
			load_plugin_textdomain( 'wp-triggers', false, 'wp-triggers/languages' );
		}

		static function sc_run_on_activation() {
			$pluginOptions = get_option('wptgg_info');

			if( false === $pluginOptions || floatval($pluginOptions) < floatval(WPTGG_VERSION) ) {
				global $wpdb;

				if ( !empty($wpdb->charset) ) {
					$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
				}

				if ( !empty($wpdb->collate) ) {
					$charset_collate .= " COLLATE $wpdb->collate";
				}

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

				$sql_arr = array(
						"CREATE TABLE `" . WPTGG_TABLE . "` (
							`ID` int(11) NOT NULL AUTO_INCREMENT,\n
							`box_name` varchar(50) NOT NULL,\n
							`box_info` longtext NOT NULL,\n
							`no_found` longtext NOT NULL,\n
							`placeholder` varchar(100) NOT NULL,\n
							`show_chk` varchar(10) NOT NULL,\n
							`only_number_chk` varchar(10) NOT NULL,\n
							`scroll_to_chk` varchar(10) NOT NULL,\n
							`shake_chk` varchar(10) NOT NULL,\n
							`btnactivate` varchar(10) NOT NULL,\n
							`placement` varchar(50) NOT NULL,\n
							`apply_custom_styles` varchar(10) NOT NULL,\n
							`box_custom_style` longtext NOT NULL,\n
							`option` longtext NOT NULL,\n
							`btn_value` varchar(30) NOT NULL,\n
							`apply_button_custom_style` varchar(30) NOT NULL,\n
							`create_datetime` datetime NOT NULL,\n

							PRIMARY KEY  (`ID`)
						) $charset_collate",

						"CREATE TABLE `" . WPTGG_HISTORY_TABLE . "` (
							`ID` int(11) NOT NULL AUTO_INCREMENT,\n
							`trigger` varchar(50) NOT NULL,\n
							`valid_status` varchar(10) NOT NULL,\n
							`optin` varchar(200) NULL,\n
							`trigger_box_id` int(11) NOT NULL,\n
							`create_datetime` datetime NOT NULL,\n

							PRIMARY KEY  (`ID`)
						) $charset_collate"
					);

				foreach($sql_arr as $sql) {
					dbDelta($sql);
				}

				update_option("wptgg_info", WPTGG_VERSION);
			}
		}

		static function sc_run_on_deactivation(){}

		static function page_load_control() {
			include( WPTGG_PLUGIN_PATH . "includes/function.php" );

			wpTrigger::set_load_page();
		}

		function register_settings() {
			register_setting( 'wtsgwda-settings-group', 'wtsgwda_last_request_timestamp' );
			register_setting( 'wtsgwda-settings-group', 'wtsgwda_request_count' );
			register_setting( 'wtsgwda-settings-group', 'wptgg_lcn_mark' );
		}

		static function add_css_file($stylesheet_arr) {
			if( !is_array($stylesheet_arr) ) { return; }

			foreach($stylesheet_arr as $stylesheet) {
				$myStyleDir = WPTGG_PLUGIN_PATH. 'css/'.$stylesheet.'.css';
				$myStyleUrl = WPTGG_PLUGIN_URL. 'css/'.$stylesheet.'.css';

				wp_register_style('wptgg_'.$stylesheet, $myStyleUrl);
				wp_enqueue_style( 'wptgg_'.$stylesheet);
			}
		}

		static function add_js_file($jsfile_arr, $frm = null) {
			if( !is_array($jsfile_arr) ) { return; }

			if( $frm == "jquery" ) { wp_enqueue_script( 'jquery' ); }

			foreach($jsfile_arr as $jsfile) {
				$myJsUrl = WPTGG_PLUGIN_URL. 'js/'.$jsfile.'.js';
				wp_register_script('wptgg_'.$jsfile, $myJsUrl);
				wp_enqueue_script( 'wptgg_'.$jsfile);
			}
		}

		//-----admin panel---

		static function set_load_page() {
			global $wptgg_page_action;

			if(!empty($_GET["page"])) {
				if( $_GET["page"] === "wp-trigger" ) { $wptgg_page_action = "trigger_list"; }

				if( $_GET["page"] === "trigger-import" ) { $wptgg_page_action = "trigger_import"; }

				if( $_GET["page"] === "add-trigger" || ( $_GET["page"] === "wp-trigger" && !empty($_GET["trigger_id"]) )) { $wptgg_page_action = "trigger_add"; }

				if( $_GET["page"] === "trigger-history" ) { $wptgg_page_action = "trigger_history"; }
			}
		}

		static function create_admin_menu() {
			add_menu_page('WP Triggers', 'WP Triggers', 'administrator', 'wp-trigger', array('wpTrigger','trigger_list_page'), WPTGG_PLUGIN_URL . "img/wp-triggers-icon.png");
			add_submenu_page('wp-trigger', 'All Triggers','All Triggers', 'administrator', 'wp-trigger',array('wpTrigger','trigger_list_page'));
			add_submenu_page('wp-trigger', 'New Trigger','New Trigger', 'administrator', 'add-trigger',array('wpTrigger','add_trigger_page'));
			add_submenu_page('wp-trigger', 'Import', 'Import', 'administrator', 'trigger-import',array('wpTrigger','trigger_import_page'));
			add_submenu_page('wp-trigger', 'Trigger History','Trigger History', 'administrator', 'trigger-history',array('wpTrigger','trigger_history_page'));
		}

		static function create_admin_sub_menu_guide() {
			global $submenu;

			$submenu['wp-trigger'][500] = array( '<span id="admin_sub_menu_guide">Guide</span>', 'manage_options' , 'http://webdesignersacademy.com/wptriggers/guide' );
		}

		static function admin_add_css_file() {
			global $wptgg_page_action;

			if( $wptgg_page_action === "trigger_list" ) { $cssfiles = array("main", "pages/admin/trigger_list"); }

			if( $wptgg_page_action === "trigger_add" ) { $cssfiles = array("lib/spectrum", "lib/switchery.min", "main", "pages/admin/add_trigger"); }

			if( $wptgg_page_action === "trigger_import" ) { $cssfiles = array("main", "pages/admin/add_trigger", "pages/admin/trigger_import"); }

			if( $wptgg_page_action === "trigger_history" ) {
				wp_enqueue_style("wp-jquery-ui-dialog");
				$cssfiles = array("main", "pages/admin/trigger_history");
			}

			if(isset($cssfiles)) {
				wpTrigger::add_css_file($cssfiles);
			}
		}

		static function admin_add_js_file() {
			global $wptgg_page_action;

			if( $wptgg_page_action === "trigger_list" ) {
				$jsfiles = array("main", "pages/admin/trigger_list");
			}

			if( $wptgg_page_action === "trigger_add" ) {
				wp_enqueue_editor();

				$jsfiles = array("lib/spectrum", "lib/jquery.json-2.3", "lib/switchery.min", "main", "pages/admin/add_trigger");
			}

			if( $wptgg_page_action === "trigger_import" ) { $jsfiles = array("main", "pages/admin/trigger_import"); }

			if( $wptgg_page_action === "trigger_history" ) {
				wp_enqueue_script('jquery-ui-dialog');

				$jsfiles = array("main", "pages/admin/trigger_history");
			}

			if(isset($jsfiles)) {
				wpTrigger::add_js_file($jsfiles);
			}
		}

		static function trigger_list_page() {
			if( $_GET["page"] === "wp-trigger" && !empty($_GET["trigger_id"]) ) {
				wpTrigger::add_trigger_page();
			} else {
				include( WPTGG_PLUGIN_PATH . "includes/pages/admin/trigger_list.php" );
			}
		}

		static function add_trigger_page() {
			include( WPTGG_PLUGIN_PATH . "includes/pages/admin/add_trigger.php" );
		}

		static function trigger_import_page() {
			include( WPTGG_PLUGIN_PATH . "includes/pages/admin/import_trigger.php" );
		}

		static function trigger_history_page() {
			include( WPTGG_PLUGIN_PATH . "includes/pages/admin/trigger_history.php" );
		}


		//---fron end----

		static function front_include_css_js() {
			echo "<style>
				.wptgg_loading {
					background-image: url( '" . esc_url( WPTGG_PLUGIN_URL . "img/ajax-loader.gif" )."' );
					padding: 0px 7px;
					background-repeat: no-repeat;
				}
				</style>";
			echo "<script type='text/javascript'>var wptgg_ajaxurl = '" . esc_url( admin_url("admin-ajax.php") ) . "'</script>";
			$cssfiles = array("lib/animate");
			wpTrigger::add_css_file($cssfiles);
			$jsfiles = array("lib/jquery.json-2.3", "lib/jquery.scrollTo.min", "lib/jquery.appear", "main", "pages/front/trigger_process");
			wpTrigger::add_js_file($jsfiles, "jquery");
		}

		static function get_trigger_process($param) {
			global $wptrr_num;

			$wptrr_num++;
			ob_start();

			include( WPTGG_PLUGIN_PATH . "includes/pages/front/trigger_process.php" );

			$content = ob_get_contents();

			ob_end_clean();

			return $content;
		}

		function table_column_exists( $table_name, $column_name ) {
			global $wpdb;

			$column = $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
				DB_NAME, $table_name, $column_name
			) );

			if ( ! empty( $column ) ) {
				return true;
			}

			return false;
		}
	}

	$wptrigger = new wpTrigger();
}

add_action( 'wp_ajax_get_trigger_set', array( 'wptggBackEnd', 'ajax_get_trigger_one_set' ) );
add_action( 'wp_ajax_trigger_export', array( 'wptggBackEnd', 'ajax_trigger_export' ) );
add_action( 'wp_ajax_get_display_trigger', array( 'wptggFrontEnd', 'get_display_trigger' ) );
add_action( 'wp_ajax_nopriv_get_display_trigger', array( 'wptggFrontEnd', 'get_display_trigger' ) );
add_action( 'wp_ajax_get_display_optinbox', array( 'wptggFrontEnd', 'get_display_optinbox' ) );
add_action( 'wp_ajax_nopriv_get_display_optinbox', array( 'wptggFrontEnd', 'get_display_optinbox' ) );
?>
