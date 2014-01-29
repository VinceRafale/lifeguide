<?php
/**
 * Sets up the default framework sidebars if the theme supports them.  By default, the framework registers 
 * seven sidebars.  Themes may choose to use one or more of these sidebars.  A theme must register support 
 * for 'supreme-core-sidebars' to use them and register each sidebar ID within an array for the second 
 * parameter of add_theme_support().
 *
 * @package HybridCore
 * @subpackage Functions
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link http://themehybrid.com/supreme-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
/* Register widget areas. */
add_action( 'widgets_init', 'supreme_register_sidebars' );
/*
 Name :supreme_register_sidebars
 Description : Registers the supreme supported sidebars 
 */
function supreme_register_sidebars() {
	unregister_widget('WP_Widget_Text');
	/* Get the theme-supported sidebars. */
	$supported_sidebars = get_theme_support( 'supreme-core-sidebars' );
	/* If the theme doesn't add support for any sidebars, return. */
	if ( !is_array( $supported_sidebars[0] ) )
		return;
	/* Get the available core framework sidebars. */
	$core_sidebars = supreme_get_sidebars();
	/* Loop through the supported sidebars. */
	foreach ( $supported_sidebars[0] as $sidebar ) {
		/* Make sure the given sidebar is one of the core sidebars. */
		if ( isset( $core_sidebars[$sidebar] ) ) {
			/* Set up some default sidebar arguments. */
			$defaults = array(
				'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
				'after_widget' => 	'</div></div>',
				'before_title' => 	'<h3 class="widget-title">',
				'after_title' => 	'</h3>'
			);
			/* Parse the sidebar arguments and defaults. */
			$args = wp_parse_args( $core_sidebars[$sidebar], $defaults );
			/* If no 'id' was given, use the $sidebar variable and sanitize it. */
			$args['id'] = ( isset( $args['id'] ) ? sanitize_key( $args['id'] ) : sanitize_key( $sidebar ) );
			/* Register the sidebar. */
			
			register_sidebar($args);
		}
	}
	
	if(is_plugin_active('woocommerce/woocommerce.php')){
		$args = array(
			'name'          => __( 'WooCommerce Sidebar', THEME_DOMAIN ),
			'id'            => 'supreme_woocommerce',
			'description'   => apply_filters('supreme_woo_commerce_sidebar_description',__('This sidebar is specially for woocommerce product pages, whichever widgets you drop here will be shown in woocommerce product pages.',THEME_DOMAIN)),
			'class'         => '',
			'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>' );
		register_sidebar( $args );
	}
}
/**
 Name : supreme_get_sidebars
 Description : get the sidebar of supreme
 */
function supreme_get_sidebars() {
	/* Set up an array of sidebars. */
	global $theme_sidebars,$plugin_sidebars;
	if(empty($theme_sidebars))
	{
		$theme_sidebars = array(''); 
	}
	if(empty($plugin_sidebars))
	{
		$plugin_sidebars = array(''); 
	}
	
	$sidebars = array(
		'header' => array(
			'name' =>	apply_filters('supreme_header_right_title',_x( 'Header', 'sidebar', THEME_DOMAIN )),
			'description' =>	apply_filters('supreme_header_right_description',__( "The area is located on the right side of your header (between primary and secondary navigation).", THEME_DOMAIN )),
		),
		'secondary_navigation_right' => array(
			'name' =>	_x( 'Secondary Navigation', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Widgets placed inside this area will appear on the right side of your secondary navigation bar (the one below the logo). The simple "Search" widget works best here.', THEME_DOMAIN ),
		),
		'home-page-banner' => array(
			'name' =>	apply_filters('supreme_home_page_banner_title',_x( 'Homepage Slider', 'sidebar', THEME_DOMAIN )),
			'description' =>	__( "This area usually displays the big slider or the map. It's located between the header and the Homepage - Main Content area.", THEME_DOMAIN ),
		),	
		
		'home-page-content' => array(
			'name' =>	_x( 'Homepage - Main Content', 'sidebar', THEME_DOMAIN ),
			'description' =>	apply_filters('supreme_home_page_widget_area_description',__('This area appears alongside the homepage sidebar. It&#39;s located between the homepage slider and subsidiary areas.',THEME_DOMAIN)),
		),	
		'front-page-sidebar' => array(
		'name' =>  _x( 'Homepage Sidebar', 'sidebar', THEME_DOMAIN ),
		'description' => __( 'The area is located below the homepage slider and above subsidiary areas. It shows alongside Homepage - Main Content area.', THEME_DOMAIN )
		),
		
		
		'post-listing-sidebar' => array(
		'name' =>  _x( 'Post Category Page Sidebar', 'sidebar', THEME_DOMAIN ),
		'description' => __( 'This sidebar will show on category pages for the built-in WordPress Posts.', THEME_DOMAIN )
		),
		
		'post-detail-sidebar' => array(
		'name' =>  _x( 'Post Detail Page Sidebar', 'sidebar', THEME_DOMAIN ),
		'description' => __( 'This sidebar will show on detail (single) Post pages.', THEME_DOMAIN )
		),
		
		'after-content' => array(
			'name' =>	_x( 'All Pages - Below Content', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( "The widget area is located below the main content on all pages. For example, on detail pages you'll find it below the comments and related posts.", THEME_DOMAIN ),
		),
		'before-content' => array(
			'name' =>	_x( 'All Pages - Above Content', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( "The widget area is located above the main content on all pages. For example, on detail pages you'll find it above the title.", THEME_DOMAIN ),
		),
		'after-singular' => array(
			'name' =>	_x( 'Detail Pages - Below Content', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'The area shows below the main content on WordPress Posts and Pages.', THEME_DOMAIN ),
		),
		'primary-sidebar' => array(
			'name' => 	_x( 'Primary Sidebar', 'sidebar', THEME_DOMAIN ),
			'description' => 	__( 'This sidebar will show on pages that do not have a unique sidebar area assigned to them. It&#39;s essentially a backup sidebar..', THEME_DOMAIN )
		),
		'entry' => array(
			'name' =>	_x( 'Post Detail Page - Before Description', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Display widgets between the title and description on detail Post pages.', THEME_DOMAIN),
		),
		
		'subsidiary' => array(
			'name' => 	_x( 'Subsidiary - 1 Column', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Displays widgets in a single column on all pages. The area shows below the main content area.', THEME_DOMAIN),
		),
		'subsidiary-2c' => array(
			'name' =>	_x( 'Subsidiary - 2 Column', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Displays widgets in 2 columns on all pages. The area shows below the main content area.', THEME_DOMAIN),
		),
		
		'subsidiary-3c' => array(
			'name' =>	_x( 'Subsidiary - 3 Column', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Displays widgets in 3 columns on all pages. The area shows below the main content area.', THEME_DOMAIN),
		),
		'after-header' => array(
			'name' =>	_x( 'After Header', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'A 1-column widget area loaded after the header of the site.', THEME_DOMAIN ),
		),		
		'contact_page_widget' => array(
			'name' =>	_x( 'Contact Page - Main Content', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'The area displays widgets above the contact form on the contact page. Use the "Contact Us" template to create a contact page.', THEME_DOMAIN ),
		),
		
		'contact_page_sidebar' => array(
			'name' =>	_x( 'Contact Page Sidebar', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Display widgets inside the Contact page sidebar area.', THEME_DOMAIN ),
		),
		'author-page-sidebar' => array(
			'name' =>	_x( 'Author Page Sidebar', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'This sidebar will show on individual author pages. To visit your author page visit a URL like this one: your-domain.com/author/your-username.',THEME_DOMAIN ),
		),
		'footer' => array(
			'name' =>	_x( 'Footer', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Displays widgets below the subsidiary area.',THEME_DOMAIN ),
		),
	
	);
	
	$sidebars = array_merge($sidebars,$theme_sidebars,$plugin_sidebars);
	/* Return the sidebars. */
	
return $sidebars;
}
?>