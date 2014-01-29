<?php
add_action('pre_get_posts','directory_pre_get_posts',12);
function directory_pre_get_posts($query){	
	global $wp_query, $post,$current_cityinfo;	
	/*Call function for set the current city info variable */	
	$tmpdata = get_option('templatic_settings');
	if(!is_author()){
		$current_term = $wp_query->get_queried_object();		
		$current_term = $wp_query->get_queried_object();		
	}
	if(!is_admin()){
		if((!is_search() && ! is_author())&&  (isset($query->query_vars['post_type']) && $query->query_vars['post_type']==CUSTOM_POST_TYPE_LISTING && is_archive() || (is_archive() && ((isset($current_term->taxonomy) && @$current_term->taxonomy == CUSTOM_CATEGORY_TYPE_LISTING || @$current_term->taxonomy == CUSTOM_TAG_TYPE_LISTING))))){						
			add_filter('posts_where', 'directory_sortby_where');
			add_filter('posts_orderby', 'directory_category_filter_orderby');
		}
		/* Is search condition */
		if(is_search() && (isset($_GET['nearby']) && $_GET['nearby']=='search')){		
			add_filter('posts_where', 'directory_search_filter_nearby');
		}
		if(is_search() && (isset($_GET['adv_city']) && $_GET['adv_city']!='')){		
			add_filter('posts_where', 'directory_advancesearch_filter_multicity',20);		
		}
		
		if(is_search() && (isset($_REQUEST['post_type']) && $_REQUEST['post_type']==CUSTOM_POST_TYPE_LISTING)){	
			add_filter('posts_where', 'directory_sortby_where');
			add_filter('posts_orderby', 'directory_category_filter_orderby');
		
		}
	}
}
/*
 * Function Name: directory_category_filter_orderby
 * Return: display the category page listing by ordering wise.
 */
function directory_category_filter_orderby($orderby){
	global $wpdb,$wp_query;		
	if (isset($_REQUEST['directory_sortby']) && ($_REQUEST['directory_sortby'] == 'title_asc' || $_REQUEST['directory_sortby'] == 'alphabetical'))
	{
		$orderby= "$wpdb->posts.post_title ASC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
	}
	elseif (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'title_desc' )
	{
		$orderby = "$wpdb->posts.post_title DESC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
	}
	elseif (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'date_asc' )
	{
		$orderby = "$wpdb->posts.post_date ASC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
	}
	elseif (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'date_desc' )
	{
		$orderby = "$wpdb->posts.post_date DESC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
	}
	elseif (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'stdate_low_high' )
	{
		$orderby = "(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id = $wpdb->posts.ID and $wpdb->postmeta.meta_key like \"st_date\") ASC";
	}
	elseif (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'stdate_high_low' )
	{
		$orderby = "(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id = $wpdb->posts.ID and $wpdb->postmeta.meta_key like \"st_date\") DESC";
	}elseif(isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'random' )
	{
		$orderby = "(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC,rand()";
	}elseif(isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'reviews' )
	{
		$orderby = 'DESC';
		$orderby = " comment_count $orderby,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
	}elseif(isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'rating' )
	{
		$orderby = " (select $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id = $wpdb->posts.ID and $wpdb->postmeta.meta_key like \"average_rating\") DESC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
	}
	else
	{
		$orderby = " (SELECT $wpdb->postmeta.meta_value from $wpdb->postmeta where ($wpdb->posts.ID = $wpdb->postmeta.post_id) AND $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC, $wpdb->posts.post_date DESC";
	}
	return $orderby;
}
/*
 * Function Name: directory_search_filter_nearby
 * Return: search widget for fetch the near by post through address
 */
function directory_search_filter_nearby($where){
	global $wpdb,$wp_query,$current_cityinfo;
	$search = str_replace(' ','',$_REQUEST['location']);
	if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; }
	$geocode = file_get_contents($http.'maps.google.com/maps/api/geocode/json?address='.$search.'&sensor=false');
	$output= json_decode($geocode);
	$lat = $output->results[0]->geometry->location->lat;
	$long = $output->results[0]->geometry->location->lng;
	
	$miles = @$_REQUEST['radius'];
	$saddress = @$_REQUEST['location'];
	
	if(isset($_REQUEST['radius_type']) && $_REQUEST['radius_type']== strtolower('Kilometer')){
		$miles = @$_REQUEST['radius'] * 0.621;
	}else{
		$miles = @$_REQUEST['radius'];	
	}
	$tbl_postcodes = $wpdb->prefix . "postcodes";
	if($saddress &&  !isset($_REQUEST['radius'])){
		$where .= " AND ($wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key like 'address' and pm.meta_value like \"%$saddress%\") )";			
	}
	
	
	if($saddress=='' && !empty($current_cityinfo)){		
		$lat=$current_cityinfo['lat'];
		$long=$current_cityinfo['lng'];
	}
	if(!empty($_REQUEST['post_type']) )
	{
		$post_type = implode(",",$_REQUEST['post_type']);
		$post_type_array = explode(",",$post_type);
		$sep = ",";
		for($i=0;$i<count($post_type_array);$i++)
		{
			if($i == (count($post_type_array) - 1))
			{
				$sep = "";
			}
			$post_type1 .= "'".$post_type_array[$i]."'".$sep;
		}
	}
	if($lat!='' && $long!='' && (isset($_REQUEST['radius']) && $_REQUEST['radius']!='')){
		if (function_exists('icl_register_string')) {
			$where .= " AND ($wpdb->posts.ID in (SELECT post_id FROM  $tbl_postcodes WHERE $tbl_postcodes.post_type in (".$post_type1.")  AND truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) <= ".$miles." ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) ASC))";
		}
		else
		{
			$where .= " AND ($wpdb->posts.ID in (SELECT post_id FROM  $tbl_postcodes WHERE $tbl_postcodes.post_type in (".$post_type1.") AND truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) <= ".$miles." ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) ASC))";
		}
	}else{
		if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
		{
			$where .= " AND $wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='post_city_id' and FIND_IN_SET( ".$current_cityinfo['city_id'].", pm.meta_value ))";
		}
	}
	//echo $where;
	return $where;
}
/*
 * Function Name: directory_advancesearch_filter_multicity
 *
 */
function directory_advancesearch_filter_multicity($where){
	global $wpdb;
	if(isset($_GET['adv_city']) && $_GET['adv_city']!=''){		
		$where .= " AND $wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='post_city_id' and FIND_IN_SET( ".$_GET['adv_city'].", pm.meta_value ))";
	}	
	return $where;
}
/*
 * Function Name: directory_nearby_filter
 * Return: display near by distance listing where condition
 */
function directory_nearby_filter($where){
	global $wpdb,$current_post,$miles,$post;
	//$geo_latitude=get_post_meta($current_post,'geo_latitude',true);
	$geo_latitude = (get_post_meta($post->ID,'geo_latitude',true))?get_post_meta($post->ID,'geo_latitude',true): $_SESSION['custom_fields']['geo_latitude'];
	//$geo_longitude=get_post_meta($current_post,'geo_longitude',true);
	$geo_longitude = (get_post_meta($post->ID,'geo_longitude',true))?get_post_meta($post->ID,'geo_longitude',true) : $_SESSION['custom_fields']['geo_longitude'];
	$postcode = $wpdb->prefix."postcodes";
	$where .= " AND ($wpdb->posts.ID in (SELECT post_id FROM $postcode WHERE truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$geo_latitude."')) + cos(radians(`latitude`)) * cos( radians('".$geo_latitude."')) * cos( radians(`longitude` - '".$geo_longitude."') ) ) ) * 69.09),1) <= ".$miles." ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$geo_latitude."')) + cos(radians(`latitude`)) * cos( radians('".$geo_latitude."')) * cos( radians(`longitude` - '".$geo_longitude."') ) ) ) * 69.09),1) ASC))";
	
	return $where;
}
/*
 * Function Name: directory_archive_search
 * Return : contact post where condition for archive search page
 */
function directory_archive_search(){
	global $wpdb;
	if(isset($_REQUEST['directory_keywords']) && $_REQUEST['directory_keywords']!=''){
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $_REQUEST['directory_keywords']) ) . '%\'';
	}
	return $where;	
}
/*
 * Function Name:directory_sortby_where
 * Return: search by alphabetical
 */
function directory_sortby_where($where){
	global $wpdb,$wp_query;	
	if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!=''){
		$where .= "  AND $wpdb->posts.post_title like '".$_REQUEST['sortby']."%'";
	}
	return $where;
}
/*
 * Function Name: directory_listing_search_posts_where 
 * Return: find near my listing from current city
 */
function directory_listing_search_posts_where($where){
	global $wpdb,$wp_query,$post,$current_cityinfo;		
	
	$ip  = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
	$url = "http://freegeoip.net/json/$ip";
	$data=wp_remote_get( $url, array( 'timeout' => 120, 'httpversion' => '1.1' ) );
			
	if ($data) {
		$location = json_decode($data['body']);				
		$lat = $location->latitude;
		$long = $location->longitude;
	}else{
		$lat =$current_cityinfo['lat'];
		$long = $current_cityinfo['lng'];
	}
	$miles_range=explode('-',$_REQUEST['miles_range']);
	$to_miles = trim($miles_range[0]);
	$miles = trim($miles_range[1]);
	//$miles = $_REQUEST['miles_range'];
	$tbl_postcodes = $wpdb->prefix . "postcodes";		
	
	
	$where .= " AND ($wpdb->posts.ID in (SELECT post_id FROM  $tbl_postcodes WHERE truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) <= ".$miles." AND truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) >= ".$to_miles." ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) ASC))";			
	
	return $where;
}
function directory_feature_listing_orderby($orderby){
	global $wpdb,$wp_query,$post,$current_cityinfo;
	$orderby=" (SELECT distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where ($wpdb->posts.ID = $wpdb->postmeta.post_id) AND $wpdb->postmeta.meta_key = 'featured_h' AND $wpdb->postmeta.meta_value = 'h') DESC, $wpdb->posts.post_date DESC";
	return $orderby;
}
function wpml_listing_milewise_search_language($where)
{
	$language = ICL_LANGUAGE_CODE;
	$where .= " and t.language_code='".$language."'";
	return $where;
}
?>