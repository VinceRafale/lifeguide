<?php
global $wp_query,$wpdb,$wp_rewrite;
/* ACTIVATING MANAGE IP */
if((isset($_REQUEST['activated']) && $_REQUEST['activated'] == 'manage_ip') && (isset($_REQUEST['true']) && $_REQUEST['true'] == 1 )){ 
	update_option('manage_ip','Active'); //ACTIVATING	
} else if( (isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'manage_ip') && (isset($_REQUEST['true']) && $_REQUEST['true'] == 0)){
	delete_option('manage_ip'); //DEACTIVATING
}

/* EOF - MANAGE IP ACTIVATION */
if(is_active_addons('manage_ip')){
	/* define security related Constants */	
	define('IS',__('Is',DOMAIN));
	define('IP_NOT_DETECTED',__('IP not detected',DOMAIN));
	define('IP_IS',__('IP for this post is',DOMAIN));
	define('SUBMITTED_IP',__('Post is submitted from IP',DOMAIN));
	define('BLOCK_IP',__('Block IP',DOMAIN));
	define('UNBLOCK_IP',__('Unblock IP',DOMAIN));
	
	/* INCLUDING A FUNCTIONS FILE */
	if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-manage_ip/manage_ip_functions.php'))
	{
		include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-manage_ip/manage_ip_functions.php");
	}

	add_action("admin_init", "admin_init_func"); /* CALL A FUNCTION TO CREATE A META BOX IN BACK END */
	/*Generate security tab in general setting */
	add_filter('templatic_general_settings_tab', 'security_setting',12); 
	/*display security related option in security tab setting */
	add_action('templatic_general_data','security_setting_general');	
	/*create  */
	add_action('admin_init','ip_settings_table_create');
	
	
}
/*
 * Function Name: ip_settings_table_create
 * Create the ip_settings_table_create
 */
function ip_settings_table_create(){
	global $wpdb,$ip_db_table_name,$pagenow;
	$ip_db_table_name= $wpdb->prefix ."ip_settings";
	if((isset($_REQUEST['activated']) && $_REQUEST['activated']=='manage_ip') && (isset($_REQUEST['true']) && $_REQUEST['true'] == 1 )  || (isset($_REQUEST['tab']) && $_REQUEST['tab']=='security-settings'))
	{	
		/* CREATE A TABLE TO STORE THE BLOCK IP ADDRESS DATA */		
		if($wpdb->get_var("SHOW TABLES LIKE \"$ip_db_table_name\"") != $ip_db_table_name && $ip_db_table_name !=''){
			$ip_table = 'CREATE TABLE IF NOT EXISTS `'.$ip_db_table_name.'` (
			  `ipid` int(11) NOT NULL AUTO_INCREMENT,
			  `ipaddress` varchar(255) NOT NULL,
			  `ipstatus` varchar(25) NOT NULL,
			  PRIMARY KEY (`ipid`)
			)DEFAULT CHARSET=utf8';
			$wpdb->query($ip_table);
		}
		/* EOF - CREATE A TABLE */
	}
}

/*
 * Add Filter for create the security settings tab on general setting menu
 */
function security_setting($tabs ) {
	$tabs['security-settings']=__('Security Settings',DOMAIN);
	return $tabs;
}
/*
 * Satrt the security main general tab
 */
function security_setting_general($tab)
{
	$tmpdata = get_option('templatic_settings');
	switch($tab)
	{
		case 'security-settings':
		?>	
				<tr>
					<th colspan="2">
						<div class="tevo_sub_title" style="margin-top: 0px;"><?php _e("IP Blocking and SSL Settings",DOMAIN);?></div>
					</th>
				</tr>
				<tr>
					<th><label for="ilc_intro"><?php _e('Blocked IP addresses',DOMAIN); ?></label></th>
					<td>
						<?php global $ip_db_table_name,$wpdb;
							$parray = $wpdb->get_results("select ipaddress from $ip_db_table_name where ipstatus='1'");
							$mvalue = ""; ?>
						<textarea name="block_ip" id= "block_ip" class="tb_textarea" ><?php foreach($parray as $pay)
							{
								$ip = $pay->ipaddress;
								$val = $pay->ipaddress;
								if($val != "")
								{
									$mvalue .= $val.",";
								}
							}
							echo trim($mvalue); ?></textarea>
						<input type="hidden" name="ipaddress2" id="ipaddress2" value="<?php echo trim($mvalue); ?>"/><br/>
						<p class="description"><?php _e("Enter IP addresses that you want to block. Separate multiple IPs with a comma.",DOMAIN); ?>.</p>
					</td>
				</tr>
				<tr>
					<th><label><?php _e('Use SSL on submission and registration pages',DOMAIN);	$templatic_is_allow_ssl =  @$tmpdata['templatic-is_allow_ssl']; ?></label></th>
					<td>
                              <label for="templatic-is_allow_ssl"> <input type="checkbox" id="templatic-is_allow_ssl" name="templatic-is_allow_ssl" value="Yes" <?php if($templatic_is_allow_ssl == 'Yes' ){?>checked="checked"<?php }?> /> <?php _e('Enable',DOMAIN);?>
                              <label for="ilc_tag_class"></label><p class="description"><?php _e('Enable this option only if you have a SSL certificate for your domain. Enabling it without the certificate will break the registration / submission process.',DOMAIN);?>.</p>
					</td>
				</tr>
				<tr>
					<th colspan="2">
						<div class="tevo_sub_title"><?php _e("Captcha Settings",DOMAIN);?></div>
					</th>
				</tr>
				<?php
				$user_verification_page =  @$tmpdata['user_verification_page'];?>
				<tr>
				<th><label><?php _e('Active spam verification system',DOMAIN);?></label></th>
	
				<td>
                         <label for="recaptcha"> <input type="radio" id="recaptcha" name="recaptcha" value="recaptcha" <?php if(isset($tmpdata['recaptcha']) && $tmpdata['recaptcha'] == 'recaptcha'){?>checked="checked"<?php }?> /> &nbsp;<?php _e('WP-reCaptcha',DOMAIN);?> </label>&nbsp;
                         
                         <label for="playthru"> <input type="radio" id="playthru" name="recaptcha" <?php if(isset($tmpdata['recaptcha']) &&$tmpdata['recaptcha'] == 'playthru'){?> checked="checked"<?php }?> value="playthru" /> &nbsp;<?php _e('Are You a Human',DOMAIN);?>  </label>
                         <p class="description"><?php _e('To actually use these systems you will first have to install the appropriate plugins. Do so from the Plugins section (by searching for them) or use the following download links: <a href="http://wordpress.org/plugins/wp-recaptcha/">WP-reCAPTCHA</a> | <a href="http://wordpress.org/plugins/are-you-a-human/">Are you a human</a>',DOMAIN)?></p>
					
				</td>
			 </tr>
			 <tr>
				<th><label><?php _e('Enable spam verification for',DOMAIN);?></label></th>
				<td class="captcha_chk">
	
				  <label><input type='checkbox' name="user_verification_page[]" id="user_verification_page" <?php if(count($user_verification_page) > 0 && in_array('registration', $user_verification_page)){ echo "checked=checked"; } ?> value="registration"/> <?php _e('Registration page',DOMAIN); ?></label><div class="clearfix"></div>
				  <label><input type='checkbox' name="user_verification_page[]" id="user_verification_page" <?php if(count($user_verification_page) > 0 && in_array('submit', $user_verification_page)){ echo "checked=checked"; } ?> value="submit"/> <?php _e('Submit listing page',DOMAIN); ?></label><div class="clearfix"></div>				  
				  <label><input type='checkbox' name="user_verification_page[]" id="user_verification_page" <?php if(count($user_verification_page) > 0 && in_array('claim', $user_verification_page)){ echo "checked=checked"; } ?> value="claim"/> <?php _e('Claim Ownership',DOMAIN); ?></label><div class="clearfix"></div>
				   <label><input type='checkbox' name="user_verification_page[]" id="user_verification_page" <?php if(count($user_verification_page) > 0 && in_array('emaitofrd', $user_verification_page)){ echo "checked=checked"; } ?> value="emaitofrd"/> <?php _e('Email to Friend',DOMAIN); ?></label><div class="clearfix"></div><div class="clearfix"></div>
                   	<label><input type='checkbox' name="user_verification_page[]" id="user_verification_page" <?php if(count($user_verification_page) > 0 && in_array('sendinquiry', $user_verification_page)){ echo "checked=checked"; } ?> value="sendinquiry"/> <?php _e('Send Inquiry',DOMAIN); ?></label><div class="clearfix"></div><div class="clearfix"></div>
                         </div>					
				</td>
			 </tr>
			 <?php
		break;		
	}
}
/*Finish the main security general tab */
?>