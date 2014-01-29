<?php
/**
 * Archive Template
 *
 * The archive template is the default template used for archives pages without a more specific template. 
 *
 * @package supreme
 * @subpackage Template
 */
get_header(); // Loads the header.php template. 
	do_action( 'before_content' ); // supreme_before_content
	
	global $wp_query, $posts;	
	 ?>
<section id="content">
  <?php do_action( 'open_content' ); // supreme_open_content ?>
  
  <div class="hfeed">
    <?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template.
			
		apply_filters('tmpl_before-content-archive',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.
		do_action( 'before_loop_archive' ); // supreme_before_entry		
		if ( have_posts() ) : 
		 while ( have_posts() ) : the_post();
			do_action( 'before_entry' ); // supreme_before_entry ?>
    <!-- article start -->
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <?php
			get_template_part('content',get_post_format()); 
		?>
    </article>
    <!-- article end -->
    <?php 
			do_action( 'after_entry' ); // supreme_after_entry
					
		endwhile; 
		wp_reset_query();
		else:
			apply_filters('supreme-loop-error',get_template_part( 'loop-error' )); // Loads the loop-error.php template. 
		endif;
		do_action( 'after_loop_archive' ); // supreme_before_entry	
		
		apply_filters('tmpl_after-content-archive',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it ?>
  </div>
  <!-- .hfeed -->
  <?php do_action( 'close_content' ); // supreme_close_content
	    apply_filters('supreme_archive_loop_navigation',supreme_loop_navigation($post)); // Loads the loop-nav.php template. ?>
</section>
<!-- #content -->
<?php do_action( 'after_content' ); // supreme_after_content
	
	apply_filters('supreme-post-listing-sidebar',supreme_post_listing_sidebar());// load the side bar of listing page
	
get_footer(); // Loads the footer.php template. ?>