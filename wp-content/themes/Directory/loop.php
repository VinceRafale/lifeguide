<?php
/**
 * Loop Template
 *
 * Displays the entire post content.
 *
 * @package supreme
 * @subpackage Template
 */
global $posts,$wpdb;
if ( have_posts() ) : 
 while ( have_posts() ) : the_post();
	do_action( 'before_entry' ); // supreme_before_entry 
		$format = get_post_format( $post->ID ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <?php	get_template_part( 'content', get_post_format()); ?>
</article>
<?php
	do_action( 'after_entry' ); // supreme_after_entry
	endwhile; 
	wp_reset_query();
else:
	apply_filters('supreme-loop-error',get_template_part( 'loop-error' )); // Loads the loop-error.php template. 
endif; ?>