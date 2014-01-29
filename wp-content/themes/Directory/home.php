<?php
/**
 * Home Template
 *
 * This is the home template.  Technically, it is the "posts page" template.  It is used when a visitor is on the 
 * page assigned to show a site's latest blog posts.
 *
 * @package supreme
 * @subpackage Template
 */
get_header(); // Loads the header.php template.
do_action( 'before_content' ); // supreme_before_content  ?>
<section id="content">
  <?php do_action( 'open_content' ); // supreme_open_content ?>
  
  <div class="hfeed">
    <?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template.
    
		apply_filters('tmpl_before-content-home',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.
		remove_action('pre_get_posts', 'home_page_feature_listing');
		do_action('supreme_before_article_list');			
		if ( have_posts() ) : 
			while ( have_posts() ) : the_post();
				do_action( 'before_entry' ); // supreme_before_entry ?>
			    <!-- Article start -->
			    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				 <?php get_template_part('content',get_post_format()); ?>
			    </article>
			    <!-- Article end -->
			    <?php do_action( 'after_entry' ); // supreme_after_entry
						
			endwhile; 
			wp_reset_query();
		else:
			apply_filters('supreme-loop-error',get_template_part( 'loop-error' )); // Loads the loop-error.php template. 
		endif;
		do_action('supreme_after_article_list');				
		apply_filters('tmpl_after-content-home',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it ?>
  </div>
  <!-- .hfeed -->
  <?php do_action( 'close_content' ); // supreme_close_content 
	
	 apply_filters('supreme_front_loop_navigation',supreme_loop_navigation($post)); // Loads the loop-navigation . ?>
</section>
<!-- #content -->
<?php do_action( 'after_content' ); // supreme_after_content
			
apply_filters( 'tmpl-front_page_sidebar',supreme_front_page_sidebar() ); // Loads the front page sidebar.
get_footer(); // Loads the footer.php template. ?>