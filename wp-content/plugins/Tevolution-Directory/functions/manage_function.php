<?php
/*
 * Create the need plugin table create
 *
 */
include_once(TEVOLUTION_DIRECTORY_DIR.'functions/manage_category_customfields.php'); 
include_once(TEVOLUTION_DIRECTORY_DIR.'functions/widget_functions.php'); 
include_once(TEVOLUTION_DIRECTORY_DIR.'functions/directory_functions.php');
include_once(TEVOLUTION_DIRECTORY_DIR.'functions/directory_filters.php');
include_once(TEVOLUTION_DIRECTORY_DIR.'functions/directory_page_templates.php');
include_once(TEVOLUTION_DIRECTORY_DIR.'functions/directory_listing_functions.php');
include_once(TEVOLUTION_DIRECTORY_DIR.'functions/directory_single_functions.php');
 
add_action('admin_init','tables_creatation');
function tables_creatation(){
	global $wpdb,$country_table,$zones_table,$multicity_table,$city_log_table,$pagenow;
	/*
	 * Create postcodes table and save the sorting option in templatic setting on plugin page or tevolution system menu page
	 */
	if($pagenow=='plugins.php' || $pagenow=='themes.php' || (isset($_REQUEST['page']) && ($_REQUEST['page']=='templatic_system_menu' || $_REQUEST['page']=='templatic_settings'))){
	
		/*MultiCity Table Creation BOF */
		$postcodes_table = $wpdb->prefix . "postcodes";	
		if($wpdb->get_var("SHOW TABLES LIKE \"$postcodes_table\"") != $postcodes_table) {
			$postcodes_table = "CREATE TABLE IF NOT EXISTS $postcodes_table (
			  pcid bigint(20) NOT NULL AUTO_INCREMENT,
			  post_id bigint(20) NOT NULL,
			  post_type varchar(100) NOT NULL,
			  address varchar(255) NOT NULL,
			  latitude varchar(255) NOT NULL,
			  longitude varchar(255) NOT NULL,
			  PRIMARY KEY (pcid)
			)DEFAULT CHARSET=utf8";
			$wpdb->query($postcodes_table);			
		}
		
		/*directory Setting option */
		$templatic_settings=get_option('templatic_settings');
		if($templatic_settings=='' || empty($templatic_settings)){		
			$templatic_settings=array('sorting_type'   => 'select',
								 'category_map'   => 'yes',
								 'sorting_option' => array('title_asc','title_desc','date_asc','date_desc','random','stdate_low_high','stdate_high_low'),
								 );
			
			update_option('templatic_settings',$templatic_settings);
			
		}
		/*finish directory setting option */
	}
}
add_action('admin_head','manage_function_style',true); // to call the css on top
add_action('wp_head','manage_function_headstyle',true); // to call the css on top
/* Function to add the css on top -in admin header*/
function manage_function_style(){
	
	
	wp_enqueue_style('thickbox'); 
}
/* end */
/* Function to add the css on top -in admin header*/
function manage_function_headstyle(){
	global $pagenow,$post,$wp_query;
	$current_term = $wp_query->get_queried_object();		
	wp_enqueue_style('directory_style',TEVOLUTION_DIRECTORY_URL.'css/directory.css');
	
}
/* end */
/*
 * Function Name:directory_multisity_custom_field_save
 * Save the multisite id, country id, zone id when admin user update or new create listing.
 */
add_action('save_post','directory_multisity_custom_field_save',12);
function directory_multisity_custom_field_save($post_id){
	global $wpdb;
	$post_type= @$_POST['post_type'];
	
	if(isset($_POST['country_id']) && $_POST['country_id']!="")
		update_post_meta($_POST['post_ID'],'country_id',$_POST['country_id']);
	if(isset($_POST['zones_id']) && $_POST['zones_id']!="")
		update_post_meta($_POST['post_ID'],'zones_id',$_POST['zones_id']);
	if(isset($_POST['post_city_id']) && $_POST['post_city_id']!=""){
		$post_city_id=$_POST['post_city_id'];
		if(is_array($post_city_id)){
			$post_city_id	=implode(',',$post_city_id);
		}
		update_post_meta($_POST['post_ID'],'post_city_id',$post_city_id);
	}
	
	
	$post_address = (isset($_POST['address']))? @$_POST['address']:@$_SESSION['custom_fields']['address'];
	$latitude = (isset($_POST['geo_latitude']))? @$_POST['geo_latitude']:@$_SESSION['custom_fields']['geo_latitude'];
	$longitude = (isset($_POST['geo_longitude']))? @$_POST['geo_longitude']:@$_SESSION['custom_fields']['geo_longitude'];
	$pID = (isset($_POST['post_ID']))?$_POST['post_ID'] : $post_id;
	$post_type=get_post_type( $pID );
	if($post_address && $latitude && $longitude){
		$postcodes_table = $wpdb->prefix . "postcodes";	
		$pcid = $wpdb->get_var("select pcid from $postcodes_table where post_id = '".$pID."'");	
		if($pcid){
			$postcodes_update = "UPDATE $postcodes_table set 
				post_type='".$post_type."',
				address = '".$post_address."',
				latitude ='".$latitude."',
				longitude='".$longitude."' where pcid = '".$pcid."' and post_id = '".$pID."'";
				$wpdb->query($postcodes_update);				
			}
		else
		{
			$postcodes_insert = 'INSERT INTO '.$postcodes_table.' set 
					pcid="",
					post_id="'.$pID.'",
					post_type="'.$post_type.'",
					address = "'.$post_address.'",
					latitude ="'.$latitude.'",
					longitude="'.$longitude.'"';			
					$wpdb->query($postcodes_insert);
		}
	}	
}
/* 
 * Function Name: directory_import_insert_post
 * Return: insert postcodes table when import xml data by wordpress import plugin
 */
add_action('wp_import_insert_post','directory_import_insert_post',10,4);
function directory_import_insert_post($post_id, $original_post_ID, $postdata, $post){
	
	global $wpdb;	
	foreach($post['postmeta'] as $key=>$val){		
		if($val['key']=='address'){
			$post_address=$val['value'];	
		}
		if($val['key']=='geo_latitude'){
			$latitude=$val['value'];	
		}
		if($val['key']=='geo_longitude'){
			$longitude=$val['value'];	
		}		
	}
	
	if($post_address && $latitude && $longitude){
		$postcodes_table = $wpdb->prefix . "postcodes";	
		$pcid = $wpdb->get_results($wpdb->prepare("select pcid from $postcodes_table where post_id = %d",$post_id));
		if(count($pcid)!=0){
			$wpdb->update($postcodes_table , array('post_type' => $post['post_type'],'address'=>$post_address,'latitude'=> $latitude,'longitude'=> $longitude), array('pcid' => $pcid,'post_id'=>$post_id) );	
		}else{
			$wpdb->query( $wpdb->prepare("INSERT INTO $postcodes_table ( post_id,post_type,address,latitude,longitude) VALUES ( %d, %s, %s, %s, %s)", $post_id,$post['post_type'],$post_address,$latitude,$longitude ) );
		}
	}
}
/*
 * Function :get_default_city_id
 * Return: default city name
 */
function get_default_city_id()
{
	global $wpdb,$country_table,$zones_table,$multicity_table;
	if(isset($_GET['city_name']) && $_GET['city_name']!=""){
		
	}else{
		$wpdb->get_var("select $option_name from $multicity_db_table_name where city_id=\"$id\"");
	}
	
	return $city_name;
}
// Added Ad locations for AdManager plugin to display Ad: Start
//Filter to add Ad locations for post detail pages
add_filter('add_theme_detail_locations','directory_add_theme_detail_locations_callback',12);
//Filter to add Ad locations for category pages
add_filter('add_theme_category_locations','directory_add_theme_category_locations_callback',12);
if(!function_exists('directory_add_theme_detail_locations_callback')){
	function directory_add_theme_detail_locations_callback($directory_locations){
		$directory_locations['listing'] = array(
													"directory_before_post_title"	=>	'Above Listing Detail Title',
													"directory_after_post_title"	=>	'Below Listing Detail Title',
													"directory_before_post_content"	=>	'Above Listing Detail Description',
													"directory_after_post_content"	=>	'Below Listing Detail Description',
													"directory_before_post_loop"	=>	'Above Listings Detail Page',
													"directory_after_post_loop"		=>	'Below Listings Detail Page',
									);
		return $directory_locations;
	}
}
if(!function_exists('directory_add_theme_category_locations_callback')){
	function directory_add_theme_category_locations_callback($directory_category_locations){
		
		$directory_category_locations['listing'] = array(
													"directory_before_categories_title"	=>	'Above Listing Category Title',
													"directory_after_categories_title"	=>	'Below Listing Category Title',
													"directory_before_categories_description"	=>	'Above Listing Category Description',
													"directory_after_categories_description"	=>	'Below Listing Category Description',
													"directory_before_loop_taxonomy"	=>	'Above Listings on Category Page',
													"directory_after_loop_taxonomy"	=>	'Below Listings on Category Page'
									);
		return $directory_category_locations;
	}
}
// Added Ad locations for AdManager plugin to display Ad: End
/*
Name : directory_full_url
Desc : Get the full url
*/
function directory_full_url()
{
    global $wp_query;
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
    $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    $host = (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST']))? $_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];
	$current_term = $wp_query->get_queried_object();
	
	if(!is_tax() && is_archive() && !is_search())
	{			
		$post_type=(get_post_type()!='')? get_post_type() : get_query_var('post_type');
		$permalink = get_post_type_archive_link($post_type);
		$permalink=str_replace('&event_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
	}elseif(is_search()){
		$search_query_str=str_replace('&directory_sortby=alphabetical&sortby='.@$_REQUEST['sortby'],'',$_SERVER['QUERY_STRING']);
		$permalink= site_url()."?".$search_query_str;
	}else{
		$permalink=($current_term->slug) ?  get_term_link($current_term->slug, $current_term->taxonomy):'';
		if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!='')
			$permalink=str_replace('&directory_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
		
	}
	
	
	if(false===strpos($permalink,'?')){
	    $url_glue = '?';
	}else{
		$url_glue = '&amp;';	
	}
    return $permalink.$url_glue;
}
?>