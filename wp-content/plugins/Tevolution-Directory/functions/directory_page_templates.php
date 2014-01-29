<?php
/*
 * Apply filter for custom post type archive page template
 * Function Name: tevolution_get_archive_page_template
 */
function directory_get_archive_page_template($archive_template)
{
	global $wpdb,$wp_query,$post;
	if(is_archive() && (get_post_type()==CUSTOM_POST_TYPE_LISTING || $wp_query->query_vars['post_type']==CUSTOM_POST_TYPE_LISTING))
	{	
		if ( file_exists(STYLESHEETPATH . '/archive-'.get_post_type(). '.php')) {
			
			$archive_template = STYLESHEETPATH . '/archive-'.get_post_type(). '.php';
			
		} else if ( file_exists(TEMPLATEPATH . '/archive-'.get_post_type(). '.php') ) {
			
			$archive_template = TEMPLATEPATH . '/archive-'.get_post_type(). '.php';
			
		}elseif( file_exists(TEVOLUTION_DIRECTORY_DIR . 'templates/archive-listing.php')){
			
			$archive_template = TEVOLUTION_DIRECTORY_DIR . 'templates/archive-listing.php';			
		}
	}		
	return $archive_template;
}
add_filter( "archive_template", "directory_get_archive_page_template",13) ;
/*
 * Apply filter for taxonomy page 
 * Function name: get_taxonomy_product_post_type_template
 */ 
function directory_get_taxonomy_page_template($taxonomy_template)
{	
	global $wpdb,$wp_query,$post;
	//fetch the current page taxonomy
	$current_term = $wp_query->get_queried_object();		
	
	if($current_term->taxonomy == CUSTOM_CATEGORY_TYPE_LISTING || get_post_type()==CUSTOM_POST_TYPE_LISTING)
	{
		if ( file_exists(STYLESHEETPATH . '/taxonomy-'.$current_term->taxonomy. '.php')) {
			
			$taxonomy_template = STYLESHEETPATH . '/taxonomy-'.$current_term->taxonomy. '.php';
			
		} else if ( file_exists(TEMPLATEPATH . '/taxonomy-'.$current_term->taxonomy. '.php') ) {
			
			$taxonomy_template = TEMPLATEPATH . '/taxonomy-'.$current_term->taxonomy. '.php';
			
		}else{
			
			$taxonomy_template = TEVOLUTION_DIRECTORY_DIR . 'templates/taxonomy-listingcategory.php';
			
		}
	}
     return $taxonomy_template;
}
add_filter( "taxonomy_template", "directory_get_taxonomy_page_template",13) ;
/*
 * Apply filter for taxonomy page for tags 
 * Function name: get_tevolution_tag_page_template
 */ 
function directory_get_tag_page_template($tags_template)
{	
	global $wpdb,$wp_query,$post;
	//fetch the current page taxonomy
	$current_term = $wp_query->get_queried_object();		
	
	if($current_term->taxonomy==CUSTOM_TAG_TYPE_LISTING || get_post_type()==CUSTOM_POST_TYPE_LISTING)
	{		
		if ( file_exists(STYLESHEETPATH . '/taxonomy-'.$current_term->taxonomy. '.php')) {
			
			$tags_template = STYLESHEETPATH . '/taxonomy-'.$current_term->taxonomy. '.php';			
			
		} else if ( file_exists(TEMPLATEPATH . '/taxonomy-'.$current_term->taxonomy. '.php') ) {
			
			$tags_template = TEMPLATEPATH . '/taxonomy-'.$current_term->taxonomy. '.php';
			
		}else{
			
			$tags_template = TEVOLUTION_DIRECTORY_DIR . 'templates/taxonomy-listingtags.php';
			
		}
	}	
	
     return $tags_template;
}
add_filter( "taxonomy_template", "directory_get_tag_page_template",11) ;
/*
 * Apply filter for single template page
 * Function name: get_tevolution_single_template
 */ 
function directory_get_single_template($single_template)
{	
	global $wpdb,$wp_query,$post;	
	if(get_post_type()==CUSTOM_POST_TYPE_LISTING)
	{			
		if ( file_exists(STYLESHEETPATH . '/single-'.get_post_type(). '.php')) {
			
			$single_template = STYLESHEETPATH . '/single-'.get_post_type(). '.php';			
			
		} else if ( file_exists(TEMPLATEPATH . '/single-'.get_post_type(). '.php') ) {
			
			$single_template = TEMPLATEPATH . '/single-'.get_post_type(). '.php';
			
		}else{
			
			$single_template = TEVOLUTION_DIRECTORY_DIR . 'templates/single-listing.php';
			
		}
	}	
	
     return $single_template;
}
add_filter( "single_template", "directory_get_single_template",13) ;
/*
 * Search Page template for only tevolution custom post type
 *
 */
add_filter( "search_template",'tevolution_get_search_template',11 );
function tevolution_get_search_template($search_template)
{	
	global $wpdb,$wp_query,$post;		
	$post_type=get_query_var('post_type');	
	
	
	//fetch the tevolution post type	
	$custom_post_type = tevolution_get_post_type();	
	if($post_type== CUSTOM_POST_TYPE_LISTING)
	{			
		if ( file_exists(STYLESHEETPATH . '/listing-search.php')) {
			
			$search_template = STYLESHEETPATH . '/listing-search.php';			
			
		}else if ( file_exists(TEMPLATEPATH . '/listing-search.php') ) {
			
			$search_template = TEMPLATEPATH . '/listing-search.php';
			
		}else{
			$search_template = TEVOLUTION_DIRECTORY_DIR. 'templates/listing-search.php';
		}
	}	
     return $search_template;
}
/*Add action get_template_part for listing template part */
add_action( 'get_template_part_directory-listing','single_directory_listing_template_part',12,2);
function single_directory_listing_template_part($slug,$name)
{
	
	if ( file_exists(STYLESHEETPATH . "/{$slug}-{$name}.php")) {
			
		$single_template = STYLESHEETPATH . "/{$slug}-{$name}.php";			
			
	}else if(file_exists(TEMPLATEPATH."/{$slug}-{$name}.php"))
	{
		$single_template = TEMPLATEPATH. "/{$slug}-{$name}.php";		
	}else{
		$single_template = TEVOLUTION_DIRECTORY_DIR. "templates/{$slug}-{$name}.php";		
		include_once($single_template);	
	}	
	
}
add_action( 'init', 'directory_custom_fields_preview' ,10);
function directory_custom_fields_preview()
{
	
	if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=''){
		$_REQUEST['cur_post_type']=get_post_type($_REQUEST['pid']);
	}
	if((isset($_REQUEST['page']) && $_REQUEST['page'] == "preview")  && isset($_REQUEST['cur_post_type']) && $_REQUEST['cur_post_type']==CUSTOM_POST_TYPE_LISTING)
	{
		if ( file_exists(STYLESHEETPATH . '/single-listing-preview.php')) {
			
			$single_template_preview = STYLESHEETPATH . '/single-listing-preview.php';			
			
		} else if ( file_exists(TEMPLATEPATH . '/single-listing-preview.php') ) {
			
			$single_template_preview = TEMPLATEPATH . '/single-listing-preview.php';
			
		}else{
			
			$single_template_preview = TEVOLUTION_DIRECTORY_DIR . 'templates/single-listing-preview.php';
			
		}
		include($single_template_preview);
		exit;
	}
}
add_action('directory_before_categories_title','directory_manager_listing_custom_field');
add_action('directory_before_archive_title','directory_manager_listing_custom_field');
function directory_manager_listing_custom_field(){
	global $wpdb,$post,$htmlvar_name,$pos_title;
	
	$cus_post_type = (isset($_REQUEST['action']))? CUSTOM_POST_TYPE_LISTING : get_post_type();	
	$heading_type = directory_fetch_heading_post_type($cus_post_type);
	if(count($heading_type) > 0)
	{
		foreach($heading_type as $key=>$heading)
		{	
		
			$htmlvar_name[$heading] = get_directory_listing_customfields($cus_post_type,$heading,$key);//custom fields for custom post type..
		}
	}
	return $htmlvar_name;
}
/*
 * Function name: get_directory_listing_customfields
 * Return: return array for event listing custom fields
 */
function get_directory_listing_customfields($post_type,$heading='',$heading_key=''){
	global $wpdb,$post,$posttitle;
	$args = array( 'post_type' => 'custom_fields',
				'posts_per_page' => -1	,
				'post_status' => array('publish'),
				'meta_query' => array('relation' => 'AND',
								array(
									'key'     => 'post_type_'.$post_type.'',
									'value'   => $post_type,
									'compare' => '=',
									'type'    => 'text'
								),		
								array(
									'key'     => 'is_active',
									'value'   =>  '1',
									'compare' => '='
								),
								array(
									'key'     => 'show_on_listing',
									'value'   =>  '1',
									'compare' => '='
								),
								array(
									'key'     => 'heading_type',
									'value'   =>  array('basic_inf',$heading),
									'compare' => 'IN'
								)
							),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value',
				'order' => 'ASC'
		);
	
	remove_all_actions('posts_where');		
	$post_query = null;
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	
		
	$post_query = get_transient( '_tevolution_query_taxo_'.trim($post_type).'_'.trim($heading_key) );
	if ( false === $post_query && get_option('tevolution_cache_disable') ==1 ){
		$post_query = new WP_Query($args);
		set_transient( '_tevolution_query_taxo_'.trim($post_type).'_'.trim($heading_key), $post_query, 12 * HOUR_IN_SECONDS );
	}elseif(get_option('tevolution_cache_disable')==''){
		$post_query = new WP_Query($args);
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	
	$htmlvar_name='';
	if($post_query->have_posts())
	{
		while ($post_query->have_posts()) : $post_query->the_post();
			$ctype = get_post_meta($post->ID,'ctype',true);
			$post_name=get_post_meta($post->ID,'htmlvar_name',true);
			$style_class=get_post_meta($post->ID,'style_class',true);
			$option_title=get_post_meta($post->ID,'option_title',true);
			$option_values=get_post_meta($post->ID,'option_values',true);
			$htmlvar_name[$post_name] = array( 'type'=>$ctype,
										'label'=> $post->post_title,
										'style_class'=>$style_class,
										'option_title'=>$option_title,
										'option_values'=>$option_values,
									);		
			$posttitle[] = $post->post_title;
		endwhile;
		wp_reset_query();
	}
	return $htmlvar_name;
	
}
/*
 * Function Name: directory_fetch_heading_post_type
 *
 */
function directory_fetch_heading_post_type($post_type){
	
	global $wpdb,$post;
	remove_all_actions('posts_where');
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	remove_action('pre_get_posts','location_pre_get_posts',12);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$heading_title = array();
	$args=
	array( 
	'post_type' => 'custom_fields',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key' => 'ctype',
			'value' => 'heading_type',
			'compare' => '=',
			'type'=> 'text'
		),
		array(
			'key' => 'post_type',
			'value' => $post_type,
			'compare' => 'LIKE',
			'type'=> 'text'
		)
		
	),
	'meta_key' => 'sort_order',	
	'orderby' => 'meta_value_num',
	'meta_value_num'=>'sort_order',
	'order' => 'ASC'
	);
	$post_query = null;
	remove_all_actions('posts_orderby');
	
	$post_query = get_transient( '_tevolution_query_heading_'.trim($post_type));
	if ( false === $post_query && get_option('tevolution_cache_disable')==1){
		$post_query = new WP_Query($args);
		set_transient( '_tevolution_query_heading_'.trim($post_type), $post_query, 12 * HOUR_IN_SECONDS );
	}elseif(get_option('tevolution_cache_disable')==''){
		$post_query = new WP_Query($args);
	}
	$post_meta_info = $post_query;	
	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
			$otherargs=
			array( 
			'post_type' => 'custom_fields',
			'posts_per_page' => -1	,
			'post_status' => array('publish'),
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'is_active',
					'value' => '1',
					'compare' => '=',
					'type'=> 'text'
				),
				array(
					'key' => 'heading_type',
					'value' => $post->post_title,
					'compare' => '=',
					'type'=> 'text'
				)
			));		
			
			$other_post_query = null;
			$htmlvar_name=get_post_meta(get_the_ID(),'htmlvar_name',true);
			$other_post_query = get_transient( '_tevolution_query_heading'.trim($post_type).'_'.trim($htmlvar_name) );
			if ( false === $other_post_query  && get_option('tevolution_cache_disable')==1) {
				$other_post_query = new WP_Query($otherargs);				
				set_transient( '_tevolution_query_heading'.trim($post_type).'_'.trim($htmlvar_name), $other_post_query, 12 * HOUR_IN_SECONDS );
			}elseif(get_option('tevolution_cache_disable')==''){				
				$other_post_query = new WP_Query($otherargs);
			}
			if(count($other_post_query->post) > 0)
			{
				$heading_title[$post->post_name] = $post->post_title;
			}
		endwhile;
		wp_reset_query();
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	return $heading_title;
}
?>