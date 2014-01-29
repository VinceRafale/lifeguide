<?php
//Theme Setting Options file
if(file_exists(get_template_directory().'/library/functions/theme_options.php')){
	require_once(get_template_directory().'/library/functions/theme_options.php'); // framework theme setting options file
}
/* @This file contain the framework custom functions */
if ( ! function_exists( 'supreme_entry_meta' ) ) :
/**
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own supreme_entry_meta() to override in a child theme.
 *
 * @since Twenty Twelve 1.0
 */
function supreme_entry_meta() {
	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', THEME_DOMAIN ) );
	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', THEME_DOMAIN ) );
	$date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
	/*$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', THEME_DOMAIN ), get_the_author() ) ),
		get_the_author()
	);*/
	// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
	if ( $tag_list ) {
		$utility_text = '<div class="post_info_meta">'.__( 'Posted in %1$s and tagged %2$s.', THEME_DOMAIN ).'</div>';
	} elseif ( $categories_list ) {
		$utility_text = '<div class="post_info_meta">'.__( 'Posted in %1$s.', THEME_DOMAIN ).'</div>';
	} 
	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		$date,
		$author
	);
}
endif;
/* Add extra support for post types. */
add_action( 'init', 'supreme_add_post_type_support' );
/* Add extra file headers for themes. */
add_filter( 'extra_theme_headers', 'supreme_extra_theme_headers' );
/**
 * This function is for adding extra support for features not default to the core post types.
 * Excerpts are added to the 'page' post type.  Comments and trackbacks are added for the
 * 'attachment' post type.  Technically, these are already used for attachments in core, but 
 * they're not registered.
 */
function supreme_add_post_type_support() {
	/* Add support for excerpts to the 'page' post type. */
	add_post_type_support( 'page', array( 'excerpt' ) );
	/* Add support for trackbacks to the 'attachment' post type. */
	add_post_type_support( 'attachment', array( 'trackbacks' ) );
}
/**
 * Creates custom theme headers.  This is the information shown in the header block of a theme's 'style.css' 
 * file.  Themes are not required to use this information, but the framework does make use of the data for 
 * displaying additional information to the theme user.
 *
 */
function supreme_extra_theme_headers( $headers ) {
	/* Add support for 'Template Version'. This is for use in child themes to note the version of the parent theme. */
	if ( !in_array( 'Template Version', $headers ) )
		$headers[] = 'Template Version';
	/* Add support for 'License'.  Proposed in the guidelines for the WordPress.org theme review. */
	if ( !in_array( 'License', $headers ) )
		$headers[] = 'License';
	/* Add support for 'License URI'. Proposed in the guidelines for the WordPress.org theme review. */
	if ( !in_array( 'License URI', $headers ) )
		$headers[] = 'License URI';
	/* Add support for 'Support URI'.  This should be a link to the theme's support forums. */
	if ( !in_array( 'Support URI', $headers ) )
		$headers[] = 'Support URI';
	/* Add support for 'Documentation URI'.  This should be a link to the theme's documentation. */
	if ( !in_array( 'Documentation URI', $headers ) )
		$headers[] = 'Documentation URI';
	/* Return the array of custom theme headers. */
	return $headers;
}
/**
 * Looks for a template based on the supreme_get_context() function.  If the $template parameter
 * is a directory, it will look for files within that directory.  Otherwise, $template becomes the 
 * template name prefix.  The function looks for templates based on the context of the current page
 * being viewed by the user.
 *
 */
function get_atomic_template( $template ) {
	$templates = array();
	$theme_dir = trailingslashit( THEME_DIR ) . $template;
	$child_dir = trailingslashit( CHILD_THEME_DIR ) . $template;
	if ( is_dir( $child_dir ) || is_dir( $theme_dir ) ) {
		$dir = true;
		$templates[] = "{$template}/index.php";
	}
	else {
		$dir = false;
		$templates[] = "{$template}.php";
	}
	foreach ( supreme_get_context() as $context )
		$templates[] = ( ( $dir ) ? "{$template}/{$context}.php" : "{$template}-{$context}.php" );
	return locate_template( array_reverse( $templates ), true );
}
/**
 * Generates the relevant template info.  Adds template meta with theme version.  Uses the theme 
 * name and version from style.css.  In 0.6, added the supreme_meta_template 
 * filter hook.
 *
 */
function supreme_meta_template() {
	$theme = wp_get_theme( get_template(), get_theme_root( get_template_directory() ) );
	
	$template = '<meta name="template" content="' . esc_attr( $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ) ) . '" />' . "\n";
	
	echo apply_atomic( 'meta_template', $template );
}
/**
 * Dynamic element to wrap the site title in.  If it is the front page, wrap it in an <h1> element.  One other 
 * pages, wrap it in a <div> element. 
 *
 */
function supreme_site_title() {
	/* If viewing the front page of the site, use an <h1> tag.  Otherwise, use a <div> tag. */
	$tag = 'h1';
	/* Get the site title.  If it's not empty, wrap it with the appropriate HTML. */
	if ( $title = get_bloginfo( 'name' ) ){
		if(function_exists('icl_register_string')){
			icl_register_string(THEME_DOMAIN,$title,$title);
			$title1 = icl_t(THEME_DOMAIN,$title,$title);
		}else{
			$title1 = $title;
		}
		$title = sprintf( '<%1$s id="site-title"><a href="%2$s" title="%3$s" rel="home"><span>%4$s</span></a></%1$s>', tag_escape( $tag ), home_url(), esc_attr( $title1 ), $title1 );
	}	
	/* Display the site title and apply filters for developers to overwrite. */
	echo apply_atomic( 'site_title', $title );
}
/**
 * Dynamic element to wrap the site description in.  If it is the front page, wrap it in an <h2> element.  
 * On other pages, wrap it in a <div> element.
 */
function supreme_site_description() {
	/* If viewing the front page of the site, use an <h2> tag.  Otherwise, use a <div> tag. */
	$tag = ( is_front_page() ) ? 'h2' : 'div';
	$tmpdata = get_option(supreme_prefix().'_theme_settings');
	/* Get the site description.  If it's not empty, wrap it with the appropriate HTML. */
	if ( $desc = get_bloginfo( 'description' ) ){
		if(function_exists('icl_register_string')){
			icl_register_string(THEME_DOMAIN,$desc,$desc);
			$desc1 = icl_t(THEME_DOMAIN,$desc,$desc);
		}else{
			$desc1 = $desc;
		}
		$desc = sprintf( '<%1$s id="site-description"><span>%2$s</span></%1$s>', tag_escape( $tag ), $desc1 );
	}	
		/* Display the site description and apply filters for developers to overwrite. */
		echo apply_atomic( 'site_description', $desc );
}
/**
 * Checks if a post of any post type has a custom template.  This is the equivalent of WordPress' 
 * is_page_template() function with the exception that it works for all post types.
 *
 */
function supreme_has_post_template( $template = '' ) {
	/* Assume we're viewing a singular post. */
	if ( is_singular() ) {
		/* Get the queried object. */
		$post = get_queried_object();
		/* Get the post template, which is saved as metadata. */
		$post_template = get_post_meta( get_queried_object_id(), "_wp_{$post->post_type}_template", true );
		/* If a specific template was input, check that the post template matches. */
		if ( !empty( $template) && ( $template == $post_template ) )
			return true;
		/* If no specific template was input, check if the post has a template. */
		elseif ( empty( $template) && !empty( $post_template ) )
			return true;
	}
	/* Return false for everything else. */
	return false;
}
/**
 * Defines the theme prefix. This allows developers to infinitely change the theme. In theory,
 * one could use the Hybrid core to create their own theme or filter 'hybrid_prefix' with a 
 * plugin to make it easier to use hooks across multiple themes without having to figure out
 * each theme's hooks (assuming other themes used the same system).
 *
 * @since 0.7.0
 * @access public
 * @uses get_template() Defines the theme prefix based on the theme directory.
 * @global object $supreme The global Hybrid object.
 * @return string $supreme->prefix The prefix of the theme.
 */
function supreme_prefix() {
	global $supreme;
	$supreme->prefix = sanitize_key( apply_filters( 'hybrid_prefix', get_template() ) );
	return $supreme->prefix;
}
/**
 * Adds contextual filter hooks to the theme.  This allows users to easily filter context-based content 
 * without having to know how to use WordPress conditional tags.  The theme handles the logic.
 *
 * An example of a basic hook would be 'hybrid_entry_meta'.  The apply_atomic() function extends 
 * that to give extra hooks such as 'hybrid_singular_entry_meta', 'hybrid_singular-post_entry_meta', 
 * and 'hybrid_singular-post-ID_entry_meta'.
 *
 * @since 0.7.0
 * @access public
 * @uses supreme_prefix() Gets the theme prefix.
 * @uses supreme_get_context() Gets the context of the current page.
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed $value The value on which the filters hooked to $tag are applied on.
 * @param mixed $var,... Additional variables passed to the functions hooked to $tag.
 * @return mixed $value The value after it has been filtered.
 */
function apply_atomic( $tag = '', $value = '' ) {
	if ( empty( $tag ) )
		return false;
	/* Get theme prefix. */
	$pre = supreme_prefix();
	/* Get the args passed into the function and remove $tag. */
	$args = func_get_args();
	array_splice( $args, 0, 1 );
	/* Apply filters on the basic hook. */
	$value = $args[0] = apply_filters_ref_array( "{$pre}_{$tag}", $args );
	/* Loop through context array and apply filters on a contextual scale. */
	foreach ( supreme_get_context() as $context )
		$value = $args[0] = apply_filters_ref_array( "{$pre}_{$context}_{$tag}", $args );
	/* Return the final value once all filters have been applied. */
	return $value;
}
/**
 * Wraps the output of apply_atomic() in a call to do_shortcode(). This allows developers to use 
 * context-aware functionality alongside shortcodes. Rather than adding a lot of code to the 
 * function itself, developers can create individual functions to handle shortcodes.
 *
 * @since 0.7.0
 * @access public
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed $value The value to be filtered.
 * @return mixed $value The value after it has been filtered.
 */
function apply_atomic_shortcode( $tag = '', $value = '' ) {
	return do_shortcode( apply_atomic( $tag, $value ) );
}
/**
 * The theme can save multiple things in a transient to help speed up page load times. We're
 * setting a default of 12 hours or 43,200 seconds (60 * 60 * 12).
 *
 * @since 0.8.0
 * @access public
 * @return int Transient expiration time in seconds.
 */
function hybrid_get_transient_expiration() {
	return apply_filters( supreme_prefix() . '_transient_expiration', 43200 );
}
/**
 * Function for setting the content width of a theme.  This does not check if a content width has been set; it 
 * simply overwrites whatever the content width is.
 *
 * @since 1.2.0
 * @access public
 * @global int $content_width The width for the theme's content area.
 * @param int $width Numeric value of the width to set.
 */
function supreme_set_content_width( $width = '' ) {
	global $content_width;
	$content_width = absint( $width );
}
/**
 * Loads the Hybrid theme settings once and allows the input of the specific field the user would 
 * like to show.  Hybrid theme settings are added with 'autoload' set to 'yes', so the settings are 
 * only loaded once on each page load.
 *
 * @since 0.7.0
 * @access public
 * @uses get_option() Gets an option from the database.
 * @uses supreme_prefix() Gets the prefix of the theme.
 * @global object $supreme The global Hybrid object.
 * @param string $option The specific theme setting the user wants.
 * @return mixed $settings[$option] Specific setting asked for.
 */
function supreme_get_settings( $option = '' ) {
	//return if option is not passed
	if ( !$option )
		return false;
	
	//Check for theme prefix
	if(function_exists('supreme_prefix')){
		$pref = supreme_prefix();
	}else{
		$pref = get_template();
	}
	//Getting theme options array
	$theme_settings = get_option($pref.'_theme_settings');	
	//return value of requested theme option
	
	if(isset($theme_settings[$option])):
		return $theme_settings[$option];
	else:
		return '';
	endif;
}
/**
 * Loads the Hybrid theme settings once and allows the input of the specific field the user would 
 * like to show.  Hybrid theme settings are added with 'autoload' set to 'yes', so the settings are 
 * only loaded once on each page load.
 *
 * @since 0.7.0
 * @access public
 * @uses get_option() Gets an option from the database.
 * @uses supreme_prefix() Gets the prefix of the theme.
 * @global object $supreme The global Hybrid object.
 * @param string $option The specific theme setting the user wants.
 * @return mixed $settings[$option] Specific setting asked for.
 */
if(!function_exists('theme_get_settings')){
	function theme_get_settings($option=''){
		global $supreme;
		/* If no specific option was requested, return false. */
		if(!$option){return false;}
		/* Call get_option() to get an array of theme settings. */
		$theme_options = get_option(supreme_prefix().'_theme_settings');
		/* If the settings isn't an array or the specific option isn't in the array, return false. */
		if( !is_array( $theme_options ) || empty($theme_options)){return false;}
		/* If the specific option is an array, return it. */
		if ( is_array( @$theme_options[$option] ) ){
			return $theme_options[$option];
		/* Strip slashes from the setting and return. */
		}else{
			return wp_kses_stripslashes( @$theme_options[$option] );
		}	
	}
}
/**
Name :supreme_default_theme_settings
Description : get supreme default theme settings
 */
function supreme_default_theme_settings() {
	/* Set up some default variables. */
	$settings = array();
	$prefix = supreme_prefix();
	/* Get theme-supported meta boxes for the settings page. */
	$supports = get_theme_support( 'supreme-core-theme-settings' );
	/* If the current theme supports the footer meta box and shortcodes, add default footer settings. */
	if ( is_array( $supports[0] ) && in_array( 'footer', $supports[0] ) && current_theme_supports( 'supreme-core-shortcodes' ) ) {
		/* If there is a child theme active, add the [child-link] shortcode to the $footer_insert. */
		if ( is_child_theme() )
			$settings['footer_insert'] = '<p class="copyright">' . __( 'Copyright &#169; [the-year] [site-link].', THEME_DOMAIN ) . '</p>' . "\n\n" . '<p class="credit">' . __( 'Powered by [wp-link], [theme-link], and [child-link].', THEME_DOMAIN ) . '</p>';
		/* If no child theme is active, leave out the [child-link] shortcode. */
		else
			$settings['footer_insert'] = '<p class="copyright">' . __( 'Copyright &#169; [the-year] [site-link].', THEME_DOMAIN ) . '</p>' . "\n\n" . '<p class="credit">' . __( 'Powered by [wp-link] and [theme-link].', THEME_DOMAIN ) . '</p>';
	}
	/* Return the $settings array and provide a hook for overwriting the default settings. */
	return apply_filters( "{$prefix}_default_theme_settings", $settings );
}
/**
Name : supreme_get_post_categories
Args : label
Description : Return the categories of post
**/
function supreme_get_categories($label,$taxonomy,$class,$tags_label,$tag_taxonomy){
	$label = $label;
	$tags_label = $tags_label;
	
	if(function_exists('icl_register_string')){
		icl_register_string(THEME_DOMAIN,$label,$label);
	}
	
	if(function_exists('icl_t')){
		$label1 = icl_t(THEME_DOMAIN,$label,$label);
	}else{
		$label1 = $label; 
	}
	if(function_exists('icl_register_string')){
		icl_register_string(THEME_DOMAIN,$tags_label,$tags_label);
	}
	
	if(function_exists('icl_t')){
		$tags_label1 = icl_t(THEME_DOMAIN,$tags_label,$tags_label);
	}else{
		$tags_label1 = $tags_label; 
	}
	echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta '.$class.'">' .' '. sprintf(__( '[entry-terms taxonomy="%s" before="%s"] [entry-terms taxonomy="%s" before="%s"]', THEME_DOMAIN ),$taxonomy,$label1,$tag_taxonomy,$tags_label1) . '</div>' );
}
/**
Name : supreme_get_post_categories
Description : Displays the author's avatar and biography. This is typically shown on singular view pages only.
**/
function supreme_author_biography_($post){
	global $post;
	$theme_options = get_option(supreme_prefix().'_theme_settings');
	$supreme_author_bio_pages = @$theme_options['supreme_author_bio_pages'];
	 if(is_page() && $supreme_author_bio_pages){?>
<div class="entry-author-meta"> <a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>" title="<?php echo esc_attr( get_the_author_meta( 'display_name' ) ); ?>" class="avatar-frame"><?php  if(get_user_meta(get_the_author_meta( 'ID' ),'profile_photo',true) != ""){
							echo '<img class="avatar" src="'.get_user_meta(get_the_author_meta( 'ID' ),'profile_photo',true).'" alt="'.esc_attr( get_the_author_meta( 'display_name' ) ).'" title="'.esc_attr( get_the_author_meta( 'display_name' ) ).'" width="60" height="60"/>';
						}else{ echo get_avatar(get_the_author_meta('ID'), '60', '', ''); }?></a>
  <p class="author-name">
    <?php  do_action('entry-author');  ?>
  </p>
  <p class="author-description">
    <?php the_author_meta('description'); ?>
  </p>
</div>
<!-- .entry-author -->
<?php }	
		$theme_options = get_option(supreme_prefix().'_theme_settings');
		$supreme_author_bio_posts = @$theme_options['supreme_author_bio_posts'];
	if(is_single() && $supreme_author_bio_posts){ ?>
<div class="entry-author-meta"> <a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>" title="<?php echo esc_attr( get_the_author_meta( 'display_name' ) ); ?>" class="avatar-frame"><?php if(get_user_meta(get_the_author_meta( 'ID' ),'profile_photo',true) != ""){
							echo '<img class="avatar" src="'.get_user_meta(get_the_author_meta( 'ID' ),'profile_photo',true).'" alt="'.esc_attr( get_the_author_meta( 'display_name' ) ).'" title="'.esc_attr( get_the_author_meta( 'display_name' ) ).'" width="60" height="60"/>';
						}else{ echo get_avatar(get_the_author_meta('ID'), '60', '', ''); }?></a>
  <p class="author-name">
    <?php do_action('entry-author'); ?>
  </p>
  <p class="author-description">
    <?php the_author_meta('description'); ?>
  </p>
</div>
<!-- .entry-author -->
<?php }
}
/* Functions file for loading scripts and stylesheets.  This file also handles the output of attachment files  */
add_action( 'wp_enqueue_scripts', 'supreme_register_scripts', 1 ); /* Register Supreme Core scripts. */
add_action( 'wp_enqueue_scripts', 'supreme_enqueue_scripts' );/* Load Supreme Core scripts. */
add_filter( 'stylesheet_uri', 'supreme_debug_stylesheet', 10, 2 ); /* Load the development stylsheet in script debug mode. */
/* Add all image sizes to the image editor to insert into post. */
add_filter( 'image_size_names_choose', 'hybrid_image_size_names_choose' );
/**
 * Registers JavaScript files for the framework.  This function merely registers scripts with WordPress using
 * the wp_register_script() function.   */
function supreme_register_scripts() {
	/* Supported JavaScript. */
	$supports = get_theme_support( 'supreme-core-javascript' );
}
/**
 * Tells WordPress to load the scripts needed for the framework using the wp_enqueue_script() function.
 */
function supreme_enqueue_scripts() {
	/* Supported JavaScript. */
	$supports = get_theme_support( 'supreme-core-javascript' );
	/* Load the comment reply script on singular posts with open comments if threaded comments are supported. */
	if ( is_singular() && get_option( 'thread_comments' ) && comments_open() )
		wp_enqueue_script( 'comment-reply' );
}
/**
 * Function for using a debug stylesheet when developing.  To develop with the debug stylesheet, 
 * SCRIPT_DEBUG must be set to 'true' in the 'wp-config.php' file.  This will check if a 'style.dev.css'
 * file is present within the theme folder and use it if it exists.  Else, it defaults to 'style.css'.
 */
function supreme_debug_stylesheet( $stylesheet_uri, $stylesheet_dir_uri ) {
	/* If SCRIPT_DEBUG is set to true and the theme supports 'dev-stylesheet'. */
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && current_theme_supports( 'dev-stylesheet' ) ) {
		/* Remove the stylesheet directory URI from the file name. */
		$stylesheet = str_replace( trailingslashit( $stylesheet_dir_uri ), '', $stylesheet_uri );
		/* Change the stylesheet name to 'style.dev.css'. */
		$stylesheet = str_replace( '.css', '.dev.css', $stylesheet );
		/* If the stylesheet exists in the stylesheet directory, set the stylesheet URI to the dev stylesheet. */
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $stylesheet ) )
			$stylesheet_uri = trailingslashit( $stylesheet_dir_uri ) . $stylesheet;
	}
	/* Return the theme stylesheet. */
	return $stylesheet_uri;
}
/**
 * Adds theme/plugin custom images sizes added with add_image_size() to the image uploader/editor.  This 
 * allows users to insert these images within their post content editor.
 *
 * @since 1.3.0
 * @access private
 * @param array $sizes Selectable image sizes.
 * @return array $sizes
 */
function hybrid_image_size_names_choose( $sizes ) {
	/* Get all intermediate image sizes. */
	$intermediate_sizes = get_intermediate_image_sizes();
	$add_sizes = array();
	/* Loop through each of the intermediate sizes, adding them to the $add_sizes array. */
	foreach ( $intermediate_sizes as $size )
		$add_sizes[$size] = $size;
	/* Merge the original array, keeping it intact, with the new array of image sizes. */
	$sizes = array_merge( $add_sizes, $sizes );
	/* Return the new sizes plus the old sizes back. */
	return $sizes;
}
/**
 * Loads the correct function for handling attachments.  Checks the attachment mime type to call 
 * correct function. Image attachments are not loaded with this function.  The functionality for them 
 * should be handled by the theme's attachment or image attachment file.
 *
 * Ideally, all attachments would be appropriately handled within their templates. However, this could 
 * lead to messy template files.
 *
 * @since 0.5.0
 * @access public
 * @uses get_post_mime_type() Gets the mime type of the attachment.
 * @uses wp_get_attachment_url() Gets the URL of the attachment file.
 * @return void
 */
function hybrid_attachment() {
	$file = wp_get_attachment_url();
	$mime = get_post_mime_type();
	$mime_type = explode( '/', $mime );
	/* Loop through each mime type. If a function exists for it, call it. Allow users to filter the display. */
	foreach ( $mime_type as $type ) {
		if ( function_exists( "hybrid_{$type}_attachment" ) )
			$attachment = call_user_func( "hybrid_{$type}_attachment", $mime, $file );
		$attachment = apply_atomic( "{$type}_attachment", $attachment );
	}
	echo apply_atomic( 'attachment', $attachment );
}
/**
 * Handles application attachments on their attachment pages.  Uses the <object> tag to embed media 
 * on those pages.
 *
 * @since 0.3.0
 * @access public
 * @param string $mime attachment mime type
 * @param string $file attachment file URL
 * @return string
 */
function hybrid_application_attachment( $mime = '', $file = '' ) {
	$embed_defaults = wp_embed_defaults();
	$application = '<object class="text" type="' . esc_attr( $mime ) . '" data="' . esc_url( $file ) . '" width="' . esc_attr( $embed_defaults['width'] ) . '" height="' . esc_attr( $embed_defaults['height'] ) . '">';
	$application .= '<param name="src" value="' . esc_url( $file ) . '" />';
	$application .= '</object>';
	return $application;
}
/**
 * Handles text attachments on their attachment pages.  Uses the <object> element to embed media 
 * in the pages.
 *
 * @since 0.3.0
 * @access public
 * @param string $mime attachment mime type
 * @param string $file attachment file URL
 * @return string
 */
function hybrid_text_attachment( $mime = '', $file = '' ) {
	$embed_defaults = wp_embed_defaults();
	$text = '<object class="text" type="' . esc_attr( $mime ) . '" data="' . esc_url( $file ) . '" width="' . esc_attr( $embed_defaults['width'] ) . '" height="' . esc_attr( $embed_defaults['height'] ) . '">';
	$text .= '<param name="src" value="' . esc_url( $file ) . '" />';
	$text .= '</object>';
	return $text;
}
/**
 * Handles audio attachments on their attachment pages.  Puts audio/mpeg and audio/wma files into 
 * an <object> element.
 *
 * @todo Test out and support more audio types.
 *
 * @since 0.2.2
 * @access public
 * @param string $mime attachment mime type
 * @param string $file attachment file URL
 * @return string
 */
function hybrid_audio_attachment( $mime = '', $file = '' ) {
	$embed_defaults = wp_embed_defaults();
	$audio = '<object type="' . esc_attr( $mime ) . '" class="player audio" data="' . esc_url( $file ) . '" width="' . esc_attr( $embed_defaults['width'] ) . '" height="' . esc_attr( $embed_defaults['height'] ) . '">';
		$audio .= '<param name="src" value="' . esc_url( $file ) . '" />';
		$audio .= '<param name="autostart" value="false" />';
		$audio .= '<param name="controller" value="true" />';
	$audio .= '</object>';
	return $audio;
}
/**
 * Handles video attachments on attachment pages.  Add other video types to the <object> element.
 *
 * @since 0.2.2
 * @access public
 * @param string $mime attachment mime type
 * @param string $file attachment file URL
 * @return string
 */
function hybrid_video_attachment( $mime = false, $file = false ) {
	$embed_defaults = wp_embed_defaults();
	if ( $mime == 'video/asf' )
		$mime = 'video/x-ms-wmv';
	$video = '<object type="' . esc_attr( $mime ) . '" class="player video" data="' . esc_url( $file ) . '" width="' . esc_attr( $embed_defaults['width'] ) . '" height="' . esc_attr( $embed_defaults['height'] ) . '">';
		$video .= '<param name="src" value="' . esc_url( $file ) . '" />';
		$video .= '<param name="autoplay" value="false" />';
		$video .= '<param name="allowfullscreen" value="true" />';
		$video .= '<param name="controller" value="true" />';
	$video .= '</object>';
	return $video;
}
/*
Name : loop_pagination
Description : return pagination
 * @package   LoopPagination
 * @version   0.1.6
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2012, Justin Tadlock
 * @link      http://themehybrid.com/docs/tutorials/loop-pagination
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
function loop_pagination( $args = array() ) {
	global $wp_rewrite, $wp_query;
	/* If there's not more than one page, return nothing. */
	if ( 1 >= $wp_query->max_num_pages )
		return;
	/* Get the current page. */
	$current = ( get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1 );
	/* Get the max number of pages. */
	$max_num_pages = intval( $wp_query->max_num_pages );
	/* Set up some default arguments for the paginate_links() function. */
	$defaults = array(
		'base'         => add_query_arg( 'paged', '%#%' ),
		'format'       => '',
		'total'        => $max_num_pages,
		'current'      => $current,
		'prev_next'    => true,
		//'prev_text'  => __( '&laquo; Previous' ), // This is the WordPress default.
		//'next_text'  => __( 'Next &raquo;' ), // This is the WordPress default.
		'show_all'     => false,
		'end_size'     => 1,
		'mid_size'     => 1,
		'add_fragment' => '',
		'type'         => 'plain',
		// Begin loop_pagination() arguments.
		'before'       => '<div class="pagination loop-pagination">',
		'after'        => '</div>',
		'echo'         => true,
	);
	/* Add the $base argument to the array if the user is using permalinks. */
	if ( $wp_rewrite->using_permalinks() && !is_search() )
		$defaults['base'] = user_trailingslashit( trailingslashit( get_pagenum_link() ) . 'page/%#%' );
	/* Allow developers to overwrite the arguments with a filter. */
	$args = apply_filters( 'loop_pagination_args', $args );
	/* Merge the arguments input with the defaults. */
	$args = wp_parse_args( $args, $defaults );
	/* Don't allow the user to set this to an array. */
	if ( 'array' == $args['type'] )
		$args['type'] = 'plain';
	/* Get the paginated links. */
	$page_links = paginate_links( $args );
	/* Remove 'page/1' from the entire output since it's not needed. */
	$page_links = str_replace( array( '&#038;paged=1\'', '/page/1\'', '/page/1/\'' ), '\'', $page_links );
	/* Wrap the paginated links with the $before and $after elements. */
	$page_links = $args['before'] . $page_links . $args['after'];
	/* Allow devs to completely overwrite the output. */
	$page_links = apply_filters( 'loop_pagination', $page_links );
	/* Return the paginated links for use in themes. */
	if ( $args['echo'] )
		echo $page_links;
	else
		return $page_links;
}
/*
Name :supreme_get_post_taxonomies
*/
function supreme_get_post_taxonomies($post) {
    $post_type  = get_post_type($post);
    $taxonomies = get_object_taxonomies($post_type, 'objects');
	$key_ ='';
	foreach($taxonomies as $key=>$var){
		$key_ .= $key.",";	
	}
	return explode(',',$key_);
}
/*
 * Function Name:the_content_limit
 * Return : Display the limited content
 */
if(!function_exists('the_content_limit')){
	function the_content_limit($max_char, $more_link_text = '', $stripteaser = true, $more_file = '') {	
		global $post;
		$more_link_text = ($more_link_text!='')? $more_link_text: __('Read More &raquo;',THEME_DOMAIN);
		$content = get_the_content();
		$content = strip_tags($content);
		$content = substr($content, 0, $max_char);
		$content = substr($content, 0, strrpos($content, " "));
		$more_link_text='<a href="'.get_permalink().'">'.$more_link_text.'</a>';
		$content = $content." ".$more_link_text;
		echo $content;	
	}
}
/*
 * Function Name:listing_post_title_before_image
 * Return : Display the post title
 */
add_action('listing_post_title_before_image','listing_post_title_before_image');
if(!function_exists('listing_post_title_before_image')){
function listing_post_title_before_image($instance)
{
	if(!empty($instance['show_title'])) :
		printf( '<h2><a href="%s" title="%s">%s</a></h2>', get_permalink(), the_title_attribute('echo=0'), the_title_attribute('echo=0') );
	endif;
}
}
/* 
 * Function name: featured_get_image
 * Return: pass post image;
 */
if(!function_exists('featured_get_image')){
	function featured_get_image($arg)
	{
	global $post;
	if($arg['format']=='html')
	{ echo $arg['size'];
		$image = supreme_get_images($post->ID,$arg['size']);	
		$thumb_img = @$image[0]['file'];
		if($thumb_img)
			echo '<img class="img thumbnail " src="'.$thumb_img.'" />';		
	}else
	{
		$image = supreme_get_images($post->ID,$arg['size']);	
		$thumb_img = @$image[0]['file'];
		echo $thumb_img;
	}	
	}
}
/*
 * Function Name: supreme_get_additional_image_sizes;
 * Return : display all image size
 */
function supreme_get_additional_image_sizes() {
	global $_wp_additional_image_sizes;
	if ( $_wp_additional_image_sizes )
			return $_wp_additional_image_sizes;
	return array();
}
function supreme_loop_navigation($post){
	if ( is_attachment() ) : ?>
<div class="loop-nav">
  <?php previous_post_link( '%link', '<span class="previous">' . __( '<span class="meta-nav">&larr;</span> Return to entry', THEME_DOMAIN ) . '</span>' ); ?>
</div>
<!-- .loop-nav -->
<?php elseif ( is_singular( 'post' ) ) : ?>
<div class="loop-nav">
  <?php previous_post_link( '%link', '<span class="previous">' . __( '<span class="meta-nav">&larr;</span> Previous', THEME_DOMAIN ) . '</span>' ); ?>
  <?php next_post_link( '%link', '<span class="next">' . __( 'Next <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) . '</span>' ); ?>
</div>
<!-- .loop-nav -->
<?php elseif ( !is_singular() && current_theme_supports( 'loop-pagination' ) ) :  loop_pagination( array( 'prev_text' => __( '<span class="meta-nav">&larr;</span> Previous', THEME_DOMAIN ), 'next_text' => __( 'Next <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) ) ); ?>
<?php elseif ( !is_singular() && $nav = get_posts_nav_link( array( 'sep' => '', 'prelabel' => '<span class="previous">' . __( '<span class="meta-nav">&larr;</span> Previous', THEME_DOMAIN ) . '</span>', 'nxtlabel' => '<span class="next">' . __( 'Next &rarr;', THEME_DOMAIN ) . '</span>' ) ) ) : echo "qwe"; ?>
<div class="loop-nav"> <?php echo $nav; ?> </div>
<!-- .loop-nav -->
<?php endif;
}
/*
Name : supreme_quote_post_content
Description : apply quats to content
*/
function supreme_quote_post_content( $content ) {
	if ( has_post_format( 'quote' ) ) {
		preg_match( '/<blockquote.*?>/', $content, $matches );
		if ( empty( $matches ) )
			$content = "<blockquote>{$content}</blockquote>";
	}
	return $content;
}
/* filter for excerpt length */
if(!function_exists('supreme_excerpt_length')){ 
	function supreme_excerpt_length() { 
		$tmpdata = get_option(supreme_prefix().'_theme_settings'); 
		if($tmpdata['templatic_excerpt_length']){
			return $tmpdata['templatic_excerpt_length'];
		}else{
			return 400;
		}
	}
}
/* filter for excerpt length */
if(!function_exists('slider_excerpt_length')){
	function slider_excerpt_length() {
	global $legnth_content;
		return $legnth_content;
	}
}
/*
Name : string_limit_words
Desc : filter for excerpt length and Read more link filter
*/
function string_limit_words($string, $word_limit)
{
	$words = explode(' ', $string, ($word_limit + 1));
	if(count($words) > $word_limit)
	array_pop($words);
	return implode(' ', $words).new_excerpt_more();
}
/*
Name : new_excerpt_more
Desc : Read more link filter
*/
if(!function_exists('new_excerpt_more')){
	function new_excerpt_more($more) {
		global $post;
		$tmpdata = get_option(supreme_prefix().'_theme_settings');
		if(function_exists('icl_t')){
			icl_register_string(THEME_DOMAIN,@$tmpdata['templatic_excerpt_link'],@$tmpdata['templatic_excerpt_link']);
			$link = icl_t(THEME_DOMAIN,@$tmpdata['templatic_excerpt_link'],@$tmpdata['templatic_excerpt_link']);
		}else{
			$link = @$tmpdata['templatic_excerpt_link'];
		}
		if(isset($tmpdata['templatic_excerpt_link']) && $tmpdata['templatic_excerpt_link']){
			return '... <a class="moretag" href="'. get_permalink($post->ID) . '">'.$link.'</a>';
		}else{
			return '... <a class="moretag" href="'. get_permalink($post->ID) . '">'.__('Read more &raquo;',THEME_DOMAIN).'</a>';
		}
	}
}
if(!function_exists('supreme_is_layout1c')){
	function supreme_is_layout1c(){
		$global_layout = theme_get_settings( 'supreme_global_layout' );
			if ( $global_layout == 'layout_1c' ){
				return true;
			}else{
				return false;
			}
			
	}
}
/*
Name :supreme_havent_gallery
Descriptioon : is detail page content have gallery shortcode or not 
*/
function supreme_havent_gallery() {
    global $post;
    if (isset($post->post_content) && !stripos($post->post_content, '[gallery')) {
       return true;
    }else{
       return false;
    }
    
}
/*	
name : supreme_get_images
description :Function for Getting All images of post -- */
function supreme_get_images($iPostID,$img_size='thumb') 
{
	remove_all_actions('posts_where');
     $arrImages =& get_children('order=ASC&orderby=menu_order ID&post_type=attachment&post_mime_type=image&post_parent=' . $iPostID );
	
	$return_arr = array();
	if($arrImages) {		
       foreach($arrImages as $key=>$val)
	   {
	   		$id = $val->ID;
			if($img_size == 'large')
			{
				$img_arr = wp_get_attachment_image_src($id,'full');	// THE FULL SIZE IMAGE INSTEAD
				$imgarr['id'] = $id;
				$imgarr['file'] = $img_arr[0];
				$return_arr[] = $imgarr;
			}
			elseif($img_size == 'medium')
			{
				$img_arr = wp_get_attachment_image_src($id, 'medium'); //THE medium SIZE IMAGE INSTEAD
				$imgarr['id'] = $id;
				$imgarr['file'] = $img_arr[0];
				$return_arr[] = $imgarr;
			}
			elseif($img_size == 'thumb')
			{
				$img_arr = wp_get_attachment_image_src($id, 'thumbnail'); // Get the thumbnail url for the attachment
				$imgarr['id'] = $id;
				$imgarr['file'] = $img_arr[0];
				$return_arr[] = $imgarr;
			}
			elseif($img_size == 'detail_page_image')
			{
				$img_arr = wp_get_attachment_image_src($id, 'detail_page_image'); // Get the thumbnail url for the attachment
				$imgarr['id'] = $id;
				$imgarr['file'] = $img_arr[0];
				$return_arr[] = $imgarr;
			}else{
				$img_arr = wp_get_attachment_image_src($id, $img_size); // Get the thumbnail url for the attachment
				$imgarr['id'] = $id;
				$imgarr['file'] = $img_arr[0];
				$return_arr[] = $imgarr;
	   		}
	   }
	  return $return_arr;
	}
}
/*
Name : supreme_post_gallery
Description : supreme post gallery for detail pages
*/
function supreme_post_gallery($post) {
	global $post;
	$post_images= supreme_get_images($post->ID,'thumb');
	$post_main_image = supreme_get_images($post->ID,'large');
	?>
<?php if(count($post_images)>0): ?>
<!--Image Gallery Start -->
<script type="text/javascript">
					jQuery(window).load(function() {
					  // The slider being synced must be initialized first
					  jQuery('#carousel').flexslider({
						animation: "slide",
						controlNav: false,
						animationLoop: false,
						slideshow: false,
						itemWidth: 100,
						itemMargin: 5,
						asNavFor: '#slider',
						smoothHeight: true,
					  });
					   
					  jQuery('#slider').flexslider({
						animation: "slide",
						controlNav: false,
						animationLoop: false,
						slideshow: false,
						sync: "#carousel",
						smoothHeight: true,
					  });
					});
				//FlexSlider: Default Settings
				</script>
<div class="post_gallery_container">
  <div id="slider" class="flexslider clearfix ">
    <div class="slides_container">
      <ul id = "main_image" class="slides">
        <?php for($im=0;$im<count($post_main_image);$im++): 
                                    		$attachment_id = $post_main_image[$im]['id'];
											//echo "<pre>"; print_r($post_images);
											$alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
											$attach_data = get_post($attachment_id);
											$title = $attach_data->post_title;
                                    ?>
        <li> <a rel="example_group" href="<?php echo $post_main_image[$im]['file'];?>" title="<?php echo $title; ?>"> <img src="<?php echo $post_main_image[$im]["file"];?>" title="<?php echo $title; ?>" alt="<?php echo $alt; ?>" /> </a> </li>
        <?php endfor; ?>
      </ul>
    </div>
  </div>
  <!--Finish image gallery -->
  <?php if(count($post_images) > 1){ ?>
  <div id="carousel" class="flexslider">
    <ul class="slides">
      <?php for($im=0;$im<count($post_images);$im++): 
												$attachment_id = $post_images[$im]['id'];
												//echo "<pre>"; print_r($post_images);
												$alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
												$attach_data = get_post($attachment_id);
												$title = $attach_data->post_title;
										?>
      <li> <a href="<?php echo $post_images[$im]['file'];?>" title="<?php echo $title; ?>"> <img src="<?php echo $post_images[$im]["file"];?>" height="70" width="70"  title="<?php echo $title; ?>" alt="<?php echo $alt; ?>" /> </a> </li>
      <?php endfor; ?>
    </ul>
  </div>
  <?php } ?>
</div>
<?php endif; ?>
<?php 
}
/*
Name : supreme_get_favicon
Description : return the favicon icon 
*/
function supreme_get_favicon(){
	global $tmpdata;
	if(supreme_get_settings('supreme_favicon_icon')){
		return supreme_get_settings('supreme_favicon_icon');
	}
}
/*
 * Function Name:user_single_post_visit_count
 * Argument: Post id
 */
if(!function_exists('user_single_post_visit_count')):
	function user_single_post_visit_count($pid)
	{
		if(get_post_meta($pid,'viewed_count',true))
		{
			return get_post_meta($pid,'viewed_count',true);
		}else
		{
			return '0';	
		}
	}
endif;
/*
 * Function Name:user_single_post_visit_count_daily
 * Argument: Post id
 */
if(!function_exists('user_single_post_visit_count_daily')):
function user_single_post_visit_count_daily($pid)
{
	if(get_post_meta($pid,'viewed_count_daily',true))
	{
		return get_post_meta($pid,'viewed_count_daily',true);
	}else
	{
		return '0';	
	}
}
endif;
/*
 * Functon Name:supreme_view_count
 * Argument: post content
 * add view count display after the content
 */
if(!function_exists('supreme_view_count')):
function supreme_view_count( $content ) {	
	$custom_content='';
	global $post;
	if ( is_single() || is_singular() && !is_page()) 
	{
			$sep =" , ";
			if(user_single_post_visit_count($post->ID) == 1){
				$time = ' '.__("time",THEME_DOMAIN);
			}else{
				$time = ' '.__("times",THEME_DOMAIN);
			}
			
			if(user_single_post_visit_count_daily($post->ID) == 1){
				$visit = ' '.__("Visit",THEME_DOMAIN);
			}else{
				$visit = ' '.__("Visits",THEME_DOMAIN);
			}
			$custom_content.="<p><span class='view_counter'>".__("Visited",THEME_DOMAIN).'<b>'.sprintf(__(' %s',THEME_DOMAIN) ,user_single_post_visit_count($post->ID)).$time.'</b>';
			$custom_content.= $sep.' <b>'.user_single_post_visit_count_daily($post->ID).' '.$visit."</b>".' '.__("today",THEME_DOMAIN)."</span></p>";
			$custom_content .= $content;
			//$content.=$custom_content;
			return $custom_content;
	} 
	return $content;
}
endif;
add_action('before_body_end','supreme_gogole_analytics');
function supreme_gogole_analytics(){
	echo stripslashes(supreme_get_settings('supreme_gogle_analytics_code'));
}
//ADDED ACTION FOR CUSTOMIZER TEXTURE SETTINGS START.
if(!function_exists('templatic_texture_settings')){
	function templatic_texture_settings(){
		$supreme_theme_settings = get_option(supreme_prefix().'_theme_settings');
		$textture_settings = "";
		$ext = pathinfo( @$supreme_theme_settings['alternate_of_texture'], PATHINFO_EXTENSION);
		if(isset($supreme_theme_settings['alternate_of_texture']) && trim($supreme_theme_settings['alternate_of_texture'])!='' && ($ext=='jpg' || $ext=='jpeg' || $ext=='png' || $ext=='gif'  || $ext=='bmp' )):
			$textture_settings_alternet = $supreme_theme_settings['alternate_of_texture'];
		else:
			$textture_settings_alternet = '';
		endif;
		if(($textture_settings_alternet) && $textture_settings_alternet!=""){
			$textture_settings = $textture_settings_alternet;
		}else{
			if(isset($supreme_theme_settings['templatic_texture1']) && trim($supreme_theme_settings['templatic_texture1']) !='' && trim($supreme_theme_settings['templatic_texture1']) !=' '):
				$textture_settings = $supreme_theme_settings['templatic_texture1'];
			else:
				$textture_settings ='none';
			endif;
		}
		if($textture_settings){
				if($textture_settings == 'none'){?>
							<style type="text/css"> 
									body{ background-image: none; } 
					</style>
		<?php
			}else{
	 ?>
						<style type="text/css"> 
									body{ background-image: url(<?php echo $textture_settings;?>); } 
					</style>
<?php
				}
	}
		if(get_stylesheet_directory().'/functions/admin-style.php')
		{
			include_once(get_stylesheet_directory().'/functions/admin-style.php'); // child theme css file
		}
		elseif(file_exists(get_template_directory().'/functions/admin-style.php')){
			include_once(get_template_directory().'/functions/admin-style.php');
		}
	}
}
//ADDED ACTION FOR CUSTOMIZER TEXTURE SETTINGS FINISH.
/*
Name :supreme_secondary_navigation
Description : return secondary navigation menu
*/
function supreme_secondary_navigation(){
	$theme_name = get_option('stylesheet');
	$nav_menu = get_option('theme_mods_'.strtolower($theme_name));
	if(isset($nav_menu['nav_menu_locations'])  && @$nav_menu['nav_menu_locations']['secondary'] != 0){ // show only on desktop ?>
<div id="nav-secondary" class="nav_bg">
  <?php 
		if(isset($nav_menu['nav_menu_locations'])  && @$nav_menu['nav_menu_locations']['secondary'] != 0){
							
		apply_filters('tmpl_supreme_header_secondary',supreme_header_secondary_navigation()); // Loads the menu-secondary template.
							
		}?>
</div>
<?php 
		//add_action('pre_get_posts', 'home_page_feature_listing');	
		apply_filters('tmpl_after-header',supreme_sidebar_after_header()); // Loads the sidebar-after-header. 
	}elseif(is_active_sidebar('mega_menu')){
		if(function_exists('dynamic_sidebar')){
			dynamic_sidebar('mega_menu'); // jQuery mega menu
		} 
	}else{
		echo '<div id="nav-secondary" class="nav_bg"><div id="menu-secondary" class="menu-container clearfix"><nav class="wrap" role="navigation"><div class="menu"><ul id="menu-secondary-items" class="">';
			wp_list_pages('title_li=&depth=0&child_of=0&number=5&show_home=1&sort_column=ID&sort_order=DESC');
		echo '</ul></div>';
		apply_filters('supreme-nav-right',dynamic_sidebar('secondary_navigation_right'));
		echo '</nav></div></div>';
	}
}
/*
Name :supreme_secondary_navigation
Description : return secondary navigation menu
*/
function supreme_sticky_secondary_navigation(){
	$theme_name = get_option('stylesheet');
	$nav_menu = get_option('theme_mods_'.strtolower($theme_name));
	?>
<?php
	echo '<div class="sticky_main" style="display:none">';
	?>
<?php if(supreme_get_settings( 'display_header_text' )){ ?>
<div id="branding1">
  <?php if ( supreme_get_settings( 'supreme_logo_url' ) ) : ?>
  <h1 id="site-title1"> <a href="<?php echo home_url(); ?>/" title="<?php echo bloginfo( 'name' ); ?>" rel="Home"> <img class="logo" src="<?php echo supreme_get_settings( 'supreme_logo_url' ); ?>" alt="<?php echo bloginfo( 'name' ); ?>" /> </a> </h1>
  <?php else :
			supreme_site_title();
		  endif; 
		if ( !supreme_get_settings( 'supreme_site_description' ) )  : // If hide description setting is un-checked, display the site description. 
			supreme_site_description(); 
	endif; ?>
</div>
<!-- #branding -->
<?php } ?>
<?php
	if(isset($nav_menu['nav_menu_locations'])  && $nav_menu['nav_menu_locations']['secondary'] != 0){ // show only on desktop ?>
<div id="nav-secondary1" class="nav_bg">
  <?php 
		if(isset($nav_menu['nav_menu_locations'])  && $nav_menu['nav_menu_locations']['secondary'] != 0){
							
		apply_filters('tmpl_supreme_header_secondary',supreme_header_secondary_navigation()); // Loads the menu-secondary template.
							
		}?>
</div>
<?php 
		//add_action('pre_get_posts', 'home_page_feature_listing');	
		apply_filters('tmpl_after-header',supreme_sidebar_after_header()); // Loads the sidebar-after-header. 
	}elseif(is_active_sidebar('mega_menu')){
		if(function_exists('dynamic_sidebar')){
			echo '<div id="nav-secondary1" class="nav_bg">';
			dynamic_sidebar('mega_menu'); // jQuery mega menu
			echo '</div>';
		} 
	}else{
		echo '<div id="nav-secondary1" class="nav_bg"><div id="menu-secondary1" class="menu-container"><nav class="wrap" role="navigation"><div class="menu"><ul id="menu-secondary-items1" class="">';
			wp_list_pages('title_li=&depth=0&child_of=0&number=5&show_home=1&sort_column=ID&sort_order=DESC');
		echo '</ul></div>';
		apply_filters('supreme-nav-right',dynamic_sidebar('secondary_navigation_right'));
		echo '</nav></div></div>';
	}
	echo '</div>';
}
/*
Name :supreme_primary_navigation
Description : return primary navigation menu
*/
function supreme_primary_navigation(){
	/* $theme_name = get_option('stylesheet');
	$nav_menu = get_option('theme_mods_'.strtolower($theme_name));
	if(isset($nav_menu['nav_menu_locations'])  && $nav_menu['nav_menu_locations']['primary'] != 0){ */
		if ( has_nav_menu( 'primary' ) ) : 
		do_action( 'before_menu_primary' ); // supreme_before_menu_primary ?>
<!-- Primary Navigation Menu Start -->
<div id="menu-primary" class="menu-container">
  <nav role="navigation" class="wrap">
    <div id="menu-primary-title">
      <?php _e( 'Menu', THEME_DOMAIN ); ?>
    </div>
    <!-- #menu-primary-title -->
    <?php do_action( 'open_menu_primary' ); // supreme_open_menu_primary 
				wp_nav_menu( array( 'theme_location' => 'primary', 'container_class' => 'menu', 'menu_class' => 'primary_menu clearfix', 'menu_id' => 'menu-primary-items', 'fallback_cb' => '' ) ); 
				do_action( 'close_menu_primary' ); // supreme_close_menu_primary ?>
  </nav>
</div>
<!-- #menu-primary .menu-container -->
<!-- Primary Navigation Menu End -->
<?php do_action( 'after_menu_primary' ); // supreme_after_menu_primary
		endif;
	/* }else{
		do_action( 'before_menu_primary' );
		echo '<div id="menu-primary" class="menu-container"><nav role="navigation" class="wrap"><div id="menu-primary-title">'.__( 'Menu', THEME_DOMAIN ).'</div><div class="menu"><ul id="menu-primary-items" class="primary_menu clearfix">';
		do_action( 'open_menu_primary' ); 
		wp_list_pages('title_li=&depth=0&child_of=0&number=3&show_home=1&sort_column=ID&sort_order=DESC');
		do_action( 'close_menu_primary' ); 	
		echo '</ul></div></nav></div>';
		do_action( 'after_menu_primary' );
	} */
}
/*
Name :is_it_frontend
Description : return true if you are looking in front end
*/
function is_it_frontend(){
	if(!strstr($_SERVER['REQUEST_URI'],'/wp-admin/')){
		return true;
	}else{
		return false;
	}
}
/* to display slider above main */
apply_filters('supreme_above_main-banner',add_action('before_main','supreme_home_banner_sidebar')); // you can remove this filter from chid theme and add another one to show slider inside main like : add_action ('before-content','supreme_above_main-banner'); 
	
function supreme_home_banner_sidebar(){ 
		if(is_home() || is_front_page())
			dynamic_sidebar('home-page-banner');
}
/*
Name : supreme_plugin_layouts
Description :  Filters 'get_theme_layout' to set layouts for specific installed plugin pages.
 */
function supreme_plugin_layouts( $layout ) {
	if ( current_theme_supports( 'theme-layouts' ) ) {
	
		$global_layout = theme_get_settings( 'supreme_global_layout' );
		if ( $layout == 'layout-default' ) {
	
			
				if ( $global_layout == 'layout_1c' )
					$layout = 'layout-1c';
				elseif ( $global_layout == 'layout_2c_l' )
					$layout = 'layout-2c-l';
				elseif ( $global_layout == 'layout_2c_r' )
					$layout = 'layout-2c-r';
				elseif ( $global_layout == 'layout_3c_c' )
					$layout = 'layout-3c-c';
				elseif ( $global_layout == 'layout_3c_l' )
					$layout = 'layout-3c-l';
				elseif ( $global_layout == 'layout_3c_r' )
					$layout = 'layout-3c-r';
				elseif ( $global_layout == 'layout_hl_1c' )
					$layout = 'layout-hl-1c';
				elseif ( $global_layout == 'layout_hl_2c_l' )
					$layout = 'layout-hl-2c-l';
				elseif ( $global_layout == 'layout_hl_2c_r' )
					$layout = 'layout-hl-2c-r';
				elseif ( $global_layout == 'layout_hr_1c' )
					$layout = 'layout-hr-1c';
				elseif ( $global_layout == 'layout_hr_2c_l' )
					$layout = 'layout-hr-2c-l';
				elseif ( $global_layout == 'layout_hr_2c_r' )
					$layout = 'layout-hr-2c-r';
		}
		
	}
	
	return $layout;
}
/**
 * Filters 'theme_layouts_strings'.
 */
function supreme_theme_layouts( $strings ) {
	/* Set up the layout strings. */
	$strings = array(
		'default' => __( 'Default', THEME_DOMAIN ),
		'1c' => __( 'One Column', THEME_DOMAIN ),
		'2c-l' => __( 'Two Columns, Left', THEME_DOMAIN ),
		'2c-r' => __( 'Two Columns, Right', THEME_DOMAIN ),
		'3c-c' => __( 'Three Columns, Center', THEME_DOMAIN ),
		'3c-l' => __( 'Three Columns, Left', THEME_DOMAIN ),
		'3c-r' => __( 'Three Columns, Right', THEME_DOMAIN),
		'hl-1c' => __( 'Header Left One Column', THEME_DOMAIN ),
		'hl-2c-l' => __( 'Header Left Two Columns, Left', THEME_DOMAIN ),
		'hl-2c-r' => __( 'Header Left Two Columns, Right', THEME_DOMAIN ),
		'hr-1c' => __( 'Header Right One Column', THEME_DOMAIN ),
		'hr-2c-l' => __( 'Header Right Two Columns, Left', THEME_DOMAIN ),
		'hr-2c-r' => __( 'Header Right Two Columns, Right', THEME_DOMAIN )
	);
	return $strings;
}
/*
 * Filters 'get_theme_layout'.
 */
function supreme_layout_default( $layout ) {return 'layout-default';}
function supreme_layout_1c( $layout ) {return 'layout-1c';}
function supreme_layout_2c_l( $layout ) {return 'layout-2c-l';}
function supreme_layout_2c_r( $layout ) {return 'layout-2c-r';}
function supreme_layout_3c_c( $layout ) {return 'layout-3c-c';}
function supreme_layout_3c_l( $layout ) {return 'layout-3c-l';}
function supreme_layout_3c_r( $layout ) {return 'layout-3c-r';}
function supreme_layout_hl_1c( $layout ) {return 'layout-hl-1c';}
function supreme_layout_hl_2c_l( $layout ) {return 'layout-hl-2c-l';}
function supreme_layout_hl_2c_r( $layout ) {return 'layout-hl-2c-r';}
function supreme_layout_hr_1c( $layout ) {return 'layout-hr-1c';}
function supreme_layout_hr_2c_l( $layout ) {return 'layout-hr-2c-l';}
function supreme_layout_hr_2c_r( $layout ) {return 'layout-hr-2c-r';}
/*
 * Disables sidebars based on layout choices.
 *
 */
function supreme_disable_sidebars( $sidebars_widgets ) {
	global $wp_query;
	if ( current_theme_supports( 'theme-layouts' ) && !is_admin() ) {
		if ( 'layout-1c' == theme_layouts_get_layout() ) {
			$sidebars_widgets['front-page-sidebar'] = false;
			$sidebars_widgets['primary-sidebar'] = false;
			$sidebars_widgets['post-listing-sidebar'] = false;
			$sidebars_widgets['post-detail-sidebar'] = false;
			$sidebars_widgets['secondary'] = false;
			$sidebars_widgets['contact_page_sidebar'] = false;
			$args=array(
			  '_builtin' => false,
			);
			//Disable all custom post type sidebar start
			$post_types = get_post_types($args);
			foreach($post_types as $post_type){
				$sidebars_widgets["{$post_type}_category_listing_sidebar"] = false;
				$sidebars_widgets["{$post_type}_detail_sidebar"] = false;
				$sidebars_widgets["{$post_type}_tag_listing_sidebar"] = false;
			}
			//Disable all custom post type sidebar end
			
			//Filter for hide sidebar from theme start
			// USAGE
			// ------------------------------------------------
			//  add_filter('theme_sidebar_hide','hide_bars');
			//  function hide_bars(){
			//  	return array('house_detail_sidebar','primary-sidebar');
			//  }
			// ------------------------------------------------
			$theme_sidebar = '';
			$hide_theme_sidebars = apply_filters('theme_sidebar_hide',$theme_sidebar);
			if($hide_theme_sidebars){
				foreach($hide_theme_sidebars as $hide_theme_sidebar){
					$sidebars_widgets["{$hide_theme_sidebar}"] = false;
				}
			}
			//Filter for hide sidebar from theme end
		}
		elseif ( 'layout-hl-1c' == theme_layouts_get_layout() || 'layout-hr-1c' == theme_layouts_get_layout() ) {
			$sidebars_widgets['front-page-sidebar'] = false;
			$sidebars_widgets['primary-sidebar'] = false;
			$sidebars_widgets['secondary'] = false;
			$sidebars_widgets['after-header'] = false;
			$sidebars_widgets['after-header-2c'] = false;
			$sidebars_widgets['after-header-3c'] = false;
			$sidebars_widgets['after-header-4c'] = false;
			$sidebars_widgets['after-header-5c'] = false;
		}
		elseif ( 'layout-hl-2c-l' == theme_layouts_get_layout() || 'layout-hl-2c-r' == theme_layouts_get_layout() || 'layout-hr-2c-l' == theme_layouts_get_layout() || 'layout-hr-2c-r' == theme_layouts_get_layout() ) {
			$sidebars_widgets['after-header'] = false;
			$sidebars_widgets['after-header-2c'] = false;
			$sidebars_widgets['after-header-3c'] = false;
			$sidebars_widgets['after-header-4c'] = false;
			$sidebars_widgets['after-header-5c'] = false;
		}
		
	}
	return $sidebars_widgets;
}
/*
Name : templatic_advance_search_template_where
Description : advence search where fillter
*/
add_action('init','templatic_advance_search_template_function');
if(!function_exists('templatic_advance_search_template_function')){
	function templatic_advance_search_template_function($query){		
		if(isset($_REQUEST['search_template']) && $_REQUEST['search_template']==1 )
		{
			remove_all_actions('posts_where');
			do_action('advance_search_action');
			add_filter('posts_where', 'templatic_advance_search_template_where');		
		}	
	}
}	
/*
 * Function Name: templatic_advance_search_template_where
 * Return : sql where 
*/
if(!function_exists('templatic_advance_search_template_where')){
	function templatic_advance_search_template_where($where){
		global $wpdb;
		$post_type=$_REQUEST['post_type'];
		$tag_s=$_REQUEST['tag_s'];
		
		$taxonomies = $wpdb->get_var("select tt.taxonomy from $wpdb->term_taxonomy tt ,$wpdb->term_taxonomy t where tt.term_id = t.term_id and t.term_id = '".$_REQUEST['category']."'");
		$todate = trim(@$_REQUEST['todate']);		
		$frmdate = trim(@$_REQUEST['frmdate']);
		$articleauthor = trim(@$_REQUEST['articleauthor']);
		$exactyes = trim(@$_REQUEST['exactyes']);
		
		if(isset($_REQUEST['todate']) && $_REQUEST['todate'] != ""){
			$todate = $_REQUEST['todate'];
			$todate= explode('/',$todate);
			$todate = $todate[2]."-".$todate[0]."-".$todate[1];
			
		}
		if(isset($_REQUEST['frmdate']) && $_REQUEST['frmdate'] != ""){
			$frmdate = $_REQUEST['frmdate'];
			$frmdate= explode('/',$frmdate);
			$frmdate = $frmdate[2]."-".$frmdate[0]."-".$frmdate[1];
		}
		
		
		if($todate!="" && $frmdate=="")
		{
			$where .= " AND   DATE_FORMAT($wpdb->posts.post_date,'%Y-%m-%d %G:%i:%s') >='".$todate."'";
		}
		else if($frmdate!="" && $todate=="")
		{
			
			$where .= " AND  DATE_FORMAT($wpdb->posts.post_date,'%Y-%m-%d %G:%i:%s') <='".$frmdate."'";
		}
		else if($todate!="" && $frmdate!="")
		{
			$where .= " AND  DATE_FORMAT($wpdb->posts.post_date,'%Y-%m-%d %G:%i:%s') BETWEEN '".$todate."' and '".$frmdate."'";
			
		}
		if($articleauthor!="" && $exactyes!=1)
		{
			$where .= " AND  $wpdb->posts.post_author in (select $wpdb->users.ID from $wpdb->users where $wpdb->users.display_name  like '".$articleauthor."') ";
		}
		if($articleauthor!="" && $exactyes==1)
		{
			$where .= " AND  $wpdb->posts.post_author in (select $wpdb->users.ID from $wpdb->users where $wpdb->users.display_name  = '".$articleauthor."') ";
		}		
		//search custom field
		if(isset($_REQUEST['search_custom']) && is_array($_REQUEST['search_custom']))
		{
			foreach($_REQUEST['search_custom'] as $key=>$value)
			{		
				if($_REQUEST[$key]!="" && $key != 'category')
				{
					$where .= " AND ($wpdb->posts.ID in (select $wpdb->postmeta.post_id from $wpdb->postmeta where $wpdb->postmeta.meta_key='$key' and ($wpdb->postmeta.meta_value like \"%$_REQUEST[$key]%\" ))) ";					
				}
			}
		}
		//finish custom field			
		
		if(isset($_REQUEST['category']) && $_REQUEST['category']!="")
		{
			$scat = @$_REQUEST['category'];
			$tax = $taxonomies; 
			$where .= " AND  $wpdb->posts.ID in (select $wpdb->term_relationships.object_id from $wpdb->term_relationships join $wpdb->term_taxonomy on $wpdb->term_taxonomy.term_taxonomy_id=$wpdb->term_relationships.term_taxonomy_id where $wpdb->term_taxonomy.taxonomy=\"$tax\" AND $wpdb->term_taxonomy.term_id=\"$scat\" ) ";
		}
		
		 /* Added for tags searching */
		if(is_search() && $_REQUEST['tag_s']!=""){
			$where .= " OR  ($wpdb->posts.ID in (select p.ID from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p ,$wpdb->postmeta t where c.name like '".$tag_s."' and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.ID = t.post_id and p.post_status = 'publish' group by  p.ID))";
		}	
		
		if(is_plugin_active('wpml-string-translation/plugin.php')){
			$language = ICL_LANGUAGE_CODE;
			$where .= " AND t.language_code='".$language."'";
		}
		return $where;
	
	return $where;
	
	}
}
function templatic_posts_where_filter($join)
{
	global $wpdb, $pagenow, $wp_taxonomies;
	$language_where='';
	if(is_plugin_active('wpml-translation-management/plugin.php')){
		$language = ICL_LANGUAGE_CODE;
		$join .= " AND t.language_code='".$language."'";
	}	
	return $join;
}
function templatic_widget_wpml_filter(){
	global $wpdb;
	if(is_plugin_active('wpml-string-translation/plugin.php')){
		$language = ICL_LANGUAGE_CODE;
		$where = " AND t.language_code='".$language."'";
	}
	return $where;
}
if(!function_exists('check_if_woocommerce_active')){
	function check_if_woocommerce_active(){
		$plugins = wp_get_active_and_valid_plugins();
		$flag ='false';
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
if(function_exists('check_if_woocommerce_active')){
	$is_woo_active = check_if_woocommerce_active();
	if($is_woo_active == 'true'){
		add_theme_support( 'woocommerce' );
	}
}	
/* Action for post type selection meta box for advanced search page template */
add_action('admin_menu', 'supreme2_ptthemes_taxonomy_meta_box');	
add_action('save_post', 'supreme2_insert_post_type');
/*Add The Metabox for post type selection for archive page template. START*/
if(!function_exists('supreme2_ptthemes_taxonomy_meta_box')){
	function supreme2_ptthemes_taxonomy_meta_box() {
		add_meta_box("supreme2_post_type_meta", "Post type options", "supreme2_post_type_meta", "page", "side", "core");
	}	
}
function remove_post_type_option()
{
	if(get_current_screen()->id == 'page')
	{
		echo '<style type="text/css">
				.metabox-prefs label[for="supreme2_post_type_meta-hide"] { display: none !important; }
			</style>';
	}
}
add_action('admin_head', 'remove_post_type_option', 10);
function supreme2_post_type_meta(){ ?>
<script type="text/javascript">
		jQuery.noConflict(); 
		jQuery(document).ready(function() {
		var page_template = jQuery("#page_template").val();
		if(page_template !='page-templates/advance-search.php'){
			jQuery("#supreme2_post_type_meta").css('display','none');
		}
		jQuery("#page_template").change(function() {
			var src = jQuery(this).val();
				if(jQuery("#page_template").val() =='page-templates/advance-search.php'){
				jQuery("#supreme2_post_type_meta").fadeIn(2000); }else{
				jQuery("#supreme2_post_type_meta").fadeOut(2000);
				}
			});
		});
	</script>
<?php
		$custom_post_types_args = array();  
		$custom_post_types = get_post_types($custom_post_types_args,'objects');   
		global $post;
		$template_post_type = "";
		$template_post_type = get_post_meta($post->ID,'template_post_type',true);
		$template_post_types = get_option('templatic_custom_post');
		$template_post_types = array_keys($template_post_types);
		
		$i=0;
		foreach ($custom_post_types as $content_type) 
		{
			if($content_type->name =='post' || in_array($content_type->name,$template_post_types)){				
								
				
				if($template_post_type == $content_type->name || $i==0){ $c = 'checked=checked';}else{ $c=''; }
				?>
<input type="radio" name="template_post_type1" id="<?php echo $content_type->name?>" value="<?php echo $content_type->name;?>"  <?php echo $c;?> />
<label for="<?php echo $content_type->name?>"> <?php echo ucfirst($content_type->name); ?></label>
<br/>
<?php
				$i++;
			}
		}	
}
function supreme2_insert_post_type(){
	global $globals,$wpdb;
	update_post_meta(@$_POST['post_ID'], 'template_post_type', @$_POST['template_post_type1']);
}
function supreme2_view_counter(){
   $prefix = supreme_prefix();
   $settings = get_option( supreme_prefix()."_theme_settings" ); 
   remove_filter('the_content','view_sharing_buttons');  	
   remove_filter( 'the_content', 'view_count' );
   if(isset($settings['enable_view_counter']) && $settings['enable_view_counter']==1){  
	   	add_filter("before_content", "supreme_get_custom_post_type_template" ) ;
		add_filter( 'the_content', 'supreme_view_count' );
   }
	if(function_exists('check_if_woocommerce_active')){
		$is_woo_active = check_if_woocommerce_active();
	}	
	if($is_woo_active == 'true' && 'product' == get_post_type()){
		add_action('woocommerce_single_product_summary','supreme2_view_sharing_buttons',100);
	}else{
		add_action("after_entry",'supreme2_view_sharing_buttons');
	}
			
	
}
add_action("wp_head", "supreme2_view_counter");
if(!function_exists('supreme2_view_sharing_buttons')){
	function supreme2_view_sharing_buttons($content){
		global $post;	
		if (is_single() && ($post->post_type!='product_variation' )) 
		{
			$post_thumbnail_id = get_post_thumbnail_id( $post->ID ); 
			$post_img = wp_get_attachment_image_src( $post_thumbnail_id, 'thumb' );
			$post_images = $post_img[0];
			$title=urlencode($post->post_title);
			$url=urlencode(get_permalink($post->ID));
			$summary=urlencode(htmlspecialchars($post->post_content));
			$image=supreme_get_images($post->ID, 'thumb' );
			$image = $image[0]['file'];
			$settings = get_option( supreme_prefix()."_theme_settings" );   	
			$excerpt = get_the_excerpt();
			if( @$settings['facebook_share_detail_page'] || @$settings['google_share_detail_page'] || @$settings['twitter_share_detail_page'] || @$settings['pintrest_detail_page']){
			echo '<div class="share_link">';
				if(@$settings['facebook_share_detail_page'] == 1){
					?>
<a onClick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $title;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&amp;p[images][0]=<?php echo $image;?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)" id="facebook_share_button">
<?php _e('Facebook Share.',THEME_DOMAIN); ?>
</a>
<?php
				}
				if(@$settings['google_share_detail_page'] == 1): ?>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
<div class="g-plus" data-action="share" data-annotation="bubble"></div>
<?php endif;
				
				if(@$settings['twitter_share_detail_page'] == 1): ?>
<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-text='<?php echo $excerpt;?>' data-url="<?php echo get_permalink($post->ID); ?>" data-counturl="<?php echo get_permalink($post->ID); ?>">
<?php _e('Tweet',THEME_DOMAIN); ?>
</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<?php endif;
				
				if(@$settings['pintrest_detail_page'] == 1):?>
<!-- Pinterest -->
<div class="pinterest"> <a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>&media=<?php echo $image; ?>&description=<?php the_title(); ?>" >
  <?php _e("Pin It",THEME_DOMAIN); ?>
  </a>
  <script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
</div>
<?php endif; 
			echo '</div>';
			}
		}
		return $content;
	}
}
/*
 * Function Name: get_custom_post_type_template
 * add single post view counter
 */
if(!function_exists('supreme_get_custom_post_type_template')){
	function supreme_get_custom_post_type_template($single_template) { 
		global $post;	 
		if(is_single() || is_singular())
			supreme_view_counter_single_post($post->ID);
		
		return $single_template;
	}
}
/*
 * Function Name: view_counter_single_post
 * Argument: post id
 */
if(!function_exists('supreme_view_counter_single_post')){
	function supreme_view_counter_single_post($pid){ 
		if( @$_SERVER['HTTP_REFERER'] == '' || !strstr( @$_SERVER['HTTP_REFERER'], @$_SERVER['REQUEST_URI']))
	{
			$viewed_count = get_post_meta($pid,'viewed_count',true);
			$viewed_count_daily = get_post_meta($pid,'viewed_count_daily',true);
			$daily_date = get_post_meta($pid,'daily_date',true);
		
			update_post_meta($pid,'viewed_count',$viewed_count+1);
		
			if(get_post_meta($pid,'daily_date',true) == date('Y-m-d')){
				update_post_meta($pid,'viewed_count_daily',$viewed_count_daily+1);
			} else {
				update_post_meta($pid,'viewed_count_daily','1');
			}
			update_post_meta($pid,'daily_date',date('Y-m-d'));
		}
	}
}
if(!function_exists('custom_excerpt')){
	function custom_excerpt($limit) {
	  $excerpt = explode(' ', the_excerpt(), $limit);
	  if (count($excerpt)>=$limit) {
		array_pop($excerpt);
		$excerpt = implode(" ",$excerpt).'...';
	  } else {
		$excerpt = implode(" ",$excerpt);
	  }	
	  $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
	  echo $excerpt;
	  return $excerpt;
	}
}
add_action('admin_head','attach_admin_style');
if(!function_exists('attach_admin_style')){
	function attach_admin_style(){
		echo '<link href="' .get_template_directory_uri() . '/library/css/admin_backend.css" rel="stylesheet" type="text/css" />';
	}
}
if(!function_exists('supreme_get_link_url')){
	function supreme_get_link_url() {
		$has_url = get_the_post_format_url();
		return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
	}
}
add_action('admin_footer','delete_sample_data');
if(!function_exists('delete_sample_data')){
function delete_sample_data()
{
?>
<script type="text/javascript">
jQuery(document).ready( function(){
	jQuery('.button_delete').click( function() {
		if(confirm(" Delete the dummy data only if you haven't changed the added data (posts, pages, etc). If you have, all those changes will be lost. Deleting dummy data might also cause changes with your widgets.")){
			window.location = "<?php echo home_url()?>/wp-admin/themes.php?dummy=del";
		}else{
			return false;
		}	
	});
});
</script>
<?php } }
add_action('paypal_successfull_return_content','successfull_return_paypal_status',10,4);
function successfull_return_paypal_status()
{
	update_post_meta($_REQUEST['pid'],"status","Approved");
}
//Set Default permalink on theme activation: start
add_action( 'load-themes.php', 'default_permalink_set' );
function default_permalink_set(){
	global $pagenow;
	if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ){ // Test if theme is activate
		//Set default permalink to postname start
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$wp_rewrite->flush_rules();
		if(function_exists('flush_rewrite_rules')){
			flush_rewrite_rules(true);  
		}
		//Set default permalink to postname end
	}
}
//Set Default permalink on theme activation: end
add_action('testimonial_script','widget_testimonial_script',20,3);
function widget_testimonial_script($transition,$fadin,$fadout)
{
	?>
<script type="text/javascript">
		var $testimonials = jQuery.noConflict();
		$testimonials(document).ready(function() {
		  $testimonials('#testimonials')
			.cycle({
				fx: '<?php echo $transition; ?>', // choose your transition type, ex: fade, scrollUp, scrollRight, shuffle
				 timeout: '<?php echo $fadin; ?>',
				 speed:'<?php echo $fadout; ?>',
	
			 });
		});
		</script>
<?php
}
add_action('init','popular_post_widget',10);
//add image size for pospular post widget
function popular_post_widget()
{
	$supreme_thumbnail_height = apply_filters('supreme_thumbnail_height',100);
	$supreme_thumbnail_width =  apply_filters('supreme_thumbnail_width',100);
	add_image_size( 'popular_post-thumbnail', $supreme_thumbnail_height, $supreme_thumbnail_width, true );
}
add_filter('popular_post_thumb_image','crop_popular_post_thumb_image',10);
function crop_popular_post_thumb_image()
{
	return get_the_image(array('echo' => false, 'size'=> 'popular_post-thumbnail','height' => 100,'width'=>100,'default_image'=>get_template_directory_uri()."/images/noimage100x100.jpg"));
}
if(!function_exists('excerpt')){
	function excerpt($limit = 27) {
		$excerpt = explode(' ', get_the_excerpt(), $limit);
		if (count($excerpt)>=$limit) {
			array_pop($excerpt);
			$excerpt = implode(" ",$excerpt).'...';
		} else {
			$excerpt = implode(" ",$excerpt);
		}	
		$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
		return $excerpt;
	}
}
if(!function_exists('content')){
	function content($limit = 27) {
		$content = explode(' ', get_the_content(), $limit);
		if (count($content)>=$limit) {
			array_pop($content);
			$content = implode(" ",$content).'...';
		} else {
			$content = implode(" ",$content);
		}	
		$content = preg_replace('/\[.+\]/','', $content);
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}
}
//Add action for theme settings menu: Start
	add_action('admin_menu', 'register_theme_settings_menu',9999);
//Add action for theme settings menu: End
/*
Function Name: register_theme_settings_menu
Purpose		 : To create submenu for theme settings in Appearance menu
*/
if(!function_exists('register_theme_settings_menu')){
	function register_theme_settings_menu() {
		add_submenu_page( 'themes.php', __("Theme Settings",THEME_DOMAIN), __("Theme Settings",THEME_DOMAIN), 'manage_options', 'theme-settings-page', 'theme_settings_page_callback' ); 
	}
}
/* to check tevolution plugin is active or not*/
function is_tevolution_active(){
	if(is_plugin_active('Tevolution/templatic.php')){
		return true;
	}else{
		return false;
	}
}
/* to hide description on author page */
function tmpl_donot_display_description(){
	global $post;
	$tmpdata = get_option('templatic_settings');
	if(is_tevolution_active() && ($tmpdata['listing_hide_excerpt']=='' || !in_array($post->post_type,$tmpdata['listing_hide_excerpt']))){
			
		return false;
		
	}else{
		return true;
	}
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
?>
