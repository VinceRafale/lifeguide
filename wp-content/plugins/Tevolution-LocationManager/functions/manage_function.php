<?php
/* location manager functions - manage_functions.php */
add_action('wp_enqueue_scripts','googlemap_script'); // add goole map script
function googlemap_script(){	
	
	wp_register_script( 'widget-googlemap-js', TEVOLUTION_LOCATION_URL.'js/googlemap.js');
	wp_register_script( 'google-map-js', TEVOLUTION_LOCATION_URL.'js/page_googlemap.js' );
	wp_enqueue_script('location_script',TEVOLUTION_LOCATION_URL.'js/location_script.min.js',array( 'jquery' ),'',false);
}
add_action('admin_head','location_function_style'); // to add style in admin head
add_action('wp_head','location_function_style',true); // to add style in head
function location_function_style()
{
	global $pagenow,$post,$wp_query;
	wp_enqueue_style('location_style',TEVOLUTION_LOCATION_URL.'css/location.css');
	?>
	<script type="text/javascript">
	var ajaxUrl = "<?php echo esc_js( get_bloginfo( 'wpurl' ) . '/wp-admin/admin-ajax.php' ); ?>";
	var default_city_text = '<?php _e('Default City',LM_DOMAIN);?>';
	</script>
	<?php
	wp_enqueue_script('location_script',TEVOLUTION_LOCATION_URL.'js/location_script.min.js',array( 'jquery' ),'',false);
	/* Directory Plugin Style Sheet File */	
}
if(file_exists(TEVOLUTION_LOCATION_DIR.'functions/manage_locations.php')){
	include_once(TEVOLUTION_LOCATION_DIR.'functions/manage_locations.php');
}
if(file_exists(TEVOLUTION_LOCATION_DIR.'functions/location_filter.php')){
	include_once(TEVOLUTION_LOCATION_DIR.'functions/location_filter.php');
}
if(is_admin() && file_exists(TEVOLUTION_LOCATION_DIR.'functions/location_city_logs.php')){
	include_once(TEVOLUTION_LOCATION_DIR.'functions/location_city_logs.php');
}
if(file_exists(TEVOLUTION_LOCATION_DIR.'functions/location_rewrite_rule.php')){
	include_once(TEVOLUTION_LOCATION_DIR.'functions/location_rewrite_rule.php'); 
}
if(file_exists(TEVOLUTION_LOCATION_DIR.'functions/widget_functions.php')){
	include_once(TEVOLUTION_LOCATION_DIR.'functions/widget_functions.php'); 
}
if(file_exists(TEVOLUTION_LOCATION_DIR.'functions/google_map_widget.php')){
	include_once(TEVOLUTION_LOCATION_DIR.'functions/google_map_widget.php');
}
if(file_exists(TEVOLUTION_LOCATION_DIR.'functions/googlemap_listing_widget.php')){
	include_once(TEVOLUTION_LOCATION_DIR.'functions/googlemap_listing_widget.php');
}
if(file_exists(TEVOLUTION_LOCATION_DIR.'functions/location_functions.php')){
	include_once(TEVOLUTION_LOCATION_DIR.'functions/location_functions.php');
}
/*
 * Function Name: get_custom_terms
 * Return: fetch taxonomy category ids;
 */
function get_custom_terms($taxonomies, $args){
	$args = array('orderby'=>'asc');
	$custom_terms = get_terms($taxonomies, $args);
	$count_term = count($custom_terms);
	$sep =',';
	$i=0;
	$termid='';
	foreach($custom_terms as $term){
		if($i == ($count_term-1))
		{ 
			$sep ="";
		}
		$termid .=  $term->term_id.$sep;
		$i++;
	}
	return $termid ;
}
/*
 * Function Name: location_tables_creatation
 * Return: generate country, zone and multicity table
 */
add_action('admin_init','location_tables_creatation');
function location_tables_creatation(){
	global $wpdb,$country_table,$zones_table,$multicity_table,$city_log_table,$pagenow;	
	$country_table = $wpdb->prefix."countries";
	$zones_table =$wpdb->prefix . "zones";	
	$multicity_table = $wpdb->prefix . "multicity";	
	/*
	 * Create postcodes table and save the sorting option in templatic setting on plugin page or tevolution system menu page
	 */
	if($pagenow=='plugins.php' || $pagenow=='themes.php' || (isset($_REQUEST['page']) && ($_REQUEST['page']=='templatic_system_menu' || $_REQUEST['page']=='location_settings' || $_REQUEST['page']=='custom_fields' ))){
		/*Country Table Creation BOF */
		
		if($wpdb->get_var("SHOW TABLES LIKE \"$country_table\"") != $country_table) {
			$create_country = "CREATE TABLE IF NOT EXISTS ".$country_table." (
			country_id int(8) NOT NULL AUTO_INCREMENT,
			country_name varchar(255) NOT NULL,
			iso_code_2 char(2) NOT NULL,
			iso_code_3 char(3) NOT NULL,
			country_flg varchar(255) NOT NULL,
			message text NULL,
			is_enable int(1) NOT NULL DEFAULT '1',
			PRIMARY KEY (country_id))DEFAULT CHARSET=utf8";
			$wpdb->query($create_country);
			$country_file = TEVOLUTION_LOCATION_DIR."functions/csv/country.csv";
			$country_handel = fopen($country_file, 'r');
			$theData = fgets($country_handel);
			$i = 0;
			$j=0;
			$insert_country = "INSERT INTO $country_table(country_id,country_name,iso_code_2,iso_code_3,country_flg) VALUES";
			while (!feof($country_handel)) { 
				$country_data[] = fgets($country_handel, 1024); 
				$country_array = explode(",",$country_data[$i]);			
				if(trim($country_array[0])!='' && trim($country_array[1])!='' && trim($country_array[2])!='' && trim($country_array[3])!='')
				{
					$flag='';
					if(trim($country_array[4])!="")
						$flag=TEVOLUTION_LOCATION_URL."images/flags/".trim($country_array[4]);
						
					$insert_country.="('".trim($country_array[0])."','".mysql_escape_string(trim($country_array[1]))."','".mysql_escape_string(trim($country_array[2]))."','".mysql_escape_string(trim($country_array[3]))."','".$flag."'),";
				}
				$j++;
				$i++;
			}
			
			$wpdb->query(substr($insert_country,0,-1));	
			fclose($country_handel);
		}
		/* Check country flg, country message and is_enable filed in countries table */
		$country_flg = $wpdb->get_var("SHOW COLUMNS FROM $country_table LIKE 'country_flg'");
		if('country_flg'!=$country_flg){			
			$wpdb->query("ALTER TABLE $country_table ADD country_flg varchar(255)");
		}		
		
		$message = $wpdb->get_var("SHOW COLUMNS FROM $country_table LIKE 'message'");
		if('message'!=$message){			
			$wpdb->query("ALTER TABLE $country_table ADD message text");
		}
		$is_enable = $wpdb->get_var("SHOW COLUMNS FROM $country_table LIKE 'is_enable'");
		if('is_enable'!=$is_enable){			
			$wpdb->query("ALTER TABLE $country_table ADD is_enable int(1) DEFAULT '1'");
		}
		
		
		/*Zone Table Creation BOF */		
		if($wpdb->get_var("SHOW TABLES LIKE \"$zones_table\"") != $zones_table) { 
			$create_zones = "CREATE TABLE " . $zones_table . " (
			  zones_id int(8) NOT NULL AUTO_INCREMENT,
			  country_id int(8) NOT NULL,
			  zone_code varchar(10) NOT NULL,
			  zone_name varchar(255) NOT NULL,
			  PRIMARY KEY zones_id (zones_id))DEFAULT CHARSET=utf8;";
			$wpdb->query($create_zones);
			$zones_file = TEVOLUTION_LOCATION_DIR."functions/csv/zones.csv";
			$zones_handel = fopen($zones_file, 'r');
			$theData = fgets($zones_handel);
			$i = 0;
			$j=0;
			$counter=1;
			$insert_zones = "INSERT INTO $zones_table(country_id,zone_code,zone_name) VALUES";
			while (!feof($zones_handel)) { 
				$zones_data[] = fgets($zones_handel, 1024); 
				$zones_array = explode(",",$zones_data[$i]);			
				if(trim($zones_array[0])!='' && trim($zones_array[1])!='' && trim($zones_array[2])!='' && trim($zones_array[3])!='')
				{
					$insert_zones.= "(".trim($zones_array[1]).",'".mysql_escape_string(trim($zones_array[2]))."','".mysql_escape_string(trim($zones_array[3]))."'), ";
				}				
				$i++;
			}		
			$wpdb->query(substr($insert_zones,0,-2));		
			fclose($zones_handel);
		}
		/*zones Table Creation EOF */
		
		/*MultiCity Table Creation BOF */
		$terms1 = get_custom_terms('listingcategory', @$args);
		$terms = 'all,'.$terms1;
				
		if($wpdb->get_var("SHOW TABLES LIKE \"$multicity_table\"") != $multicity_table) {
			$create_multicity = "CREATE TABLE IF NOT EXISTS $multicity_table (
			city_id int(8) NOT NULL AUTO_INCREMENT,
			country_id int(8) NOT NULL,		
			zones_id int(8) NOT NULL,
			cityname varchar(255) NOT NULL,
			city_slug varchar(255) NOT NULL,
			lat varchar(255) NOT NULL,
			lng varchar(255) NOT NULL,
			scall_factor int(100) NOT NULL,
			is_zoom_home varchar(100) NOT NULL,
			map_type varchar(1000) NOT NULL,
			post_type text NOT NULL,		
			categories varchar(255) NOT NULL,		
			is_default tinyint(4) NOT NULL DEFAULT '0',
			message text NOT NULL,
			color varchar(255) NOT NULL DEFAULT '',
			images varchar(255) NOT NULL DEFAULT '',
			header_color varchar(255) NOT NULL DEFAULT '',
			header_image varchar(255) NOT NULL DEFAULT '',
			PRIMARY KEY (city_id))DEFAULT CHARSET=utf8";
			$wpdb->query($create_multicity);	
		
			$insert_muticity = $wpdb->query("INSERT INTO $multicity_table (country_id,zones_id,cityname,city_slug,lat,lng,scall_factor,is_zoom_home,map_type,post_type,categories,is_default) VALUES
			('226','3713','New York','new-york','40.714321', '-74.00579', 13, '0','ROADMAP', 'listing','$terms', 1),
			('226','3721','Philadelphia','philadelphia', '39.952473', '-75.164106', 13, '1','ROADMAP', 'listing','$terms', 0),('226','3682','San Fransisco','san-fransisco', '37.774936', '-122.4194229', 13, '1','ROADMAP', 'listing','$terms', 0)");
		}	
		
		$header_color = $wpdb->get_var("SHOW COLUMNS FROM $multicity_table LIKE 'header_color'");
		if('header_color' != $header_color){
			$wpdb->query("ALTER TABLE $multicity_table ADD header_color varchar(255) NOT NULL DEFAULT ''");
		}
		$header_image = $wpdb->get_var("SHOW COLUMNS FROM $multicity_table LIKE 'header_image'");
		if('header_image' != $header_image){
			$wpdb->query("ALTER TABLE $multicity_table ADD header_image varchar(255) NOT NULL DEFAULT ''");
		}
		
		/*MultiCity Table Creation BOF */
		$city_log_table = $wpdb->prefix . "city_log";	
		if($wpdb->get_var("SHOW TABLES LIKE \"$city_log_table\"") != $city_log_table) {
			$create_city_log = "CREATE TABLE IF NOT EXISTS $city_log_table (
			log_id int(10) NOT NULL AUTO_INCREMENT,
			log_city_id int(10) NOT NULL,		
			log_count int(10) NOT NULL,
			ip_address varchar(255) NOT NULL DEFAULT '',
			PRIMARY KEY (log_id)
			)DEFAULT CHARSET=utf8";
			$wpdb->query($create_city_log);
		}
		
		
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
		/* Insert Multicity custom field */
		 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_city_id' and $wpdb->posts.post_type = 'custom_fields'");
		 if(count($post_content) == 0)
		 {
			$my_post = array(
				 'post_title' => 'Multi City',
				 'post_content' => '',
				 'post_status' => 'publish',
				 'post_author' => 1,
				 'post_name' => 'post_city_id',
				 'post_type' => "custom_fields",
				);
			$post_meta = array(
				'heading_type' => '[#taxonomy_name#]',			
				'ctype'=>'multicity',
				'htmlvar_name'=>'post_city_id',
				'field_category' =>'all',
				'sort_order' => '6',
				'is_active' => '1',
				'is_require' => '0',
				'show_on_page' => 'both_side',
				'show_in_column' => '0',
				'show_on_listing' => '0',
				'is_edit' => 'true',
				'show_on_detail' => '0',
				'is_search'=>'1',
				'show_in_email'  =>'1',
				'is_delete' => '0'
				);
			$post_id = wp_insert_post( $my_post );
			wp_set_post_terms($post_id,'1','category',true);
			//wp_set_post_terms($post_id,'1','category',true);
			foreach($post_meta as $key=> $_post_meta)
			{
				add_post_meta($post_id, $key, $_post_meta);
			}
			
			$post_types=get_option('templatic_custom_post');
			$posttype='post,';
			foreach($post_types as $key=>$val){
				$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $key,'public'   => true, '_builtin' => true ));	
				$posttype.=$key.',';
				update_post_meta($post_id, 'post_type_'.$key,$key );
				update_post_meta($post_id, 'taxonomy_type_'.$taxonomies[0],$taxonomies[0] );
			}
			update_post_meta($post_id, 'post_type_post','post' );
			update_post_meta($post_id, 'taxonomy_type_category','category');
			update_post_meta($post_id, 'post_type',substr($posttype,0,-1));
		}
		
	}// Finish the id condition for check plugin page or tevolution page
	if(isset($_REQUEST['page']) && $_REQUEST['page']=='custom_taxonomy') {
		
		
		$post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_city_id' and $wpdb->posts.post_type = 'custom_fields'");		
		$post_id=$post_content->ID;
		$post_types=get_option('templatic_custom_post');
		$posttype='post,';
		foreach($post_types as $key=>$val){
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $key,'public'   => true, '_builtin' => true ));	
			$posttype.=$key.',';
			update_post_meta($post_id, 'post_type_'.$key,$key );
			update_post_meta($post_id, 'taxonomy_type_'.$taxonomies[0],$taxonomies[0] );
		}
		update_post_meta($post_id, 'post_type_post','post' );
		update_post_meta($post_id, 'taxonomy_type_category','category');
		update_post_meta($post_id, 'post_type',substr($posttype,0,-1));
	}
	
	
	$templatic_settings=get_option('templatic_settings');
	
	if(!isset($templatic_settings['related_post_type'])){
		$post_types=get_option('templatic_custom_post');
		$posttype='';
		foreach($post_types as $key=>$val){
			$posttype[]=$key;	
		}
		
		$settings=array('related_post_type'=>$posttype);	
		update_option('templatic_settings',array_merge($templatic_settings,$settings));
	}
	$templatic_settings=get_option('templatic_settings');
	
}
/* Function for screen option */
function location_settings_option() {
 	global $location_settings_option;
 	$screen = get_current_screen();
 	// get out of here if we are not on our settings page
	if(!is_object($screen) || $screen->id != $location_settings_option)
		return;
 
	$args = array(
		'label' => __('Location setting fields per page', LM_DOMAIN),
		'default' => 25,
		'option' => 'location_setting_fields_per_page'
	);
	add_screen_option( 'per_page', $args );
}
/*
 * Function Name: directory_plugin_settings
 * Display directory settings list
 */
function location_plugin_settings(){
	
	echo '<div id="icon-options-general" class="icon32 clearfix"><br></div>';
	echo "<h1 class=''>".__('Locations',LM_DOMAIN)."</h1>";
	echo '<p class="tevolution_desc">'.__('Using this section you will be able to define country, states and cities which can then be used to filter content on your site. Posts, listings or events added for one city will not show for other cities.',LM_DOMAIN).'</p>';
	echo '<h2 class="nav-tab-wrapper">';	
		$tabs=isset($_REQUEST['location_tabs'])?$_REQUEST['location_tabs']:'';
		location_settings_tabs($tabs);
	echo '</h2>';
	/*do action for directory settings tabs content */
	$tabs_content=isset($_REQUEST['location_tabs'])?$_REQUEST['location_tabs']:'location_manage_locations';
	do_action('location_tabs_content',$tabs_content);	
	
}
/*
 * Function Name: directory_settings_tabs
 * Return: display the directory general settings tabs
 */
function location_settings_tabs($current = 'location_manage_locations'){	
	
	$tabs = apply_filters('location_settings_tabs', array( 				
									'location_manage_locations' => __('Manage Locations',LM_DOMAIN),									
									'location_city_log'=>__('City Logs',LM_DOMAIN),
									));
	
    $links = array();
	if($current=="")
		$current='location_manage_locations';
		
	foreach( $tabs as $tab => $name ) :
		if ( $tab == $current ) :
			$links[] = "<a class='nav-tab nav-tab-active' id='".$tab."_pointer' href='?page=location_settings&location_tabs=$tab'>$name</a>";
		else :
			$links[] = "<a class='nav-tab' id='".$tab."_pointer' href='?page=location_settings&location_tabs=$tab'>$name</a>";
		endif;
	endforeach;
	
	foreach ( $links as $link )
		echo $link;
}
/*
 * Add action for create new multisite custom field type 
 * Function Name: multicity_custom_field_type
 * Return: add multicity type on custom fields menu
 */
add_action('cunstom_field_type','multicity_custom_field_type');
function multicity_custom_field_type($post_id){	
	?>
     <option value="multicity" <?php if(get_post_meta($post_id,"ctype",true)=='multicity'){ echo 'selected="selected"';}?>><?php _e('Multi City',LM_DOMAIN);?></option>
     <?php
}
/*
 * Function Name:directory_multicity_custom_fieldtype
 * display listing multi site custom field on front end
 */
add_action('tevolution_custom_fieldtype','location_multicity_custom_fieldtype',10,3);
function location_multicity_custom_fieldtype($key,$val,$post_type){
	global $wpdb,$country_table,$zones_table,$multicity_table,$validation_info;
	$country_table = $wpdb->prefix."countries";
	$zones_table =$wpdb->prefix . "zones";	
	$multicity_table = $wpdb->prefix . "multicity";	
	$name = $val['name'];
	$site_title = $val['label'];
	$type = $val['type'];
	$htmlvar_name = $val['htmlvar_name'];	
	$admin_desc = $val['desc'];
	$option_values = $val['option_values'];
	$default_value = $val['default'];
	$style_class = $val['style_class'];
	$extra_parameter = $val['extra_parameter'];
	
	
	$location_post_type=explode(',',implode(',',get_option('location_post_type')));
	$ID = $wpdb->get_var("SELECT ID FROM $wpdb->posts where post_name='".$htmlvar_name."'");	
	
	 $milti_city_post=get_post_meta($ID,'post_type_'.$post_type,$post_type);	
	
	if($type=='multicity' && in_array($milti_city_post,$location_post_type)){		
		$validation_info[] = array(
							'title'	       => __('Select Country',LM_DOMAIN),
							'name'	       => 'country_id',
							'espan'	       => 'country_id_error',
							'type'	       => 'select',
							'text'	       => __('Please select country name',LM_DOMAIN),
							'is_require'	  => 1,
							'validation_type'=> 'require'
						);
		$validation_info[] = array(
							'title'	       => __('Select State',LM_DOMAIN),
							'name'	       => 'zones_id',
							'espan'	       => 'zones_id_error',
							'type'	       => 'select',
							'text'	       => __('Please select state name',LM_DOMAIN),
							'is_require'	  => 1,
							'validation_type'=> 'require'
						);
		$validation_info[] = array(
						'title'	       => __('Select City',LM_DOMAIN),
						'name'	       => 'city_id',
						'espan'	       => 'city_id_error',
						'type'	       => 'select',
						'text'	       => __('Please select city name',LM_DOMAIN),
						'is_require'	  => 1,
						'validation_type'=> 'require'
					);
		if(isset($_REQUEST['pid']) && $_REQUEST['pid']!='')
		{
			
			$country_id = get_post_meta($_REQUEST['pid'], 'country_id',true);
			$zones_id = get_post_meta($_REQUEST['pid'], 'zones_id',true);
			$post_city_id = get_post_meta($_REQUEST['pid'], 'post_city_id',true);
		}elseif(isset($_SESSION['custom_fields']) && !empty($_SESSION['custom_fields'])){
			$country_id = $_SESSION['custom_fields']['country_id'];
			$zones_id = $_SESSION['custom_fields']['zones_id'];
			$post_city_id = $_SESSION['custom_fields']['post_city_id'];
		}		
		
		?>
		 <div class="form_row clearfix">
			<label><?php _e('Select Country',LM_DOMAIN);?><span>*</span></label>
               <?php $countryinfo = $wpdb->get_results($wpdb->prepare("SELECT  distinct  c.country_id,c.*  FROM $country_table c,$multicity_table mc where  c.`country_id`=mc.`country_id`  AND c.is_enable=%d group by country_name order by country_name ASC",1));?>
               <select name="country_id" id="country_id" onchange="fill_zones_cmb(this,'1');"  class="textfield textfield_x <?php echo $style_class;?>">
                    <option value=""><?php _e('Select Country',LM_DOMAIN);?></option>
               <?php foreach($countryinfo as $country): $selected=($country->country_id==$country_id)? 'selected':'';
				 $country_name=$country->country_name;
				 if (function_exists('icl_register_string')) {									
						icl_register_string('location-manager', 'location_country_'.$country->country_id,$country_name);
						$country_name = icl_t('location-manager', 'location_country_'.$country->country_id,$country_name);
				  }
			?>
                    <option value="<?php echo $country->country_id?>" <?php echo $selected;?>><?php echo $country_name;?></option>
               <?php endforeach; ?>
               </select>			
			<span class="message_note"></span>
			<span id="country_id_error" class=""></span>
		</div>
          
           <div class="form_row clearfix">
			<label><?php _e('Select State',LM_DOMAIN);?><span>*</span></label>              
                	<select name="zones_id"  id="zones_id" onchange="fill_city_cmb(this);"  class="textfield textfield_x <?php echo $style_class;?>">
                    <option value=""><?php _e('Select State',LM_DOMAIN);?></option>
             		 <?php if($zones_id!=''):										
					$zoneinfo = $wpdb->get_results($wpdb->prepare("SELECT distinct z.zones_id,z.* FROM $zones_table z, $multicity_table mc where z.zones_id=mc.zones_id AND mc.country_id=%d  order by zone_name ASC",$country_id));
					foreach($zoneinfo as $zone): $selected=($zone->zones_id ==$zones_id)? 'selected':'';
						$zone_name=$zone->zone_name;
						 if (function_exists('icl_register_string')) {									
								icl_register_string('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
								$zone_name = icl_t('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
						  }	
					?>
					<option value="<?php echo $zone->zones_id?>" <?php echo $selected;?>><?php echo $zone_name;?></option>
				<?php endforeach;?>
				<?php endif;?>
               </select>
               <span id="process_state" style="display:none;"><img src="<?php echo TEVOLUTION_LOCATION_URL.'images/process.gif'?>" height="16" width="16" /></span>
			<span class="message_note"></span>
			<span id="zones_id_error" class=""></span>			
		</div>
          
           <div class="form_row clearfix">
			<label><?php _e('Select City',LM_DOMAIN);?><span>*</span></label>              
               <select name="<?php echo $key?>" id="city_id"   class="textfield textfield_x <?php echo $style_class;?>">
                    <option value=""><?php _e('Select City',LM_DOMAIN);?></option> 
                    <?php if($post_city_id!=''):
					$cityinfo = $wpdb->get_results($wpdb->prepare("SELECT * FROM $multicity_table where country_id=%d AND zones_id=$zones_id order by cityname ASC",$country_id));
					foreach($cityinfo as $city): $selected=($city->city_id ==$post_city_id)? 'selected':'';
						$cityname=$city->cityname;
						   if (function_exists('icl_register_string')) {									
								icl_register_string('location-manager', 'location_city_'.$city->city_slug,$cityname);
								$cityname = icl_t('location-manager', 'location_city_'.$city->city_slug,$cityname);
						   }
					?>
					<option value="<?php echo $city->city_id?>" <?php echo $selected;?>><?php echo $cityname;?></option>
				<?php endforeach;?>
				<?php endif;?>                 
               </select>
               <span id="process_city" style="display:none;"><img src="<?php echo TEVOLUTION_LOCATION_URL.'images/process.gif'?>" height="16" width="16"  /></span>
			<span class="message_note"></span>
			<span id="city_id_error" class=""></span>
			
		</div>
		<?php
	}
	
}
/*
 * Function Name: directory_backend_custom_field
 * Display the listing multi city custom field display on backend side
 */
add_action('tevolution_backend_custom_fieldtype','directory_backend_custom_field',10,3);
function directory_backend_custom_field($pt_id,$pt_metabox,$post)
{
	global $wpdb,$country_table,$zones_table,$multicity_table,$validation_info;
	$type = $pt_metabox['type'];
	
	$location_post_type=explode(',',implode(',',get_option('location_post_type')));
	$ID = $wpdb->get_var("SELECT ID FROM $wpdb->posts where post_name='".$pt_metabox['htmlvar_name']."'");
	
	if(isset($_REQUEST['post_type']) && $_REQUEST['post_type']!=''){
		$posttype=$_REQUEST['post_type'];
	}else{
		$posttype=(get_post_type($_REQUEST['post']))? get_post_type($_REQUEST['post']) :'post';
	}
	$milti_city_post=get_post_meta($ID,'post_type_'.$posttype,$posttype);	
	
	if($type=='multicity' && in_array($milti_city_post,$location_post_type)){	
		$country_id=get_post_meta($post->ID,'country_id',true);
		$zones_id=get_post_meta($post->ID,'zones_id',true);
		$city_id=get_post_meta($post->ID,'post_city_id',true);
		$post_city_id=explode(',',$city_id);
		if($city_id!='')
			$sql = $wpdb->get_results("SELECT * FROM $multicity_table where city_id in($city_id) order by cityname ASC");		
		//$country_id=$sql[0]->country_id;
		//$zones_id=$sql[0]->zones_id;
		?>
		<tr>
          	<th><label><?php _e('Select Country',LM_DOMAIN);?></label></th>
          	<td>				
                    <?php $countryinfo = $wpdb->get_results($wpdb->prepare("SELECT  distinct  c.country_id,c.*  FROM $country_table c,$multicity_table mc where  c.`country_id`=mc.`country_id`  AND c.is_enable=%d group by country_name order by country_name ASC",1));?>
                    <select name="country_id" id="country_id" onchange="fill_multicity_cmb(this,'1'); fill_zones_cmb(this,'1');"  class="textfield textfield_x <?php echo $style_class;?>">
                         <option value=""><?php _e('Select Country',LM_DOMAIN);?></option>
                    <?php foreach($countryinfo as $country): $selected=($country->country_id==$country_id)? 'selected':'';
						$country_name=$country->country_name;
						 if (function_exists('icl_register_string')) {									
								icl_register_string('location-manager', 'location_country_'.$country->country_id,$country_name);
								$country_name = icl_t('location-manager', 'location_country_'.$country->country_id,$country_name);
						  }
				?>
                         <option value="<?php echo $country->country_id?>" <?php echo $selected;?>><?php echo $country_name;?></option>
                    <?php endforeach; ?>
                    </select>		
               </td>
          </tr>
          <tr>
          	<th><label><?php _e('Select State',LM_DOMAIN);?></label> </th>
          	<td>
               	<select name="zones_id"  id="zones_id" onchange="fill_city_cmb(this);"  class="textfield textfield_x <?php echo $style_class;?>">
                    	<option value=""><?php _e('Select State',LM_DOMAIN);?></option> 
                         <?php if($country_id!=""):?> 
                         <?php $zoneinfo = $wpdb->get_results($wpdb->prepare("SELECT distinct z.zones_id,z.* FROM $zones_table z, $multicity_table mc where z.zones_id=mc.zones_id AND mc.country_id=%d  order by zone_name ASC",$country_id));
						foreach($zoneinfo as $zone): $selected=($zone->zones_id ==$zones_id)? 'selected':'';
							$zone_name=$zone->zone_name;
							 if (function_exists('icl_register_string')) {									
									icl_register_string('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
									$zone_name = icl_t('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
							  }	
						?>
                              <option value="<?php echo $zone->zones_id?>" <?php echo $selected;?>><?php echo $zone_name;?></option>
                         <?php endforeach;?>
                         <?php endif;?>
               	</select>
                    <span id="process_state" style="display:none;"><img src="<?php echo TEVOLUTION_LOCATION_URL.'images/process.gif'?>" height="16" width="16"  /></span>
               </td>
          </tr>
          <tr>
          	<th><label><?php _e('Select MultiCity',LM_DOMAIN);?></label> </th>
          	<td> 
               	<select multiple="multiple"  name="<?php echo $pt_id;?>[]" id="city_id" class="textfield textfield_x <?php echo $style_class;?>">  
                     	<option value=""><?php _e('Select City',LM_DOMAIN);?></option>                  	
                          <?php if($post_city_id!=''):
						$cityinfo = $wpdb->get_results($wpdb->prepare("SELECT * FROM $multicity_table where country_id=%d order by cityname ASC",$country_id));
						foreach($cityinfo as $city): 
							   $selected=(in_array($city->city_id,$post_city_id))? 'selected':'';
							   $cityname=$city->cityname;
							   if (function_exists('icl_register_string')) {									
									icl_register_string('location-manager', 'location_city_'.$city->city_slug,$cityname);
									$cityname = icl_t('location-manager', 'location_city_'.$city->city_slug,$cityname);
							   }
						?>
                              <option value="<?php echo $city->city_id?>" <?php echo $selected;?>><?php echo $cityname;?></option>
                         <?php endforeach;?>
                         <?php endif;?>           
               	</select> <br/>
					<?php do_action('tevolution_multicity');  ?>
					
                    <span id="process_city" style="display:none;"><img src="<?php echo TEVOLUTION_LOCATION_URL.'images/process.gif'?>" height="16" width="16"  /></span>
               </td>
          </tr>
		<?php
		
	}
}
/*
 * Function Name: advancesearch_custom_fieldtype
 * Return : display the multicity field type in advance search shortcodes
 */
add_action('advancesearch_custom_fieldtype','advancesearch_custom_multicitytype',10,3);
function advancesearch_custom_multicitytype($key,$val,$post_type){
	
	global $wpdb,$country_table,$zones_table,$multicity_table;
	
	$countryinfo = $wpdb->get_results($wpdb->prepare("SELECT  distinct  c.country_id,c.*  FROM $country_table c,$multicity_table mc where  c.`country_id`=mc.`country_id`  AND c.is_enable=%d group by country_name order by country_name ASC",1));
	if($default_country_id)
		$zoneinfo = $wpdb->get_results($wpdb->prepare("SELECT * FROM $zones_table z, $multicity_table mc where z.zones_id=mc.zones_id AND mc.country_id=%d  order by zone_name ASC",$default_country_id));
	if($default_zone_id  && $default_country_id)
		$cityinfo = $wpdb->get_results($wpdb->prepare("SELECT * FROM $multicity_table where zones_id=$default_zone_id AND country_id=%d order by cityname ASC",$default_country_id));
	if($val['type']=='multicity'){
		?>
          <div class="form_row clearfix">               
               <select name="adv_country" id="adv_country">
                    <option value=""><?php _e('All Countries',LM_DOMAIN);?></option>
                    <?php foreach($countryinfo as $country): $selected=($country->country_id==$default_country_id)? 'selected':'';
					$country_name=$country->country_name;
					 if (function_exists('icl_register_string')) {									
							icl_register_string('location-manager', 'location_country_'.$country->country_id,$country_name);
							$country_name = icl_t('location-manager', 'location_country_'.$country->country_id,$country_name);
					  }
				?>
                    <option value="<?php echo $country->country_id?>" <?php echo $selected;?>><?php echo $country_name;?></option>
                    <?php endforeach; ?>
               </select>
              
          </div>
           <div class="form_row clearfix">               
               <select name="adv_zone" id="adv_zone">
                    <option value=""><?php _e('All Regions',LM_DOMAIN);?></option>
                    <?php 
					if($zoneinfo){
					foreach($zoneinfo as $zone): $selected=($zone->zones_id ==$default_zone_id)? 'selected':'';
					$zone_name=$zone->zone_name;
					 if (function_exists('icl_register_string')) {									
							icl_register_string('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
							$zone_name = icl_t('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
					  }	
				?>
                    <option value="<?php echo $zone->zones_id?>" <?php echo $selected;?>><?php echo $zone_name;?></option>
                    <?php endforeach;
					} ?>
               </select>
              
          </div>
           <div class="form_row clearfix">             
               <select name="adv_city" id="adv_city">
                    <option value=""><?php _e('All Cities',LM_DOMAIN);?></option>
                    <?php
					if($cityinfo){
					foreach($cityinfo as $city): $selected=($city->city_id ==$default_city_id)? 'selected':'';
						$cityname=$city->cityname;
						if (function_exists('icl_register_string')) {									
								icl_register_string('location-manager', 'location_city_'.$city->city_slug,$cityname);
								$cityname = icl_t('location-manager', 'location_city_'.$city->city_slug,$cityname);
						} ?>
						<option value="<?php echo $city->city_id?>" <?php echo $selected;?>><?php echo $cityname;?></option>
                    <?php endforeach;
					} ?>
               </select>
              
          </div>
          <?php		
	}
}
/*
 * Function Name:directory_multisity_custom_field_save
 * Save the multisite id, country id, zone id when admin user update or new create listing.
 */
add_action('save_post','location_multicity_custom_field_save',12);
function location_multicity_custom_field_save($post_id){
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
	
	$post_address = (isset($_POST['address']))?$_POST['address']:@$_SESSION['custom_fields']['address'];
	$latitude = (isset($_POST['geo_latitude']))?$_POST['geo_latitude']:@$_SESSION['custom_fields']['geo_latitude'];
	$longitude = (isset($_POST['geo_longitude']))?$_POST['geo_longitude']:@$_SESSION['custom_fields']['geo_longitude'];
	$pID = (isset($_POST['post_ID']))?$_POST['post_ID'] : $post_id;
	$post_type=get_post_type( $pID );
	if($post_address && $latitude && $longitude){
		$postcodes_table = $wpdb->prefix . "postcodes";	
		$pcid = $wpdb->get_var($wpdb->prepare("select pcid from $postcodes_table where post_id = %d",$pID));
		if($pcid){
			$wpdb->update($postcodes_table , array('post_type' => $post_type,'address'=>$post_address,'latitude'=>$latitude,'longitude'=>$longitude), array('pcid' => $pcid,'post_id'=>$pID) );		
		}
		else
		{
			$wpdb->query( $wpdb->prepare("INSERT INTO $postcodes_table (post_id, post_type,address, latitude,longitude) VALUES ( %s, %s, %s,%s,%s )", $pID, $post_type, $post_address,$latitude,$longitude) );		
		}
	}	
}
/*
 * Function Name: location_multicity_logs
 * Return: insert/update city log
 */
add_action('init','location_multicity_logs');
function location_multicity_logs(){
	if(!session_id())
		session_start();
	global $city_log_table,$wpdb,$country_table,$zones_table,$multicity_table;
	/* Store header multi city id in settion */
	$country_table = $wpdb->prefix."countries";
	$zones_table =$wpdb->prefix . "zones";
	$multicity_table = $wpdb->prefix . "multicity";
	$city_log_table =$wpdb->prefix . "city_log";	
	if(isset($_POST['header_city']) && $_POST['header_city']!=""){
		$_SESSION['post_city_id']=$_POST['header_city'];			
		$city_log_res = $wpdb->get_row($wpdb->prepare("SELECT log_count FROM $city_log_table where log_city_id=%d AND ip_address=%s",$_POST['header_city'],$_SERVER['REMOTE_ADDR']));
		if(count($city_log_res) == 0)			
			$wpdb->query( $wpdb->prepare("INSERT INTO $city_log_table (log_city_id, log_count,ip_address) VALUES ( %d, %d, %s )", $_POST['header_city'], 1, $_SERVER['REMOTE_ADDR']));
		else	
			$wpdb->query("UPDATE $city_log_table set log_count=log_count+1 where log_city_id=".$_POST['header_city']." and ip_address='".$_SERVER['REMOTE_ADDR']."'" );			
	}
	
	/* Store widget multi city id in settion */
	if(isset($_POST['widget_city']) && $_POST['widget_city']!=""){
		$_SESSION['post_city_id']=$_POST['widget_city'];	
		$city_log_res = $wpdb->get_row($wpdb->prepare("SELECT log_count FROM $city_log_table where log_city_id=%d AND ip_address=%s",$_POST['widget_city'],$_SERVER['REMOTE_ADDR']));
		if(count($city_log_res) == 0)			
			$wpdb->query( $wpdb->prepare("INSERT INTO $city_log_table (log_city_id, log_count,ip_address) VALUES ( %d, %d, %s )", $_POST['widget_city'], 1, $_SERVER['REMOTE_ADDR']));
		else	
			$wpdb->query("UPDATE $city_log_table set log_count=log_count+1 where log_city_id=".$_POST['widget_city']." and ip_address='".$_SERVER['REMOTE_ADDR']."'" );
			
	}
}
/*
 * Function Name: directory_header_location
 * Return: display the directory location on above the header
 */
add_action('after_body','location_header_location',10);
function location_header_location(){
 $directory_multicity_location=get_option('directory_multicity_location');		
 if((!is_author()) && $directory_multicity_location=='location_vertical')
 {	
	global $wpdb,$country_table,$zones_table,$multicity_table,$current_cityinfo;	
	$country_table = $wpdb->prefix."countries";
	$zones_table =$wpdb->prefix . "zones";	
	$multicity_table = $wpdb->prefix . "multicity";	
	
	location_current_multicity(); /* Set the multicity info*/	
	
	$default_country_id=$current_cityinfo['country_id'];
	$default_zone_id=$current_cityinfo['zones_id'];
	$default_city_id=$current_cityinfo['city_id'];
	$city_ids=$wpdb->get_results("SELECT GROUP_CONCAT(distinct meta_value) as city_ids from {$wpdb->prefix}postmeta where `meta_key` ='post_city_id'");
	if($city_ids[0]->city_ids){
		foreach($city_ids as $ids){
			$cityids.=$ids->city_ids.",";
		}
		$cityids=str_replace(",","','",substr($cityids,0,-1));
		$countryinfo = $wpdb->get_results("SELECT  distinct  c.country_id,c.country_name,GROUP_CONCAT(mc.cityname) as cityname, GROUP_CONCAT(mc.city_slug) as city_slug   FROM $country_table c,$multicity_table mc where mc.city_id in('$cityids') AND c.`country_id`=mc.`country_id`  AND c.is_enable=1 group by country_name order by country_name ASC");			
	}
	if($default_country_id)
		$zoneinfo = $wpdb->get_results($wpdb->prepare("SELECT distinct z.zones_id,z.* FROM $zones_table z, $multicity_table mc where z.zones_id=mc.zones_id AND mc.country_id=%d  order by zone_name ASC",$default_country_id));
	if($default_zone_id  && $default_country_id)
		$cityinfo = $wpdb->get_results($wpdb->prepare("SELECT * FROM $multicity_table where zones_id=%d AND country_id=%d order by cityname ASC",$default_zone_id,$default_country_id));
	?>   
    <div id="show_togglebox-button" class="clearfix"><div id="show_togglebox_wrap"><p><?php echo ($current_cityinfo['country_flg']!='')?'<img src="'.$current_cityinfo['country_flg'].'"  alt="'.$current_cityinfo['cityname'].'"/> ' :'';echo ($current_cityinfo['cityname']!='')? $current_cityinfo['cityname']:_e('Location',LM_DOMAIN);?></p><i class="fa fa-map-marker"></i></div>    		
     	<div class="customizer_wrap">
               <div id="header_location">
                    <ul class="location_nav">
                         <li>
                              <select name="header_country" id="header_country">
                                   <option value=""><?php _e('All Countries',LM_DOMAIN);?></option>
                              <?php foreach($countryinfo as $country): $selected=($country->country_id==$default_country_id)? 'selected':'';
							 $country_name=$country->country_name;
							 if (function_exists('icl_register_string')) {									
									icl_register_string('location-manager', 'location_country_'.$country->country_id,$country_name);
									$country_name = icl_t('location-manager', 'location_country_'.$country->country_id,$country_name);
							  }
						
						?>
                                   <option value="<?php echo $country->country_id?>" <?php echo $selected;?>><?php echo $country_name;?></option>
                              <?php endforeach; ?>
                              </select>
                         </li>
                         <li>
                              <select name="header_zone" id="header_zone">
                                   <option value=""><?php _e('All Regions',LM_DOMAIN);?></option>
                              <?php foreach($zoneinfo as $zone): $selected=($zone->zones_id ==$default_zone_id)? 'selected':'';
							 $zone_name=$zone->zone_name;
							 if (function_exists('icl_register_string')) {									
									icl_register_string('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
									$zone_name = icl_t('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
							  }	
						?>
                                   <option value="<?php echo $zone->zones_id?>" <?php echo $selected;?>><?php echo $zone_name;?></option>
                              <?php endforeach;?>
                              </select>
                         </li>
                         <li>
                              <form name="multicity_form" id="multicity_form" action="<?php echo home_url( '/' ); ?>" method="post">
                                   <select name="header_city" id="header_city">
                                        <option value=""><?php _e('All Cities',LM_DOMAIN);?></option>
                                   <?php foreach($cityinfo as $city): $selected=($city->city_id ==$default_city_id)? 'selected':'';
							
								   $cityname=$city->cityname;
								   if (function_exists('icl_register_string')) {									
										icl_register_string('location-manager', 'location_city_'.$city->city_slug,$cityname);
										$cityname = icl_t('location-manager', 'location_city_'.$city->city_slug,$cityname);
								   }
							?>
                                        <option value="<?php echo $city->city_id?>" <?php echo $selected;?>><?php echo $cityname ;?></option>
                                   <?php endforeach;?>
                                   </select>
                              </form>
                         </li>
                         <li>
                         	<a href="<?php echo get_bloginfo( 'url' )."?find_city=nearest"; ?>"><?php _e('My Nearest City',LM_DOMAIN);?></a>
                         </li>
                    </ul>
                    
             </div>
            <p class="city_name clearfix">
				<?php if($current_cityinfo['country_flg']):?>
                    	<img src="<?php echo $current_cityinfo['country_flg']?>" alt="<?php echo $current_cityinfo['cityname']?>"/>
                    <?php endif;?>
			
				<?php echo $current_cityinfo['cityname'];?>
             </p> 
            <div class="city_message clearfix">            	
           	<?php echo $current_cityinfo['message'];?>
            </div>
        </div>                 
      </div>      
     <?php	
 }
}
/*
 *  Show MultiCity Option on horizontal
 * Function Name: directory_header_location
 * Return: display the directory location on above the header 
 */
add_action('after_body','location_header_horizontal',10);
function location_header_horizontal(){
 $directory_multicity_location=get_option('directory_multicity_location');		
 if((!is_author()) && $directory_multicity_location=='location_hoizontal')
 {
	
	global $wpdb,$country_table,$zones_table,$multicity_table,$current_cityinfo;
	
	$country_table = $wpdb->prefix."countries";
	$zones_table =$wpdb->prefix . "zones";	
	$multicity_table = $wpdb->prefix . "multicity";	
	
	location_current_multicity(); /* Set the multicity info*/	
	
	
	$default_country_id=$current_cityinfo['country_id'];
	$default_zone_id=$current_cityinfo['zones_id'];
	$default_city_id=$current_cityinfo['city_id'];	
	
	$city_ids=$wpdb->get_results("SELECT GROUP_CONCAT(distinct meta_value) as city_ids from {$wpdb->prefix}postmeta where `meta_key` ='post_city_id'");
	if($city_ids[0]->city_ids){
		foreach($city_ids as $ids){
			$cityids.=$ids->city_ids.",";
		}
		$cityids=str_replace(",","','",substr($cityids,0,-1));
		$countryinfo = $wpdb->get_results("SELECT  distinct  c.country_id,c.country_name,GROUP_CONCAT(mc.cityname) as cityname, GROUP_CONCAT(mc.city_slug) as city_slug   FROM $country_table c,$multicity_table mc where mc.city_id in('$cityids') AND c.`country_id`=mc.`country_id`  AND c.is_enable=1 group by country_name order by country_name ASC");			
	}
	if($default_country_id)
		$zoneinfo = $wpdb->get_results($wpdb->prepare("SELECT distinct z.zones_id,z.* FROM $zones_table z, $multicity_table mc where z.zones_id=mc.zones_id AND mc.country_id=%d  order by zone_name ASC",$default_country_id));
	if($default_zone_id  && $default_country_id)
		$cityinfo = $wpdb->get_results($wpdb->prepare("SELECT * FROM $multicity_table where zones_id=%d AND country_id=%d order by cityname ASC",$default_zone_id,$default_country_id));
	
	$class_name=(has_nav_menu( 'primary' ))?'primary_location':'';
	?>    
    <div class="togler_handler_wrap clearfix">  
    <div id="horizontal_show_togglebox-button" class="d_location_type_horizontal clearfix">
	     <span id="horizontal_show_togglebox_wrap <?php echo $class_name?>" class="toggle_handler <?php echo $class_name?>"><a href="#" id="directorytab" class="toggle_handler directorytab_open"><?php echo ($current_cityinfo['country_flg']!='')? '<img src="'.$current_cityinfo['country_flg'].'" width="18" height="12" alt="'.$current_cityinfo['cityname'].'" /> ':'';echo ($current_cityinfo['cityname']!='')? $current_cityinfo['cityname']:_e('Location',LM_DOMAIN);?> <i class="fa fa-angle-down"></i></a></span>          		
     	<div id="location_horizontal_wrap">
               <div id="horizontal_header_location" class="d_location_navigation_left">
                    <ul class="horizontal_location_nav">
                         <li>
                              <select name="header_country" id="header_country">
                                   <option value=""><?php _e('All Countries',LM_DOMAIN);?></option>
                              <?php foreach($countryinfo as $country): $selected=($country->country_id==$default_country_id)? 'selected':'';
							 $country_name=$country->country_name;
							 if (function_exists('icl_register_string')) {									
									icl_register_string('location-manager', 'location_country_'.$country->country_id,$country_name);
									$country_name = icl_t('location-manager', 'location_country_'.$country->country_id,$country_name);
							  }
						?>
                                   <option value="<?php echo $country->country_id?>" <?php echo $selected;?>><?php echo $country_name;?></option>
                              <?php endforeach; ?>
                              </select>
                         </li>
                         <li>
                              <select name="header_zone" id="header_zone">
                                   <option value=""><?php _e('All Regions',LM_DOMAIN);?></option>
                              <?php foreach($zoneinfo as $zone): $selected=($zone->zones_id ==$default_zone_id)? 'selected':'';
							$zone_name=$zone->zone_name;
							 if (function_exists('icl_register_string')) {									
									icl_register_string('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
									$zone_name = icl_t('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
							  }	
						?>
                                   <option value="<?php echo $zone->zones_id?>" <?php echo $selected;?>><?php echo $zone_name;?></option>
                              <?php endforeach;?>
                              </select>
                         </li>
                         <li>
                              <form name="multicity_form" id="multicity_form" action="<?php echo home_url( '/' ); ?>" method="post">
                                   <select name="header_city" id="header_city">
                                        <option value=""><?php _e('All Cities',LM_DOMAIN);?></option>
                                   <?php foreach($cityinfo as $city): $selected=($city->city_id ==$default_city_id)? 'selected':'';
								$cityname=$city->cityname;
								   if (function_exists('icl_register_string')) {									
										icl_register_string('location-manager', 'location_city_'.$city->city_slug,$cityname);
										$cityname = icl_t('location-manager', 'location_city_'.$city->city_slug,$cityname);
								   }
							?>
                                        <option value="<?php echo $city->city_id?>" <?php echo $selected;?>><?php echo $cityname ;?></option>
                                   <?php endforeach;?>
                                   </select>
                              </form>
                         </li>
                         <li>
                            <a href="<?php echo get_bloginfo( 'url' )."?find_city=nearest"; ?>"><?php _e('My Nearest City',LM_DOMAIN);?></a>
                         </li>
                    </ul>
             </div>
             <div class='d_location_navigation_right'>
                    <p class="horizontal_city_name clearfix">
                         <?php if($current_cityinfo['country_flg']):?>
                              <img src="<?php echo $current_cityinfo['country_flg']?>" alt="<?php echo $current_cityinfo['cityname']?>"/>
                         <?php endif;?>
                    
                         <?php echo $current_cityinfo['cityname'];?>
                    </p> 
                    <div class="horizontal_city_message clearfix">            	
                    <?php echo $current_cityinfo['message'];?>
                    </div>
            </div>
        </div>
      </div>    
      </div>        
     <?php	
 }
}
/*
 *  Show MultiCity Option on Navigation
 * Function Name: directory_header_location
 * Return: display the directory location on above the header 
 */
add_action('after_body','location_header_navigation',10);
add_action('wp_ajax_nopriv_tev_ajax_headerlocation','tev_ajax_headerlocation');
add_action('wp_ajax_tev_ajax_headerlocation','tev_ajax_headerlocation');
function tev_ajax_headerlocation(){ 
	global $wpdb,$country_table,$zones_table,$multicity_table,$current_cityinfo;	
	
	$country_table = $wpdb->prefix."countries";
	$zones_table =$wpdb->prefix . "zones";	
	$multicity_table = $wpdb->prefix . "multicity";	
	
	location_current_multicity(); /* Set the multicity info*/
	$default_country_id=$current_cityinfo['country_id'];
	$default_zone_id=$current_cityinfo['zones_id'];
	$default_city_id=$current_cityinfo['city_id'];	
	?>
	<div class="my_nearest_city"><a href="<?php echo get_bloginfo( 'url' )."?find_city=nearest"; ?>"><?php _e('My Nearest City',LM_DOMAIN);?></a></div>
	<ul class="horizontal_location_nav">
						
                  <?php
			     $city_ids=$wpdb->get_results("SELECT GROUP_CONCAT(distinct meta_value) as city_ids from {$wpdb->prefix}postmeta where `meta_key` ='post_city_id'");
				if($city_ids[0]->city_ids){
					foreach($city_ids as $ids){
						$cityids.=$ids->city_ids.",";
					}
					$cityids=str_replace(",","','",substr($cityids,0,-1));
					$countryinfo = $wpdb->get_results("SELECT  distinct  c.country_id,c.country_name,GROUP_CONCAT(mc.cityname) as cityname, GROUP_CONCAT(mc.city_slug) as city_slug   FROM $country_table c,$multicity_table mc where mc.city_id in('$cityids') AND c.`country_id`=mc.`country_id`  AND c.is_enable=1 group by country_name order by country_name ASC");			
				}				
					 foreach($countryinfo as $country):
			   		 $country_name=$country->country_name;
			   		 if (function_exists('icl_register_string')) {
						icl_register_string('location-manager', 'location_country_'.$country->country_id,$country_name);
					   	$country_name = icl_t('location-manager', 'location_country_'.$country->country_id,$country_name);
					  }
			   ?>
                    <li>
                        <h3><?php echo $country_name;?></h3>
                          <?php 
                            echo '<div class="cities_names"><ul>';
					   $city_name=explode(',',$country->cityname);
					   $city_slug=explode(',',$country->city_slug);					   
					    for($i=0;$i<count($city_name);$i++){
						   $cityname=$city_name[$i];
						   $city_url= rtrim(get_bloginfo('url'), '/').'/city/'.$city_slug[$i];
						   if (function_exists('icl_register_string')) {
								icl_register_string('location-manager', 'location_city_'.$city_slug[$i],$cityname);
								$cityname = icl_t('location-manager', 'location_city_'.$city_slug[$i],$cityname);
								$city_url= icl_get_home_url().'city/'.$city_slug[$i];
						   }
						   echo '<li><a href="'.$city_url.'">'.$cityname.'</a></li>';
					   }
                            echo '</ul></div>';
                        ?>
                     </li>
                  <?php endforeach;?>
                  
    </ul>
<?php exit; }
function location_header_navigation(){
	$directory_multicity_location=get_option('directory_multicity_location');		
	if($directory_multicity_location=='location_navigation')
	{
	
	global $wpdb,$country_table,$zones_table,$multicity_table,$current_cityinfo;	
	
	$country_table = $wpdb->prefix."countries";
	$zones_table =$wpdb->prefix . "zones";	
	$multicity_table = $wpdb->prefix . "multicity";	
	
	location_current_multicity(); /* Set the multicity info*/
	$default_country_id=$current_cityinfo['country_id'];
	$default_zone_id=$current_cityinfo['zones_id'];
	$default_city_id=$current_cityinfo['city_id'];	
	if((get_option('show_on_front')=='page' && is_front_page()) || is_home()){
		if(strstr($_SERVER['REQUEST_URI'],'/city/')){
			$city_log_table =$wpdb->prefix . "city_log";	
			$city_log_res = $wpdb->get_row($wpdb->prepare("SELECT log_count FROM $city_log_table where log_city_id=%d AND ip_address=%s",$default_city_id,$_SERVER['REMOTE_ADDR']));
			if(count($city_log_res) == 0)
				$wpdb->query( $wpdb->prepare("INSERT INTO $city_log_table (log_city_id, log_count,ip_address) VALUES ( %d, %d, %s )", $default_city_id, 1, $_SERVER['REMOTE_ADDR']));
			else	
				$wpdb->query("UPDATE $city_log_table set log_count=log_count+1 where log_city_id=$default_city_id and ip_address='".$_SERVER['REMOTE_ADDR']."'" );
		}
	}
	/* Finish the  */
	$class_name=(has_nav_menu( 'primary' ))?'primary_location':'';	
 	?>  
    <div class="togler_handler_wrap clearfix">  
    <div id="directory_location_navigation" class="d_location_type_navigation clearfix" >
    	 <span class="toggle_handler <?php echo $class_name?>"><a id="directorytab" href="#" ><?php echo ($current_cityinfo['country_flg']!='')? '<img src="'.$current_cityinfo['country_flg'].'"  width="18" height="12" alt="'.$current_cityinfo['cityname'].'"/> ':'';echo ($current_cityinfo['cityname']!='')? $current_cityinfo['cityname']:_e('Location',LM_DOMAIN);?> <i class="fa fa-angle-down"></i></a></span> 
     	<div id="location_navigation_wrap">
		<div id="location_loading" style="display:none;"><br/><img src="<?php echo TEVOLUTION_LOCATION_URL; ?>/images/process.gif" style="vertical-align:middle;" height="16" width="16" alt="<?php _e('Loading...',LM_DOMAIN); ?>"/>&nbsp;<?php _e('Loading...',LM_DOMAIN); ?></div>
            <div id="horizontal_header_location" class="d_location_navigation_left"></div>
             <div class="d_location_navigation_right">
                 <p class="horizontal_city_name clearfix"><?php if($current_cityinfo['country_flg']):?><img src="<?php echo $current_cityinfo['country_flg']?>" alt="<?php echo $current_cityinfo['cityname']?>"/><?php endif;?><?php echo $current_cityinfo['cityname'];?></p> 
                 <div class="horizontal_city_message clearfix">            	
                    <?php echo $current_cityinfo['message'];?>
                 </div>
            </div>
        </div>
      </div>
      </div>	 	  
     <?php	
 }
}
/*
 * Function Name: directory_location_set
 * Return: set the city wise background color
 */
add_action('after_body','nearest_location_set',9);
function nearest_location_set(){
	global $wpdb,$country_table,$zones_table,$multicity_table,$current_cityinfo,$wp_query;
	$location_tracking= get_option('default_city_set');
	if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; } 
	if((is_home() || is_front_page()) && $location_tracking=='location_tracking')
	{	
		if(!session_id())
			session_start();			
		echo '<div id="nearest_city_load" style="display:none;"><p class="loading_msg">'.__('Please wait, We are taking you to your nearest city.',LM_DOMAIN).'</p></div>';
		if(!isset($_COOKIE['c_latitude']) && !isset($_COOKIE['c_longitude'])): ?>	
		<script type="text/javascript" src="<?php echo $http; ?>gmaps-samples-v3.googlecode.com/svn/trunk/geolocate/geometa.js"></script>       
		<script type="text/javascript"> 
		// <![CDATA[ 
		function doGeolocation(){if(navigator.geolocation){navigator.geolocation.getCurrentPosition(positionSuccess,positionError)}else{positionError(-1)}}function positionError(e){var t;switch(e.code){case e.UNKNOWN_ERROR:t="Unable to find your location";break;case e.PERMISSION_DENINED:t="Permission denied in finding your location";break;case e.POSITION_UNAVAILABLE:t="Your location is currently unknown";break;case e.BREAK:t="Attempt to find location took too long";break;default:t="Location detection not supported in browser"}}onload=doGeolocation()
		 
		 function positionSuccess(position) {
		    // Centre the map on the new location
		    var coords = position.coords || position.coordinate || position;
		    var c_latitude=coords.latitude;
		    var c_longitude=coords.longitude;	
		    
		    setCookie('c_latitude',c_latitude,1);
		    setCookie('c_longitude',c_longitude,1);
		    jQuery('#nearest_city_load').css('display','block');	
		    
		    jQuery.ajax({
				url:ajaxUrl,
				type:'POST',
				data:'action=nearest_location_redirect&c_latitude=' + c_latitude+'&c_longitude='+c_longitude,
				success:function(results) {
					window.location =results;
				}
			});	    
		  }
		 // ]]> 
		</script>
		<?php
		endif;
		
	}
	location_current_multicity(); /* Set the multicity info*/
	
	add_filter('body_class','location_body_class',10,2);
	if($current_cityinfo['color'] || $current_cityinfo['images']):?>
	<style>body{ <?php if($current_cityinfo['color']):?> background-color:<?php echo $current_cityinfo['color'];?>; <?php endif;?> <?php if($current_cityinfo['images']):?> background-image:url('<?php echo $current_cityinfo['images'];?>'); <?php endif;?> }
	div#header, header#header{ <?php if($current_cityinfo['header_color']):?> background-color:<?php echo $current_cityinfo['header_color'];?>; <?php endif;?> <?php if($current_cityinfo['header_image']):?>background-image:url('<?php echo $current_cityinfo['header_image'];?>'); <?php endif;?> }</style>
	<?php endif;
}
/*
 * Function Name: nearest_location_redirect
 * Return: sent nearest city url
 */
add_action('wp_ajax_nopriv_nearest_location_redirect','nearest_location_redirect');
add_action('wp_ajax_nearest_location_redirect','nearest_location_redirect');
function nearest_location_redirect(){
	global $wpdb,$country_table,$zones_table,$multicity_table,$current_cityinfo,$wp_query;	
	$lat = $_REQUEST['c_latitude'];
	$long = $_REQUEST['c_longitude'];
	
	$sql="SELECT distinct city_id, cityname,city_slug FROM  $multicity_table, {$wpdb->prefix}postmeta WHERE meta_key='post_city_id' AND meta_value=city_id and  truncate((degrees(acos( sin(radians(lat)) * sin( radians('".$lat."')) + cos(radians(lat)) * cos( radians('".$lat."')) * cos( radians(lng - '".$long."') ) ) ) * 69.09),1) ORDER BY truncate((degrees(acos( sin(radians(lat)) * sin( radians('".$lat."')) + cos(radians(lat)) * cos( radians('".$lat."')) * cos( radians(lng - '".$long."') ) ) ) * 69.09),1) ASC LIMIT 0,1";
	$nearest_result=$wpdb->get_results($sql);	
	$city_slug=$nearest_result[0]->city_slug;
	
	echo site_url().'/city/'.$city_slug;	
	exit;
}
/*
 * Function Name: location_body_class
 * Return: add citywise background image
 */
function location_body_class($classes,$class){
	global $wpdb,$country_table,$zones_table,$multicity_table,$current_cityinfo,$wp_query;
	if($current_cityinfo['images']){
		$classes[]='city_image';	
	}
	return $classes;
}
/* function to check is table exists */
function is_location_table_exists(){
	global $wpdb;
	$multicity_table = $wpdb->prefix . "multicity";	
	if($wpdb->get_var("SHOW TABLES LIKE \"$multicity_table\"") == $multicity_table){
		return true;
	}else{
		return false;
	}
}
?>