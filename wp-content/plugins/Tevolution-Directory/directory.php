<?php
/*
Plugin Name: Tevolution - Directory
Plugin URI: http://templatic.com/
Description: Tevolution - Directory plugin is specially built to turn your site into a powerful listings directory for any niche. To be used with Tevolution, this plugin is loaded with a bundle of features like listing submissions, power search, unlimited categories, custom fields and subscription or per listing payment packages. .
Version: 1.0.5
Author: Templatic
Author URI: http://templatic.com/
*/
ob_start();
// Plugin version
define( 'TEVOLUTION_DIRECTORY_VERSION', '1.0.5' );
define('TEVOLUTION_DIRECTORY_SLUG','Tevolution-Directory/directory.php');
// Plugin Folder URL
define( 'TEVOLUTION_DIRECTORY_URL', plugin_dir_url( __FILE__ ) );
// Plugin Folder Path
define( 'TEVOLUTION_DIRECTORY_DIR', plugin_dir_path( __FILE__ ) );
// Plugin Root File
define( 'TEVOLUTION_DIRECTORY_FILE', __FILE__ );
//Define domain name
@define('DOMAIN','tevolution_directory');
if(!defined('INCLUDE_ERROR'))
	define('INCLUDE_ERROR',__('System might facing the problem in include ',DOMAIN));
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
ob_start();
error_reporting(0);
global $templatic_settings;
$templatic_settings=get_option('templatic_settings');
if(strstr($_SERVER['REQUEST_URI'],'plugins.php')){
	require_once('wp-updates-plugin.php');
	new WPDirectoryUpdates( 'http://templatic.com/updates/api/index.php', plugin_basename(__FILE__) );
}
if(is_plugin_active('Tevolution/templatic.php'))
{
	//Include plugin main files.
	load_textdomain( DOMAIN,TEVOLUTION_DIRECTORY_DIR.'languages/en_US.mo' );
	//load_plugin_textdomain( DOMAIN, TEVOLUTION_DIRECTORY_DIR); it's deprecated since version 2.7	
	
	//Include the tevolution plugins main file to use the core functionalities of plugin.
	if(is_plugin_active('Tevolution/templatic.php') && file_exists(WP_PLUGIN_DIR . '/Tevolution/templatic.php')){
		include_once( WP_PLUGIN_DIR . '/Tevolution/templatic.php');
	}else{
		add_action('admin_notices','directory_admin_notices');
	}
	
	require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
	
	/* Bundle Box*/
	if(is_admin() && (isset($_REQUEST['page']) && $_REQUEST['page']=='templatic_system_menu')){
		include(TEVOLUTION_DIRECTORY_DIR."bundle_box.php");	
		include(TEVOLUTION_DIRECTORY_DIR."install.php");
	}
	include(TEVOLUTION_DIRECTORY_DIR.'listing/listing.php');	
	
	
	if (function_exists('is_active_addons') && is_active_addons('tevolution_directory')){	
		include(TEVOLUTION_DIRECTORY_DIR.'functions/manage_function.php');		
	}
	
	
}else{
	add_action('admin_notices','directory_admin_notices');
}
function directory_admin_notices(){
		echo '<div class="error"><p>' . sprintf(__('You have not activated the base plugin %s. Please activate it to use Tevolution-Directory plugin.',DOMAIN),'<b>Tevolution</b>'). '</p></div>';
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),'directory_action_links'  );
function directory_action_links($links){
	if (function_exists('is_active_addons') && is_active_addons('tevolution_directory')){
		$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=templatic_settings' ) . '">' . __( 'Settings', DOMAIN ) . '</a>',				
		);
	}else{
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=templatic_system_menu' ) . '">' . __( 'Settings', DOMAIN ) . '</a>',
		);
	}
	return array_merge( $plugin_links, $links );
}
/*
 * Plugin Deactivation hook
 * 
 */
 
register_deactivation_hook(__FILE__,'unregister_directory_taxonomy');
function unregister_directory_taxonomy(){
	 $post_type = get_option("templatic_custom_post");
	 $taxonomy = get_option("templatic_custom_taxonomy");
	 $tag = get_option("templatic_custom_tags");
	 $taxonomy_slug = $post_type['listing']['slugs'][0];
	 $tag_slug = $post_type['listing']['slugs'][1];
	 
	 unset($post_type['listing']);
	 unset($taxonomy[$taxonomy_slug]);
	 unset($tag[$tag_slug]);
	 update_option("templatic_custom_post",$post_type);
	 update_option("templatic_custom_taxonomy",$taxonomy);
	 update_option("templatic_custom_tags",$tag);
	 unlink(get_template_directory()."/taxonomy-".$taxonomy_slug.".php");
	 unlink(get_template_directory()."/taxonomy-".$tag_slug.".php");
	 unlink(get_template_directory()."/single-listing.php");
	 
	 delete_option('directory_redirect_activation');
}
register_activation_hook(__FILE__,'directory_plugin_activate');
if(!function_exists('directory_plugin_activate')){
	function directory_plugin_activate(){	
		global $wpdb;
		
		$field_check = $wpdb->get_var("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_icon'");
		if('term_icon' != $field_check)	{
			$wpdb->query("ALTER TABLE $wpdb->terms ADD term_icon varchar(255) NOT NULL DEFAULT ''");
		}
		update_option('directory_redirect_activation','Active');
	}
}
add_action('admin_init', 'directory_plugin_redirect');
/*
Name : directory_plugin_redirect
Description : Redirect on plugin templatic settings
*/
function directory_plugin_redirect()
{
	if (get_option('directory_redirect_activation') == 'Active')
	{
		update_option('directory_redirect_activation', 'Deactive');
		wp_redirect(site_url().'/wp-admin/admin.php?page=templatic_settings');
	}
}
/*
 * Function Name: directory_update_login
 * Return: update directory_update_login plugin version after templatic member login
 */
add_action('wp_ajax_directory','directory_update_login');
function directory_update_login()
{
	check_ajax_referer( 'directory', '_ajax_nonce' );
	$plugin_dir = rtrim( plugin_dir_path(__FILE__), '/' );	
	require_once( $plugin_dir .  '/templatic_login.php' );	
	exit;
}
/* remove wp autoupdates */
add_action('admin_init','directory_wpup_changes',20);
function directory_wpup_changes(){
	 remove_action( 'after_plugin_row_Tevolution-Directory/directory.php', 'wp_plugin_update_row' ,10, 2 );
}
/*
Name :tevolution_directory_admin_menu
Desc : add advertisement menu in tevolution
*/
add_action('templ_add_admin_menu_', 'tevolution_directory_admin_menu', 21);
function tevolution_directory_admin_menu(){
	$menu_title1 = __('Listing', DOMAIN);
	$menu_title2 = __('Add a Listing', DOMAIN);
	add_submenu_page('templatic_system_menu', "",   '<span class="tevolution-menu-separator" style="display:block; 1px -5px;  padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>',"administrator", "admin.php?page=templatic_system_menu"  );
	add_submenu_page('templatic_system_menu', $menu_title1, $menu_title1,'administrator', 'edit.php?post_type=listing', '');	
	add_submenu_page('templatic_system_menu', $menu_title2, $menu_title2,'administrator', 'post-new.php?post_type=listing', '');	
}
add_action('admin_init','directory_wpup_changes',20);
?>