<?php
/*
 * supreme Framework Version
 */
function supreme_version_init () {
    $suprem_framework_version = '2.0.3';
    if ( get_option( 'suprem_framework_version' ) != $suprem_framework_version ) {
    		update_option( 'suprem_framework_version', $suprem_framework_version );
    }
}
add_action( 'init', 'supreme_version_init', 10 );
add_action('admin_menu','supreme_templatic_menu');
add_action('admin_menu','remove_supreme_templatic_menu');
/*
 * Supreme framework update menu
 */
 
function supreme_templatic_menu(){
	if(is_tevolution_active()){
		
		add_submenu_page('templatic_system_menu', "",   '<span class="tevolution-menu-separator" style="display:block; 1px -5px;  padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>',"administrator", "#"  );
		add_submenu_page( 'templatic_system_menu', __('Theme Update',THEME_DOMAIN), __('Theme Update',THEME_DOMAIN), 'administrator', 'tmpl_theme_update', 'tmpl_theme_update',27 );
		
		
		add_submenu_page( 'templatic_system_menu',  __('Get Support',THEME_DOMAIN) , __('Get Support',THEME_DOMAIN) , 'administrator', 'tmpl_support_theme', 'tmpl_support_theme',29 );
		
		add_submenu_page( 'templatic_system_menu',  __('Browse Other Themes',THEME_DOMAIN), __('Browse Other Themes',THEME_DOMAIN), 'administrator', 'tmpl_purchase_theme', 'tmpl_purchase_theme',30 );
	}else{
		add_menu_page('Templatic', 'Templatic', 'administrator', 'templatic_menu', 'tmpl_theme_update', '',111); 
		
		add_submenu_page( 'templatic_menu',  __('Theme Update',THEME_DOMAIN), __('Theme Update',THEME_DOMAIN), 'administrator', 'tmpl_theme_update', 'tmpl_theme_update',27 );
		
		add_submenu_page( 'templatic_menu',  __('Get Support',THEME_DOMAIN) , __('Get Support',THEME_DOMAIN) , 'administrator', 'tmpl_support_theme', 'tmpl_support_theme',29 );
		
		add_submenu_page( 'templatic_menu',  __('Browse Other Themes',THEME_DOMAIN), __('Browse Other Themes',THEME_DOMAIN), 'administrator', 'tmpl_purchase_theme', 'tmpl_purchase_theme',30 );
	}
}
function remove_supreme_templatic_menu(){
	remove_submenu_page('templatic_menu','templatic_menu');
}
if(!function_exists('tmpl_purchase_theme')){
function tmpl_purchase_theme(){
	wp_redirect('http://templatic.com/wordpress-themes-store/'); exit;
}}
/* frame work update templatic menu*/
function tmpl_support_theme(){
	echo "<h3>".__("Need Help?",THEME_DOMAIN)."</h3>";
	echo "<p>".__("Here's how you can get help from templatic on any thing you need with regarding this theme.",THEME_DOMAIN)." </p>";
	echo "<br/>";
	echo '<p><a href="http://templatic.com/docs/theme-guides/">'.__("Take a look at theme guide",THEME_DOMAIN).'</a></p>';
	echo '<p><a href="http://templatic.com/docs/" target="blank">'.__("Knowlegebase",THEME_DOMAIN).'</a></p>';
	echo '<p><a href="http://templatic.com/forums/" target="blank">'.__("Explore our community forums",THEME_DOMAIN).'</a></p>';
	echo '<p><a href="http://templatic.com/helpdesk/" target="blank">'.__("Create a support ticket in Helpdesk",THEME_DOMAIN).'</a></p>';
}
/* frame work update templatic menu*/
function tmpl_theme_update(){
	
	require_once(TEMPLATE_DIR."library/templatic_login.php");
}?>