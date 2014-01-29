<?php
/*
name :is_on_ssl_url
description : check whether url is ssl enable or not.*/
function is_on_ssl_url()
{
	$tmpdata = get_option('templatic_settings');
	if(isset($tmpdata['templatic-is_allow_ssl']) && $tmpdata['templatic-is_allow_ssl'] == 'Yes')
	{
		return true;
	}
	else
	{
		return false;
	}
}
/*
name :tmpl_get_ssl_normal_url
description : replace http with https if ssl is enable.*/
function tmpl_get_ssl_normal_url($url)
{
	if(is_on_ssl_url())
	{
		$url = str_replace('http://','https://',$url);
	}
	return $url;
}
/*
 * Function Name: tevolution_general_function
 * Return: include the generalization css in header
 */
add_action( 'wp_enqueue_scripts', 'tevolution_general_function' );
function tevolution_general_function(){	
	if(is_single()){		
		wp_enqueue_style('general-style', TEMPL_PLUGIN_URL.'tmplconnector/monetize/templatic-generalizaion/css/style.css' );
		wp_enqueue_script("generalization-leadmodal",TEMPL_PLUGIN_URL.'tmplconnector/monetize/templatic-generalizaion/js/jquery.leanModal.min.js',array('jquery'));
		wp_enqueue_script("generalization-basic",TEMPL_PLUGIN_URL.'tmplconnector/monetize/templatic-generalizaion/js/basic.js',array('jquery'));
	}
}
/*
 * Fincation Name: send_email_to_friend
 * Include popup_form.php file
 */
function send_email_to_friend()
{	?>
	<script>
		jQuery(document).ready(function(){
			jQuery('#send_friend_id').on('click', function(){
				jQuery('html,body').scrollTop(0);
				jQuery('#basic-modal-content').scrollTop(0);
			}); 
		});
	</script>
<?php	
	include_once(TEMPL_MONETIZE_FOLDER_PATH."templatic-generalizaion/popup_frms.php");
}
/*
 * Fincation Name: send_inquiry
 * include popup_inquiry_frm.php
 */
function send_inquiry()
{
	?>
	<script>
		jQuery(document).ready(function(){
			jQuery('#send_inquiry_id').on('click', function(){
				jQuery('html,body').scrollTop(0);
				jQuery('#inquiry_div').scrollTop(0);
			}); 
		});
	</script>
<?php	
	include_once(TEMPL_MONETIZE_FOLDER_PATH."templatic-generalizaion/popup_inquiry_frm.php");	
}
/* start code to add add to favourites on author dash board */
if(current_theme_supports('tevolution_my_favourites')){
	add_action('tevolution_author_tab','tmpl_dashboard_favourites_tab'); // to display tab 
}
function tmpl_dashboard_favourites_tab(){
	global $current_user,$curauth,$wp_query;
	$qvar = $wp_query->query_vars;
	$author = $qvar['author'];
	if(isset($author) && $author !='') :
		$curauth = get_userdata($qvar['author']);
	else :
		$curauth = get_userdata(intval($_REQUEST['author']));
	endif;	
	if(isset($_REQUEST['sort']) && $_REQUEST['sort'] =='favourites'){
		$class = 'nav-author-post-tab-active';
	}else{
		$class ='';
	}
	
	if($current_user->ID == $curauth->ID){
		echo "<li><a class='author_post_tab ".$class."' href=".esc_url(get_author_posts_url($current_user->ID).'?sort=favourites&custom_post=all').">".esc_html(__('My Favourites',DOMAIN))."</a></li>";
	}
	
}
if(isset($_REQUEST['sort']) && $_REQUEST['sort'] =='favourites'){
	global $current_user,$curauth,$wp_query;
	add_filter('posts_join','tevolution_favourites_post_join'); // to apply filter where - filter listing
	add_filter('posts_where','tevolution_favourites_post',12); // to apply filter where - filter listing
}
/*
Name : tevolution_favourites_post_join
Desc : start function to list - favourites post on dashboard */
function tevolution_favourites_post_join($join){

	global $wpdb, $pagenow, $wp_taxonomies,$ljoin;
	$language_where='';	
	if(is_plugin_active('wpml-translation-management/plugin.php')){
	
		$post_types=get_option('templatic_custom_post');
		$posttype='';		
		foreach($post_types as $key=>$value){
			$posttype.="'post_".$key."',";
		}
		$posttype=substr($posttype,0,-1);
		$language = ICL_LANGUAGE_CODE;
		$join .= " {$ljoin} JOIN {$wpdb->prefix}icl_translations t1 ON {$wpdb->posts}.ID = t1.element_id			
			AND t1.element_type IN (".$posttype.") JOIN {$wpdb->prefix}icl_languages l1 ON t1.language_code=l1.code AND l1.active=1 AND t1.language_code='".$language."'";
	}
	return $join;
}

/*
Name : tevolution_favourites_post
Desc : start function to list - favourites post on dashboard */
function tevolution_favourites_post(){
	global $wpdb,$current_user,$curauth,$wp_query;
	
	$where = '';
	$query_var = $wp_query->query_vars;
	$user_id = $query_var['author'];
	$post_ids = get_user_meta($current_user->ID,'user_favourite_post',true);
	$final_ids = '';
	if(!empty($post_ids))
	{
		$post_ids = implode(",",$post_ids);
	}
	else
	{
	 	$post_ids = "''";
	}
	$qvar = $wp_query->query_vars;
	$authname = $qvar['author_name'];
	$curauth = get_userdata($qvar['author']);
	$nicename = $current_user->user_nicename;
	
	if($_REQUEST['sort']=='favourites')	{
		$where .= " AND ($wpdb->posts.ID in ($post_ids))";			
	}else
	{	
		if(is_plugin_active('wpml-string-translation/plugin.php')){
			$language = ICL_LANGUAGE_CODE;
			$where = " AND ($wpdb->posts.post_author = $user_id)  AND t.language_code='".$language."'";
		}else{
			$where = " AND ($wpdb->posts.post_author = $user_id) ";
		}
	}	
	return $where;
}
/* Name :tevolution_server_date_time
Description : to fetch the server date and time  */
add_action('tevolution_details','tevolution_server_date_time');
function tevolution_server_date_time() {
	
	
	$tev_time_now = date("D dS M, Y h:i a");
	$timezone_now = date("e, (T P)");
	echo "<p id='server-date-time'><strong>".__('Server Date/Time',DOMAIN).":</strong> $tev_time_now <br/><strong>".__('Time Zone',DOMAIN).": </strong> $timezone_now</p>";
	
}
/* get tevolution version details */
function tevolution_version() {
	
	$plugin_file = get_tmpl_plugin_directory()."Tevolution/templatic.php";
	$plugin_details = get_plugin_data( $plugin_file, $markup = true, $translate = true ); 
	$version = @$plugin_details['Version'];
	echo " <span class='tevolution_version'>".@$version."<span>";
}
//Set Default permalink on theme activation: start
if(isset($_POST['submit-taxonomy']) && $_POST['submit-taxonomy'] !=''){
	add_action('admin_init','tevolution_default_permalink_set');
}
/*
Name : tevolution_default_permalink_set
Description : set permalink on set of new taxonomy
*/
function tevolution_default_permalink_set(){
	global $pagenow;
	if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && $_GET['page'] =='custom_taxonomy' ){ // Test if theme is activate
		//Set default permalink to postname start
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$wp_rewrite->flush_rules();
		if(function_exists('flush_rewrite_rules')){
			flush_rewrite_rules(true);  
		}
		//Set default permalink to postname end
	}
}
//Set Default permalink on theme activation: end
if(isset($_POST['Verify']) && $_POST['Verify'] !=''){
	global $wp_version;
	$arg=array('method' => 'POST',
			 'timeout' => 45,
			 'redirection' => 5,
			 'httpversion' => '1.0',
			 'blocking' => true,
			 'headers' => array(),
			 'body' => array( 'licencekey' => $_POST['licencekey'],'action'=>'licensekey_verification'),
			 'user-agent' => 'WordPress/'. $wp_version .'; '. home_url(),
			 'cookies' => array()
		);
	$warnning_message='';
	$response = wp_remote_get('http://templatic.net/api/verification/index.php',$arg );
	if(!is_wp_error( $response ) ) {
		update_option('templatic_licence_key',$response['body']);
		if(isset($_POST['licencekey']) && $_POST['licencekey'] !=''){ 
			if(strstr($response['body'],'error_message')){
				update_option('templatic_licence_key_','');
							
				add_action('tevolution_error_message','tevolution_error_message_error');
			}else{ 
				update_option('templatic_licence_key_',$_POST['licencekey']);
				add_action('tevolution_error_message','tevolution_error_message_success');
			}
		}else{
			update_option('templatic_licence_key_','');
			add_action('tevolution_error_message','tevolution_error_message_error');
		}
	}else{
		
		update_option('templatic_licence_key','{"error_message":"WP HTTP Error: couldn\'t connect to host."}');
		add_action('tevolution_error_message','tevolution_error_message_host');
	}
	
}else{
	if(!get_option('templatic_licence_key_')){
		add_action('tevolution_error_message','tevolution_error_message_error');
	}
}
//379372543521c694b4d19c
$templatic_licence_key = get_option('templatic_licence_key');
if(strstr($templatic_licence_key,'is_supreme') && get_option('templatic_licence_key_') !='' && !$_POST){
	add_action('tevolution_error_message','tevolution_key_is_verified');
}
function tevolution_error_message_host(){
	echo "<p>WP HTTP Error: couldn't connect to host.</p>";
}
function tevolution_key_is_verified(){
	echo "<p style='color:Green;'>&nbsp;Licence Key Verified.</p>";
}
/* tevolution licence key error message */
function tevolution_error_message_error($message){
	if(isset($_POST['Verify']) && $_POST['Verify'] !=''){
		echo "<p style='color:red;'>"; 
			$error_message=json_decode(get_option('templatic_licence_key'));				
			if($error_message){
				echo base64_decode($error_message->error_message);
			}
		echo "</p>";
	}
	echo "<p>The key can be obtained from Templatic <a href='http://templatic.com/members/member'>member  area</a></p>";
}
/* end */
/* tevolution licence key success message */
function tevolution_error_message_success(){
	echo "<p style='color:green;'>";
	$success_message=json_decode(get_option('templatic_licence_key'));				
	echo base64_decode($success_message->success_message);
	echo "</p>";
}
/* end */
add_action('wp_head','tevolution_licence_message');
function tevolution_licence_message(){
	if(!is_admin() && !strstr($_SERVER['REQUEST_URI'],'wp-admin/')){
		$templatic_licence_key = get_option('templatic_licence_key');
		if(strstr($templatic_licence_key,'error_message') || !get_option('templatic_licence_key_')){
			if(!get_option('templatic_licence_key_'))
			{
				echo "<h2>".__('Your copy of Tevolution hasn&#32;t been verified yet. To verify the plugin and unlock the site please <a href="'.admin_url( 'admin.php?page=templatic_system_menu').'" style="color:red;">click here</a> to verify your licence key',DOMAIN)."</h2>";
			}else{
				echo "<h2>".__('You are not allowed to run this site, because of invalid licence key. <a href="'.admin_url( 'admin.php?page=templatic_system_menu').'">click here</a> to verify your valid licence key',DOMAIN)."</h2>";
			}
			die;
		}
	}
}
/*
 * Function Name: tevolution_send_inquiry_form
 * Return: send inquiry email function 
 */
add_action('wp_ajax_tevolution_send_inquiry_form','tevolution_send_inquiry_form');
add_action('wp_ajax_nopriv_tevolution_send_inquiry_form','tevolution_send_inquiry_form');
function tevolution_send_inquiry_form(){
	global $wpdb;
	$post = array();
	if( @$_REQUEST['postid'] ){
		$post = get_post($_REQUEST['postid']);
	}
	if(isset($_REQUEST['your_iemail']) && $_REQUEST['your_iemail'] != "")
	{	
		/* CODE TO CHECK WP-RECAPTCHA */
		$tmpdata = get_option('templatic_settings');
		$display = $tmpdata['user_verification_page'];
		if( $tmpdata['recaptcha'] == 'recaptcha')
		{
			if(file_exists(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php') && is_plugin_active('wp-recaptcha/wp-recaptcha.php') && in_array('sendinquiry',$display))
			{
				require_once( get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php');
				$a = get_option("recaptcha_options");
				$privatekey = $a['private_key'];
				$resp = recaptcha_check_answer ($privatekey,getenv("REMOTE_ADDR"),$_REQUEST["recaptcha_challenge_field"],$_REQUEST["recaptcha_response_field"]);						
									
				if ($resp->is_valid =="")
				{
					echo '1';
					exit;
				}
			}
		}
		/* END OF CODE - CHECK WP-RECAPTCHA */	
		$yourname = $_REQUEST['full_name'];
		$youremail = $_REQUEST['your_iemail'];
		$contact_num = $_REQUEST['contact_number'];
		$frnd_subject = $_REQUEST['inq_subject'];
		$frnd_comments = $_REQUEST['inq_msg'];
		$post_id = $_REQUEST['listing_id'];	
		$to_email = (get_post_meta($post->ID,'email',true)!="")? get_post_meta($post->ID,'email',true): get_the_author_meta( 'user_email', $post->post_author )  ;
		$userdata = get_userdata($post->post_author);
		$to_name = $userdata->data->display_name;
		if($post_id != "")
		{
			$productinfosql = "select ID,post_title from $wpdb->posts where ID ='".$post_id."'";
			$productinfo = $wpdb->get_results($productinfosql);
			foreach($productinfo as $productinfoObj)
			{
				$post_title = stripslashes($productinfoObj->post_title); 
			}
		}
		///////Inquiry EMAIL START//////
		global $General;
		global $upload_folder_path;
		$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
		$tmpdata = get_option('templatic_settings');	;
		$email_subject =$tmpdata['send_inquirey_email_sub'];
		$email_content =$tmpdata['send_inquirey_email_description'];	
		
		
		if($email_content == "" && $email_subject=="")
		{
			$message1 =  __('[SUBJECT-STR]You might be interested in [SUBJECT-END]
			<p>Dear [#to_name#],</p>
			<p>[#frnd_comments#]</p>
			<p>Link : <b>[#post_title#]</b> </p>
			<p>Contact number : [#contact#]</p>
			<p>From, [#your_name#]</p>
			<p>Sent from -[#$post_url_link#]</p></p>',DOMAIN);
			$filecontent_arr1 = explode('[SUBJECT-STR]',$message1);
			$filecontent_arr2 = explode('[SUBJECT-END]',$filecontent_arr1[1]);
			$subject = $filecontent_arr2[0];
			if($subject == '')
			{
				$subject = $frnd_subject;
			}
			$client_message = $filecontent_arr2[1];
		} else {
			$client_message = $email_content;
		}
		$subject = $frnd_subject;
	
		$post_url_link = '<a href="'.$_REQUEST['link_url'].'">'.$post_title.'</a>';
		/////////////customer email//////////////
		$yourname_link = __('<b><a href="'.get_option('siteurl').'">'.get_option('blogname').'</a></b>.',DOMAIN);
		$search_array = array('[#to_name#]','[#frnd_subject#]','[#post_title#]','[#frnd_comments#]','[#your_name#]','[#$post_url_link#]','[#contact#]');
		$replace_array = array($to_name,$frnd_subject,$post_url_link,$frnd_comments,$yourname,$yourname_link,$contact_num);
		$client_message = str_replace($search_array,$replace_array,$client_message,$contact_num); 
		templ_send_email($youremail,$yourname,$to_email,$to_name,$subject,$client_message,$extra='');///To clidne email
		//////Inquiry EMAIL END////////		
		$post = "";
		if(get_option('siteurl').'/' == $_REQUEST['request_uri']){
				echo __('Email sent successfully',DOMAIN);
				exit;
		} else {
				echo __('Email sent successfully',DOMAIN);
				exit;
		}
		
	}
}
/*
 * Function Name: tevolution_send_friendto_form
 * Return: send friend to email function 
 */
add_action('wp_ajax_tevolution_send_friendto_form','tevolution_send_friendto_form');
add_action('wp_ajax_nopriv_tevolution_send_friendto_form','tevolution_send_friendto_form');
function tevolution_send_friendto_form(){
	
	global $wpdb,$General,$upload_folder_path,$post;
	$postdata = array();
	if( @$_REQUEST['post_id']!="" ){
		$postdata = get_post($_REQUEST['post_id']);
	}
	if( @$_REQUEST['yourname'] )
	{
		/* CODE TO CHECK WP-RECAPTCHA */
		$tmpdata = get_option('templatic_settings');
		$display = $tmpdata['user_verification_page'];
		if( $tmpdata['recaptcha'] == 'recaptcha')
		{
			if(file_exists(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php') && is_plugin_active('wp-recaptcha/wp-recaptcha.php') && in_array('emaitofrd',$display))
			{
				require_once( get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php');
				$a = get_option("recaptcha_options");
				$privatekey = $a['private_key'];
				$resp = recaptcha_check_answer ($privatekey,getenv("REMOTE_ADDR"),$_REQUEST["recaptcha_challenge_field"],$_REQUEST["recaptcha_response_field"]);						
					
				if ($resp->is_valid=="")
				{
					echo '1';
					exit;					
				}				
			}
		}
		else
		{
			if(file_exists(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php') && is_plugin_active('are-you-a-human/areyouahuman.php')  && in_array('emaitofrd',$display) && $tmpdata['recaptcha'] == 'playthru')
			{
				require_once(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php');
				require_once(get_tmpl_plugin_directory().'are-you-a-human/includes/ayah.php');
				$ayah = new AYAH();
		
				/* The form submits to itself, so see if the user has submitted the form.
				Use the AYAH object to get the score. */
				$score = $ayah->scoreResult();		
				if(!$score && $score=="")			
				{
					echo '2';
					exit;
				}
			}
		}
		
		
		/* END OF CODE - CHECK WP-RECAPTCHA */	
		$yourname = $_REQUEST['yourname'];
		$youremail = $_REQUEST['youremail'];
		$frnd_subject = $_REQUEST['frnd_subject'];
		$frnd_comments = $_REQUEST['frnd_comments'];
		$to_friend_email = $_REQUEST['to_friend_email'];
		$to_name = $_REQUEST['to_name_friend'];
		///////Inquiry EMAIL START//////
		global $General,$wpdb;
		global $upload_folder_path;
		$post_title = stripslashes($postdata->post_title);
		$tmpdata = get_option('templatic_settings');	;
		$email_subject =$tmpdata['mail_friend_sub'];
		$email_content =$tmpdata['mail_friend_description'];
		
		
		if($email_content == "" && $email_subject=="")
		{
			$message1 =  __('[SUBJECT-STR]You might be interested in [SUBJECT-END]
			<p>Dear [#to_name#],</p>
			<p>[#frnd_comments#]</p>
			<p>Link : <b>[#post_title#]</b> </p>
			<p>From, [#your_name#]</p>',DOMAIN);
			$filecontent_arr1 = explode('[SUBJECT-STR]',$message1);
			$filecontent_arr2 = explode('[SUBJECT-END]',$filecontent_arr1[1]);
			$subject = $filecontent_arr2[0];
			if($subject == '')
			{
				$subject = $frnd_subject;
			}
			$client_message = $filecontent_arr2[1];
		}else
		{
			$client_message = $email_content;
		}
		$subject = $frnd_subject;
		$post_url_link = '<a href="'.$_REQUEST['link_url'].'">'.$post_title.'</a>';
		/////////////customer email//////////////
		//$post_url_link ='<a href="'.get_option('siteurl').'">'.get_option('blogname').'</a>';
		$search_array = array('[#to_name#]','[#post_title#]','[#frnd_comments#]','[#your_name#]','[#post_url_link#]');
		$replace_array = array($to_name,$post_url_link,nl2br($frnd_comments),$yourname,$post_url_link);
		$client_message = str_replace($search_array,$replace_array,$client_message);	
		templ_send_email($youremail,$yourname,$to_friend_email,$to_name,$subject,$client_message,$extra='');///To clidne email
		
		//////Inquiry EMAIL END////////			
		echo __('Email sent successfully',DOMAIN);
		exit;
	}
		
}
add_action('admin_init','tevolution_licensekey_popupbox');
function tevolution_licensekey_popupbox(){
	global $pagenow;	
	if($pagenow=='themes.php' || ($pagenow=='admin.php' && isset($_REQUEST['page']) && $_REQUEST['page']=='templatic_system_menu')){
		$templatic_licence_key=get_option('templatic_licence_key_');
		if(($pagenow=='themes.php' &&  $templatic_licence_key=='') || $templatic_licence_key==''){
			?>
			<div id="boxes" class="licensekey_boxes">
				<div style="top:0px; left: 551.5px; display: none;" id="dialog" class="window">
                    	<span class="close"><a href="#" class="close"><img src="<?php echo TEMPL_PLUGIN_URL.'images/delete_10.png'?>" alt="<?php _e('Close it',DOMAIN);?>"  /></a></span>
					<h2><?php _e('Licence key',DOMAIN); ?></h2>
                         <form action="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu";?>" name="" method="post">
                         <div class="inside">
                         <p><?php _e('Enter the license key in order to unlock the plugin and enable automatic updates.',DOMAIN); ?></p>
						 <div id="licence_fields">
                                   <input type="password" name="licencekey" id="licencekey" value="<?php echo get_option('templatic_licence_key_'); ?>" size="30" max-length="36" PLACEHOLDER="templatic.com purchase code"/>
                                   <input type="submit" accesskey="p" value="<?php _e('Verify',DOMAIN);?>" class="button button-primary button-large" id="Verify" name="Verify">
                                   <?php do_action('tevolution_error_message'); ?>
						</div>
                         </div>
                         </form>
				</div>
				<!-- Mask to cover the whole screen -->
				<div style="width: 1478px; height: 602px; display: none; opacity: 0.8;" id="mask"></div>
			</div>
			<?php
		}
	}
}

/*
Name:get_tmpl_plugin_directory
desc: return the plugin directory path
*/
if(!function_exists('get_tmpl_plugin_directory')){
function get_tmpl_plugin_directory() {
	 return WP_CONTENT_DIR."/plugins/";
}
}

/*
Name:tevolution_dir_popupfrms
desc: return the pop up forms in detail page
*/
if(!function_exists('tevolution_dir_popupfrms')){
	function tevolution_dir_popupfrms($post){
	$tmpdata = get_option('templatic_settings');	
	$link='';	
	if(isset($tmpdata['send_to_frnd'])&& $tmpdata['send_to_frnd']=='send_to_frnd' && function_exists('send_email_to_friend'))
	    {
		
		$claim_content_link='<a class="button small_btn tmpl_mail_friend" rel="leanModal_email_friend" href="#basic-modal-content" id="send_friend_id"  title="Mail to a friend" >'. __('Send to friend',DOMAIN).'</a>';				
	       echo '<li>';
		  add_action('wp_footer','send_email_to_friend');
	       echo $claim_content_link.'</li>';
	    }
	    if(isset($tmpdata['claim_post_type_value'])&& @in_array('listing',$tmpdata['claim_post_type_value']) && function_exists('claim_ownership'))
	    {	
		echo '<li class="claim_ownership">';
		claim_ownership();
		echo '</li>';
	    }
	    if(isset($tmpdata['send_inquiry'])&& $tmpdata['send_inquiry']=='send_inquiry' && function_exists('send_inquiry'))
	   {			
		$send_inquiry='<a class="button small_btn tmpl_mail_friend" rel="leanModal_send_inquiry"  href="#inquiry_div" title="Send Inquiry" id="send_inquiry_id" >'.__('Send inquiry',DOMAIN).'</a>';
			 add_action('wp_footer','send_inquiry');		
		$link.= '<li class="send_inquiry">'.$send_inquiry.'</li>';
	   }  
		if(current_theme_supports('tevolution_my_favourites') && ($post->post_status == 'publish' )){
			global $current_user;
			$user_id = $current_user->ID;
			$link.= directory_favourite_html($user_id,@$post);
		}
	    echo $link;
	    ?>
	  
	<?php }
}
/*
Name:tevolution_socialpost_link
desc: return the social media links of current post
*/
if(!function_exists('tevolution_socialpost_link')){
	function tevolution_socialpost_link($post){
		$facebook=get_post_meta($post->ID,'facebook',true);
		$google_plus=get_post_meta($post->ID,'google_plus',true);
		$twitter=get_post_meta($post->ID,'twitter',true);
	echo '<div class="share_link">';
	if($facebook!=""):?>
	 <a href="<?php echo $facebook;?>"><img src="<?php echo TEVOLUTION_DIRECTORY_URL; ?>images/i_facebook21.png" alt="Facebook"/></a>
	 <?php endif;?>

	 <?php if($twitter!=""):?>
	 <a href="<?php echo $twitter;?>"><img src="<?php echo TEVOLUTION_DIRECTORY_URL; ?>images/i_twitter2.png" alt="Twitter"/></a>
	 <?php endif;?>

	 <?php if($google_plus!=""):?>
	 <a href="<?php echo $google_plus;?>"><img src="<?php echo TEVOLUTION_DIRECTORY_URL; ?>images/i_googleplus.png" alt="Google Plus"/></a>
	 <?php endif;
		
	echo '</div>';
	}
}
/*
Name:tevolution_socialmedia_sharelink
Desc: Social media share link
*/
if(!function_exists('tevolution_socialmedia_sharelink')){
function tevolution_socialmedia_sharelink($post){
	$tmpdata = get_option('templatic_settings');	
	$title=urlencode($post->post_title);
	$post_img = bdw_get_images_plugin($post->ID,'thumb');
	$post_images = @$post_img[0]['file'];
	$url=urlencode(get_permalink($post->ID));
	$summary=urlencode(htmlspecialchars($post->post_content));
	$image=$post_images;
	if($tmpdata['google_share_detail_page'] == 'yes' || $tmpdata['twitter_share_detail_page'] == 'yes' || $tmpdata['pintrest_detail_page']=='yes')
	{?>
	<div class="single-social-media">
	
		<div class="addthis_toolbox addthis_default_style">
			<a href="http://www.addthis.com/bookmark.php?v=250&amp;username=xa-4c873bb26489d97f" class="addthis_button_compact sharethis"><img src="<?php echo TEMPL_PLUGIN_URL; ?>images/i_share.png" alt="share"/></a>
	</div>
	
	
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4c873bb26489d97f"></script> 
	<?php if($tmpdata['facebook_share_detail_page'] == 'yes') { ?>
     <div class="addthis_toolbox">
		<a onClick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $title;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&amp;p[images][0]=<?php echo $image;?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)" id="facebook_share_button"><?php _e('Facebook Share.',DOMAIN); ?></a>
	</div>
	<?php }
		if($tmpdata['google_share_detail_page'] == 'yes'): ?>
	<div class="addthis_toolbox">
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
		<div class="g-plus" data-action="share" data-annotation="bubble"></div>
	</div>
	<?php endif; ?>
	
	<?php if($tmpdata['twitter_share_detail_page'] == 'yes'): ?>
	<div class="addthis_toolbox">
			<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-text='<?php echo htmlentities($post->post_content);?>' data-url="<?php echo get_permalink($post->ID); ?>" data-counturl="<?php echo get_permalink($post->ID); ?>"><?php _e('Tweet',DOMAIN); ?></a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	</div>
	<?php endif; ?>
	
	<?php if(@$tmpdata['pintrest_detail_page']=='yes'):?>
	<!-- Pinterest -->
	<div class="addthis_toolbox pinterest"> 
		<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;media=<?php echo $image; ?>&amp;description=<?php the_title(); ?>" >Pin It</a>
		<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>                    
	</div>
	<?php endif; ?>   

	</div>
<?php } 
}
}
?>