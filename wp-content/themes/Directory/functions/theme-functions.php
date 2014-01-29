<?php
/* 
include theme related functions and filters 
*/
/* to fetch the front page page template */
global $wpdb;
$pageid='';
if(!get_option('directory_frontpage')){
	$wp_pages = get_pages(array(
	'meta_key' => '_wp_page_template',
	'meta_value' => 'page-templates/front-page.php'
	));
	foreach($wp_pages as $page){
		$pageid = $page->ID;
	}
	if(!$pageid){
		$page_meta = array('_wp_page_template'=>'page-templates/front-page.php','Layout'=>'2c-l'); 
		$page_info_arr[] = array('post_title'=>'Front page',
								'post_content'=>'',
								'post_meta'=> $page_meta);
		if(function_exists('set_page_info_autorun'))
			set_page_info_autorun(@$pages_array,$page_info_arr); /* function to save.autosave the pages */
		$wp_pages = get_pages(array(
		'meta_key' => '_wp_page_template',
		'meta_value' => 'page-templates/front-page.php'
		));
		foreach($wp_pages as $page){
		 $pageid = $page->ID;
		}
	}
	update_option('directory_frontpage',$pageid);
}else{
	$pageid = get_option('directory_frontpage');
}
/* show custom home page for this theme*/
if(get_option('show_on_front') && !get_option('page_update_first')){
	update_option('show_on_front','page');
	update_option('page_on_front', $pageid);
	update_option('page_update_first', 1);
}
/* Hook to change the height of croausal slider image  */
add_filter('carousel_slider_height', 'responsive_crousal_height');
/* return height for crausal slider image */
function responsive_crousal_height($height){
	$height = 400;
	return $height;
}
/* set default hight and width of slider images */
add_filter('supreme_slider_width','supreme_slider_width_',11);
add_filter('supreme_slider_height','supreme_slider_height',11);
function supreme_slider_height($height){
	return 300;
}
function supreme_slider_width_($width){
	return 500;
}
/* to provide a support of display content in slider */
add_theme_support('slider-post-content');
/* to provide a option of posts per slide */
add_theme_support('postperslide');	
/*
 * Function Name: directory_theme_before_main
 * Return: display google map full widget on list and event listing page
 */
add_action('before_main','directory_theme_before_main');
function directory_theme_before_main(){
	global $post;	
	if(!is_single() && !is_author() && !is_home()){
		$tmpdata = get_option('city_googlemap_setting');	
		if(isset($tmpdata['google_map_full_width']) && $tmpdata['google_map_full_width']=='yes'){
			remove_action('after_event_header','after_event_header');
			remove_action('after_directory_header','after_directory_header');
			$map_class=($tmpdata['google_map_full_width']=='yes')?'map_full_width':'map_fixed_width';
			if(get_post_type()=='listing'){
				if ( is_active_sidebar( 'after_directory_header') ) : ?>
                         <div id="category-widget" class="category-widget">
                           <?php dynamic_sidebar('after_directory_header'); ?>
                         </div>
               <?php endif;
			}
			if(get_post_type()=='event'){
				if ( is_active_sidebar( 'after_event_header') ) : ?>
                         <div id="category-map" class="category-map">
                           <?php dynamic_sidebar('after_event_header'); ?>
                         </div>
               <?php endif;
			}
		}
	}
}
if(!function_exists('is_tevolution_active')){
	function is_tevolution_active(){
		if(is_plugin_active('Tevolution/templatic.php')){
			return true;
		}else{
			return false;
		}
	}
}
add_filter('post_class','featured_post_class');
/* add class to featured [post */
function featured_post_class($class){
	global $post;
	if(is_author()){
		$featured=get_post_meta($post->ID,'featured_c',true);						
		$featured=($featured=='c')?'featured_c':'';	
		$class[]=$featured;
	}
	
	return $class;
}
/* 
Name : directory_bdw_get_images_plugin
description : Resize image
*/
if(!function_exists('directory_bdw_get_images_plugin')){
	function directory_bdw_get_images_plugin($iPostID,$img_size='thumb',$no_images='') 
	{
		$arrImages =& get_children('order=ASC&orderby=menu_order ID&post_type=attachment&post_mime_type=image&post_parent=' . $iPostID );	
		$counter = 0;
		$return_arr = array();	
		
		if (has_post_thumbnail( $iPostID ) && is_tax()){
			$img_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $iPostID ), 'thumbnail' );
			$imgarr['id'] = $id;
			$imgarr['file'] = $img_arr[0];
			$return_arr[] = $imgarr;
		}else{
			if($arrImages) 
			{
			   foreach($arrImages as $key=>$val)
			   {		  
					$id = $val->ID;
					if($val->post_title!="")
					{
						if($img_size == 'thumb')
						{
							$img_arr = wp_get_attachment_image_src($id, 'thumbnail'); // Get the thumbnail url for the attachment
							$return_arr[] = $img_arr[0];
						}
						else
						{
							$img_arr = wp_get_attachment_image_src($id, $img_size); 
							$imgarr['id'] = $id;
							$imgarr['file'] = $img_arr[0];
							$return_arr[] = $imgarr;
						}
					}
					$counter++;
					if($no_images!='' && $counter==$no_images)
					{
						break;	
					}
					
			   }
			}	
		}  return $return_arr;
	}
}
add_action('templ_before_container_breadcrumb','breadcrumb_trail');
/*
 Name : supreme_layouts
 Description: Conditional logic deciding the layout of certain pages.
*/
function supreme_layouts() {
	if ( current_theme_supports( 'theme-layouts' ) ) {
		$global_layout = supreme_get_settings( 'supreme_global_layout' );
		$woocommerce_layout = supreme_get_settings( 'supreme_woocommerce_layout' );
		$layout = theme_layouts_get_layout();
		if ( !is_singular() && $global_layout !== 'layout_default' && function_exists( "supreme_{$global_layout}" ) ) {
			add_filter( 'get_theme_layout', 'supreme_' . $global_layout );
		} // end global layout control
		
		if ( is_singular() && $layout == 'layout-default' && $global_layout !== 'layout_default' && function_exists( "supreme_{$global_layout}" ) ) {
			add_filter( 'get_theme_layout', 'supreme_' . $global_layout );
		} // end singular layout control relative to global layout control
		
		if ( function_exists ( 'bbp_loaded' ) ) {
			if ( is_bbpress() && !is_singular() && $bbpress_layout !== 'layout_default' && function_exists( "supreme_{$bbpress_layout}" ) ) {
				add_filter( 'get_theme_layout', 'supreme_' . $bbpress_layout );
			}
			elseif ( is_bbpress() && is_singular() && $layout == 'layout-default' && $bbpress_layout !== 'layout_default' && function_exists( "supreme_{$bbpress_layout}" ) ) {
				add_filter( 'get_theme_layout', 'supreme_' . $bbpress_layout );
			}
		} // end bbpress layout control
		
		remove_post_type_support('admanager','theme-layouts');
		if ( function_exists ( 'is_woocommerce' ) ) {
			if ( is_woocommerce() && !is_singular() && $woocommerce_layout !== 'layout_default' && function_exists( "supreme_{$woocommerce_layout}" ) ) {
				add_filter( 'get_theme_layout', 'supreme_' . $woocommerce_layout );
			}
			elseif ( is_woocommerce() && is_singular() && $layout == 'layout-default' && $woocommerce_layout !== 'layout_default' && function_exists( "supreme_{$woocommerce_layout}" ) ) {
				add_filter( 'get_theme_layout', 'supreme_' . $woocommerce_layout );
			}
		} // end woocommerce layout control
	}
}
/* Paginaton start BOF
   Function that performs a Boxed Style Numbered Pagination (also called Page Navigation).
   Function is largely based on Version 2.4 of the WP-PageNavi plugin */
function directory_pagenavi_plugin($before = '', $after = '') {
    global $wpdb, $wp_query,$paged;
	
    $pagenavi_options = array();
   // $pagenavi_options['pages_text'] = ('Page %CURRENT_PAGE% of %TOTAL_PAGES%:');
    $pagenavi_options['current_text'] = '%PAGE_NUMBER%';
    $pagenavi_options['page_text'] = '%PAGE_NUMBER%';
    $pagenavi_options['first_text'] = __('First Page',THEME_DOMAIN);
    $pagenavi_options['last_text'] = __('Last Page',THEME_DOMAIN);
    $pagenavi_options['next_text'] = '<strong class="next page-numbers">'.__('Next <span class="meta-nav">&rarr;</span>',THEME_DOMAIN).'</strong>';
    $pagenavi_options['prev_text'] = '<strong class="prev page-numbers">'.__('<span class="meta-nav">&larr;</span> Previous',THEME_DOMAIN).'</strong>';
    $pagenavi_options['dotright_text'] = '...';
    $pagenavi_options['dotleft_text'] = '...';
    $pagenavi_options['num_pages'] = 5; //continuous block of page numbers
    $pagenavi_options['always_show'] = 0;
    $pagenavi_options['num_larger_page_numbers'] = 0;
    $pagenavi_options['larger_page_numbers_multiple'] = 5;
 
    if (!is_single()) {
        $request = $wp_query->request;
        $posts_per_page = intval(get_query_var('posts_per_page'));
        $paged = intval(get_query_var('paged'));
        $numposts = $wp_query->found_posts;
        $max_page = $wp_query->max_num_pages;
 
        if(empty($paged) || $paged == 0) {
            $paged = 1;
        }
 
        $pages_to_show = intval($pagenavi_options['num_pages']);
        $larger_page_to_show = intval($pagenavi_options['num_larger_page_numbers']);
        $larger_page_multiple = intval($pagenavi_options['larger_page_numbers_multiple']);
        $pages_to_show_minus_1 = $pages_to_show - 1;
        $half_page_start = floor($pages_to_show_minus_1/2);
        $half_page_end = ceil($pages_to_show_minus_1/2);
        $start_page = $paged - $half_page_start;
 
        if($start_page <= 0) {
            $start_page = 1;
        }
 
        $end_page = $paged + $half_page_end;
        if(($end_page - $start_page) != $pages_to_show_minus_1) {
            $end_page = $start_page + $pages_to_show_minus_1;
        }
        if($end_page > $max_page) {
            $start_page = $max_page - $pages_to_show_minus_1;
            $end_page = $max_page;
        }
        if($start_page <= 0) {
            $start_page = 1;
        }
 
        $larger_per_page = $larger_page_to_show*$larger_page_multiple;
        //templ_round_num() custom function - Rounds To The Nearest Value.
        $larger_start_page_start = (templ_round_num($start_page, 10) + $larger_page_multiple) - $larger_per_page;
        $larger_start_page_end = templ_round_num($start_page, 10) + $larger_page_multiple;
        $larger_end_page_start = templ_round_num($end_page, 10) + $larger_page_multiple;
        $larger_end_page_end = templ_round_num($end_page, 10) + ($larger_per_page);
 
        if($larger_start_page_end - $larger_page_multiple == $start_page) {
            $larger_start_page_start = $larger_start_page_start - $larger_page_multiple;
            $larger_start_page_end = $larger_start_page_end - $larger_page_multiple;
        }
        if($larger_start_page_start <= 0) {
            $larger_start_page_start = $larger_page_multiple;
        }
        if($larger_start_page_end > $max_page) {
            $larger_start_page_end = $max_page;
        }
        if($larger_end_page_end > $max_page) {
            $larger_end_page_end = $max_page;
        }
        if($max_page > 1 || intval($pagenavi_options['always_show']) == 1) {
             $pages_text = str_replace("%CURRENT_PAGE%", number_format_i18n($paged), @$pagenavi_options['pages_text']);
            $pages_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pages_text);
			previous_posts_link($pagenavi_options['prev_text']);
       
            if ($start_page >= 2 && $pages_to_show < $max_page) {
                $first_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['first_text']);
                echo '<a href="'.esc_url(get_pagenum_link()).'" class="first page-numbers" title="'.$first_page_text.'">'.$first_page_text.'</a>';
                if(!empty($pagenavi_options['dotleft_text'])) {
                    echo '<span class="expand page-numbers">'.$pagenavi_options['dotleft_text'].'</span>';
                }
            }
 
            if($larger_page_to_show > 0 && $larger_start_page_start > 0 && $larger_start_page_end <= $max_page) {
                for($i = $larger_start_page_start; $i < $larger_start_page_end; $i+=$larger_page_multiple) {
                    $page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
                    echo '<a href="'.esc_url(get_pagenum_link($i)).'" class="page-numbers" title="'.$page_text.'">'.$page_text.'</a>';
                }
            }
 
            for($i = $start_page; $i  <= $end_page; $i++) {
                if($i == $paged) {
                    $current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
                    echo '<a  class="current page-numbers">'.$current_page_text.'</a>';
                } else {
                    $page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
                    echo '<a href="'.esc_url(get_pagenum_link($i)).'" class="page-numbers" title="'.$page_text.'"><strong>'.$page_text.'</strong></a>';
                }
            }
 
            if ($end_page < $max_page) {
                if(!empty($pagenavi_options['dotright_text'])) {
                    echo '<span class="expand page-numbers">'.$pagenavi_options['dotright_text'].'</span>';
                }
                $last_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['last_text']);
                echo '<a class="page-numbers" href="'.esc_url(get_pagenum_link($max_page)).'" title="'.$last_page_text.'">'.$last_page_text.'</a>';
            }
           
            if($larger_page_to_show > 0 && $larger_end_page_start < $max_page) {
                for($i = $larger_end_page_start; $i <= $larger_end_page_end; $i+=$larger_page_multiple) {
                    $page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
                    echo '<a href="'.esc_url(get_pagenum_link($i)).'" class="page-numbers" title="'.$page_text.'">'.$page_text.'</a>';
                }
            }
            echo $after;
			 next_posts_link($pagenavi_options['next_text'], $max_page);
        }
    }
}
/*
@Action: before_listing_page_setting
@Function: directory_before_listing_page_setting_callback
@return: Print option for display view for listing page.(list,grid)
*/
add_action('before_listing_page_setting','directory_before_listing_page_setting_callback');
if(!function_exists('directory_before_listing_page_setting_callback')){
	function directory_before_listing_page_setting_callback(){ 
		$get_plug_data = get_option('templatic_settings');
		$googlemap_setting=get_option('city_googlemap_setting');
?>
<tr>
  <th><label>
      <?php _e('Default page view',THEME_DOMAIN); ?>
    </label></th>
  <td><label for="default_page_view1">
      <input type="radio" id="default_page_view1" name="default_page_view" value="gridview" <?php if( @$get_plug_data['default_page_view']=='gridview') echo "checked=checked";?> />
      <?php _e('Grid',THEME_DOMAIN); ?>
    </label>
    &nbsp;&nbsp;&nbsp;
    <label for="default_page_view2">
      <input type="radio" id="default_page_view2" name="default_page_view" value="listview" <?php if( @$get_plug_data['default_page_view']== "" || $get_plug_data['default_page_view']=='listview') echo "checked=checked";?> />
      <?php _e('List',THEME_DOMAIN); ?>
    </label>
    <?php if( is_plugin_active('Tevolution-Directory/directory.php') && $googlemap_setting['category_googlemap_widget']=='yes'){ ?>
    &nbsp;&nbsp;&nbsp;
    <label for="default_page_view3">
      <input type="radio" id="default_page_view3" name="default_page_view" value="mapview" <?php if( $get_plug_data['default_page_view']== "" || $get_plug_data['default_page_view']=='mapview') echo "checked=checked";?> />
      <?php _e('Map',THEME_DOMAIN); ?>
    </label>
    <?php } ?>
   </td>
</tr>
<?php
	}
}
//add_action('admin_notices','admin_footer_message');
function admin_footer_message(){
	global $parent_file;
	if($parent_file == 'tools.php'){
		_e("<div id='message' class='updated below-h2'><p>Use WordPress Importer if you are using XML file given with the Tevolution-Directory plugin.</p></div>",THEME_DOMAIN);
	}	
}
/* search for filter for 404 page*/
add_filter( 'get_search_form', 'search_form_for_404_display' );
function search_form_for_404_display($searchform){
	$searchform ='<div class="404_search">
			<form role="search" method="get" class="search-form" action="' . esc_url( home_url( '/' ) ) . '">
			   <input type="text" value="" name="s" id="search_near" class="searchpost" PLACEHOLDER="'.__('Search',DOMAIN).'" />
               <input type="text" name="location" id="location" class="location" PLACEHOLDER="'.__('Address',DOMAIN).'" value="" />
			   <input type="hidden" name="post_type" value="listing" />
			   <input type="hidden" name="nearby" value="search" />
               <input type="submit" alt="" class="sgo" value="'.__('Search',DOMAIN).'" />
          </form></div>';
	return $searchform;
}

/*
 * Function Name: remove_woocommerce_script
 * Return : remove woocommece js from listing and detail page
 */

add_action('wp_head','remove_woocommerce_script');
function remove_woocommerce_script(){
	if(is_plugin_active('woocommerce/woocommerce.php')){
		if(!is_woocommerce() && !is_checkout() && !is_cart()){
			wp_deregister_script( 'jquery-cookie' );
			wp_deregister_script( 'jquery-blockui' );
			wp_deregister_script( 'wc-cart-fragments' );
			wp_deregister_script( 'wc-add-to-cart' );
			wp_deregister_script( 'jquery-placeholder' );
		}
	}
}

/*
 * Function Name: directory_add_custom_post_field
 * Return: add google map marker option in add/edit taxonomy page on backend.
 */
add_action('tevolution_add_custom_post_field','directory_add_custom_post_field');
function directory_add_custom_post_field($edit_post){
	global $wpdb;
	$tevolution_taxonomy_marker=get_option('tevolution_taxonomy_marker');	
	$taxonomy=$edit_post[@$_REQUEST['post-type']]['slugs'][0];
	
	/* directory plugin and event plugin taxonomy available then return marker custom field */
	if($taxonomy=='ecategory' || $taxonomy=='listingcategory'){
		return $edit_post;	
	}
	?>
     <tr>
     	<td valign="top"><label for="marker" class="form-textarea-label"><?php _e('Categorywise Map Markers',THEME_DOMAIN) ;?></label></td>
          <td>
          	<input type="checkbox" id="taxonomy_marker" name="taxonomy_marker" value="enable" <?php if($taxonomy!='' && $tevolution_taxonomy_marker[$taxonomy]=='enable'){echo 'checked';}?> />&nbsp;<label for="taxonomy_marker"><?php _e('Enable the option to assign different map markers to different categories of this post type',THEME_DOMAIN);?></label>
          </td>
     </tr>
     <?php	
	if(isset($_POST['submit-taxonomy']) && $_POST['submit-taxonomy'] !='' && isset($_POST['taxonomy_marker']) && $_POST['taxonomy_marker']!='')	{
		
		$tevolution_taxonomy_marker=get_option('tevolution_taxonomy_marker');
		if($tevolution_taxonomy_marker){
			$taxonomy_marker=array($_POST['taxonomy_slug']=>$_POST['taxonomy_marker']);
			$tevolution_taxonomy_marker=array_merge($tevolution_taxonomy_marker,$taxonomy_marker);
			update_option('tevolution_taxonomy_marker',$tevolution_taxonomy_marker);
		}else{
			$tevolution_taxonomy_marker=array($_POST['taxonomy_slug']=>$_POST['taxonomy_marker']);
			update_option('tevolution_taxonomy_marker',$tevolution_taxonomy_marker);
		}
		
	}else{
		$tevolution_taxonomy_marker=get_option('tevolution_taxonomy_marker');
		unset($tevolution_taxonomy_marker[$_POST['taxonomy_slug']]);
		update_option('tevolution_taxonomy_marker',$tevolution_taxonomy_marker);
	}
}

/*
 * Function Name: tevolution_add_marker_fields 
 * Return: display marker upload field in category page on backend
 */
add_action('admin_init','tevolution_add_marker_fields');
function tevolution_add_marker_fields(){
	$tevolution_taxonomy_marker=get_option('tevolution_taxonomy_marker');
	
	if(!empty($tevolution_taxonomy_marker)){
		foreach($tevolution_taxonomy_marker as $key=>$value){
			add_action('edited_'.$key,'marker_custom_fields_AlterFields');
			add_action('created_'.$key,'marker_custom_fields_AlterFields');
			add_filter('manage_'.$key.'_custom_column', 'manage_marker_category_columns', 10, 3);
			add_filter('manage_edit-'.$key.'_columns', 'marker_category_columns');
			
			if(isset($_GET['taxonomy']) && ($_GET['taxonomy']== $key)) 
			{
				$taxnow=$_GET['taxonomy'];
				add_action($taxnow.'_edit_form_fields','marker_custom_fields_EditFields',11);
				add_action($taxnow.'_add_form_fields','marker_custom_fields_AddFieldsAction',11);		
			}
		}
	}
}
/*
Name:directory_theme_breadcrumb
Desc: return breadcrumb
*/
function directory_theme_breadcrumb(){
	global $wpdb,$post;
	if ( current_theme_supports( 'breadcrumb-trail' ) && supreme_get_settings('supreme_show_breadcrumb')) breadcrumb_trail( array( 'separator' => '&raquo;' ) );
}

function marker_custom_fields_EditFields($tag)
{
	marker_custom_fields_AddFields($tag,'edit');	
}
function marker_custom_fields_AddFieldsAction($tag)
{
	marker_custom_fields_AddFields($tag,'add');
}

/*
 * Function Name: marker_custom_fields_AddFields
 * display custom field in event and listing category page
 */
function marker_custom_fields_AddFields($tag,$screen)
{	
	$tax = @$tag->taxonomy;
	?>
     	<div class="form-field-category">
		<tr class="form-field form-field-category">
			<th scope="row" valign="top"><label for="cat_icon"><?php _e("Map Marker", THEME_DOMAIN); ?></label></th>
			<td> 
                    <input id="cat_icon" type="text" size="60" name="cat_icon" value="<?php echo (@$tag->term_icon)? @$tag->term_icon:''; ?>"/>	
                    <?php _e('Or',DOMAIN);?>
                    <a class="button upload_button" title="Add city background image" id="cat_icon" data-editor="cat_upload_icon" href="#">
                    <span class="wp-media-buttons-icon"></span><?php _e('Browse',THEME_DOMAIN);?>	</a>		
                    <p class="description"><?php _e('It will appear on the homepage Google map for listings placed in this category. ',DOMAIN);?></p>    
			</td>
		</tr>
		</div>
	<?php
}

/*
 * Function Name: marker_custom_fields_AlterFields
 * add/ edit listing and event custom taxonomy custom field 
 */
function marker_custom_fields_AlterFields($termId)
{
	global $wpdb;
	$term_table=$wpdb->prefix."terms";		
	$cat_icon=$_POST['cat_icon'];		
	//update the service price value in terms table field	
	if(isset($_POST['cat_icon'])){
		$sql="update $term_table set term_icon='".$cat_icon."' where term_id=".$termId;
		$wpdb->query($sql);
	}
	
}

/*
 * Function Name: marker_category_columns
 * manage columns for event and listing custom taxonomy
 */
function marker_category_columns($columns)
{
	$columns['icon'] = __('Map Marker',THEME_DOMAIN);
	return $columns;	
}

/*
 * Function Name: manage_marker_category_columns
 * display listing and event custom taxonomy custom field display in category columns
 */
function manage_marker_category_columns($out, $column_name, $term_id){
	global $wpdb;
	$term_table=$wpdb->prefix."terms";		
	$sql="select * from $term_table where term_id=".$term_id;
	$term=$wpdb->get_results($sql);	
	
	switch ($column_name) {
		case 'icon':					
				 $out= ($term[0]->term_icon)?'<img src="'.$term[0]->term_icon.'" >':'<img src="'.TEVOLUTION_DIRECTORY_URL.'images/pin.png" >';
			break; 
		default:
			break;
	}
	return $out;	
}

add_filter('get_theme_layout','directory_custom_page_layout');

function directory_custom_page_layout($global_layout){

	if(isset($_REQUEST['page']) && ($_REQUEST['page']=='preview' || $_REQUEST['page']=='success')){		
		$global_layout=supreme_plugin_layouts( $global_layout );
	}
	return $global_layout;
}


/*
 * Function Name: directory_site_get_avatar
 * Return: profile_photo user custom filed not blank then display user custom photo.
 */
add_filter('get_avatar', 'directory_site_get_avatar', 10, 5);
function directory_site_get_avatar($avatar, $id_or_email, $size, $default, $alt){
	if(get_user_meta($id_or_email,'profile_photo',true)){		
		$imgpath = get_user_meta($id_or_email,'profile_photo',true);
	     $avatar = "<img class='avatar avatar-".$size." photo' src='".$imgpath."' alt='".$alt."' height='".$size."' width='".$size."' />";
	}	
	return $avatar;

}
?>