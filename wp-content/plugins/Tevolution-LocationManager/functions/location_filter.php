<?php
/*
 * Function Name: location_multicity_get_posts
 * Return: check current detail page in given city if current detail post not in given city url slug then redirect to home page
 */
add_action('wp_head','location_multicity_get_posts',0);
function location_multicity_get_posts($query){
	global $wp_query, $post,$current_cityinfo,$wpdb;
	$multicity_table = $wpdb->prefix . "multicity";
	if(!is_admin() && is_single() && get_post_type()!='' && get_post_type()!='post'){
		$location_post_type=implode(',',get_option('location_post_type'));
		if (strpos($location_post_type,','.get_post_type()) !== false) {
			$multi_citry_id=explode(',',get_post_meta($post->ID,'post_city_id',true));			
			if(strstr(urldecode($_SERVER['REQUEST_URI']),'/city/')){
				$current_city = explode('/city/',$_SERVER['REQUEST_URI']);
				if(strstr($current_city[1],'/')){
					$current_city = explode('/',$current_city[1]);
					$current_city = str_replace('/','',$current_city[0]);
				}else{
					$current_city = str_replace('/','',$current_city[1]);
				}
			}
			$sql=$wpdb->prepare("SELECT city_id FROM $multicity_table where city_slug=%s",urldecode($current_city));
			
			$default_city = $wpdb->get_results($sql);
			if (!in_array($default_city[0]->city_id,$multi_citry_id)){			
				wp_redirect(site_url());
				exit;
			}
		}
	}
}

add_action('pre_get_posts','location_pre_get_posts',12);
function location_pre_get_posts($query){
	global $wp_query, $post,$current_cityinfo;	
	
	if(!is_admin()){
		$tmpdata = get_option('templatic_settings');
		/* its only for archive category and tag listing page */
		if((is_archive() || is_category() || is_tax()) && (!is_search() && !is_author() && !is_home() && !is_page() && !is_single())){
			/*
			 * Set the flag if any post type set as a location wise display on archive ,category and tag listing page
			 * if Tevolution home listing type value blank then check by default wordpress post type
			 */
			
			$flg=0;
			$location_post_type=implode(',',get_option('location_post_type'));
			if(isset($query->query_vars['post_type']) && $query->query_vars['post_type']!=''){
				if (strpos($location_post_type,','.$query->query_vars['post_type']) !== false) {
				   $flg=1;
				}
			}else{
				if (strpos($location_post_type,','.$query->tax_query->queries[0]['taxonomy']) !== false) {
				   $flg=1;
				}	
			}
			/*
			 * if flag is set then multicity location where filter apply
			 */
			if($flg==1){
				add_filter('posts_where', 'location_multicity_where');
			}
		}		
		
		
		
		/* Is home page only */
		//if(is_home() && $query->query_vars['post_type']!='post' && (is_array($query->query_vars['post_type']) && !in_array('post',$query->query_vars['post_type'])) && $query->query_vars['post_type']!='')
		if(is_home())
		{	
		
			/*
			 * Set the flag if any post type set as a location wise display on home page.
			 * if Tevolution home listing type value blank then check by default wordpress post type
			 */
			$flg=0;
			
			$location_posttype=get_option('location_post_type');			
			for($j=0;$j<count($location_posttype);$j++){
				
					$location_type = explode(',',$location_posttype[$j]);					
					$location_post_type.=$location_type[0].',';
			}
			if($query->query_vars['post_type']!=''){
				$home_listing_type_value = implode(',',@$tmpdata['home_listing_type_value']);				
				if (strpos($location_post_type,$home_listing_type_value) !== false) {
				   $flg=1;
				}
			}else{
				$query->query_vars['post_type']='post';	
				if (strpos($location_post_type,$query->query_vars['post_type']) !== false) {
				   $flg=1;
				}
			}
			
			
			/*
			 * if flag is set then multicity location where filter apply
			 */
			if($flg==1){
				add_filter('posts_where', 'location_multicity_where');
			}
				
		}
	}
}
function location_city_filter($where){
	global $wpdb,$cityid;
	$where .= " AND $wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='post_city_id' and FIND_IN_SET( ".$cityid.", pm.meta_value ))";
	return $where;
}
function location_multicity_where($where){
	
	global $wpdb,$country_table,$zones_table,$multicity_table,$city_log_table,$current_cityinfo,$wp_query;	
	/* latest post -page start */
	if(is_home() ){	// in home page city could not be save we should refresh the page
	
		if(strstr($_SERVER['REQUEST_URI'],'/city/')){
			$current_city = explode('/city/',$_SERVER['REQUEST_URI']);	
	
			if(strstr($current_city[1],'/')){
				$current_city = explode('/',$current_city[1]);
				$current_city = str_replace('/','',$current_city[0]);
			}else{
				$current_city = str_replace('/','',$current_city[1]);
			}
			$wp_query->set('city',$current_city);				
		}
		$multicity_table = $wpdb->prefix . "multicity";	
		if($wpdb->get_var("SHOW TABLES LIKE '$multicity_table'") == $multicity_table) {
			if(strstr($_SERVER['REQUEST_URI'],'/city/')){			
				$sql="SELECT * FROM $multicity_table where city_slug='".get_query_var('city')."'";
			}elseif(isset($_SESSION['post_city_id']) && $_SESSION['post_city_id']!=''){
				if(get_query_var('city')!='')
					$sql=$wpdb->prepare("SELECT * FROM $multicity_table where city_slug=%s",get_query_var('city'));
				else
					$sql=$wpdb->prepare("SELECT * FROM $multicity_table where city_id=%d",$_SESSION['post_city_id']);
				
			}else{			
				$sql="SELECT * FROM $multicity_table where is_default=1";		
			}
		}
		$default_city = $wpdb->get_results($sql);
		$default_city_id=$default_city[0]->city_id;
		
		$where .= " AND $wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='post_city_id' and FIND_IN_SET( ".$default_city_id.", pm.meta_value ))";
	/* latest post -page end */
	}else{ 
		if($current_cityinfo['city_id']!=''){		
			//$where .= " AND $wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='post_city_id' and pm.meta_value like'%".$current_cityinfo['city_id']."%' )";
			$where .= " AND $wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='post_city_id' and FIND_IN_SET( ".$current_cityinfo['city_id'].", pm.meta_value ))";
		} 
		if(isset($_REQUEST['radius']) && $_REQUEST['radius'] !=''){
		
		}
	}
	
	return $where;
}
/*
 * Function Name: location_related_posts_where_filter
 * Return: near by location where filter return
 */
function location_related_posts_where_filter($where){
	global $wpdb,$wp_query,$post,$current_cityinfo;
	$search = get_post_meta($post->ID,'address',true);	
	
	$where .= " AND $wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='post_city_id' and FIND_IN_SET( ".$current_cityinfo['city_id'].", pm.meta_value ))";
	if($search!=""){		
		$lat =get_post_meta($post->ID,'geo_latitude',true);
		$long = get_post_meta($post->ID,'geo_longitude',true);
		
		$tmpdata = get_option('templatic_settings');
		$miles = $tmpdata['related_radius'];
		$tbl_postcodes = $wpdb->prefix . "postcodes";		
		
		if($search){
			$where .= " AND ($wpdb->posts.ID in (SELECT post_id FROM  $tbl_postcodes WHERE truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) <= ".$miles." ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) ASC))";			
			
		}	
	}	
	
	return $where;
}
/*
 * Function Name: location_post_listing_post_where
 * Return : include multilocation post where filter on post listing widget
 */
add_action('post_listing_widget_before_post_where','location_post_listing_post_where');
function location_post_listing_post_where($instance){	
	$location_post_type=implode(',',get_option('location_post_type'));
	if (strpos($location_post_type,$instance['post_type']) !== false) {
	 	add_filter('posts_where', 'location_multicity_where');
	}
}
add_action('post_listing_widget_after_post_where','remove_location_post_listing_post_where');
function remove_location_post_listing_post_where($instance){	
	$location_post_type=implode(',',get_option('location_post_type'));
	if (strpos($location_post_type,$instance['post_type']) !== false) {
	 	remove_filter('posts_where', 'location_multicity_where');
	}
}

/*
 * Function Name: location_comments_clauses
 * Return: display comment review city wise
 */

function location_comments_clauses($pieces){
	
	global $wpdb,$country_table,$zones_table,$multicity_table,$city_log_table,$current_cityinfo,$wp_query;	
	$pieces['where'] .= " AND $wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='post_city_id' and FIND_IN_SET( ".$current_cityinfo['city_id'].", pm.meta_value ))";	
	return $pieces;
}
?>