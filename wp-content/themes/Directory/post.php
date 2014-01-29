<?php
/**
 * Post Template
 *
 * This is the default post template.  It is used when a more specific template can't be found to display
 * singular views of the 'post' post type.
 *
 * @package supreme
 * @subpackage Template
 */
get_header(); // Loads the header.php template.
do_action( 'before_content' ); // supreme_before_content	?>
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
				apply_filters( 'tmpl-entry',supreme_sidebar_entry() ); // Loads the sidebar-entry ?>
				<div class="entry-content">
				<?php  do_action('open-post-content');	
					$format = get_post_format();
					if(supreme_havent_gallery() && ($format =='gallery')){
						apply_filters('supreme_detail_page_gallery',supreme_post_gallery($post));
					}
					the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) );
					do_action('single_post_custom_fields');
					wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', THEME_DOMAIN ), 'after' => '</p>' ) );
					do_action('close-post-content');
				?>
			     </div>
			     <!-- .entry-content -->
				<?php
				apply_filters('supreme_author_biograply',supreme_author_biography_($post));	// show author biography below post
				apply_filters('supreme_single_categories',supreme_get_categories(__('Categories: ',THEME_DOMAIN),'category','',__('Tags: ',THEME_DOMAIN),'post_tag')); // 1- category label, 2- category slug,3- class name of div, 3- tags label,4- tags slug
				do_action( 'close_entry' ); // supreme_close_entry ?>
			</article>
			<!-- .hentry -->
			<?php do_action( 'after_entry' ); // supreme_after_entry 					
				apply_filters('tmpl_after-singular',supreme_sidebar_after_singular()); // Loads the sidebar-after-singular.
				do_action( 'after_singular' ); // supreme_after_singular 
				apply_filters('supreme_post_loop_navigation',supreme_loop_navigation($post)); // Loads the loop-navigation; // Loads the loop-nav.php template. 
				do_action( 'before_comments' ); // before_comments 
				if ( supreme_get_settings( 'enable_comments_on_post' )) {
					comments_template( '/comments.php', true ); // Loads the comments.php template. 
				}
				do_action( 'after_comments' ); // after_comments 	
			endwhile; 
		endif;
		apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it ?>
	</div>
	<!-- .hfeed -->
	<?php do_action( 'close_content' ); // supreme_close_content ?>
</section>
<!-- #content -->
<?php 
do_action( 'after_content' ); // supreme_after_content
apply_filters('supreme-post-detail-sidebar',supreme_post_detail_sidebar());// load the side bar of listing page	
get_footer(); // Loads the footer.php template. ?>