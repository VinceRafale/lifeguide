<?php
/**
 * Directory archive page
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
<?php do_action('after_directory_header'); ?>
<?php do_action('directory_before_container_breadcrumb'); /*do action for display the bradcrumb in between header and container. */?>
<div id="content" class="contentarea <?php directory_class();?>">
	<?php do_action('directory_inside_container_breadcrumb'); /*do action for display the bradcrumn  inside the container. */ ?>
     
	<?php do_action('directory_before_archive_title');//do action for display before categories title?>              
     <h1 class="page-title">
		<?php echo ucfirst(apply_filters('tevolution_archive_page_title','Listing'));?>		
	</h1>
	<?php  do_action('directory_after_archive_title');// do action for display after categories title ?>
     
     <!--Start loop archive page-->    
     
     <?php do_action('directory_before_loop_archive');?> 
     
     <div id="loop_listing_archive" class="<?php if($tmpdata['default_page_view']=="gridview"){echo 'grid';}else{echo 'list';}?>" <?php if( is_plugin_active('Tevolution-Directory/directory.php') && $tmpdata['default_page_view']=="mapview"){ echo 'style="display: none;"';}?>>
     	<?php if (have_posts()) : 
				while (have_posts()) : the_post(); ?>	
                    <?php do_action('directory_before_post_loop');?>
                    	<div class="post <?php templ_post_class();?>">  
                         	<?php do_action('directory_before_archive_image');           /*do_action before the post image */?>
                         	
						<?php do_action('directory_archive_page_image');?>  
                              
						<?php do_action('directory_after_archive_image');           /*do action after the post image */?> 
                              
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
                                   
                                  <?php						    
						    if($tmpdata['listing_hide_excerpt']=='' || !in_array(get_post_type(),$tmpdata['listing_hide_excerpt'])){
								if(function_exists('supreme_prefix')){
									$theme_settings = get_option(supreme_prefix()."_theme_settings");
								}else{
									$theme_settings = get_option("supreme_theme_settings");
								}
								if($theme_settings['supreme_archive_display_excerpt']){
									echo '<div itemprop="description" class="entry-summary">';
									the_excerpt();
									echo '</div>';
								}else{
									echo '<div itemprop="description" class="entry-content">';
									the_content(); 
									echo '</div>';
								}
						    }
							?>
                                   
                                   <?php do_action('directory_after_post_content');        /* do action for after the post content. */?>
                                   <!-- End Post Content -->
                                   
                                   <!-- Show custom fields where show on listing = yes -->
                                   <?php do_action('directory_listing_custom_field');/*add action for display the listing page custom field */?>
                                   
                                   <?php do_action('templ_the_taxonomies');   ?>  
                                   
                                   <?php do_action('directory_after_taxonomies');?>
				   		</div>
                              
                              <?php do_action('directory_after_post_entry');?>
                              
                         </div>
                         <?php do_action('directory_after_post_loop');?>
          	<?php endwhile; ?>               	 
               
			<?php wp_reset_query();
			else:?>
          	<p class='nodata_msg'><?php _e( 'Apologies, but no results were found for the requested archive.', DOMAIN ); ?></p>              
          <?php endif;?>
     </div>
     
     <?php do_action('directory_after_loop_archive');?>
     	
     <?php if($wp_query->max_num_pages !=1):?>
     <div id="listpagi">
          <div class="pagination pagination-position">
                <?php if(function_exists('pagenavi_plugin')) { pagenavi_plugin(); } ?>
          </div>
     </div>
    <?php endif;?>
      <!--End loop archive page -->
</div>
<!--archive sidebar -->
<?php if ( is_active_sidebar( 'listingcategory_listing_sidebar' )) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar( 'listingcategory_listing_sidebar' ); ?>		
	</div>
<?php elseif ( is_active_sidebar( 'primary-sidebar') ) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar('primary-sidebar'); ?>
	</div>
<?php elseif ( is_active_sidebar( 'primary') ) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar('primary'); ?>
	</div>
<?php endif; ?>
<!--archive sidebar -->
<?php get_footer(); ?>