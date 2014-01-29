<?php
global $wp_query,$wpdb;
/**-- condition for activate bulk upload --**/
if((isset($_REQUEST['activated']) && $_REQUEST['activated'] == 'bulk_upload') && (isset($_REQUEST['true']) && $_REQUEST['true']==1)){
		update_option('bulk_upload','Active');
}elseif((isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'bulk_upload') && (isset($_REQUEST['true']) && $_REQUEST['true']==0)){
		delete_option('bulk_upload');
}
/**-- Add submenu under Templatic main menu--**/
if(is_active_addons('bulk_upload')){
	add_action('templ_add_admin_menu_', 'templ_add_submenu_bulk_upload',20);
	
}
function templ_add_submenu_bulk_upload()
{
	$menu_title = __('Bulk Import/Export',DOMAIN);	
	add_submenu_page('templatic_system_menu', "",   '<span class="tevolution-menu-separator" style="display:block; 1px -5px;  padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>',"administrator", "admin.php?page=templatic_system_menu"  );
	add_submenu_page('templatic_system_menu', $menu_title,$menu_title, 'administrator', 'bulk_upload', 'templ_bulk_upload');			
	
}

add_action('tevolution_custom_fields','tevolution_address_custom_fields',10,4);
function tevolution_address_custom_fields($post_id,$data,$k,$v){
	
	if($k=='address' && $data['geo_latitude']=='' && $data['geo_longitude']==''){		
		$http=(is_ssl())?"https://":"http://";
		$v = str_replace(' ','+',convert_chars(addslashes(iconv('', 'utf-8',$v))));
		$geocode = file_get_contents($http.'maps.google.com/maps/api/geocode/json?address='.$v.'&sensor=false');
		$output= json_decode($geocode);
		$lat = $output->results[0]->geometry->location->lat;
		$long = $output->results[0]->geometry->location->lng;
		update_post_meta($post_id, 'geo_latitude', convert_chars(addslashes(iconv('', 'utf-8',$lat))));
		update_post_meta($post_id, 'geo_longitude', convert_chars(addslashes(iconv('', 'utf-8',$long))));		
	}
	
}
/*	included file containing bulk upload functionality	*/
function templ_bulk_upload()
{
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-bulk_upload/templatic_bulk_upload.php')){
		include_once(TEMPL_MONETIZE_FOLDER_PATH.'templatic-bulk_upload/templatic_bulk_upload.php');
	}
}
?>