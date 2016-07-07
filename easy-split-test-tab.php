<?php

/**
 * Plugin Name: Easy Split Test Tab
 *
 * Description: This plugin help you and Google Analytics to create experiments that test which version of a landing page leads better. 
 *
 * Plugin URI: http://wordpress.org/extend/plugins/easy-split-test-tab
 * Version: 1.2.1 
 * Author: Ertil Gani
 * Author URI: http://www.prima-posizione.com
 * License: GPLv2
 * @package easy-split-test-tab
 *
 * This plugin used the Object-Oriented Plugin Template Solution as a skeleton
 * easy-split-test-tab
 */
 
/*  Copyright 2013  Prima Posizione Srl  (email : ertil@prima-posizione.it)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );


define( 'EASY_SPLITTEST_TAB_DOMAIN', 'easy-split-test-tab' );
define( 'EASY_SPLITTEST_TAB_VERSION', '1.0' );

define( 'EASY_SPLITTEST_TAB_DIR', WP_PLUGIN_DIR . '/easy-split-test-tab' );
define( 'EASY_SPLITTEST_TAB_URL', WP_PLUGIN_URL . '/easy-split-test-tab' );
GLOBAL $wpdb;
define( 'MY_TABLE_WP', $wpdb->prefix.'easy_splittest_tab');




if (!class_exists("Easy_SplitTest_Tab")) :

class Easy_SplitTest_Tab {

	var $settings, $options_page;
	var $sqltable = MY_TABLE_WP;
	
	function __construct() {	

		if (is_admin()) {
			// Load example settings page
		add_action('admin_menu', array($this,'admin_menu') );	
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ) );
		}
		
        
		
		
		
		add_action('init', array($this,'init') );
		add_action('admin_init', array($this,'admin_init') );
		
		
		register_activation_hook( __FILE__, array($this,'activate') );
		register_deactivation_hook( __FILE__, array($this,'deactivate') );
		
		// Translations for plugin
		self::handle_load_domain();
		
		// Translate plugin create file.mo on languages folder
		load_plugin_textdomain(EASY_SPLITTEST_TAB_DOMAIN, false, WP_CONTENT_DIR . '/lang' );
		
	}
	
	
	
		/**
         * Handles the translation of plugin
         * 
         * @return void
         * @access public
		 */
        function handle_load_domain()
		{
			
			
			// Get language in use from settings
			$locale = get_locale();
			
			if ($locale!='') {
				// locate translation file
				$mofile = EASY_SPLITTEST_TAB_DIR. '/lang/' . EASY_SPLITTEST_TAB_DOMAIN . '-' . $locale . '.mo';
				
				if (file_exists($mofile)) {
					// load translation
					load_textdomain(EASY_SPLITTEST_TAB_DOMAIN, $mofile);
				}
			}
		}
	
	/**
	 * Return the plugin action links.  This will only be called if the plugin
	 * is active.
	 *
	 * @since 0.1
	 * @param array $actions associative array of action names to anchor tags
	 * @return array associative array of plugin action links
	 */
	public function add_plugin_action_links( $actions ) {

		$custom_actions = array(
			'configure' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=easy-split-test-tab-setting-admin' ), __( 'Configure', EASY_SPLITTEST_TAB_DOMAIN ) ),
			'faq'       => sprintf( '<a href="%s">%s</a>', 'http://wordpress.org/plugins/faq/', __( 'FAQ', EASY_SPLITTEST_TAB_DOMAIN ) ),
			'support'   => sprintf( '<a href="%s">%s</a>', 'http://wordpress.org/support/plugin/', __( 'Support', EASY_SPLITTEST_TAB_DOMAIN ) ),
		);

		// add the links to the front of the actions list
		return array_merge( $custom_actions, $actions );
	}
		
	/*
	*  admin_menu
	*
	*  @description:  
	*  @since 3.5.1	
	*/
	function admin_menu() { 		
				if ( ! current_user_can('update_plugins') )
					return;
		
		add_menu_page( 
				__('Easy Split Test Tab', EASY_SPLITTEST_TAB_DOMAIN)
				,__('Easy Split Test', EASY_SPLITTEST_TAB_DOMAIN)
				,'administrator','easy-split-test-tab', array(&$this, 'st_admin_page'), EASY_SPLITTEST_TAB_URL. '/img/st_logo_16x16.png');
 
			
		add_submenu_page('easy-split-test-tab'
		,__('Experiments', EASY_SPLITTEST_TAB_DOMAIN)
		,__('Experiments', EASY_SPLITTEST_TAB_DOMAIN)
		, 'administrator', 'easy-split-test-tab', array(&$this, 'st_admin_page')); 
		
		add_submenu_page('easy-split-test-tab'
		,__('Options', EASY_SPLITTEST_TAB_DOMAIN)
		,__('Options', EASY_SPLITTEST_TAB_DOMAIN)
		, 'administrator', 'easy-split-test-tab-setting-admin', array(&$this, 'st_options_page')); 
		
        add_submenu_page('easy-split-test-tab'
		, __('Help Split Test', EASY_SPLITTEST_TAB_DOMAIN)
		,__('Help', EASY_SPLITTEST_TAB_DOMAIN)
		, 'manage_options', 'easy-split-test-tab-help', array(&$this, 'st_help_page'));
		
        							
				
		}
function st_help_page(){
				require(EASY_SPLITTEST_TAB_DIR . '/admin/st_help_page.php');     
}
/** Admin Page*/function st_admin_page() {
		if (!current_user_can('manage_options')) {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		require_once(dirname(__FILE__) . '/admin/split-test-admin-tables.php');
		$splittest_admin_table = new Easy_SplitTest_Tab_Table();
		$splittest_admin_table->prepare_items('approved'); 
		
		?>
		<div class="wrap">
			<img class="icon32" src="<?php echo EASY_SPLITTEST_TAB_URL ?>/img/st_logo_32x32.png" />
			<h2><?php _e('Easy Split Test Tab Analytics: Experiments',EASY_SPLITTEST_TAB_DOMAIN); 
			echo ' <a href="#TB_inline?width=750&height=550&inlineId=split-test-popup" class="add-new-h2 thickbox">' . esc_html( __( 'Add New', EASY_SPLITTEST_TAB_DOMAIN ) ) . '</a>';
			?></h2>
		</div>	
		
		<?php
		echo '<form id="form" method="POST">';
		$splittest_admin_table->display();
		echo '</form>'; 
		
		
		$this->split_test_popup();
	}
	
	
	
	
	/*
	* Options Page
	*/
	
	function st_options_page() {
		if (!current_user_can('manage_options')) {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		require_once(dirname(__FILE__) . '/admin/split-test-options.php');		
		
		
	}
	
	
	/*
	* * Return all pages: select 
	*/

		
	function select_pages_posts($type){		
		$pages = get_pages();
		 $select_pages_posts = "";
		
		if($type == "select"){
				
				$select_pages_posts='<select name="post_id" id="post_id">';
				$select_pages_posts.='<option value="" selected>'.__('--- Select original page',EASY_SPLITTEST_TAB_DOMAIN).'</option>';			
				
					 foreach ($pages as $pagg) {
					 
							$select_pages_posts .= '<option value="'.$pagg->ID.'">';
							$select_pages_posts .='['.$pagg->ID.'] '. $pagg->post_title;
							$select_pages_posts .= '</option>';                    
					}
					$select_pages_posts .='</select>';			
		} 
		if($type == "checkbox"){
		$select_pages_posts .='<ul class="checkbox-list">';	
					foreach ($pages as $pagg) {                    
							$select_pages_posts .= '<li><input type="checkbox" name="variant_id[]" id="variant_id[]" value="'.$pagg->ID.'"><label>['.$pagg->ID.'] '. $pagg->post_title.'</label><li>';                                        
					}
		$select_pages_posts .='</ul>';				
		}
		return  $select_pages_posts;
		}
		

		
	
	/*
	* Create database on db
	*/	
		
	function split_test_update_database() {
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql = "CREATE TABLE $this->sqltable (
				 id int(11) NOT NULL AUTO_INCREMENT,
				 post_id int(11) DEFAULT '0',
				 variant_id longtext DEFAULT NULL,				 
				 splittest_name varchar(100) DEFAULT NULL,
				 splittest_status tinyint(1) DEFAULT '0',				 
				 splittest_code text,
				 date_time DATETIME DEFAULT NULL,				 
				PRIMARY KEY (id)
				)
				CHARACTER SET utf8
				COLLATE utf8_general_ci;";
		dbDelta($sql);
	}
	
	/*
	* Insert to db
	*/
	function split_test_insert_db(){
		global $wpdb;
		global $post;
		$post_id = '';
		$splittest_name  = '';
		$splittest_code = '';
		$date_time = '';
		
		if (isset($_POST['submit_split_test'])) {
			if ($_POST['submit_split_test'] == 'OK') {
			
		$post_id = $_POST['post_id'];
		$splittest_name  = $_POST['splittest_name'];
		$splittest_code =  $_POST['splittest_code'];		
		$variant_id =  $_POST['variant_id'];
		
		$date_time =  date('Y-n-j G:i:s');
			
			$newdata = array(
					
						'post_id'       => $post_id,
						'splittest_name'   => $splittest_name,
						'variant_id'  => maybe_serialize($variant_id),
						'splittest_code'  =>maybe_serialize($splittest_code),
						'date_time'    => $date_time
					
					
				);
		
			$validData = true;
				if ($post_id == '') {
					$output .= '<span style="color:red;">È necessario inserire un id per la tua recensione.</span>';
					$validData = false;
				} else if ($splittest_name == '') {
					$output .= '<span style="color:red;">È necessario inserire un nome per la tua recensione.</span>';
					$validData = false;				
				}else if ($splittest_code == '') {
					$output .= '<span style="color:red;">È necessario inserire un splittest_code per la tua recensione.</span>';
					$validData = false;				
				}

				
				if ($validData) {
					$wpdb->insert($this->sqltable, $newdata);					
					echo '<div id="message" class="updated fade"><p><strong>' . __( 'Your experiment have been saved.', 'EASY_SPLITTEST_TAB_DOMAIN' ) . '</strong></p></div>';
				}
			
			}
		}
		
					
		return '<div class="updated" style="padding: 10px;">' . $output . '</div>';
	}
	
	
	
		
	/*
		Load language translation files (if any) for our plugin.
	*/
	function init() {
		load_plugin_textdomain( EASY_SPLITTEST_TAB_DOMAIN, EASY_SPLITTEST_TAB_DIR . '/lang', 
							   basename( dirname( __FILE__ ) ) . '/lang' );
	//apdate table					   
	global $wpdb;
		$tableSearch = $wpdb->get_var("SHOW TABLES LIKE '$this->sqltable'");
		
		if ($tableSearch != $this->sqltable) {
			$this->split_test_update_database();
		}
		
	
	add_action( 'wp_head',array($this, 'add_code_GA_on_test_page') );
	add_action('wp_footer' ,array($this, 'add_credits_footer') );
	
	// Add thickbox
	add_thickbox();
		
	}
		
		
	
	
	
	
	/*
	*
	* Split test popup
	*
	*/
	function split_test_popup(){
		?>

	<div id="split-test-popup" style="display:none;">
		<div class="wrap">
			<img class="icon32" src="<?php echo EASY_SPLITTEST_TAB_URL ?>/img/st_logo_32x32.png" />
			<h2><?php _e('Split Test: Experiments: Add new',EASY_SPLITTEST_TAB_DOMAIN); 			
			?></h2>
		</div>
		<form  action="" method="post" id="easy_splittest_tab_form">
			<input type="hidden" name="submit_split_test" value="OK" />
		<table cellpadding="0" cellspacing="5" border="0">
			<tr>
				<td valign="top">
							<label><?php _e('Original page',EASY_SPLITTEST_TAB_DOMAIN); ?></label><br>
						<?php echo $this->select_pages_posts("select"); ?><br>
					<br>
						<label><?php _e('New experiment name',EASY_SPLITTEST_TAB_DOMAIN); ?></label>						
						<input class="splittest_name" type="text" name="splittest_name" value="" size="49"/><br>
					<br>
						<label><?php _e('Experiment google code',EASY_SPLITTEST_TAB_DOMAIN); ?></label><br>
						<label style="font-size:11px; color:red;"><?php _e('Insert only javascript code.',EASY_SPLITTEST_TAB_DOMAIN); ?></label>
						<textarea class="splittest_code" rows="16" cols="50" name="splittest_code"></textarea><br>
				</td>
				<td valign="top">
				<label><?php _e('Variation page',EASY_SPLITTEST_TAB_DOMAIN); ?></label><br>
				<div style=" border:1px solid #ccc; width:250px; height: 400px; overflow-y: scroll;">
					<?php echo $this->select_pages_posts("checkbox"); ?>  
				</div>		
			
				</td>
			</tr>
		</table>
		<input name="submitButton" type="submit" value="Submit Split Test" />
		</form>
		</div>	
	<?php	
	}
	
	
		
	function activate($networkwide) {
		//$this->network_propagate(array($this, '_activate'), $networkwide);
	}

	function deactivate($networkwide) {
		//$this->network_propagate(array($this, '_deactivate'), $networkwide);
	}

	/*
		Enter our plugin activation code here.
	*/
	function _activate() {}

	/*
		Enter our plugin deactivation code here.
	*/
	function _deactivate() {
		//register_deactivation_hook( __FILE__, EASY_SPLITTEST_TAB_DOMAIN );
	}
	

	

	function admin_init() {
	
		add_option( 'easy_splittest_tab_credits', '1');
		add_option( 'easy_splittest_tab_noindex', '0');
		register_setting( 'default', 'easy_splittest_tab_credits' ); 
		register_setting( 'default', 'easy_splittest_tab_noindex' ); 
	
	/*** Custom css 	*/
		wp_register_style( 'easy-split-test-tab-css', EASY_SPLITTEST_TAB_URL.'/css/easy-split-test-tab.css' );
		wp_enqueue_style('easy-split-test-tab-css');
	
		
	}


	/*
		Print the the experiment code on head page.
	*/
	function add_code_GA_on_test_page(){
		global $wpdb, $post;		
		$where = ' WHERE post_id="'.$post->ID.'" AND splittest_status="1"';
		$data = $wpdb->get_row("SELECT * FROM $this->sqltable" . $where );		
		if(isset($data)){		
			echo stripslashes($data->splittest_code);		
		}
	
	$where = ' WHERE  splittest_status="1"';
	$noindex_id = $wpdb->get_row("SELECT * FROM $this->sqltable" . $where );		
	
	
		if(isset($noindex_id)){
		$array_id = maybe_unserialize($noindex_id->variant_id);					
		
			if((in_array($post->ID, $array_id))&&(get_option('easy_splittest_tab_noindex'))):
		?>
<meta name="robots" content="noindex,nofollow">
<?php
			endif;
		}	
	}
	
	/*
		Add credits
	*/
	function add_credits_footer(){
		if(get_option('easy_splittest_tab_credits')): ?>
		<div class="site-info">
			<a href="<?php echo esc_url( __( 'http://www.prima-posizione.com', EASY_SPLITTEST_TAB_DOMAIN ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', EASY_SPLITTEST_TAB_DOMAIN ); ?>" target="_blank">
			<?php printf( __( 'Split Test %s', EASY_SPLITTEST_TAB_DOMAIN ), 'Prima Posizione' ); ?></a>
		</div><!-- .site-info -->
		<?php endif;
	}
	

} // end class
endif;


// Initialize our plugin object.
global $Easy_SplitTest_Tab;
if (class_exists("Easy_SplitTest_Tab") && !$Easy_SplitTest_Tab) {
    $Easy_SplitTest_Tab = new Easy_SplitTest_Tab();	
}	
?>