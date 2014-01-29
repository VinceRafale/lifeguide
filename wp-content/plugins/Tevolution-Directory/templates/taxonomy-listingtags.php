<?php
/**
 * Directory Category taxonomy page
 *
**/
get_header(); //Header Portion
$tmpdata = get_option('templatic_settings');
?>
<script type="text/javascript">
var category_map = '<?php echo $tmpdata['category_map'];?>';
<?php if($_COOKIE['display_view']=='event_map' && $tmpdata['category_map']=='yes'):?>
jQuery(function() {			
	jQuery('#listpagi').hide();
});
<?php endif;?>
</script>
<?php do_action('after_directory_header');?>
<?php do_action('directory_before_container_breadcrumb'); /*do action for display the breadcrumb in between header and container. */?>
<div id="content" class="contentarea <?php directory_class();?>">
	<?php do_action('directory_inside_container_breadcrumb'); /*do action for display the breadcrumb  inside the container. */ ?>
	<?php do_action('directory_before_categories_title');//do action for display before categories title?>
     <h1 class="loop-title"><?php single_cat_title(); ?></h1>
     <?php 
		if(function_exists('supreme_sidebar_before_content'))
			apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); // Loads the sidebar-before-content.?>
	<?php do_action('directory_after_categories_title');// do action for display after categories title ?>
     <?php do_action('directory_before_categories_description');// do action for display after categories title ?>
     <?php if ( category_description() ) : // Show an optional category description ?>
          <div class="archive-meta"><?php echo category_description(); ?></div>
     <?php endif; ?>
     <?php do_action('directory_after_categories_description');// do action for display after categories title ?>
     <?php do_action('directory_before_subcategory');?>
     <?php do_action('directory_subcategory');?>
     <?php do_action('directory_after_subcategory');?>
     <!--Start loop taxonomy page-->
     <?php do_action('directory_before_loop_taxonomy');?>
     <!--Start loop taxonomy page-->
     <div id="loop_listing_taxonomy" class="<?php if($tmpdata['default_page_view']=="gridview"){echo 'grid';}else{echo 'list';}?>" <?php if( is_plugin_active('Tevolution-Directory/directory.php') && $tmpdata['default_page_view']=="mapview"){ echo 'style="display: none;"';}?>>
     	<?php if (have_posts()) : 
				while (have_posts()) : the_post(); ?>
                    	<?php do_action('directory_before_post_loop');?>
                    	<div class="post <?php templ_post_class();?>">  
                         	<?php do_action('directory_before_category_page_image');           /*do_action before the post image */?>
						<?php do_action('directory_category_page_image');?>  
						<?php do_action('directory_after_category_page_image');           /*do action after the post image */?> 
                              <?php do_action('directory_before_post_entry');?>
                         	<div class="entry"> 
                                   <!--start post type title -->
                                   <?php do_action('directory_before_post_title');         /* do action for before the post title.*/ ?>
                                   <div class="listing-title">
                                   <?php do_action('templ_post_title');                /* do action for display the single post title */?>
                                   </div>
                                   <?php do_action('directory_after_post_title');          /* do action for after the post title.*/?>
                                   <!--end post type title -->
                                   <?php do_action('directory_post_info');                 /*do action for display the post info */ ?>     
                                   <!--Start Post Content -->
                                   <?php do_action('directory_before_post_content');       /* do action for before the post content. */ ?> 
                                   <?php do_action('templ_taxonomy_content');	?>
                                   <?php do_action('directory_after_post_content');        /* do action for after the post content. */?>
                                   <!-- End Post Content -->
                                   <!-- Show custom fields where show on listing = yes -->
                                   <?php do_action('directory_listing_custom_field');/*add action for display the listing page custom field */ ?>
                                   <?php do_action('templ_the_taxonomies');   ?> 
                                   <?php do_action('directory_after_taxonomies');?>
				   		</div>
                              <?php do_action('directory_after_post_entry');?>
                         </div>
                         <?php do_action('directory_after_post_loop');?>
          	<?php endwhile;
				wp_reset_query(); 
			else:?>
          	<p class='nodata_msg'><?php _e( 'Apologies, but no results were found for the requested archive.', DOMAIN ); ?></p>              
          <?php endif;?>
     </div>
     <?php do_action('directory_after_loop_taxonomy');?>
	<?php 
		if(function_exists('supreme_sidebar_after_content'))
			apply_filters('tmpl_after-content',supreme_sidebar_after_content()); // after-content-sidebar use remove filter to dont display it ?>
     
      <?php if($wp_query->max_num_pages !=1):?>
     <div id="listpagi">
          <div class="pagination pagination-position">
                <?php if(function_exists('pagenavi_plugin')) { pagenavi_plugin(); } ?>
          </div>
     </div>
     <?php endif;?>
      <!--End loop taxonomy page -->
</div>
<!--taxonomy  sidebar -->
<?php if ( is_active_sidebar( 'listingtags_tag_listing_sidebar') ) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar('listingtags_tag_listing_sidebar'); ?>
	</div>
<?php 
elseif ( is_active_sidebar( 'primary-sidebar') ) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar('primary-sidebar'); ?>
	</div>
<?php endif; ?>
<!--end taxonomy sidebar -->
<?php get_footer(); ?>