<?php
/**
 * Template Name: Front Page Template
 *
 * This is the home template.  Technically, it is the "posts page" template.  It is used when a visitor is on the 
 * page assigned to show a site's latest blog posts.
 *
 * @package supreme
 * @subpackage Template
 */
get_header(); // Loads the header.php template. ?>

<section id="content">
  <?php do_action( 'open_content' ); // supreme_open_content 
	if ( have_posts() ) : 
		while ( have_posts() ) : the_post(); 
			do_action( 'before_entry' ); // supreme_before_entry ?>
                 
               <div id="post-<?php the_ID(); ?>" class="<?php supreme_entry_class(); ?>">
				<?php do_action( 'open_entry' ); // supreme_open_entry  ?>
                    <div class="entry-content">
                    <?php 
					the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );
					wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', THEME_DOMAIN ), 'after' => '</p>' ) );
                    ?>
                    </div>
                    <!-- .entry-content -->
                    <?php do_action( 'close_entry' ); // supreme_close_entry ?>
               </div>
          	<!-- .hentry -->
  	<?php
		endwhile;
	endif; ?>
     <div class="hfeed">
		<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>
          <?php dynamic_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>
          <div class="home_page_content">
          	<?php dynamic_sidebar('home-page-content'); ?>
          </div>
          <?php dynamic_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>
     </div>
  	<!-- .hfeed -->
	<?php 
  	do_action( 'close_content' ); // supreme_close_content 
	apply_filters('supreme_custom_front_loop_navigation',supreme_loop_navigation($post)); // Loads the loop-navigation .
	?>
</section>
<!-- #content -->
<?php 
apply_filters( 'tmpl-front_page_sidebar',supreme_front_page_sidebar() ); // Loads the front page sidebar.
do_action( 'after_content' ); // supreme_after_content
get_footer(); // Loads the footer.php template. ?>