<?php
/**
 * Tevolution single custom post type template
 *
**/
get_header(); //Header Portion
global $upload_folder_path;
if(isset($_POST['preview'])){
	
	$_SESSION['custom_fields'] = $_POST; // set custom_fields session	
	if(isset($_POST['category']))
	 {
		$_SESSION['category'] = $_POST['category'];
	 }
}
if(isset($_POST['imgarr']) && $_POST['imgarr']!=""){
	$_SESSION['file_info'] = explode(",",$_POST['imgarr']);
	$_SESSION["templ_file_info"] = explode(",",$_POST['imgarr']);
}
if(isset($_FILES) && !empty($_FILES) && !strstr($_SERVER['REQUEST_URI'],'/wp-admin/'))
{
	foreach($_FILES as $key => $FILES)
	 {		   
		if($FILES['name']!=''){
			$_SESSION['upload_file'][$key] = get_file_upload($_FILES[$key]);
		}
	 }	
}	
if($_SESSION["file_info"])
{
	foreach($_SESSION["file_info"] as $image_id=>$val)
	{
		 $image_src =  get_template_directory_uri().'/images/tmp/'.$val;
		 break;
	}				
	
}else
{
	/* execute when come after go back and edit */
	$image_src = @$thumb_img_arr[0];
	if($_REQUEST['pid']){
		$large_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'medium');
		$thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'thumb');
	}
	$image_src = $large_img_arr[0];		
}
if($_REQUEST['pid'])
{	/* execute when comes for edit the post */
	$large_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'medium');
	$thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'thumb');
	$largest_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'large');		
}
 
$current_user = wp_get_current_user();
$cur_post_id = $_SESSION['custom_fields']['cur_post_id'];
$cur_post_type = get_post_meta($cur_post_id,'submit_post_type',true);
$tmpdata = get_option('templatic_settings');	
$address=$_SESSION['custom_fields']['address'];
$geo_latitude =$_SESSION['custom_fields']['geo_latitude'];
$geo_longitude = $_SESSION['custom_fields']['geo_longitude'];
$map_type =$_SESSION['custom_fields']['map_view'];
$website=$_SESSION['custom_fields']['website'];
$phone=$_SESSION['custom_fields']['phone'];
$listing_logo=$_SESSION['upload_file']['listing_logo'];
$listing_timing=$_SESSION['custom_fields']['listing_timing'];
$email=$_SESSION['custom_fields']['email'];
$special_offer=$_SESSION['custom_fields']['proprty_feature'];
$video=$_SESSION['custom_fields']['video'];
$facebook=$_SESSION['custom_fields']['facebook'];
$google_plus=$_SESSION['custom_fields']['google_plus'];
$twitter=$_SESSION['custom_fields']['twitter'];
if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=''){
	$address=get_post_meta($_REQUEST['pid'],'address',true);
	$geo_latitude =get_post_meta($_REQUEST['pid'],'geo_latitude',true);
	$geo_longitude = get_post_meta($_REQUEST['pid'],'geo_longitude',true);
	$map_type =get_post_meta($_REQUEST['pid'],'map_type',true);
	
	$website=get_post_meta($_REQUEST['pid'],'website',true);
	$phone=get_post_meta($_REQUEST['pid'],'phone',true);
	$listing_logo=get_post_meta($_REQUEST['pid'],'listing_logo',true);
	$listing_timing=get_post_meta($_REQUEST['pid'],'listing_timing',true);
	$email=get_post_meta($_REQUEST['pid'],'email',true);
	
	$special_offer=get_post_meta($_REQUEST['pid'],'proprty_feature',true);
	$video=get_post_meta($_REQUEST['pid'],'video',true);
	
	$facebook=get_post_meta($_REQUEST['pid'],'facebook',true);
	$google_plus=get_post_meta($_REQUEST['pid'],'google_plus',true);
	$twitter=get_post_meta($_REQUEST['pid'],'twitter',true);
}
//condition for captcha inserted properly or not.
$tmpdata = get_option('templatic_settings');
if(isset($tmpdata['user_verification_page']) && $tmpdata['user_verification_page'] != "")
{
	$display = $tmpdata['user_verification_page'];
}
else
{
	$display = "";	
}
$id = $_SESSION['custom_fields']['cur_post_id'];
$permalink = get_permalink( $id );
if( is_plugin_active('wp-recaptcha/wp-recaptcha.php') && $tmpdata['recaptcha'] == 'recaptcha' && in_array('submit',$display) && 'preview' == @$_REQUEST['page'] && 'delete' != $_REQUEST['action'] ){
		require_once( WP_CONTENT_DIR.'/plugins/wp-recaptcha/recaptchalib.php');
		$a = get_option("recaptcha_options");
		$privatekey = $a['private_key'];
						$resp = recaptcha_check_answer ($privatekey,
								getenv("REMOTE_ADDR"),
								$_POST["recaptcha_challenge_field"],
								$_POST["recaptcha_response_field"]);
											
		if (!$resp->is_valid ) {
			if($_REQUEST['pid'] != '')
			 {
				wp_redirect(get_permalink($cur_post_id).'/?ptype=post_event&pid='.$_REQUEST['pid'].'&action=edit&backandedit=1&ecptcha=captch');
			 }
			 else
			 {
				wp_redirect(get_permalink($cur_post_id).'/?ptype=post_event&backandedit=1&ecptcha=captch');	 
			 }
			exit;
		} 
	}
if(file_exists(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php') && is_plugin_active('are-you-a-human/areyouahuman.php') && $tmpdata['recaptcha'] == 'playthru'  && in_array('submit',$display) && 'preview' == @$_REQUEST['page'] && 'delete' != $_REQUEST['action'] )
{
	require_once( get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php');
	require_once(get_tmpl_plugin_directory().'are-you-a-human/includes/ayah.php');
	$ayah = new AYAH();
	$score = $ayah->scoreResult();
	if(!$score)
	{
		wp_redirect(get_permalink($cur_post_id).'/?ptype=post_event&backandedit=1&invalid=playthru');
		exit;
	}
}
if(function_exists('bdw_get_images_plugin') && (isset($_REQUEST['pid']) && $_REQUEST['pid']!=''))
{
	$post_img = bdw_get_images_plugin($_REQUEST['pid'],'directory-single-image');
	$postimg_thumbnail = bdw_get_images_plugin($_REQUEST['pid'],'thumbnail');
	$more_listing_img = bdw_get_images_plugin($_REQUEST['pid'],'directory-single-thumb');
	$thumb_img = $post_img[0]['file'];
	$attachment_id = $post_img[0]['id'];
	$attach_data = get_post($attachment_id);
	$img_title = $attach_data->post_title;
	$img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
}
wp_enqueue_script('jquery-ui-tabs');
?>
<link rel='stylesheet' id='directory_style-css'  href='<?php echo TEVOLUTION_DIRECTORY_URL; ?>css/directory.css?ver=3.5.2' type='text/css' media='all' />		
 <script type="text/javascript">		
		jQuery(function() {
			jQuery('#image_gallery a').lightBox();
		});
		
		jQuery('#tabs').bind('tabsshow', function(event, ui) {			
		    if (ui.panel.id == "listing_map") {	    
				google.maps.event.trigger(Demo.map, 'resize');
				Demo.map.setCenter(Demo.map.center); // be sure to reset the map center as well
				Demo.init();
		    }
		});
		jQuery(function() {
			jQuery('#tabs').tabs({
				activate: function(event ,ui){
				    //console.log(event);
				    var panel=jQuery(".ui-state-active a").attr("href");
				    if(panel=='#listing_map'){
					     google.maps.event.trigger(Demo.map, 'resize');
						Demo.map.setCenter(Demo.map.center); // be sure to reset the map center as well
						Demo.init();
				    }
				}
			});
		});
		</script>
<?php do_action('fetch_directory_preview_field');?>
<!-- start content part-->
<div id="content" role="main">	
	
     <div id="post-<?php the_ID(); ?>" class="listing type-listing listing-type-preview hentry" >  
     
		<?php include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-custom_fields/submit_preview_buttons.php"); /* fetch publish options and button options */?>
          
          <?php		
               if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete'):
                    //do_action('tmpl_detail_page_custom_fields_collection');
               else:	
                    //do_action('tmpl_preview_page_fields_collection',$cur_post_type);
               endif;	
          ?>  
          
         <header class="entry-header">
                    <div class="entry-header-title">
                         <h1 itemprop="name" class="entry-title"><?php echo stripslashes($_SESSION['custom_fields']['post_title']); ?></h1>
					
                         <div class="entry-header-custom-wrap">
                              <div class="entry-header-custom-left">
                                   <?php if($address!=""):?>
                                   	<p><?php echo $address?></p>
                                   <?php endif;?>
                                   <?php if($website!=""):?>
                                   	<p><a href="<?php echo $website;?>"><?php _e('Visit Website',DOMAIN);?></a></p>
                                   <?php endif;?>
                              </div>
                              <div class="entry-header-custom-right">
                                   <?php if($phone!=""):?>
                                   <p class="phone"><label><?php _e('Phone: ',DOMAIN);?></label><span class="listing_custom"><?php echo $phone;?></span></p>
                                   <?php endif;?>
                                   <?php if($listing_timing!=""):?>
                                   <p class="time"><label><?php _e('Time: ',DOMAIN);?></label><span class="listing_custom"><?php echo $listing_timing;?></span></p>
                                   <?php endif;?>
                                  	<?php if($email!=""):?>
                                   <p class="email"><label><?php _e('Email: ',DOMAIN);?></label><span class="listing_custom"><?php echo antispambot($email);?></span></p>
                                   <?php endif;?>                                  
                              </div>
                         </div>
                    </div>
                    
                    <?php if($listing_logo!=""):?>
                    <div class="entry-header-logo">
                    	<img src="<?php echo $listing_logo?>" alt="<?php _e('Logo',DOMAIN);?>" />
                    </div>
                    <?php endif;?>
               </header>
        		<!-- listing content-->
               <div class="entry-content">
	          <?php do_action('directory_preview_before_post_content'); /*Add Action for after preview post content. */?>
               	<div id="tabs">
                    	 <ul>
                              <li><a href="#listing_description"><?php _e('Overview',DOMAIN);?></a></li>
                              
                              <?php if(!empty($_SESSION['file_info']) || !empty($post_img)):?>
                              <li><a href="#image_gallery"><?php _e('Photos',DOMAIN);?></a></li>
                              <?php endif;?>
                              
                              <?php if($address!=''):?>
                              <li><a href="#listing_map"><?php _e('Map',DOMAIN);?></a></li>
                              <?php endif;?>
                              
                              <?php if($special_offer!=""):?>
                              <li><a href="#special_offer"><?php _e('Special Offer',DOMAIN);?></a></li>
                              <?php endif;?>
                              
                              <?php if($video!=""):?>
                              <li><a href="#listing_video"><?php _e('Video',DOMAIN);?></a></li>
                              <?php endif;?>
                         </ul>
                    	 <!--Overview Section Start -->
                         <div id="listing_description">
                              <div class="<?php if($thumb_img || $_SESSION['file_info']!=''):?>listing_content<?php else:?>content_listing <?php endif;?>">
                              
                              <?php echo stripslashes($_SESSION['custom_fields']['post_content']);?>
                              
                              </div>
                               <?php if($thumb_img):?>
                                   <div id="directory_detail_img" class="entry-header-image">
                                        <?php do_action('directory_before_post_image');?>
                                        
                                        <div class="listing-image">                    
                                        <?php 			
                                        if ( has_post_thumbnail()):
                                        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'large' );
                                             ?>               
                                             <a id="directory_detail_img" href="<?php echo $thumb[0];?>" class="listing_img">
                                             <?php the_post_thumbnail('directory-single-image'); ?> 
                                             </a>
                                         <?php    
                                        else:
                                             ?>
                                             <a href="<?php echo $image_attributes[0];?>" class="listing_img">
                                             <?php if($thumb_img):?>
                                                  <img src="<?php echo $thumb_img; ?>"  alt="<?php echo $img_alt; ?>" title="<?php echo $img_title; ?>" />                   
                                             <?php endif;?>
                                             </a>	
                                         <?php endif;?>
                                        </div>
                                        
                                        <?php if(!empty($more_listing_img) && count($more_listing_img)>1):?>
                                             <div id="gallery">
                                                  <ul class="more_photos">
                                                  <?php foreach($more_listing_img as $key=>$value):
                                                            $attachment_id = $value['id'];
                                                            $attach_data = get_post($attachment_id);
                                                            $image_attributes = wp_get_attachment_image_src( $attachment_id ,'large'); // returns an array							
                                                            $img_title = $attach_data->post_title;
                                                  ?>
                                                       <li>
                                                             <a href="<?php echo $image_attributes['0'];?>" title="<?php echo $img_title; ?>" alt="<?php echo $img_alt; ?>" >		
                                                                 <img src="<?php echo $value['file'];?>" />
                                                             </a>
                                                       </li>
                                                            
                                                  <?php endforeach;?>
                                                  </ul>
                                             </div>
                                        <?php endif;?>
                                        
                                        <?php do_action('directory_after_post_image');?>
                                   </div><!-- .entry-header-image -->
                                   <?php endif;?>
                              
                             <?php if($_SESSION['file_info']):?>
                              <div id="directory_detail_img" class="entry-header-image">
                                   <?php do_action('directory_preview_before_post_image');?>                                   
                                  
                                   
                                   <!-- --->
                                    <?php	
							$thumb_img_counter = 0;
							$thumb_img_counter = $thumb_img_counter+count($_SESSION["file_info"]);
							$image_path = get_image_phy_destination_path_plugin();
							$tmppath = "/".$upload_folder_path."tmp/";						
							foreach($_SESSION["file_info"] as $image_id=>$val):
								 $thumb_image = get_template_directory_uri().'/images/tmp/'.trim($val);
								break;
							endforeach;	
							
							if(isset($_REQUEST['pid']) && $_REQUEST['pid']!="")
							{	/* execute when comes for edit the post */
								$large_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'directory-single-image');
								$thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'directory-single-thumb');
								$largest_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'directory-single-image');		
							}
						 ?>							
							<div class="listing-image">
								 <?php $f=0; foreach($_SESSION["file_info"] as $image_id=>$val):
										$curry = date("Y");
										$currm = date("m");
										$src = get_template_directory().'/images/tmp/'.$val;
										$img_title = pathinfo($val);
										
								  ?>
									<?php if($largest_img_arr): ?>
											<?php  foreach($largest_img_arr as $value):
												 $name = end(explode("/",$value['file']));
												  if($val == $name):	
											?>
												<img src="<?php echo  $value['file'];?>" alt="" width="300" height="230" class="Thumbnail thumbnail large post_imgimglistimg"/>
											<?php endif;
												endforeach;?>
									<?php else: ?>								
										<img src="<?php echo $thumb_image;?>" alt="" width="300" height="230" class="Thumbnail thumbnail large post_imgimglistimg"/>
									<?php endif; ?>    
								  <?php if($f==0) break; endforeach;?>								 
							 </div>	
                                    <?php  if(count(array_filter($_SESSION["file_info"]))>1):?>					
							 <div id="gallery" class="image_title_space">
								<ul class="more_photos">
								 <?php foreach($_SESSION["file_info"] as $image_id=>$val)
									{
										$curry = date("Y");
										$currm = date("m");
										$src = get_template_directory().'/images/tmp/'.$val;
										$img_title = pathinfo($val);						
										if($val):
										if(file_exists($src)):
												 $thumb_image = get_template_directory_uri().'/images/tmp/'.$val; ?>
												 <li><a href="<?php echo $thumb_image;?>" title="<?php echo $img_title['filename']; ?>"><img src="<?php echo $thumb_image;?>" alt="" height="50" width="50" title="<?php echo $img_title['filename'] ?>" /></a></li>
										<?php else: ?>
											<?php
												if($largest_img_arr):
												foreach($largest_img_arr as $value):
													$name = end(explode("/",$value['file']));									
													if($val == $name):?>
													<li><a href="<?php echo $value['file']; ?>" title="<?php echo $img_title['filename']; ?>"><img src="<?php echo $value['file'];?>" alt="" height="50" width="50" title="<?php echo $img_title['filename'] ?>" /></a></li>
											<?php
													endif;
												endforeach;
												endif;
											?>
										<?php endif; ?>
										
										<?php else: ?>
										<?php if($thumb_img_arr): ?>
											<?php 
											$thumb_img_counter = $thumb_img_counter+count($thumb_img_arr);
											for($i=0;$i<count($thumb_img_arr);$i++):
												$thumb_image = $large_img_arr[$i];
												
												if(!is_array($thumb_image)):
											?>
											  <li><a href="<?php echo $thumb_image;?>" title="<?php echo $img_title['filename']; ?>"><img src="<?php echo $thumb_image;?>" alt="" height="50" width="50" title="<?php echo $img_title['filename'] ?>" /></a></li>
											  <?php endif;?>
										<?php endfor; ?>
										<?php endif; ?>	
										<?php endif; ?>
									<?php
									$thumb_img_counter++;
									} ?>
									
									</ul>
							 </div>                 
                                   <?php endif;?>
                                   <!-- -->
                                   
                                   <?php do_action('directory_preview_after_post_image');?> 
                                   </div>                             
                              <?php endif;?>
                         </div>
                         <!--Overview Section End -->	
                         <?php if(!empty($postimg_thumbnail)):?>
                              <!--Image gallery Section Start -->
                              <div id="image_gallery">
                                   <ul>
                                     <?php foreach($postimg_thumbnail as $key=>$value):
                                             $attachment_id = $value['id'];
                                             $attach_data = get_post($attachment_id);
                                             $image_attributes = wp_get_attachment_image_src( $attachment_id ,'large'); // returns an array							
                                             $img_title = $attach_data->post_title;
                                     ?>
                                        <li>
                                              <a href="<?php echo $image_attributes['0'];?>" title="<?php echo $img_title; ?>" alt="<?php echo $img_alt; ?>" >		
                                                  <img src="<?php echo $value['file'];?>" />
                                              </a>
                                        </li>
                                             
                                   <?php endforeach;?>
                                   </ul>
                              
                              </div>
                              <!--Image gallery Section End -->
                         <?php endif;?>
                         
                          <?php if(!empty($_SESSION["file_info"])):?>
                              <!--Image gallery Section Start -->
                              <div id="image_gallery">
                                   <ul>
                                    <?php foreach($_SESSION["file_info"] as $image_id=>$val)
									{
										$curry = date("Y");
										$currm = date("m");
										$src = get_template_directory().'/images/tmp/'.$val;
										$img_title = pathinfo($val);						
										if($val):
										if(file_exists($src)):
												 $thumb_image = get_template_directory_uri().'/images/tmp/'.$val; ?>
												 <li><a href="<?php echo $thumb_image;?>" title="<?php echo $img_title['filename']; ?>"><img src="<?php echo $thumb_image;?>" alt="" height="150" width="150" title="<?php echo $img_title['filename'] ?>" /></a></li>
										<?php else: ?>
											<?php
												if($largest_img_arr):
												foreach($largest_img_arr as $value):
													$name = end(explode("/",$value['file']));									
													if($val == $name):?>
													<li><a href="<?php echo $value['file']; ?>" title="<?php echo $img_title['filename']; ?>"><img src="<?php echo $value['file'];?>" alt="" height="150" width="150" title="<?php echo $img_title['filename'] ?>" /></a></li>
											<?php
													endif;
												endforeach;
												endif;
											?>
										<?php endif; ?>
										
										<?php else: ?>
										<?php if($thumb_img_arr): ?>
											<?php 
											$thumb_img_counter = $thumb_img_counter+count($thumb_img_arr);
											for($i=0;$i<count($thumb_img_arr);$i++):
												$thumb_image = $large_img_arr[$i];
												
												if(!is_array($thumb_image)):
											?>
											  <li><a href="<?php echo $thumb_image;?>" title="<?php echo $img_title['filename']; ?>"><img src="<?php echo $thumb_image;?>" alt="" height="150" width="150" title="<?php echo $img_title['filename'] ?>" /></a></li>
											  <?php endif;?>
										<?php endfor; ?>
										<?php endif; ?>	
										<?php endif; ?>
									<?php
									$thumb_img_counter++;
									} ?>
                                   </ul>
                              </div>
                              <!--Image gallery Section End -->
                         <?php endif;?>
                         
                         <?php if($address!=''):?>
                              <!--Map Section Start -->
                              <div id="listing_map">
                                   <div id="directory_location_map" style="width:100%;">
                                        <div class="directory_google_map" id="directory_google_map_id" style="width:100%;"> 
                                        <?php include_once (TEVOLUTION_DIRECTORY_DIR.'functions/google_map_detail.php');?> 
                                        </div>  <!-- google map #end -->
                                   </div>
                              </div>
                              <!--Map Section End -->
                         <?php endif; ?>
                         
                         <?php if($special_offer!=""):?>
                              <!--Special Offer Start -->
                              <div id="special_offer">
                                   <?php echo stripslashes($special_offer);?>
                              </div>
                              <!--Special Offer End -->
                         <?php endif;?>
                         
                         
                         <?php if($video!=""):?>
                              <!--Video Code Start -->
                              <div id="listing_video">
                                   <?php echo str_replace('\"','',$video);?>
                              </div>
                              <!--Video code End -->
                         <?php endif;?>
                    </div>
               
               	<div class="single-social-media">  
					<?php if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; } ?>			
                    <script type="text/javascript" src="<?php echo $http; ?>s7.addthis.com/js/250/addthis_widget.js#username=xa-4c873bb26489d97f"></script>
                    <?php if($facebook!=""):?>
                         <a href="<?php echo $facebook;?>"><img src="<?php echo TEVOLUTION_DIRECTORY_URL; ?>images/i_facebook21.png" alt="Facebook"  /></a>
                    <?php endif;?>
                    
                    <?php if($twitter!=""):?>
                         <a href="<?php echo $twitter;?>"><img src="<?php echo TEVOLUTION_DIRECTORY_URL; ?>images/i_twitter2.png" alt="Twitter"  /></a>
                    <?php endif;?>
                    
                    <?php if($google_plus!=""):?>
                         <a href="<?php echo $google_plus;?>"><img src="<?php echo TEVOLUTION_DIRECTORY_URL; ?>images/i_googleplus.png" alt="Google Plus"  /></a>
                    <?php endif;?>
               </div>
               
        		<?php do_action('directory_preview_after_post_content'); /*Add Action for after preview post content. */?>
	          </div>
               <!--Finish the listing Content -->
     		
               <?php do_action('directory_preview_page_fields_collection');?>
     
          <div id="back-top" class="get_direction clearfix">
               <a href="#top" class="button getdir" style=""><?php _e('Back to Top',DOMAIN);?></a>
          </div>
     
     </div>
</div>
<!--End content part -->
<!--single post type sidebar -->
<?php if ( is_active_sidebar('listing_detail_sidebar' ) ) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar( 'listing_detail_sidebar' ); ?>		
	</div>
	<?php
elseif ( is_active_sidebar( 'primary-sidebar') ) : ?>
	<div id="sidebar-primary" class="sidebar">
		<?php dynamic_sidebar('primary-sidebar'); ?>
	</div>
<?php endif; ?>
<!--end single post type sidebar -->
<script type="text/javascript">
jQuery(function() {
	jQuery( "#tabs" ).tabs();
});
jQuery(function() {
	jQuery('#image_gallery a').lightBox();
});
jQuery('#tabs').bind('tabsshow', function(event, ui) {	
    if (ui.panel.id == "listing_map") {	    
		google.maps.event.trigger(Demo.map, 'resize');
		Demo.map.setCenter(Demo.map.center); // be sure to reset the map center as well
		Demo.init();
    }
});
</script>
<!-- end  content part-->
<?php 
$_SESSION['file_info'] = (isset($_POST['imgarr']) && $_POST['imgarr']!="")?explode(",",$_POST['imgarr']) : '';
get_footer();?>