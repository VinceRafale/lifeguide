<?php
/**
 * Archive Template
 *
 * The archive template is the default template used for archives pages without a more specific template. 
 *
 * @package supreme
 * @subpackage Template
 */
remove_action('pre_get_posts','tevolution_author_post');
get_header(); // Loads the header.php template. 
	do_action( 'before_content' ); // supreme_before_content
	$user_id = get_query_var('author');?>
<section id="content">
  <?php do_action('author_box');?>
  <?php do_action( 'open_content' ); // supreme_open_content 	?>
  
  <div class="hfeed">
    <?php //get_template_part( 'loop-meta' ); // Loads the loop-meta.php template.
			global $wp_query,$htmlvar_name;
			$cus_post_type = get_post_type();
			if(function_exists('directory_fetch_heading_post_type') && function_exists('get_directory_listing_customfields'))
			{
				$heading_type = directory_fetch_heading_post_type($cus_post_type);
				if(count($heading_type) > 0)
				{
					foreach($heading_type as $key=>$heading)
					{	
					
						$htmlvar_name[$heading] = get_directory_listing_customfields($cus_post_type,$heading,$key);//custom fields for custom post type..
					}
				}
			}
			
				
				do_action( 'before_loop_archive' ); // supreme_before_entry		
				if($_REQUEST['fb_event']!='facebook_event')
				{
					if ( have_posts() ) : 
					 while ( have_posts() ) : the_post();
						do_action( 'before_entry' ); // supreme_before_entry 
						
						$featured=get_post_meta(get_the_ID(),'featured_c',true);
						
						$featured=($featured=='c')?'featured_c':'';
						
						if(isset($_REQUEST['sort']) && $_REQUEST['sort'] =='favourites'){
							$post_type_tag = $post->post_type;
							$class="featured_list";
						}else{
							$post_type_tag = '';
							$class='';
						}
						?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <?php get_template_part('content',get_post_format());  #post ?>
    </article>
    <?php
						do_action( 'after_entry' ); // supreme_after_entry
					endwhile; 
						
					else:
						apply_filters('supreme-loop-error',get_template_part( 'loop-error' )); // Loads the loop-error.php template. 
					endif;
				}
				do_action( 'after_loop_archive' ); // supreme_before_entry	
		
			apply_filters('tmpl_after-content-archive',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it ?>
  </div>
  <!-- .hfeed -->
  <?php 
		
		do_action( 'close_content' ); // supreme_close_content
		if($_REQUEST['fb_event']!='facebook_event')
		{
			if(function_exists('directory_pagenavi_plugin')) {
				echo '<div class="pagination loop-pagination">';
				directory_pagenavi_plugin(); 
				echo '</div>';
			}
		}
	    ?>
</section>
<!-- #content -->
<?php do_action( 'after_content' ); // supreme_after_content
	
	apply_filters('supreme-author-page-sidebar',supreme_author_page_sidebar());// load the side bar of listing page
	
get_footer(); // Loads the footer.php template. ?>