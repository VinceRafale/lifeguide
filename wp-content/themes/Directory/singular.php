<?php
/**
 * Singular Template
 *
 * This is the default singular template.  It is used when a more specific template can't be found to display
 * singular views of posts (any post type).
 *
 * @package supreme
 * @subpackage Template
 */
get_header(); // Loads the header.php template.
	do_action( 'before_content' ); // supreme_before_content ?>
<section id="content">
  <?php do_action( 'open_content' ); // supreme_open_content ?>
  <div class="hfeed">
    <?php apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.
			if ( have_posts() ) :
				while ( have_posts() ) : the_post(); 
					do_action( 'before_entry' ); // supreme_before_entry ?>
    <article id="post-<?php the_ID(); ?>" class="<?php supreme_entry_class(); ?>">
      <?php do_action( 'open_entry' ); // supreme_open_entry 
						do_action('entry-title');
						do_action('supreme-single-post-info');
						//apply_filters( 'supreme-single-post-info', supreme_single_post_info());
						apply_filters( 'tmpl-entry',supreme_sidebar_entry() ); // Loads the sidebar-entry ?>
      <div class="entry-content">
        <?php 
							do_action('open-post-content');
							the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );
							wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', THEME_DOMAIN ), 'after' => '</p>' ) ); 
							do_action('close-post-content');
							?>
      </div>
      <!-- .entry-content -->
      <?php 
						apply_filters('supreme_author_biograply',supreme_author_biography_($post));	// show author biography below post
						apply_filters('supreme_singular_post_categories',supreme_get_categories('Categories: ','category','','Tags: ','post_tag')); // 1- category label, 2- category slug,3- class name of div, 3- tags label,4- tags slug
						do_action( 'close_entry' ); // supreme_close_entry ?>
    </article>
    <!-- .hentry -->
    <?php do_action( 'after_entry' ); // supreme_after_entry
					apply_filters('tmpl_after-singular',supreme_sidebar_after_singular()); // Loads the sidebar-after-singular.
					do_action( 'after_singular' ); // supreme_after_singular
					do_action( 'before_comments' ); // before_comments 
					comments_template( '/comments.php', true ); // Loads the comments.php template. 
					do_action( 'after_comments' ); // after_comments 	
			endwhile; 
			endif;
			apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it ?>
  </div>
  <!-- .hfeed -->
  <?php do_action( 'close_content' ); // supreme_close_content
		apply_filters('supreme_singular_loop_navigation',supreme_loop_navigation($post)); // Loads the loop-navigation . ?>
</section>
<!-- #content -->
<?php do_action( 'after_content' ); // supreme_after_content 
get_sidebar();
get_footer(); // Loads the footer.php template. ?>