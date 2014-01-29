<?php
/**
 * Admin functions :  used with other components of the framework admin. This file is for 
 * setting up any basic features and holding additional admin helper functions.
 */
/* Add the admin setup function to the 'admin_menu' hook. */
add_action( 'admin_menu', 'supreme_admin_setup' );
/*
Name : supreme_admin_setup
Descriptuin :  Sets up the adminstration functionality for the framework and themes.
*/
function supreme_admin_setup() {
	/* Load the post meta boxes on the new post and edit post screens. */
	add_action( 'load-post.php', 'supreme_load_post_meta_boxes' );
	add_action( 'load-post-new.php', 'supreme_load_post_meta_boxes' );
	/* Loads admin stylesheets for the framework. */
	add_action( 'admin_enqueue_scripts', 'supreme_admin_enqueue_styles' );
}
/*
Name :supreme_load_post_meta_boxes
Description: Loads the core post meta box files on the 'load-post.php' action hook.  Each meta box file is only loaded if the theme declares support for the feature.
*/
function supreme_load_post_meta_boxes() {
	/* Load the post template meta box. */
	require_if_theme_supports( 'supreme-core-template-hierarchy', trailingslashit( SUPREME_ADMIN ) . 'meta-boxes.php' );
}
/*
Name :supreme_admin_enqueue_styles
Description : Loads the admin.css stylesheet for admin-related features.
*/
function supreme_admin_enqueue_styles( $suffix ) {
	/* Load admin styles if on the widgets screen and the current theme supports 'supreme-core-widgets'. */
	if ( current_theme_supports( 'supreme-core-widgets' ) && 'widgets.php' == $suffix )
		wp_enqueue_style( 'supreme-core-admin' );
}
/*
Name :supreme_get_post_templates
Description : Function for getting an array of available custom templates with a specific header. Ideally, this function would be used to grab custom singular post (any post type) templates.  It is a recreation of the WordPress page templates function because it doesn't allow for other types of templates.
*/
function supreme_get_post_templates( $args = array() ) {
	/* Parse the arguments with the defaults. */
	$args = wp_parse_args( $args, array( 'label' => array( 'Post Template' ) ) );
	/* Get theme and templates variables. */
	$themes = wp_get_themes();
	$theme = wp_get_theme();
	@$templates = $themes[$theme]['Template Files'];
	$post_templates = array();
	/* If there's an array of templates, loop through each template. */
	if ( is_array( $templates ) ) {
		/* Set up a $base path that we'll use to remove from the file name. */
		$base = array( trailingslashit( get_template_directory() ), trailingslashit( get_stylesheet_directory() ) );
		/* Loop through the post templates. */
		foreach ( $templates as $template ) {
			/* Remove the base (parent/child theme path) from the template file name. */
			$basename = str_replace( $base, '', $template );
			/* Get the template data. */
			$template_data = implode( '', file( $template ) );
			/* Make sure the name is set to an empty string. */
			$name = '';
			/* Loop through each of the potential labels and see if a match is found. */
			foreach ( $args['label'] as $label ) {
				if ( preg_match( "|{$label}:(.*)$|mi", $template_data, $name ) ) {
					$name = _cleanup_header_comment( $name[1] );
					break;
				}
			}
			/* If a post template was found, add its name and file name to the $post_templates array. */
			if ( !empty( $name ) )
				$post_templates[trim( $name )] = $basename;
		}
	}
	/* Return array of post templates. */
	return $post_templates;
}
if ( ! function_exists( 'suprme_alternate_stylesheet' ) ) {
function suprme_alternate_stylesheet() {
	$style = '';
	echo "\n" . '<!-- Alt Stylesheet -->' . "\n";
	// If we're using the query variable, be sure to check for /css/layout.css as well.
	if ( $style != '' ) {
		if ( file_exists( get_stylesheet_uri() . '/style.css' ) ) {
			echo '<link href="' . esc_url( get_stylesheet_uri() ) . '" rel="stylesheet" type="text/css" />' . "\n";
		} else {
			echo '<link href="' . esc_url( get_template_directory_uri() . '/styles/' . $style . '.css' ) . '" rel="stylesheet" type="text/css" />' . "\n";
		}
	} 
} // End woo_output_alt_stylesheet()
}
/*=========================== Load theme customization options ===========================================*/
/* Load custom control classes. */
add_action( 'customize_register', 'supreme_customize_controls', 1 );
/* Register custom sections, settings, and controls. */
add_action( 'customize_register', 'supreme_customize_register' );
/* Add the footer content Ajax to the correct hooks. */
add_action( 'wp_ajax_supreme_customize_footer_content', 'supreme_customize_footer_content_ajax' );
add_action( 'wp_ajax_nopriv_supreme_customize_footer_content', 'supreme_customize_footer_content_ajax' );
/**
 * Registers custom sections, settings, and controls for the $wp_customize instance.
 *
 * @since 1.4.0
 * @access private
 * @param object $wp_customize
 */
function supreme_customize_register( $wp_customize ) {
	/* Get supported theme settings. */
	$supports = get_theme_support( 'supreme-core-theme-settings' );
	/* Get the theme prefix. */
	$prefix = supreme_prefix();
	/* Get the default theme settings. */
	$default_settings = supreme_default_theme_settings();
	/* Add the footer section, setting, and control if theme supports the 'footer' setting. */
	if ( is_array( $supports[0] ) && in_array( 'footer', $supports[0] ) ) {
		/* Add the footer section. */
		$wp_customize->add_section(
			'supreme-core-footer',
			array(
				'title' => 		esc_html__( 'Footer', THEME_DOMAIN ),
				'priority' => 	200,
				'capability' => 	'edit_theme_options'
			)
		);
		/* Add the 'footer_insert' setting. */
		$wp_customize->add_setting(
			"{$prefix}_theme_settings[footer_insert]",
			array(
				'label' => 		' HTML tags allow, enter whatever you want to display in footer section.',
				'default' => 		@$default_settings['footer_insert'],
				'type' => 			'option',
				'capability' => 		'edit_theme_options',
				'sanitize_callback' => 	'supreme_customize_sanitize',
				'sanitize_js_callback' => 	'supreme_customize_sanitize',
				'transport' => 		'postMessage',
			)
		);
		/* Add the textarea control for the 'footer_insert' setting. */
		$wp_customize->add_control(
			new Hybrid_Customize_Control_Textarea(
				$wp_customize,
				'supreme-core-footer',
				array(
					'label' => 	 __('Footer', THEME_DOMAIN ),
					'section' => 	'supreme-core-footer',
					'settings' => 	"{$prefix}_theme_settings[footer_insert]",
				)
			)
		);
	/* If viewing the customize preview screen, add a script to show a live preview. */
		if ( $wp_customize->is_preview() && !is_admin() )
			add_action( 'wp_footer', 'supreme_customize_preview_script', 21 );
	}
}
/**
 * Sanitizes the footer content on the customize screen.  Users with the 'unfiltered_html' cap can post 
 * anything.  For other users, wp_filter_post_kses() is ran over the setting.
 *
 * @since 1.4.0
 * @access public
 * @param mixed $setting The current setting passed to sanitize.
 * @param object $object The setting object passed via WP_Customize_Setting.
 * @return mixed $setting
 */
function supreme_customize_sanitize( $setting, $object ) {
	/* Get the theme prefix. */
	$prefix = supreme_prefix();
	/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
	if ( "{$prefix}_theme_settings[footer_insert]" == $object->id && !current_user_can( 'unfiltered_html' )  )
		$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
	/* Return the sanitized setting and apply filters. */
	return apply_filters( "{$prefix}_customize_sanitize", $setting, $object );
}
/**
 * Runs the footer content posted via Ajax through the do_shortcode() function.  This makes sure the 
 * shortcodes are output correctly in the live preview.
 *
 * @since 1.4.0
 * @access private
 */
function supreme_customize_footer_content_ajax() {
	/* Check the AJAX nonce to make sure this is a valid request. */
	check_ajax_referer( 'supreme_customize_footer_content_nonce' );
	/* If footer content has been posted, run it through the do_shortcode() function. */
	if ( isset( $_POST['footer_content'] ) )
		echo do_shortcode( wp_kses_stripslashes( $_POST['footer_content'] ) );
	/* Always die() when handling Ajax. */
	die();
}
/**
 * Handles changing settings for the live preview of the theme.
 *
 * @since 1.4.0
 * @access private
 */
function supreme_customize_preview_script() {
	/* Create a nonce for the Ajax. */
	$nonce = wp_create_nonce( 'supreme_customize_footer_content_nonce' );
	?>
<script type="text/javascript">
	wp.customize(
		'<?php echo supreme_prefix(); ?>_theme_settings[footer_insert]',
		function( value ) {
			value.bind(
				function( to ) {
					jQuery.post( 
						'<?php echo admin_url( 'admin-ajax.php' ); ?>', 
						{ 
							action: 'supreme_customize_footer_content',
							_ajax_nonce: '<?php echo $nonce; ?>',
							footer_content: to
						},
						function( response ) {
							jQuery( '.footer-content' ).html( response );
						}
					);
				}
			);
		}
	);
	</script>
<?php
}
/*
	@Theme Customizer settings for Wordpress customizer.
*/	
global $pagenow;
if(is_admin() && 'admin.php' == $pagenow){
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this section.',THEME_DOMAIN ) );
	}
}
/*	Add Action for Customizer   START	*/
	add_action( 'customize_register',  'templatic_register_customizer_settings');
/*	Add Action for Customizer   END	*/
//echo "<pre>";print_r(get_option('supreme_theme_settings'));echo "</pre>";
/*	Function to create sections, settings, controls for wordpress customizer START.  */
global $support_woocommerce;
$support_woocommerce = get_theme_support('supreme_woocommerce_layout');
/*
Name : templatic_register_customizer_settings
Description : register customizer settings option , it returns the options for theme->customizer.php
*/
function templatic_register_customizer_settings( $wp_customize ){
	global $support_woocommerce;
	//ADD SECTION FOR DIFFERENT CONTROLS IN CUSTOMIZER START
		//HEADER IMAGE SECTION SETTINGS START
		$wp_customize->get_section('header_image')->priority = 5;
		//HEADER IMAGE SECTION SETTINGS END
		//NAVIGATION MENU SECTION SETTINGS START
		$wp_customize->get_section('nav')->priority = 6;
		//NAVIGATION MENU SECTION SETTINGS END
		//COLOR SECTION SETTINGS START
		$wp_customize->get_section('colors')->title = __( 'Colors Settings' ,THEME_DOMAIN);
		$wp_customize->get_section('colors')->priority = 7;
		//COLOR SECTION SETTINGS END
		//BACKGROUND SECTION SETTINGS START
		$wp_customize->get_section('background_image')->title = __( 'Background Settings',THEME_DOMAIN );
		$wp_customize->get_section('background_image')->priority = 8;
		//BACKGROUND SECTION SETTINGS END
		//ADD SITE LOGO SECTION START
		$wp_customize->add_section('templatic_logo_settings', array(
			'title' => 'Site Logo',
			'priority'=> 9
		));
		//ADD SITE LOGO SECTION FINISH
		
		//SITE TITLE SECTION SETTINGS START
		$wp_customize->get_section('title_tagline')->priority = 10;
		//SITE TITLE SECTION SETTINGS END
		
		
		//STATIC FRONT PAGE SECTION SETTINGS START
		$wp_customize->get_section('static_front_page')->priority = 12;
		//STATIC FRONT PAGE SECTION SETTINGS END
		
		//SUPREME CORE FOOTER SECTION SETTINGS START
		$wp_customize->get_section('supreme-core-footer')->priority = 17;
		//SUPREME CORE FOOTER SECTION SETTINGS END
		
	//ADD SECTION FOR DIFFERENT CONTROLS IN CUSTOMIZER FINISH
		
	/*	Add Settings START */
		
		//ADD SETTINGS FOR SITE LOGO START
		//CALLBACK FUNCTION: templatic_customize_supreme_logo_url
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_logo_url]',array(
			'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_supreme_logo_url",
			'sanitize_js_callback' => 	"templatic_customize_supreme_logo_url",
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS FOR SITE LOGO FINISH
		
		//ADD SETTINGS FOR FAVICON ICON START
		//CALLBACK FUNCTION: templatic_customize_supreme_favicon_icon
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_favicon_icon]',array(
			'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_supreme_favicon_icon",
			'sanitize_js_callback' => 	"templatic_customize_supreme_favicon_icon",
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS FOR FAVICON ICON FINISH
		
		//ADD SETTINGS FOR HIDE/SHOW SITE DESCRIPTION START
		//CALLBACK FUNCTION: templatic_customize_supreme_site_description
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[supreme_site_description]',array(
			'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	'templatic_customize_supreme_site_description',
			'sanitize_js_callback' => 	'templatic_customize_supreme_site_description',
			
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS FOR HIDE/SHOW SITE DESCRIPTION FINISH
			
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[footer_lbl]', array(
	        'default' => '',
		));
		// ADDED CUSTOM LABEL CONTROL FINISH
		
		//COLOR SETTINGS START.
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[color_picker_color1]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_supreme_color1",
				'sanitize_js_callback' => 	"templatic_customize_supreme_color1",
				//'transport' => 'postMessage',
			));
			
			$wp_customize->add_setting(supreme_prefix().'_theme_settings[color_picker_color2]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_supreme_color2",
				'sanitize_js_callback' => 	"templatic_customize_supreme_color2",
				//'transport' => 'postMessage',
			));
			
			$wp_customize->add_setting(supreme_prefix().'_theme_settings[color_picker_color3]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_supreme_color3",
				'sanitize_js_callback' => 	"templatic_customize_supreme_color3",
				//'transport' => 'postMessage',
			));
			
			$wp_customize->add_setting(supreme_prefix().'_theme_settings[color_picker_color4]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_supreme_color4",
				'sanitize_js_callback' => 	"templatic_customize_supreme_color4",
				//'transport' => 'postMessage',
			));
			
			$wp_customize->add_setting(supreme_prefix().'_theme_settings[color_picker_color5]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_supreme_color5",
				'sanitize_js_callback' => 	"templatic_customize_supreme_color5",
				//'transport' => 'postMessage',
			));
			
			$wp_customize->add_setting(supreme_prefix().'_theme_settings[color_picker_color6]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_supreme_color6",
				'sanitize_js_callback' => 	"templatic_customize_supreme_color6",
				//'transport' => 'postMessage',
			));
			
		//COLOR SETTINGS FINISH.
		
		//TEXTURE SETTINGS START.
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[templatic_texture1]',array(
				'default' => '',
				'type' => 'option',
				'capabilities' => 'edit_theme_options',
				'sanitize_callback' => 	"templatic_customize_templatic_texture1",
				'sanitize_js_callback' => 	"templatic_customize_templatic_texture1",
				//'transport' => 'postMessage',
		));
		
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[alternate_of_texture]',array(
			'default' => '',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_alternate_of_texture",
			'sanitize_js_callback' => 	"templatic_customize_alternate_of_texture",
			//'transport' => 'postMessage',
		));
		//TEXTURE SETTINGS FINISH.
				
		//ADD SETTINGS FOR BACKGROUND HEADER IMAGE START
		//CALLBACK FUNCTION: templatic_customize_supreme_header_background_image
		$wp_customize->add_setting( 'header_image', array(
			'default'        => get_theme_support( 'custom-header', 'default-image' ),
			'theme_supports' => 'custom-header',
		) );
		
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[header_image_display]',array(
			'default' => 'after_nav',
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_header_image_display",
			'sanitize_js_callback' => 	"templatic_customize_header_image_display",
			//'transport' => 'postMessage',
		));
		//ADD SETTINGS FOR BACKGROUND HEADER IMAGE FINISH
		
		//Add settings for hide/show header text start
		$wp_customize->add_setting(supreme_prefix().'_theme_settings[display_header_text]',array(
			'default' => 1,
			'type' => 'option',
			'capabilities' => 'edit_theme_options',
			'sanitize_callback' => 	"templatic_customize_display_header_text",
			'sanitize_js_callback' => 	"templatic_customize_display_header_text",
			//'transport' => 'postMessage',
		));
		//Add settings for hide/show header text end
		
	/*	Add Settings END */
		
	/*	Add Control START */
		
		//ADDED SITE LOGO CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, supreme_prefix().'_theme_settings[supreme_logo_url]', array(
			'label' => __(' Upload image for logo',THEME_DOMAIN),
			'section' => 'templatic_logo_settings',
			'settings' => supreme_prefix().'_theme_settings[supreme_logo_url]',
		)));
		//ADDED SITE LOGO CONTROL FINISH
		
		//ADDED SITE FAVICON ICON CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, supreme_prefix().'_theme_settings[supreme_favicon_icon]', array(
			'label' => __(' Upload favicon icon',THEME_DOMAIN),
			'section' => 'templatic_logo_settings',
			'settings' => supreme_prefix().'_theme_settings[supreme_favicon_icon]',
		)));
		//ADDED SITE FAVICON ICON CONTROL FINISH
		
		//ADDED SHOW/HIDE SITE DESCRIPTION CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		//type    : Type of control you want to use
		$wp_customize->add_control( 'supreme_site_description', array(
			'label' => __('Hide Site Description',THEME_DOMAIN),
			'section' => 'title_tagline',
			'settings' => supreme_prefix().'_theme_settings[supreme_site_description]',
			'type' => 'checkbox',
			'priority' => 106
		));
		//ADDED SHOW/HIDE SITE DESCRIPTION CONTROL FINISH
		
		$wp_customize->add_control( new supreme_custom_lable_control($wp_customize, supreme_prefix().'_theme_settings[footer_lbl]', array(
			'label' => __('Footer Text ( e.g. <p class="copyright">&copy;',THEME_DOMAIN).' '.date('Y').' '.__('<a href="http://templatic.com/demos/responsive">Responsive</a>. All Rights Reserved. </p>)',THEME_DOMAIN),
			'section' => 'supreme-core-footer',
			'priority' => 1,
		)));
		
		//Color Settings Control Start
		/*
			Primary: 	 Effect on buttons, links and main headings.
			Secondary: 	 Effect on sub-headings.
			Content: 	 Effect on content.
			Sub-text: 	 Effect on sub-texts.
			Background:  Effect on body & menu background. 
		
		*/
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_picker_color1', array(
			'label'   => __( 'Change Body background color', THEME_DOMAIN ),
			'section' => 'colors',
			'settings'   => supreme_prefix().'_theme_settings[color_picker_color1]',
			'priority' => 1,
		) ) );
		
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_picker_color2', array(
			'label'   => __( 'Change Primary and Secondary navigation, Footer, background color', THEME_DOMAIN ),
			'section' => 'colors',
			'settings'   => supreme_prefix().'_theme_settings[color_picker_color2]',
			'priority' => 2,	
		) ) );
		
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_picker_color3', array(
			'label'   => __( 'Change Text color of content area', THEME_DOMAIN ),
			'section' => 'colors',
			'settings'   => supreme_prefix().'_theme_settings[color_picker_color3]',
			'priority' => 3,
		) ) );
		
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_picker_color4', array(
			'label'   => __( 'Change Categories Links, Navigation Links, Footer Links hover and Sub text of page color', THEME_DOMAIN ),
			'section' => 'colors',
			'settings'   => supreme_prefix().'_theme_settings[color_picker_color4]',
			'priority' => 4,
		) ) );
		
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_picker_color5', array(
			'label'   => __( 'Change Meta text, Breadcrumb, Pagination text and All grey color text', THEME_DOMAIN ),
			'section' => 'colors',
			'settings'   => supreme_prefix().'_theme_settings[color_picker_color5]',
			'priority' => 5,
		) ) );
		
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_picker_color6', array(
			'label'   => __( 'Change Buttons, Date and recurrences label Color', THEME_DOMAIN ),
			'section' => 'colors',
			'settings'   => supreme_prefix().'_theme_settings[color_picker_color6]',
			'priority' => 6,
		) ) );
		
		//REMOVE WORDPRESS DEFAULT CONTROL START.
		$wp_customize->remove_control('background_color');
		//REMOVE WORDPRESS DEFAULT CONTROL FINISH.
		//Color Settings Control End
		//ADD CONTROL FOR TEXTURE SETTINGS START.
		$wp_customize->add_control( new WP_Image_Control($wp_customize, supreme_prefix().'_theme_settings[templatic_texture1]', array(
			'label'   => __( 'Texture Overlays', THEME_DOMAIN ),
			'section' => 'background_image',
			'settings'   => supreme_prefix().'_theme_settings[templatic_texture1]',
		)));
		
		$wp_customize->add_control( supreme_prefix().'_theme_settings[alternate_of_texture]', array(
			'label' => __('OR Enter Your Custom Texture',THEME_DOMAIN),
			'section' => 'background_image',
			'settings' => supreme_prefix().'_theme_settings[alternate_of_texture]',
			'type' => 'text',
		));
		
		//ADD CONTROL FOR TEXTURE SETTINGS FINISH.
		//ADDED HEADER BACKGROUND IMAGE CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		$wp_customize->add_control( new WP_Customize_Header_Image_Control( $wp_customize ) );
		
		$wp_customize->add_control( supreme_prefix().'_theme_settings[header_image_display]', array(
			'label' => __('Display Header Image ( Go in Appearance -> Header to set/change the image )',THEME_DOMAIN),
			'section' => 'header_image',
			'settings' => supreme_prefix().'_theme_settings[header_image_display]',
			'type' => 'select',
			'choices' => array(
								'before_nav' 	=> 'Before Secondary Menu',	
								'after_nav' 	=> 'After Secondary Menu',	
							  ),
		));
		
		//Added display header text CONTROL START
		//ARGS USAGES
		//label   : Text which you want to display for which this control is to be used. 
		//section : In which section you want to display this control
		//settings: Define the settings to call callback function
		$wp_customize->add_control( supreme_prefix().'_theme_settings[display_header_text]', array(
			'label' => __('Display Header Text',THEME_DOMAIN),
			'section' => 'title_tagline',
			'settings' => supreme_prefix().'_theme_settings[display_header_text]',
			'type' => 'checkbox',
			'priority' => 105,
		));
		
		//ADDED HEADER BACKGROUND IMAGE CONTROL FINISH
		$wp_customize->remove_control('header_textcolor');
		$wp_customize->remove_control('display_header_text');
	/*	Add Control END */
}
/*	Function to create sections, settings, controls for wordpress customizer END.  */
/*  Handles changing settings for the live preview of the theme START.  */	
	
	function templatic_customize_supreme_logo_url( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_logo_url]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_logo_url", $setting, $object );
	}
	
	function templatic_customize_supreme_favicon_icon( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_favicon_icon]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_favicon_icon", $setting, $object );
	}
	
	function templatic_customize_supreme_site_description( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[supreme_site_description]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_site_description", $setting, $object );
	}
	function templatic_customize_supreme_color1( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[color_picker_color1]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_color1", $setting, $object );
	}
	
	function templatic_customize_supreme_color2( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[color_picker_color2]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_color2", $setting, $object );
	}
	
	function templatic_customize_supreme_color3( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[color_picker_color3]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_color3", $setting, $object );
	}
	
	function templatic_customize_supreme_color4( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[color_picker_color4]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_color4", $setting, $object );
	}
	
	function templatic_customize_supreme_color5( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[color_picker_color5]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_color5", $setting, $object );
	}
	
	function templatic_customize_supreme_color6( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[color_picker_color6]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_supreme_color6", $setting, $object );
	}
	
	
	//TEXTURE SETTINGS START.
	function templatic_customize_templatic_texture1( $setting, $object ) {
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[templatic_texture1]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_templatic_texture1", $setting, $object );
	}
	
	function templatic_customize_alternate_of_texture( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[alternate_of_texture]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_alternate_of_texture", $setting, $object );
	}
	//TEXTURE SETTINGS FINISH.
	
	//BACKGROUND HEADER IMAGE FUNCTION START
	function templatic_customize_header_image_display( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[header_image_display]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_header_image_display", $setting, $object );
	}
	//BACKGROUND HEADER IMAGE FUNCTION END
	
	//Display header text FUNCTION START
	function templatic_customize_display_header_text( $setting, $object ) {
		
		/* Make sure we kill evil scripts from users without the 'unfiltered_html' cap. */
		if ( supreme_prefix()."_theme_settings[display_header_text]" == $object->id && !current_user_can( 'unfiltered_html' )  )
			$setting = stripslashes( wp_filter_post_kses( addslashes( $setting ) ) );
		/* Return the sanitized setting and apply filters. */
		return apply_filters( "templatic_customize_display_header_text", $setting, $object );
	}
	//Display header text FUNCTION END
	
/*  Handles changing settings for the live preview of the theme END.  */	
/**
 * Loads framework-specific customize control classes.  Customize control classes extend the WordPress 
 * WP_Customize_Control class to create unique classes that can be used within the framework.
 */
function supreme_customize_controls() {
	 /*
	 * Custom label customize control class.
	 */
	if(class_exists('WP_Customize_Control')){
		class supreme_custom_lable_control extends WP_Customize_Control{
			  public function render_content(){
	?>
<label> <span><?php echo esc_html( $this->label ); ?></span> </label>
<?php
			 }
		}
	}
	/**
	 * Textarea customize control class.
	 */
	if(class_exists('WP_Customize_Control')){
		class Hybrid_Customize_Control_Textarea extends WP_Customize_Control {
			public $type = 'textarea';
			public function __construct( $manager, $id, $args = array() ) {
				parent::__construct( $manager, $id, $args );
			}
			public function render_content() { ?>
<label>
<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
<div class="customize-control-content">
  <textarea cols="25" rows="5" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
</div>
</label>
<?php }
		}
	}
	//CREATE CUSTOM TEXTURE CONTROL START.
	if(!class_exists('WP_Image_Control')){
		class WP_Image_Control extends WP_Customize_Control{
			public function render_content(){
				$name = '_customize-radio-' . $this->id;?>
<style type="text/css">
                    	.texture_wrap {
							margin-left: -5px;
							}
							
						.texture_wrap label {
							display: inline-block;
							*display: inline;
							zoom: 1;
							vertical-align: top;
							position: relative;
							width: 32px;
							height: 32px;
							border: 1px solid #ccc;
							color: #fff;
							margin: 0 0 7px 4px;
							}
							
						.texture_wrap label input[type='radio']{
							position: absolute;
							visibility: hidden;
							}
                    </style>
<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
<div class="texture_wrap">
  <label>
    <input type="radio" value="" name="templatic_texture" <?php $this->link(); checked( $this->value(), '' ); ?> />
    <span id="texture1">
    <?php _e('None',THEME_DOMAIN);?>
    </span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture2.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture2.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture2.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture3.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture3.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture3.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture4.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture4.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture4.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture5.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture5.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture5.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture6.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture6.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture6.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture7.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture7.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture7.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture8.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture8.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture8.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture9.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture9.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture9.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture10.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture10.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture10.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture11.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture11.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture11.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture12.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture12.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture12.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture13.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture13.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture13.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture14.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture14.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture14.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture15.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture15.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture15.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture16.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture16.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture16.png'; ?>" alt="" /></span> </label>
  <label>
    <input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture17.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture17.png' ); ?> />
    <span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture17.png'; ?>" alt="" /></span> </label>
  <label>
		<input type="radio" value="<?php echo get_template_directory_uri().'/images/texture/tts_texture18.png'; ?>" name="templatic_texture" <?php $this->link(); checked( $this->value(), get_template_directory_uri().'/images/texture/tts_texture18.png' ); ?> />
		<span id="texture1"><img src="<?php echo get_template_directory_uri().'/images/texture/icon_texture18.png'; ?>" alt="" /></span> </label>	
</div>
<?php
			}
		}
	}
	//CREATE CUSTOM TEXTURE CONTROL FINISH.
}
/*
Name :get_header_image_location
Description : to display header image
*/
if(!function_exists('get_header_image_location')){
	function get_header_image_location(){
		$theme_name = get_option('stylesheet');
		$theme_settings = get_option(supreme_prefix().'_theme_settings');
		if(!empty($theme_settings)){
			if(isset($theme_settings['header_image_display']) && @$theme_settings['header_image_display']!="" && @$theme_settings['header_image_display'] == 'before_nav'){
				return 0;
			}elseif(isset($theme_settings['header_image_display']) && @$theme_settings['header_image_display']!="" && @$theme_settings['header_image_display'] == 'after_nav'){
				return 1;
			}
		}
	}
}
?>