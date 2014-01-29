<?php
/*
Plugin Name: Tevolution - LocationManager
Plugin URI: http://templatic.com/
Description: Tevolution - Location Manager plugin is specially built to enhance your site's functionality by allowing location search and sort, setup the maps on your custom post pages with pin point effects. You can also add and manage locations for your site and even have city logs that will show you the number of visits to each of your cities.
Version: 1.0.4
Author: Templatic
Author URI: http://templatic.com/
*/
ob_start();
error_reporting(0);

// Plugin version
define( 'TEVOLUTION_LOCATION_VERSION', '1.0.4' );
define('TEVOLUTION_LOCATION_SLUG','Tevolution-LocationManager/location-manager.php');
// Plugin Folder URL
define( 'TEVOLUTION_LOCATION_URL', plugin_dir_url( __FILE__ ) );
// Plugin Folder Path
define( 'TEVOLUTION_LOCATION_DIR', plugin_dir_path( __FILE__ ) );
// Plugin Root File
define( 'TEVOLUTION_LOCATION_FILE', __FILE__ );
//Define domain name
@define('LM_DOMAIN','tevolution_location');
if(!defined('INCLUDE_ERROR'))
	define('INCLUDE_ERROR',__('System might facing the problem in include ',LM_DOMAIN));
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(strstr($_SERVER['REQUEST_URI'],'plugins.php')){
	require_once('wp-updates-plugin.php');
	new WP_Location_Manager_Updates( 'http://templatic.com/updates/api/index.php', plugin_basename(__FILE__) );
}
/*
Name:get_tmpl_plugin_directory
desc: return the plugin directory path
*/
if(!function_exists('get_tmpl_plugin_directory')){
function get_tmpl_plugin_directory() {
	 return WP_CONTENT_DIR."/plugins/";
}
}

if(file_exists(get_tmpl_plugin_directory() . 'Tevolution-LocationManager/language.php')){
		include_once( get_tmpl_plugin_directory() . 'Tevolution-LocationManager/language.php');
}
if(is_plugin_active('Tevolution/templatic.php'))
{
	//Include booking system's main files.
	load_textdomain( LM_DOMAIN,TEVOLUTION_LOCATION_DIR.'languages/en_US.mo' );
	
	//Include the tevolution plugins main file to use the core functionalities of plugin.
	if(file_exists(get_tmpl_plugin_directory() . 'Tevolution/templatic.php')){
		include_once( get_tmpl_plugin_directory() . 'Tevolution/templatic.php');
	}
	
	require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
	
	/* Bundle Box*/
	if(is_admin() && (isset($_REQUEST['page']) && $_REQUEST['page']=='templatic_system_menu')){
		include(TEVOLUTION_LOCATION_DIR."bundle_box.php");	
		include(TEVOLUTION_LOCATION_DIR."install.php");
	}
	
	
	if (function_exists('is_active_addons') && is_active_addons('tevolution_location')){	
		include(TEVOLUTION_LOCATION_DIR.'functions/manage_function.php');
		if(file_exists(TEVOLUTION_LOCATION_DIR.'functions/map/map-shortcodes/map-shortcodes.php')){
			include(TEVOLUTION_LOCATION_DIR.'functions/map/map-shortcodes/map-shortcodes.php');
		}
	}
	
	
}else
{
	add_action('admin_notices','location_admin_notices');
}
function location_admin_notices(){
	echo '<div class="error"><p>' . sprintf(__('You have not activated the base plugin %s. Please activate it to use Tevolution-LocationManager plugin.',LM_DOMAIN),'<b>Tevolution</b>'). '</p></div>';
	
}
/* plugin activation hook */
register_activation_hook(__FILE__,'location_plugin_activate');
if(!function_exists('location_plugin_activate')){
	function location_plugin_activate(){
		global $wpdb;
		update_option('tevolution_location','Active');
		$field_check = $wpdb->get_var("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_icon'");
		if('term_icon' != $field_check)	{
			$wpdb->query("ALTER TABLE $wpdb->terms ADD term_icon varchar(255) NOT NULL DEFAULT ''");
		}
		
		$location_post_type[]='post,category,post_tag';
		$post_types=get_option('templatic_custom_post');
		foreach($post_types as $key=>$val){
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $key,'public'   => true, '_builtin' => true ));
			$location_post_type[]=$key.','.$taxonomies[0].','.$taxonomies[1];
		}
		if(!get_option('location_post_type'))
			$post_types=update_option('location_post_type',$location_post_type);
		update_option('location_redirect_activation','Active');		
		update_option('directory_multicity_location','location_navigation');
		update_option('default_city_set','default_city');
	}
	
}
function location_plugin_activate_settings(){ 
	global $wpdb,$pagenow;
	/*
	 * Create postcodes table and save the sorting option in templatic setting on plugin page or location setting system menu page
	 */
	if($pagenow=='plugins.php' || $pagenow=='themes.php' || (isset($_REQUEST['page']) && $_REQUEST['page']=='location_settings')){
		update_option('tevolution_location','Active');
		$field_check = $wpdb->get_var("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_icon'");
		if('term_icon' != $field_check)	{
			$wpdb->query("ALTER TABLE $wpdb->terms ADD term_icon varchar(255) NOT NULL DEFAULT ''");
		}
		
		$location_post_type[]='post,category,post_tag';
		$post_types=get_option('templatic_custom_post');
		foreach($post_types as $key=>$val){
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $key,'public'   => true, '_builtin' => true ));
			$location_post_type[]= @$key.','. @$taxonomies[0].','. @$taxonomies[1];
		}
	
		if(!get_option('location_post_type'))
			update_option('location_post_type',$location_post_type);
			
	}
}
add_action('admin_init', 'location_plugin_activate_settings');
add_action('admin_init', 'location_plugin_redirect');
/*
Function Name : directory_plugin_redirect
Description : Redirect on plugin templatic settings
*/
function location_plugin_redirect()
{
	if (get_option('location_redirect_activation') == 'Active')
	{
		update_option('location_redirect_activation', 'Deactive');
		wp_redirect(site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=city_manage_locations');
	}
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),'location_action_links'  );
function location_action_links($links){
	if (function_exists('is_active_addons') && is_active_addons('tevolution_location')){
		$plugin_links = array(				
				'<a href="' . admin_url( 'admin.php?page=location_settings' ) . '">' . __( 'Location Settings', LM_DOMAIN ) . '</a>',
		);
	}else{
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=templatic_system_menu' ) . '">' . __( 'Settings', LM_DOMAIN ) . '</a>',
		);
	}
	return array_merge( $plugin_links, $links );
}
/*
 * Function name: templ_add_admin_menu_
 * Return: display the admin submenu page of tevolution menu page
 */
add_action('templ_add_admin_menu_', 'location_add_page_menu', 20);
function location_add_page_menu(){
	$menu_title2 = __('Manage Locations', LM_DOMAIN);
	global $location_settings_option;
	add_submenu_page('templatic_system_menu', "",   '<span class="tevolution-menu-separator" style="display:block; 1px -5px;  padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>',"administrator", "admin.php?page=templatic_system_menu",''  );
	add_submenu_page('templatic_system_menu',$menu_title2,'','administrator', 'location_settings', 'location_plugin_settings');
	$location_settings_option=add_submenu_page('templatic_system_menu', $menu_title2, $menu_title2,'administrator', 'location_settings&amp;location_tabs=location_manage_locations&amp;locations_subtabs=city_manage_locations', 'location_plugin_settings');
	add_action("load-$location_settings_option", "location_settings_option");
}
/*
 * Function Name: location_menu_script
 * Return: active manage location menu
 */
add_action('admin_footer','location_menu_script');
function location_menu_script()
{
	?>
	<script type="text/javascript">
     jQuery(document).ready(function(){	
          if(jQuery('#adminmenu ul.wp-submenu li').hasClass('current'))
          {
               <?php if(isset($_REQUEST['page']) && $_REQUEST['page']=='location_settings' && isset($_REQUEST['location_tabs']) && $_REQUEST['location_tabs']!='' ):?>
               jQuery('#adminmenu ul.wp-submenu li').removeClass('current');
               jQuery('#adminmenu ul.wp-submenu li a').removeClass('current');								
               jQuery('#adminmenu ul.wp-submenu li a[href*="page=location_settings&location_tabs=location_manage_locations&locations_subtabs=city_manage_locations"]').attr('href', function() {					    
                        jQuery('#adminmenu ul.wp-submenu li a[href*="page=location_settings&location_tabs=location_manage_locations&locations_subtabs=city_manage_locations"]').addClass('current');
                        jQuery('#adminmenu ul.wp-submenu li a[href*="page=location_settings&location_tabs=location_manage_locations&locations_subtabs=city_manage_locations"]').parent().addClass('current');
               });
               <?php endif;?>
          }
     });
     </script>
     <?php
}
/* 
 * Function Name:  location_init
 * Return: remove the wpml icl_redirect_canonical_wrapper function for home page redirect issue
 */
add_action('plugins_loaded', 'location_init'); 
function location_init(){
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))
	{
	 	remove_action('template_redirect', 'icl_redirect_canonical_wrapper', 11);
	}
}
/*
 * Function Name: directory_update_login
 * Return: update directory_update_login plugin version after templatic member login
 */
add_action('wp_ajax_location-manager','location_manager_update_login');
function location_manager_update_login()
{
	check_ajax_referer( 'location-manager', '_ajax_nonce' );
	$plugin_dir = rtrim( plugin_dir_path(__FILE__), '/' );	
	require_once( $plugin_dir .  '/templatic_login.php' );	
	exit;
}
/* remove wp autoupdates */
add_action('admin_init','location_manager_wpup_changes',20);
function location_manager_wpup_changes(){
	 remove_action( 'after_plugin_row_Tevolution-LocationManager/location-manager.php', 'wp_plugin_update_row' ,10, 2 );
}