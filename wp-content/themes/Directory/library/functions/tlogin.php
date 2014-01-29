<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>
<?php esc_html_e( 'Supreme Update' ); ?>
</title>
<?php
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_style( 'jquery-tools', plugins_url( '/css/tabs.css', __FILE__ ) );
			wp_admin_css( 'global' );
			wp_admin_css( 'admin' );
			wp_admin_css();
			wp_admin_css( 'colors' );
			do_action('admin_print_styles');
			do_action('admin_print_scripts');
			do_action('admin_head');
			?>
</head>
<?php
	global $current_user;
	$self_url = add_query_arg( array( 'slug' => THEME_DOMAIN, 'action' => THEME_DOMAIN , '_ajax_nonce' => wp_create_nonce( THEME_DOMAIN ), 'TB_iframe' => true ), admin_url( 'admin-ajax.php' ) );
	if(isset($_POST['templatic_login']) && isset($_POST['templatic_username']) && $_POST['templatic_username']!=''  && isset($_POST['templatic_password']) && $_POST['templatic_password']!='')
	{ 
		$arg=array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => array( 'username' => $_POST['templatic_username'], 'password' => $_POST['templatic_password']),
			'cookies' => array()
		    );
		$warnning_message='';
		$response = wp_remote_post('http://templatic.com/members/login_api.php',$arg );	
	
		if( is_wp_error( $response ) ) {
		  	$warnning_message="Invalid UserName or password. are you using templatic member username and password?";
		} else { 
		  	$data = unserialize($response['body']);
		}
	
		/*Return error message */
		if(isset($data->error_message) && $data->error_message!='')
		{
			$warnning_message=$data->error_message;			
		}
		/*Finish error message */
		if(isset($data->product) && is_array($data->product))
		{		
			foreach($data->product as $key=>$val)
			{
				$product[]=$key;
			}			
			if(in_array('Nightlife - developer license',$product))
			{
				$successfull_login=1;				
				$download_link=$data->product['Nightlife - developer license '];
			}else
			{
				$warnning_message="We don't find supreme in your templatic account, you will not be able to update without a license";
			}			
		}
	}else{
		if(isset($_POST['templatic_login']) && ($_POST['templatic_username'] =='' || $_POST['templatic_password']=='')){
		$warnning_message="Invalid UserName or password. Please enter templatic member's username and password."; }
	}
	?>
<body style="padding:40px;">
<div id='pblogo'> <a href="http://templatic.com" alt="WordPress Directory Theme" title="WordPress Directory Theme"><img src="<?php echo esc_url( get_template_directory_uri()."/images/templatic-wordpress-themes.jpg"); ?>" alt="WordPress Directory Theme" style="margin-right: 50px;" /> </a> </div>
<div class='wrap templatic_login'>
  <?php
		if(isset($warnning_message) && $warnning_message!='')
		{?>
  <div class='error'>
    <p><strong><?php echo sprintf(__('%s',THEME_DOMAIN), $warnning_message);?></strong></p>
  </div>
  <?php
          }
		?>
  <?php if(!isset($successfull_login) && $successfull_login!=1):?>
  <p class="info">
    <?php _e('Templatic Login , enter your templatic credentials to take the updates of supreme.',THEME_DOMAIN);?>
  </p>
  <form action="<?php echo $self_url;?>" name="" method="post">
    <table>
      <tr>
        <td><label>
            <?php _e('User Name', THEME_DOMAIN)?>
          </label></td>
        <td><input type="text" name="templatic_username"  /></td>
      </tr>
      <tr>
        <td><label>
            <?php _e('Password', THEME_DOMAIN)?>
          </label></td>
        <td><input type="password" name="templatic_password"  /></td>
      </tr>
      <tr>
        <td><input type="submit" name="templatic_login" value="Sign In" class="button-secondary"/></td>
        <td><a title="Close" id="TB_closeWindowButton" href="#" class="button-secondary">
          <?php _e('Cancel',THEME_DOMAIN); ?>
          </a></td>
      </tr>
    </table>
  </form>
  <?php else:								
				 $file=THEME_DOMAIN;
				if(is_multisite()){
		 		 $download= wp_nonce_url(admin_url('/network/update.php?action=upgrade-theme&theme=').$file, 'upgrade-theme_' . $file);
				 }else{
				  $download= wp_nonce_url(admin_url('update.php?action=upgrade-theme&theme=').$file, 'upgrade-theme_' . $file);
				 }
				  echo ' Supreme framework <a id="TB_closeWindowButton" href="'.$download.'" target="_parent" class="button-secondary">Update Now</a>';
			 endif;?>
</div>
<?php
do_action('admin_footer', '');
do_action('admin_print_footer_scripts');
?>
</body>
</html>