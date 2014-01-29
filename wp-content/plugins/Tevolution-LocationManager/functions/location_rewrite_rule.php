<?php 
/* add  city wise permalink*/
add_action('init', 'templatic_add_rewrite_rules');
function templatic_add_rewrite_rules() {
	global $wp_rewrite;
	$wp_rewrite->add_rewrite_tag('%city%', '([^/]+)', 'city=');
	$pid = get_option('default_comments_page');
	if($pid =='last'){ $pid ='1'; }else{ $pid ='1';}
	$location_post_type=get_option('location_post_type');	
	if($location_post_type!='' ||!empty($location_post_type)){
		foreach($location_post_type as $post_type){
			$posttype=explode(',',$post_type);	
			$wp_rewrite->add_rewrite_tag('%'.$posttype[0].'%', '([^/]+)', $posttype[0].'=');
			//$wp_rewrite->add_rewrite_tag('%event%', '([^/]+)', 'event=');
			$wp_rewrite->add_permastruct($posttype[0], '/city/%city%/'.$posttype[0].'/%'.$posttype[0].'%', false);
			//$wp_rewrite->add_permastruct('event', '/city/%city%/event/%event%', false);
		}
		$wp_rewrite->flush_rules();
		if(function_exists('flush_rewrite_rules')){
			//
			$ver = filemtime( __FILE__ ); // Get the file time for this file as the version number
			$defaults = array( 'version' => 0, 'time' => time() );
			$r = wp_parse_args( get_option( __CLASS__ . '_flush', array() ), $defaults );
			if ( $r['version'] != $ver || $r['time'] + 86400 < time() ) { // Flush if ver changes or if 48hrs has passed.
				flush_rewrite_rules(true);  
				// trace( 'flushed' );
				$args = array( 'version' => $ver, 'time' => time() );
				if ( ! update_option( __CLASS__ . '_flush', $args ) )
					add_option( __CLASS__ . '_flush', $args );
			}
			//
		}
	}
}
/*
 * Function Name: templatic_create_permalinks
 * Return : post_city_id is available , add city name slug in permalink
 */
add_filter('post_type_link', 'templatic_create_permalinks', 10, 3);
function templatic_create_permalinks($permalink, $post, $leavename) {	
	global $current_cityinfo;
	$no_data = 'no-data';
	$post_id = $post->ID;
	$pcity_id = apply_filters('city_permalink_slug',get_post_meta($post->ID,'post_city_id',true));	
	
	if(($post->post_type != '' && $pcity_id!='') && ( empty($permalink) || in_array($post->post_status, array('draft', 'pending', 'auto-draft'))))
		return $permalink;
	
	//echo 
	$pcity_id = get_post_meta($post_id, 'post_city_id', true);
	
	global $wpdb,$city_info;
	$multicity_db_table_name = $wpdb->prefix . "multicity"; // DATABASE TABLE  MULTY CITY
	$pcity_id = get_post_meta($post->ID,'post_city_id',true);
	if(strstr($pcity_id,',')){
		$pcity_id_ = explode(',',$pcity_id);
		$pcity_id = $pcity_id_[0];
	}
	if(!is_admin() && !empty($pcity_id_) && is_array($pcity_id_) && in_array($current_cityinfo['city_id'],$pcity_id_)){
		$pcity_id=$current_cityinfo['city_id'];
	}
	if($pcity_id!=''){
		if(is_admin() || is_singular() || is_search())
			$city = strtolower($wpdb->get_var("SELECT city_slug FROM $multicity_db_table_name WHERE city_id =\"$pcity_id\""));	
		else
			$city = ($current_cityinfo['city_slug']!='' && !is_author())? $current_cityinfo['city_slug']:strtolower($wpdb->get_var("SELECT city_slug FROM $multicity_db_table_name WHERE city_id =\"$pcity_id\""));				
	}else{
		$city = 'na';
	}  
	$permalink = str_replace('%city%', $city, $permalink);	
	return $permalink;
}
/* Commnet post redirect link with location manager*/
add_filter('comment_post_redirect', 'redirect_after_comment');

/*
Name: redirect_after_comment
Desciption: Redirect on same listing page afetr post the comment ( With location manager city permalink)
*/
function redirect_after_comment($location)
{
	global $wpdb;
		$pid = get_option('default_comments_page');
	if($pid =='last'){ $pid ='1'; }else{ $pid ='2';}
	return $_SERVER["HTTP_REFERER"]."/#comment-".$wpdb->insert_id;
}
function directory_myfeed_request($qv) {
	if (isset($qv['feed']))
		$qv['post_type'] = get_post_types();
	return $qv;
}
add_filter('request', 'directory_myfeed_request');



?>