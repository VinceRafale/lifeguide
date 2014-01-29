<?php
global $wpdb,$pagenow;
$listing_post_type=0;
$custom_post_type_listing = CUSTOM_POST_TYPE_LISTING;
$custom_cat_type_listing = CUSTOM_CATEGORY_TYPE_LISTING;
$custom_tag_type_listing = CUSTOM_TAG_TYPE_LISTING;
$custom_post_types_args = array();
$post_type_array = get_post_types($custom_post_types_args,'objects');
if(isset($_POST['reset_custom_fields']) && (isset($_POST['custom_reset']) && $_POST['custom_reset']==1))
{
	update_option('directory_custom_fields_insert','none');
}
/*
 * Function Name: tevolution_event_taxonomy_msg
 *
 */
function tevolution_listing_taxonomy_msg(){
	echo '<div id="message" class="error below-h2">';
	echo '<form action="" method="post">';	
	echo "<p class='tevolution_desc'>".__('You have no listing post type now but your directory is in active status so you can generate listing post type again. ',DOMAIN);
	echo '<input type="submit" name="listing_post_type" value="'.__('Generate Listing Taxonomy',DOMAIN).'" class="button-primary">';
	echo '</p>';
	echo '<form>';
	echo '</div>';
}
if((isset($_REQUEST['page']) && $_REQUEST['page']=='custom_taxonomy') && (isset($_POST['listing_post_type']))){	
	$listing_post_type=1;
}

if((isset($_REQUEST['page']) && $_REQUEST['page']=='templatic_system_menu')){	
	$listing_post_type=1;
}

if(($pagenow=='plugins.php' || $pagenow=='themes.php' || $listing_post_type==1) && !array_key_exists('listing',$post_type_array) ) 
{
	
	update_option('tevolution_directory','Active');
	
	/*Register Listing cutom post type */
	$post_arr_merge[$custom_post_type_listing] = array(  'label' 		=> CUSTOM_MENU_SIGULAR_NAME_LISTING,
										'labels' 			=> array( 'name' 			 =>  CUSTOM_MENU_SIGULAR_NAME_LISTING,
																'singular_name'  	 =>  CUSTOM_MENU_SIGULAR_NAME_LISTING,
																'menu_name'          =>  CUSTOM_MENU_NAME_LISTING,
																'all_items'          =>  CUSTOM_MENU_TITLE_LISTING,
																'add_new' 		 =>  CUSTOM_MENU_ADD_NEW_LISTING,
																'add_new_item' 	 =>  CUSTOM_MENU_ADD_NEW_ITEM_LISTING,
																'edit' 			 =>  CUSTOM_MENU_EDIT_LISTING,
																'edit_item' 		 =>  CUSTOM_MENU_EDIT_ITEM_LISTING,
																'new_item' 		 =>  CUSTOM_MENU_NEW_LISTING,
																'view_item'		 =>  CUSTOM_MENU_VIEW_LISTING,
																'search_items' 	 =>  CUSTOM_MENU_SEARCH_LISTING,
																'not_found' 		 =>  CUSTOM_MENU_NOT_FOUND_LISTING,
																'not_found_in_trash' =>  CUSTOM_MENU_NOT_FOUND_TRASH_LISTING	
															    ),
										'public' 			 => true,
										'has_archive'        => true,
										'can_export'		 => true,
										'show_ui' 		 => true, /* SHOW UI IN ADMIN PANEL */
										'_builtin' 		 => false, /* IT IS A CUSTOM POST TYPE NOT BUILT IN */
										'_edit_link' 		 => 'post.php?post=%d',
										'capability_type' 	 => 'post',
										'menu_icon' 		 => '',
										'hierarchical' 	 => false,
										'rewrite' 		 => array("slug" => "$custom_post_type_listing"), /* PERMALINKS TO EVENT POST TYPE */
										'query_var' 		 => "$custom_post_type_listing", /* THIS GOES TO WPQUERY SCHEMA */
										'supports' 		 => array('title', 'author','excerpt','thumbnail','comments','editor','trackbacks','custom-fields','revisions') ,
										'show_in_nav_menus'	 => true ,
										'slugs'			 => array("$custom_cat_type_listing","$custom_tag_type_listing"),
										'taxonomies'		 => array(CUSTOM_MENU_SIGULAR_CAT_LISTING,CUSTOM_MENU_TAG_LABEL_LISTING)
									);
	$original = get_option('templatic_custom_post');
	if($original)	
		$post_arr_merge = array_merge($original,$post_arr_merge);
		
	ksort($post_arr_merge);
	update_option('templatic_custom_post',$post_arr_merge);
	/*END register listing custom post type */
	
	/* REGISTER CUSTOM TAXONOMY FOR POST TYPE EVENT */
	$original = array();
	$taxonomy_arr_merge[$custom_cat_type_listing] = array( "hierarchical" 	=> true, 
											    "label" 		=> CUSTOM_MENU_CAT_LABEL_LISTING, 
											    "post_type"	=> $custom_post_type_listing,
											    "post_slug"	=> $custom_post_type_listing,
											    'labels' 		=> array('name' 	         =>  CUSTOM_MENU_CAT_TITLE_LISTING,
																    'singular_name'     =>  $custom_cat_type_listing,
																    'search_items' 	    =>  CUSTOM_MENU_CAT_SEARCH_LISTING,
																    'popular_items'     =>  CUSTOM_MENU_CAT_SEARCH_LISTING,
																    'all_items' 	    =>  CUSTOM_MENU_CAT_ALL_LISTING,
																    'parent_item' 	    =>  CUSTOM_MENU_CAT_PARENT_LISTING,
																    'parent_item_colon' =>  CUSTOM_MENU_CAT_PARENT_COL_LISTING,
																    'edit_item' 	    =>  CUSTOM_MENU_CAT_EDIT_LISTING,
																    'update_item'	    =>  CUSTOM_MENU_CAT_UPDATE_LISTING,
																    'add_new_item' 	    =>  CUSTOM_MENU_CAT_ADDNEW_LISTING,
																    'new_item_name'     =>  CUSTOM_MENU_CAT_NEW_NAME_LISTING,
																 ), 
											    'public' 		=> true,
											    'show_ui' 		=> true,
											    "rewrite" 		=> true	
											);
	$original = get_option('templatic_custom_taxonomy');
	if($original)
		$taxonomy_arr_merge = array_merge($original,$taxonomy_arr_merge);
	
	ksort($taxonomy_arr_merge);
	update_option('templatic_custom_taxonomy',$taxonomy_arr_merge);
	/*EOF - REGISTER CUSTOM TAXONOMY FOR POST TYPE LISTING */
	
	
	/* REGISTER TAG FOR POST TYPE LISTING */
	$tag_arr_merge = array();
	$tag_arr_merge[$custom_tag_type_listing] =array("hierarchical" => false, 
									"label" 		=> CUSTOM_MENU_TAG_LABEL_LISTING, 
									"post_type"	=> $custom_post_type_listing,
									"post_slug"	=> $custom_post_type_listing,
									'labels' 		=> array( 'name' 			=>  CUSTOM_MENU_TAG_TITLE_LISTING,
														'singular_name' 	=>  $custom_tag_type_listing,
														'search_items' 	=>  CUSTOM_MENU_TAG_SEARCH_LISTING,
														'popular_items' 	=>  CUSTOM_MENU_TAG_POPULAR_LISTING,
														'all_items' 		=>  CUSTOM_MENU_TAG_ALL_LISTING,
														'parent_item' 		=>  CUSTOM_MENU_TAG_PARENT_LISTING,
														'parent_item_colon' =>  CUSTOM_MENU_TAG_PARENT_COL_LISTING,
														'edit_item' 		=>  CUSTOM_MENU_TAG_EDIT_LISTING,
														'update_item'		=>  CUSTOM_MENU_TAG_UPDATE_LISTING,
														'add_new_item' 	=>  CUSTOM_MENU_TAG_ADD_NEW_LISTING,
														'new_item_name' 	=>  CUSTOM_MENU_TAG_NEW_ADD_LISTING,	
													),  
									'public' 		=> true,
									'show_ui' 	=> true,
									"rewrite" 	=> true	
									);
	$original = get_option('templatic_custom_tags');
	if($original)	
		$tag_arr_merge = array_merge($original,$tag_arr_merge);
	ksort($tag_arr_merge);
	update_option('templatic_custom_tags',$tag_arr_merge);
	
}
/*
 * display event taxonomy generate when event taxonomy not exists 
 */
$post_type_arra=get_option('templatic_custom_post',@$post_arr_merge);
if(!array_key_exists('listing',$post_type_arra)){	
	add_action('tevolution_custom_taxonomy_msg','tevolution_listing_taxonomy_msg');
}
if((isset($_REQUEST['page']) && (($_REQUEST['page']=='custom_fields' || $_REQUEST['page']=='templatic_system_menu')) || $pagenow=='themes.php' || $pagenow=='plugins.php' ) && get_option('directory_custom_fields_insert')!='inserted') 
{
	update_option('directory_custom_fields_insert','inserted');
	/*Reset tevolution Custom Fields */
	if(isset($_POST['reset_custom_fields']) && (isset($_POST['custom_reset']) && $_POST['custom_reset']==1))
	{
		$args=array('post_type'      => 'custom_fields',
				  'posts_per_page' => -1	,
				  'post_status'    => array('publish'),
				  'meta_key'       => 'post_type_'.$custom_post_type_listing,
				  'meta_value'     => $custom_post_type_listing,
				  'order'          => 'ASC'
				);
		$custom_field = new WP_Query($args);
		if($custom_field):
			while ($custom_field->have_posts()) : $custom_field->the_post();				
				wp_delete_post( get_the_ID(), true);
			endwhile;
		endif;
	}
	/*Finish the reset all custom fields */
	 /*Insert listing custom field */
	
	 /* Insert Post heading type into posts */
	 $taxonomy_name = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_title = '[#taxonomy_name#]' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($taxonomy_name) != 0)
	 {
		 $post_type=get_post_meta($taxonomy_name->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($taxonomy_name->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($taxonomy_name->ID, 'post_type_listing','listing' );
			update_post_meta($taxonomy_name->ID, 'taxonomy_type_listingcategory','listingcategory' );
		 
	 }
	
	 /* Insert Post Category into posts */
	 $post_category = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'category' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_category) != 0)
	 {
		 $post_type=get_post_meta($post_category->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_category->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_category->ID, 'post_type_listing','listing' );
			update_post_meta($post_category->ID, 'taxonomy_type_listingcategory','listingcategory' );
		 
	 }
	
	 /* Insert Post title into posts */
	 $post_title = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_title' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_title) != 0)
	 {
		 $post_type=get_post_meta($post_title->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_title->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_title->ID, 'post_type_listing','listing' );
			update_post_meta($post_title->ID, 'taxonomy_type_listingcategory','listingcategory' );
		 
	 }
	
	 /* Insert Post content into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_content' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) != 0)
	 {
		 $post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
		 
	 }
	
	 /* Insert Post excerpt into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_excerpt' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) != 0)
	 {
		 $post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
		 
	 }
	
	/*Insert post image */
	 $post_images = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_images' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_images) != 0)
	 {
		 $post_type=get_post_meta($post_images->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_images->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_images->ID, 'post_type_listing','listing' );
			update_post_meta($post_images->ID, 'taxonomy_type_listingcategory','listingcategory' );
		 
	 }
	 
	 
	  /* Insert Post Contact Info heading into posts */
 	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'contact_info' and $wpdb->posts.post_type = 'custom_fields'"); 	 
	
	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Contact Information',
			 'post_content' => '',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'contact_info',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'heading_type',
			'htmlvar_name'=>'contact_info',
			'field_category' =>'all',
			'sort_order' => '16',
			'is_active' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'is_delete' => '0'
			);
		wp_set_post_terms($post_id,'1','category',true);
		$post_id = wp_insert_post( $my_post );
		
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		
		//wp_set_post_terms($post_id,'1','category',true);
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }		
	
	/* Insert Post Geo Address into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'address' and $wpdb->posts.post_type = 'custom_fields'"); 	
	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Address',
			 'post_content' => '',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'address',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => '[#taxonomy_name#]',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'geo_map',
			'htmlvar_name'=>'address',
			'field_category' =>'all',
			'is_require' => '1',
			'sort_order' => '8',
			'is_active' => '1',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '1',
			'is_edit' => 'false',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'show_on_success' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'field_require_desc' => 'Please Enter Address',
			'validation_type' => 'require',
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		//wp_set_post_terms($post_id,'1','category',true);
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }	
	 /* Insert Post Google Map View into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'map_view' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Google Map View',
			 'post_content' => '',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'map_view',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => '[#taxonomy_name#]',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'radio',
			'htmlvar_name'=>'map_view',
			'sort_order' => '7',
			'field_category' =>'all',
			'is_active' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'show_on_success' => '0',
			'option_title'=>'Road Map,Terrain Map,Satellite Map,Street Map',
			'option_values' => 'Road Map,Terrain Map,Satellite Map,Street Map'
			);
		$post_id = wp_insert_post( $my_post );	
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }
	 
	 
	 
	 
	 /* Insert listing feature */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'proprty_feature' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Special Offers',
			 'post_content' => 'Enter any special offers (optional)',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'proprty_feature',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => '[#taxonomy_name#]',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'texteditor',
			'htmlvar_name'=>'proprty_feature',
			'field_category' =>'all',
			'is_require' => '',
			'sort_order' => '9',
			'is_active' => '1',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'show_on_success' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'field_require_desc' => '',
			'validation_type' => ''
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 		
	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }
	 
	 /* Insert End Time into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'listing_timing' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Opening Hours',
			 'post_content' => 'Enter business hours.<br>for example:<b>10.00-18.00 week days - Sunday closed</b>',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'listing_timing',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => '[#taxonomy_name#]',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'listing_timing',
			'field_category' =>'all',
			'is_require' => '0',
			'sort_order' => '10',
			'is_active' => '1',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '1',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'show_on_success' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'field_require_desc' => '',
			'validation_type' => '',
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }
	 
	 /* Insert Listing contact information */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'phone' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Phone',
			 'post_content' => 'Enter phone or cell phone number.',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'phone',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Contact Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'phone',
			'field_category' =>'all',
			'sort_order' => '17',
			'is_active' => '1',
			'is_require' => '',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '1',
			'is_edit' => 'true',
			'is_search'=>'0',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'show_in_email'  =>'1',
			'field_require_desc' => 'Please enter phone number',
			'validation_type' => 'require'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 	
	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }
	 
	 /* Insert How to Register into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'email' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Email',
			 'post_content' => 'Enter your email address.',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'email',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Contact Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'email',
			'field_category' =>'all',
			'sort_order' => '18',
			'is_active' => '1',
			'is_require' => '1',
			'validation_type' =>'email',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '0',
			'is_delete' => '0',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'show_on_success' => '1',
			'field_reduire_desc' => 'Please provide your email address',			
			);
		$post_id = wp_insert_post( $my_post );	
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 		
	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }
	 
	 /* Insert Website into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'website' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Website',
			 'post_content' => 'Enter website url for example as http://www.yoursite.com',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'website',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Contact Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'website',
			'field_category' =>'all',
			'sort_order' => '19',
			'is_active' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'show_in_email'  =>'1',
			'is_search'=>'0',
			'show_on_success' => '1'
			);
		$post_id = wp_insert_post( $my_post );	
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
 		
	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }
	 
	 /* Insert Website into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'listing_logo' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Logo',
			 'post_content' => 'Upload logo from your computer',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'listing_logo',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => '[#taxonomy_name#]',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'upload',
			'htmlvar_name'=>'listing_logo',
			'field_category' =>'all',
			'sort_order' => '20',
			'is_active' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'false',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'show_on_success' => '1'
			);
		$post_id = wp_insert_post( $my_post );	
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
 		
	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }
	 
	 /* Insert Twitter into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'twitter' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Twitter',
			 'post_content' => 'Enter Twitter profile url for example as http://www.twitter.com/profile',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'twitter',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Contact Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'twitter',
			'field_category' =>'all',
			'sort_order' => '21',
			'is_active' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'show_on_success' => '1'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		//wp_set_post_terms($post_id,'1','category',true);
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
	 	}
	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }
	 
	 
	 
	 /* Insert Facebook into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'facebook' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Facebook',
			 'post_content' => 'Enter Facebook profile url for example as https://www.facebook.com/profile',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'facebook',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Contact Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'facebook',
			'field_category' =>'all',
			'sort_order' => '22',
			'is_active' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'show_on_success' => '1'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		//wp_set_post_terms($post_id,'1','category',true);
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
 		
	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }
	 
	 
	  /* Insert Google Plus into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'google_plus' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Google+ ',
			 'post_content' => ' Enter Google+ profile url for example as https://www.plus.google.com/profile',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'google_plus',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Contact Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'google_plus',
			'field_category' =>'all',
			'sort_order' => '23',
			'is_active' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'show_on_success' => '1'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		//wp_set_post_terms($post_id,'1','category',true);
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
 		
	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }
	 
	 /* Insert Video into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'video' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Video code',
			 'post_content' => 'Paste video embed code here',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'video',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => '[#taxonomy_name#]',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'textarea',
			'htmlvar_name'=>'video',
			'field_category' =>'all',
			'sort_order' => '23',
			'is_active' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'is_delete' => '0'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
 	}else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }
	 
	 
	  /* Insert Tag Keyword */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_tags' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Tag Keyword',
			 'post_content' => 'Tags are short keywords, with no space within. Up to 40 characters only.',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'post_tags',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => '[#taxonomy_name#]',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'post_tags',
			'field_category' =>'all',
			'sort_order' => '25',
			'is_active' => '0',
			'is_require' => '0',
			'show_on_page' => 'user_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'false',
			'show_on_detail' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'is_delete' => '0'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
 	}else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
	 }
	 
	 
	 /*Set the Submit listing page */
	 $post_content = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'submit-listing' and $wpdb->posts.post_type = 'page'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Submit Listing',
			 'post_content' => "Submit the listing in category of your choice. [submit_form post_type='listing']",
			 'post_status' => 'publish',
			 'comment_status' => 'closed',
			 'post_author' => 1,
			 'post_name' => 'submit-listing',
			 'post_type' => "page",
			);
		$post_meta = array(
			'_wp_page_template' => 'default',
			'_edit_last'        => '1',
			
			);
		$post_id = wp_insert_post( $my_post );		
	 }
	 
	 /*Set the Advance listing Search page */
	 $post_content = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'listing-search' and $wpdb->posts.post_type = 'page'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Advance Listing Search',
			 'post_content' => "[advance_search_page post_type='listing']",
			 'post_status' => 'publish',
			 'comment_status' => 'closed',
			 'post_author' => 1,
			 'post_name' => 'listing-search',
			 'post_type' => "page",
			);
		$post_meta = array(
			'_wp_page_template' => 'default',
			'_edit_last'        => '1',
			
			);
		$post_id = wp_insert_post( $my_post );		
	 }
	 
	$tmpdata = get_option('templatic_settings');
	$templatic_settings['related_radius']='100';
	$templatic_settings['related_radius']=array('listing');
	update_option('templatic_settings',array_merge( $templatic_settings, $tmpdata ));
}
if(is_admin()){
	$results = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type='monetization_package'");
	if(count($results)!=0 && !get_option('update_directory_price')){
		foreach($results as $res){
			echo $res->ID;
			$package_post_type=get_post_meta($res->ID,'package_post_type',true);
			$package_post_type.=$custom_post_type_listing.',';
			update_post_meta($res->ID,'package_post_type',substr($package_post_type,0,-1));
			
		}
		update_option('update_directory_price',1);
	}
}
?>