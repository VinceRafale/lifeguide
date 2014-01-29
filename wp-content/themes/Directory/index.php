<?php
/**
 * Index Template
 *
 * This is the default template.  It is used when a more specific template can't be found to display
 * posts.  It is unlikely that this template will ever be used, but there may be rare cases.
 *
 * @package supreme
 * @subpackage Template
 */
get_header(); // Loads the header.php template.
do_action( 'before_content' ); // supreme_before_content 
?>
<section id="content">

  <?php do_action( 'open_content' ); // supreme_open_content ?>
  
  <div class="hfeed">
  
    <?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. 
	apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content. 
	if ( have_posts() ) :
		while ( have_posts() ) : the_post(); 
			do_action( 'before_entry' ); // supreme_before_entry ?>
               <div id="post-<?php the_ID(); ?>" class="<?php supreme_entry_class(); ?>">
               <?php do_action( 'open_entry' ); // supreme_open_entry 
				do_action( 'entry-title' ); 
				apply_filters('supreme-front-post-info',supreme_front_post_info());
				apply_filters( 'tmpl-entry',supreme_sidebar_entry() ); // Loads the sidebar-entry	?>
                <div class="entry-content">
                  <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );
				wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', THEME_DOMAIN ), 'after' => '</p>' ) ); ?>
                </div>
                <!-- .entry-content -->
                <?php if(supreme_get_settings( 'display_post_terms' )){ 
			 		apply_filters('supreme_index_post_categories',supreme_get_categories('Categories: ','category','','Tags: ','post_tag'));// 1- category label, 2- category slug,3- class name of div, 3- tags label,4- tags slug
                      }
              		do_action( 'close_entry' ); // supreme_close_entry ?>
               </div>
               <!-- .hentry -->
               <?php do_action( 'after_entry' ); // supreme_after_entry 
		endwhile;
	else : ?>
     <div class="<?php supreme_entry_class(); ?>">
          <h2 class="entry-title"><?php _e( 'No Entries', THEME_DOMAIN ); ?></h2>
          <div class="entry-content">
               <p><?php _e( 'Apologies, but no entries were found.', THEME_DOMAIN ); ?></p>
     	</div>
     </div>
     <?php endif;?>
    <!-- .hentry .error -->
    <?php apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it ?>
  </div>
  <!-- .hfeed -->
  <?php do_action( 'close_content' ); // supreme_close_content 
	
	 apply_filters('supreme_index_loop_navigation',supreme_loop_navigation($post)); // Loads the loop-navigation . ?>
</section>
<!-- #content -->
<?php do_action( 'after_content' ); // supreme_after_content 
get_sidebar();
get_footer(); // Loads the header.php template. ?>