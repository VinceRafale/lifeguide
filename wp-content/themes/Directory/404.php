<?php
/**
 * 404 Template
 *
 * The 404 template is used when a reader visits an invalid URL on your site. By default, the template will 
 * display a generic message.
 *
 * @package supreme
 * @subpackage Template
 * @link http://codex.wordpress.org/Creating_an_Error_404_Page
 */
@header( 'HTTP/1.1 404 Not found', true, 404 );
add_filter('body_class','directory_404_page_class');
function directory_404_page_class($class){
	$class[]='layout-1c';
	return $class;
}
get_header(); // Loads the header.php template. 
global $post;
$single_post = $post;
if ( current_theme_supports( 'breadcrumb-trail' ) && supreme_default_theme_settings('supreme_show_breadcrumb')) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); ?>
<section id="content" class="error_404">
	<?php do_action( 'open_content' ); // supreme_open_content ?>  
	<div class="hfeed">
		<div id="post-0" >
			<div class="wrap404 clearfix">
				<p class="display404"><img src="<?php echo get_template_directory_uri()?>/library/images/404.jpg" /></p>
				<h4><?php _e("Sorry, The page you're looking for cannot be found!",THEME_DOMAIN); ?></h4>
		          <p><?php _e("I can help you find the page you want to see, just help me with a few clicks please.",THEME_DOMAIN); ?></p>
				<p><?php  _e(sprintf( 'I recommend you either <a href="javascript://" title="Go BACK" onclick="history.back();">go back</a>, <a href="%2$s" title="Home">go to home</a> page or simply search what you want to see below',							esc_url( $_SERVER['HTTP_REFERER']),esc_url( home_url() )),THEME_DOMAIN); ?></p>
		     </div>
			<div class="entry-content">
				<div class="search404"><?php get_search_form(); // Loads the searchform.php template. ?></div>
			</div>
		</div>
     <!-- .hentry -->
	</div>
	<!-- .hfeed -->
	<?php $post = $single_post;?>
</section>
<!-- #content -->
<?php get_footer(); // Loads the footer.php template. ?>