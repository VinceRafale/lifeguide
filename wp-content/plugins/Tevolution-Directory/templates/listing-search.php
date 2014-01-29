<?php
/**
 * Directory search page
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
     
	<?php do_action('directory_before_search_title');//do action for display before categories title?>              
	<?php
	global $current_cityinfo;
	if((isset($_REQUEST['radius']) && $_REQUEST['radius']!='') || (isset($_REQUEST['location']) && $_REQUEST['location']!='')){
		if(isset($_REQUEST['radius']) && $_REQUEST['radius']==1){
			$radius_type=(isset($_REQUEST['radius_type']) && $_REQUEST['radius_type']=='kilometer')? 'kilometer': 'mile';
		}
		if(isset($_REQUEST['radius']) && $_REQUEST['radius']!=1 && $_REQUEST['radius']!=""){
			$radius_type=(isset($_REQUEST['radius_type']) && $_REQUEST['radius_type']=='kilometer')? 'kilometers': 'miles';
		}
		$radius=(isset($_REQUEST['location']) && $_REQUEST['location']!='')?  $_REQUEST['radius'].' '.$radius_type.' around "'.$_REQUEST['location'].'"' : $_REQUEST['radius'].' '.$radius_type.' around "'.$current_cityinfo['cityname'].'"';
	}	
	?>
     <header class="page-header">
          <h1 class="page-title"><?php printf( __( 'Search Results for: %s %s', DOMAIN ), '<span>"' . get_search_query() . '"</span>' ,'<span>' . $radius. '</span>'); ?></h1>
     </header>
	<?php  do_action('directory_after_search_title');// do action for display after categories title ?>
     
     <!--Start loop search page-->    
     
     <?php do_action('directory_before_loop_search');?> 
     
     <div id="loop_listing_archive" class="list">
     	<?php if (have_posts()) : 
				while (have_posts()) : the_post(); ?>	
                    	<div class="post <?php templ_post_class();?>">  
                         	<?php do_action('directory_before_search_image');           /*do_action before the post image */?>
                         	
						<?php do_action('directory_archive_page_image');?>  
                              
						<?php do_action('directory_after_search_image');           /*do action after the post image */?> 
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
							echo '<div itemprop="description" class="entry-summary">';
							the_excerpt();
							echo '</div>';
							?>
                                   
                                   <?php do_action('directory_after_post_content');        /* do action for after the post content. */?>
                                   <!-- End Post Content -->
                                   
                                   <!-- Show custom fields where show on listing = yes -->
                                   <?php do_action('directory_listing_custom_field');/*add action for display the listing page custom field */?>
                                   
                                   <?php  do_action('templ_the_taxonomies'); ?> 
                                   
                                   <?php do_action('directory_after_taxonomies');?>
				   		</div>
                         </div>
          	<?php endwhile; ?>               	 
               
			<?php wp_reset_query();
			else: ?>
				<p class='nodata_msg'><?php _e( 'Sorry! No results were found for the requested search. Try searching with some different keywords', DOMAIN ); ?></p>
               
				<?php get_template_part( 'directory-listing','search-form' ); 
				
			endif;?>
     </div>
     
     <?php do_action('directory_after_loop_search');?>
     
     <?php if($wp_query->max_num_pages !=1):?>
     <div id="listpagi">
          <div class="pagination pagination-position">
                <?php if(function_exists('pagenavi_plugin')) { pagenavi_plugin(); } ?>
          </div>
     </div>
     <?php endif;?>
      <!--End loop search page -->
</div>
<!--search sidebar -->
<?php if ( is_active_sidebar( 'primary' )) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar( 'primary' ); ?>		
	</div>
<?php elseif ( is_active_sidebar( 'primary-sidebar') ) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar('primary-sidebar'); ?>
	</div>
<?php elseif ( is_active_sidebar( 'listingcategory_listing_sidebar') ) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar('listingcategory_listing_sidebar'); ?>
	</div>
<?php endif; ?>
<!--search sidebar -->
<?php get_footer(); ?>