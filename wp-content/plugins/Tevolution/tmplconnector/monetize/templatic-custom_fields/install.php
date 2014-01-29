<?php
global $wp_query,$wpdb,$wp_rewrite,$current_user;
/**-- conditions for activation Custom Fields --**/
if((isset($_REQUEST['activated']) && $_REQUEST['activated']=='custom_fields_templates') && (isset($_REQUEST['true']) && $_REQUEST['true']==1) || (isset($_REQUEST['activated']) && $_REQUEST['activated']=='true'))
{
		update_option('custom_fields_templates','Active');
		$templatic_settings=get_option('templatic_settings');
		
		if(!isset($templatic_settings['templatic-category_custom_fields']) && $templatic_settings['templatic-category_custom_fields']=='')
			$tmpdata['templatic-category_custom_fields'] = 'No';
		else
			$tmpdata['templatic-category_custom_fields'] = $templatic_settings['templatic-category_custom_fields'];
		
		update_option('templatic_settings',array_merge($templatic_settings,$tmpdata));
		
}elseif((isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'custom_fields_templates') && (isset($_REQUEST['true']) && $_REQUEST['true']==0)){
		delete_option('custom_fields_templates');
}
/**-- coading to add submenu under main menu--**/
if(is_active_addons('custom_fields_templates')){
	add_action('templ_add_admin_menu_', 'templ_add_submenu',12);/* create custom field setup menu */
	add_filter('set-screen-option', 'custom_fields_set_screen_option', 10, 3);
	
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/custom_fields_function.php') )
	{
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/custom_fields_function.php");	
	}
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/language.php') )
	{
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/language.php");
	}
	/* Specially for image resizer */
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/image_resizer.php'))
	{
		require_once (TEMPL_MONETIZE_FOLDER_PATH . 'templatic-custom_fields/image_resizer.php');
	}
	
	
	add_filter('body_class','remove_admin_bar',10,2);/* call body class for remove admin bar */
	add_action( 'init', 'custom_fields_preview' ,11);
	add_action('admin_init','create_default_wordpress_customfields');
	
	add_action('templatic_general_setting_data','post_page_setting_data');/* call general setting data */
	
	add_action('admin_init','post_expire_session_table_create');
	add_action('admin_notices','tevolution_custom_fields_notice',30);
	
}

function templ_add_submenu()
{
	$menu_title1 = __('Custom Fields Setup',DOMAIN);
	global $custom_fields_screen_option;
	$custom_fields_screen_option = add_submenu_page('templatic_system_menu', $menu_title1,$menu_title1, 'administrator', 'custom_fields', 'add_custom_fields');
	add_action("load-$custom_fields_screen_option", "custom_fields_screen_options");
}

/* Set the file extension for allown only image/picture file extension in upload file*/
$extension_file=array('.jpg','.JPG','jpeg','JPEG','.png','.PNG','.gif','.GIF','.jpe','.JPE');  
global $extension_file;
/* Function for screen option */
function custom_fields_screen_options() {
 	global $custom_fields_screen_option;
 	$screen = get_current_screen();
 	// get out of here if we are not on our settings page
	if(!is_object($screen) || $screen->id != $custom_fields_screen_option)
		return;
 
	$args = array( 'label' => __('Custom Fields per page', DOMAIN),
				'default' => 10,
				'option' => 'custom_fields_per_page'
			);
	add_screen_option( 'per_page', $args );
}


function custom_fields_set_screen_option($status, $option, $value) {
	if ( 'custom_fields_per_page' == $option ) return $value;
}

function add_custom_fields(){
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'addnew'){
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/admin_manage_custom_fields_edit.php");
	}else{
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/admin_manage_custom_fields_list.php");
	}
}

/*
 * Function Name: remove_admin_bar
 * Return: unset the admin-bar class on preview and success page 
 */
function remove_admin_bar($classes,$class){
		
	if(isset($_REQUEST['page']) && ($_REQUEST['page'] == "preview" || $_REQUEST['page'] == "success")){
		if(($key = array_search('admin-bar', $classes)) !== false) {
		    unset($classes[$key]);
		}
	}
	return $classes;
}


/* Custom Fields Preview page Start  */
function custom_fields_preview()
{
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "preview")
	{
		include(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/custom_fields_preview.php");
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "payment")
	{
		include(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/post_upgrade_payment.php");
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "paynow")
	{
		global $_wp_additional_image_sizes;		
		include(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/custom_fields_paynow.php");
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "upgradenow")
	{
		global $_wp_additional_image_sizes;		
		include(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/post_upgrade_pay.php");
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "success")
	{
		include(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/success.php");
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "paypal_pro_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-paypal_pro/includes/paypal_pro_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "authorizedotnet_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-authorizedotnet/includes/authorizedotnet_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "googlecheckout_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-googlecheckout/includes/googlecheckout_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "worldpay_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-worldpay/includes/worldpay_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "eway_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-eway/includes/eway_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "ebay_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-ebay/includes/ebay_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "ebs_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-ebs/includes/ebs_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "psigate_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-psigate/includes/psigate_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "2co_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-2co/includes/2co_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "stripe_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-stripe/includes/stripe_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "braintree_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-Braintree/includes/braintree_success.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "inspire_commerce_success")
	{
		$dir = get_tmpl_plugin_directory() . 'Tevolution-InspireCommerce/includes/inspire_commerce_success.php';
		include($dir);
		exit;
	}
	if(isset($_GET['stripe-listener']) && $_GET['stripe-listener'] == 'recurring') {
		$dir = get_tmpl_plugin_directory() . 'Tevolution-stripe/includes/stripe_listener.php';
		include($dir);
		exit;
	}
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'login')
	{
		include(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/registration.php");
		exit;
	}
}
/* Custom Fields Preview page End  */
/* Insert wordpress default fields in posts table when plugin activated */
function create_default_wordpress_customfields()
{
	global $wpdb,$pagenow,$table_name;
	if($pagenow=='plugins.php' || $pagenow=='themes.php' || (is_active_addons('custom_fields_templates') && (isset($_REQUEST['page']) && ($_REQUEST['page']=='templatic_system_menu' || $_REQUEST['page']=='custom_fields' ))))
	{
		
		/*Reset tevolution Custom Fields */
		if(isset($_POST['reset_custom_fields']) && (isset($_POST['custom_reset']) && $_POST['custom_reset']==1))
		{
			$args=array('post_type'      => 'custom_fields',
					  'posts_per_page' => -1	,
					  'post_status'    => array('publish'),
					  'order'          => 'ASC'
					);
			$custom_field = new WP_Query($args);
			if($custom_field):
				while ($custom_field->have_posts()) : $custom_field->the_post();
					wp_delete_post( get_the_ID(), true);
				endwhile;
			endif;
	
		}
		/*
		* @filter : reset_exclude_post_types
		* @how to use in other plugins
		* example code of used in booking plugin
		* 
		*	add_action('admin_init','booking_admin_init_callback',9);
		*	function booking_admin_init_callback(){
		*		add_filter('reset_exclude_post_types','reset_exclude_post_types_callback',9);
		*	}
		*	if(!function_exists('reset_exclude_post_types_callback')){
		*		function reset_exclude_post_types_callback($exclude_post_types){
		*			$exclude_post_types[] = "booking";
		*			$exclude_post_types[] = "house";
		*			$exclude_post_types[] = "room";
		*			return $exclude_post_types;
		*		}
		*	}
		*
		* Here You have to pass "$exclude_post_types" same variable in other plugins as well.
		*/
		$exclude_post_type = apply_filters('reset_exclude_post_types',array());
		$cus_pos_type = get_option("templatic_custom_post");
		$post_type_arr='post,';
		$heading_post_type_arr='post,';
		if($cus_pos_type && count($cus_pos_type) > 0)
		{
			foreach($cus_pos_type as $key=> $_cus_pos_type)
			{
				if(!empty($exclude_post_type)){
					if(!in_array($key,$exclude_post_type)){
						$post_type_arr .= $key.",";
					}
				}else{
					$post_type_arr .= $key.",";
				}
				$heading_post_type_arr .= $key.",";
			}
		}
		$post_type_arr = substr($post_type_arr,0,-1);
		$heading_post_type_arr = substr($heading_post_type_arr,0,-1);
		
		/* Insert Post Category into posts */
		$post_category = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'category' and $wpdb->posts.post_type = 'custom_fields'");		
		if(count($post_category) == 0)
		 {
			$my_post = array(
						 'post_title'   => 'Post Category',
						 'post_content' => '',
						 'post_status'  => 'publish',
						 'post_author'  => 1,
						 'post_name'    => 'category',
						 'post_type'    => "custom_fields",
					);
			$post_meta = array(
				'post_type'          => $post_type_arr,
				'ctype'              =>'post_categories',
				'htmlvar_name'       =>'category',
				'sort_order'         => '1',
				'is_active'          => '1',
				'is_require'         => '1',
				'show_on_page'       => 'user_side',
				'is_edit'            => 'true',
				'show_on_detail'     => '0',
				'show_on_listing'    => '0',
				'show_in_column'     => '0',
				'is_search'          =>'0',
				'field_require_desc' => 'Please Select Category',
				'validation_type'    => 'require',
				'heading_type'       => '[#taxonomy_name#]',
				);
			$post_id = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('wpml-translation-management/plugin.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}
			wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			 {
				add_post_meta($post_id, $key, $_post_meta);
			 }
			$ex_post_type = '';
			$ex_post_type = explode(",",$post_type_arr);
			foreach($ex_post_type as $_ex_post_type)
			 {
				add_post_meta($post_id, 'post_type_'.$_ex_post_type.'' , 'all');
			 }
		 }
		 /* Finish The category custom field */
		 
		 /* Insert Post title into posts */
		$post_title = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_title' and $wpdb->posts.post_type = 'custom_fields'");
		if(count($post_title) == 0)
		 {
			$my_post = array(
						 'post_title'   => 'Post Title',
						 'post_content' => '',
						 'post_status'  => 'publish',
						 'post_author'  => 1,
						 'post_name'    => 'post_title',
						 'post_type'    => "custom_fields",
					);
			$post_meta = array(
				'post_type'          => $post_type_arr,
				'ctype'              =>'text',
				'htmlvar_name'       =>'post_title',
				'sort_order'         => '2',
				'is_active'          => '1',
				'is_require'         => '1',
				'show_on_page'       => 'user_side',
				'is_edit'            => 'true',
				'show_on_detail'     => '0',
				'show_on_success'    => '1',
				'show_on_listing'    => '1',
				'show_in_column'     => '0',
				'is_search'          =>'0',
				'field_require_desc' => __('Please Enter title',DOMAIN),
				'validation_type'    => 'require',
				'heading_type'       => '[#taxonomy_name#]',
				);
			$post_id = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('wpml-translation-management/plugin.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}
			wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			 {
				add_post_meta($post_id, $key, $_post_meta);
			 }
			$ex_post_type = '';
			$ex_post_type = explode(",",$post_type_arr);
			foreach($ex_post_type as $_ex_post_type)
			 {
				add_post_meta($post_id, 'post_type_'.$_ex_post_type.'' , 'all');
			 }
	 
		 }
		 /* Finish the post title custom fields */
		 
		  /* Insert Post content into posts */
		 $post_content = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_content' and $wpdb->posts.post_type = 'custom_fields'");
		 if(count($post_content) == 0)
		 {
			$my_post = array(
						 'post_title'  => 'Post Content',
						 'post_content'=> '',
						 'post_status' => 'publish',
						 'post_author' => 1,
						 'post_name'   => 'post_content',
						 'post_type'   => "custom_fields",
					);
			$post_meta = array(
				'post_type'          => $post_type_arr,
				'ctype'              =>'texteditor',
				'show_in_column'     => '0',
				'htmlvar_name'       =>'post_content',
				'sort_order'         => '3',
				'is_active'          => '1',
				'is_require'         => '1',
				'show_on_page'       => 'user_side',
				'is_edit'            => 'true',
				'show_on_detail'     => '1',
				'show_on_listing'    => '1',
				'show_in_column'     => '0',
				'is_search'          =>'0',
				'field_require_desc' => __('Please Enter content',DOMAIN),
				'validation_type'    => 'require',
				'heading_type'       => '[#taxonomy_name#]',
				);
			$post_id = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('wpml-translation-management/plugin.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}
			wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			 {
				add_post_meta($post_id, $key, $_post_meta);
			 }
			
			$ex_post_type = '';
			$ex_post_type = explode(",",$post_type_arr);
			foreach($ex_post_type as $_ex_post_type)
			 {
				add_post_meta($post_id, 'post_type_'.$_ex_post_type.'' , 'all');
			 }
		 }
		 /* Finish the post content custom field */
		 
		  /* Insert Post excerpt into posts */
		 $post_content = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_excerpt' and $wpdb->posts.post_type = 'custom_fields'");
		 if(count($post_content) == 0)
		 {
			$my_post = array(
						 'post_title'   => 'Post Excerpt',
						 'post_content' => '',
						 'post_status'  => 'publish',
						 'post_author'  => 1,
						 'post_name'    => 'post_excerpt',
						 'post_type'    => "custom_fields",
					);
			$post_meta = array(
				'post_type'      => $post_type_arr,
				'ctype'          => 'textarea',
				'htmlvar_name'   => 'post_excerpt',
				'sort_order'     => '3',
				'is_active'      => '1',
				'is_require'     => '0',
				'show_on_page'   => 'user_side',
				'show_in_column' => '0',
				'show_on_listing'=> '1',
				'is_edit'        => 'true',
				'show_on_detail' => '1',
				'show_in_column' => '0',
				'is_search'      =>'0',
				'heading_type'   => '[#taxonomy_name#]',
				);
			$post_id = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('wpml-translation-management/plugin.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}
			wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			 {
				add_post_meta($post_id, $key, $_post_meta);
			 }
			
			$ex_post_type = '';
			$ex_post_type = explode(",",$post_type_arr);
			foreach($ex_post_type as $_ex_post_type)
			 {
				add_post_meta($post_id, 'post_type_'.$_ex_post_type.'' , 'all');
			 }
		 }
		 /* Finish The post excerpt custom field */
		 
		 /* Insert Post image_uploader into posts */
		 $post_images = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_images' and $wpdb->posts.post_type = 'custom_fields'");
		 if(count($post_images) == 0)
		 {
			$my_post = array(
						 'post_title'   => 'Post Images',
						 'post_content' => '',
						 'post_status'  => 'publish',
						 'post_author'  => 1,
						 'post_name'    => 'post_images',
						 'post_type'    => "custom_fields",
					);
			$post_meta = array(
				'post_type'	   => $post_type_arr,
				'ctype'		   =>'image_uploader',
				'site_title'	   =>'Post Images',
				'htmlvar_name'    =>'post_images',
				'sort_order' 	   => '4',
				'is_active' 	   => '1',
				'is_require' 	   => '1',
				'show_on_page'    => 'user_side',
				'show_in_column'  => '0',
				'show_on_detail'  => '1',
				'show_on_listing' => '1',
				'show_in_email'   => '0',
				'is_edit'         => 'true',
				'is_search'       =>'0',
				'heading_type'    => '[#taxonomy_name#]',
				);
			$post_id = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('wpml-translation-management/plugin.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}
			wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			 {
				add_post_meta($post_id, $key, $_post_meta);
			 }
			
			$ex_post_type = '';
			$ex_post_type = explode(",",$post_type_arr);
			foreach($ex_post_type as $_ex_post_type)
			 {
				add_post_meta($post_id, 'post_type_'.$_ex_post_type.'' , 'all');
			 }
		 }
		 /* Finish the post images custom fields */
		 
		 /* Insert Post heading type into posts */
		 $post_images = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE $wpdb->posts.post_title = '[#taxonomy_name#]' and $wpdb->posts.post_type = 'custom_fields'");
		 if(count($post_images) == 0)
		 {
			$my_post = array(
						 'post_title'   => '[#taxonomy_name#]',
						 'post_content' => 'It is a default heading type used for grouping certain custom fields together under the same particular heading at front end. (e.g. place information, event information etc.)',
						 'post_status'  => 'publish',
						 'post_author'  => 1,
						 'post_name'    => 'basic_inf',
						 'post_type'    => "custom_fields",
					);
			$post_meta = array(
				'post_type'	  => $heading_post_type_arr,
				'ctype'	       =>'heading_type',
				'site_title'	  =>'[#taxonomy_name#]',
				'htmlvar_name'   =>'basic_inf',
				'sort_order' 	  => '5',
				'is_active' 	  => '1',
				'show_on_page'   => 'user_side',
				'show_on_detail' => '0',
				'show_in_column' => '0',
				'is_search'      =>'0',
				'is_edit' 	  => 'true',
				'heading_type'   => '[#taxonomy_name#]',
				);
			$post_id = wp_insert_post( $my_post );
			/* Finish the place geo_latitude and geo_longitude in postcodes table*/
			if(is_plugin_active('wpml-translation-management/plugin.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
			}
			wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			 {
				add_post_meta($post_id, $key, $_post_meta);
			 }
			
			$ex_post_type = '';
			$ex_post_type = explode(",",$post_type_arr);
			foreach($ex_post_type as $_ex_post_type)
			 {
				add_post_meta($post_id, 'post_type_'.$_ex_post_type.'' , 'all');
			 }
		 }
		 /* Finish the taxonomy name heading custom fields */
		
	}// First if condition
	
	
}



/*
* Crate action for post par listing setting
*/	
function post_page_setting_data($column)
{
	$tmpdata = get_option('templatic_settings');
	
	?>
	    <p class="tevolution_desc"> <?php _e('This is the main Tevolution settings area. As you add Tevolution add-ons their settings will appear here. <br><b>Note:</b> Do not forget to click on "Save all settings" at the bottom when done with tweaking settings. You should also clear Tevolution cache (top of the page) after every major change. ',DOMAIN)?> </p>
			
		
			<?php if(!current_theme_supports('home_listing_type_value') || ((function_exists('directory_admin_notices') || function_exists('event_manager_admin_notices') )&& (!current_theme_supports('tev_taxonomy_sorting_opt') || !current_theme_supports('tev_taxonomy_excerpt_opt')))):?>
					<div id="theme_support_setting">
						<p><?php _e("Copy below code and paste them in your theme's functions.php file located in your active WordPress directory to enhance your theme functionality:",DOMAIN); ?></p>
			<?php endif;?>
			
			<?php if(!current_theme_supports('home_listing_type_value')):?>
					<p class="tevolution_desc"><?php _e('Display different post type on home page   -   add_theme_support("home_listing_type_value");',DOMAIN); ?></p>
			<?php endif;?>
			<!--Start taxonomy sorting theme supports description -->
			<?php if(!current_theme_supports('tev_taxonomy_sorting_opt') && function_exists('directory_admin_notices')):?>               
			<p class="tevolution_desc"><?php _e('Display sorting option on taxonomy page    -   add_theme_support("tev_taxonomy_sorting_opt");',DOMAIN); ?></p>
			<?php endif;?> 
			<!-- End taxonomy sorting option -->
			
			<?php if(!current_theme_supports('tev_taxonomy_excerpt_opt')  && function_exists('directory_admin_notices')):?>                        
			<p class="tevolution_desc"><?php _e('Display excerpt setting on post listing page   -   add_theme_support("tev_taxonomy_excerpt_opt");',DOMAIN); ?></p>
			<?php endif;?> 
			</div>
			
	<ul class="subsubsub">
		<?php if(current_theme_supports('home_listing_type_value') || current_theme_supports('tev_taxonomy_excerpt_opt')):?>
		<li class="listing_page_settings"><a href="#listing_page_settings"><?php _e('Category page settings',DOMAIN);?></a></li>
		<?php endif;?>
		<li class="submit_page_settings"><a href="#submit_page_settings"><?php _e('Submission page settings',DOMAIN);?></a></li>
		<li class="detail_page_settings"><a href="#detail_page_settings"><?php _e('Detail Page Settings',DOMAIN);?></a></li>
		<?php if(is_active_addons('templatic-login')):?>
		<li class="registration_page_setup"><a href="#registration_page_setup" ><?php _e('Registration Page Setup',DOMAIN);?></a></li>
		<?php endif;?>
		
		<?php if(is_active_addons('claim_ownership')):?>
		<li class="general_claim_setting"><a href="#general_claim_setting"><?php _e('Claim ownership settings',DOMAIN);?></a></li>
		<?php endif;?>
	</ul>     
			
	<?php $fl=0; if(current_theme_supports('home_listing_type_value')) : $fl=1; ?>
		<tr id="listing_page_settings">
			<th colspan="2"><div class="tevo_sub_title"><?php _e('Category page settings',DOMAIN);?></div>
		    </th>
		</tr>
			
		<?php do_action('before_listing_page_setting');?>      
		<tr>
			<th><label><?php _e('Homepage displays',DOMAIN); ?> </label></th>
			<td>
			<?php 
			$posttaxonomy = get_option("templatic_custom_post");
			if(!empty($posttaxonomy))
			{
				foreach($posttaxonomy as $key=>$_posttaxonomy):						
					?>
					<div class="element">
						<label for="home_listing_type_value_<?php echo $key; ?>"><input type="checkbox" name="home_listing_type_value[]" id="home_listing_type_value_<?php echo $key; ?>" value="<?php echo $key; ?>" <?php if(@$tmpdata['home_listing_type_value'] && in_array($key,$tmpdata['home_listing_type_value'])) { echo "checked=checked";  } ?>>&nbsp;<?php _e($_posttaxonomy['label'],DOMAIN); ?></label>
					</div>
				<?php endforeach;  }
			else
			{
				$url = '<a target=\"_blank\" href='.admin_url("admin.php?page=custom_taxonomy&action=add_taxonomy").'>';
				$url .= __('here',DOMAIN);
				$url .= '</a>'; 
				 _e('Please create a custom post type from ',DOMAIN);
				 echo $url;
			}
			 do_action('templ_post_type_description');?>  <p class="description"><?php echo sprintf(__('For this option to work you must select set the "Front page displays" option within <a href=" %s options-reading.php" target= "_blank">WordPress reading settings</a> to "Your latest posts".',DOMAIN),admin_url());?></p>           
			</td>
		</tr>
		<?php
				if(!current_theme_supports('listing_excerpt_setting')){
			?>
					<tr>
						 <th><label><?php _e('Hide excerpts for',DOMAIN); ?></label></th>
						 <td>
						 <?php $templatic_custom_post = get_option('templatic_custom_post');
								if(!empty($posttaxonomy))
								{						 
									 foreach ($templatic_custom_post as $key => $val):							
									 ?>                            
									 <div class="element">
										  <label for="listing_hide_excerpt_<?php echo $key; ?>"><input type="checkbox" name="listing_hide_excerpt[]" id="listing_hide_excerpt_<?php echo $key; ?>" value="<?php echo $key; ?>" <?php if(@$tmpdata['listing_hide_excerpt'] && in_array($key,$tmpdata['listing_hide_excerpt'])) { echo "checked=checked";  } ?>>&nbsp;<?php echo $val['label']; ?></label>
									 </div>
									 <?php endforeach; 
								}
								else
								{
									$url = '<a target=\"_blank\" href='.admin_url("admin.php?page=custom_taxonomy&action=add_taxonomy").'>';
									$url .= __('here',DOMAIN);
									$url .= '</a>'; 
									 _e('You can hide custom post type which you can create from ',DOMAIN);
									 echo $url;
								}?>
								 <p class="description"><?php _e('Hiding excerpts will also hide the "Read more" link.',DOMAIN);?></p>
								 <?php do_action('templ_post_type_description');?>            
								 </td>
					</tr>
		    <?php 
				}
				do_action('after_listing_page_setting');
			?> 
		
		<?php endif; ?>
			
		<?php if(current_theme_supports('tev_taxonomy_excerpt_opt')) :?>
			<?php if($fl==0):?>
			<tr id="listing_page_settings">
				<th colspan="2"><div class="tevo_sub_title"><?php _e('Category page settings',DOMAIN);?></div></th>
			</tr>
			<?php endif;?>    
			<tr>
				<th><label><?php _e('Length Of Summary ',DOMAIN); ?></label></th>
				<td>
					<input type="text" name="excerpt_length" value="<?php echo $tmpdata['excerpt_length']; ?>" />
					<p class="description"><?php _e("If you haven't entered excerpt in your post we will display here mentioned number of characters from your post description .",DOMAIN);?></p>
				</td>
			</tr>
			 <tr>
				<th><label><?php _e('Title For Continue Link ',DOMAIN); ?></label></th>
				<td>
					<input type="text" name="excerpt_continue" value="<?php echo $tmpdata['excerpt_continue']; ?>" />
					<p class="description"><?php _e('Mention the title you want to show for a link which will be redirected to post detail page ',DOMAIN);?></p>
				</td>
			</tr>
		<?php endif;?>     
		
		<?php if(current_theme_supports('tev_taxonomy_sorting_opt')):?>
		<tr>
			<th valign="top"><label><?php _e('Show the sorting box as',DOMAIN);?></label></th>
			<td>
			<label for="sorting_type_select"><input type="radio" id="sorting_type_select" <?php if($tmpdata['sorting_type']=='select') echo 'checked';?> name="sorting_type" value="select"/>&nbsp;<?php _e('Dropdown',DOMAIN);?></label>&nbsp;&nbsp;
			<label for="sorting_type_normal"><input type="radio" id="sorting_type_normal" <?php if($tmpdata['sorting_type']=='normal') echo 'checked';?> name="sorting_type" value="normal"/>&nbsp;<?php _e('Simple links',DOMAIN);?></label>
			</td>
		</tr>
		 <tr class="templatic_sorting">
			<th valign="top"><label><?php _e('Sorting options in sorting box',DOMAIN);?></label></th>
			<td>
				<label><input type="checkbox" class="checkall" name="sorting_option[]" <?php if(!empty($tmpdata['sorting_option']) && in_array('select_all',$tmpdata['sorting_option'])) echo 'checked';?> onclick="SelectAllSorting()" value="select_all" /> <?php _e("Select all",DOMAIN);?></label><br/>
				 <label for="title_alphabetical"><input type="checkbox" id="title_alphabetical" name="sorting_option[]" value="title_alphabetical" <?php if(!empty($tmpdata['sorting_option']) && in_array('title_alphabetical',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php  _e('Alphabetical',DOMAIN);?></label><br/>
				<label for="title_asc"><input type="checkbox" id="title_asc" name="sorting_option[]" value="title_asc" <?php if(!empty($tmpdata['sorting_option']) && in_array('title_asc',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php  _e('Title Ascending',DOMAIN);?></label><br/>
				<label for="title_desc"><input type="checkbox" id="title_desc" name="sorting_option[]" value="title_desc" <?php if(!empty($tmpdata['sorting_option']) && in_array('title_desc',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php _e('Title Descending',DOMAIN);?></label><br />
				<label for="date_asc"><input type="checkbox" id="date_asc" name="sorting_option[]" value="date_asc" <?php if(!empty($tmpdata['sorting_option']) && in_array('date_asc',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php _e('Publish Date Ascending',DOMAIN);?></label><br/>
				<label for="date_desc"><input type="checkbox" id="date_desc" name="sorting_option[]" value="date_desc" <?php if(!empty($tmpdata['sorting_option']) && in_array('date_desc',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php _e('Publish Date Descending',DOMAIN);?></label><br />
				 <label for="reviews"><input type="checkbox" id="reviews" name="sorting_option[]" value="reviews" <?php if(!empty($tmpdata['sorting_option']) && in_array('reviews',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php _e('Reviews ',DOMAIN);?></label><br />
				  <label for="rating"><input type="checkbox" id="rating" name="sorting_option[]" value="rating" <?php if(!empty($tmpdata['sorting_option']) && in_array('rating',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php _e('Rating',DOMAIN);?></label><br />
				<label for="random"><input type="checkbox" id="random" name="sorting_option[]" value="random" <?php if(!empty($tmpdata['sorting_option']) && in_array('random',$tmpdata['sorting_option'])) echo 'checked';?>/>&nbsp;<?php _e('Random',DOMAIN);?></label><br />
				<?php do_action('taxonomy_sorting_option','sorting_option');?>
				<p class="description"><?php _e('For the "Rating" option to work you must enable the "Show rating" setting available in the "Registrations options" section below.',DOMAIN);?></p>
				<script type="text/javascript">
				function SelectAllSorting()
				{
					jQuery('.templatic_sorting').find(':checkbox').attr('checked', jQuery('.checkall').is(":checked"));
				}
				</script>
			</td>
		</tr>
		<tr>
			<td>
				<p class="submit" style="clear: both;">
				  <input type="submit" name="Submit"  class="button-primary" value="<?php _e('Save',DOMAIN);?>" />
				</p>
			</td>
		</tr>
		<?php endif;?>
			
		<tr id="submit_page_settings">                    
			<th colspan="2">
			<div class="tevo_sub_title"><?php _e('Submission page settings',DOMAIN);?></div>
			   <p class="tevolution_desc"><?php _e('To generate a submission page enter the following shortcode into any page or post -> [submit_form post_type= &acute;your_post_type_name&acute;]. For details on this please open the <a href="http://templatic.com/docs/tevolution-guide" target= "_blank" > documentation guide</a>',DOMAIN)?></p><br />
		    </th>
		</tr> 
	   
		<tr>
			<th>
				<label><?php _e('Show custom fields categorywise',DOMAIN);	$templatic_category_custom_fields =  @$tmpdata['templatic-category_custom_fields']; if(!isset($templatic_category_custom_fields) && $templatic_category_custom_fields == ''){update_option('templatic-category_custom_fields','No');}?></label>
			</th>
			<td>
				<label for="templatic-category_custom_fields"><input type="checkbox" id="templatic-category_custom_fields" name="templatic-category_custom_fields" value="Yes" <?php if($templatic_category_custom_fields == 'Yes' || $templatic_category_custom_fields ==''){?>checked="checked"<?php }?> />&nbsp;<?php _e('Enable',DOMAIN);?>
				
				<p class="description"><?php _e('Enabling this option will display the submit page with the particularly defined custom fields according to their category. Open the <a href="http://templatic.com/docs/tevolution-guide/#basic_settings" title="Tevolution Guid" target="_blank">Tevolution guide</a> for more information.  ',DOMAIN);?></p>
			</td>
		</tr>
			 <tr>
				<th><label><?php _e('Display categories as a',DOMAIN); ?></label></th>
				<td>
					<div class="element">
						 <div class="input_wrap">
							<?php $templatic_category_type =  @$tmpdata['templatic-category_type']; ?>
						  <select id="templatic-category_type" name="templatic-category_type" style="vertical-align:top;width:200px;" >
							<option value=""><?php  _e('Please select category type',DOMAIN);  ?></option>
							<option value="checkbox" <?php if($templatic_category_type == 'checkbox' ) { echo "selected=selected";  } ?>><?php _e('Check Box',DOMAIN); ?></option>
							<option value="select" <?php if($templatic_category_type == 'select' ) { echo "selected=selected";  } ?>><?php _e('Select Box',DOMAIN); ?></option>
							<option value="multiselectbox" <?php if($templatic_category_type == 'multiselectbox' ) { echo "selected=selected";  } ?>><?php _e('Multi-select Box',DOMAIN); ?></option>
						</select> 
					</div>
					</div>
				   <label for="ilc_tag_class"><p class="description"><?php _e('Specify the format in which you want to display the categories on Submit page.',DOMAIN);?></p></label>
				</td>
			 </tr>
			 <tr>
				<th><label><?php _e('Allowed image upload size',DOMAIN);	$templatic_image_size =  @$tmpdata['templatic_image_size']; ?></label></th>
				<td>
					<div class="element">
						 <div class="input_wrap">
						 <input type="text" id="templatic_image_size" name="templatic_image_size" value="<?php echo $templatic_image_size; ?>"/> </div>
						</div>
					</div>
				   <label for="ilc_tag_class"><p class="description"><?php _e('The size is in kilobytes, e.g. 1MB = 1024KB. Enter only the number.',DOMAIN);?></p></label>
				</td>
			 </tr> 
		  <tr>
				<th><label><?php _e('Default status for free submissions',DOMAIN);	$post_default_status =  @$tmpdata['post_default_status']; ?></label></th>
				<td>
					<select name="post_default_status">
							<option value="publish" <?php if($post_default_status == 'publish')echo "selected";?>><?php _e('Published',DOMAIN); ?></option>
							<option value="draft" <?php if($post_default_status == 'draft')echo "selected";?>><?php _e('Draft',DOMAIN); ?></option>
						</select>
						 <p class="description"><?php _e('Choose what happens with free listings once they are submitted.',DOMAIN);?></p>
				</td>
			 </tr> 
		<tr>
				<th><label><?php _e('Default status for paid submissions',DOMAIN);	$post_default_status_paid =  @$tmpdata['post_default_status_paid']; ?></label></th>
				<td>
					<select name="post_default_status_paid">
							<option value="publish" <?php if($post_default_status_paid == 'publish')echo "selected";?>><?php _e('Published',DOMAIN); ?></option>
							<option value="draft" <?php if($post_default_status_paid == 'draft')echo "selected";?>><?php _e('Draft',DOMAIN); ?></option>
					</select>
						 <p class="description"><?php _e('Choose what happens with paid listings once they are submitted.',DOMAIN);?></p>
				</td>
			 </tr> 
		
		 <tr>
				<th><label><?php _e('Default status for expired listings',DOMAIN);	$post_listing_ex_status =  @$tmpdata['post_listing_ex_status']; ?></label></th>
				<td>
					<select name="post_listing_ex_status">
							<option value="draft" <?php if($post_listing_ex_status == 'draft')echo "selected";?>><?php _e('Draft',DOMAIN); ?></option>
					 <option value="trash" <?php if($post_listing_ex_status == 'trash')echo "selected";?>><?php _e('Trash',DOMAIN); ?></option>
					</select>
						 <p class="description"><?php _e('Select what happens to listings once they expire.',DOMAIN);?></p>
				</td>
			 </tr> 
			 
		 <tr>
				<th><label><?php _e('Expiry email notification (days)',DOMAIN);	$listing_email_notification =  @$tmpdata['listing_email_notification']; ?></label></th>
				<td>
					<select name="listing_email_notification">
							<option value="">-- Choose One --</option>
					 <option value="1" <?php if($listing_email_notification == '1')echo "selected";?>>1</option>
					 <option value="2" <?php if($listing_email_notification == '2')echo "selected";?>>2</option>
					 <option value="3" <?php if($listing_email_notification == '3')echo "selected";?>>3</option>
					 <option value="4" <?php if($listing_email_notification == '4')echo "selected";?>>4</option>
					 <option value="5" <?php if($listing_email_notification == '5')echo "selected";?>>5</option>
					 <option value="6" <?php if($listing_email_notification == '6')echo "selected";?>>6</option>
					 <option value="7" <?php if($listing_email_notification == '7')echo "selected";?>>7</option>
					 <option value="8" <?php if($listing_email_notification == '8')echo "selected";?>>8</option>
					 <option value="9" <?php if($listing_email_notification == '9')echo "selected";?>>9</option>
					 <option value="10" <?php if($listing_email_notification == '10')echo "selected";?>>10</option>
					</select>
						 <p class="description"><?php _e('When should users receive the expiry notification (choose the number of days before expiry)?',DOMAIN);?></p>
				</td>
			 </tr> 
		<tr>
				<th><label><?php _e('Show terms and conditions',DOMAIN); 
				$tev_accept_term_condition =  @$tmpdata['tev_accept_term_condition'];
				if($tev_accept_term_condition ==1){ $checked ="checked=checked"; }else{
					$checked='';
				}
				?> <label> </th>
				<td>
					<label for="tev_accept_term_condition"><input id="tev_accept_term_condition" type="checkbox" value="1" name="tev_accept_term_condition" <?php echo $checked; ?>/>&nbsp; <?php _e('Enable',DOMAIN); ?></label>
				</td>
			</tr> 
			
			<tr>
				<th><label><?php _e('Terms and condition text',DOMAIN); 
				$term_condition_content =  stripslashes(@$tmpdata['term_condition_content']);
				?> <label> </th>
				<td>
					<textarea class="tb_textarea" id="term_condition_content" name="term_condition_content"><?php echo $term_condition_content; ?></textarea>
					 <p class="description"><?php _e('Enter your terms in the above window. You can use HTML to create a link to your full terms of use page.',DOMAIN);?></p>
				</td>
			</tr>
			<tr>
				<td>
					<p class="submit" style="clear: both;">
					<input type="submit" name="Submit"  class="button-primary" value="<?php _e('Save',DOMAIN);?>" />
					</p>
				</td>
			</tr>
		<tr id="detail_page_settings">
			<th colspan="2"><div class="tevo_sub_title"><?php _e('Detail/Single page settings',DOMAIN);?></div>
		    <br />
		    </th>
		</tr> 
		<?php do_action('before_detail_page_setting');?>                    
							
		 <?php do_action('before_related_post');?>
		 <tr>
				<th><label><?php _e('Filter related posts by',DOMAIN);	$related_post =  @$tmpdata['related_post']; ?></label></th>
				<td>
					<label for="related_post_categories"><input id="related_post_categories" type="radio" name="related_post" value="categories"  <?php if(isset($related_post) && $related_post=='categories') echo 'checked'; ?>/>&nbsp;<?php _e('Category',DOMAIN);?></label>&nbsp;&nbsp;
					<label for="related_post_tags"> <input id="related_post_tags" type="radio" name="related_post" value="tags" <?php if(isset($related_post) && $related_post=='tags') echo 'checked'; ?>/>&nbsp;<?php _e('Tag',DOMAIN);?></label>
				</td>
			 </tr>
			 <tr>
				<th><label><?php _e('Number of related posts shown',DOMAIN);	$related_post_numbers =  @$tmpdata['related_post_numbers']; ?></label></th>
				<td>
					<label for="related_post_numbers">
						<input id="related_post_numbers" type="number" value="<?php if(isset($related_post_numbers)){ echo @$related_post_numbers;}else{ echo 3;}  ?>" size="4" maxlength="4" name="related_post_numbers">
					</label>
				</td>
			 </tr>
		<?php do_action('after_related_post');?>
		
		
		<?php if(!current_theme_supports('remove_tevolution_sharing_opts')){ // Condition to hide this option in supreme2 themes?>                         
		<tr>
			<th><label><?php _e('Show view counter',DOMAIN);	$templatic_view_counter =  @$tmpdata['templatic_view_counter']; ?></label></th>
			<td>
				<div class="element">								 
				<label for="yes"><input type="checkbox" name="templatic_view_counter" value="Yes" <?php if($templatic_view_counter == 'Yes' || $templatic_view_counter ==''){?>checked="checked"<?php }?> id="yes" />&nbsp;<?php _e('Enable',DOMAIN);?>			 					 
				</div>
			</td>
		</tr>
		  <tr>
				<th><label><?php _e('Show sharing options for',DOMAIN);?></label></th>
				<td>
					<?php
					$facebook_share_detail_page =  @$tmpdata['facebook_share_detail_page']; 
					$google_share_detail_page =  @$tmpdata['google_share_detail_page'];
					$twitter_share_detail_page =  @$tmpdata['twitter_share_detail_page'];
					$pintrest_detail_page =  @$tmpdata['pintrest_detail_page'];
					?>
					<label for="facebook_share_detail_page_yes"><input id="facebook_share_detail_page_yes" type="checkbox" name="facebook_share_detail_page" value="yes"  <?php if(isset($facebook_share_detail_page) && $facebook_share_detail_page=='yes') echo 'checked'; ?>/>&nbsp;<?php _e('Facebook',DOMAIN);?></label> <br/>
					
					<label for="google_share_detail_page_yes"><input id="google_share_detail_page_yes" type="checkbox" name="google_share_detail_page" value="yes"  <?php if(isset($google_share_detail_page) && $google_share_detail_page=='yes') echo 'checked'; ?>/>&nbsp;<?php _e('GooglePlus',DOMAIN);?></label> <br/>
					
					<label for="twitter_share_detail_page_yes"><input id="twitter_share_detail_page_yes" type="checkbox" name="twitter_share_detail_page" value="yes"  <?php if(isset($twitter_share_detail_page) && $twitter_share_detail_page=='yes') echo 'checked'; ?>/>&nbsp;<?php _e('Twitter',DOMAIN);?></label> <br/>
					
					<label for="pintrest_detail_page_yes"><input id="pintrest_detail_page_yes" type="checkbox" name="pintrest_detail_page" value="yes"  <?php if(isset($pintrest_detail_page) && $pintrest_detail_page=='yes') echo 'checked'; ?>/>&nbsp;<?php _e('Pintrest',DOMAIN);?></label> <br/>
			   
					<p class="description"><?php _e('Once enabled, selected sharing buttons will appear above the image gallery on detail pages',DOMAIN);?></p>
				</td>
			 </tr>                        
			<?php 
			} 
		do_action('after_detail_page_setting');
		?>
		<tr>
			<td>
				<p class="submit" style="clear: both;">
					<input type="submit" name="Submit"  class="button-primary" value="<?php _e('Save',DOMAIN);?>" />	 
				</p>
			</td>
		</tr>
		<?php
}
	

/*
 * Function Name: post_expire_session_table_create
 * Create the post_expire_session_table_create table
 */
function post_expire_session_table_create(){
	global $wpdb,$pagenow,$table_name;
	$table_name = $wpdb->prefix . "post_expire_session";
	
	if($pagenow=='index.php' || $pagenow=='plugins.php' || (isset($_REQUEST['page']) && ($_REQUEST['page']=='templatic_system_menu' || $_REQUEST['page']=='transcation' || $_REQUEST['page']=='monetization'))){
		
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
		{
			$sql = 'CREATE TABLE `'.$table_name.'` (
						`session_id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`execute_date` DATE NOT NULL ,
						`is_run` TINYINT( 4 ) NOT NULL DEFAULT "0"
					)DEFAULT CHARSET=utf8';
			$wpdb->query($sql);
		}
	
	}
}
/*
 * Function Name: tevolution_custom_fields_notice
 * Return: display message on admin notices for clear tevolution query cache
 */
function tevolution_custom_fields_notice(){
	global $wpdb;
	$taxonomy = get_option("templatic_custom_taxonomy");
	$tag = get_option("templatic_custom_tags");
	if((isset($_REQUEST['page']) && $_REQUEST['page']=='custom_fields' && !isset($_REQUEST['activated'])) || (isset($_REQUEST['taxonomy']) && array_key_exists($_REQUEST['taxonomy'],$taxonomy)) || (isset($_REQUEST['taxonomy']) && array_key_exists($_REQUEST['taxonomy'],$tag))){
			
			if(isset($_POST['tevolution_query']) && $_POST['tevolution_query']!='' && isset($_POST['tevolution_cache']) && $_POST['tevolution_cache']==1){
				$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name like '%s'",'%_tevolution_query_%' ));
				update_option('tevolution_query_cache',0);
				?>
                    <div id="tevolution_message" class="updated fade below-h2">
                    <p><?php _e('Tevolution cache has been successfully cleared.',DOMAIN);?></p>
                    </div>
                    <?php
			}
			
			if(isset($_POST['tevolution_query_cache']) && $_POST['tevolution_query_cache']!=''){				
				update_option('tevolution_cache_disable',$_REQUEST['tevolution_cache_disable']);
			}
			
		$tevolution_query_cache=get_option('tevolution_query_cache');
		$tevolution_cache_disable=get_option('tevolution_cache_disable');
	
		if(!isset($_POST['tevolution_query']))
		{ ?>
			<div id="message" class="update-nag below-h2 tev-cache-msg clearfix" style="width: 50%; height: 40px;">
				
                   <?php if($tevolution_cache_disable ==1){ ?> 
				   <div>
				   <form action="" method="post" style="width: 70%; float: left;">
				   <input type="hidden" name="tevolution_cache" value="1" />
					<p><?php _e('In order to apply the changes made to the site you must clear the Tevolution cache.',DOMAIN);?> <input class="button-primary" type="submit" name="tevolution_query" value="<?php _e('Clear cache',DOMAIN);?>" /></p>	
					</form>
				    <form action="" method="post" style="float: left; margin: 12px 0px;">
						<input type="hidden" name="tevolution_cache_disable" value=""/><input class="button-secondary" type="submit" name="tevolution_query_cache" value="<?php _e('Disable Cache',DOMAIN);?>" />
					</form></div>
                    
					<?php }else{ ?>
					<form action="" method="post" style="width: 50%; float: left; text-align:right;"><p><?php _e('Tevolution caching is disabled.',DOMAIN);?>&nbsp;&nbsp;</p></form>
					<form action="" method="post" style="width: 50%; float: left; margin: 10px 0px; text-align:left;">
					  <input type="hidden" name="tevolution_cache_disable" value="1"/><input class="button-secondary" type="submit" name="tevolution_query_cache" value="<?php _e('Enable Cache',DOMAIN);?>" />	
					</form>
					<?php } ?>
			</div>
        <?php
		}
	}
}

/*
Name: tevolution_post_upgrade_success
Desc: change information for upgraded post

*/
add_action('init','tevolution_post_upgrade_success');
remove_action('paypal_successfull_return_content','successfull_return_paypal_content',10);
function tevolution_post_upgrade_success(){
   if(isset($_REQUEST['pmethod']) && $_REQUEST['pmethod'] !='' && isset($_REQUEST['trans_id']) && $_REQUEST['trans_id'] !='' && isset($_REQUEST['pid']) && $_REQUEST['pid'] !='' & $_REQUEST['upgrade'] =='pkg'){

		remove_action('tevolution_submition_success_post_content','tevolution_submition_success_post_submited_content',10);
		include(TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/post_upgrade_pay_success.php");
   }
}
?>