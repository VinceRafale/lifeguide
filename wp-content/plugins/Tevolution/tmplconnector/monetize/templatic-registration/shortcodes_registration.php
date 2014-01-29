<?php
/*
 * Registration Module short codes
 *
 */
 
 /* 
  * Function Name: tevolution_user_login
  * Return: display the tevolution user login form
  */
function tevolution_user_login($atts)
{
	extract(shortcode_atts(array('display' => true, 'redirect' => '', 'submit' => 'page'), $atts));
	ob_start();		
	if(is_user_logged_in()): // user login
		// user alrady logeed in then redirect user page
			$user_id = get_current_user_id();
			wp_redirect(get_author_posts_url( $user_id ));
			exit;
	else: // user not login
		if(isset($_SESSION['update_password']) && $_SESSION['update_password']!='')
		{
			echo "<p class=\"success_msg\"> ".PW_CHANGE_SUCCESS_MSG."</p>";
			unset($_SESSION['update_password']);
		}
	
		echo '<div class="login_form_l">';
		echo '<h3>'.__(SIGN_IN_PAGE_TITLE).'</h3>';
		$flg=0;		
		if((isset($_POST['log']) && $_POST['log']!='') && (isset($_POST['pwd']) && $_POST['pwd']!='' ))
		{
			$flg= ( !user_pass_ok( $_POST['log'], $_POST['pwd'] ) ) ? '1' :'2';			
		}
		if((isset($_POST['log']) && $_POST['log']=='') || (isset($_POST['pwd']) && $_POST['pwd']=='' )){			
			$flg=1;
		}	
		
		// If the user wants ssl but the session is not ssl, force a secure cookie.
		if ( !empty($_POST['log']) && !force_ssl_admin() ) 
		{
			$user_name = sanitize_user($_POST['log']);
			if ( $user = get_user_by('login',$user_name) )
			{		
				if ( get_user_option('use_ssl', $user->ID) ) 
				{
					$secure_cookie = true;
					force_ssl_admin(true);
				}
			}
		}
		
		
		if(isset($flg) && $flg==1)
		{
			echo '<p class="error_msg"> '.INVALID_USER_PW_MSG.' </p>';
		}
		if(isset($flg) && $flg==2){		
			// username and password correct then auto login with redirect author page
			$creds = array();
			$creds['user_login'] = $_POST['log'];
			$creds['user_password'] = $_POST['pwd'];
			$creds['remember'] = true;
			$user = wp_signon($creds, $secure_cookie);	
			$user = get_user_by('login',$_POST['log']);			
			if($user->ID!=''){
				$redirect_url=apply_filters('tevolution_login_redirect',get_author_posts_url( $user->ID ));				
				wp_redirect($redirect_url);
				exit;
			}
		}	
		
		/*Lost password action for retrive forget password */
		if(isset($_POST['action']) && $_POST['action']=='lostpassword'){
			
			$errors = tevolution_retrieve_password();			
			$error_message = $errors->errors['invalid_email'][0];
			if ( is_wp_error($errors) ) {
				echo '<p class="error_msg">'.__($error_message,DOMAIN).'</p>';
			}else
			{
				echo $message = '<div class="success_msg">'.PW_SEND_CONFIRM_MSG.'</div>';				
			}
		}
		/*End lost password action for retrive forget password*/		
		$lang=(isset($_REQUEST['lang']) && $_REQUEST['lang']!="") ?'&lang='.$_REQUEST['lang'] : '';	
		
		?>
			<div class="login_form_box">
            	<?php do_action('action_before_login_from');?>
				<form name="loginform" id="loginform" action="<?php echo get_permalink(); ?>" method="post" >
                    	<input type="hidden" name="action" value="login" />                         
				    <div class="form_row clearfix">
					 <label><?php _e(USERNAME_TEXT); ?> <span class="indicates">*</span> </label>
					 <input type="text" name="log" id="user_login" value="<?php if(isset($user_login)){ echo esc_attr($user_login);} ?>" size="20" class="textfield" />
					 <span id="user_loginInfo"></span> </div>
				    <div class="form_row clearfix">
					 <label> <?php _e('Password',DOMAIN); ?> <span class="indicates">*</span> </label>
					 <input type="password" name="pwd" id="user_pass" class="textfield" value="" size="20"  />
					 <span id="user_passInfo"></span> </div>
					  <input type="hidden" name="redirect_to" value="<?php if(isset($_SERVER['HTTP_REFERER']))echo $_SERVER['HTTP_REFERER'];  ?>" />
					<input type="hidden" name="testcookie" value="1" />
				    <div class="form_row rember clearfix">
					 <label>
						<input name="rememberme" type="checkbox" id="rememberme" value="forever" class="fl" />
						<?php _e(REMEMBER_ON_COMPUTER_TEXT,DOMAIN); ?> 
					 </label>	
					 <a href="javascript:void(0);showhide_forgetpw();" class="forgot_password" ><?php _e(FORGOT_PW_TEXT,DOMAIN);?></a>
				    </div>
				    <!-- <a  href="javascript:void(0);" onclick="chk_form_login();" class="highlight_button fl login" >Sign In</a>-->
					
					<div class="form_row ">
				    <input class="b_signin_n" type="submit" value="<?php _e(SIGN_IN_BUTTON,DOMAIN);?>"  name="submit" />
				    </div> <?php do_action('login_form'); ?> 
							
			  </form>
              <?php do_action('action_after_login_from');?>
			</div>
			<!-- Enable social media(gigya plugin) if activated-->         
			<?php if(is_plugin_active('gigya-socialize-for-wordpress/gigya.php') && get_option('users_can_register')):          
					echo '<div id="componentDiv">';
					dynamic_sidebar('below_registration'); 
					echo '</div>';
				endif; ?>
			<!--End of plugin code-->
			<?php 
			
			if ( @$_REQUEST['emsg']=='fw' && @$_REQUEST['action'] != 'register'){
				echo "<p class=\"error_msg\"> ".INVALID_USER_FPW_MSG." </p>";
				$display_style = 'style="display:block;"';
			} else if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'register'){
				$display_style = 'style="display:none;"';
			}
			else{
				$display_style = 'style="display:none;"';
			}
			
			?>
			
			
			<div id="lostpassword_form" <?php if($display_style != '') { echo $display_style; } else { echo 'style="display:none;"';} ?> >
				<h3><?php  _e(FORGOT_PW_TEXT,DOMAIN);?></h3>
				<form name="lostpasswordform" id="lostpasswordform" action="<?php echo get_permalink(); ?>" method="post" >
                    	<input type="hidden" name="action" value="lostpassword" />
					<div class="form_row clearfix">
					<label> <?php  _e(USERNAME_EMAIL_TEXT,DOMAIN); ?>: </label>
					<input type="text" name="user_login" id="user_login_email" onkeypress="forget_email_validate();"  value="<?php if(isset($user_login))echo esc_attr($user_login); ?>" size="20" class="textfield" />
                         <span id="forget_user_email_error" class="message_error2"></span>
					<?php do_action('lostpassword_form'); ?>
					</div>
					<input type="hidden" name="pwdredirect_to" value="<?php if(isset($_SERVER['HTTP_REFERER'])) echo $_SERVER['HTTP_REFERER']; ?>" />
					<input type="submit" name="get_new_password" onclick="return forget_email_validate();" value="<?php _e(GET_NEW_PW_TEXT);?>" class="b_signin_n " />
				</form>
			</div>
			<script  type="text/javascript" >
				function showhide_forgetpw()
				{
					if(document.getElementById('lostpassword_form').style.display=='none')
					{
						document.getElementById('lostpassword_form').style.display = 'block';
					}else
					{
						document.getElementById('lostpassword_form').style.display = 'none';
					}	
				}
				
				function forget_email_validate(){
					var email = document.getElementById('user_login_email');
					var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					
					if(email.value==''){
						document.getElementById('forget_user_email_error').innerHTML='<?php _e('Please Enter E-mail',DOMAIN);?>';
						email.focus;
						return false;
					}else if (!filter.test(email.value)) {						
						document.getElementById('forget_user_email_error').innerHTML='<?php _e('Please provide a valid email address',DOMAIN);?>';
						email.focus;
						return false;
					}else
					{
						document.getElementById('forget_user_email_error').innerHTML='';
						return true;
					}
				}
			</script>
		<?php
			
		echo '</div>';
	endif;
	return ob_get_clean();
}
 
/* 
* Function Name: tevolution_user_register
* Return: display the tevolution user register form
*/
function tevolution_user_register($atts)
{	
	ob_start();	
	do_action("tmpl_registration_option_js");
	if(is_user_logged_in()): // user login
		// user alrady logeed in then redirect user page
			$user_id = get_current_user_id();
			wp_redirect(get_author_posts_url( $user_id ));
			exit;
	else: // user not login
		include(TT_REGISTRATION_FOLDER_PATH.'registration_form.php');
	endif;
	return ob_get_clean();
}
 
 
/*
 * Function Name: tevolution_retrieve_password
 * Return: send the user password
 */
function tevolution_retrieve_password()
{
	global $wpdb;
	$errors = new WP_Error();
	if ( empty( $_POST['user_login'] ) && empty( $_POST['user_email'] ) )
		$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.',DOMAIN));
	if ( strpos($_POST['user_login'], '@') ) {
		$user_data = get_user_by_email(trim($_POST['user_login']));
		if ( empty($user_data) )
			$errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.',DOMAIN));
	} else {
		$login = trim($_POST['user_login']);
		$user_data = get_user_by('login',$login);
	}
	if ( $errors->get_error_code() )
		return $errors;
	if ( !$user_data ) {
		$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.',DOMAIN));
		return $errors;
	}
	// redefining user_login ensures we return the right case in the email
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	do_action('retreive_password', $user_login);  // Misspelled and deprecated
	do_action('retrieve_password', $user_login);
	$user_email = $_POST['user_email'];
	$user_login = $_POST['user_login'];
	
	$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_login like \"$user_login\" or user_email like \"$user_login\"");
	if ( empty( $user ) )
		return new WP_Error('invalid_key', __('Invalid key',DOMAIN));
		
	$new_pass = wp_generate_password(7,false);
	
	wp_set_password($new_pass, $user->ID);
	update_user_meta($user->ID, 'default_password_nag', true); //Set up the Password change nag.
	$user_name = $user_data->user_nicename;
	$fromEmail = get_site_emailId_plugin();
	$fromEmailName = get_site_emailName_plugin();
	$title = sprintf('[%s]'.__(' Your new password',DOMAIN), get_option('blogname'));
	$title = apply_filters('password_reset_title', $title);
	$message  = '<p>'.sprintf(__('Hi %s',DOMAIN),$user_name).'<br></p>';
	$message .= '<p>'.sprintf(__('You have requested for a new password for your account %s. Here is the new password,',DOMAIN),$user_data->user_email).'<br>';
	$message .= sprintf(__('Login URL: %s',DOMAIN),"<a href='".get_tevolution_login_permalink()."'>".__('Login',DOMAIN)."</a>").'<br>';
	$message .= sprintf(__('%s %s'),USERNAME_TEXT.": ", $user->user_login)."</br>";
	$message .= sprintf(__('%s %s'), PASSWORD_TEXT.": " ,$new_pass)."<br>";
	$message .= __('You may change this password in your profile once you login with the new password.',DOMAIN)."<br></p>";
	$message .= '<p>'.__('Thanks,',DOMAIN)."<br/>".get_option('blogname')."</p>";	
	$message = apply_filters('password_reset_message', $message, $new_pass);
	templ_send_email($fromEmail,$fromEmailName,$user_email,$user_name,$title,$message,$extra='');
	return true;
}
/*
 * Function Name: tevolution_user_profile
 * Return: display the user profile update and view field
 */
function tevolution_user_profile($atts)
{
	ob_start();
	if(!is_user_logged_in()): // user not login
		// user not logeed in then redirect login page	
		$login_url=get_tevolution_login_permalink();	
		wp_redirect($login_url);
		exit;
	else: // user  login
	
		include(TT_REGISTRATION_FOLDER_PATH.'user_profile.php');
	
	endif;
	
	return ob_get_clean();
}
/**
 * Registration module Shortcode creation
 **/
add_shortcode('tevolution_login', 'tevolution_user_login');
add_shortcode('tevolution_register', 'tevolution_user_register');
add_shortcode('tevolution_profile', 'tevolution_user_profile');
?>