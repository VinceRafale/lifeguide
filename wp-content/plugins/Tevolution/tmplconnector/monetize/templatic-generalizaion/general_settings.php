<?php
if(isset($_POST["settings-submit"]) && $_POST["settings-submit"] == 'Y' ) 
{
	templatic_save_settings();
	$url_parameters = isset($_GET['tab'])? 'updated=true&tab='.$_GET['tab'] : 'updated=true';
	$sub_url_parameters = isset($_GET['sub_tab'])? '&sub_tab='.$_GET['sub_tab'] : '';
	echo "<script>location.href='".admin_url('admin.php?page=templatic_settings&'.$url_parameters.$sub_url_parameters.'')."'</script>";

}

/*
Name : templatic_save_settings
Description : Save all general settings
*/
function templatic_save_settings() {
   global $pagenow;
   $settings = get_option( "templatic_settings" );    
   $filename = basename($_SERVER['PHP_SELF']);
   if ( $filename == 'admin.php' && $_GET['page'] == 'templatic_settings' )
   {		
		/* POST BLOCKED IP ADDRESSES */
		if(isset($_POST['block_ip']) && $_POST['block_ip']!="")
		{
			/* CALL A FUNCTION TO SAVE IP DATA */			
			insert_ip_address_data($_POST['block_ip']);
		}	
		
		//Saving general settings data: Start
		
			
		if(isset($_REQUEST['tab']) && $_REQUEST['tab']=="security-settings"){
			$_POST['user_verification_page']=isset($_POST['user_verification_page'])?$_POST['user_verification_page']:array();		
			$_POST['templatic-is_allow_ssl']=isset($_POST['templatic-is_allow_ssl'])?$_POST['templatic-is_allow_ssl']:'No';
		}	
		if(isset($_REQUEST['tab']) && $_REQUEST['tab']=="email")
		{
			$_POST['send_to_frnd']=($_POST['send_to_frnd'])?$_POST['send_to_frnd']:'';		
			$_POST['send_inquiry']=($_POST['send_inquiry'])?$_POST['send_inquiry']:'';		
		}
		
		if((isset($_REQUEST['tab']) && $_REQUEST['tab']=="general") || $_REQUEST['tab']=="")
		{
			$_POST['tev_accept_term_condition']=isset($_POST['tev_accept_term_condition'])?$_POST['tev_accept_term_condition']:'';	
			$_POST['sorting_option']=isset($_POST['sorting_option'])?$_POST['sorting_option']:array();
			$_POST['home_listing_type_value']=isset($_POST['home_listing_type_value'])?$_POST['home_listing_type_value']:array();
			
			
			$_POST['facebook_share_detail_page']=isset($_POST['facebook_share_detail_page'])?$_POST['facebook_share_detail_page']:array();
			$_POST['google_share_detail_page']=isset($_POST['google_share_detail_page'])?$_POST['google_share_detail_page']:array();
			$_POST['twitter_share_detail_page']=isset($_POST['twitter_share_detail_page'])?$_POST['twitter_share_detail_page']:array();
			$_POST['pintrest_detail_page']=isset($_POST['pintrest_detail_page'])?$_POST['pintrest_detail_page']:array();
			$_POST['templatin_rating']=isset($_POST['templatin_rating'])?$_POST['templatin_rating']:array();
			$_POST['allow_autologin_after_reg']=isset($_POST['allow_autologin_after_reg'])?$_POST['allow_autologin_after_reg']:array();
			
			$_POST['templatic_view_counter']=isset($_POST['templatic_view_counter'])?$_POST['templatic_view_counter']:'No';
			$_POST['pippoint_oncategory']=isset($_POST['pippoint_oncategory'])?$_POST['pippoint_oncategory']:'';
			
			$_POST['google_map_full_width']=isset($_POST['google_map_full_width'])?$_POST['google_map_full_width']:array();
			
			$_POST['category_map']=isset($_POST['category_map'])?$_POST['category_map']:'';
			$_POST['direction_map']=isset($_POST['direction_map'])?$_POST['direction_map']:'';
			$_POST['category_googlemap_widget']=isset($_POST['category_googlemap_widget'])?$_POST['category_googlemap_widget']:'';
			
			$_POST['templatic-category_custom_fields']=isset($_POST['templatic-category_custom_fields'])?$_POST['templatic-category_custom_fields']:'No';
			
			$_POST['claim_post_type_value']=isset($_POST['claim_post_type_value'])?$_POST['claim_post_type_value']:'';		
			$_POST['listing_hide_excerpt']=(isset($_POST['listing_hide_excerpt']))?$_POST['listing_hide_excerpt']:array();		
			$_POST['related_post_type']=(isset($_POST['related_post_type']))?$_POST['related_post_type']:array();
		}
		foreach($_POST as $key=>$val)
		{
			$settings[$key] = ($_POST[$key]) ? $_POST[$key] : '';
			update_option('templatic_settings',$settings);
		}
		//Saving general settings data: Start
   }
}


// general setting tab filter
add_filter('templatic_general_settings_tab', 'general_setting',10); 
function general_setting($tabs ) {
	
	$tabs['general']=__('General Settings',DOMAIN);
	return $tabs;
}

/*
 * Create email setting data action
 */
add_action('templatic_general_data','email_setting_data',10);
function email_setting_data($column)
{
	$tmpdata = get_option('templatic_settings');		
	switch($column)
	{
		case 'email':	
			?>
			<thead>
				<tr>
					<th class="first-th">
						<label for="email_type" class="form-textfield-label"><?php _e('Email Type',DOMAIN); ?></label>
					</th>
					
					<th class="last-th">
						<label for="email_desc" class="form-textfield-label"><?php _e('Actions',DOMAIN); ?></label>
					</th>
				</tr>
				</thead>
				<tr class="email-to-friend alternate">
					<td>
					<label class="form-textfield-label"><?php _e('Send to friend',DOMAIN); ?></label>
					</td>
					
					<td>
					<a href="javascript:void(0);" title="email-to-friend,edit-email-to-friend" onclick="open_quick_edit('email-to-friend','edit-email-to-friend')"><?php _e("Quick Edit",DOMAIN);?></a> 
					| 
					<a href="javascript:void(0);" onclick="reset_to_default('mail_friend_sub','mail_friend_description','email-to-friend');"><?php _e("Reset",DOMAIN);?></a>
					<span class="spinner" style="margin:2px 18px 0;"></span>
					<span class="qucik_reset"><?php _e("Data reset",DOMAIN);?></span>
					</td>
				</tr>
				<tr class="edit-email-to-friend alternate" style="display:none">
					<td width="100%" colspan="2">
						<h4 class="edit-sub-title"><?php _e("Quick Edit",DOMAIN);?></h4>
						<table width="98%" align="left" class="tab-sub-table">
							<tr>
								<td style="line-height:10px">
									<label class="form-textfield-label sub-title"><?php _e('Subject',DOMAIN); ?></label>
								</td>
								<td width="90%" style="line-height:10px">
									<input type="text" name="mail_friend_sub" id="mail_friend_sub" value="<?php if(isset($tmpdata['mail_friend_sub'])){echo stripslashes($tmpdata['mail_friend_sub']);}else{_e('Check out this post',DOMAIN);} ?>"/>
								</td>
							</tr>
							<tr>
								<td style="line-height:10px">
									<label class="form-textfield-label sub-title"><?php _e('Message',DOMAIN); ?></label>
								</td>
								<td width="90%" style="line-height:10px">
									<?php
									$settings =   array(
													'wpautop' => false, // use wpautop?
													'media_buttons' => false, // show insert/upload button(s)
													'textarea_name' => 'mail_friend_description', // set the textarea name to something different, square brackets [] can be used here
													'textarea_rows' => '7', // rows="..."
													'tabindex' => '',
													'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
													'editor_class' => '', // add extra class(es) to the editor textarea
													'teeny' => true, // output the minimal editor config used in Press This
													'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
													'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
													'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
												);	
									// default settings
									if($tmpdata['mail_friend_description'] != ""){
										$content = stripslashes($tmpdata['mail_friend_description']);
									}else{
										$content = '<p>Hey [#to_name#],</p><p>[#frnd_comments#]</p><p>Link: [#post_title#]</p><p>Cheers<br/>[#your_name#]</p>';
									}
									wp_editor( $content, 'mail_friend_description', $settings);
								?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="buttons">
										<div class="inline_update">
										<a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php _e("Save Changes",DOMAIN);?></a>
										<a class="button-secondary alignright cancel " href="javascript:void(0);" onclick="open_quick_edit('edit-email-to-friend','email-to-friend')" accesskey="c"><?php _e('Cancel',DOMAIN);?></a>
										<span class="save_error" style="display:none"></span><span class="spinner"></span>
										</div>
									</div>	
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="inquiry-email">
					<td><label class="form-textfield-label"><?php _e('Inquiry email',DOMAIN); ?></label></td>
					
					<td>
						<a href="javascript:void(0);" onclick="open_quick_edit('inquiry-email','edit-inquiry-email')"><?php _e("Quick Edit",DOMAIN);?></a>
						|
						<a href="javascript:void(0);" onclick="reset_to_default('send_inquirey_email_sub','send_inquirey_email_description','inquiry-email');"><?php _e("Reset",DOMAIN);?></a>
						<span class="spinner" style="margin:2px 18px 0;"></span>
						<span class="qucik_reset"><?php _e("Data reset",DOMAIN);?></span>
					</td>
				</tr>
				<tr class="edit-inquiry-email" style="display:none">
					<td width="100%" colspan="3">
						<h4 class="edit-sub-title">Quick Edit</h4>
						<table width="98%" align="left" class="tab-sub-table">
							<tr>
								<td style="line-height:10px">
									<label class="form-textfield-label sub-title"><?php _e('Subject',DOMAIN); ?></label>
								</td>
								<td width="90%" style="line-height:10px">
									<input type="text" name="send_inquirey_email_sub" id="send_inquirey_email_sub" value="<?php if(isset($tmpdata['send_inquirey_email_sub'])){echo stripslashes($tmpdata['send_inquirey_email_sub']);}else{echo 'Inquiry email';} ?>"/>
								</td>
							</tr>
							<tr>
								<td style="line-height:10px">
									<label class="form-textfield-label sub-title"><?php _e('Message',DOMAIN); ?></label>
								</td>
								<td width="90%" style="line-height:10px">
									<?php
									$settings =   array(
													'wpautop' => false, // use wpautop?
													'media_buttons' => false, // show insert/upload button(s)
													'textarea_name' => 'send_inquirey_email_description', // set the textarea name to something different, square brackets [] can be used here
													'textarea_rows' => '7', // rows="..."
													'tabindex' => '',
													'editor_css' => '<style>.wp-editor-wrap{width:640px;margin-left:0px;}</style>', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
													'editor_class' => '', // add extra class(es) to the editor textarea
													'teeny' => true, // output the minimal editor config used in Press This
													'dfw' => true, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
													'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
													'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
												);	
									if($tmpdata['send_inquirey_email_description'] != ""){
										$content = stripslashes($tmpdata['send_inquirey_email_description']);
									}else{
										$content = '<p>Hello [#to_name#],</p><p>This is an inquiry regarding the following post: <b>[#post_title#]</b></p><p><b>Subject: [#frnd_subject#]</b></p><p>Link : <b>[#post_title#]</b> </p><p>Contact number : [#contact#]</p><p>[#frnd_comments#]</p><p>Thank you,<br />[#your_name#]</p>';
									}
									wp_editor( $content, 'send_inquirey_email_description', $settings);
								?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="buttons">
									<div class="inline_update"><a class="button-primary save  alignleft quick_save" href="javascript:void(0);" accesskey="s"><?php _e("Save Changes",DOMAIN);?></a></div>
										<div><a class="button-secondary cancel alignright" href="javascript:void(0);" onclick="open_quick_edit('edit-inquiry-email','inquiry-email')" accesskey="c">Cancel</a>
										<span class="save_error" style="display:none"></span><span class="spinner"></span>
										</div>
										
									</div>	
								</td>
							</tr>
						</table>
					</td>
				</tr>
<?php	break;
	}
}
/*
 * Apply filter for get the general setting tabs
 * if you want to create new main tab in general setting menu then use 'templatic_general_settings_tab' filter hook and pass the tabs arrya in filter hook function and return tabs array.
 */
@$tabs = apply_filters('templatic_general_settings_tab',$tabs);	
echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>'; 
?>
<h2><?php _e('Tevolution Settings',DOMAIN);?></h2>
<h2 class="nav-tab-wrapper">
<?php
$i=0;
foreach( $tabs as $tab => $name ){
	if($i==0)	
		$tab_key=$tab;	
		
	$current_tab=isset($_REQUEST['tab'])?$_REQUEST['tab']:$tab_key;			
	$class = ( $tab == $current_tab) ? ' nav-tab-active' : '';				
	echo "<a class='nav-tab$class' href='?page=templatic_settings&tab=$tab'>$name</a>";	
	$i++;
}
echo '</h2>';
/* Finish the general setting menu main tabs */
/*
 * create the general setting sub tabs
 */
if($current_tab=='security-settings'):
	$i=0;	
	/*Add Filter for create the general setting sub tab for Captcha setting */	 
	
	add_filter('templatic_general_settings_tab', 'email_setting_tab',12); 
	function email_setting_tab($tabs){
		$tabs['email']='Email Settings';
		return $tabs;
	}
	
	/*Apply filter for create the general setting subtabs */
	/*
	 * if you want to create new subtabs in general setting menu then use 'templatic_general_settings_subtabs' filter hook function and pass the subtabs array in filter hook function and return subtabs array.
	 */	 
	@$sub_tabs = apply_filters('templatic_general_settings_subtabs',$sub_tabs);	
	
	if(isset($sub_tabs) && $sub_tabs!=''){
	foreach($sub_tabs as $key=>$value)
	{	
		if($i==0)
			$sab_key=$key;				
		$current=isset($_REQUEST['sub_tab'])?$_REQUEST['sub_tab']:$sab_key;
		$class = (isset($current) && ($key == $current)) ? ' nav-tab-active' : '';				
		echo "<a id='$key' class='nav-tab$class' href='?page=templatic_settings&tab=general&sub_tab=$key'>$value</a>";	
		$i++;
	}
	}
	
endif;

/* Display the message */
if(isset($_REQUEST['updated']) && $_REQUEST['updated'] == 'true' ): ?>
	<div class="act_success updated" id="message">
		<p><?php _e("<strong>Record updated successfully</strong>",DOMAIN); ?> .</p>
	</div>
<?php endif; ?>
<!--Finish the display message-->
<div class="templatic_settings">
    <form method="post" class="form_style" action="<?php admin_url( 'themes.php?page=templatic_settings' ); ?>">
	<?php 
		$tmpdata = get_option('templatic_settings');
		if(isset($_REQUEST['tab']) && $_REQUEST['tab'] =='email'){ 
		?>
		<table class="form-table email-wide-table widefat">
		<tbody>
			       	<tr>					
                    <td>
						<p class="tevolution_desc">
							<?php _e('Use this section to manage outgoing emails and messages generated by Tevolution. The outgoing ("From") email address can be changed from',DOMAIN);?>
							<a href="<?php echo admin_url('options-general.php');?>"><?php _e("General Settings",DOMAIN);?></a>
						</p>
                       <table>						
							<tr>
                                <th><label><?php _e('Function for sending email',DOMAIN);?></label></th>
                                <td>
                                    <div class="input_wrap"> <label for="php_mail"><input type="radio" id="php_mail" name="php_mail" value="php_mail" <?php if(isset($tmpdata['php_mail']) && $tmpdata['php_mail'] == 'php_mail'){?>checked="checked"<?php }?> />&nbsp;<?php _e('PHP - mail()',DOMAIN);?></label>&nbsp;&nbsp;&nbsp;&nbsp;
									<label for="wp_smtp"><input type="radio" id="wp_smtp" name="php_mail" <?php if(isset($tmpdata['php_mail']) && $tmpdata['php_mail'] == 'wp_smtp'){?> checked="checked"<?php }?> value="wp_smtp" />&nbsp;<?php _e('WordPress - wp_mail()',DOMAIN);?>
                                    </label></div>
                                   <p class="description"><?php _e("Tevolution uses mail() by default. Change the setting to wp_mail() only if you're using a third-party plugin for modifying outgoing emails.",DOMAIN); ?></p>
                                </td>
							</tr>
                        </table>
                  
                    	 <table >	
                         	<tr>
                            	<th><label><?php _e('Enable additional forms',DOMAIN);?></label></th>
                                <td>
								<div class="input_wrap"> <label for="send_to_frnd"><input type="checkbox" id="send_to_frnd" name="send_to_frnd" value="send_to_frnd" <?php if(isset($tmpdata['send_to_frnd']) && $tmpdata['send_to_frnd'] == 'send_to_frnd'){?>checked="checked"<?php }?> />&nbsp;<?php _e('Send to Friend',DOMAIN);?></label>&nbsp;&nbsp;&nbsp;&nbsp;<br/><label for="send_inquiry"><input type="checkbox" id="send_inquiry" name="send_inquiry" <?php if(isset($tmpdata['send_inquiry']) && $tmpdata['send_inquiry'] == 'send_inquiry'){?> checked="checked"<?php }?> value="send_inquiry" /><?php _e('Send Inquiry',DOMAIN);?>
								</label>
                                </div>
								<p class="description"><?php _e('These forms appear as links on detail pages. They open on click.',DOMAIN); ?></p>
							</td>
                            </tr>
                         </table>
                    </td>
                </tr>
				</tbody>
	</table>
		
	<?php	}
	
	if(isset($_REQUEST['tab']) && $_REQUEST['tab'] =='email' ){
		$tclass= 'widefat post email-wide-table';
	}else{
		$tclass= 'email-wide-table form-table';
	}
	?>
    <table class="<?php echo $tclass; ?>">
    <?php
		$j=0;
		$i=0;
    	foreach( $tabs as $tab => $name ){
			if($j==0)				
				$tab_key=$tab;					
			
			if($current_tab=='general'): /* Display the general setting subtabs menu */
				//display general s etting tab wise displaydata
				do_action('templatic_general_setting_data');
			endif;
			if(isset($_REQUEST['tab']) && $_REQUEST['tab']==$tab):				
				do_action('templatic_general_data',$tab); /* add action hook 'templatic_general_data' for show the general setting tabs data. pass the general setting tabs key. */		
			endif;
			$tab_key="";
			$current_tab='';
			$j++;
		}	
    ?>
    	</table>
    <p class="submit" style="clear: both;">
      <input type="submit" name="Submit"  class="button-primary" value="<?php _e('Save All Settings',DOMAIN);?>" />
      <input type="hidden" name="settings-submit" value="Y" />
    </p>
    </form>
</div>
</div>