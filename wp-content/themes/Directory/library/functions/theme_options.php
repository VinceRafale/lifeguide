<?php
if(isset($_POST['theme_options_nonce']) && $_POST['theme_options_nonce'] !=''){
	if ( wp_verify_nonce( @$_POST['theme_options_nonce'], basename(__FILE__) ) ){
		if(function_exists('supreme_prefix')){
			$pref = supreme_prefix();
		}else{
			$pref = sanitize_key( apply_filters( 'hybrid_prefix', get_template() ) );
		}
		$theme_options = get_option($pref.'_theme_settings');
		foreach($_POST as $key => $value){
			if( $key!="theme_options_nonce" && $key !="Submit" && $key != 'hide_ajax_notification' ){
				$theme_options[$key] = $value;
			}
		}
		$theme_options['supreme_global_layout'] = ($_POST['supreme_global_layout']) ? $_POST['supreme_global_layout'] : '';
		$theme_options['customcss'] = ($_POST['customcss']) ? $_POST['customcss'] : '';
		$theme_options['enable_sticky_header_menu'] = ($_POST['enable_sticky_header_menu']) ? $_POST['enable_sticky_header_menu'] : '';
		$theme_options['supreme_author_bio_posts'] = ($_POST['supreme_author_bio_posts']) ? $_POST['supreme_author_bio_posts'] : '';
		$theme_options['supreme_author_bio_pages'] = ($_POST['supreme_author_bio_pages']) ? $_POST['supreme_author_bio_pages'] : '';
		$theme_options['supreme_show_breadcrumb'] = ($_POST['supreme_show_breadcrumb']) ? $_POST['supreme_show_breadcrumb'] : '';
		$theme_options['supreme_global_contactus_captcha'] = ($_POST['supreme_global_contactus_captcha']) ? $_POST['supreme_global_contactus_captcha'] : '';
		$theme_options['enable_inquiry_form'] = ($_POST['enable_inquiry_form']) ? $_POST['enable_inquiry_form'] : '';
		$theme_options['post_type_label'] = ($_POST['post_type_label']) ? $_POST['post_type_label'] : '';
		$theme_options['supreme_gogle_analytics_code'] = ($_POST['supreme_gogle_analytics_code']) ? $_POST['supreme_gogle_analytics_code'] : '';
		$theme_options['supreme_display_image'] = ($_POST['supreme_display_image']) ? $_POST['supreme_display_image'] : '';
		$theme_options['supreme_display_noimage'] = ($_POST['supreme_display_noimage']) ? $_POST['supreme_display_noimage'] : '';
		$theme_options['display_author_name'] = ($_POST['display_author_name']) ? $_POST['display_author_name'] : '';
		$theme_options['display_publish_date'] = ($_POST['display_publish_date']) ? $_POST['display_publish_date'] : '';
		$theme_options['display_post_terms'] = ($_POST['display_post_terms']) ? $_POST['display_post_terms'] : '';
		$theme_options['display_post_response'] = ($_POST['display_post_response']) ? $_POST['display_post_response'] : '';
		$theme_options['supreme_archive_display_excerpt'] = ($_POST['supreme_archive_display_excerpt']) ? $_POST['supreme_archive_display_excerpt'] : '';
		$theme_options['templatic_excerpt_length'] = ($_POST['templatic_excerpt_length']) ? $_POST['templatic_excerpt_length'] : '';
		$theme_options['templatic_excerpt_link'] = ($_POST['templatic_excerpt_link']) ? $_POST['templatic_excerpt_link'] : '';
		$theme_options['enable_comments_on_page'] = ($_POST['enable_comments_on_page']) ? $_POST['enable_comments_on_page'] : '';
		$theme_options['enable_comments_on_post'] = ($_POST['enable_comments_on_post']) ? $_POST['enable_comments_on_post'] : '';
		
		update_option('hide_ajax_notification',$_POST['hide_ajax_notification']);
		update_option($pref.'_theme_settings',$theme_options);
		wp_safe_redirect(admin_url('themes.php?page=theme-settings-page&updated=1'));
	}else{
		wp_die("You do not have permission to edit theme settings.");
	}
}
/*
Function Name: theme_settings_page_callback
Purpose		 : To display theme setting options 
*/
if(!function_exists('theme_settings_page_callback')){
	function theme_settings_page_callback() {
		if(function_exists('supreme_prefix')){
			$pref = supreme_prefix();
		}else{
			$pref = sanitize_key( apply_filters( 'hybrid_prefix', get_template() ) );
		}
		$theme_settings = get_option($pref.'_theme_settings');
?>
<div class="wrap">
  <form name="theme_options_settings" id="theme_options_settings" method="post" enctype="multipart/form-data">
    <input type="hidden" name="theme_options_nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>" />
    <div class="icon32 icon32-posts-post" id="icon-edit"><br>
    </div>
    <h2>
      <?php _e("Theme Settings",THEME_DOMAIN);?>
    </h2>    
    <ul class="subsubsub">
      <li class="general_settings"> <a href="#general_settings">
        <?php _e("General Settings",THEME_DOMAIN);?>
        </a> | </li>
      <li class="listing_settings"> <a href="#listing_settings">
        <?php _e("Category/Tag Archive Page Settings",THEME_DOMAIN);?>
        </a> | </li>
      <li class="detail_settings"> <a href="#detail_settings">
        <?php _e("Comments Settings",THEME_DOMAIN);?>
        </a> </li>
    </ul>
    <?php if($_REQUEST['updated']){?>
    <div class="updated" id="message" style="clear:both">
      <p>
        <?php _e("Theme Settings",THEME_DOMAIN);?>
        <strong>
        <?php _e("saved",THEME_DOMAIN);?>
        </strong>.</p>
    </div>
    <?php }?>
    <table class="form-table">
      <tbody>
        <!-- General Settings -->
        <tr id="general_settings">
          <td colspan="2">
          	<div class="theme_sub_title" style="margin-top:0;"><?php _e("General Settings",THEME_DOMAIN);?></div>
            </td>
        </tr>
        <tr>
          <th><label for="supreme_global_layout">
              <?php _e('Global layout',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <select style="vertical-align:top;width:200px;" name="supreme_global_layout" id="supreme_global_layout">
                <option value="layout_default" <?php echo ($theme_settings['supreme_global_layout']=='layout_default') ? 'selected' : ''?>>
                <?php _e("Default Layout",THEME_DOMAIN);?>
                </option>
                <option value="layout_1c" <?php echo ($theme_settings['supreme_global_layout']=='layout_1c') ? 'selected' : ''?>>
                <?php _e("One Column",THEME_DOMAIN);?>
                </option>
                <option value="layout_2c_l" <?php echo ($theme_settings['supreme_global_layout']=='layout_2c_l') ? 'selected' : ''?>>
                <?php _e("Two Columns, Left",THEME_DOMAIN);?>
                </option>
                <option value="layout_2c_r" <?php echo ($theme_settings['supreme_global_layout']=='layout_2c_r') ? 'selected' : ''?>>
                <?php _e("Two Columns, Right",THEME_DOMAIN);?>
                </option>
              </select>
            </div>
            <p class="description">
              <?php _e("This setting can be overwritten by layout settings within individual posts/pages.",THEME_DOMAIN);?>
            </p></td>
        </tr>
        <tr>
          <th><label for="hide_ajax_notification">
              <?php _e('Show the "Insert Sample Data" button',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <input type="checkbox" value="1" <?php echo (get_option('hide_ajax_notification')==1) ? 'checked' : ''?> id="hide_ajax_notification" name="hide_ajax_notification">
              <label for="hide_ajax_notification"> <?php _e('Disable',THEME_DOMAIN);?></label>
            </div>
            <p class="description">
              <?php _e("Disabling this will hide the entire yellow box that appears above the active theme inside Appearance &rsaquo;&rsaquo; Themes section.",THEME_DOMAIN);?>
            </p>
            </td>
        </tr>
        <tr>
          <th><label for="customcss">
              <?php _e("Use custom.css",THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <input type="checkbox"  value="1" <?php echo ($theme_settings['customcss']==1) ? 'checked' : ''?> id="customcss" name="customcss">
              <label for="customcss">
                <?php _e('Enable',THEME_DOMAIN);?>
              </label>
            </div>
            <p class="description">
              <?php echo sprintf(__('Custom.css is used for quick design tweaks. You can modify it from the %s theme editor section. For more details on custom.css read %s this article.',THEME_DOMAIN),'<a href="'.site_url().'/wp-admin/theme-editor.php">theme editor section</a>','<a href="http://templatic.com/docs/using-custom-css-for-theme-customizations/">this article</a>');?>
            </p></td>
        </tr>
        <tr>
          <th><label for="enable_sticky_header_menu">
              <?php _e('Show sticky header',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <input type="checkbox"  value="1" <?php echo ($theme_settings['enable_sticky_header_menu']==1) ? 'checked' : ''?> id="enable_sticky_header_menu" name="enable_sticky_header_menu">
              <label for="enable_sticky_header_menu">
                <?php _e('Enable',THEME_DOMAIN);?>
              </label>
            </div>
            <p class="description">
              <?php _e('Sticky header is a persistent navigation bar that continues to show even when you scroll down the page.',THEME_DOMAIN);?>
            </p></td>
        </tr>
        <tr>
          <th><label for="supreme_author_bio_posts">
              <?php _e('Show author bio on WordPress pages',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <input type="checkbox" value="1" <?php echo ($theme_settings['supreme_author_bio_posts']==1) ? 'checked' : ''?>  id="supreme_author_bio_posts" name="supreme_author_bio_posts">
              <label for="supreme_author_bio_posts">
                <?php _e('Enable',THEME_DOMAIN);?>
              </label>
            </div>
            <p class="description">
              <?php _e('If enabled, a small box with the authors name, avatar and description will be shown below regular WordPress pages.',THEME_DOMAIN);?>
            </p></td>
        </tr>
        <tr>
          <th><label for="supreme_author_bio_pages">
              <?php _e('Show author bio on WordPress posts',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <input type="checkbox" value="1" <?php echo ($theme_settings['supreme_author_bio_pages']==1) ? 'checked' : ''?>  id="supreme_author_bio_pages" name="supreme_author_bio_pages">
              <label for="supreme_author_bio_pages">
                <?php _e('Enable',THEME_DOMAIN);?>
              </label>
            </div>
            <p class="description">
              <?php _e('If enabled, a small box with the authors name, avatar and description will be shown below regular WordPress posts.',THEME_DOMAIN);?>
            </p></td>
        </tr>
        <tr>
          <th><label for="supreme_show_breadcrumb">
              <?php _e('Show breadcrumbs',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <input type="checkbox" value="1"  <?php echo ($theme_settings['supreme_show_breadcrumb']==1) ? 'checked' : ''?> id="supreme_show_breadcrumb" name="supreme_show_breadcrumb">
              <label for="supreme_show_breadcrumb">
                <?php _e('Enable',THEME_DOMAIN);?>
              </label>
            </div></td>
        </tr>
        <tr>
          <th><label for="enable_inquiry_form">
              <?php _e('Contact page options',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <input type="checkbox" value="1" <?php echo ($theme_settings['enable_inquiry_form']==1) ? 'checked' : ''?> id="enable_inquiry_form" name="enable_inquiry_form">
              <label for="enable_inquiry_form">
                <?php _e('Enable the inquiry form on the contact page',THEME_DOMAIN);?>
              </label>
              <br/>
              <input type="checkbox" value="1" <?php echo ($theme_settings['supreme_global_contactus_captcha']==1) ? 'checked' : ''?>  id="supreme_global_contactus_captcha" name="supreme_global_contactus_captcha">
              <label for="supreme_global_contactus_captcha">
                <?php _e('Enable captcha on the contact page',THEME_DOMAIN);?>
              </label>
            </div>
            <p class="description">
              <?php _e('Use the "Contact Us" page template to create a contact page. For captcha to work you must install the  <a href="http://wordpress.org/plugins/wp-recaptcha/">WP-reCAPTCHA plugin</a>.',THEME_DOMAIN);?>
            </p></td>
        </tr>
        <tr>
          <th><label for="post_type_label">
              <?php _e('Categories for the 404 page',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <?php 
										$post_types=get_post_types();
										$PostTypeName = '';
										foreach($post_types as $post_type):		
											if($post_type!='page' && $post_type!="attachment" && $post_type!="revision" && $post_type!="nav_menu_item" && $post_type!="admanager"):
												$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));	
												$archive_query = new WP_Query('showposts=60&post_type='.$post_type);
												if( count(@$archive_query->posts) > 0 ){
													$PostTypeName .= $post_type.', ';
												}
											endif;
										endforeach;
										$all_post_types = rtrim($PostTypeName,', ');
									?>
              <input type="text" value="<?php echo $theme_settings['post_type_label'];?>" id="post_type_label" name="post_type_label">
            </div>
            <p class="description">
              <?php _e('Enter comma separated post type slugs that you want displayed.',THEME_DOMAIN);?>
              <br/>
              <?php _e(' Available slugs: ',THEME_DOMAIN); echo $all_post_types;?>
            </p></td>
        </tr>
        <tr>
          <th><label for="supreme_gogle_analytics_code">
              <?php _e('Google Analytics tracking code',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <textarea name="supreme_gogle_analytics_code" id="supreme_gogle_analytics_code" rows="6" cols="60"><?php echo stripslashes($theme_settings['supreme_gogle_analytics_code']);?></textarea>
            </div>
            <p class="description">
              <?php _e("Enter the analytics code you received from GA or some other analytics software. e.g. <a href='https://www.google.co.in/analytics/'>Google Analytics</a>",THEME_DOMAIN);?>
            </p></td>
        </tr>
        <!-- Listing Page Settings -->
        <tr id="listing_settings">
          <td colspan="2"><div class="theme_sub_title">
              <?php _e('Category page settings',THEME_DOMAIN);?>
            </div>
            </td>
        </tr>
        <tr>
          <th><label for="supreme_display_image">
              <?php _e('Category page display options',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <input type="checkbox" value="1" <?php echo ($theme_settings['supreme_display_image']==1) ? 'checked' : ''?>  id="supreme_display_image" name="supreme_display_image">
              <label for="supreme_display_image">
                <?php _e("Show thumbnail on archive pages",THEME_DOMAIN);?>
              </label>
              <br/>
              <input type="checkbox" value="1" <?php echo ($theme_settings['supreme_display_noimage']==1) ? 'checked' : ''?> id="supreme_display_noimage" name="supreme_display_noimage">
              <label for="supreme_display_noimage">
                <?php _e("Show <em>no-image-available</em> thumbnail when there is no image uploaded in a particular post",THEME_DOMAIN);?>
              </label>
              <br/>
              <input type="checkbox" value="1" <?php echo ($theme_settings['display_author_name']==1) ? 'checked' : ''?> id="display_author_name" name="display_author_name">
              <label for="display_author_name">
                <?php _e("Show author name with a link to his profile for all posts",THEME_DOMAIN);?>
              </label>
              <br/>
              <input type="checkbox" value="1" <?php echo ($theme_settings['display_publish_date']==1) ? 'checked' : ''?> id="display_publish_date" name="display_publish_date">
              <label for="display_publish_date">
                <?php _e("Show published date of all posts",THEME_DOMAIN);?>
              </label>
              <br/>
              <input type="checkbox" value="1" <?php echo ($theme_settings['display_post_terms']==1) ? 'checked' : ''?> id="display_post_terms" name="display_post_terms">
              <label for="display_post_terms">
                <?php _e("Show selected categories and tags of individual posts",THEME_DOMAIN);?>
              </label>
              <br/>
              <input type="checkbox" value="1" <?php echo ($theme_settings['display_post_response']==1) ? 'checked' : ''?> id="display_post_response" name="display_post_response">
              <label for="display_post_response">
                <?php _e("Show number of comments for all posts with a link to comments section on post detail page",THEME_DOMAIN);?>
              </label>
              <br/>
              <input type="checkbox" value="1" <?php echo ($theme_settings['supreme_archive_display_excerpt']==1) ? 'checked' : ''?> id="supreme_archive_display_excerpt" name="supreme_archive_display_excerpt">
              <label for="supreme_archive_display_excerpt">
                <?php _e("Show post <a href='http://codex.wordpress.org/Excerpt'>excerpt</a> instead of full text",THEME_DOMAIN);?>
              </label>
              <br/>
            </div></td>
        </tr>
        <tr>
          <th><label for="templatic_excerpt_length">
              <?php _e('Excerpt length',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <input type="text" value="<?php echo $theme_settings['templatic_excerpt_length'];?>" id="templatic_excerpt_length" name="templatic_excerpt_length">
              <br/>
            </div>
            <p class="description">
              <?php _e('Enter the number of characters that should be displayed from your post description. This option can be overwritten by entering the actual excerpt for the post.',THEME_DOMAIN);?>
            </p></td>
        </tr>
        <tr>
          <th><label for="templatic_excerpt_link">
              <?php _e('Read more link name',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <input type="text" value="<?php echo stripslashes($theme_settings['templatic_excerpt_link']);?>" id="templatic_excerpt_link" name="templatic_excerpt_link">
            </div>
            <p class="description">
              <?php _e('Default link name is "Read More".',THEME_DOMAIN);?>
            </p></td>
        </tr>
        <!-- Detail Page Settings -->
        <tr id="detail_settings">
          <td colspan="2"><div class="theme_sub_title">
              <?php _e('Comments settings',THEME_DOMAIN);?>
            </div>
            </td>
        </tr>
        <tr>
          <th><label for="enable_comments_on_page">
              <?php _e('Comment display options',THEME_DOMAIN);?>
            </label></th>
          <td><div class="element">
              <input type="checkbox" value="1" <?php echo ($theme_settings['enable_comments_on_page']==1) ? 'checked' : ''?>  id="enable_comments_on_page" name="enable_comments_on_page">
              <label for="enable_comments_on_page">
                <?php _e("Show comments on WordPress pages",THEME_DOMAIN);?>
              </label>
              <br/>
              <input type="checkbox" value="1" <?php echo ($theme_settings['enable_comments_on_post']==1) ? 'checked' : ''?>  id="enable_comments_on_post" name="enable_comments_on_post">
              <label for="enable_comments_on_post">
                <?php _e('Show comments on posts (includes custom post types that you created)',THEME_DOMAIN);?>
              </label>
            </div></td>
        </tr>
        <tr>
          <td colspan="2"><p style="clear: both;" class="submit">
              <input type="submit" value="<?php _e('Save All Settings',THEME_DOMAIN); ?>" class="button-primary" name="Submit">
            </p></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>
<?php
	}
}
?>