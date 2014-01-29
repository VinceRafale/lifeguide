<?php 
/* Craete child themes widget area */
global $theme_sidebars;
$theme_sidebars = array(''); 
	$theme_sidebars = array(
		'two_column_content' => array(
			'name' =>	apply_filters('supreme_two_column_content',_x( '2 Column Content - Home Page', 'sidebar', THEME_DOMAIN )),
			'description' =>	apply_filters('supreme_two_column_content',__( "Display widgets in two column on home page.", THEME_DOMAIN )),
		),
		'footer_left' => array(
			'name' =>	_x( 'Footer Left', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Display widgets in footer left.',THEME_DOMAIN ),
		),
		'footer_right' => array(
			'name' =>	_x( 'Footer Right', 'sidebar', THEME_DOMAIN ),
			'description' =>	__( 'Display widgets in footer right.',THEME_DOMAIN ),
		));
?>