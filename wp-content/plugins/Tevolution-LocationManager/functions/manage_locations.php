<?php
/*
 * Add action for display the directory locations content page
 * 
 */
add_action('admin_head','location_manage_locations_scriptjs');
function location_manage_locations_scriptjs($taxonomy){ ?>
	<script type="text/javascript">
		function get_categories_checklist(str,city_id){
			if (str==""){
				document.getElementById("field_category").innerHTML="";
				return;
			}else{
				document.getElementById("field_category").innerHTML="";
				document.getElementById("process").style.display ="block";
			}
			var checkedValue = null; 
			var checkedValue = ''; 
			var inputElements = document.getElementsByName('city_post_type[]');
			for(var i=0; inputElements[i]; ++i){
				if(inputElements[i].checked){
					checkedValue += inputElements[i].value +",";
				}
			}
			var cityid='';
			if(city_id!=''){
				cityid='&city_id='+city_id;
			}
			jQuery.ajax({
				url:ajaxUrl,
				type:'POST',
				data:'action=GetCategories_fn&post_type='+checkedValue+'&mod=custom_fields'+cityid,
				success:function(results){
					document.getElementById("process").style.display ="none";
					document.getElementById("field_category").innerHTML=results;
				}
			});
		}
	
function displaychk_frm(){
	dml = document.forms['price_frm'];
	chk = dml.elements['category[]'];
	len = dml.elements['category[]'].length;	
	if(document.getElementById('selectall').checked == true) { 
		for (i = 0; i < len; i++)
		chk[i].checked = true ;
	} else { 
		for (i = 0; i < len; i++)
		chk[i].checked = false ;
	}
}
<?php if(isset($_REQUEST['action']) && $_REQUEST['action']=='addnew'):?>
window.onload = function()
                {
				jQuery('#selectall').attr('checked','checked');
                    displaychk_frm();
                };
<?php endif;?>
</script>
<?php }

/**
* Function: GetCategories_fn_callback
* Filter: wp_ajax_nopriv_XXX and wp_ajax_XXX
* Return: Get all categories for selected post type
*/
add_action('wp_ajax_nopriv_GetCategories_fn','GetCategories_fn_callback');
add_action('wp_ajax_GetCategories_fn','GetCategories_fn_callback');
function GetCategories_fn_callback(){
	global $wpdb,$country_table,$zones_table,$multicity_table;
	if(empty($_REQUEST['post_type']) || $_REQUEST['post_type']==""){
		echo '<ul><li>'.__("Please select any post type.",LM_DOMAIN).'</li></ul>';			
		exit;
	}
	$my_post_type = explode(",",$_REQUEST['post_type']);
	$catid = $_REQUEST['mcatid'];
	$term_icon = $_REQUEST['term_icon'];
	$cprice = $_REQUEST['cprice'];
	$categories='';
	if(isset($_REQUEST['city_id']) && $_REQUEST['city_id']!=''){
		$cityinfo = $wpdb->get_results($wpdb->prepare("select categories  from $multicity_table where city_id =%d",$_REQUEST['city_id'] ));
		$categories=$cityinfo[0]->categories;
	}
	for($c=0 ; $c < count($my_post_type) ; $c ++){
		if($my_post_type[$c] !=''){
			if($c ==0){
			get_location_category_checklist($my_post_type[$c],$categories,$_REQUEST['mod'],'select_all');
			}else{
			get_location_category_checklist($my_post_type[$c],$categories,$_REQUEST['mod'],'');
			}
		}
	}
	exit;
}
add_action('location_selection_option','location_selection_option');
function location_selection_option(){
	global $wpdb,$country_table,$zones_table,$multicity_table;	
	$directory_multicity_location=get_option('directory_multicity_location');
	?>
     <tr class="directory_city_option">
          <th><label><?php _e('Location of the city selection box',LM_DOMAIN);?> </label></th>
          <td><fieldset><label for="location_hoizontal"><input type="radio" id="location_hoizontal" name="directory_multicity_location" value="location_hoizontal" <?php if($directory_multicity_location=='location_hoizontal'){echo 'checked';}?> />&nbsp;<?php _e('Above header (dropdowns)',LM_DOMAIN);?>&nbsp;&nbsp;</label>
          <label for="location_vertical"><input type="radio" id="location_vertical" name="directory_multicity_location" value="location_vertical" <?php if($directory_multicity_location=='location_vertical'){echo 'checked';}?>/>&nbsp;<?php _e('Left edge of the site',LM_DOMAIN);?>&nbsp;&nbsp;</label>
          <label for="location_navigation"><input type="radio" id="location_navigation" name="directory_multicity_location" value="location_navigation" <?php if($directory_multicity_location=='location_navigation'){echo 'checked';}?>/>&nbsp;<?php _e('Above header (lists)',LM_DOMAIN);?>&nbsp;&nbsp;</label></fieldset>
          <p class="description"><?php _e('Shows a location selection box on your site from where your users can select country/state/city.',LM_DOMAIN);?></p>	</td>			
     </tr>     
     <?php 
}
add_action('location_tabs_content','location_manage_locations_tab');
function location_manage_locations_tab($location_tabs='location_manage_locations'){
	switch ($location_tabs):
		case 'location_manage_locations' :
		
		 global $wpdb,$country_table,$zones_table,$multicity_table;	
				 if(isset($_POST['location_submit'])){						
						
						update_option('directory_multicity_location',$_POST['directory_multicity_location']);
						
						
						/*Delete the  multi city post type */						
						$post_content = $wpdb->get_row($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = %s and $wpdb->posts.post_type = %s",'post_city_id','custom_fields'));						
						$post_id=$post_content->ID;
						
						 $total_post_type = get_option('templatic_custom_post');
						 delete_post_meta($post_id, 'post_type');
						 delete_post_meta($post_id, 'post_type_post');
						 delete_post_meta($post_id, 'taxonomy_type_category');
						 foreach($total_post_type as $key=> $_total_post_type)
						 {
							delete_post_meta($post_id, 'post_type_'.$key.'');
						 	delete_post_meta($post_id, 'taxonomy_type_'.$_total_post_type['slugs'][0].'');
						 }
						 
						 if(isset($_POST['location_post_type']) && $_POST['location_post_type']!="")
						 {
							 $post_type = $_POST['location_post_type'];
							 foreach($post_type as $_post_type)
							  {				 
									$post_type_ex = explode(",",$_post_type);																		
									update_post_meta($post_id, 'post_type_'.$post_type_ex[0].'', $post_type_ex[0]);
									update_post_meta($post_id, 'taxonomy_type_'.$post_type_ex[1].'', $post_type_ex[1]);								
									$finpost_type .= $post_type_ex[0].",";								  
							  }
							 update_post_meta($post_id, 'post_type',substr($finpost_type,0,-1));
						 }
						/*Finish the multi cisty post type */
						
						update_option('location_post_type',$_POST['location_post_type']);
						update_option('location_tracking',$_POST['location_tracking']);
						
						update_option('default_city_set',$_POST['default_city_set']);
						$message = __('Record updated successfully.',LM_DOMAIN);
				  }						 
				  
				 $directory_multicity_location=get_option('directory_multicity_location');
				 $default_city_set=get_option('default_city_set');
				 $location_tracking=get_option('location_tracking');
				 
				if(@$message){?>
				<div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px;" >
				  <?php echo $message;?>
				</div>
				<?php }
			if(!isset($_REQUEST['action'])){ /* show option only on city listing page*/	
			?>
			<table class="form-table ">
				<form name="location_settings" id="location_settings" action="" method="post">			
               
               <?php do_action('location_selection_option');?>			
	
				<tr class="directory_city">
					<th><label><?php _e('Homepage Displays',LM_DOMAIN);?></label></th>
					<td><?php $cityinfo = $wpdb->get_results("SELECT cityname FROM $multicity_table where is_default=1");?>                   
						<label for="default_city_set"><input type="radio" onclick="change_default_city_set(this);" id="default_city_set" name="default_city_set" value="default_city" <?php if($default_city_set=='default_city'){echo "checked='checked'";}?>/>&nbsp;<span id="default_city_name"><?php echo __('Default City',LM_DOMAIN).' ('.$cityinfo[0]->cityname.')';?></span></label>&nbsp;&nbsp;
                              <label for="location_tracking_set"><input type="radio" onclick="change_default_city_set(this);" id="location_tracking_set" name="default_city_set" value="location_tracking" <?php if($default_city_set=='location_tracking'){echo "checked='checked'";}?>/>&nbsp;<?php _e('Ask To Show Nearest City',LM_DOMAIN);?></label>&nbsp;&nbsp;
						<label for="nearest_city_set"><input type="radio" onclick="change_default_city_set(this);" id="nearest_city_set" name="default_city_set" value="nearest_city" <?php if($default_city_set=='nearest_city'){echo "checked='checked'";}?>/>&nbsp;<?php _e('Nearest City',LM_DOMAIN);?></label>
						<p class="description" id="default_city_set_msg" <?php if($default_city_set!='default_city'){ echo "style='display:none'"; } ?>><?php _e('When this option is selected all visitors will be taken to the default city',LM_DOMAIN);?></p>
                              <p class="description" id="nearest_city_set_msg" <?php if($default_city_set!='nearest_city'){ echo "style='display:none'"; } ?>><?php _e('When this option is selected all visitors will be taken to the city closest to them (without confirming it first).',LM_DOMAIN);?></p>
						<p  id="location_tracking" class="description" <?php if($default_city_set!='location_tracking'){ echo "style='display:none'"; } ?>><?php _e('Upon visiting the site visitors will be asked whether they want to share their location. If they confirm they will be taken to the nearest city. If they refuse the default city will open.',LM_DOMAIN);?></p>
					</td>
				</tr>               
                    <tr class="location_option">     
					<th><label><?php _e('Activate location management for',LM_DOMAIN);?></label></th>
					<td>
					<div class="locaiton_post_type"> 
						<fieldset class="location_post_type_option">
						<?php
							$location_post_type=get_option('location_post_type');							
							$post_types=get_option('templatic_custom_post');
							?>
							
							<label for="location_post"><input type="checkbox" name="location_post_type[]" value="post,category,post_tag" <?php if(in_array('post,category,post_tag',$location_post_type)){echo 'checked="checked"';}?> id="location_post"  />&nbsp;<?php echo 'Post';?></label><br/>
						<?php foreach($post_types as $key=>$post_type):
								$value=$key.','.implode(',',$post_type['slugs']);
							?>
							<label for="location_<?php echo $key?>"><input type="checkbox" name="location_post_type[]" value="<?php echo $value;?>" <?php if(in_array($value,$location_post_type)){echo 'checked="checked"';}?> id="location_<?php echo $key?>"  />&nbsp;<?php echo $post_type['label'];?></label><br/>
						<?php endforeach;?>
						
						</fieldset>
					</div></td>
					
				</tr>
				
				<tr><td colspan="2">
				<input type="submit" name="location_submit" value="<?php _e('Submit',LM_DOMAIN);?>" class="button-primary"  />
				</td></tr>
				</form>
			</table>
				<?php
				echo '<div class="clearfix"><ul class="subsubsub">';	
					$subtabs=isset($_REQUEST['locations_subtabs'])?$_REQUEST['locations_subtabs']:'';
					location_manage_locations_subtabs($subtabs);
				echo '</ul></div>';
			 } /* end */	
				/*do action for display the manage locations content */
				$subtabs_content=isset($_REQUEST['locations_subtabs'])?$_REQUEST['locations_subtabs']:'countries_manage_locations';
				do_action('manage_location_content',$subtabs_content);	
			break;
	endswitch;
}
/*
 * Function Name: location_manage_locations_subtabs
 * Return : this function use for display the directory location sub tabs content like country, state, and city tabs.
 */
function location_manage_locations_subtabs($current = 'countries_manage_locations'){
	
	
	$tabs = apply_filters('location_manage_locations_subtabs', array( 				
									'countries_manage_locations' => __('Countries',LM_DOMAIN),
									'state_manage_locations' => __('States',LM_DOMAIN),
									'city_manage_locations' => __('Cities',LM_DOMAIN),
									));
	
    $links = array();
	if($current=="")
		$current='countries_manage_locations';	
	$i=1;
	$count=count($tabs);
	foreach( $tabs as $tab => $name ) :
	
		$slashes=($i!=$count)?' | ': '';
		if ( $tab == $current ) :
			$links[] = "<li class='active'><a class='nav-tab-location current' id='".$tab."_pointer' href='?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=$tab'>$name</a> ".$slashes."</li>";
		else :
			$links[] = "<li><a class='nav-tab-location' id='".$tab."_pointer' href='?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=$tab'>$name</a> ".$slashes."</li>";
		endif;
		
		$i++;
	endforeach;
	
	foreach ( $links as $link )
		echo $link;
}
/*
 * Function Name: manage_countries_locations
 * Return: add and display the countries
 */
add_action('manage_location_content','manage_countries_locations');
function manage_countries_locations($location_tabs='countries_manage_locations'){
	
	switch ($location_tabs):
		case 'countries_manage_locations' :
				?>
                    <div class="wrap">                    	
                         <?php if(isset($_REQUEST['action']) && $_REQUEST['action']=='addnew'): ?>
                         
                              <div class="tevo_sub_title"><?php _e('Add a Country',LM_DOMAIN);?>	
                              <a id="country_list" href="<?php echo site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=countries_manage_locations';?>" title="<?php _e('Back to countries list',LM_DOMAIN);?>" name="btnviewlisting" class="add-new-h2" /><?php _e('Back to countries list',LM_DOMAIN); ?></a>
                              </div>
                         	<?php add_edit_countries();?>	
                              
                         <?php elseif(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'):?>	
                         
                         	 <div class="tevo_sub_title"><?php _e('Edit Country',LM_DOMAIN);?>	
                              <a id="country_list" href="<?php echo site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=countries_manage_locations';?>" title="<?php _e('Back to countries list',LM_DOMAIN);?>" name="btnviewlisting" class="add-new-h2" /><?php _e('Back to countries list',LM_DOMAIN); ?></a>
                              </div>
                               <?php add_edit_countries();?>
                               
                         <?php else:// Display countries List?>
                       		 <!--Display the countries list -->
                             <div class="tevo_sub_title"><?php _e('Manage Countries',LM_DOMAIN);?>	
                                   <a id="country_list" href="<?php echo site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=countries_manage_locations&action=addnew';?>" title="<?php _e('Add a field for country',LM_DOMAIN);?>" name="btnviewlisting" class="add-new-h2" /><?php _e('Add new country',LM_DOMAIN); ?></a>
                              </div>
                              <p class="tevolution_desc"><?php _e('Add and manage your country details from this section. To add new country, use above add link.<br> This section helps your user to select between all the countries available on your site.',LM_DOMAIN);?></p>
                                <?php						  
						 
							if(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype']=='add-suc') 
								$message = __('Country created successfully.',LM_DOMAIN);
							elseif(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype'] =='edit-suc')
								$message = __('Country updated successfully.',LM_DOMAIN);
							elseif(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype'] =='dele-suc')
								$message = __('Country deleted  successfully.',LM_DOMAIN);
							elseif(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype'] =='enable-suc')
								$message = __('Country enable successfully.',LM_DOMAIN);
							elseif(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype'] =='disable-suc')
								$message = __('Country disable successfully.',LM_DOMAIN);
                                
                              if(@$message){?>
                              <div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px;" >
                                <?php echo $message;?>
                              </div>
                              <?php }?>
                             <form name="frm_country" id="frm_country" action="" method="post" >
                                   <?php
                                   $directory_country_table = new wp_list_manage_countries();
                                   $directory_country_table->prepare_items();
                                   $directory_country_table->search_box('search', 'search_id');
                                   $directory_country_table->display();
                                   ?>
                                   <input type="hidden" name="check_compare">
                              </form>
                         <?php endif;?>
                    </div>                    
                    <?php
			break;
	endswitch;
}
/*
 * Function Name: manage_state_locations
 * Return: add and display the state
 */
add_action('manage_location_content','manage_state_locations');
function manage_state_locations($location_tabs='state_manage_locations'){
	
	switch ($location_tabs):
		case 'state_manage_locations' :
				?>
                      <div class="wrap">                    	
                          <?php if(isset($_REQUEST['action']) && $_REQUEST['action']=='addnew'): ?>
                         
                              <div class="tevo_sub_title"><?php _e('Add a State',LM_DOMAIN);?>	
                              <a id="country_list" href="<?php echo site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=state_manage_locations';?>" title="<?php _e('Back to states list',LM_DOMAIN);?>" name="btnviewlisting" class="add-new-h2" /><?php _e('Back to states list',LM_DOMAIN); ?></a>
                              </div>
                         	<?php add_edit_zone();?>	
                              
                         <?php elseif(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'):?>	
                         
                         	<div class="tevo_sub_title"><?php _e('Edit State',LM_DOMAIN);?>	
                              <a id="country_list" href="<?php echo site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=state_manage_locations';?>" title="<?php _e('Back to states list',LM_DOMAIN);?>" name="btnviewlisting" class="add-new-h2" /><?php _e('Back to states list',LM_DOMAIN); ?></a>
                              </div>
                               <?php add_edit_zone();?>
                               
                         <?php else:// Display countries List?>
                       		 <!--Display the countries list -->
                              
                              <div class="tevo_sub_title"><?php _e('Manage States',LM_DOMAIN);?>	
                                   <a id="zone_list" href="<?php echo site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=state_manage_locations&action=addnew';?>" title="<?php _e('Add a field for state',LM_DOMAIN);?>" name="btnviewlisting" class="add-new-h2" /><?php _e('Add new State',LM_DOMAIN); ?></a>
                              </div>
                              <p class="tevolution_desc"><?php _e('Add and manage your state details from this section. You can add new states using above add link.<br> These states will be listed in the dropdown according to the country selected by your user.',LM_DOMAIN);?></p>
                                <?php
							if(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype']=='add-suc') 
								$message = __('State created successfully.',LM_DOMAIN);
							elseif(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype'] =='edit-suc')
								$message = __('State updated successfully.',LM_DOMAIN);
							elseif(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype'] =='dele-suc')
								$message = __('State deleted  successfully.',LM_DOMAIN);
                                
                              if(@$message){?>
                              <div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px;" >
                                <?php echo $message;?>
                              </div>
                              <?php }?>
                        
                         <form name="frm_zone" id="frm_zone" action="" method="post" >
						<?php
                              $directory_state_table = new wp_list_manage_states();
                              $directory_state_table->prepare_items();
                              $directory_state_table->search_box('search', 'search_id');
                              $directory_state_table->display();
						?>                             
                         </form>
                         <?php endif;?>
                    </div>     
                    <?php
			break;
	endswitch;
}
/*
 * Function Name: manage_city_locations
 * Return: add and display the city
 */
add_action('manage_location_content','manage_city_locations');
function manage_city_locations($location_tabs='city_manage_locations'){
	
	global $wpdb,$country_table,$zones_table,$multicity_table;
	switch ($location_tabs):
		case 'city_manage_locations' :
				?>
                      <div class="wrap">                    	
                          <?php if(isset($_REQUEST['action']) && $_REQUEST['action']=='addnew'): ?>
                         
                              <div class="tevo_sub_title"><?php _e('Add a city',LM_DOMAIN);?>	
                              <a id="country_list" href="<?php echo site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=city_manage_locations';?>" title="<?php _e('Back to city list',LM_DOMAIN);?>" name="btnviewlisting" class="add-new-h2" /><?php _e('Back to city list',LM_DOMAIN); ?></a>
                              </div>                              
                              <p class="tevolution_desc"><?php _e('Adequate information will lead to accurate results and map. So, please enter all information accordingly.',LM_DOMAIN);?></p>
                         	<?php add_edit_multicity();?>	
                              
                         <?php elseif(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'):?>	
                         
                         	<div class="tevo_sub_title"><?php _e('Edit city',LM_DOMAIN);?>	
                              <a id="country_list" href="<?php echo site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=city_manage_locations';?>" title="<?php _e('Back to city list',LM_DOMAIN);?>" name="btnviewlisting" class="add-new-h2" /><?php _e('Back to city list',LM_DOMAIN); ?></a>
                              </div>
                              <p class="tevolution_desc"><?php _e('For accurate results, please enter the adequate information.',LM_DOMAIN);?></p>
                              
                               <?php add_edit_multicity();?>
                               
                         <?php else:// Display countries List?>
                       		 <!--Display the countries list -->
                              
                              <div class="tevo_sub_title"><?php _e('Manage Cities',LM_DOMAIN);?>	
                                   <a id="country_list" href="<?php echo site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=city_manage_locations&action=addnew';?>" title="<?php _e('Add a field for country',LM_DOMAIN);?>" name="btnviewlisting" class="add-new-h2" /><?php _e('Add new city',LM_DOMAIN); ?></a>
                              </div>
                              <p class="tevolution_desc"><?php _e('<b>Important note:</b> Cities will appear in front-end city selection box only after you add some listings/events to them. <br/><br/>',LM_DOMAIN);?></p>
                                <?php						  
						 
							if(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype']=='add-suc') 
								$message = __('City created successfully. Until you do not create any post for this particular city, it will not appear in a navigation strip for selection.',LM_DOMAIN);
							elseif(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype'] =='edit-suc')
								$message = __('City updated successfully.',LM_DOMAIN);
							elseif(isset($_REQUEST['msgtype']) && $_REQUEST['msgtype'] =='dele-suc')
								$message = __('City deleted  successfully.',LM_DOMAIN);
                               ?>
                         <form name="frm_city" id="frm_city" action="" method="post" >
                         	<input type="hidden" value="<?php echo wp_create_nonce('delete_city');?>" name="_wpnonce"  />
                         	
						<?php
                              $directory_multicitiy_table = new wp_list_manage_multicitiy();
                              $directory_multicitiy_table->prepare_items();
                              $directory_multicitiy_table->search_box('search', 'search_id');
                              $directory_multicitiy_table->display();
                              ?>
                              <input type="hidden" name="check_compare">
                         </form>
                         <?php endif;?>
                    </div>     
                    <?php
			break;
	endswitch;
}
/*========================== WP List table================================= */
/*
 * Manage countries list table 
 */
class wp_list_manage_countries extends WP_List_Table 
{	
	/* fetch all the country data */
	function fetch_countries()
	{
		global $post,$wpdb,$country_table;
		$paged   = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$per_page = $this->get_items_per_page('directory_setting_fields_per_page', 25);				
		if(isset($_POST['s']) && $_POST['s']!=""){
			$sql = "select * from $country_table where country_name ='".$_POST['s']."'";
		}else{
			if(isset($_GET['orderby']) && $_GET['orderby']=='ISO_Code2')
				$order_by='iso_code_2';
			elseif (isset($_GET['orderby']) && $_GET['orderby']=='ISO_Code2')
				$order_by='iso_code_3';
			elseif (isset($_GET['orderby']) && $_GET['orderby']=='is_enable')
				$order_by='is_enable';
			else
				$order_by='country_name';
				
			$order=(isset($_GET['order']))?$_GET['order']:'ASC';
			$sql = "select * from $country_table order by $order_by  $order";
		}	
		
		
		$countryinfo = $wpdb->get_results($sql);		
		
		if($countryinfo)
		{ 
			 foreach($countryinfo as $resobj) :
			 	$flag='';
			 	if($resobj->country_flg)
					$flag='<img src="'.$resobj->country_flg.'" title="'.$resobj->country_name.'">' ;
					
				$url= site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=state_manage_locations&country_id='.$resobj->country_id;	
			 	$country_data[] =  array('ID'           => $resobj->country_id,
									'title'		=> '<a href="'.$url.'">'.$resobj->country_name.'</a>',
									'ISO_Code2'	=> $resobj->iso_code_2,
									'ISO_Code3'	=> $resobj->iso_code_3,
									//'message'		=> $resobj->message,
									'is_enable'	=> $resobj->is_enable,
									'flag'		=> $flag,
									'is_browse'    => '<a href="'.$url.'">Browse States</a>',
								);
			endforeach;
		}
		return $country_data;
	}
	
	
	/* DEFINE THE COLUMNS FOR THE TABLE */
	function get_columns()
	{
		$columns = array('cb' => '<input type="checkbox" />',
					'title' =>  __('Country Name',LM_DOMAIN),
					'flag' =>  __('Country Flag',LM_DOMAIN),
					'ISO_Code2' =>  __('ISO Code2',LM_DOMAIN),
					'ISO_Code3' => __('ISO Code3',LM_DOMAIN),
					//'message' => __('Message',LM_DOMAIN),
					'is_enable' => __('Active',LM_DOMAIN),
					'is_browse' => __('Browse',LM_DOMAIN),
				);
		return $columns;
	}
	
	/*Bulk Action process*/
	function process_bulk_action()
	{ 
		global $wpdb,$country_table,$zones_table,$multicity_table;		
		$cids = $_REQUEST['cf'];		
		if( 'delete' === $this->current_action() )
		{
			foreach( $cids as $cid )
			{	
				if( wp_verify_nonce($_REQUEST['_wpnonce'],'bulk-tevolution_page_location_settings')){
					$wpdb->delete( "$country_table", array( 'country_id' => $cid ), array( '%d' ) );
					$wpdb->delete( "$zones_table", array( 'country_id' => $cid ), array( '%d' ) );
					$wpdb->delete( "$multicity_table", array( 'country_id' => $cid ), array( '%d' ) );
				}else{		
					$redirect_to=site_url().'/wp-admin/admin.php?page=location_settings&msgtype=noncenotverify';
					wp_redirect($redirect_to);
					exit;
				}
			}
			$redirect_to=site_url().'/wp-admin/admin.php?page=location_settings&msgtype=dele-suc';
			wp_redirect($redirect_to);
		}
		if( 'enable' === $this->current_action() )
		{
			foreach( $cids as $cid )
			{	
				$wpdb->update($country_table , array('is_enable' => 1), array('country_id' => $cid) );
			}
			$redirect_to=site_url().'/wp-admin/admin.php?page=location_settings&msgtype=enable-suc';
			wp_redirect($redirect_to);
		}
		if( 'disable' === $this->current_action() )
		{
			foreach( $cids as $cid )
			{
				$wpdb->update($country_table , array('is_enable' => 0), array('country_id' => $cid) );
			}
			$redirect_to=site_url().'/wp-admin/admin.php?page=location_settings&msgtype=disable-suc';
			wp_redirect($redirect_to);
		}
	}
        
	function prepare_items()
	{
		$per_page = $this->get_items_per_page('directory_setting_fields_per_page', 25);
		$columns = $this->get_columns(); /* CALL FUNCTION TO GET THE COLUMNS */
		
		$hidden = array();
		$sortable = array();
		$sortable = $this->get_sortable_columns(); /* GET THE SORTABLE COLUMNS */
		
		$this->_column_headers = array($columns, $hidden, $sortable);		
		$this->process_bulk_action(); /* FUNCTION TO PROCESS THE BULK ACTIONS */		
		$data = $this->fetch_countries(); /* RETIRIVE THE TRANSACTION DATA */
		
		$current_page = $this->get_pagenum(); 
		$total_items = count($data); 
		if(is_array($data))
		$this->found_data = array_slice($data,(($current_page-1)*$per_page),$per_page); 
		$this->items = $this->found_data; 
		
		$this->set_pagination_args( array('total_items' => $total_items,'per_page'    => $per_page) );
	}
	
	/* To avoid the need to create a method for each column there is column_default that will process any column for which no special method is defined */
	function column_default( $item, $column_name )
	{
		switch( $column_name )
		{
			case 'ID':
			case 'title':
			case 'flag':
			case 'ISO_Code3':
			case 'ISO_Code2':
			case 'message':
			case 'is_browse':
			return $item[ $column_name ];
			case 'is_enable':
			return ($item[ $column_name ]==1)?'Yes': 'No';
			default:
			return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}
	
	/* DEFINE THE COLUMNS TO BE SORTED */
	function get_sortable_columns()
	{
		$sortable_columns = array(			
			'title' => array('title',true),
			'ISO_Code2'=>array('ISO_Code2',true),
			'ISO_Code3' => array('ISO_Code3',true),
			'is_enable' => array('is_enable',true),
			);
		return $sortable_columns;
	}
	function column_title($item)
	{
		$delete_url="<a href='?page=".$_REQUEST['page']."&action=delete&cf[]=".$item['ID']."&location_tabs=location_manage_locations&locations_subtabs=countries_manage_locations&_wpnonce=".wp_create_nonce('bulk-tevolution_page_location_settings')."'>Delete</a>";
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&action=%s&cf=%s&%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID'],'location_tabs=location_manage_locations&locations_subtabs=countries_manage_locations'),
			'delete' => $delete_url
			);
		
		return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions , $always_visible = false) );
	}	
	function get_bulk_actions()
	{
		$actions = array('delete' => 'Delete','enable' => 'Enable','disable' => 'Disable');
		return $actions;
	}
	function column_cb($item)
	{ 
		return sprintf('<input type="checkbox" name="cf[]" value="%s" />', $item['ID']);
	}
}
/*
 * Manage state list table 
 */
class wp_list_manage_states extends WP_List_Table 
{	
	/* fetch all the state data */
	function fetch_states()
	{
		global $post,$wpdb,$zones_table,$country_table;
		
		$paged   = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$per_page = $this->get_items_per_page('directory_setting_fields_per_page', 25);		
		if(isset($_POST['s']) && $_POST['s']!=""){
			$sql = "select z.zones_id,z.zone_name,z.zone_code ,c.country_name,c.country_id from $zones_table z ,$country_table c where c.country_id=z.country_id AND zone_name ='".$_POST['s']."'";
		}else{
			if(isset($_GET['orderby']) && $_GET['orderby']=='zone_code')
				$order_by='z.zone_code';
			elseif (isset($_GET['orderby']) && $_GET['orderby']=='country_name')
				$order_by='c.country_name';			
			else
				$order_by='z.zone_name';
				
			$order=(isset($_GET['order']))?$_GET['order']:'ASC';
			
			if(isset($_GET['country_id']) && $_GET['country_id']!='')
				$sql = "select z.zones_id,z.zone_name,z.zone_code ,c.country_name ,c.country_id from $zones_table z ,$country_table c where c.country_id=z.country_id AND z.country_id=".$_GET['country_id']." ORDER BY $order_by  $order ";
			else
				$sql = "select z.zones_id,z.zone_name,z.zone_code ,c.country_name,c.country_id from $zones_table z ,$country_table c where c.country_id=z.country_id ORDER BY $order_by  $order ";
		}	
		$zonesinfo = $wpdb->get_results($sql);
		
		if($zonesinfo)
		{ 
			 foreach($zonesinfo as $resobj) :
			 	$url= site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=city_manage_locations&country_id='.$resobj->country_id.'&zone_id='.$resobj->zones_id;	
			 	$zone_data[] =  array(		
								  'ID'          => $resobj->zones_id,
								  'title'		 => '<a href="'.$url.'">'.$resobj->zone_name.'</a>',
								  'zone_code'	 => $resobj->zone_code,
								  'country_name'=> $resobj->country_name,
								  'is_browse'    => '<a href="'.$url.'">Browse Cities</a>',
								);
			endforeach;
		}		
		return $zone_data;
	}
	/* DEFINE THE COLUMNS FOR THE TABLE */
	function get_columns()
	{
		$columns = array(	
			'cb' => '<input type="checkbox" />',
			'title' =>  __('State Name',LM_DOMAIN),
			'zone_code' =>  __('State Code',LM_DOMAIN),
			'country_name' => __('Country Name',LM_DOMAIN),
			'is_browse' => __('Browse',LM_DOMAIN)
			);
		return $columns;
	}
	function process_bulk_action()
	{ 
		global $wpdb,$country_table,$zones_table,$multicity_table;		
		$cids = $_REQUEST['cf'];		
		if( 'delete' === $this->current_action() )
		{
			foreach( $cids as $cid )
			{					
				if( wp_verify_nonce($_REQUEST['_wpnonce'],'bulk-tevolution_page_location_settings')){
					$wpdb->delete( "$zones_table", array( 'zones_id' => $cid ), array( '%d' ) );
					$wpdb->delete( "$multicity_table", array( 'zones_id' => $cid ), array( '%d' ) );
				}else{		
					$redirect_to=site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=state_manage_locations&msgtype=noncenotverify';
					wp_redirect($redirect_to);
					exit;
				}
			}			
			$redirect_to=site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=state_manage_locations&msgtype=dele-suc';
			wp_redirect($redirect_to);
		}
	}
        
	function prepare_items()
	{
		$per_page = $this->get_items_per_page('directory_setting_fields_per_page', 25);
		$columns = $this->get_columns(); /* CALL FUNCTION TO GET THE COLUMNS */
		
		$hidden = array();
		$sortable = array();
		$sortable = $this->get_sortable_columns(); /* GET THE SORTABLE COLUMNS */
		
		$this->_column_headers = array($columns, $hidden, $sortable);				
		$this->process_bulk_action(); /* FUNCTION TO PROCESS THE BULK ACTIONS */	
		$data = $this->fetch_states(); /* RETIRIVE THE TRANSACTION DATA */
		
		$current_page = $this->get_pagenum(); 
		$total_items = count($data); 
		if(is_array($data))
		$this->found_data = array_slice($data,(($current_page-1)*$per_page),$per_page); 
		$this->items = $this->found_data; 
		
		$this->set_pagination_args( array('total_items' => $total_items,'per_page'=> $per_page) );
	}
	
	/* To avoid the need to create a method for each column there is column_default that will process any column for which no special method is defined */
	function column_default( $item, $column_name )
	{
		switch( $column_name )
		{
			case 'ID':
			case 'title':
			case 'zone_code':
			case 'country_name':
			case 'is_browse':
			return $item[ $column_name ];
			default:
			return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}
	function column_title($item)
	{
		$delete_url="<a href='?page=".$_REQUEST['page']."&action=delete&cf[]=".$item['ID']."&location_tabs=location_manage_locations&locations_subtabs=state_manage_locations&_wpnonce=".wp_create_nonce('bulk-tevolution_page_location_settings')."'>Delete</a>";
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&action=%s&cf=%s&%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID'],'location_tabs=location_manage_locations&locations_subtabs=state_manage_locations'),
			'delete' => $delete_url
			);
		
		return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions , $always_visible = false) );
	}	
	
	/* DEFINE THE COLUMNS TO BE SORTED */
	function get_sortable_columns()
	{
		$sortable_columns = array('title' => array('title',true),'zone_code'=>array('zone_code',true),'country_name' => array('country_name',true));
		return $sortable_columns;
	}
	
	function get_bulk_actions()
	{
		$actions = array('delete' => 'Delete');
		return $actions;
	}
	
	function column_cb($item)
	{ 
		return sprintf('<input type="checkbox" name="cf[]" value="%s" />', $item['ID']);
	}
}
/*
 * Manage multicity list table 
 */
class wp_list_manage_multicitiy extends WP_List_Table 
{
	function total_fetch_multicity(){
		global $post,$wpdb,$zones_table,$country_table,$multicity_table;
		$paged   = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$per_page = $this->get_items_per_page('directory_setting_fields_per_page', 25);		
		if(isset($_POST['s']) && $_POST['s']!=""){
			$sql = "select mc.*,z.zone_name,c.country_name from $multicity_table mc, $zones_table z ,$country_table c where mc.country_id=c.country_id AND mc.zones_id=z.zones_id AND c.country_id=z.country_id AND mc.cityname ='".$_POST['s']."'";
		}else{
			if(isset($_GET['orderby']) && $_GET['orderby']=='zone_name')
				$order_by='z.zone_name';
			elseif (isset($_GET['orderby']) && $_GET['orderby']=='country_name')
				$order_by='c.country_name';	
			elseif (isset($_GET['orderby']) && $_GET['orderby']=='city_code')
				$order_by='mc.city_code';
			elseif (isset($_GET['orderby']) && $_GET['orderby']=='city_id')
				$order_by='mc.city_id';
			else
				$order_by='mc.cityname';
				
			$order=(isset($_GET['order']))?$_GET['order']:'ASC';
			
			if(isset($_GET['country_id']) && $_GET['country_id']!='' && isset($_GET['zone_id']) && $_GET['zone_id']!='')
				$sql = "select count(*) as count from $multicity_table mc, $zones_table z ,$country_table c where mc.country_id=c.country_id AND mc.zones_id=z.zones_id AND c.country_id=z.country_id AND mc.country_id=".$_GET['country_id']." AND mc.zones_id=".$_GET['zone_id']."  ORDER BY $order_by $order ";
			else
				$sql = "select count(*) as count from $multicity_table mc, $zones_table z ,$country_table c where mc.country_id=c.country_id AND mc.zones_id=z.zones_id AND c.country_id=z.country_id ORDER BY $order_by $order ";
		}			
		$multicitiyinfo = $wpdb->get_results($sql);		
		return $multicitiyinfo[0]->count;
		
	}
	
	/* fetch the all multicity list data*/
	function fetch_multicity()
	{
		global $post,$wpdb,$zones_table,$country_table,$multicity_table;
		$paged   = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$per_page = $this->get_items_per_page('directory_setting_fields_per_page', 25);		
		if(isset($_POST['s']) && $_POST['s']!=""){
			$sql = "select mc.*,z.zone_name,c.country_name from $multicity_table mc, $zones_table z ,$country_table c where mc.country_id=c.country_id AND mc.zones_id=z.zones_id AND c.country_id=z.country_id AND mc.cityname ='".$_POST['s']."'";
		}else{
			
			if(isset($_GET['orderby']) && $_GET['orderby']=='zone_name')
				$order_by='z.zone_name';
			elseif (isset($_GET['orderby']) && $_GET['orderby']=='country_name')
				$order_by='c.country_name';	
			elseif (isset($_GET['orderby']) && $_GET['orderby']=='city_code')
				$order_by='mc.city_code';
			elseif (isset($_GET['orderby']) && $_GET['orderby']=='city_id')
				$order_by='mc.city_id';
			else
				$order_by='mc.cityname';
				
			$order=(isset($_GET['order']))?$_GET['order']:'ASC';
			
			if(isset($_GET['country_id']) && $_GET['country_id']!='' && isset($_GET['zone_id']) && $_GET['zone_id']!='')
				$sql = "select mc.*,z.zone_name,c.country_name from $multicity_table mc, $zones_table z ,$country_table c where mc.country_id=c.country_id AND mc.zones_id=z.zones_id AND c.country_id=z.country_id AND mc.country_id=".$_GET['country_id']." AND mc.zones_id=".$_GET['zone_id']."  ORDER BY $order_by $order ";
			else
				$sql = "select mc.*,z.zone_name,c.country_name from $multicity_table mc, $zones_table z ,$country_table c where mc.country_id=c.country_id AND mc.zones_id=z.zones_id AND c.country_id=z.country_id ORDER BY $order_by $order ";
		}
		$paged=((isset($_REQUEST['paged']) && $_REQUEST['paged']!='')? ($_REQUEST['paged']-1) : 0)*$per_page;
		$sql=$sql." LIMIT $paged, $per_page";
		$multicitiyinfo = $wpdb->get_results($sql);
		if($multicitiyinfo)
		{ 
			 foreach($multicitiyinfo as $resobj) :
			 	if($resobj->map_type=='ROADMAP')
					$map_type='Road Map';
				elseif($resobj->map_type=='TERRAIN')
					$map_type='Terrain Map';
				elseif($resobj->map_type=='SATELLITE')
					$map_type='Satellite Map';
				elseif($resobj->map_type=='HYBRID')
					$map_type='Hybrid Map';
				elseif($resobj->map_type=='streetview')
					$map_type='Street View Map';
					
				if($resobj->is_default==1){
					$cityname='<span style="font-weight:bold;" id="city_default_'.$resobj->city_id.'">'.$resobj->cityname.'</span>&nbsp;<span style="color:green;" class="default_city" id="set_default_city_'.$resobj->city_id.'">'.__('Default City',LM_DOMAIN).'</span>';
				}else{
					$cityname= '<span id="city_default_'.$resobj->city_id.'">'.$resobj->cityname.'</span>';
				}
				
			 	$multicity_data[] =  array(		
								  'ID'             => $resobj->city_id,								 
								  'title'		    => $cityname,								  
								  'country_name'   => $resobj->zone_name.', '.$resobj->country_name,
								  'map_type'       => $map_type,
								  'city_post_type' => $resobj->post_type,
								  'message'        => substr($resobj->message,0,50),
								  'scaling_factor' => $resobj->scall_factor,
								  'set_default'    => '<a id="default_city_'.$resobj->city_id.'" '. @$onclick.'>'.$resobj->is_default.'</a>',
								);
			endforeach;
		}			
		return $multicity_data;
	}
	
	/* DEFINE THE COLUMNS FOR THE TABLE */
	function get_columns()
	{
		$columns = array(	
			'cb'             => '<input type="checkbox" />',			
			'title'          => __('City',LM_DOMAIN),			
			'country_name'   => __('Located In',LM_DOMAIN),
			'map_type'       => __('Map Type',LM_DOMAIN),
			'city_post_type' => __('Post Type',LM_DOMAIN),
			'message'        => __('Message',LM_DOMAIN),
			'scaling_factor' => __('Scaling Factor',LM_DOMAIN),			
			);
		return $columns;
	}
	
	function process_bulk_action()
	{ 
		global $wpdb,$country_table,$zones_table,$multicity_table;		
		$cids = @$_REQUEST['cf'];
		if( 'delete' === $this->current_action() )
		{
			foreach( $cids as $cid )
			{		
				if( wp_verify_nonce($_REQUEST['_wpnonce'],'bulk-tevolution_page_location_settings')){					
					$wpdb->delete( "$multicity_table", array( 'city_id' => $cid ), array( '%d' ) );
				}else{		
					$redirect_to=site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=city_manage_locations&msgtype=noncenotverify';
					wp_redirect($redirect_to);		
				}	
			}
			$redirect_to=site_url().'/wp-admin/admin.php?page=location_settings&location_tabs=location_manage_locations&locations_subtabs=city_manage_locations&msgtype=dele-suc';
			wp_redirect($redirect_to);
		}
	}
        
	function prepare_items()
	{
		$per_page = $this->get_items_per_page('directory_setting_fields_per_page', 25);
		$columns = $this->get_columns(); /* CALL FUNCTION TO GET THE COLUMNS */
		
		$hidden = array();
		$sortable = array();
		$sortable = $this->get_sortable_columns(); /* GET THE SORTABLE COLUMNS */
		
		$this->_column_headers = array($columns, $hidden, $sortable);				
		$this->process_bulk_action(); /* FUNCTION TO PROCESS THE BULK ACTIONS */	
		$data = $this->fetch_multicity(); /* RETIRIVE THE TRANSACTION DATA */
		
		$current_page = $this->get_pagenum(); 
		$total_items = $this->total_fetch_multicity(); 
		if(is_array($data))
		$this->found_data = $data; 
		$this->items = $this->found_data; 
		
		$this->set_pagination_args( array('total_items' => $total_items,'per_page'=> $per_page) );
	}
	function column_default( $item, $column_name )
	{
		switch( $column_name )
		{
			case 'ID':			
			case 'title':
			case 'city_code':
			case 'zone_name':
			case 'country_name':
			case 'scaling_factor';
			case 'map_type';
			case 'city_post_type';
			case 'message';			
			return $item[ $column_name ];
			default:
			return $item[ $column_name ]; //Show the whole array for troubleshooting purposes
		}
	}
	function column_title($item)
	{	
		$onclick="onClick=set_default_city(this,'".$item['ID']."')";
		$delete_url="<a href='?page=".$_REQUEST['page']."&action=delete&cf[]=".$item['ID']."&location_tabs=location_manage_locations&locations_subtabs=city_manage_locations&_wpnonce=".wp_create_nonce('bulk-tevolution_page_location_settings')."'>".__('Delete',LM_DOMAIN)."</a>";
		$actions = array(
				'city_id' => __('City ID:',LM_DOMAIN).' '.$item['ID'],
				'edit' => sprintf('<a href="?page=%s&action=%s&cf=%s&%s">'.__('Edit',LM_DOMAIN).'</a>',$_REQUEST['page'],'edit',$item['ID'],'location_tabs=location_manage_locations&locations_subtabs=city_manage_locations'),
				'delete' => $delete_url,
				'set_default' => sprintf('<a href="javascript:void(0);" id="default_city_%s" class="%s" %s>'.__('Set Default',LM_DOMAIN).'</a>',$item['ID'],$item['city_name'],$onclick)
			);
		
		return sprintf('%1$s %2$s', stripslashes($item['title']), $this->row_actions($actions , $always_visible = false) );
	}	
	
	/* DEFINE THE COLUMNS TO BE SORTED */
	function get_sortable_columns()
	{
		$sortable_columns = array(	
			'city_id' => array('city_id',true),
			'title' => array('title',true),
			'city_code' => array('city_code',true),
			'zone_name' => array('zone_name',true),
			'country_name' => array('country_name',true),
			);
		return $sortable_columns;
	}
	
	function get_bulk_actions()
	{
		$actions = array('delete' => 'Delete');
		return $actions;
	}
	function column_cb($item)
	{ 
		return sprintf('<input type="checkbox" name="cf[]" value="%s" />', $item['ID']);
	}
}
/*======================= Add Edit submit form for country, state, and multicity ========================= */
/*
 * Add and edit countries
 *
 */
function add_edit_countries(){
	global $wpdb,$country_table;
	
	/* Check exists country field edit */
	if(isset($_POST['edit_country']) && (isset($_POST['country_id']) && $_POST['country_id']!='')){
		
		/* update country query using update statement */		
		$wpdb->update($country_table , array('country_name' => $_POST['country_name'],'iso_code_2'=>$_POST['iso_code2'],'iso_code_3'=>$_POST['iso_code3'],'message'=>$_POST['country_message'],'country_flg'=>$_POST['country_flag']), array('country_id' => $_POST['country_id']) );
		
		$redirect_to=site_url().'/wp-admin/admin.php?page='.$_POST['redirect_to'].'&msgtype=edit-suc';
		wp_redirect($redirect_to);
	}
	/* Check new country field insert */
	if(isset($_POST['save_country'])){
		/* Insert Into country query using prepare statement */		
		$wpdb->query( $wpdb->prepare("INSERT INTO $country_table ( country_name, iso_code_2, iso_code_3,message, country_flg) VALUES ( %s, %s, %s,%s,%s )", $_POST['country_name'], $_POST['iso_code2'], $_POST['iso_code3'],$_POST['country_message'],$_POST['country_flag'] ) );		
		$redirect_to=site_url().'/wp-admin/admin.php?page='.$_POST['redirect_to'].'&msgtype=add-suc';
		wp_redirect($redirect_to);
	}
	$submit='save_country';
	?>
     <form action="" method="post">     	
     	<?php
		if((isset($_REQUEST['action']) && $_REQUEST['action']=='edit') && (isset($_REQUEST['cf']) && $_REQUEST['cf']!=''))
		{			
			$countryinfo = $wpdb->get_results($wpdb->prepare("select * from $country_table where country_id =%d",$_REQUEST['cf']));			
			?>
               <input type="hidden" name="country_id" value="<?php echo ($countryinfo[0]->country_id)? $countryinfo[0]->country_id: '';?>" />
               <?php
			$submit="edit_country";
		}		
		?>
          <input type="hidden" name="redirect_to" value="location_settings&location_tabs=location_manage_locations&locations_subtabs=countries_manage_locations" />
    		
     	<table class="form-table" width="70%" cellspacing="1" cellpadding="4" border="0">
          	<tbody>
               	<tr>
                    	<th><?php _e('Country Name',LM_DOMAIN);?></th>
                         <td>
                         	<input type="text" name="country_name" value="<?php echo ($countryinfo[0]->country_name)? $countryinfo[0]->country_name: '';?>" />
                         	<p class="description"><?php _e('Write the country name.',LM_DOMAIN);?></p>
                         </td>
                    </tr>
                    <tr>
                    	<th><?php _e('ISO Code 2',LM_DOMAIN);?></th>
                         <td>
                         	<input type="text" name="iso_code2" value="<?php echo ($countryinfo[0]->iso_code_2)? $countryinfo[0]->iso_code_2: '';?>" />
                         	<p class="description"><?php _e('Write the two letter country code here <br>(e.g. "US" for United States of America. You can find these codes from <a href ="http://en.wikipedia.org/wiki/ISO_3166-1#Current_codes" target="_blank"> here </a> )',LM_DOMAIN);?></p>
                         </td>
                    </tr>
                    <tr>
                    	<th><?php _e('ISO Code 3',LM_DOMAIN);?></th>
                         <td>
                         	<input type="text" name="iso_code3" value="<?php echo ($countryinfo[0]->iso_code_3)? $countryinfo[0]->iso_code_3: '';?>" />
                         	<p class="description"><?php _e('Write the three letter country code here <br>(e.g. "USA" for United States of America. You can find these codes from <a href ="http://en.wikipedia.org/wiki/ISO_3166-1#Current_codes" target="_blank"> here </a> )',LM_DOMAIN);?></p>
                         </td>
                    </tr>
                   <!--<tr>
                    	<th><?php _e('Message',LM_DOMAIN);?></th>
                         <td>
                         	<textarea name="country_message" cols="60" rows="5"><?php echo ($countryinfo[0]->message)?$countryinfo[0]->message:'';?></textarea>
                         	<p class="tevolution_desc"><?php _e('Write the country message here to display it on your site.',LM_DOMAIN);?></p>
                         </td>
                    </tr>-->
                     <tr>
                    	<th><?php echo __('Country flag',LM_DOMAIN);?></th>
                         <td>
                         	<input id="country_flag_icon" type="text" size="60" name="country_flag" value="<?php echo ($countryinfo[0]->country_flg)?$countryinfo[0]->country_flg:'';?>" />	
                             <?php _e('Or',LM_DOMAIN);?>
                             <a class="button upload_button" title="Add country flag icon" id="country_flag_icon" data-editor="country_flag_icon" href="#">
                             <span class="wp-media-buttons-icon"></span><?php _e('Browse',LM_DOMAIN);?>	</a>				
                              
                              <p class="description"><?php _e('Upload flag image for above mentioned country. It will appear on your site along with the country name.',LM_DOMAIN);?></p>
                         </td>
                    </tr>
                    <tr id="save_coupon">
                         <td colspan="2">
                         	<input id="save" class="button-primary" type="submit" value="<?php _e('Save all changes',LM_DOMAIN); ?>"  name="<?php echo $submit;?>">
                         </td>
                    </tr>
               </tbody>
          </table>
     </form>
     <?php
}
/*
 * Add and edit zone
 *
 */
function add_edit_zone(){
	global $wpdb,$country_table,$zones_table;
	
	/* Check exists country field edit */
	if(isset($_POST['edit_zone']) && (isset($_POST['country_id']) && $_POST['country_id']!='')){
		/* update country query using update statement */		
		$wpdb->update($zones_table , array('country_id' => $_POST['country_id'],'zone_code'=>$_POST['zone_code'],'zone_name'=>$_POST['zone_name']), array('zones_id' => $_POST['zone_id']) );
		
		$redirect_to=site_url().'/wp-admin/admin.php?page='.$_POST['redirect_to'].'&msgtype=edit-suc';
		wp_redirect($redirect_to);
	}
	/* Check new country field insert */
	if(isset($_POST['save_zone'])){		
		/* Insert Into zone query using prepare statement */		
		$wpdb->query( $wpdb->prepare("INSERT INTO $zones_table ( country_id, zone_code, zone_name) VALUES ( %d, %s, %s)", $_POST['country_id'], $_POST['zone_code'], $_POST['zone_name']) );	
		
		$redirect_to=site_url().'/wp-admin/admin.php?page='.$_POST['redirect_to'].'&msgtype=add-suc';
		wp_redirect($redirect_to);
	}
	
	$submit='save_zone';
	?>
     <form action="" method="post">     	
     	<?php
		if((isset($_REQUEST['action']) && $_REQUEST['action']=='edit') && (isset($_REQUEST['cf']) && $_REQUEST['cf']!=''))
		{
			$zoneinfo = $wpdb->get_results($wpdb->prepare("select * from $zones_table where zones_id =%d",$_REQUEST['cf'] ));
			?>
               <input type="hidden" name="zone_id" value="<?php echo ($zoneinfo[0]->zones_id)? $zoneinfo[0]->zones_id: '';?>" />
               <?php
			$submit="edit_zone";
		}		
		?>
          <input type="hidden" name="redirect_to" value="location_settings&location_tabs=location_manage_locations&locations_subtabs=state_manage_locations" />
    		
     	<table class="form-table" width="70%" cellspacing="1" cellpadding="4" border="0">
          	<tbody>
               	<tr>
                    	<th><?php _e('Country',LM_DOMAIN);?></th>
                         <td>
                         	<?php $countryinfo = $wpdb->get_results("SELECT * FROM $country_table order by country_name ASC");?>
                              	<select name="country_id" >
								<option value=""><?php _e('Select Country Name',LM_DOMAIN);?></option>
                              <?php foreach($countryinfo as $country): $selected=($country->country_id==$zoneinfo[0]->country_id)? 'selected':'';?>
							<option value="<?php echo $country->country_id?>" <?php echo $selected;?>><?php echo $country->country_name;?></option>
						<?php endforeach; ?>
                              </select>
                         
                         </td>
                    </tr>
                    <tr>
                    	<th><?php _e('State Name',LM_DOMAIN);?></th>
                         <td><input type="text" name="zone_name" value="<?php echo ($zoneinfo[0]->zone_name)? $zoneinfo[0]->zone_name: '';?>" />
                          <p class="description"><?php _e('Write the state name that falls in the above selected country',LM_DOMAIN);?></p>
                          </td>
                    </tr>
                    <tr>
                    	<th><?php _e('State Code',LM_DOMAIN);?></th>
                         <td><input type="text" name="zone_code" value="<?php echo ($zoneinfo[0]->zone_code)? $zoneinfo[0]->zone_code: '';?>" />
                        <p class="description"><?php _e('Write the zone code of the above mentioned state',LM_DOMAIN);?></p>
                         </td>
                    </tr>
                    
                    <tr id="save_coupon">
                         <td colspan="2">
                         	<input id="save" class="button-primary" type="submit" value="<?php _e('Save all changes',LM_DOMAIN); ?>"  name="<?php echo $submit;?>">
                         </td>
                    </tr>
               </tbody>
          </table>
     </form>
     <?php
}
/*
 * Add or Edit multicity
 *
 */
 
function add_edit_multicity(){
	global $wpdb,$country_table,$zones_table,$multicity_table;
	
	/* Check exists city field edit */
	//print_r($_POST['category']); exit;
	if(isset($_POST['edit_city']) && (isset($_POST['country_id']) && $_POST['country_id']!='')){
		$city_post_type=implode(',',$_POST['city_post_type']);
		$categories=implode(',',$_POST['category']);	
		$city_slug=str_replace(' ','-',strtolower($_POST['city_name']));
		
		$wpdb->update($multicity_table , array('country_id' => $_POST['country_id'],'zones_id'=>$_POST['zones_id'],'cityname'=> stripslashes($_POST['city_name']),'city_slug'=>$city_slug,'lat'=>$_POST['geo_latitude'],'lng'=>$_POST['geo_longitude'],'scall_factor'=>$_POST['scaling_factor'],'is_zoom_home'=>$_POST['set_zooming_opt'],'map_type'=>$_POST['map_type'],'post_type'=>$city_post_type,'categories'=>$categories,'message'=>stripslashes($_POST['city_message']),'color'=>$_POST['background_city_colour'],'images'=>$_POST['city_image'],'header_color'=>$_POST['header_background_city_colour'],'header_image'=>$_POST['header_city_image']), array('city_id' => $_POST['city_id']) );
		/*City String Name change using wpml  */
		if (function_exists('icl_register_string')) {			
			icl_register_string('location-manager', 'location_city_'.$city_slug,$_POST['city_name']);		
		}
		
		$redirect_to=site_url().'/wp-admin/admin.php?page='.$_POST['redirect_to'].'&msgtype=edit-suc';
		wp_redirect($redirect_to);
	}
	/* Check new city field insert */
	if(isset($_POST['save_city'])){		
			$city_post_type=implode(',',$_POST['city_post_type']);
			$categories=implode(',',$_POST['category']);	
			$city_slug=str_replace(' ','-',strtolower($_POST['city_name']));		
		
		/* Insert Into zone query using prepare statement */			
		$wpdb->query( $wpdb->prepare("INSERT INTO $multicity_table ( country_id,zones_id,cityname,city_slug,lat,lng,scall_factor,is_zoom_home,map_type,post_type,categories,message,color,images,header_color,header_image) VALUES ( %d, %d, %s, %s, %s, %s, %d, %s, %s, %s, %s, %s, %s,%s,%s,%s)", $_POST['country_id'], $_POST['zones_id'],  stripslashes($_POST['city_name']),$city_slug,$_POST['geo_latitude'],$_POST['geo_longitude'],$_POST['scaling_factor'], $_POST['set_zooming_opt'], $_POST['map_type'], $city_post_type, $categories, stripslashes($_POST['city_message']), $_POST['background_city_colour'], $_POST['city_image'],$_POST['header_background_city_colour'],$_POST['header_city_image']   ) );	
		
		/*City String Name change using wpml  */
		if (function_exists('icl_register_string')) {			
			icl_register_string('location-manager', 'location_city_'.$city_slug,$_POST['city_name']);		
		}
		
		$redirect_to=site_url().'/wp-admin/admin.php?page='.$_POST['redirect_to'].'&msgtype=add-suc';
		wp_redirect($redirect_to);
	}
	$submit='save_city';
	?>
     <form action="" method="post" onsubmit="return manage_city_validation();" name="price_frm">     	
     	<?php
		if((isset($_REQUEST['action']) && $_REQUEST['action']=='edit') && (isset($_REQUEST['cf']) && $_REQUEST['cf']!=''))
		{
			
			$cityinfo = $wpdb->get_results($wpdb->prepare("select * from $multicity_table where city_id =%d",$_REQUEST['cf'] ));
			?>
               <input type="hidden" name="city_id" value="<?php echo ($cityinfo[0]->city_id)? $cityinfo[0]->city_id: '';?>" />
               <?php
			$submit="edit_city";
			$zonesinfo = $wpdb->get_results($wpdb->prepare("select * from $zones_table where country_id =%d",$cityinfo[0]->country_id ));
		}			
		?>
          <input type="hidden" name="redirect_to" value="location_settings&location_tabs=location_manage_locations&locations_subtabs=city_manage_locations" />
    		
     	<table class="form-table" width="70%" cellspacing="1" cellpadding="4" border="0">
          	<tbody>
               	<tr id="admin_country_id">
                    	<th><?php _e('Country',LM_DOMAIN);?><span class="required">*</span></th>
                         <td>
                              <?php $countryinfo = $wpdb->get_results($wpdb->prepare("SELECT * FROM $country_table where is_enable=%d order by country_name ASC",1 ));?>
                              	<select name="country_id" id="country_id" onchange="fill_zones_cmb(this,'');">
								<option value=""><?php _e('Select Country',LM_DOMAIN);?></option>
                              <?php foreach($countryinfo as $country): $selected=($country->country_id==$cityinfo[0]->country_id)? 'selected':'';?>
							<option value="<?php echo $country->country_id?>" <?php echo $selected;?>><?php echo $country->country_name;?></option>
						<?php endforeach; ?>
                              </select>                         
                         </td>
                    </tr>
                    <tr id="admin_zones_id">
                    	<th><?php _e('State/region',LM_DOMAIN);?><span class="required">*</span></th>
                         <td>                         	
                             	<select name="zones_id"  id="zones_id">
								<option value=""><?php _e('Select state',LM_DOMAIN);?></option>
                              <?php if($zonesinfo):
								foreach($zonesinfo as $zone): $selected=($zone->zones_id==$cityinfo[0]->zones_id)? 'selected':'';?>
									<option value="<?php echo $zone->zones_id?>" <?php echo $selected;?>><?php echo $zone->zone_name;?></option>
						<?php 	endforeach;
							 endif;?>
                              </select>
                              <span id="process_state" style="display:none;"><img src="<?php echo TEVOLUTION_LOCATION_URL.'images/process.gif'?> " height="16" width="16"  /></span>
                         
                         </td>
                    </tr>
                     <tr>
                    	<th><?php _e('Map type',LM_DOMAIN);?></th>
                         <td>
                              <input type="radio" id="roadmap" name="map_type" value="ROADMAP" <?php if( @$cityinfo[0]->map_type == 'ROADMAP' || @$cityinfo[0]->map_type == ''){?>checked="checked"<?php }?> /> <label for="roadmap"> <?php _e('  Road Map',LM_DOMAIN);?></label>
                              	<input type="radio" id="terrain" name="map_type" <?php if( @$cityinfo[0]->map_type == 'TERRAIN'){?> checked="checked"<?php }?> value="TERRAIN" /> <label for="terrain"><?php _e('Terrain Map',LM_DOMAIN);?></label> 
           &nbsp;
                              	<input type="radio" id="satellite" name="map_type" <?php if( @$cityinfo[0]->map_type == 'SATELLITE'){?> checked="checked"<?php }?> value="SATELLITE" /> <label for="satellite"><?php _e('Satellite Map',LM_DOMAIN);?></label> 
                              &nbsp;
                              	<input type="radio" id="hybrid" name="map_type" <?php if( @$cityinfo[0]->map_type == 'HYBRID'){?> checked="checked"<?php }?> value="HYBRID" /> <label for="hybrid"><?php _e('Hybrid  Map',LM_DOMAIN);?></label>   
                              
                         	<p class="description"><?php _e('Select any of the above type for your map',LM_DOMAIN);?></p>
                         	
                         </td>
                    </tr>
                    <tr>
                    	<th><?php _e('Map scaling factor',LM_DOMAIN);?></th>
                         <td>
							<select name="scaling_factor">
								<?php for($sf=1; $sf < 20 ; $sf++){ ?>
									<?php if($cityinfo[0]->scall_factor !=''){ $sf1=$cityinfo[0]->scall_factor; }else{ if($sf == 13) { $sf1 = '13'; }else{ $sf1=''; }  }
									if($sf == $sf1){ $sel ="selected=selected"; }else{ $sel =''; }
									?>
									<option value="<?php echo $sf; ?>" <?php echo $sel; ?>><?php echo $sf; ?></option>
								<?php } ?>							
							</select>
                         	<p class="description"><?php _e('Define the zoom level of the map here.<br/> Its Min. value is 1 and Max. Value is 19. The recommended level is 13',LM_DOMAIN);?></p>
                         </td>
                    </tr>
                    <tr id="admin_city_name">
                    	<th><?php _e('City Name',LM_DOMAIN);?><span class="required">*</span></th>
                         <td>
                         	<div style="width:25%; float:left;">
                         	<input type="text"  id="address" class="pt_input_text" name="city_name" value="<?php echo ( @$cityinfo[0]->cityname)? stripslashes($cityinfo[0]->cityname): '';?>" />
                         	<input type="hidden" name="geo_address" id="geo_address" value="<?php echo stripslashes($cityinfo[0]->cityname);?>" /> 
                              <input type="button" class="btn_input_normal btn_spacer button" value="<?php _e('Set Address on Map',LM_DOMAIN);?>" onclick="geocode();initialize();" />
                              </div>
                             
                         </td>
                    </tr>
                    <tr>
                    	<td colspan="2">
                              <div class="option option-select"  >     		
                              <?php 
						$zooming_factor=( @$cityinfo[0]->scall_factor)? $cityinfo[0]->scall_factor: '13';
						$map_lat=( @$cityinfo[0]->lat)? $cityinfo[0]->lat: '40.714623';
						$map_lng=( @$cityinfo[0]->lng)? $cityinfo[0]->lng: '-74.006605';
						include_once(TEVOLUTION_LOCATION_DIR . "functions/map/locations_map.php"); ?>                              
                              </div>
                         </td>
                    </tr>
                    <tr>
                    	<th><?php _e('City latitude',LM_DOMAIN);?></th>
                         <td><input type="text"  onblur="changeMap();" class="textfield" id="geo_latitude"  name="geo_latitude" value="<?php echo ( @$cityinfo[0]->lat)? $cityinfo[0]->lat: '';?>" /></td>
                    </tr>
                    <tr>
                    	<th><?php _e('City longitude',LM_DOMAIN);?></th>
                         <td><input type="text" onblur="changeMap();" class="textfield" id="geo_longitude" name="geo_longitude" value="<?php echo ( @$cityinfo[0]->lng)? $cityinfo[0]->lng: '';?>" /></td>
                    </tr>                    
                    <tr>
                    	<th><?php _e('Map display',LM_DOMAIN);?></th>
                         <td>
                              
                              <input type="radio" id="set_zooming_opt" name="set_zooming_opt" value="0" <?php if( @$cityinfo[0]->is_zoom_home == '0' || @$cityinfo[0]->is_zoom_home == ''){?>checked="checked"<?php }?> /> <label for="set_zooming_opt"> <?php _e('According to Map Scaling factor',LM_DOMAIN);?></label>
                              &nbsp;
                              	<input type="radio" id="set_zooming_opt1" name="set_zooming_opt" <?php if( @$cityinfo[0]->is_zoom_home == '1'){?> checked="checked"<?php }?> value="1" /> <label for="set_zooming_opt1"><?php _e('Fit all available listings',LM_DOMAIN);?></label>                              	
                         	<p class="description"><?php _e('Select whether you want to display map as per Map scaling factor you have set above or adjust the scaling factor<br/> automatically so that all available listings appear on the map.',LM_DOMAIN);?></p>
                         </td>
                    </tr>
                    <tr id="admin_post_type">
                    	<th><?php _e('Post Type',LM_DOMAIN);?><span class="required">*</span></th>
                           <td>
                         	<?php						
						$location_post_type=implode(',',get_option('location_post_type'));
						$post_types = get_option("templatic_custom_post");	
						$city_post_type=( @$cityinfo[0]->post_type!='')? explode(',',$cityinfo[0]->post_type):'';
						foreach($post_types as $key=>$post_type):
							$checked=((!empty($city_post_type)) && in_array($key,$city_post_type))?'checked':'';							
							$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $key,'public'   => true, '_builtin' => true ));
							if($key !='' && strpos($location_post_type,$key) !== false){
							?>
							<div class="input_wrap">
                                   	<input type="checkbox" class="checkbox_list" id="<?php echo $key;?>" name="city_post_type[]" value="<?php echo $key;?>" <?php echo $checked?> onclick="get_categories_checklist('<?php echo $key; ?>','<?php echo ($cityinfo[0]->city_id)? $cityinfo[0]->city_id: '';?>');" />&nbsp;&nbsp;<label for="<?php echo $key;?>"><?php echo $post_type['label'];?></label>
                                   </div>
                                   
						<?php } endforeach;?>
                              <p class="description"><?php _e('Selected post type will be displayed on home page map. <br> <b>Note:</b> Make sure about its category selection in the below given &quot;Categories&quot; option',LM_DOMAIN);?></p>
                         </td>
                    </tr>               
				</tr>
                    <tr id="admin_post_type">
                    	<th><?php _e('Categories',LM_DOMAIN);?><span class="required">*</span></th>
                         <td>
						 <div class="element wp-tab-panel" id="field_category" style="height:120px;overflow-y: scroll; margin-bottom:5px;">
							 <?php 
							 	$post_types = get_option("templatic_custom_post");
								$categories=( @$cityinfo[0]->categories!='')? $cityinfo[0]->categories:'';								
								$c=0;
								if(!empty($city_post_type)){
								foreach($post_types as $key=>$post_type):
									if(in_array($key,$city_post_type)){
										if($c == 0){
											echo get_location_category_checklist($key,$categories,'','select_all');
										}else{ 
											echo get_location_category_checklist($key,$categories,'','');
										}
										$c++;
									}
								endforeach;
								}else{
									foreach($post_types as $key=>$post_type):									
										if($c == 0){
											echo get_location_category_checklist($key,$categories,'','select_all');
										}else{ 
											echo get_location_category_checklist($key,$categories,'','');
										}
										$c++;									
									endforeach;
								}
							?>  
						  </div>
						  <span id='process' style='display:none;'><img src="<?php echo TEVOLUTION_LOCATION_URL.'images/process.gif'?>" alt='Processing..' height="16" width="16"  /></span>
						  <p class="description"><?php _e('Select the categories for which this field should appear in on the place or event submission form.',LM_DOMAIN);?></p>
                         </td>
                    </tr>
                    <tr>
                    	<th><?php _e('City Message',LM_DOMAIN);?></th>
                         <td>
                         	<textarea name="city_message" cols="60" rows="5"><?php echo ( @$cityinfo[0]->message)?$cityinfo[0]->message:'';?></textarea>
                              <p class="description"><?php _e('Write a description of the city that you would like to display on your site.',LM_DOMAIN);?></p>
                         </td>
                    </tr>
                    <tr>
                    	<th><?php _e('Background Color',LM_DOMAIN);?></th>
                         <td>   
                         	<script type="text/javascript">
						/*BEING City background color picker */
						jQuery(document).ready(function($){
							jQuery('#background_city_colour').farbtastic('#city_color');
						});
						</script>
                              <input type="text" name="background_city_colour" onclick="show_colorpicker(this.id);" id="city_color" value="<?php echo ( @$cityinfo[0]->color)?$cityinfo[0]->color:'#';?>" >
                              <img style="position:relative;vertical-align:middle;" src="<?php echo TEVOLUTION_LOCATION_URL; ?>images/Color_block.png" />
                              <div id="background_city_colour"  name="city_color" style="display:none" ></div>
                              <div class="clearfix"></div>
                              <p class="description"><?php _e('choose a background color for this city. <br> <b>Tip:</b>You can define a color that represents the particular city to add an advantage to recognize the cities on your site.',LM_DOMAIN);?></p>
                         </td>
                    </tr>
                    <tr>
                    	<th><?php _e('Background Image',LM_DOMAIN);?></th>
                         <td>                          	
                             <input id="city_upload_icon" type="text" size="60" name="city_image" value="<?php echo ( @$cityinfo[0]->images)?$cityinfo[0]->images:'';?>" />	
                             <?php _e('Or',LM_DOMAIN);?>
                             <a class="button upload_button" title="Add city background image" id="city_upload_icon" data-editor="city_upload_icon" href="#">
                             <span class="wp-media-buttons-icon"></span><?php _e('Browse',LM_DOMAIN);?>	</a>&nbsp; <?php _e('<span style="color:red; font-weight:bold;">NOTE:</span> Insert the image into post after uploading it.','templatic'); ?>			
                              
                              <p class="description"><?php _e('Upload image to display it as a background image for this city. Ideal width size for it is <b>1900 pixels</b>.',LM_DOMAIN);?></p>                              
                         </td>
                    </tr>
                    
                    <tr>
                    	<th><?php _e('Header Background Color',LM_DOMAIN);?></th>
                         <td>   
                         	<script type="text/javascript">
						/*BEING City background color picker */
						jQuery(document).ready(function($){
							jQuery('#header_background_city_colour').farbtastic('#header_city_color');
						});
						</script>
                              <input type="text" name="header_background_city_colour" onclick="show_colorpicker(this.id);" id="header_city_color" value="<?php echo ( @$cityinfo[0]->header_color)?$cityinfo[0]->header_color:'#';?>" >
                              <img style="position:relative;vertical-align:middle;" src="<?php echo TEVOLUTION_LOCATION_URL; ?>images/Color_block.png" />
                              <div id="header_background_city_colour"  name="header_city_color" style="display:none" ></div>
                              <div class="clearfix"></div>
                              <p class="description"><?php _e('choose a header background color for this city. <br> <b>Tip:</b>You can define a color that represents the particular city to add an advantage to recognize the cities on your site.',LM_DOMAIN);?></p>
                         </td>
                    </tr>
                    <tr>
                    	<th><?php _e('Header Upload Image',LM_DOMAIN);?></th>
                         <td>                          	
                             <input id="header_city_upload_icon" type="text" size="60" name="header_city_image" value="<?php echo ( @$cityinfo[0]->header_image)?$cityinfo[0]->header_image:'';?>" />	
                             <?php _e('Or',LM_DOMAIN);?>
                             <a class="button upload_button" title="Add city header background image" id="header_city_upload_icon" data-editor="city_upload_icon" href="#">
                             <span class="wp-media-buttons-icon"></span><?php _e('Browse',LM_DOMAIN);?>	</a>&nbsp; <?php _e('<span style="color:red; font-weight:bold;">NOTE:</span> Insert the image into post after uploading it.','templatic'); ?>			
                              
                              <p class="description"><?php _e('Upload image to display it as a header background image for this city.Ideal width size for it is 1240 pixels and keep the height upto 240px.',LM_DOMAIN);?></p>                              
                         </td>
                    </tr>
                    <tr id="save_coupon">
                         <td colspan="2">
                         	<input id="save" class="button-primary" type="submit" value="<?php _e('Save all changes',LM_DOMAIN); ?>"  name="<?php echo $submit;?>">
                              <p class="description"><?php _e('',LM_DOMAIN);?></p>
                         </td>
                    </tr>
               </tbody>
          </table>
     </form>
     <?php
}
/*=============================================================================================================================================== */
/*
 * Function Name: location_set_default_city
 * Return: fill the default city
 */
add_action('wp_ajax_nopriv_default_city','location_set_default_city');
add_action('wp_ajax_default_city','location_set_default_city');
function location_set_default_city()
{
	global $wpdb,$country_table,$zones_table,$multicity_table;	
	$default_city_id = $wpdb->get_results($wpdb->prepare("SELECT city_id FROM $multicity_table where is_default=%d",1));		
	$last_default_city=$default_city_id[0]->city_id;	
	$wpdb->update($multicity_table , array('is_default' => 0), array('is_default' => 1) );
	$wpdb->update($multicity_table , array('is_default' => 1), array('city_id' => $_POST['city_id']) );	
	echo $last_default_city;
	exit;
}
/*
 * Wp_ajax action call for fill the state name according country when new city insert
 * Function Name: location_fill_states_cmb
 * Return: fill the state drop down box
 */
add_action('wp_ajax_nopriv_fill_states_cmb','location_fill_states_cmb');
add_action('wp_ajax_fill_states_cmb','location_fill_states_cmb');
function location_fill_states_cmb()
{
	global $wpdb,$country_table,$zones_table,$multicity_table;
	if(isset($_REQUEST['front']) && $_REQUEST['front']==1){
		$zonesinfo = $wpdb->get_results($wpdb->prepare("SELECT distinct z.zones_id, z.* FROM $zones_table z,$country_table c,$multicity_table mc where mc.zones_id=z.zones_id AND z.country_id=c.country_id AND c.is_enable=%d AND z.country_id =%d order by zone_name ASC",1,$_REQUEST['country_id']));		
	}
	else{
		$zonesinfo = $wpdb->get_results($wpdb->prepare("SELECT distinct z.zones_id, z.* FROM $zones_table z,$country_table c where z.country_id=c.country_id AND c.is_enable=%d AND z.country_id =%d order by zone_name ASC",1,$_REQUEST['country_id']));		
	}	
	$zones_ontion.='<option value="">'.__('Select state',LM_DOMAIN).'</option>';
	if($zonesinfo):		
		foreach($zonesinfo as $zone):
		 $zone_name=$zone->zone_name;
		 if (function_exists('icl_register_string')) {									
				icl_register_string('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
				$zone_name = icl_t('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
		  }	
		
			$zones_ontion.='<option value="'.$zone->zones_id.'" >'.$zone_name.'</option>';
		endforeach;
	else:
		$zones_ontion='<option value="">'.__('States not available',LM_DOMAIN).'</option>';
	endif;
	
	if(isset($_REQUEST['header']) && $_REQUEST['header']==1){
		$zones_ontion.='++<option value="">'.__('Select city',LM_DOMAIN).'</option>';	
	}
	echo $zones_ontion;
	exit;
}
/*
 * Wp_ajax action call for fill the state name according country when new city insert
 * Function Name: location_fill_cities_cmb
 * Return: Fill the city drop downbox
 */
add_action('wp_ajax_nopriv_fill_city_cmb','location_fill_cities_cmb');
add_action('wp_ajax_fill_city_cmb','location_fill_cities_cmb');
function location_fill_cities_cmb()
{
	global $wpdb,$country_table,$zones_table,$multicity_table;	
	if(isset($_REQUEST['front']) && $_REQUEST['front']==1){			
		$cityinfo = $wpdb->get_results($wpdb->prepare("SELECT distinct c.city_id,c.* FROM $multicity_table c,{$wpdb->prefix}postmeta m where m.meta_key =%s and FIND_IN_SET(c.city_id , m.meta_value ) AND zones_id =%d order by cityname  ASC",'post_city_id',$_REQUEST['state_id']));
	}else{
		$cityinfo = $wpdb->get_results($wpdb->prepare("SELECT * FROM $multicity_table where zones_id =%d order by cityname  ASC",$_REQUEST['state_id']));
	}
	$city_ontion.='<option value="">'.__('Select city',LM_DOMAIN).'</option>';
	if($cityinfo):		
		foreach($cityinfo as $city):
		   $cityname=$city->cityname;		   
		   if (function_exists('icl_register_string')) {			   		
				icl_register_string('location-manager', 'location_city_'.$city->city_slug,$cityname);
				$cityname = icl_t('location-manager', 'location_city_'.$city->city_slug,$cityname);
		   }		
		   $city_ontion.='<option value="'.$city->city_id.'" >'.$cityname.'</option>';
		endforeach;
	else:
		$city_ontion='<option value="">'.__('City not available',LM_DOMAIN).'</option>';
	endif;
	echo $city_ontion;
	exit;
}
/*
 * Function Name: location_fill_multicity_cmb
 * Return: Fill the multicity drop downbox
 */
add_action('wp_ajax_nopriv_fill_multicity_cmb','location_fill_multicity_cmb');
add_action('wp_ajax_fill_multicity_cmb','location_fill_multicity_cmb');
function location_fill_multicity_cmb(){
	global $wpdb,$country_table,$zones_table,$multicity_table;			
	$cityinfo = $wpdb->get_results($wpdb->prepare("SELECT * FROM $multicity_table where country_id =%d order by cityname  ASC",$_REQUEST['country_id']));	
	if($cityinfo):		
		foreach($cityinfo as $city):
			$city_ontion.='<option value="'.$city->city_id.'" >'.$city->cityname.'</option>';
		endforeach;
	else:
		$city_ontion='<option value="">'.__('City not available',LM_DOMAIN).'</option>';
	endif;
	echo $city_ontion;
	exit;
}
/* get category checklist tree BOF*/
function get_location_category_checklist($post_type,$pid,$mod='',$select_all='')
{
	global $wpdb;
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		global $sitepress;
		remove_filter('terms_clauses', array($sitepress, 'terms_clauses'));    
	}
	$post_taxonomy = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
	$pid = explode(',',$pid);
	
	$taxonomy_details = get_option('templatic_custom_taxonomy');
		$taxonomy = $post_taxonomy[0];
		$post_taxonomy = $post_taxonomy[0];
		$table_prefix = $wpdb->prefix;
		$wpcat_id = NULL;
		/*-Fetch main category-*/
		if($taxonomy == "")
		{
			$sql= $wpdb->prepare("SELECT * FROM {$table_prefix}terms, {$table_prefix}term_taxonomy WHERE {$table_prefix}terms.term_id = {$table_prefix}term_taxonomy.term_id AND ({$table_prefix}term_taxonomy.taxonomy =%s'".$post_taxonomy."') and  {$table_prefix}term_taxonomy.parent=%d  ORDER BY {$table_prefix}terms.name",$post_taxonomy,0);
			$wpcategories = (array)$wpdb->get_results($sql);
		}else{
			$sql= $wpdb->prepare("SELECT * FROM {$table_prefix}terms, {$table_prefix}term_taxonomy WHERE {$table_prefix}terms.term_id = {$table_prefix}term_taxonomy.term_id AND {$table_prefix}term_taxonomy.taxonomy =%s and  {$table_prefix}term_taxonomy.parent=%d  ORDER BY {$table_prefix}terms.name",$post_taxonomy,0);
			
			$wpcategories = (array)$wpdb->get_results($sql);
		}
		$wpcategories = array_values($wpcategories);
		$wpcat2 = NULL;
		if($wpcategories)
		{
			$counter = 0;
		echo "<ul>";
		if($select_all == 'select_all')
		{
		?>
		<li><label for="selectall"><input type="checkbox" name="category[]" id="selectall" value="all" class="checkbox" <?php if( @$_REQUEST['mod']=='custom_fields'){ ?> onclick="displaychk_frm();"<?php  } elseif( @$_REQUEST['mod']=='price'){ ?> onclick="displaychk_price();"<?php  }else{ ?>onclick="displaychk_frm();"<?php } ?> <?php if( @$pid[0]){ if(in_array('all',$pid)){ echo "checked=checked"; } }else{  }?>/>&nbsp;<?php echo __("Select All",LM_DOMAIN); ?></label></li>
		<?php
		}
		foreach ($wpcategories as $wpcat)
		{ 
			if($counter ==0){ 
				$tname = $taxonomy_details[$post_taxonomy]['label']; 
				if($post_taxonomy =='category' || $post_taxonomy ==''): ?>
				<li><label style="font-weight:bold;"><?php _e('Categories',LM_DOMAIN); ?></label></li>
				<?php else:?>
						<li><label style="font-weight:bold;"><?php echo $tname; ?></label></li>
			<?php 	
				endif;
			}
		
		$counter++;
		$termid = $wpcat->term_id;;
		$name = ucfirst($wpcat->name); 
		$termprice = $wpcat->term_price;
		$tparent =  $wpcat->parent;	
		?>
		<li><label for="<?php echo $termid; ?>"><input type="checkbox" name="category[]" id="<?php echo $termid; ?>" value="<?php echo $termid; ?>" class="checkbox" <?php if($pid[0]){ if(in_array($termid,$pid) || in_array('all',$pid)){ echo "checked=checked"; } }else{  }?> />&nbsp;<?php echo $name; if($termprice != "") { echo " (".display_amount_with_currency_plugin($termprice).") ";}else{  echo " (".display_amount_with_currency_plugin('0').") "; } ?></label></li>
		<?php
		
		if($taxonomy !=""){
		 $child = get_term_children( $termid, $post_taxonomy );
		 $args = array(
				'type'                     => 'place,event',
				'child_of'                 => $termid,
				'hide_empty'               => 0,
				'taxonomy'                 => $post_taxonomy
				);
		 $categories = get_categories( $args );
		 
		 foreach($categories as $child_of)
		 { 
			$child_of = $child_of->term_id; 
		 	$p = 0;
			$term = get_term_by( 'id', $child_of,$post_taxonomy);
			$termid = $term->term_taxonomy_id;
			$term_tax_id = $term->term_id;
			$termprice = $term->term_price;
			$name = $term->name;
			if($child_of)
			{				
				$catprice = $wpdb->get_row($wpdb->prepare("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id=%s and t.term_id = tt.term_id AND tt.taxonomy =%s",$child_of,$taxonomy));
				for($i=0;$i<count($catprice);$i++)
				{
					if($catprice->parent)
					{	
						$p++;
						$catprice1 = $wpdb->get_row($wpdb->prepare("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id=%s and t.term_id = tt.term_id AND tt.taxonomy =%s",$catprice->parent,$taxonomy));
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
			<li style="margin-left:<?php echo $p; ?>px;"><label for="<?php echo $term_tax_id; ?>"><input type="checkbox" name="category[]" id="<?php echo $term_tax_id; ?>" value="<?php echo $term_tax_id; ?>" class="checkbox" <?php if($pid[0]){ if(in_array($term_tax_id,$pid) || in_array('all',$pid)){ echo "checked=checked"; } }else{  }?> />&nbsp;<?php echo $name; if($termprice != "") { echo " (".display_amount_with_currency_plugin($termprice).") ";}else{  echo " (".display_amount_with_currency_plugin('0').") "; } ?></label></li>
		<?php  }	}else{
		 $child = get_term_children( $termid, $post_taxonomy );
		 
		 foreach($child as $child_of)
		 { 
		 	$p = 0;
			$term = get_term_by( 'id', $child_of,$post_taxonomy);
			$termid = $term->term_taxonomy_id;
			$term_tax_id = $term->term_id;
			$termprice = $term->term_price;
			$name = $term->name;
			if($child_of)
			{
				$catprice = $wpdb->get_row($wpdb->prepare("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id=%s and t.term_id = tt.term_id AND (tt.taxonomy =%s)",$child_of,$post_taxonomy));
				for($i=0;$i<count($catprice);$i++)
				{
					if($catprice->parent)
					{	
						$p++;
						$catprice1 = $wpdb->get_row($wpdb->prepare("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id=%s and t.term_id = tt.term_id AND (tt.taxonomy =%s)",$catprice->parent,$post_taxonomy));
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
			<li style="margin-left:<?php echo $p; ?>px;"><label><input type="checkbox" name="category[]" id="<?php echo $term_tax_id; ?>" value="<?php echo $term_tax_id; ?>" class="checkbox" <?php if($pid[0]){ if(in_array($term_tax_id,$pid) || in_array('all',$pid)){ echo "checked=checked"; } }else{  }?> />&nbsp;<?php echo $name; if($termprice != "") { echo " (".display_amount_with_currency_plugin($termprice).") ";}else{  echo " (".display_amount_with_currency_plugin('0').") "; } ?></label></li>
		<?php  }	
				}		
}
	echo "</ul>"; } else{
			sprintf(__('There is no categories in %s',LM_DOMAIN),$post_type);
	}
}
/*
 * Function Name:  get_current_multicity_info
 *
 */
 
add_action('init','location_current_multicity',9);
function location_current_multicity(){
	global $wpdb,$country_table,$zones_table,$multicity_table,$current_cityinfo,$wp_query;
	$country_table = $wpdb->prefix."countries";
	$zones_table =$wpdb->prefix . "zones";	
	$multicity_table = $wpdb->prefix . "multicity";	
	
	if(isset($_REQUEST['find_city']) && $_REQUEST['find_city']=='nearest'){
		$lat=$_COOKIE['c_latitude'];
		$long=$_COOKIE['c_longitude'];
		if(!isset($_COOKIE['c_latitude']) && !isset($_COOKIE['c_longitude'])){
			$ip  = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
			$url = "http://freegeoip.net/json/$ip";
			$data=wp_remote_get( $url, array( 'timeout' => 120, 'httpversion' => '1.1' ) );
			if ($data) {
			    $location = json_decode($data['body']);				
			    $lat = $_COOKIE['c_latitude'] = $location->latitude;
			    $long = $_COOKIE['c_longitude']= $location->longitude;			    
			}
		}
		$sql="SELECT distinct city_id, cityname,city_slug FROM  $multicity_table, {$wpdb->prefix}postmeta WHERE meta_key='post_city_id' AND meta_value=city_id and  truncate((degrees(acos( sin(radians(lat)) * sin( radians('".$lat."')) + cos(radians(lat)) * cos( radians('".$lat."')) * cos( radians(lng - '".$long."') ) ) ) * 69.09),1) ORDER BY truncate((degrees(acos( sin(radians(lat)) * sin( radians('".$lat."')) + cos(radians(lat)) * cos( radians('".$lat."')) * cos( radians(lng - '".$long."') ) ) ) * 69.09),1) ASC LIMIT 0,1";
		$nearest_result=$wpdb->get_results($sql);
		$_SESSION['post_city_id']=$nearest_result[0]->city_id;	
	}
	
	/* Header City */
	if(isset($_POST['header_city']) && $_POST['header_city']!=""){
		$_SESSION['post_city_id']=$_POST['header_city'];	
	}
	if(isset($_POST['widget_city']) && $_POST['widget_city']!=""){
		$_SESSION['post_city_id']=$_POST['widget_city'];	
	}
	if((get_option('show_on_front')=='page' && is_front_page()) || is_home()){
		if(strstr($_SERVER['REQUEST_URI'],'/city/')){
			$current_city = explode('/city/',$_SERVER['REQUEST_URI']);	
			if(strstr($current_city[1],'/')){
				$current_city = explode('/',$current_city[1]);
				$current_city = str_replace('/','',$current_city[0]);
			}else{
				$current_city = str_replace('/','',$current_city[1]);
				$wp_query->set('city',$current_city);
			}
		}
	}
	
	if(isset($_SESSION['post_city_id']) && $_SESSION['post_city_id']!=''){
		/* 
		 * Check the query var city not equal blank then set the multicity by cityname slug
		 */
		if($wpdb->get_var("SHOW TABLES LIKE '$multicity_table'") == $multicity_table) {
			if(get_query_var('city')!='')
				$sql=$wpdb->prepare("SELECT * FROM $multicity_table where city_slug=%s",get_query_var('city'));
			else
			$sql=$wpdb->prepare("SELECT * FROM $multicity_table where city_id=%d",$_SESSION['post_city_id']);
		}
	}else{
		/*  Fetch the remote address location*/		
		if(get_option('default_city_set')=='nearest_city'){
			$ip  = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
			$url = "http://freegeoip.net/json/$ip";
			$data=wp_remote_get( $url, array( 'timeout' => 120, 'httpversion' => '1.1' ) );
			if ($data) {
			    $location = json_decode($data['body']);				
			    $lat = $_COOKIE['c_latitude'] = $location->latitude;
			    $long = $_COOKIE['c_longitude']= $location->longitude;
			    $sql="SELECT distinct city_id, cityname,city_slug FROM  $multicity_table, {$wpdb->prefix}postmeta WHERE meta_key='post_city_id' AND meta_value=city_id and  truncate((degrees(acos( sin(radians(lat)) * sin( radians('".$lat."')) + cos(radians(lat)) * cos( radians('".$lat."')) * cos( radians(lng - '".$long."') ) ) ) * 69.09),1) ORDER BY truncate((degrees(acos( sin(radians(lat)) * sin( radians('".$lat."')) + cos(radians(lat)) * cos( radians('".$lat."')) * cos( radians(lng - '".$long."') ) ) ) * 69.09),1) ASC LIMIT 0,1";
			}
		/* Finish fetach remote address location */	
		}else{
			$sql=$wpdb->prepare("SELECT * FROM $multicity_table where is_default=%d",1);
		}
	}
	
	$default_city = $wpdb->get_results($sql);
	$default_city_id=$default_city[0]->city_id;
	$_SESSION['post_city_id']=$default_city_id;
	
	$cityinfo = $wpdb->get_results($wpdb->prepare("SELECT mc.*,mc.message as msg,c.country_name,c.message,c.country_flg,z.zone_name FROM $multicity_table mc,$zones_table z,$country_table c where c.country_id=mc.country_id AND z.zones_id=mc.zones_id AND  mc.city_id =%d order by cityname   ASC",$_SESSION['post_city_id']));
	
	 if (function_exists('icl_register_string')){
			icl_register_string('location-manager', 'location_city_'.$cityinfo[0]->city_slug,$cityinfo[0]->cityname);
			icl_register_string('location-manager', 'location_city_msg'.$cityinfo[0]->city_slug,$cityinfo[0]->msg);
			$cityinfo[0]->cityname = icl_t('location-manager', 'location_city_'.$cityinfo[0]->city_slug,$cityinfo[0]->cityname);
			$cityinfo[0]->msg = icl_t('location-manager', 'location_city_msg'.$cityinfo[0]->city_slug,$cityinfo[0]->msg);
	 }
	
	$current_cityinfo=array('city_id'      =>$cityinfo[0]->city_id,
					    'country_id'   =>$cityinfo[0]->country_id,
					    'zones_id'     =>$cityinfo[0]->zones_id,
					    'cityname'     => stripslashes($cityinfo[0]->cityname),
					    'city_slug'    =>$cityinfo[0]->city_slug,
					    'lat'          =>$cityinfo[0]->lat,
					    'lng'          =>$cityinfo[0]->lng,
					    'scall_factor' =>$cityinfo[0]->scall_factor,
					    'is_zoom_home' =>$cityinfo[0]->is_zoom_home,
					    'map_type'     =>$cityinfo[0]->map_type,
					    'post_type'    =>$cityinfo[0]->post_type,
					    'categories'   =>$cityinfo[0]->categories,
					    'color'        =>$cityinfo[0]->color,
					    'message'      =>$cityinfo[0]->msg,
					    'color'        =>$cityinfo[0]->color,
					    'images'       =>$cityinfo[0]->images,
					    'country_name' =>$cityinfo[0]->country_name,
					    'country_flg'  =>$cityinfo[0]->country_flg,
					    'zone_name'    =>$cityinfo[0]->zone_name,
					    'header_color' =>$cityinfo[0]->header_color,
					    'header_image' =>$cityinfo[0]->header_image,
					    );
	return $current_cityinfo;
}

?>