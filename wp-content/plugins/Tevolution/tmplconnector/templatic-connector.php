<?php
session_start();
$no_include = array('templatic-generalizaion','templ_header_section.php','general_settings.php','general_functions.php','add_to_favourites.php  ','templ_footer_section.php','images','.svn');
/*
 * Function Name: tevolution_addons_install_includes
 * Return: include the tevolution add-ons install.php file
 */
$files=array('templatic-bulk_upload','templatic-claim_ownership','templatic-custom_fields','templatic-custom_taxonomy','templatic-manage_ip','templatic-monetization','templatic-ratings','templatic-registration','templatic-widgets');

$files=apply_filters('tevolution-addons_instal_files',$files);		
if(!empty($files)){
	foreach($files as $file){
		if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.$file.'/install.php')){ 
			require_once(TEMPL_MONETIZE_FOLDER_PATH.$file."/install.php" ); 
		}
	}
}// check files variable 


require_once(TEMPL_MONETIZE_FOLDER_PATH."templatic-generalizaion/general_functions.php" );

/* Add to favourites for tevolution*/
if(file_exists(TEMPL_MONETIZE_FOLDER_PATH."templatic-generalizaion/add_to_favourites.php") && (!strstr($_SERVER['REQUEST_URI'],'/wp-admin/') || strstr($_SERVER['REQUEST_URI'],'/admin-ajax.php') )){
	require_once(TEMPL_MONETIZE_FOLDER_PATH."templatic-generalizaion/add_to_favourites.php" );
}

/*
Name : templ_add_admin_menu_
Description : do action for admin menu
*/
add_action('admin_menu', 'templ_add_admin_menu_'); /* create templatic admin menu */
function templ_add_admin_menu_()
{
	do_action('templ_add_admin_menu_');
}


add_action('templ_add_admin_menu_', 'templ_add_mainadmin_menu_', 0);
add_action('templ_add_admin_menu_', 'templ_remove_mainadmin_sub_menu_');
if(!function_exists('templ_remove_mainadmin_sub_menu_')){
	function templ_remove_mainadmin_sub_menu_(){
		remove_submenu_page('templatic_system_menu', 'templatic_system_menu'); 
		add_submenu_page( 'templatic_system_menu', __('Overview',DOMAIN), __('Overview',DOMAIN), 'administrator', 'templatic_system_menu', 'templatic_connector_class' );
		add_submenu_page( 'templatic_system_menu', __('Setup Steps',DOMAIN), __('Setup Steps',DOMAIN), 'administrator', 'templatic_system_menu&tab=setup-steps', 'templatic_connector_class' );
		
		/*Tevolution Submenu Separator	 */
		add_submenu_page('templatic_system_menu', "",   '<span class="tevolution-menu-separator" style="display:block; 1px -5px;  padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>',"administrator", "admin.php?page=templatic_system_menu"  );
	}
}
function templatic_connector_class()
{
	 require_once(TEVOLUTION_PAGE_TEMPLATES_DIR.'classes/main.connector.class.php' );	
}
/*
Name : templ_add_mainadmin_menu_
Description : Return the main menu at admin sidebar
*/
function templ_add_mainadmin_menu_()
{
	$menu_title = __('Tevolution', DOMAIN);
	if (function_exists('add_object_page'))
	{
		if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'templatic_system_menu'){
			$icon = TEMPL_PLUGIN_URL.'favicon-active.png';
		}else{
			$icon = TEMPL_PLUGIN_URL.'favicon-active.png';
		}
		$hook = add_menu_page("Admin Menu", $menu_title, 'administrator', 'templatic_system_menu', 'dashboard_bundles', '',3); // title of new sidebar
	}else{
		add_menu_page("Admin Menu", $menu_title, 'administrator',  'templatic_wp_admin_menu', 'design','');		
	} 
}
/*
Name : dashboard_bundles
Description : return the connection with dashboard wizards(bundle box)
*/
function dashboard_bundles()
{
	$Templatic_connector = New Templatic_connector;
	require_once(TEVOLUTION_PAGE_TEMPLATES_DIR.'classes/main.connector.class.php' );	
	if((!isset($_REQUEST['tab'])&& @$_REQUEST['tab']=='') || (isset($_REQUEST['tab']) && @$_REQUEST['tab'] =='overview')) { 
		require_once(TEMPL_MONETIZE_FOLDER_PATH.'templ_header_section.php' );
		$Templatic_connector->templ_dashboard_bundles();
		$Templatic_connector->templ_dashboard_extends();
		require_once(TEMPL_MONETIZE_FOLDER_PATH.'templ_footer_section.php' );
	}else if(isset($_REQUEST['tab']) && $_REQUEST['tab'] =='setup-steps') { 	
		$Templatic_connector->templ_setup_steps();
	}else if(isset($_REQUEST['tab']) && $_REQUEST['tab'] =='extend') { 	
		$Templatic_connector->templ_extend();
	}else if(isset($_REQUEST['tab']) && $_REQUEST['tab'] =='payment-gateways') { 	
		$Templatic_connector->templ_payment_gateway();
	}
  
}

/*
Name : templ_add_my_stylesheet
Description : return main CSS of Plugin
*/
add_action('admin_head', 'templ_add_my_stylesheet'); /* include style sheet */
add_action('wp_head', 'templ_add_my_stylesheet'); /* include style sheet */	

function templ_add_my_stylesheet()
{
  /* Respects SSL, Style.css is relative to the current file */
  wp_enqueue_script('jquery'); // include jQuery
  wp_enqueue_style('tevolution_style',TEMPL_PLUGIN_URL.'style.css');
  if(function_exists('theme_get_settings')){
	  if(theme_get_settings('supreme_archive_display_excerpt')){
		  if(function_exists('tevolution_excerpt_length')){
			add_filter('excerpt_length', 'tevolution_excerpt_length');
		  }
		  if(function_exists('new_excerpt_more')){
			add_filter('excerpt_more', 'new_excerpt_more');
		  }
	  }
  }
}
/*
Name : is_active_addons
Description : return each add-ons is activated or not
*/
function is_active_addons($key)
{
  $act_key = get_option($key);
  if ($act_key != '')
  {
    return true;
  }
}
/*
Name : templ_remove_dashboard_widgets
Description : Function will remove the admin dashboard widget
*/
function templ_remove_dashboard_widgets()
{
  // Globalize the metaboxes array, this holds all the widgets for wp-admin
  global $wp_meta_boxes;
  // Remove the Dashboard quickpress widget
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
  // Remove the Dashboard  incoming links widget
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
  // Remove the Dashboard secondary widget
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
}
add_action('wp_dashboard_setup', 'templ_remove_dashboard_widgets');

/* -- coding to add submenu under main menu-- */
add_action('templ_add_admin_menu_', 'templ_add_page_menu');
function templ_add_page_menu()
{
	if (is_active_addons('templatic_page-templates') || is_active_addons('templatic-login') || is_active_addons('monetization')  || is_active_addons('claim_ownership') || is_active_addons('custom_fields_templates') || is_active_addons('custom_taxonomy'))
	{
		$menu_title2 = __('General Settings', DOMAIN);
		add_submenu_page('templatic_system_menu', $menu_title2, $menu_title2,'administrator', 'templatic_settings', 'my_page_templates_function');
		
		if(is_active_addons('manage_ip')){
			$security_settings = __('Security Settings', DOMAIN);
			add_submenu_page('templatic_system_menu', $security_settings, $security_settings,'administrator', 'templatic_settings&tab=security-settings', 'templatic_security_setting_function');
		}
		$email_setup = __('Email Settings', DOMAIN);
		add_submenu_page('templatic_system_menu', $email_setup, $email_setup,'administrator', 'templatic_settings&tab=email', 'templatic_email_setup_function');
		
		add_submenu_page('templatic_system_menu', "",   '<span class="tevolution-menu-separator" style="display:block; 1px -5px;  padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>',"administrator", "admin.php?page=templatic_system_menu"  );
	}
}

/*
 * Function Name:tevolution_menu_script
 * Return: email, security , and set up steps menu selected
 */
add_action('admin_footer','tevolution_menu_script');
function tevolution_menu_script()
{
	?>
	<script type="text/javascript">
     jQuery(document).ready(function(){	
          if(jQuery('#adminmenu ul.wp-submenu li').hasClass('current'))
          {
               <?php if(isset($_REQUEST['page']) && $_REQUEST['page']=='templatic_settings' && isset($_REQUEST['tab']) && $_REQUEST['tab']=='email' ):?>
               jQuery('#adminmenu ul.wp-submenu li').removeClass('current');
               jQuery('#adminmenu ul.wp-submenu li a').removeClass('current');								
               jQuery('#adminmenu ul.wp-submenu li a[href*="page=templatic_settings&tab=email"]').attr('href', function() {					    
                        jQuery('#adminmenu ul.wp-submenu li a[href*="page=templatic_settings&tab=email"]').addClass('current');
                        jQuery('#adminmenu ul.wp-submenu li a[href*="page=templatic_settings&tab=email"]').parent().addClass('current');
               });
               <?php endif;?>
               
               <?php if(isset($_REQUEST['page']) && $_REQUEST['page']=='templatic_settings' && isset($_REQUEST['tab']) && $_REQUEST['tab']=='security-settings' ):?>
               jQuery('#adminmenu ul.wp-submenu li').removeClass('current');
               jQuery('#adminmenu ul.wp-submenu li a').removeClass('current');								
               jQuery('#adminmenu ul.wp-submenu li a[href*="page=templatic_settings&tab=security-settings"]').attr('href', function() {					    
                        jQuery('#adminmenu ul.wp-submenu li a[href*="page=templatic_settings&tab=security-settings"]').addClass('current');
                        jQuery('#adminmenu ul.wp-submenu li a[href*="page=templatic_settings&tab=security-settings"]').parent().addClass('current');
               });
               <?php endif;?>
               
               <?php if(isset($_REQUEST['page']) && $_REQUEST['page']=='templatic_system_menu' && isset($_REQUEST['tab']) && $_REQUEST['tab']=='setup-steps' ):?>
               jQuery('#adminmenu ul.wp-submenu li').removeClass('current');
               jQuery('#adminmenu ul.wp-submenu li a').removeClass('current');								
               jQuery('#adminmenu ul.wp-submenu li a[href*="page=templatic_system_menu&tab=setup-steps"]').attr('href', function() {					    
                        jQuery('#adminmenu ul.wp-submenu li a[href*="page=templatic_system_menu&tab=setup-steps"]').addClass('current');
                        jQuery('#adminmenu ul.wp-submenu li a[href*="page=templatic_system_menu&tab=setup-steps"]').parent().addClass('current');
               });
               <?php endif;?>
               
          }
          jQuery('.reset_custom_fields').click( function() {
               if(confirm('All your modifications done with this, will be deleted forever! Still you want to proceed?')){
                    return true;
               }else{
                    return false;
               }	
          });
     });
     </script>
     <?php
}

/* -- coding to add submenu under main menu-- */
function my_page_templates_function()
{	
	include(TEMPL_MONETIZE_FOLDER_PATH.'templatic-generalizaion/general_settings.php');
}

/*
Name : my_plugin_redirect
Description : Redirect on plugin dashboard after activating plugin
*/
add_action('admin_init', 'my_plugin_redirect');
function my_plugin_redirect()
{
  //update_option('myplugin_redirect_on_first_activation', 'false');
  if (get_option('myplugin_redirect_on_first_activation') == 'true')
  {
    update_option('myplugin_redirect_on_first_activation', 'false');
    wp_redirect(MY_PLUGIN_SETTINGS_URL);
  }
}

/*
 * Function Name: view_counter_single_post
 * Argument: post id
 */
function view_counter_single_post($pid){	
	if($_SERVER['HTTP_REFERER'] == '' || !strstr($_SERVER['HTTP_REFERER'],$_SERVER['REQUEST_URI']))
	{
		$viewed_count = get_post_meta($pid,'viewed_count',true);
		$viewed_count_daily = get_post_meta($pid,'viewed_count_daily',true);
		$daily_date = get_post_meta($pid,'daily_date',true);
	
		update_post_meta($pid,'viewed_count',$viewed_count+1);
	if(get_post_meta($pid,'daily_date',true) == date('Y-m-d')){
			update_post_meta($pid,'viewed_count_daily',$viewed_count_daily+1);
		} else {
			update_post_meta($pid,'viewed_count_daily','1');
		}
		update_post_meta($pid,'daily_date',date('Y-m-d'));
	}
}
/*
 * Function Name: get_custom_post_type_template
 * add single post view counter
 */
function get_custom_post_type_template($single_template) {
	global $post;	 
		view_counter_single_post($post->ID);
	
	return $single_template;
}
/*
 * Function Name:user_single_post_visit_count
 * Argument: Post id
 */
if(!function_exists('user_single_post_visit_count')){
function user_single_post_visit_count($pid)
{
	if(get_post_meta($pid,'viewed_count',true))
	{
		return get_post_meta($pid,'viewed_count',true);
	}else
	{
		return '0';	
	}
}
}
/*
 * Function Name:user_single_post_visit_count_daily
 * Argument: Post id
 */
if(!function_exists('user_single_post_visit_count_daily')){
function user_single_post_visit_count_daily($pid)
{
	if(get_post_meta($pid,'viewed_count_daily',true))
	{
		return get_post_meta($pid,'viewed_count_daily',true);
	}else
	{
		return '0';	
	}
}
}
/*
 * Function Name:view_count
 * Argument: post content
 * add view count display after the content
 */
if( !function_exists('view_count')){
function view_count( $content ) {	
	
	if ( is_single()) 
	{
		global $post;
		$sep =" , ";
		$custom_content='';
		$custom_content.="<p>".sprintf(__('Visited %s times',DOMAIN) ,user_single_post_visit_count($post->ID));
		$custom_content.= $sep.user_single_post_visit_count_daily($post->ID).__(" Visits today",DOMAIN)."</p>";
		$custom_content .= $content;
		echo $custom_content;
	} 
}
}
function teamplatic_view_counter()
{
   $settings = get_option( "templatic_settings" );   	
   if(isset($settings['templatic_view_counter']) && $settings['templatic_view_counter']=='Yes')
   {	
		global $post;
		view_counter_single_post($post->ID);
		view_count('');
   }  
   view_sharing_buttons('');
	
}
/*Remove the  the_content filter to add view counter everywhere in single page and add action tmpl_detail_page_custom_fields_collection before the custom field display*/
add_action('tmpl_detail_page_custom_fields_collection','teamplatic_view_counter',5);
function view_sharing_buttons($content)
{
	global $post;	
	if (is_single() && ($post->post_type!='post' && $post->post_type!='page'  && $post->post_type!='product'   && $post->post_type!='product_variation' )) 
	{
		$post_img = bdw_get_images_plugin($post->ID,'thumb');
		$post_images = $post_img[0];
		$title=urlencode($post->post_title);
		$url=urlencode(get_permalink($post->ID));
		$summary=urlencode(htmlspecialchars($post->post_content));
		$image=$post_images;
		$settings = get_option( "templatic_settings" );
		
		if($settings['facebook_share_detail_page'] =='yes' || $settings['google_share_detail_page'] == 'yes' || $settings['twitter_share_detail_page'] == 'yes' || $settings['pintrest_detail_page']=='yes'){
		echo '<div class="share_link">';
			if($settings['facebook_share_detail_page'] == 'yes')
			  {
				?>
				<a onClick="window.open('//www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $title;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&amp;p[images][0]=<?php echo $image;?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)" id="facebook_share_button"><?php _e('Facebook Share.',T_DOMAIN); ?></a>
				<?php
			  }
			if($settings['google_share_detail_page'] == 'yes'): ?>
				<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
				<div class="g-plus" data-action="share" data-annotation="bubble"></div> 
			<?php endif;
			
			if($settings['twitter_share_detail_page'] == 'yes'): ?>
					<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-text='<?php echo htmlentities($post->post_content);?>' data-url="<?php echo get_permalink($post->ID); ?>" data-counturl="<?php echo get_permalink($post->ID); ?>"><?php _e('Tweet',T_DOMAIN); ?></a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			<?php endif;
			
			if(@$settings['pintrest_detail_page']=='yes'):?>
               <!-- Pinterest -->
               <div class="pinterest"> 
                    <a href="//pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;media=<?php echo $image; ?>&amp;description=<?php the_title(); ?>" >Pin It</a>
                    <script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>                    
               </div>
               <?php endif; 
		echo '</div>';
		}
	}
	return $content;
}
/*
Name : templatic_module_activationmsg
Description : this function will return the message related to specific module during activation and deactivation.
*/
function templatic_module_activationmsg($mod_slug='',$mod_name='',$mod_status = '',$mod_message='',$realted_mod =''){
	if(@$_REQUEST['activated'] && @$_REQUEST['activated'] == $mod_slug ){ ?>
		<div class="tevmod-updated">
	
		<?php if($mod_message){
		      echo "<p>".__('Module activated',DOMAIN).". ".__($mod_message,DOMAIN)."</p>"; }
			  if($realted_mod){
		      echo "<p><strong>".__($realted_mod,DOMAIN)."</strong> ".__('Modules are connected with $mod_name, so please activate them too.',DOMAIN)."</p>"; } ?>
		</div>
	<?php }else if(@$_REQUEST['deactivate'] && @$_REQUEST['deactivate'] == $mod_slug ){ ?>
		<div class="tevmod-removed" >
		<?php 
		      echo "<p>".__('Module deactivated',DOMAIN).".".__($mod_message,DOMAIN)."</p>"; 
			  if($realted_mod){
		      echo "<p><strong>".__($realted_mod,DOMAIN)."</strong> ".__('Modules are affected after deactivation of $mod_name.',DOMAIN)."</p>"; } ?>
		</div>
	<?php }
}
/*
name: templatic_get_currency_type
description: fetch currency.*/
function templatic_get_currency_type()
{
	global $wpdb;
	$option_value = get_option('currency_code');
	if($option_value)
	{
		return stripslashes($option_value);
	}else
	{
		return 'USD';
	}
	
}
/* NAME : FETCH CURRENCY
DESCRIPTION : THIS FUNCTION RETURNS THE CURRENCY WITH POSITION SELECTED IN CURRENCY SETTINGS */
function fetch_currency_with_position($amount,$currency = '')
{
	$amt_display = '';
	if($amount==''){ $amount =0; }
	if($amount >=0 )
	{
		$amount = number_format($amount,2);
		$currency = get_option('currency_symbol');
		$position = get_option('currency_pos');
		if($position == '1')
		{
			$amt_display = $currency.$amount;
		}
		else if($position == '2')
		{
			$amt_display = $currency.' '.$amount;
		}
		else if($position == '3')
		{
			$amt_display = $amount.$currency;
		}
		else
		{
			$amt_display = $amount.' '.$currency;
		}
		return $amt_display;
	}
}
/* EOF - DISPLAY CURRENCY WITH POSITION */
/* NAME : TEMPLATIC NOTIFICATION LEGENDS
DESCRIPITION : THIS FUNCTION WILL DISPLAY THE LEGENDS DESCRIPTION ON EMAIL SETTINGS PAGE IN GENERAL SETTINGS */
function templatic_legend_notification()
{
	$legend_display = '<div class="tevo_sub_title">'.__('Email shortcodes',DOMAIN).'  </div>';
	$legend_display .= '<p class="tevolution_desc">'.__('Email shortcodes are essentially variables you can use to display dynamic content. Their availability depends on the performed action meaning your options are limited in any given moment.',DOMAIN).'</p>';
	$legend_display .= '<p style="line-height:30px;width:100%;"><label style="float:left;width:180px;">[#to_name#]</label>'.__('Name of the recipient.',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#site_name#]</label>'.__('Site name as you provided in General Settings',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#site_login_url#]</label>'.__('Site\'s login page URL',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#user_login#]</label>'.__('The users username',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#user_password#]</label>'.__('User password',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#site_login_url_link#]</label>'.__('Login page URL',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#post_date#]</label>'.__('Date of post',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#information_details#]</label>'.__('Details about the submitted post.',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#transaction_details#]</label>'.__('Transaction details.',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#frnd_subject#]</label>'.__('Subject fields for the "Send to friend" form',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#frnd_comments#]</label>'.__('"Send to Friend" content',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#your_name#]</label>'.__('Sender\'s name',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#submited_information_link#]</label>'.__('URL of the detail page',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#payable_amt#]</label>'.__('Payable amount',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#bank_name#]</label>'.__('Bank name',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#account_number#]</label>'.__('Account number',DOMAIN).'<br />
	<label style="float:left;width:180px;">[#submition_Id#]</label>'.__('Submission ID',DOMAIN).'</p>';
	return $legend_display;
}
/* EOF - TEMPLATIC LEGENDS */
/*
Name : tmpl_fetch_currency
Desc : return only currency
*/
function tmpl_fetch_currency(){
	$currency = get_option('currency_symbol');
	if($currency){
		return $currency;
	}else{
		return '$';
	}	
}
/* eof fetch currency*/
/* FUNCTION NAME : TEMPLATIC SEND EMAIL
ARGUMENTS : FROM EMAIL ID, FROM EMAIL NAME, TO EMAIL ID, TO EMAIL NAME, SUBJECT, MESSEGE, HEADERS
RETURNS : THIS FUNCTION IS USED TO SEND EMAILS
*/
function templ_send_email($fromEmail,$fromEmailName,$toEmail,$toEmailName,$subject,$message,$extra='')
{
	
	$fromEmail = apply_filters('templ_send_from_emailid', $fromEmail);
	$fromEmailName = apply_filters('templ_send_from_emailname', $fromEmailName);
	$toEmail = apply_filters('templ_send_to_emailid', $toEmail);
	$toEmailName = apply_filters('templ_send_to_emailname', $toEmailName);
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		// Additional headers
	//$headers .= 'To: '.$toEmailName.' <'.$toEmail.'>' . "\r\n";
	if($fromEmail!="")
	{
		$headers .= 'From: '.$fromEmailName.' <'.$fromEmail.'>' . "\r\n";	
	}else	
		$headers .= 'From: '.get_option('blogname').' <'.get_option('admin_email').'>' . "\r\n";
		
	$subject = apply_filters('templ_send_email_subject', $subject);
	$message = apply_filters('templ_send_email_content', $message);
	$headers = apply_filters('templ_send_email_headers', $headers);	
	// Mail it
	
	if(templ_fetch_mail_type())
	{
		@mail($toEmail, $subject, $message, $headers);	
	}else
	{
		wp_mail($toEmail, $subject, $message, $headers);	
	}
	
}
/* EOF - TEMPLATIC SEND EMAIL */
/* NAME : FETCH MAIL OPTION
DESCRIPTION : THIS FUNCTION WILL FETCH THE EMAIL SETTINGS FOR PHP OR WP MAIL */
function templ_fetch_mail_type()
{
	$tmpdata = get_option('templatic_settings');
	if(@$tmpdata['php_mail'] == 'php_mail')
	{
		return true;	
	}
	return false;
}
/* EOF - FETCH MAIL OPTION */

/* NAME : FETCH CATEGORIES DROPDOWN
DESCRIPTION : THIS FUNCTION WILL FETCH THE CATEGORY DROPDOWN WHILE ADDING A PRICE PACKAGE OR CUSTOM FIELD */
function get_wp_category_checklist_plugin($post_taxonomy,$pid,$show_select_all='')
{
	$pid = explode(',',$pid);	
	global $wpdb;	
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') && (isset($_REQUEST['page']) && $_REQUEST['page']=='monetization' )){
		global $sitepress;
		remove_filter('terms_clauses', array($sitepress, 'terms_clauses'));    
	}
	
	if(is_plugin_active('wpml-translation-management/plugin.php') && (isset($_REQUEST['page']) && $_REQUEST['page']=='custom_fields' ))
	{
		global  $sitepress;		
		$sitepress->switch_lang($_REQUEST['language'],true);		
		$current_lang_code= (isset($_REQUEST['language']) && $_REQUEST['language']!='')? $_REQUEST['language'] :ICL_LANGUAGE_CODE;
	}
	if($show_select_all == '')
		$show_select_all = 1;
	$taxonomy = $post_taxonomy;
	$table_prefix = $wpdb->prefix;
	$wpcat_id = NULL;
	$taxonomy_details = get_option('templatic_custom_taxonomy');
	/* FETCH PARENT CATEGORY */
	if($taxonomy == "")
	{
		$custom_tax = @array_keys(get_option('templatic_custom_taxonomy'));
		$slugs = @implode(",",$custom_tax);
		$slugs .= ",category";
		
		if(is_plugin_active('wpml-translation-management/plugin.php') && (isset($_REQUEST['page']) && $_REQUEST['page']=='custom_fields' )){
			
			$wpcategories = (array)$wpdb->get_results
						("SELECT * FROM {$table_prefix}terms, {$table_prefix}term_taxonomy ,{$table_prefix}icl_translations icl_t
						WHERE {$table_prefix}terms.term_id = {$table_prefix}term_taxonomy.term_id
						AND ({$table_prefix}term_taxonomy.taxonomy in ('" . str_replace(",", "','", $slugs) . "')) and  {$table_prefix}term_taxonomy.parent=0 AND icl_t.element_id = {$table_prefix}term_taxonomy.term_taxonomy_id AND  icl_t.language_code = '".$current_lang_code."' AND icl_t.element_type IN ('tax_" . str_replace(",", "','tax_", $slugs) . "')  ORDER BY {$table_prefix}terms.name");
		}else{
			$wpcategories = (array)$wpdb->get_results
						("SELECT * FROM {$table_prefix}terms, {$table_prefix}term_taxonomy
						WHERE {$table_prefix}terms.term_id = {$table_prefix}term_taxonomy.term_id
						AND ({$table_prefix}term_taxonomy.taxonomy in ('" . str_replace(",", "','", $slugs) . "')) and  {$table_prefix}term_taxonomy.parent=0  ORDER BY {$table_prefix}terms.name");
		}
	}
	else
	{
						
		if(is_plugin_active('wpml-translation-management/plugin.php') && (isset($_REQUEST['page']) && $_REQUEST['page']=='custom_fields' )){
				$wpcategories = (array)$wpdb->get_results
							("SELECT * FROM {$table_prefix}terms, {$table_prefix}term_taxonomy,{$table_prefix}icl_translations icl_t
						WHERE {$table_prefix}terms.term_id = {$table_prefix}term_taxonomy.term_id
						AND {$table_prefix}term_taxonomy.taxonomy ='".$taxonomy."' and  {$table_prefix}term_taxonomy.parent=0 AND icl_t.element_id = {$table_prefix}term_taxonomy.term_taxonomy_id AND  icl_t.language_code = '".$current_lang_code."' AND icl_t.element_type = 'tax_".$taxonomy."'  ORDER BY {$table_prefix}terms.name");
		}else{
			$wpcategories = (array)$wpdb->get_results
						("SELECT * FROM {$table_prefix}terms, {$table_prefix}term_taxonomy
						WHERE {$table_prefix}terms.term_id = {$table_prefix}term_taxonomy.term_id
						AND {$table_prefix}term_taxonomy.taxonomy ='".$taxonomy."' and  {$table_prefix}term_taxonomy.parent=0  ORDER BY {$table_prefix}terms.name");		
		}
	}	
	$wpcategories = array_values($wpcategories);	
	$wpcat2 = NULL;
	
	if($wpcategories)
	{
		$counter = 0;
		
		foreach ($wpcategories as $wpcat)
		{	
			if($counter ==0){
				$tname = $taxonomy_details[$taxonomy]['label']; 
				if($taxonomy =='category' || $taxonomy ==''): 
					if(@$_REQUEST['post_type'] =='' && $show_select_all == 1){
					?>
						<label for="selectall"><input type="checkbox" name="selectall" id="selectall" class="checkbox" onclick="displaychk_frm();" />&nbsp;<?php _e('Select All',	DOMAIN); ?></label><br/>
					<?php 
					$show_select_all++;
					} ?>
				<li><label style="font-weight:bold;"><?php _e('Categories',DOMAIN); ?></label></li>
				<?php else:?>						
						<li><label style="font-weight:bold;"><?php echo $tname; ?></label></li>
			<?php 	
				endif;
			}
		
			$counter++;
			$termid = $wpcat->term_id;;
			$name = ucfirst($wpcat->name); 
			$termprice = $wpcat->term_price;
			$tparent =  $wpcat->parent; ?>
			<li><input type="checkbox" name="category[]" id="<?php echo $termid.$counter; ?>" value="<?php echo $termid; ?>" class="checkbox" <?php if($pid[0]){ if(in_array($termid,$pid) || in_array('all',$pid)){ echo "checked=checked"; } }else{  }?> />&nbsp;<label for="<?php echo $termid.$counter; ?>">&nbsp;<?php echo $name; if($termprice != "") { echo " (".fetch_currency_with_position($termprice).") ";}else{  echo " (".fetch_currency_with_position('0').") "; } ?></label></li>
			<?php if($taxonomy !="")
				{
					$child = get_term_children( $termid, $post_taxonomy );
					$args = array('child_of'	=> $termid,'hide_empty'	=> 0,'taxonomy'=> $post_taxonomy);
		 $categories = get_categories( $args );		
		 foreach($categories as $child_of)
		 { 
			$p = 0;
			//$child_of = $child_of->term_id; 
			//$term = get_term_by( 'id', $child_of,$post_taxonomy);
			$termid = $child_of->term_taxonomy_id;
			$term_tax_id = $child_of->term_id;
			$termprice = $child_of->term_price;
			$name = $child_of->name;			
			if($term_tax_id)
			{
				$catprice = $wpdb->get_row("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id='".$term_tax_id."' and t.term_id = tt.term_id AND tt.taxonomy ='".$taxonomy."'");
				for($i=0;$i<count($catprice);$i++)
				{
					if($catprice->parent)
					{	
						$p++;
						$catprice1 = $wpdb->get_row("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id='".$catprice->parent."' and t.term_id = tt.term_id AND tt.taxonomy ='".$taxonomy."'");
						if($catprice1->parent)
						{
							$i--;
							$catprice = $catprice1;
							continue;
						}
					}
				}
			}
			$p = $p*15;				
		 ?>
			<li style="margin-left:<?php echo $p; ?>px;"><label for="<?php echo $term_tax_id.$p; ?>"><input type="checkbox" name="category[]" id="<?php echo $term_tax_id.$p; ?>" value="<?php echo $term_tax_id; ?>" class="checkbox" <?php if($pid[0]){ if(in_array($term_tax_id,$pid) || in_array('all',$pid)){ echo "checked=checked"; } }else{  }?> />&nbsp;<?php echo $name; if($termprice != "") { echo " (".fetch_currency_with_position($termprice).") ";}else{  echo " (".fetch_currency_with_position('0').") "; } ?></label></li>
		<?php  }	}else{
		 $post_taxonomy  = $wpcat->taxonomy;
		 $child = get_term_children( $termid, $post_taxonomy );
		 if($child ==''){
		 $post_taxonomy  = $wpcat->taxonomy;
		 $child = get_term_children( $termid, $post_taxonomy ); }
		 foreach($child as $child_of)
		 { 
		 	$p = 0;
			$termid = $child_of->term_taxonomy_id;
			$term_tax_id = $child_of->term_id;
			$termprice = $child_of->term_price;
			$name = $child_of->name;		
			if($term_tax_id)
			{
				$catprice = $wpdb->get_row("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id='".$term_tax_id."' and t.term_id = tt.term_id AND (tt.taxonomy ='".$post_taxonomy."')");
				for($i=0;$i<count($catprice);$i++)
				{
					if($catprice->parent)
					{	
						$p++;
						$catprice1 = $wpdb->get_row("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id='".$catprice->parent."' and t.term_id = tt.term_id AND (tt.taxonomy ='".$post_taxonomy."')");
						if($catprice1->parent)
						{
							$i--;
							$catprice = $catprice1;
							continue;
						}
					}
				}
			}
			$p = $p*15;
		 ?>
			<li style="margin-left:<?php echo $p; ?>px;"><label for="<?php echo $term_tax_id.$p; ?>"> <input type="checkbox" name="category[]" id="<?php echo $term_tax_id.$p; ?>" value="<?php echo $term_tax_id; ?>" class="checkbox" <?php if($pid[0]){ if(in_array($term_tax_id,$pid) || in_array('all',$pid)){ echo "checked=checked"; } }else{  }?> />&nbsp;<?php echo $name; if($termprice != "") { echo " (".fetch_currency_with_position($termprice).") ";}else{  echo " (".fetch_currency_with_position('0').") "; } ?></label></li>
		<?php  }	
				}		
	}
	}else{
		$custom_tax = get_option('templatic_custom_taxonomy');
		$post_type = $custom_tax[$post_taxonomy]['post_type'];
		echo '<li class="element" style="font-size:12px; color:red; clear:both;">No category has been created for <strong>'.$post_taxonomy.'</strong>, <a href='.site_url('/wp-admin/edit-tags.php?taxonomy='.$post_taxonomy.'&post_type='.$post_type).'>click here</a> to create category.</li>';
	}
}
/* EOF - FETCH CATEGORIES DROPDOWN */
/*
 * Function Name: changes_post_update_link
 * Argument: post link, before, after ,id
 * Return: update post link
 */
function changes_post_update_link($link)
{
	global $post;
	$postid=$post->ID;
	$post_type=$post->post_type;
	$postdate = $post->post_date;
	//get the submitted page id from post type
	$args=array(	
		'post_type' => 'page',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'meta_query' => array(
								array(
									'key' => 'is_tevolution_submit_form',
									'value' => '1',
									'compare' => '='
									),				
								array(
									'key' => 'submit_post_type',
									'value' => $post_type,
									'compare' => '='
									)
								)
			);
	remove_all_actions('posts_where');
	$the_query  = new WP_Query( $args );	
	if( $the_query->have_posts()):
		foreach($the_query as $post):
			if($post->ID != ""):
				$page_id=$post->ID;
			endif;	
		endforeach;
		//get the front side submitted page id permalink		
		$page_link=get_permalink($page_id);
		$edit_link = '';
		$review_link = '';
		if(strpos($page_link, "?"))
		{
			$edit_link = $page_link."&pid=".$postid."&action=edit";
			$review_link = $page_link."&pid=".$postid."&renew=1";
			$delete_link = $page_link."&pid=".$postid."&page=preview&action=delete";
		}
		else
		{
			$edit_link = $page_link."?pid=".$postid."&action=edit";
			$review_link = $page_link."?pid=".$postid."&renew=1";
			$delete_link = $page_link."?pid=".$postid."&page=preview&action=delete";
		}
		$exp_days = get_time_difference_plugin( $postdate, $postid);
		$link = '';
		if($exp_days > 0 && $exp_days != '' )
		 {
			$link='<a class="post-edit-link" title="Edit Item" href="'.$edit_link.'" target="_blank">'.__('Edit',DOMAIN).'</a>&nbsp;&nbsp;';
		 }
		else
         {		
			$link.='<a class="post-edit-link" title="Renew Item" href="'.$review_link.'" target="_blank">'.__('Renew',DOMAIN).'</a>&nbsp;&nbsp;';
		 }	
		 $link.='&nbsp;<a class="post-edit-link" title="Delete Item" href="'.$delete_link.'" target="_blank">'.__('Delete',DOMAIN).'</a>&nbsp;&nbsp;';
	endif;
	if(is_author()){
		return $link;
	}
}
/*
 * add filter for changes the edit post link for author wise
 */
add_filter('edit_post_link', 'changes_post_update_link');
/* Get expire days */
function get_time_difference_plugin($start, $pid)
{
  if($start)
	{
		$alive_days = get_post_meta($pid,'alive_days',true);
		$uts['start']      =    strtotime( $start );
		$uts['end']        =    mktime(0,0,0,date('m',strtotime($start)),date('d',strtotime($start))+$alive_days,date('Y',strtotime($start)));
	
		//$post_days = gregoriantojd(date('m'), date('d'), date('Y')) - gregoriantojd(date('m',strtotime($start)), date('d',strtotime($start)), date('Y',strtotime($start)));
		$post_days = (strtotime(date("Y-m-d")) - strtotime(date('Y-m-d',strtotime($start))) ) / (60 * 60 * 24);
		$days = $alive_days-$post_days;
	
		if($days>0)
		{
			return $days;	
		}else{
			return( false );
		}
	}
}
/*
name :wpml_insert_templ_post
desc : enter language details when wp_insert_post in process ( during insert the post )
*/
function wpml_insert_templ_post($last_post_id,$post_type){
	global $wpdb,$sitepress;
	$icl_table = $wpdb->prefix."icl_translations";
	$current_lang_code= ICL_LANGUAGE_CODE;
	$element_type = "post_".$post_type;
	$default_languages = ICL_LANGUAGE_CODE;
	$default_language = $sitepress->get_default_language();
	$trid = $wpdb->get_var($wpdb->prepare("select trid from $icl_table order by trid desc LIMIT 0,1"));
	//	echo $insert_tr = " INSERT INTO $icl_table (`translation_id` ,`element_type` ,`element_id` ,`trid` ,`language_code` ,`source_language_code`)VALUES ( '' , '".$element_type."', $last_post_id, $trid , '".$current_lang_code."', '".$current_lang_code."')";
	$update = "update $icl_table set language_code = '".$current_lang_code."' where element_id = '".$last_post_id."'";
	$wpdb->query($update);		/* insert in transactions table */
}

/*
 * Function Name: admin_script
 * return: include wordpress jquery sortable tevolution admin-script in admin side
 */
add_action('admin_head','admin_script');
function admin_script()
{	
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_register_script('admin-script',TEMPL_PLUGIN_URL."js/admin-script.js");
	wp_enqueue_script('admin-script');
	$screen = get_current_screen();	
}
/* Action Edit,renew and delete link on author page */
/*
 * Function Name: tevolution_author_renoew_delete_link 
 * Return: display renew, edit and delete link in author page
 */
add_action('templ_show_edit_renew_delete_link', 'tevolution_author_renoew_delete_link');
function tevolution_author_renoew_delete_link()
{
	global $post,$author_post,$current_user,$wpdb;
	$author_post=$post;	
	$post_author_id=$post->post_author;	
	if((is_author() && is_user_logged_in()) && ($current_user->ID==$post_author_id))
	{
		if( isset($_REQUEST['rm']) && $_REQUEST['rm'] !="" ){
			if(!empty( $_SESSION['templ_file_info']) && (count($_SESSION['templ_file_info']) > 0)){
				foreach($_SESSION['templ_file_info'] as $files){
					@unlink(get_template_directory()."/images/tmp/".$files);
				}
			}
		}
		//$title.=$title;
		$link='';
		$title='';
		$postid=$post->ID;
		$post_type=$post->post_type;
		$postdate = $post->post_date;
		$post_date =  get_the_date('Y-m-d', $postid);
		/*
		 * Get the posted price package details
		 */
		$package_id=get_post_meta($post->ID,'package_select',true);
		$package_name=get_the_title($package_id);
		$alive_days=get_post_meta($post->ID,'alive_days',true);
		$recurring=get_post_meta($package_id,'recurring',true);
		$billing_num=get_post_meta($package_id,'billing_num',true);
		$billing_per=get_post_meta($package_id,'billing_per',true);
		
		
		$expire_date = date_i18n(get_option('date_format'),strtotime("+$alive_days day", strtotime($post_date)));
		if(function_exists('fetch_currency_with_position'))
		{
			$paid_amount=fetch_currency_with_position(get_post_meta($post->ID,'paid_amount',true));
		}
		echo '<div class="author_price_details">';
		
		if (function_exists('icl_register_string')) {									
			$package_name = icl_t('tevolution-price', 'package-name'.$package_id,$package_name);
		}
		
		echo ($package_id)? '<p class="package_name">'.__('<strong>Package Name: </strong>',DOMAIN).$package_name.'</p>' : '';
		echo (get_post_meta($post->ID,'paid_amount',true))? '<p class="package_price">'.__('<strong>Price: </strong>',DOMAIN).$paid_amount.'</p>' : '';		
		
		if($recurring==1){
			if($billing_per=='M')
				$billingper='month';
			elseif($billing_per=='D')
				$billingper='day';
			else
				$billingper='year';
				
			$next_billing_date = date(get_option('date_format'),strtotime("+$billing_num $billingper", strtotime($post_date)));
			echo ($alive_days)? '<p class="package_expire">'.__('<strong>Next Billing will occur on: </strong>',DOMAIN).$next_billing_date.'</p>' : '';
		}else{
			echo ($alive_days)? '<p class="package_expire">'.__('<strong>Expires On: </strong>',DOMAIN).$expire_date.'</p>' : '';
		}
		echo "</div>";
		/* Finish Price Package Details */
		
		//get the submitted page id from post type
		$args=array(	
			'post_type' => 'page',
			'post_status' => 'publish',							
			'meta_query' => array(
								array(
									'key' => 'is_tevolution_submit_form',
									'value' => '1',
									'compare' => '='
									),				
								array(
									'key' => 'submit_post_type',
									'value' => $post_type,
									'compare' => '='
									)
								)
				);
				
		 $upgradeid = $wpdb->get_var("select ID from $wpdb->posts where post_content like '%[post_upgrade%' and post_type='page' and post_status ='publish' LIMIT 0,1");
		$page_upgrade_link = get_permalink($upgradeid);
		remove_all_actions('posts_where');
		$the_query  = new WP_Query( $args );	
		if( $the_query->have_posts()):
			foreach($the_query as $post):
				if(@$post->ID != ""):
					$page_id=$post->ID;
					if(is_plugin_active('wpml-translation-management/plugin.php') && function_exists('icl_object_id')){
						$page_id = icl_object_id( $post->ID, 'page', false, ICL_LANGUAGE_CODE );
					}
				endif;	
			endforeach;						
			//get the front side submitted page id permalink					
			$page_link=get_permalink($page_id);
			$edit_link = '';
			$review_link = '';
			if(strpos($page_link, "?"))
			{
				$edit_link = $page_link."&amp;pid=".$postid."&amp;action=edit";
				$upgrade_link = $page_upgrade_link."&amp;pid=".$postid;
				$review_link = $page_link."&amp;pid=".$postid."&amp;renew=1";
				$delete_link = $page_link."&amp;pid=".$postid."&amp;page=preview&amp;action=delete";
			}
			else
			{
				$edit_link = $page_link."?pid=".$postid."&amp;action=edit";
				$upgrade_link = $page_upgrade_link."?pid=".$postid;
				$review_link = $page_link."?pid=".$postid."&amp;renew=1";
				$delete_link = $page_link."?pid=".$postid."&amp;page=preview&amp;action=delete";
			}
			$exp_days = get_time_difference_plugin( $postdate, $postid);
			$link = '';
			if($exp_days > 0 && $exp_days != '' )
			 {
				$link.='<a class="button secondary_btn tiny_btn post-edit-link" title="Edit Entry" href="'.wp_nonce_url($edit_link,'edit_link').'" target="_blank">'.__('Edit',DOMAIN).'</a>&nbsp;&nbsp;';
				//$link.='<a class="button secondary_btn tiny_btn post-edit-link" title="Upgrade Entry" href="'.wp_nonce_url($upgrade_link,'upgrade_link').'" target="_blank">'.__('Upgrade',DOMAIN).'</a>&nbsp;&nbsp;';
			 }
			else
			 {		
				$link.='<a class="button secondary_btn tiny_btn post-edit-link" title="Renew Entry" href="'.wp_nonce_url($review_link,'renew_link').'" target="_blank">'.__('Renew',DOMAIN).'</a>&nbsp;&nbsp;';
			 }	
			 $link.='<a class="button secondary_btn tiny_btn post-edit-link autor_delete_link" data-deleteid="'.$postid.'" title="Delete Entry" href="javascript:void(0);">'.__('Delete',DOMAIN).'</a>&nbsp;&nbsp;';
			 
		endif;
		$title.=$link;	
		echo $title;
	}
	$post=$author_post;
 
   do_action('templ_cancel_recurring_payment', $delete_link, $exp_days);
}

	 
	 /*Set the Submit listing page */
	 $upgradeid = $wpdb->get_var("select ID from $wpdb->posts where post_content like '%[post_upgrade%' and post_type='page' and post_status ='publish' LIMIT 0,1");
 	 if(count($upgradeid) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Submit Listing',
			 'post_content' => "Upgrade the listing in category of your choice. [post_upgrade']",
			 'post_status' => 'publish',
			 'comment_status' => 'closed',
			 'post_author' => 1,
			 'post_name' => 'post-upgrade',
			 'post_type' => "page",
			);
		$post_meta = array(
			'_wp_page_template' => 'default',
			'_edit_last'        => '1',
			
			);
		$post_id = wp_insert_post( $my_post );		
	 }
/*
 * Function Name: tevolution_listing_after_title
 * Return: display tevolution base templatic page post title like, tevolution archive page, taxonomy, single page and search page
 */
 
add_action('templ_post_title','tevolution_listing_after_title',12);
function tevolution_listing_after_title()
{
	global $post,$htmlvar_name,$posttitle,$wp_query;	
	
	$is_archive = get_query_var('is_ajax_archive');
	if((is_archive() || $is_archive == 1) || is_tax() || is_search()){
		$post_id=get_the_ID();
		$tmpdata = get_option('templatic_settings');
		if($tmpdata['templatin_rating']=='yes'):?>
		   <div class="listing_rating">
				<div class="directory_rating_row"><span class="single_rating"> <?php echo draw_rating_star_plugin(get_post_average_rating($post_id));?> </span></div>
		   </div>
	  <?php elseif(is_plugin_active('Templatic-MultiRating/multiple_rating.php') && function_exists('get_single_average_rating_image')):?>
		<div class="listing_rating">
				<div class="directory_rating_row"><span class="single_rating"> <?php echo get_single_average_rating_image($post_id);?> </span></div>
			</div>	
	<?php endif;
	}
}
/*
 * Function Name: single_post_comment_ratings
 * Return: display the rating start on comment box
 */
add_action('tmpl_before_comments','single_post_comment_ratings',99);
function single_post_comment_ratings()
{
	/* Add ratings after default fields above the comment box, always visible */
     $tmpdata = get_option('templatic_settings');
     if($tmpdata['templatin_rating']=='yes'):
		add_action( 'comment_form_logged_in_after', 'ratings_in_comments' );
		add_action( 'comment_form_after_fields', 'ratings_in_comments' );
		add_action( 'comment_text', 'display_rating_star' );
     endif;	
}
if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php'))
{
	add_action('wp_footer','remove_thoughful_comment_moderate_row',100);
	function remove_thoughful_comment_moderate_row($comments)
	{
		global $post;
		if(get_post_meta($post->ID,'author_moderate',true) != 1)
		{?>
			<script>
				jQuery(document).ready(function() {
					jQuery("p.tc-frontend").remove();
				});
			</script>
		<?php
		}
	}
}
add_action('for_comments','single_post_comment');
function single_post_comment()
{
	global $post;
	 
	if($post->post_status =='publish'){
	?>
		<div id="comments"><?php comments_template(); ?></div>
<?php
	}
}
/*
 * Function Name: single_post_template_head
 * Include the single post image fancybox related script.
 */
add_action('wp_head','single_post_template_head');
function single_post_template_head()
{
	//fetch the tevolution post type
	$custom_post_type=tevolution_get_post_type();
	?>
	
	<script type="text/javascript">var ajaxUrl = "<?php echo esc_js( get_bloginfo( 'wpurl' ) . '/wp-admin/admin-ajax.php' ); ?>";</script>
    <?php
	if((is_single() && in_array(get_post_type(),$custom_post_type)) || (isset($_REQUEST['page']) && $_REQUEST['page']=='preview')){
		?><script type="text/javascript" src="<?php echo CUSTOM_FIELDS_URLPATH; ?>js/jquery.lightbox-0.5.js"></script>
		<script type="text/javascript">
               var IMAGE_LOADING  = '<?php echo CUSTOM_FIELDS_URLPATH."images/lightbox-ico-loading.gif"; ?>';
               var IMAGE_PREV     = '<?php echo CUSTOM_FIELDS_URLPATH."images/lightbox-btn-prev.gif"; ?>';
               var IMAGE_NEXT     = '<?php echo CUSTOM_FIELDS_URLPATH."images/lightbox-btn-next.gif"; ?>';
               var IMAGE_CLOSE    = '<?php echo CUSTOM_FIELDS_URLPATH."images/lightbox-btn-close.gif"; ?>';
               var IMAGE_BLANK    = '<?php echo CUSTOM_FIELDS_URLPATH."images/lightbox-blank.gif"; ?>';
               jQuery(function() {
                    jQuery('#gallery a').lightBox();
               });
          </script>
          <link rel="stylesheet" type="text/css" href="<?php echo CUSTOM_FIELDS_URLPATH; ?>css/jquery.lightbox-0.5.css" media="screen" />	
          <?php
	}
	
	/*Include payment validation script on single preview page */
	if(isset($_REQUEST['page']) && $_REQUEST['page']=='preview'){
		?>
          <script type="text/javascript" src="<?php echo CUSTOM_FIELDS_URLPATH; ?>js/payment_gateway_validation.js"></script>
          <style type="text/css">
			.payment_error{
				color:red;
				font-size:12px;
				display:block;
			}
		</style>
          <script type="text/javascript">
			var $scroll = jQuery.noConflict();
			$scroll(document).ready(function(){
				$scroll(function () {
					$scroll('#back-top a').click(function () {
						$scroll('body,html').animate({
							scrollTop: 0
						}, 800);
						return false;
					});
				});
			});
		</script>
          <?php
	}
}


/*
 * Function Name: tevolution_submit_form_sidebar
 * Return : submit page sidebar
 */
add_action( 'get_sidebar', 'tevolution_submit_form_sidebar');
function tevolution_submit_form_sidebar($name)
{	
	global $post;
	if($name=='primary' || $name==''){
		if(get_post_meta($post->ID,'submit_post_type',true) && get_post_meta($post->ID,'is_tevolution_submit_form',true)){
			
			$post_type=get_post_meta($post->ID,'submit_post_type',true);			
			echo '<div class="sidebar" id="sidebar-primary">';
			dynamic_sidebar('add_'.$post_type.'_submit_sidebar');
			echo '</div>';
		}
	}
}
/*
 * Function Name: tevolution_disable_sidebars
 * Return: disable primary sidebar on submit page
 */
add_filter( 'sidebars_widgets', 'tevolution_disable_sidebars' );
function tevolution_disable_sidebars( $sidebars_widgets ) {	
	
	global $wpdb,$wp_query,$post;	
	if (!is_admin() ) {
		wp_reset_query();
		wp_reset_postdata();
		if(get_post_meta( @$post->ID,'submit_post_type',true) && get_post_meta( @$post->ID,'is_tevolution_submit_form',true))
		{	
			$post_type=get_post_meta($post->ID,'submit_post_type',true);	
			if(!empty($sidebars_widgets['add_'.$post_type.'_submit_sidebar'])){
				$sidebars_widgets['primary'] = false;
				$sidebars_widgets['primary-sidebar'] = false;
			}
		}
	}	
	return $sidebars_widgets;
}
add_action('wp_enqueue_scripts','tevolution_googlemap_script');
/*
Name: tevolution_googlemap_script
Desc: Add google map scripts
*/
function tevolution_googlemap_script(){		
	global $post;
	if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; }
	wp_register_script( 'google-maps-apiscript', $http.'maps.google.com/maps/api/js?sensor=false',false);
	wp_register_script( 'google-clusterig', TEVOLUTION_PAGE_TEMPLATES_URL.'js/markermanager.js',false  );
	
	/* call jquery.filestyle.js file on tevolution submit form and login, register and profile page short codes only */
	if(is_page()){
		$is_tevolution_submit_form=get_post_meta($post->ID,'is_tevolution_submit_form',true);
		$login_id=get_option('tevolution_login');
		$register_id=get_option('tevolution_register');
		$profile_id=get_option('tevolution_profile');
		if($is_tevolution_submit_form==1 || $profile_id==$post->ID || $register_id==$post->ID || $login_id==$post->ID)
			wp_enqueue_script("filetype",TEVOLUTION_PAGE_TEMPLATES_URL.'js/jquery.filestyle.js',array('jquery'));
		
	}
}
/* Find out the google map folder from our other plugin root */
add_action('init','tevolution_googlemap_support',1);
function tevolution_googlemap_support()
{
	$plugin_folder= WP_CONTENT_DIR."/plugins";
	if($handler = opendir($plugin_folder)) {
	  while (($sub = readdir($handler)) !== FALSE) {
		 if ($sub != "." && $sub != ".." && $sub != "Thumb.db" && $sub != "Thumbs.db") {			 
			if(is_dir($plugin_folder."/".$sub) && stripos($sub, 'Tevolution')!== false) {
				
				$plugin=explode('Tevolution-',$sub);
	
				if($plugin[0] ==''){
				if(is_plugin_active($sub.'/'.strtolower($plugin[1]).'.php')){
					$google_maps=read_folder_directory($plugin_folder."/".$sub);
					if(!empty($google_maps)){
						if(file_exists($google_maps.'/google_maps.php')){
							include_once($google_maps.'/google_maps.php');
							break;
						}
					}
				} }
			}
		 }
	  }
	  closedir($handler);
     }
}
/*
 * Function Name: read_folder_directory
 * Return: find out the google_maps Folder inside the tevolution folder
 */
function read_folder_directory($dir)
{
   $listDir = array();
   if($handler = opendir($dir)) {
	  while (($sub = readdir($handler)) !== FALSE) {		 
		 if ($sub != "." && $sub != ".." && $sub != "Thumb.db" && $sub != "Thumbs.db") {
			if(is_dir($dir."/".$sub) && stripos($sub, 'google-maps')!== false){
			    $listDir = $dir."/".$sub;
			}
		 }
	  }
	  closedir($handler);
   }
   return $listDir;
} 
/*
 * Wp_ajax action call for saving email related settings
 * Function Name: save_email_data_callback
 * Return: save the email related settings data 
 */
add_action('wp_ajax_nopriv_save_email_data','save_email_data_callback');
add_action('wp_ajax_save_email_data','save_email_data_callback');
function save_email_data_callback(){
	global $wpdb;
	$settings = get_option( "templatic_settings" );
	$a = array();
	foreach($_REQUEST as $key=>$val){
		if(!current_theme_supports('listing_excerpt_setting') && $key=='listing_hide_excerpt')
			continue;
		$settings[$key] = isset($_REQUEST[$key]) ? $_REQUEST[$key] : '';
		$a[$key] = $val;
		if (function_exists('icl_register_string')) {
			icl_register_string(DOMAIN,$key,$val);
		}
	}
	update_option('templatic_settings', $settings);
	echo $b = json_encode($a);
	exit;
}
/*
 * Wp_ajax action call for reset email related settings
 * Function Name: reset_email_data_callback
 * Return: reset the email related settings data 
 */
add_action('wp_ajax_nopriv_reset_email_data','reset_email_data_callback');
add_action('wp_ajax_reset_email_data','reset_email_data_callback');
function reset_email_data_callback(){
	global $wpdb;
	$settings = get_option( "templatic_settings" );
	$default_subject="";
	$default_msg="";
	
	/**
	* 
	* set default values for email subject
	* 
	**/
	if( @$_REQUEST['subject'] !="" ){
		if( @$_REQUEST['subject']=="mail_friend_sub" ){
			$default_subject= __("Check out this post",DOMAIN);
			$settings['mail_friend_sub'] = $default_subject;
		}
		if( @$_REQUEST['subject']=="send_inquirey_email_sub" ){
			$default_subject=__("Inquiry email",DOMAIN);
			$settings['send_inquirey_email_sub'] = $default_subject;
		}
		if( @$_REQUEST['subject']=="registration_success_email_subject" ){
			$default_subject= __("Thank you for registering!",DOMAIN);
			$settings['registration_success_email_subject'] = $default_subject;
		}
		if( @$_REQUEST['subject']=="post_submited_success_email_subject" ){
			$default_subject= __("A new post has been submitted on your site",DOMAIN);
			$settings['post_submited_success_email_subject'] = $default_subject;
		}
		if( @$_REQUEST['subject']=="payment_success_email_subject_to_client" ){
			$default_subject=__("Thank you for your payment!",DOMAIN);
			$settings['payment_success_email_subject_to_client'] = $default_subject;
		}
		if( @$_REQUEST['subject']=="payment_success_email_subject_to_admin" ){
			$default_subject=__("You have received a payment",DOMAIN);
			$settings['payment_success_email_subject_to_admin'] = $default_subject;
		}
		if( @$_REQUEST['subject']=="pre_payment_success_email_subject_to_admin" ){
			$default_subject=__("Pending payment through Pre bank transfer",DOMAIN);
			$settings['pre_payment_success_email_subject_to_admin'] = $default_subject;
		}
	}
	/**
	* 
	* set default values for email message
	* 
	**/
	if( @$_REQUEST['message'] !="" ){
		if( @$_REQUEST['message']=="mail_friend_description" ){
			$default_msg="<p>Hey [#to_name#],</p><p>[#frnd_comments#]</p><p>Link: [#post_title#]</p><p>Cheers<br/>[#your_name#]</p>";
			$settings['mail_friend_description'] = $default_msg;
		}
		if( @$_REQUEST['message']=="send_inquirey_email_description" ){
			$default_msg="<p>Hello [#to_name#],</p><p>This is an inquiry regarding the following post: <b>[#post_title#]</b></p><p><b>Subject: [#frnd_subject#]</b></p><p>Link : <b>[#post_title#]</b> </p><p>Contact number : [#contact#]</p><p>[#frnd_comments#]</p><p>Thank you,<br />[#your_name#]</p>";
			$settings['send_inquirey_email_description'] = $default_msg;
		}
		if( @$_REQUEST['message']=="registration_success_email_content" ){
			$default_msg="<p>Dear [#user_name#],</p><p>Thank you for registering and welcome to [#site_name#]. You can proceed with logging in to your account.</p><p>Login here: [#site_login_url_link#]</p><p>Username: [#user_login#]</p><p>Password: [#user_password#]</p><p>Feel free to change the password after you login for the first time.</p><p>&nbsp;</p><p>Thanks again for signing up at [#site_name#]</p>";
			$settings['registration_success_email_content'] = $default_msg;
		}
		if( @$_REQUEST['message']=="post_submited_success_email_content" ){
			$default_msg = __('<p>Howdy [#to_name#],</p><p>A new post has been submitted on your site. Here are some details about it</p><p>[#information_details#]</p><p>Thank You,<br/>[#site_name#]</p>',DOMAIN);
			$settings['post_submited_success_email_content'] = $default_msg;
		}
		if( @$_REQUEST['message']=="payment_success_email_content_to_client" ){
			$default_msg = __("<p>Hello [#to_name#]</p><p>Here's some info about your payment...</p><p>[#transaction_details#]</p><p>If you'll have any questions about this payment please send an email to [#admin_email#]</p><p>Thanks!,<br/>[#site_name#]</p>",DOMAIN);
			$settings['payment_success_email_content_to_client'] = $default_msg;
		}
		if( @$_REQUEST['message']=="payment_success_email_content_to_admin" ){
			$default_msg = __("<p>Howdy [#to_name#] ,</p><p>You have received a payment of [#payable_amt#] on [#site_name#]. Details are available below</p><p>[#transaction_details#]</p><p>Thanks,<br/>[#site_name#]</p>",DOMAIN);
			$settings['payment_success_email_content_to_admin'] = $default_msg;
		}
		if( @$_REQUEST['message']=="post_added_success_msg_content" ){
			$default_msg= '<p>'.__("Thank you! We have successfully received the submitted information.",DOMAIN).'</p><p><a href="[#submited_information_link#]">'.__("Click here",DOMAIN).'</a> '.__("to see the content you have just submitted.",DOMAIN).'</p><p>'.__("Thanks!",DOMAIN).'<br/> [#site_name#].</p>';
			$settings['post_added_success_msg_content'] = $default_msg;
		}
		if( @$_REQUEST['message']=="post_payment_success_msg_content" ){
			$default_msg = '<h4>'.__("Your payment has been successfully received. The submitted content is now published.",DOMAIN).'</h4><p><a href="[#submited_information_link#]" >'.__("View your submitted information",DOMAIN).'</a></p><h5>'.__("Thank you for participating at",DOMAIN).' [#site_name#].</h5>';
			$settings['post_payment_success_msg_content'] = $default_msg;
		}
		if( @$_REQUEST['message']=="post_payment_cancel_msg_content" ){
			$default_msg="<h3>Sorry! Your listing has been canceled due to some reason. To get the details on it, contact us at [#admin_email#].</h3><h5>Thank you for your kind co-operation with [#site_name#]</h5>";
			$settings['post_payment_cancel_msg_content'] = $default_msg;
		}
		if( @$_REQUEST['message']=="post_pre_bank_trasfer_msg_content" ){
			$default_msg = '<p>'.__("Thank you! We have successfully received your PreBank payment request.",DOMAIN).'</p><p>'.__("To complete the transaction please transfer ",DOMAIN).' <b>[#payable_amt#] </b> '.__("to our bank account. Our bank details are below.",DOMAIN).'</p><p>'.__("Bank Name:",DOMAIN).' <b>[#bank_name#]</b></p><p>'.__("Account Number:",DOMAIN).' <b>[#account_number#]</b></p><p>'.__("Please include the following number as reference:",DOMAIN).'#[#submition_Id#]</p><p>[#submited_information_link#] </p><p>'.__("Thank you!",DOMAIN).'<br/>[#site_name#].</p>';
			$settings['post_pre_bank_trasfer_msg_content'] = $default_msg;
		}
		if( @$_REQUEST['message']=="pre_payment_success_email_content_to_admin" ){
			$default_msg = __("<p>Howdy [#to_name#] ,</p><p>Payment from [#user_login#] is pending for the new listing they submitted on your site as they selected pre bank transfer as their preferred payment method.</p><p><p>You can view details below [#transaction_details#]</p> <p>You can contact [#user_login#] for status of the payment.</p><p>Thanks!<br/>[#site_name#]</p>",DOMAIN);
			$settings['pre_payment_success_email_content_to_admin'] = $default_msg;
		}
	}
	/**
	* 
	* Save default setting to database
	* 
	*/
	update_option('templatic_settings', $settings);	
	$updated_settings = get_option( "templatic_settings" );
	$json_value ="";
	if( @$_REQUEST['subject']!="" ){
		$json_value .='"'.$_REQUEST['subject'].'":"'.$updated_settings[$_REQUEST['subject']].'",';
	}
	if( @$_REQUEST['message']!="" ){
		$json_value .='"'.$_REQUEST['message'].'":"'.addslashes($updated_settings[$_REQUEST['message']]).'"';
	}
	echo '[{'.$json_value.'}]';
	exit;
}
/*
	Name: tevolution_display_rating
	Desc: Display ratings on detail page
*/
add_action('tevolution_display_rating','tevolution_display_rating');
function tevolution_display_rating($post_id){
	if(is_plugin_active('Templatic-MultiRating/multiple_rating.php') && function_exists('get_single_page_average_rating_image'))
	{ ?>
        <div class="tevolution_rating">
			<div class="tevolution_rating_row"><span class="single_rating"> <?php echo get_single_page_average_rating_image($post_id);?> </span></div>
		</div>
    <?php }
	else
	{
		$tmpdata = get_option('templatic_settings');
		if($tmpdata['templatin_rating']=='yes'):
			$total=get_post_total_rating(get_the_ID());
			$total=($total=='')? 0: $total;
			$review_text=($total==1 || $total==0)? '<a href="#comments">'.__('Review',DOMAIN).'</a>': '<a href="#comments">'.__('Reviews',DOMAIN).'</a>';
		?>
				<div class="tevolution_rating">
				<div class="tevolution_rating_row"><span class="single_rating"> <?php echo draw_rating_star_plugin(get_post_average_rating(get_the_ID()));?> <span><?php echo $total.' '.$review_text?></span></span></div>
				  </div>
		<?php endif;
	}
}
?>