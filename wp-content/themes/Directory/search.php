<?php
/**
 * Search Template
 *
 * The search template is loaded when a visitor uses the search form to search for something
 * on the site.
 *
 * @package supreme
 * @subpackage Template
 */
get_header(); // Loads the header.php template.
    do_action( 'before_content' ); // supreme_before_content   ?>
<section id="content">
  <?php do_action( 'open_content' ); // supreme_open_content 	?>
   <div class="hfeed">
    <?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. 
			?>
    <div class="twp_search_cont">
      <?php get_template_part('searchform'); ?>
    </div>
    <?php
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
			apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.
			get_template_part( 'loop' ); // Loads the loop.php template.
			apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // afetr-content-sidebar use remove filter to dont display it ?>
  </div>
  <!-- .hfeed -->
  <?php do_action( 'close_content' ); // supreme_close_content
		apply_filters('supreme_search_loop_navigation',supreme_loop_navigation($post)); // Loads the loop-navigation .; // Loads the loop-nav.php template. ?>
</section>
<!-- #content -->
<?php do_action( 'after_content' ); // supreme_after_content
	get_sidebar();
	get_footer(); // Loads the footer.php template. ?>