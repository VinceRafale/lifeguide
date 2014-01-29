<?php
add_action('init','directory_init_function');
add_action('wp_head','directory_script_style');
function directory_script_style(){	
	if(is_archive() && get_post_type()==CUSTOM_POST_TYPE_LISTING){
	 	wp_enqueue_script('directory-cokies-script', TEVOLUTION_DIRECTORY_URL.'js/jquery_cokies.js',array( 'jquery' ),'',false);
	}	
	if((is_single() || is_singular()) && get_post_type()==CUSTOM_POST_TYPE_LISTING){
		wp_enqueue_script('jquery-ui-tabs');
		?>
          <script type="text/javascript">		
		jQuery(function() {
			jQuery('#image_gallery a').lightBox();
		});
		
		jQuery('#tabs').bind('tabsshow', function(event, ui) {			
		    if (ui.panel.id == "listing_map") {	    
				google.maps.event.trigger(Demo.map, 'resize');
				Demo.map.setCenter(Demo.map.center); // be sure to reset the map center as well
				Demo.init();
		    }
		});
		jQuery(function() {
			jQuery('#tabs').tabs({
				activate: function(event ,ui){
				    //console.log(event);
				    var panel=jQuery(".ui-state-active a").attr("href");
				    if(panel=='#listing_map'){
					     google.maps.event.trigger(Demo.map, 'resize');
						Demo.map.setCenter(Demo.map.center); // be sure to reset the map center as well
						Demo.init();
				    }
				}
			});
		});
		</script>
		<?php
	}
}
add_action('admin_head','manage_function_script'); // to call the script in bottom
add_action('wp_head','manage_function_script');
function manage_function_script(){
	global $pagenow,$post,$wp_query;
	$current_term = $wp_query->get_queried_object();		
	if(is_admin()){
		wp_enqueue_script('function_script',TEVOLUTION_DIRECTORY_URL.'js/function_script.js',array( 'jquery' ),'',false);
		wp_enqueue_script('thickbox');
	}
	
	/* Directory Plugin Style Sheet File */		
	wp_enqueue_style('directory_style',TEVOLUTION_DIRECTORY_URL.'css/directory.css');	?>
	
	<?php
}
function directory_init_function(){
	add_image_size( 'directory-listing-image', 250, 165, true );
	add_image_size( 'directory-single-image', 300, 200, true );
	add_image_size( 'directory-single-thumb', 50, 50, true );
	add_image_size( 'directory-neighbourhood-thumb', 56, 56, true );
	// Register widgetized areas
	if ( function_exists('register_sidebar') )
	{
		register_sidebars(1,array('id' => 'after_directory_header', 'name' => __('Listing Category Pages - Below Header',DOMAIN), 'description' => __('Use this area to show widgets between the secondary navigation bar and main content area on Listing category pages.',DOMAIN),'before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h3><span>','after_title' => '</span></h3>'));		
			
	}
	remove_filter('the_content','view_sharing_buttons');
	remove_filter( 'the_content', 'view_count' );
}
add_action('directory_single_page_map','directory_singlemap_after_post_content');
function directory_singlemap_after_post_content(){
	global $post,$templatic_settings;
	$googlemap_setting=get_option('city_googlemap_setting');
	
	if(is_single() && $googlemap_setting['direction_map']=='yes'){
		$geo_latitude = get_post_meta(get_the_ID(),'geo_latitude',true);
		$geo_longitude = get_post_meta(get_the_ID(),'geo_longitude',true);
		$address = get_post_meta(get_the_ID(),'address',true);
		$map_type =get_post_meta(get_the_ID(),'map_view',true);
		$zooming_factor =get_post_meta(get_the_ID(),'zooming_factor',true);
		if($address){			
		?>
               <div id="directory_location_map" style="width:100%;">
                    <div class="directory_google_map" id="directory_google_map_id" style="width:100%;"> 
                    <?php include_once ('google_map_detail.php');?> 
                    </div>  <!-- google map #end -->
               </div>
		<?php
		}
	
	}
}
/*
 * Function Name: map_post_type
 * Return: given post type category name
 */
add_action('wp_ajax_nopriv_map_post_type_show','map_post_type_show');
add_action('wp_ajax_map_post_type_show','map_post_type_show');
function map_post_type_show(){
	global $current_cityinfo,$wpdb,$country_table,$zones_table,$multicity_table,$city_log_table;
	$post_type=substr($_REQUEST['post_type'],0,-1);
	$post_info=(strstr($post_type,','))? explode(',',$post_type):array($post_type);	
	$mapcategory_info =get_map_categoryinfo($post_info);	
	$mappost_info =get_map_postinfo($post_info);	
	
	if($mappost_info!="")
		$catinfo_arr=implode(',',$mappost_info);
	else
		$catinfo_arr='';		
	
	echo trim($catinfo_arr);
	exit;
}
/*
 * Function Name: directory_body_class filter
 * Return: add class name in body tag
 */
add_filter('body_class','directory_body_class',10,2);
function directory_body_class($classes,$class){
	
	if ( is_front_page() )
		$classes[] = 'tevolution-directory directory-front-page';
	elseif ( is_home() )
		$classes[] = 'tevolution-directory directory-home';
	elseif ( is_single() && get_post_type()==CUSTOM_POST_TYPE_LISTING || (isset($_REQUEST['page']) && $_REQUEST['page']=='preview'))
		$classes[] = 'tevolution-directory directory-single-page';
	elseif ( is_page() || isset($_REQUEST['page']) )
		$classes[] = 'tevolution-directory directory-page';	
	elseif ( is_tax() )
		$classes[] = 'tevolution-directory directory-taxonomy-page';
	elseif ( is_tag() )
		$classes[] = 'tevolution-directory directory-tag-page';
	elseif ( is_date() )
		$classes[] = 'tevolution-directory directory-date-page';
	elseif ( is_author() )
		$classes[] = 'tevolution-directory directory-author-page';
	elseif ( is_search() )
		$classes[] = 'tevolution-directory directory-search-page';
	elseif ( is_post_type_archive() )
		$classes[] = 'tevolution-directory directory-post-type-page';	
	elseif((isset($_REQUEST['page']) && $_REQUEST['page'] == "preview")  && isset($_POST['cur_post_type']) && $_POST['cur_post_type']==CUSTOM_POST_TYPE_LISTING)
	{
		$classes[] = 'tevolution-directory directory-single-page';
	}	
		
	return $classes;
}
/*
 * Function Name: templ_page_title_pinpoint
 * Return: post title with pinpoint
 */
add_action('templ_page_title_','templ_page_title_pinpoint',11);
function templ_page_title_pinpoint(){
	global $post,$directory_settings;
	$title = $post->post_title;
	$title = sprintf( '<span class="ping"><a href="#map_canvas"  class="ping" id="pinpoint_'.$post->ID.'">%s</a></span>', $title );
	echo  $title;
}
function directory_the_content($content){	
	global $post;
	
	if(current_theme_supports('listing_excerpt_setting')){
		$tmpdata = get_option('templatic_settings');
		return limited_content($tmpdata['excerpt_length'],$tmpdata['excerpt_continue']);
	}
	return $content;
}
//add_action('directory_after_archive_title','event_place_archive_search_box',12);
function event_place_archive_search_box(){
	global $wpdb,$post,$wp_query;	
	if(get_post_type()== 'listing' || get_post_type()=='event'):
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => get_post_type(),'public'   => true, '_builtin' => true ));
		$cat_args = array(
					'taxonomy'=>$taxonomies[0],
					'orderby' => 'name', 				
					'hierarchical' => 'true',
					'title_li'=>''
				);	
		$r = wp_parse_args( $cat_args);	
		$catname_arr=get_categories( $r );			
	?>
		<div class="tevolution-directory-search">
          	<form name="archive_directory_search" action="" method="post">
                    <div class="tds_row tevolution-directory-search-keyword">
                    	<input type="text" placeholder="<?php _e('Search for',DOMAIN);?>" value="" name="directory_keywords">
                    </div>
                    <div class="tds_row tevolution-directory-search-location">
                    	<input type="text" placeholder="<?php _e('Enter a location',DOMAIN);?>" value="" name="directory_address" autocomplete="off">
                    </div>
                    <div class="tds_row tevolution-directory-search-category">
                    	<select class="tevolution-search-category" name="directory_category">
                         <?php foreach($catname_arr as $cat):	?>
						<option value="<?php echo $cat->term_id;?>"><?php echo $cat->name;?></option>
					<?php endforeach;?>
                         </select>
                    </div>
                    <div class="tds_row tevolution-directory-search-btn">
                         <button class="tevolution-directory-search-submit">
                         <i class="fa fa-search"></i><?php _e('Submit',DOMAIN);?>
                         </button>
                    </div>
               </form>
          </div>		
	<?php 	
	if(isset($_POST['directory_keywords']) && isset($_POST['directory_address']) && isset($_POST['directory_category'])){
		
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$args=array( 'post_type'  =>get_post_type(),
				   'posts_per_page' =>get_option('posts_per_page'),
				   'post_status' => 'publish',
				   'paged'=>$paged,
				   'tax_query'      => array(                
										  array(
											 'taxonomy' =>$taxonomies[0],
											 'field'    => 'id',
											 'terms'    => $_POST['directory_category'],
											 'operator' => 'IN'
										  )            
									   ), 
				   'meta_query'     => array('relation' => 'AND',
									  array(
										 'key'     => 'address',
										 'value'   => $_POST['directory_address'],
										 'type'    => 'CHAR',
										 'compare' => 'LIKE'
									  )
								   ),
				   'order_by'=>'date',
				   'order' => 'DESC'
				);
		add_filter('post_where','directory_archive_search');
		query_posts( $args );
		remove_filter('post_where','directory_archive_search');
		
	}
	
	endif;// check post type on archive page
	
}
/*
 * Function Name:  directory_class
 * Return: add class name on container div
 */
function directory_class(){
	
	echo get_directory_class();		
}
function get_directory_class(){
	global $wpdb,$templatic_settings,$wp_query,$city_id;
	if($templatic_settings['pippoint_effects'] =='click')
	{ 
		$classes[]="wmap_static"; 
	}else{
		$classes[]="wmap_scroll"; 
	}	
	
	$classes = apply_filters( 'get_directory_class', $classes);
	
	if(!empty($classes))
		$classes = join( ' ', $classes );
	return $classes;	
}
/*
 * Function Name: directory_listing_search
 * Return: mile range wise search
 *
 */
add_action('wp_ajax_nopriv_listing_search','directory_listing_search');
add_action('wp_ajax_listing_search','directory_listing_search');
function directory_listing_search(){
	global $wp_query,$wpdb,$current_cityinfo;
	
	$per_page=get_option('posts_per_page');
	$args=array(
			 'post_type'      => 'listing',
			 'posts_per_page' => $per_page,
			 'post_status'    => 'publish',
			 );
	
	directory_manager_listing_custom_field();
	if(is_plugin_active('wpml-translation-management/plugin.php')){
		add_filter('posts_where', 'wpml_listing_milewise_search_language');
	}
	
	add_action('pre_get_posts','directot_search_get_posts');
	add_filter( 'posts_where', 'directory_listing_search_posts_where', 10, 2 );
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{
		add_filter('posts_where', 'location_multicity_where');
	}
	$post_details= new WP_Query($args);
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{
		remove_filter('posts_where', 'location_multicity_where');
	}
	if(is_plugin_active('wpml-translation-management/plugin.php')){
		remove_filter('posts_where', 'wpml_listing_milewise_search_language');
	}
	if ($post_details->have_posts()) :
		while ( $post_details->have_posts() ) : $post_details->the_post();
		
		if(isset($_REQUEST['page_type'])=='archive'){			
			directory_archive_search_listing($wp_query);
		}elseif(isset($_REQUEST['page_type'])=='taxonomy'){
			directory_taxonomy_search_listing($wp_query);
		}
		endwhile;
		wp_reset_query();
	else:
		?>
        <p class='nodata_msg'><?php _e( 'Apologies, but no results were found for the requested archive.', DOMAIN ); ?></p>    
        <?php
	endif;
	exit;
}
function directot_search_get_posts($wp_query){
	$wp_query->set('is_archive',1);	
	
}
function directory_archive_search_listing($wp_query){
		
	add_filter( "pre_get_posts", "directot_search_get_posts" );
	global $post,$wp_query;	
	$wp_query->set('is_ajax_archive',1);	
	do_action('directory_before_post_loop');
	
	$featured=get_post_meta(get_the_ID(),'featured_c',true);
	$classes=($featured=='c')?'featured_c':'';
	?>
     <div class="post <?php echo $classes;?>">  
          <?php do_action('directory_before_archive_image');           /*do_action before the post image */?>
          
          <?php do_action('directory_archive_page_image');?>  
          
          <?php do_action('directory_after_archive_image');           /*do action after the post image */?> 
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
              $tmpdata = get_option('templatic_settings');
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
               
               <?php do_action('directory_after_taxnomies');?>
          </div>
     </div>
     <?php do_action('directory_after_post_loop');
}
function directory_taxonomy_search_listing($wp_query){
	
	add_filter( "pre_get_posts", "directot_search_get_posts" );
	global $post,$wp_query;	
	$wp_query->set('is_ajax_archive',1);	
	
	do_action('directory_before_post_loop');
	$featured=get_post_meta(get_the_ID(),'featured_c',true);
	$classes=($featured=='c')?'featured_c':'';
	?>
                         
     <div class="post <?php echo $classes;?>">  
          <?php do_action('directory_before_category_page_image');           /*do_action before the post image */?>
          
          <?php do_action('directory_category_page_image');?>  
          
          <?php do_action('directory_after_category_page_image');           /*do action after the post image */?> 
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
               
               <?php do_action('directory_after_taxnomies');?>
          </div>
     </div>
     <?php do_action('directory_after_post_loop');
}
/*
 * Function Name: directory_edit_link
 * Return: display edit link on front end when user logged in.
 */
add_action('directory_edit_link','directory_edit_link');
function directory_edit_link() {
	$post_type = get_post_type_object( get_post_type() );
	if ( !current_user_can( $post_type->cap->edit_post, get_the_ID() ) )
		return '';
	
	$args = wp_parse_args( array( 'before' => '', 'after' => ' ' ), @$args );
	echo $args['before'] . '<span class="edit"><a class="post-edit-link" href="' . esc_url( get_edit_post_link( get_the_ID() ) ) . '" title="' . sprintf( esc_attr__( 'Edit %1$s', DOMAIN ), $post_type->labels->singular_name ) . '">' . __( 'Edit', DOMAIN ) . '</a></span>' . $args['after'];
}
/*
 * Function Name: after_directory_header 
 * Return: display the after directory header widget
 */
add_action('after_directory_header','after_directory_header');
function after_directory_header(){
	
	if ( is_active_sidebar( 'after_directory_header') ) : ?>
	<div id="category-widget" class="category-widget">
		<?php dynamic_sidebar('after_directory_header'); ?>
	</div>
	<?php endif;
}
/* Add add to favourite html for directory theme */
function directory_favourite_html($user_id,$post)
{
	global $current_user,$post;
	$add_to_favorite = __('Add to favourites',DOMAIN);
	$added = __('Added',DOMAIN);
	if(function_exists('icl_register_string')){
		icl_register_string(DOMAIN,'directory'.$add_to_favorite,$add_to_favorite);
		$add_to_favorite = icl_t(DOMAIN,'directory'.$add_to_favorite,$add_to_favorite);
		icl_register_string(DOMAIN,'directory'.$added,$added);
		$added = icl_t(DOMAIN,'directory'.$added,$added);
	}
	$post_id = $post->ID;
	
	$user_meta_data = get_user_meta($current_user->ID,'user_favourite_post',true);
	if($post->post_type !='post'){
		if($user_meta_data && in_array($post_id,$user_meta_data))
		{
			?>
		<li id="tmplfavorite_<?php echo $post_id;?>" class="fav_<?php echo $post_id;?> fav"  > <a href="javascript:void(0);" class="removefromfav" onclick="javascript:addToFavourite('<?php echo $post_id;?>','remove');"><?php echo $added;?></a>   </li>    
			<?php
		}else{
		?>
		<li id="tmplfavorite_<?php echo $post_id;?>" class="fav_<?php echo $post_id;?> fav"><a href="javascript:void(0);" class="addtofav"  onclick="javascript:addToFavourite('<?php echo $post_id;?>','add');"><?php echo $add_to_favorite;?></a></li>
		<?php } 
	}
}
/* Define the custom box */
add_action( 'add_meta_boxes', 'listing_add_custom_box' );
/* Do something with the data entered */
add_action( 'save_post', 'listevent_save_postdata' );
/* Adds a box to the main column on the Post and Page edit screens */
function listing_add_custom_box() {
    $screens = array( 'listing' );
    if(is_plugin_active('Tevolution-Events/events.php')){
	    foreach ($screens as $screen) {
		   add_meta_box('listevent_sectionid', __( 'Events linked to this listing', DOMAIN ),'listevent_custom_boxes', $screen,'side');
	    }
    }
}
/*
function: listevent_custom_boxes
description: it will list down the events enter by each user
*/
function listevent_custom_boxes(){
	global $wpdb,$post_id,$post;
	
	$event_for_listing=(get_post_meta($post_id,'event_for_listing',true))?explode(',',get_post_meta($post_id,'event_for_listing',true)):'';
	
	$post_type_name='event';
	$per_page = 50;
	$pagenum = isset( $_REQUEST[$post_type_name . '-tab'] ) && isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
	$offset = 0 < $pagenum ? $per_page * ( $pagenum - 1 ) : 0;
	$args = array(
		'offset' => $offset,
		'order' => 'ASC',
		'orderby' => 'title',
		'posts_per_page' => $per_page,
		'post_type' => 'event',
		'suppress_filters' => false,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false
	);
	
	if ( isset( $post_type['args']->_default_query ) )
		$args = array_merge($args, (array) $post_type['args']->_default_query );
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))
	{
		add_filter('posts_where', 'wpml_listing_milewise_search_language');
	}
	$get_posts = new WP_Query;
	$posts = $get_posts->query( $args );
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))
	{
		remove_filter('posts_where', 'wpml_listing_milewise_search_language');
	}
	$post_type_object = get_post_type_object($post_type_name);

	$num_pages = $get_posts->max_num_pages;	
	$page_links = paginate_links( array(
		'base' => add_query_arg(
			array(
				$post_type_name . '-tab' => 'all',
				'paged' => '%#%',
				'item-type' => 'post_type',
				'item-object' => $post_type_name,
			)
		),
		'format' => '',		
		'prev_text' => __(''),
		'next_text' => __(''),
		'total' => $num_pages,
		'current' => $pagenum
	));	
	?>
     <input type="hidden" name="event_for_listing" id="listing_event_link" value="<?php echo get_post_meta($post_id,'event_for_listing',true);?>"/>
	<div id="posttype-event-listing" class="posttypediv">		
		<div id="listing-event-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
			<?php if ( ! empty( $page_links ) ) : ?>
				<div class="add-pagelinks add-menu-item-pagelinks">
					<?php echo $page_links; ?>
				</div>
			<?php endif; ?>
			<ul id="listing_event_checklist" data-wp-lists="list:<?php echo $post_type_name?>" class="categorychecklist form-no-clear">
				<?php
				foreach($posts as $event_d){					
					if(!empty($event_for_listing) && in_array($event_d->ID,$event_for_listing)){ $checked = 'checked=checked'; }else{ $checked='';}
					echo '<li><input '.$checked.' id="event-'.$event_d->ID.'" type="checkbox" name="event_for_listing_[]" onclick="get_event_id(this,'.$pagenum.');" class="menu-item-checkbox" value="'.$event_d->ID.'"> <label for="event-'.$event_d->ID.'">'.$event_d->post_title.'</label></li>';					
				}
				?>
			</ul>
			<?php if ( ! empty( $page_links ) ) : ?>
				<div class="add-pagelinks add-menu-item-pagelinks">
					<?php echo $page_links; ?>
				</div>
			<?php endif; ?>
		</div><!-- /.tabs-panel -->

	</div><!-- /.posttypediv -->
     <script type="text/javascript">
	jQuery('#listing-event-all .add-pagelinks a').live("click",function(){
		  var pagged=jQuery(this).text()		  
		  //post code
		  <?php $postid='&post_id='.$post_id?>
		  jQuery.ajax({
				url:ajaxurl,
				type:'POST',
				data:'action=listing_event_link&posttype=event&paged='+pagged+'<?php echo $postid?>',
				success:function(results){					
					jQuery('#posttype-event-listing').html(results);
					var event_for_listing=jQuery('#listing_event_link').val();
					var separated = event_for_listing.split(",");
					for(var i=0;i<separated.length;i++){
						jQuery('#event-'+separated[i]).prop('checked', true);	
					}
				}
			});
		  return false;
	})
	var event_id='';
	function get_event_id(str,pagenum){
		var event_for_listing=jQuery('#listing_event_link').val();
		var eventid='';		
		if(jQuery('#'+str.id).attr('checked')=='checked'){
			event_for_listing+=str.value+',';
			jQuery('#listing_event_link').val(event_for_listing);
			
		}else{
			var event_listing='';
			var separated = event_for_listing.split(",");
			for(var i=0;i<separated.length;i++){				
				if(separated[i]!=str.value && separated[i]!=''){
					event_listing+=separated[i]+',';					
				}
			}
			jQuery('#listing_event_link').val(event_listing);
		}
		
	}
	
	</script>
	<?php
	
	echo '<p class="description">'.__('This connects your event listings to this listing and will show the selected events on the listing detail page.<br><strong>Note:</strong> Feature is only available with the &lsquo;Event Manager&rsquo; plugin.',DOMAIN).'</p>';
	
}
/* save the event */
function listevent_save_postdata(){
	global $wpdb,$post_id,$post;
	if(isset($_POST['event_for_listing']) && $_POST['event_for_listing']!='')
	{
		update_post_meta($post_id,'event_for_listing',$_POST['event_for_listing']); // booked tickets
	
	}
}
add_action('directory_the_taxonomies','directory_post_categories_tags');
function directory_post_categories_tags()
{
	global $post;
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));	
	$terms = get_the_terms($post->ID, $taxonomies[0]);
	$sep = ",";
	$i = 0;
	foreach($terms as $term)
	{
		
		if($i == ( count($terms) - 1))
		{
			$sep = '';
		}
		elseif($i == ( count($terms) - 2))
		{
			$sep = __(' and ',DOMAIN);
		}
		$term_link = get_term_link( $term, $taxonomies[0] );
		if( is_wp_error( $term_link ) )
			continue;
		$taxonomy_category .= '<a href="' . $term_link . '">' . $term->name . '</a>'.$sep; 
		$i++;
	}
	if(!empty($terms))
	{
		echo '<p class="bottom_line"><span class="i_category">';
		echo sprintf(__('Posted In %s',DOMAIN),$taxonomy_category);
		echo '</p></span>';
	}
	global $post;
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));	
	
	$tag_terms = get_the_terms($post->ID, $taxonomies[1]);
	$sep = ",";
	$i = 0;
	if($tag_terms){
	foreach($tag_terms as $term)
	{
		
		if($i == ( count($tag_terms) - 1))
		{
			$sep = '';
		}
		elseif($i == ( count($tag_terms) - 2))
		{
			$sep = __(' and ',DOMAIN);
		}
		$term_link = get_term_link( $term, $taxonomies[1] );
		if( is_wp_error( $term_link ) )
			continue;
		$taxonomy_tag .= '<a href="' . $term_link . '">' . $term->name . '</a>'.$sep; 
		$i++;
	}
	}
	if(!empty($tag_terms))
	{
		echo '<p class="bottom_line"><span class="i_category">';
		echo sprintf(__('Tagged In %s',DOMAIN),$taxonomy_tag);
		echo '</p></span>';
	}
}
add_action('close_entrylisting','show_listing_favourite_html'); // show favourites on listing author page
// function to show favourites on listing author page
function show_listing_favourite_html(){
	global $post,$current_user;
	echo '<div class="rev_pin">';
		echo '<ul>';
	if(function_exists('directory_favourite_html'))
		{
			$user_id = get_query_var('author');
			directory_favourite_html($user_id,$post);
		}
		$post_id=$post->ID;
		$comment_count= count(get_comments(array('post_id' => $post_id)));
		$review=($comment_count <=1 )? __('review',DOMAIN):__('reviews',DOMAIN);
		$review=apply_filters('tev_review_text',$review);
		?>
			<li class="review"> <?php echo '<a href="'.get_permalink($post_id).'#comments">'.$comment_count.' '.$review.'</a>';?></li>
		<?php
		echo '</ul>';
	echo '</div>';
}
/* 
 * Function Name: tevolution_listing_sample_csvfile
 * Display the listing sample csv file
 */
add_action('tevolution_listing_sample_csvfile','tevolution_listing_sample_csvfile');
function tevolution_listing_sample_csvfile(){
	?>
     <a href="<?php echo TEVOLUTION_DIRECTORY_URL.'functions/listing_sample.csv';?>"><?php _e('(Sample csv file)',DOMAIN);?></a>
     <?php	
}
/*
 * Function Name: directory_listing_search_map
 * Return: mile range wise search
 *
 */
add_action('wp_ajax_nopriv_listing_search_map','directory_listing_search_map');
add_action('wp_ajax_listing_search_map','directory_listing_search_map');
function directory_listing_search_map(){
	global $wp_query,$wpdb,$current_cityinfo;
	
	$per_page=get_option('posts_per_page');
	$args=array(
			 'post_type'      => 'listing',
			 'posts_per_page' => $per_page,
			 'post_status'    => 'publish',
			 );
	
	directory_manager_listing_custom_field();
	if(is_plugin_active('wpml-translation-management/plugin.php')){
		add_filter('posts_where', 'wpml_listing_milewise_search_language');
	}
	
	add_action('pre_get_posts','directot_search_get_posts');
	add_filter( 'posts_where', 'directory_listing_search_posts_where', 10, 2 );
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{
		add_filter('posts_where', 'location_multicity_where');
	}
	$post_details= new WP_Query($args);
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{
		remove_filter('posts_where', 'location_multicity_where');
	}
	if(is_plugin_active('wpml-translation-management/plugin.php')){
		remove_filter('posts_where', 'wpml_listing_milewise_search_language');
	}
	if ($post_details->have_posts()) :
		while ( $post_details->have_posts() ) : $post_details->the_post();
			$ID =get_the_ID();				
			$title = get_the_title($ID);
			$plink = get_permalink($ID);
			$lat = get_post_meta($ID,'geo_latitude',true);
			$lng = get_post_meta($ID,'geo_longitude',true);					
			$address = stripcslashes(str_replace($srcharr,$replarr,(get_post_meta($ID,'address',true))));
			$contact = str_replace($srcharr,$replarr,(get_post_meta($ID,'phone',true)));
			$website = get_post_meta($ID,'website',true);
			/*Fetch the image for display in map */
			if ( has_post_thumbnail()){
				$post_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail');						
				$post_images=$post_img[0];
			}else{
				$post_img = bdw_get_images_plugin($ID,'thumbnail');					
				$post_images = $post_img[0]['file'];
			}
			
			
			if($post_images)
				$post_image='<div class=map-item-img><img src='.$post_images.' width=150 height=150/></div>';
			else
				$post_image='';
			
			if($cat->term_icon)
				$term_icon=$cat->term_icon;
			else
				$term_icon=TEVOLUTION_LOCATION_URL.'images/pin.png';
			
			$image_class=($post_image)?'map-image' :'';
			$comment_count= count(get_comments(array('post_id' => $ID)));
			$review=($comment_count <=1 )? __('review',DOMAIN):__('reviews',DOMAIN);	
			
			if(($lat && $lng )&& !in_array($ID,$pids))
			{ 	
				$retstr ='{';
				$retstr .= '"name":"'.$title.'",';
				$retstr .= '"location": ['.$lat.','.$lng.'],';
				$retstr .= '"message":"<div class=\"google-map-info '.$image_class.'\"><div class=map-inner-wrapper><div class=map-item-info>'.$post_image;
				$retstr .= '<h6><a href='.$plink.' class=ptitle><span>'.$title.'</span></a></h6>';							
				if($address){$retstr .= '<p class=address>'.$address.'</p>';}
				if($contact){$retstr .= '<p class=contact>'.$contact.'</p>';}
				if($website){$retstr .= '<p class=website><a href= '.$website.'>'.$website.'</a></p>';}
				if($templatic_settings['templatin_rating']=='yes'){
					$rating=draw_rating_star_plugin(get_post_average_rating(get_the_ID()));
					$retstr .= '<div class=map_rating>'.str_replace('"','',$rating).' <span><a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
				}elseif(is_plugin_active('Templatic-MultiRating/multiple_rating.php') && function_exists('single_average_rating')){
					$rating=get_single_average_rating(get_the_ID());
					$retstr .= '<div class=map_rating>'.stripcslashes(str_replace('"','',$rating)).'<span><a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
				}
				$retstr .= '</div></div></div>';
				$retstr .= '",';
				$retstr .= '"icons":"'.$term_icon.'",';
				$retstr .= '"pid":"'.$ID.'"';
				$retstr .= '}';
				$content_data[] = $retstr;
				$j++;
			}	
			
			$pids[]=$ID;
		endwhile;
		wp_reset_query();	
		
	endif;
	if($content_data)	
		$cat_content_info[]= implode(',',$content_data);
				
	if($cat_content_info)
	{
		echo '[{"totalcount":"'.$j.'",'.substr(implode(',',$cat_content_info),1).']';
	}else
	{
		echo '[{"totalcount":"0"}]';
	}
	exit;
}


/*
 * Function Name: listing_event_link
 *  Return: display the event post list
 */ 

add_action('wp_ajax_listing_event_link','listing_event_link');
function listing_event_link(){
	global $wpdb,$post_id,$post;	
	$event_for_listing=(isset($_REQUEST['post_id']) && get_post_meta($_REQUEST['post_id'],'event_for_listing',true))?explode(',',get_post_meta($_REQUEST['post_id'],'event_for_listing',true)):'';
	
	$post_type_name='event';
	$per_page = 50;
	$pagenum = (isset( $_REQUEST['paged'] ) )? absint( $_REQUEST['paged'] ) : 1;
	$offset = 0 < $pagenum ? $per_page * ( $pagenum - 1 ) : 0;
	$args = array(
		'offset' => $offset,
		'order' => 'ASC',
		'orderby' => 'title',
		'posts_per_page' => $per_page,
		'post_type' => 'event',
		'suppress_filters' => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false
	);	
	if ( isset( $post_type['args']->_default_query ) )
		$args = array_merge($args, (array) $post_type['args']->_default_query );
	
	$get_posts = new WP_Query;
	$posts = $get_posts->query( $args );
	$post_type_object = get_post_type_object($post_type_name);

	$num_pages = $get_posts->max_num_pages;	
	$page_links = paginate_links( array(
		'base' => add_query_arg(
			array(
				$post_type_name . '-tab' => 'all',
				'paged' => '%#%',
				'item-type' => 'post_type',
				'item-object' => $post_type_name,
			)
		),
		'format' => '',		
		'prev_text' => __(''),
		'next_text' => __(''),
		'total' => $num_pages,
		'current' => $pagenum
	));	
	?>	
     <div id="listing-event-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
          <?php if ( ! empty( $page_links ) ) : ?>
               <div class="add-pagelinks add-menu-item-pagelinks">
                    <?php echo $page_links; ?>
               </div>
          <?php endif; ?>
          <ul id="<?php echo $post_type_name; ?>checklist" data-wp-lists="list:<?php echo $post_type_name?>" class="categorychecklist form-no-clear">
               <?php
               foreach($posts as $event_d){
                    if(!empty($event_for_listing) && in_array($event_d->ID,$event_for_listing)){ $checked = 'checked=checked'; }else{ $checked='';}
                    echo '<li><input '.$checked.' id="event-'.$event_d->ID.'" type="checkbox" name="event_for_listing_[]"  onclick="get_event_id(this,'.$pagenum.')" class="menu-item-checkbox" value="'.$event_d->ID.'"> <label for="event-'.$event_d->ID.'">'.$event_d->post_title.'</label></li>';					
               }
               ?>
          </ul>
          <?php if ( ! empty( $page_links ) ) : ?>
               <div class="add-pagelinks add-menu-item-pagelinks">
                    <?php echo $page_links; ?>
               </div>
          <?php endif; ?>
     </div><!-- /.tabs-panel -->
     <?php
	exit;
}
?>