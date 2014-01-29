<?php
/**
 * The functions file is used to initialize everything in the theme.  It controls how the theme is loaded and 
 * sets up the supported features, default actions, and default filters.  If making customizations, users 
 * should create a child theme and make changes to its functions.php file (not this one).  Friends don't let 
 * friends modify parent theme files. ;)
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */
ob_start();
error_reporting(0);
ini_set("session.gc_maxlifetime","14000");
if(file_exists(trailingslashit ( get_template_directory() ) . 'library/supreme.php'))
	require_once( trailingslashit ( get_template_directory() ) . 'library/supreme.php' ); // contain all classes and core function pf the framework
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
define('THEME_DOMAIN','supreme'); // for localization
load_theme_textdomain(THEME_DOMAIN, get_template_directory().'/languages/en_US.mo');
load_textdomain( THEME_DOMAIN, get_template_directory().'/languages/en_US.mo');
define('TEMPLATE_URI',trailingslashit(get_template_directory_uri()));
define('TEMPLATE_DIR',trailingslashit(get_template_directory()));
$theme = new Supreme(); /* Part of the framework. */
$page= @$_REQUEST['page'];
if(is_admin() && ($pagenow =='themes.php' || $pagenow =='post.php' || $pagenow =='edit.php'|| $pagenow =='admin-ajax.php' || trim($page) == trim('tmpl_theme_update'))){
	require_once('wp_theme_update.php');	
	new WPUpdates_Supreme_Updater( 'http://templatic.com/updates/api/', basename(get_template_directory_uri()) );
}
/*------------------------
  Theme setup function.  This function adds support for theme features and defines the default theme
  actions and filters.
 -----------------------------*/ 
function theme_activation(){
	global $pagenow;	
	if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {
		$b = array(
				'supreme_logo_url' 					=> get_template_directory_uri()."/images/logo.png",
				'supreme_site_description'			=> 0,
				'customcss'                             => 1,
				'supreme_display_image'				=> 1,
				'display_author_name'				=> 1,
				'display_publish_date'				=> 1,
				'display_post_terms'				=> 1,
				'supreme_display_noimage'			=> 1,
				'supreme_archive_display_excerpt'	     => 1,
				'templatic_excerpt_length'			=> 27,
				'display_header_text'				=> 1,
				'supreme_show_breadcrumb'			=> 1,
				'footer_insert' 					=> '<p class="copyright">&copy; 2013 <a href="http://templatic.com/demos/directory">Directory</a>. &nbsp;Designed by <a href="http://templatic.com" class="footer-logo"><img src="'.get_template_directory_uri().'/library/images/templatic-wordpress-themes.png" alt="WordPress Directory Theme" /></a></p>'
			);
		if(function_exists('supreme_prefix'))
			$supreme_prefix=supreme_prefix();
		else
			$supreme_prefix=sanitize_key( apply_filters( 'hybrid_prefix', get_template() ) );
		
		update_option($supreme_prefix.'_theme_settings',$b);
	}
}
add_action('admin_init','theme_activation'); 
add_action( 'after_setup_theme', 'supreme_theme_setup',11 );
function supreme_theme_setup() {
	/* Get action/filter hook prefix. */
	$prefix = supreme_prefix(); // Part of the framework, cannot be changed or prefixed.
	
	if(file_exists(get_template_directory().'/library/functions/functions.php')){
		require_once(get_template_directory().'/library/functions/functions.php'); // framework functions file 
	}
	
	if(file_exists(get_template_directory().'/functions/theme-functions.php')){
		require_once(get_template_directory().'/functions/theme-functions.php'); // framework functions file 
	}
	
	/* Add framework menus. */
	add_theme_support( 'supreme-core-menus', array( // Add core menus.
		'primary',
		'secondary',
		'subsidiary'
		) );
	/* Register aditional menus */
	
	/* if(!strstr($_SERVER['REQUEST_URI'],'/wp-admin/') && isset($_REQUEST['adv_search']) && $_REQUEST['adv_search'] == 1){
		add_action('posts_where','templatic_searching_filter_where');
	} */
	/* Add framework sidebars */
	/* add sidebar support in theme , want to remove from child theme as remove theme support from child theme's functions file */
	add_theme_support('supreme-core-seo');
	add_theme_support( 'supreme-core-sidebars', array( // Add sidebars or widget areas.
				'header',
				'mega_menu',
				'secondary_navigation_right',
				'home-page-banner',
				'home-page-content',
				'before-content',
				'entry',
				'after-content',
				'front-page-sidebar',
				'author-page-sidebar',
				'post-listing-sidebar',
				'post-detail-sidebar',
				'primary-sidebar',
				'after-singular',
				'subsidiary',
				'subsidiary-2c',
				'subsidiary-3c',
				'contact_page_widget',
				'contact_page_sidebar',
				'supreme_woocommerce',
				'footer'
				) );
	add_theme_support('slider-post-content');
	/* add theme support for menu */
	/* Add framework menus. */
	add_theme_support( 'supreme-core-menus', array( // Add core menus.
				'primary',
				'secondary',
				//'subsidiary',
				'footer',		
	) );
	add_theme_support( 'post-formats', array(
		'aside',
		'audio',
		'gallery',
		'image',
		'link',
		'quote',
		'video'
		) );
	add_post_type_support( 'post', 'post-formats' ); // support post format
	add_post_type_support( 'portfolio', 'post-formats' ); // for potfolio slides option in slider
	add_theme_support( 'supreme_banner_slider' ); // work with home page banner slider
	add_theme_support( 'supreme-show-commentsonlist' ); // to show comments counting on listing
	add_theme_support( 'supreme-core-widgets' ); // to support widgest 
	add_theme_support( 'supreme-core-shortcodes' ); // to support shortcodes
	add_theme_support( 'supreme-core-template-hierarchy' ); // This is important. Do not remove. */
	add_theme_support("home_listing_type_value");
	add_theme_support("taxonomy_sorting");
	add_theme_support("google_map"); // Show gogole map if location manager active
	add_theme_support("tevolution_my_favourites"); // Show my favourites & add to favourites with tevolution
	add_theme_support("tevolution_author_listing"); // show author listing widget with tevolution
	add_theme_support("map_fullwidth_support");
	//add_theme_support("listing_excerpt_setting"); //include if you are not using templatic theme but want to use excerpt length option from customizer
	add_action('init','remove_home_page_feature_listing_filter');
	/* Add theme support for framework layout extension. */
	add_theme_support( 'theme-layouts', array( // Add theme layout options.
		'1c',
		'2c-l',
		'2c-r',
		) );
	/* Add theme support for other framework extensions */
	
	//add_theme_support( 'custom-header' );
	add_theme_support( 'post-stylesheets' );
	add_theme_support( 'loop-pagination' );
	add_theme_support( 'get-the-image' );
	add_theme_support( 'breadcrumb-trail' );
	add_theme_support( 'supreme-core-theme-settings', array( 'footer' ) );
    if ( !current_user_can('edit_posts') ) {
		//show_admin_bar(false);
	}
	/* Add theme support for WordPress features. */
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'add-listing-to-favourite' );
	add_theme_support("tev_taxonomy_sorting_opt");
	//add_theme_support("tev_taxonomy_excerpt_opt");
	/* Add theme support for WordPress background feature */
	add_theme_support( 'custom-background', array (
		'default-color' => '',
		'default-image' => '',
		'wp-head-callback' => 'supreme_custom_background_callback',
		'admin-head-callback' => '',
		'admin-preview-callback' => ''
	));
	/* Modify excerpt more */
	add_filter('excerpt_length', 'supreme_excerpt_length',11);
	add_filter('excerpt_more', 'new_excerpt_more');
	/* Wraps <blockquote> around quote posts. */
	add_filter( 'the_content', 'supreme_quote_post_content' );
	add_filter( 'embed_defaults', 'supreme_embed_defaults' ); // Set default widths to use when inserting media files
	add_filter( 'sidebars_widgets', 'supreme_disable_sidebars' );
	/* Add aditional layouts */
	add_filter( 'theme_layouts_strings', 'supreme_theme_layouts' );
	###### ACTIONS ######
	/* Load resources into the theme. */
	add_action( 'wp_enqueue_scripts', 'supreme_resources' );
	/* Register new image sizes. */
	add_action( 'init', 'supreme_register_image_sizes' );
	add_action( 'init', 'supreme_support_woo' );
	/* Assign specific layouts to pages based on set conditions and disable certain sidebars based on layout choices. */
	add_action( 'template_redirect', 'supreme_layouts' );
	/* adding customizing taxture settings for background */
	if(function_exists('templatic_texture_settings')){
		add_action('wp_head','templatic_texture_settings');
	}
	/* Register additional widget areas. */
	add_action( 'widgets_init', 'supreme_register_sidebars', 11 ); // Number 11 indicates custom sidebars should be registered after Hybrid Core Sidebars
	/* WooCommerce Functions. */
	if ( function_exists( 'is_woocommerce' ) ) {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
	}
	/* Set content width. */
	supreme_set_content_width( 600 );
	/****** Theme related files ******/
	//add_theme_support('theme-functions');
	/* for theme functions support */
	if(current_theme_supports('theme-functions')){
		include(get_template_directory()."/functions/theme-functions.php");
	}
	if(current_theme_supports('tevolution_my_favourites')){
		add_action('tevolution_author_tab','tmpl_dashboard_favourites_tab'); // to display tab 
	}

	if(file_exists(get_stylesheet_directory()."/functions/childtheme-functions.php")){
		include_once(get_stylesheet_directory()."/functions/childtheme-functions.php"); //child theme directory functions file
	}
	if ( get_header_textcolor()=='blank') { ?>
<style type="text/css">
#site-title, #site-description {
	text-indent: -99999px;
}
</style>
<?php }
	remove_action("wp_head", "supreme2_view_counter");
	add_filter('tev_gravtar_size','tev_gravtar_size_hook');
	if(is_admin() && is_writable(WP_CONTENT_DIR."/plugins") && is_readable(get_template_directory())){
		$tev_zip = get_template_directory_uri()."/Tevolution.zip";
		$tev_zip_path = get_template_directory()."/Tevolution.zip";
		$dir_zip = get_template_directory_uri()."/Tevolution-Directory.zip";
		$dir_zip_path = get_template_directory()."/Tevolution-Directory.zip";
		$loc_zip = get_template_directory_uri()."/Tevolution-LocationManager.zip";
		$loc_zip_path = get_template_directory()."/Tevolution-LocationManager.zip";
		
		$target_path1 = get_tmpl_plugin_directory()."Tevolution.zip";  // change this to the correct site path
		$target_path2 = get_tmpl_plugin_directory()."Tevolution-Directory.zip";  // change this to the correct site path
		$target_path3 = get_tmpl_plugin_directory()."Tevolution-LocationManager.zip";  // change this to the correct site path
		
		$plug_path1 = "Tevolution/templatic.php";  // change this to the correct site path
		$plug_path2 = "Tevolution-Directory/directory.php";  // change this to the correct site path
		$plug_path3 = "Tevolution-LocationManager/location-manager.php";  // change this to the correct site path
		global $pagenow;
	
		$on_go = get_option('tev_on_go');
		if(!$on_go){ $on_go =0; }
		if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' && $on_go == 0 ) {
		if(file_exists($tev_zip_path))
			zip_copy( $tev_zip, $target_path1, $plug_path1 );
		if(file_exists($dir_zip_path))
			zip_copy( $dir_zip, $target_path2, $plug_path2 );
		if(file_exists($loc_zip_path))
			zip_copy( $loc_zip, $target_path3, $plug_path3, $add_msg =1 );
		}
	}
	/* code to auto extract plugins END*/
	 
	add_filter( 'sidebars_widgets', 'directory_disable_sidebars' );
} 
/* change gravatra size on author page */
function tev_gravtar_size_hook(){
	return 352;
}
/* Show authentication message */
function dir_one_click_install(){
	echo "<div id='ajax-notification' class='updated'><p><span style='color:red;'>AUTHENTICATION REQUIRED:</span>Your server does not allow automated plugin activation so you will have to activate the plugins manually one by one.</p>  </div>";
}
/* code to auto extract plugins  START*/	 
function zip_copy( $source, $target, $plug_path, $add_msg=0) 
{
		if(!@copy($source,$target))
		{	add_action('admin_notices','dir_one_click_install');
			$errors= error_get_last();
			echo "<span style='color:red;'>COPY ERROR:</span> ".$errors['type'];
			echo "<br />\n".$errors['message'];
		} else {
				$file = explode('.',$target);
		
				if(file_exists($target)){ 
					$message ="<span style='color:green;'>File copied from remote!</span><br/>";
					
					$zip = new ZipArchive();
					$x = $zip->open($target);
					
					if ($x === true && file_exists($target)) { 
						$zip->extractTo( get_tmpl_plugin_directory()); // change this to the correct site path
						$zip->close();
		
						
						unlink($target);
						$message = "Your .zip file was uploaded and unpacked.<br/>";
					}else{
						
					}
				}
			if($add_msg == 1 && strstr($_SERVER['REQUEST_URI'],'themes.php')){ 
				update_option('tev_on_go',1);
				
				$plug_path2 = "Tevolution-Directory/directory.php";  // change this to the correct site path
				$plug_path3 = "Tevolution-LocationManager/location-manager.php";  // change this to the correct site path
				$plug_path1 = "Tevolution/templatic.php";  // change this to the correct site path
				
				activate_plugin($plug_path2);
				activate_plugin($plug_path3);
				activate_plugin($plug_path1);
				
				$location_post_type[]='post,category,post_tag';
				$location_post_type[]='listing,listingcategory,listingtags';
				$post_types=update_option('location_post_type',$location_post_type);
			}
	}
}
/*
Name : supreme_support_woo
Description : to update option , is theme is support woocommerce or not 
*/
function supreme_support_woo(){
    $currrent_theme_name = wp_get_theme();
	$templatic_woocommerce_themes = get_option('templatic_woocommerce_themes');
	$templatic_woocommerce_ = str_replace(',','',get_option('templatic_woocommerce_themes'));
	if(!strstr(trim($templatic_woocommerce_) ,trim($currrent_theme_name))):
		update_option('templatic_woocommerce_themes',$templatic_woocommerce_themes.",".$currrent_theme_name);
	endif;		
}
/*
Name : supreme_resources
Description : load js files for supreme
*/
function supreme_resources() {
	wp_enqueue_script( 'supreme-scripts', trailingslashit ( get_template_directory_uri() ) . 'js/_supreme.min.js', array( 'jquery' ), '20120606', true );
	/* for Gravity Forms */
	
	if( class_exists( 'RGForms' ) && class_exists( 'RGFormsModel' )) {
		wp_enqueue_style( 'supreme-gravity-forms', trailingslashit (get_template_directory_uri() ) . 'css/gravity-forms.css', false, '20120312', 'all' );
	}
	
	/* for WooCommerce */
	
	if( function_exists( 'is_woocommerce') ) {
		wp_dequeue_style( 'woocommerce_frontend_styles' );
		wp_dequeue_style( 'woocommerce_chosen_styles' );
	}
}
/**
 * This is a fix for when a user sets a custom background color with no custom background image.  What 
 * happens is the theme's background image hides the user-selected background color.  If a user selects a 
 * background image, we'll just use the WordPress custom background callback.
 * 
 * Thanks to Justin Tadlock for the code.
 *
 * @since 0.1
 * @link http://core.trac.wordpress.org/ticket/16919
 */
function supreme_custom_background_callback() {
	/* Get the background image. */
	$image = get_background_image();
	/* If there's an image, just call the normal WordPress callback. We won't do anything here. */
	if ( !empty( $image ) ) {
		_custom_background_cb();
		return;
	}
	/* Get the background color. */
	$color = get_background_color();
	/* If no background color, return. */
	if ( empty( $color ) )
		return;
	/* Use 'background' instead of 'background-color'. */
	$style = "background: #{$color};";
?>
<style type="text/css">
body.custom-background {
<?php echo trim( $style );
?>
}
</style>
<?php
}
/*
Name : supreme_thumbnail_image_height
Description : Registers additional image size 'supreme-thumbnail'.
*/
function supreme_thumbnail_image_height() {
	return $thumbnail_height = apply_filters('supreme_thumbnail_image_height',170);
}
/*
Name : supreme_thumbnail_image_width
Description : Registers additional image size 'supreme-thumbnail'.
*/
function supreme_thumbnail_image_width() {
	return $thumbnail_width = apply_filters('supreme_thumbnail_image_width',220);
}
/*
Name : supreme_register_image_sizes
Description : Registers additional image size 'supreme-thumbnail'.
*/
function supreme_register_image_sizes() {
	$thumbnail_height = apply_filters('supreme_thumbnail_image_height',170);
	$thumbnail_width =  apply_filters('supreme_thumbnail_image_width',220);
	add_image_size( 'supreme-thumbnail', $thumbnail_width, $thumbnail_height, true );
	add_image_size( 'slider-thumbnail', '350', '350', true );
}
/*
 Name : supreme_embed_defaults
 Description : Overwrites the default widths for embeds.  This is especially useful for making sure videos properly expand the full width on video pages. 
*/
function supreme_embed_defaults( $args ) {
	$args['width'] = 600;
	if ( current_theme_supports( 'theme-layouts' ) ) {
		$layout = theme_layouts_get_layout();
		if ( 'layout-3c-l' == $layout || 'layout-3c-r' == $layout || 'layout-3c-c' == $layout || 'layout-hl-2c-l' == $layout || 'layout-hl-2c-r' == $layout || 'layout-hr-2c-l' == $layout || 'layout-hr-2c-r' == $layout )
		
			$args['width'] = 280;
			
		elseif ( 'layout-1c' == $layout )
		
			$args['width'] = 920;
	}
	return $args;
}
add_action('admin_init','supreme_wpup_changes',20);
function supreme_wpup_changes(){
	 remove_action( 'after_theme_row_supreme', 'wp_theme_update_row' ,10, 2 );
}
if(!function_exists('customAdmin')){
	function customAdmin() {
		require_once(get_template_directory()."/library/admin/admin-style.php");
		/* auto install for theme */
		if(strstr($_SERVER['REQUEST_URI'],'themes.php')){
			if(file_exists(get_stylesheet_directory().'/functions/auto_install/auto_install.php')){
				include_once(get_stylesheet_directory().'/functions/auto_install/auto_install.php');
			}elseif(file_exists(get_template_directory()."/functions/auto_install/auto_install.php")){
				include_once(get_template_directory().'/functions/auto_install/auto_install.php');
			}
		}
	}
}
add_action('admin_head', 'customAdmin', 11); // add admin-style.css
// Returns the portion of haystack which goes until the last occurrence of needle
if(!function_exists('reverse_strrchr')){
	function reverse_strrchr($haystack, $needle, $trail) {
		return strrpos($haystack, $needle) ? substr($haystack, 0, strrpos($haystack, $needle) + $trail) : false;
	}
}
	
/*
Name : check_if_woocommerce_active
Desc : check if woocommerce is active or not 
*/
if(!function_exists('check_if_woocommerce_active')){
	function check_if_woocommerce_active(){
		$plugins = wp_get_active_and_valid_plugins();
		$flag ='';
		foreach($plugins as $plugins){
			if (strpos($plugins,'woocommerce.php') !== false) {
				$flag = 'true';
				break;
			}else{
				 $flag = 'false';
			}
		}
		return $flag;
	}
}
/* add theme support of woocommerce */
if(function_exists('check_if_woocommerce_active')){
	$is_woo_active = check_if_woocommerce_active();
	if($is_woo_active == 'true'){
		add_theme_support( 'woocommerce' );
	}
}
/*
 * Function Name: supreme_before_title_event
 * Return: display evevnt start date.
 */
add_action('supreme_before-title_event','supreme_before_title_event');
function supreme_before_title_event(){
	global $post;	
	if($post->ID !='' && (is_author() || is_home() || is_front_page() || is_search())){
		$st_date=strtotime(get_post_meta($post->ID,'st_date',true));
		?>
<span class="date"> <?php echo date_i18n("d",$st_date); ?> <span><?php echo date_i18n("M",$st_date); ?></span> </span>
<div class="event-title">
<?php
	}	
}
/*
 * function Name: supreme_after_title_event, supreme_after_title_listing
 * return: display event and listing custom field display after title
 */
 
add_action('supreme_after-title_event','supreme_after_title_event');
add_action('supreme_after-title_listing','supreme_after_title_listing');
function supreme_after_title_listing(){
	global $post,$htmlvar_name;

	if(is_author() || is_home() || is_front_page() || is_search() || is_page() )
	{ 
		$post_id=get_the_ID();
		$post_date =  get_the_date('Y-m-d', $post_id);
		$tmpdata = get_option('templatic_settings');
		echo '<div class="author_rating">';
				if(is_plugin_active('Templatic-MultiRating/multiple_rating.php') && function_exists('get_single_average_rating_image')): ?>
<div class="event_rating_row"><span class="single_rating"> <?php echo get_single_average_rating_image($post->ID);?> </span></div>
<?php	elseif(isset($tmpdata['templatin_rating']) && $tmpdata['templatin_rating']=='yes'):?>
<div class="listing_rating">
  <div class="directory_rating_row"><span class="single_rating"> <?php echo draw_rating_star_plugin(get_post_average_rating($post_id));?> </span></div>
</div>
<?php endif;	
		
		echo "</div>";
		
		$address=get_post_meta($post->ID,'address',true);
		$phone=get_post_meta($post->ID,'phone',true);
		$time=get_post_meta($post->ID,'listing_timing',true);
		echo ($phone && $htmlvar_name['Contact Information']['phone'])? '<p class="phone">'.$phone.'</p>' : '';
		echo ($address && $htmlvar_name['[#taxonomy_name#]']['address'])? '<p class="address" >'.$address.'</p>' : '';	
		echo ($time && $htmlvar_name['[#taxonomy_name#]']['listing_timing'])? '<p class="time">'.$time.'</p>' : '';	
	}
}
function supreme_after_title_event(){
	global $post,$htmlvar_name;
	if(is_author() || is_home() || is_front_page() || is_search()){
		$address=get_post_meta($post->ID,'address',true);
		$listing_timing=get_post_meta($post->ID,'listing_timing',true);
		$phone=get_post_meta($post->ID,'phone',true);
		
		$date_formate=get_option('date_format');
		$time_formate=get_option('time_format');
		$st_date=date_i18n($date_formate,strtotime(get_post_meta($post->ID,'st_date',true)));
		$end_date=date_i18n($date_formate,strtotime(get_post_meta($post->ID,'end_date',true)));
		
		$date=$st_date.' '. __('To',THEME_DOMAIN).' '.$end_date;
		
		$st_time=date_i18n($time_formate,strtotime(get_post_meta($post->ID,'st_time',true)));
		$end_time=date_i18n($time_formate,strtotime(get_post_meta($post->ID,'end_time',true)));	
		
		echo '<div class="author_rating">';
		$post_id=get_the_ID();
		$tmpdata = get_option('templatic_settings');
		$tmpdata = get_option('templatic_settings');
		 if(is_plugin_active('Templatic-MultiRating/multiple_rating.php') && function_exists('get_single_average_rating_image')):?>
<div class="event_rating_row"><span class="single_rating"> <?php echo get_single_average_rating_image($post->ID);?> </span></div>
<?php
		elseif($tmpdata['templatin_rating']=='yes'):?>
<div class="directory_rating_row"><span class="single_rating"> <?php echo draw_rating_star_plugin(get_post_average_rating($post_id));?> </span></div>
<?php endif;
		echo "</div>";
		echo ($address && $htmlvar_name['[#taxonomy_name#]']['address'])? '<p class="address" >'.$address.'</p>' : '';
		echo ($st_date && $end_date)? '<p class="event_date"><span>'.$date.'</span></p>' : '';		
		echo ($st_time && $end_time)? '<p class="time"><span>'.$st_time.' '.__('To',THEME_DOMAIN).' '.$end_time.'</span></p>' : '';	
		
		echo '</div>';// this div generated on supreme_before_title_event function
		
	}
}
/* add_action('supreme_aftercontentevent');
add_action('supreme_aftercontentlisting'); */
function remove_home_page_feature_listing_filter()
{
	$show_on_front=get_option('show_on_front');
	if($show_on_front=='page'){
		remove_filter('pre_get_posts', 'home_page_feature_listing');
	}
}
add_filter('slider_image_thumb','slider_thumbnail');
function slider_thumbnail()
{
	return 'slider-thumbnail';
}	
/*
*function name : comment_form_defaults_comment_title
*
*description : To change the comment field title.
*/
add_filter( 'comment_form_defaults', 'comment_form_defaults_comment_title',11 );
function comment_form_defaults_comment_title( $arg ) {
	$arg['comment_field'] = '<p class="comment-form-comment"><label for="comment">'.__('Review',THEME_DOMAIN).'</label> <textarea aria-required="true" rows="8" cols="45" name="comment" id="comment"></textarea></p>';
	return $arg;
}
/*
*function name : comment_form_defaults
*
*description : to fetch fields after comment box.
*/
add_filter( 'comment_form_defaults', 'comment_form_defaults',100 );
function comment_form_defaults( $arg ) {
	global $post,$current_user;
	if(!$current_user->ID)
	{
		$fields = $arg['fields'];
		$arg['fields'] = '';
		$arg['comment_field'] .= '<div class="comment_column2">'.$fields['author'].$fields['email'].$fields['url'].'</div>';
	}
	if($post->post_type != 'post')
		$arg['label_submit'] = __('Post Review',THEME_DOMAIN);
	return $arg;
}
add_action( 'init', 'directory_register_image_sizes' );
function directory_register_image_sizes(){	
	add_image_size( 'thumbnail', 250, 165, true );
	if(get_option('thumbnail_size_w')!=250)
		update_option('thumbnail_size_w',250);
	if(get_option('thumbnail_size_h')!=165)
		update_option('thumbnail_size_h',165);
	
}
/**
 * Disables sidebars based on layout choices.
 *
 * @since 0.1
 */
function directory_disable_sidebars( $sidebars_widgets ) {	
	
	global $wpdb,$wp_query,$post;
	//fetch the current page texonomy
	$current_term = $wp_query->get_queried_object();	
	
	
	if ( current_theme_supports( 'theme-layouts' ) && !is_admin() ) {
	
		if ( 'layout-1c' == theme_layouts_get_layout() ) {
				
				$taxonomy=get_query_var( 'taxonomy' );
				if(is_tax()){
					$sidebars_widgets[$taxonomy.'_listing_sidebar'] = false;
					$sidebars_widgets[$taxonomy.'_tag_listing_sidebar'] = false;
				}
				if(is_single()){
					$sidebars_widgets[get_post_type().'_detail_sidebar'] = false;
				}
				if(is_page())
				{
					$post_type=get_post_meta($post->ID,'submit_post_type',true);
					if($post_type!='')
					{
						$sidebars_widgets['add_'.$post_type.'_submit_sidebar'] = false;
					}
				}
				if(is_home())
				{
					$sidebars_widgets['front_sidebar'] = false;
				}
			
			
		}
	}
	return $sidebars_widgets;
}
add_filter('tev_review_text','review_text_hook',11); // filter to remove space.
/* fun to remove review text */
	function review_text_hook($review){
		$review ="&nbsp;";
		return $review;
	}
	
  /*
	include font awesome css.
    */
   add_action( 'init', 'theme_css_on_init' ); // include fonts awesome
   
   function theme_css_on_init() {
       /* Register our stylesheet. */
	   if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; }
       wp_register_style( 'fontawesomecss', $http.'cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.min.css' );
	   wp_enqueue_style( 'fontawesomecss' );
   }
    /* Remove comment icon from wp-adminbar */
   function remove_comments(){
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('comments');
	}
add_action( 'wp_before_admin_bar_render', 'remove_comments' );
add_action('before_content','directory_theme_breadcrumb',11); // add breadcrumb before content
?>