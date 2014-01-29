<?php
/* File contain the form of add/edit the custom fields */
global $wpdb,$current_user;
if(isset($_REQUEST['field_id'])){
	$post_id = $_REQUEST['field_id'];
	$post_val = get_post($post_id);
}else{
	$post_val='';
}
 
if(isset($_POST['submit-fields']) && $_POST['submit-fields'] !='')
{ 
	$ctype = $_POST['ctype'];
	$admin_title = $_POST['admin_title'];
	$htmlvar_name = $_POST['htmlvar_name'];
	$admin_desc = $_POST['admin_desc'];
	$default_value = $_POST['default_value'];
	$sort_order = $_POST['sort_order'];
	
	$ptype = $_POST['post_type_sel'];
	$option_values = $_POST['option_values'];
	$show_on_page = $_POST['show_on_page'];
	$extra_parameter = $_POST['extra_parameter'];
	$validation_type = $_POST['validation_type'];
	$field_require_desc = stripslashes($_POST['field_require_desc']);
	$style_class = $_POST['style_class'];
	
	
	$_POST['is_require'] = (isset($_POST['is_require']))? $_POST['is_require'] :0;
	$_POST['is_active'] = (isset($_POST['is_active']))? $_POST['is_active'] :0;
	$_POST['show_on_listing'] = (isset($_POST['show_on_listing']))? $_POST['show_on_listing'] :0;
	$_POST['show_on_detail'] = (isset($_POST['show_on_detail']))? $_POST['show_on_detail'] :0;
	$_POST['show_on_success'] = (isset($_POST['show_on_success']))? $_POST['show_on_success'] : 0;
	$_POST['show_in_column'] = (isset($_POST['show_in_column']))? $_POST['show_in_column'] :0;
	$_POST['show_in_email'] = (isset($_POST['show_in_email']))? $_POST['show_in_email'] :0;
	$_POST['is_search'] = (isset($_POST['is_search']))? $_POST['is_search'] :0;
	
	$is_delete = $_POST['is_delete'];
	$is_edit = $_POST['is_edit'];
	
	
	if(isset($_REQUEST['field_id']))
	{
		$my_post = array(
		 'post_title' => $admin_title,
		 'post_content' => $admin_desc,
		 'post_status' => 'publish',
		 'post_author' => 1,
		 'post_type' => "custom_fields",
		 'post_name' => $htmlvar_name,
		 'ID' => $_REQUEST['field_id'],
		);
		$post_id = wp_insert_post( $my_post );
		/* Finish the place geo_latitude and geo_longitude in postcodes table*/
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		$wpdb->delete( "$wpdb->term_relationships", array( 'object_id' => $post_id ,'term_taxonomy_id' => 1), array( '%d' ,'%d') );		
		if(isset($_POST['category']) && $_POST['category'] !=''){
			$tax = $_POST['post_type_sel'];
			foreach($tax as $key=> $_tax)
			{
				$taxexp = explode(",",$_tax);
				wp_set_post_terms($post_id,'',$taxexp[1],false);
				if($taxexp[1] != 'all')
				  {
					foreach($_POST['category'] as $category)
					 {
						wp_set_post_terms($post_id,$category,$taxexp[1],true);
						
					 }
				  }
			}
		}
		foreach($_POST as $key=>$meta_value)
		 {
			if($key != 'save' && $key != 'category' && $key != 'admin_title' && $key != 'post_type' && $key != 'admin_desc')
			 {
				update_post_meta($post_id, $key, $meta_value);
			 }
		 }
		 $post_type = $_POST['post_type_sel'];
		 $total_post_type = get_option('templatic_custom_post');
		 delete_post_meta($post_id, 'post_type_post');
		 delete_post_meta($post_id, 'taxonomy_type_category');
		 foreach($total_post_type as $key=> $_total_post_type)
		  {
			delete_post_meta($post_id, 'post_type_'.$key.'');
			delete_post_meta($post_id, 'taxonomy_type_'.$_total_post_type['slugs'][0].'');
		  }
		  
		 if(count($post_type) > 0)
		  {
			 foreach($post_type as $_post_type)
			  {
				 if($_post_type != 'all,all')
				  {
					 $post_type_ex = explode(",",$_post_type);
					 update_post_meta($post_id, 'post_type_'.$post_type_ex[0].'', $post_type_ex[0]);
					 update_post_meta($post_id, 'taxonomy_type_'.$post_type_ex[1].'', $post_type_ex[1]);
					 $finpost_type .= $post_type_ex[0].",";
				  }
			  }
		  }
		 update_post_meta($post_id, 'post_type',substr($finpost_type,0,-1));
		 if(isset($_POST['category']) && $_POST['category']!=''){
			update_post_meta($post_id,"field_category",implode(",",$_POST['category']));
		 }
		$msgtype = 'edit';
	}else
	{
		$my_post = array(
		 'post_title' => $admin_title,
		 'post_content' => $admin_desc,
		 'post_status' => 'publish',
		 'post_author' => 1,
		 'post_type' => "custom_fields",
		 'post_name' => $htmlvar_name,
		);
		$post_id = wp_insert_post( $my_post );
		/* Finish the place geo_latitude and geo_longitude in postcodes table*/
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $_REQUEST['icl_trid'], $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		$tax = $_POST['post_type_sel'];
		foreach($tax as $key=> $_tax)
		{
			if(isset($_POST['category']) && $_POST['category']!="")
			{
				 $taxexp = explode(",",$_tax);
				 if($taxexp[1] != 'all')
				   {
					 foreach($_POST['category'] as $category)
					 {
						wp_set_post_terms($post_id,$category,$taxexp[1],true);
					 }
				   }
			}
		}
		foreach($_POST as $key=>$meta_value)
		 {
			if($key != 'save' && $key != 'category' && $key != 'admin_title' && $key != 'post_type' && $key != 'admin_desc')
			 {
				add_post_meta($post_id, $key, $meta_value);
			 }
		 }
		 
		 if(isset($_POST['post_type_sel']) && $_POST['post_type_sel']!="")
		 {
			 $post_type = $_POST['post_type_sel'];
			 foreach($post_type as $_post_type)
			  {				 
					 $post_type_ex = explode(",",$_post_type);
				
						//update_post_meta($post_id, 'post_type_'.$post_type_ex[0].'', $post_type_ex[0]);
						update_post_meta($post_id, 'taxonomy_type_'.$post_type_ex[1].'', $post_type_ex[1]);
				
						if(in_array('all',$post_type_ex))
						{
							update_post_meta($post_id, 'post_type_'.$post_type_ex[0].'', 'all');
						}else{
							update_post_meta($post_id, 'post_type_'.$post_type_ex[0].'', $post_type_ex[0]);
						}
					 
					 $finpost_type .= $post_type_ex[0].",";
				  
			  }
			 update_post_meta($post_id, 'post_type',substr($finpost_type,0,-1));
		 }
		 if(isset($_POST['category']) && $_POST['category']!="")
			 add_post_meta($post_id,"field_category",implode(",",$_POST['category']));
			 
		 $msgtype = 'add';
	}
	update_option('tevolution_query_cache',1);
	$location = site_url().'/wp-admin/admin.php';
	echo '<form action="'.$location.'" method="get" id="frm_edit_custom_fields" name="frm_edit_custom_fields">
				<input type="hidden" value="custom_fields" name="page"><input type="hidden" value="success" name="custom_field_msg"><input type="hidden" value="'.$msgtype.'" name="custom_msg_type">
		  </form>
		  <script>document.frm_edit_custom_fields.submit();</script>';
		  exit;
}
$tmpdata = get_option('templatic_settings');
$catoption = $tmpdata['templatic-category_custom_fields'];
?>
<script type="text/javascript">
function showcat(str)
{
	if (str=="")
	  {
	  document.getElementById("field_category").innerHTML="";
	  return;
	  }else{
	  document.getElementById("field_category").innerHTML="";
	  document.getElementById("process").style.display ="block";
	  }
		if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
		else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
		xmlhttp.onreadystatechange=function()
	  {
	    if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		 document.getElementById("process").style.display ="none";
		 document.getElementById("field_category").innerHTML=xmlhttp.responseText;
		}
	  }
	  
	  var valarr = '';
	  if(str == 'all,all')
	    {
			var valspl = str.split(",");
			valarr = valspl[1];
		}
	  else
	    {
			var val = [];
			var valfin = '';			
			jQuery("tr#post_type input[name='post_type_sel[]']").each(function() {
				if (jQuery(this).attr('checked'))
				{	
					val = jQuery(this).val();
					valfin = val.split(",");
					valarr+=valfin[1]+',';
				}
			});
			
		}	
	  if(valarr==''){ valarr ='all'; }
		<?php
		$language='';
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$language="&language=".$current_lang_code;
		}?>
	  url = "<?php echo plugin_dir_url( __FILE__ ); ?>ajax_custom_taxonomy.php?post_type="+valarr+"&page=custom_fields<?php echo $language;?>"
	  xmlhttp.open("GET",url,true);
	  xmlhttp.send();
}
function displaychk_frm()
{
	dml = document.forms['custom_fields_frm'];
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
function selectall_posttype()
{
	dml = document.forms['custom_fields_frm'];
	chk = dml.elements['post_type_sel[]'];
	len = dml.elements['post_type_sel[]'].length;
	
	if(document.getElementById('selectall_post_type').checked == true) { 
		for (i = 0; i < len; i++)
		chk[i].checked = true ;
	} else { 
		for (i = 0; i < len; i++)
		chk[i].checked = false ;
	}
}
function chk_field_form()
{
	jQuery.noConflict();
	var field_title = jQuery('#admin_title').val();
	var html_var_title = jQuery('#htmlvar_name').val();
	var sort_order = jQuery("#sort_order").val();
	if(field_title == "" || html_var_title == '' || sort_order == '')
	{
		if(field_title == '')
			jQuery('#admin_title_id').addClass('form-invalid');
		jQuery('#admin_title_id').change(on_change_admin_title);
		if(html_var_title == '')
			jQuery('#html_var_name').addClass('form-invalid');
		jQuery('#html_var_name').change(on_change_html_var);
		if(sort_order == '')
			jQuery('#sort_order_id').addClass('form-invalid');
		jQuery('#sort_order_id').change(on_change_sort_order);
		var htstr = jQuery('#htmlvar_name').val();
		var htstr1 = htstr.indexOf(" ");
		if(htstr1 > 0)
		{
			jQuery('#html_var_name').addClass('form-invalid');
		}
		return false;
	}
	function on_change_admin_title()
	{
		var field_title = jQuery('#admin_title').val();
		if(field_title == "")
		{
			jQuery('#admin_title_id').addClass('form-invalid');
			return false;
		}
		else
		{
			jQuery('#admin_title_id').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_html_var()
	{
		var html_var_title = jQuery('#htmlvar_name').val();
		if(html_var_title == "")
		{
			jQuery('#html_var_name').addClass('form-invalid');
			return false;
		}
		else
		{
			jQuery('#html_var_name').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_sort_order()
	{
		var sort_order_title = jQuery('#sort_order').val();
		if(sort_order_title == "")
		{
			jQuery('#sort_order_id').addClass('form-invalid');
			return false;
		}
		else
		{
			jQuery('#sort_order_id').removeClass('form-invalid');
			return true;
		}
	}
}
</script>
<div class="wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
    <h2><?php if(isset($_REQUEST['field_id']) && $_REQUEST['field_id'] != ''){  _e('Edit - '.$post_val->post_title,DOMAIN);
	 }else { _e('Add a new field',DOMAIN);}
	$custom_msg = sprintf(__('Use this section to define new fields for your submission forms. Fields can be created for all posts typed created using the <a href="%s" target="_blank" title="Custom Field Guide">Custom Post Types</a> section.',DOMAIN),admin_url('admin.php?page=custom_taxonomy'));
	?>    
	<a id="edit_custom_user_custom_field" href="<?php echo site_url();?>/wp-admin/admin.php?page=custom_fields" name="btnviewlisting" class="add-new-h2" title="<?php _e('Back to manage custom fields',DOMAIN);?>"/><?php _e('Back to manage custom field list',DOMAIN); ?></a>
    </h2>
<!-- Function to fetch categories -->
<!--<form class="form_style" action="<?php echo site_url();?>/wp-admin/admin.php?page=custom_fields&action=addnew" method="post" name="custom_fields_frm" onsubmit="return chk_field_form();">-->
<form class="form_style" action="<?php echo site_url();?>/wp-admin/admin.php?<?php echo $_SERVER['QUERY_STRING'];?>" method="post" name="custom_fields_frm" onsubmit="return chk_field_form();">
	<?php
	if(is_plugin_active('wpml-translation-management/plugin.php')){
		echo '<input type="hidden" name="icl_post_language" value="'. @$_REQUEST['lang'].'" />';	
		echo '<input type="hidden" name="icl_trid" value="'. @$_REQUEST['trid'].'" />';	
		echo '<input type="hidden" name="icl_translation_of" value="'. @$_REQUEST['trid'].'" />';			
	}
	?>
	
	<input type="hidden" name="save" value="1" /> <input type="hidden" name="is_delete" value="<?php if($post_val){ echo get_post_meta($post_id,"is_delete",true); }?>" />
	<?php if(@$_REQUEST['field_id']){?>
	<input type="hidden" name="field_id" value="<?php echo $_REQUEST['field_id'];?>" />
	<?php }?>
     <table class="form-table" id="form_table">
        <thead>
            <tr colspan="3">
                <p class="tevolution_desc"><?php echo $custom_msg;?></p>
            </tr>
        </thead>
		<tbody>
            <tr>
				<th colspan="2">
					<div class="tevo_sub_title" style="margin-top:0px"><?php _e("Basic Options",DOMAIN);?></div>
				</th>
			</tr>
			<tr id="post_type"  style="display:block;" >
            	<th>
                	<label for="post_name" class="form-textfield-label"><?php _e('Enable for',DOMAIN);?><span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
            	</th>
            	<td>
               	<?php
				$post_types = array();
				if( @$_REQUEST['field_id'] )
				{
					$post_types = explode(",",get_post_meta($_REQUEST['field_id'],'post_type',true));
				}
				$custom_post_types = get_option("templatic_custom_post");
				$i = 0;	
				?>
               	<fieldset>				
				<label for="selectall_post_type"><input type="checkbox" name="post_type_sel[]" id="selectall_post_type" onClick="showcat(this.value);selectall_posttype();" value="all,all" />&nbsp;<?php _e('Select All', DOMAIN);?></label><br />
				
                    <label for="post_type_post"><input type="checkbox" name="post_type_sel[]" id="post_type_post" onClick="showcat(this.value);" value="post,category" <?php if(in_array('post',$post_types)) { ?> checked="checked" <?php } ?> />
						<?php echo 'Post';?></label><br />
				<?php
							
				foreach ($custom_post_types as $content_type=>$content_type_label) {					
					?>
						
					<label for="post_type_<?php echo $i; ?>"><input type="checkbox" name="post_type_sel[]" id="post_type_<?php echo $i; ?>" onClick="showcat(this.value);" value="<?php if(isset($content_type_label['slugs'][0]) && isset($content_type)) { echo $content_type.",".$content_type_label['slugs'][0]; } ?>" <?php if(in_array($content_type,$post_types)) { ?> checked="checked" <?php } ?> />
						<?php echo $content_type_label['label'];?></label><br />
						
				<?php				
				$i++;	
				} ?>
                    </fieldset>
                    <p class="description"><?php _e('The field you&rsquo;re creating will only work for the post types you select above', DOMAIN);?></p>
			</td>
			</td>
		 </tr>
		 <tr <?php if($catoption == 'No'){ ?> style="display:none;" <?php }else{ ?> style="display:block;" <?php } ?>>
            	<th>
                	<label for="post_slug" class="form-textfield-label"><?php _e('Select the categories',DOMAIN); ?> <span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
            	</th>
                <td>
				<div class="element wp-tab-panel" id="field_category" style="width:300px;overflow-y: scroll; margin-bottom:5px;">
				<?php
					$show_select_all = 1;
					foreach($post_types as $_post_types)
					{
						foreach ($custom_post_types as $content_type=>$content_type_label)
						 {
							 $cat_slug = '';
							 if($content_type== $_post_types)
							  { 
								$cat_slug = $content_type_label['slugs'][0];
								break;
							  }else{
								$cat_slug='category';
							  }
						 }						
						get_wp_category_checklist_plugin($cat_slug,get_post_meta($post_id,"field_category",true),$show_select_all);
						$show_select_all++;
					}
					
					
				?>  
			  </div>
			  <span id='process' style='display:none;'><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/process.gif" alt='Processing..' /></span>
              </td>
         </tr>
		<?php 
  		$heading_type = fetch_heading_posts();
		if(count($heading_type) > 0):
		?>  <tr <?php if( @get_post_meta($post_id,"is_edit",true) == 'false'){?> style="display:none;" <?php }else{?> style="display:block;" <?php }?> id="heading_type_id">
            	<th>
                	<label for="heading_type" class="form-textfield-label"><?php _e('Heading',DOMAIN);?></label>
            	</th>
            	<td>
            		  <select name="heading_type" id="heading_type">
                        <?php foreach($heading_type as $key=> $_heading_type):?>
                            <option value="<?php echo $_heading_type; ?>" <?php if( @get_post_meta($post_id,"heading_type",true) == $_heading_type){ echo 'selected="selected"';}?>><?php echo $_heading_type;?></option>
                        <?php endforeach; ?>  
                      </select>
					   <p class="description"><?php
						$taxonomy_name .= 'Post';
						if(count($custom_post_types) > 0 )
						{
							foreach ($custom_post_types as $content_type=>$content_type_label) {
								$taxonomy_name .= ",".$content_type_label['label'];
							}
						}
					   echo sprintf(__('Choose the group under which the field should be placed. Select the %s option to place it inside the main grouping area.',DOMAIN),"taxonomy_name");?></p>
				</td>
            </tr>
	   <?php endif; ?>  
	<tr class="" id="tax_name" style="display:block;">
          <th>
          	<label for="field_type" class="form-textfield-label"><?php _e('Type',DOMAIN);?></label>
          </th>
          <td>
               <select name="ctype" id="ctype" onchange="show_option_add(this.value)" <?php if(get_post_meta($post_id,"ctype",true)=='geo_map'){ ?>style="pointer:none;" readonly=readonly<?php } ?>>
                    <option value="text" <?php if( @get_post_meta($post_id,"ctype",true)=='text'){ echo 'selected="selected"';}?>><?php _e('Text',DOMAIN);?></option>
                    <option value="date" <?php if( @get_post_meta($post_id,"ctype",true)=='date'){ echo 'selected="selected"';}?>><?php _e('Date Picker',DOMAIN);?></option>
                    <option value="multicheckbox" <?php if( @get_post_meta($post_id,"ctype",true)=='multicheckbox'){ echo 'selected="selected"';}?>><?php _e('Multi Checkbox',DOMAIN);?></option>
                    <option value="radio" <?php if( @get_post_meta($post_id,"ctype",true)=='radio'){ echo 'selected="selected"';}?>><?php _e('Radio',DOMAIN);?></option>
                    <option value="select" <?php if( @get_post_meta($post_id,"ctype",true)=='select'){ echo 'selected="selected"';}?>><?php _e('Select',DOMAIN);?></option>
                    <option value="texteditor" <?php if( @get_post_meta($post_id,"ctype",true)=='texteditor'){ echo 'selected="selected"';}?>><?php _e('Text Editor',DOMAIN);?></option>
                    <option value="textarea" <?php if( @get_post_meta($post_id,"ctype",true)=='textarea'){ echo 'selected="selected"';}?>><?php _e('Textarea',DOMAIN);?></option>
                    <option value="heading_type" <?php if( @get_post_meta($post_id,"ctype",true)=='heading_type'){ echo 'selected="selected"';}?>><?php _e('Heading',DOMAIN);?></option>
                    <option value="image_uploader" <?php if( @get_post_meta($post_id,"ctype",true)=='image_uploader'){ echo 'selected="selected"';}?>><?php _e('Multi image uploader',DOMAIN);?></option>
                    <option value="upload" <?php if( @get_post_meta($post_id,"ctype",true)=='upload'){ echo 'selected="selected"';}?>><?php _e('File uploader',DOMAIN);?></option>
                    <option value="geo_map" <?php if( @get_post_meta($post_id,"ctype",true)=='geo_map'){ echo 'selected="selected"';}?>><?php _e('Geo Map',DOMAIN);?></option>
                    <option value="post_categories" <?php if( @get_post_meta($post_id,"ctype",true)=='post_categories'){ echo 'selected="selected"';}?>><?php _e('Post Categories',DOMAIN);?></option>
                    <?php do_action('cunstom_field_type',$post_id); // do action use for new field type option?>
               </select>
          </td>
    </tr>
	<tr id="ctype_option_title_tr_id"  <?php if( @get_post_meta($post_id,"ctype",true)=='select' && get_post_meta($post_id,"is_edit",true) == '0'){?> style="display:block;" <?php }else{?> style="display:none;" <?php }?> >
          <th>
          	<label for="option_title" class="form-textfield-label"><?php _e('Option Title',DOMAIN);?></label>
          </th>
          <td>
               <input type="text" name="option_title" id="option_title" value="<?php echo get_post_meta($post_id,"option_title",true);?>" size="50"  />
               <p class="description"><?php _e('Separate multiple option titles with a comma. eg. Yes,No',DOMAIN);?></p>
          </td>
	</tr>
	<tr id="ctype_option_tr_id"  <?php if( @get_post_meta($post_id,"ctype",true)=='select' && get_post_meta($post_id,"is_edit",true) == '0'){?> style="display:block;" <?php }else{?> style="display:none;" <?php }?> >
          <th>
          	<label for="option_value" class="form-textfield-label"><?php _e('Option values',DOMAIN);?></label>
          </th>
          <td>
               <input type="text" name="option_values" id="option_values" value="<?php echo get_post_meta($post_id,"option_values",true);?>" size="50"  />
               <p class="description"><?php _e('Separate multiple option values with a comma. eg. Yes,No',DOMAIN);?></p>
          </td>
	</tr>
    <tr style="display:block;" id="admin_title_id">
          <th>
          	<label for="field_title" class="form-textfield-label"><?php _e('Label',DOMAIN);?><span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
          </th>
          <td>
          	<input type="text" class="regular-text" name="admin_title" id="admin_title" value="<?php if($post_val){ echo $post_val->post_title; } ?>" size="50" />
            <p class="description"><?php _e('Set the title for this field. The same label is applied to both the front-end and the back-end.', DOMAIN);?></p>
          </td>
    </tr>
	
	<tr <?php if( @get_post_meta($post_id,"is_edit",true) == 'false'){?> style="display:none;" <?php }else{?> style="display:block;" <?php }?> id="html_var_name">
          <th>
          	<label for="field_name" class="form-textfield-label"><?php _e('Unique variable name',DOMAIN);?><span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
          </th>
          <td>
               <input type="text" class="regular-text" name="htmlvar_name" id="htmlvar_name" value="<?php echo @get_post_meta($post_id,"htmlvar_name",true);?>" size="50"  <?php if( @$_REQUEST['field_id'] !="") { ?>readonly=readonly style="pointer-events: none;"<?php } ?>/>
               <p class="description"><?php _e('This name is used by the theme internally. It <b>must be</b> unique with no special characters or spaces (use underscores instead). ',DOMAIN); ?></p>
          </td>
    </tr>
	<tr style="display:block;">
          <th>
          	<label for="description" class="form-textfield-label"><?php _e('Description',DOMAIN);?></label>
          </th>
          <td>
               <input type="text" class="regular-text" name="admin_desc" id="admin_desc" value="<?php if($post_val) { echo $post_val->post_content; } ?>" size="50" />
               <p class="description"><?php _e('Provide more information about this custom field. It will be displayed below the field on your site.',DOMAIN);?></p>
          </td>
    </tr>
	
	<tr <?php if( @get_post_meta($post_id,"is_edit",true) == 'false'){?> style="display:none;" <?php }else{?> style="display:block;" <?php }?> id="default_value_id">
          <th>
          	<label for="default_value" class="form-textfield-label"><?php _e('Default value',DOMAIN). @get_post_meta($post_id,"is_edit",true);?> </label>
          </th>
          <td>
               <input type="text" class="regular-text" name="default_value" id="default_value" value="<?php echo @get_post_meta($post_id,"default_value",true);?>" size="50" />
               <p class="description"><?php _e("This value will be applied automatically, even if visitors don't select anything.",DOMAIN);?></p>
          </td>
    </tr>
	
	<tr style="display:block;">
          <th>
         		<label for="active" class="form-textfield-label"><?php _e('Active',DOMAIN);?></label>
          </th>
          <td>
          	<input type="checkbox" name="is_active" id="is_active" value="1" <?php if( @get_post_meta($post_id,"is_active",true)=='1'){ echo 'checked="checked"';}?>  />&nbsp;<label for="is_active"><?php _e('Yes',DOMAIN);?></label>              
               <p class="description"><?php _e('Uncheck this box only if you want to create the field but not use it right away.',DOMAIN);?></p>
          </td>
    </tr>
	<!-- is required and required message start-->
	<tr <?php if( @get_post_meta($post_id,"is_edit",true) == 'false'){?> style="display:none;" <?php }else{?> style="display:table-row;" <?php }?> id="validation_options" >
		<th colspan="2">
			<div class="tevo_sub_title" style="margin-top:0px"><?php _e("Validation Options",DOMAIN);?></div>
		</th>
	</tr>
	<tr <?php if( @get_post_meta($post_id,"is_edit",true) == 'false'){?> style="display:none;" <?php }else{?> style="display:block;" <?php }?> id="is_require_id">
          <th>
          	<label for="active" class="form-textfield-label"><?php _e('Required',DOMAIN);?></label>
          </th>
          <td>
               <input type="checkbox" name="is_require" id="is_require" value="1"  <?php if( @get_post_meta($post_id,"is_require",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="is_require"><?php _e('Yes',DOMAIN);?></label>
               <p class="description"><?php _e('Required fields cannot be left empty during submission. A value must be entered before moving on to the next step.',DOMAIN);?></p>
          </td>
    </tr>
	<!-- validation start -->
	<tr <?php if( @get_post_meta($post_id,"is_edit",true) == 'false'){?> style="display:none;" <?php }else{?> style="display:block;" <?php }?> id="validation_type_id">
          <th >
          	<label for="validation_type" class="form-textfield-label"><?php _e('Validation type',DOMAIN);?></label>
          </th>
          <td>
               <select name="validation_type" id="validation_type"><?php echo validation_type_cmb_plugin(get_post_meta($post_id,"validation_type",true));?></select></div>
               <p class="description"><?php _e('<small><b>Require</b> - the field cannot be left blank (default setting).<br/><b>Phone No.</b> - values must be in phone number format.<br/><b>Digit</b> - values must be all numbers.<br/><b>Email</b> - the value must be in email format.</small>',DOMAIN);?></p>
          </td>
    </tr>
	<!-- validation end -->
	
	<!-- required field msg start -->
	<tr <?php if( @get_post_meta($post_id,"is_edit",true) == 'false'){?> style="display:none;" <?php }else{?> style="display:block;" <?php }?> id="field_require_desc_id">
          <th>
          	<label for="active" class="form-textfield-label"><?php _e('Required field warning message',DOMAIN);?></label>
          </th>
          <td>
               <textarea name="field_require_desc" class="tb_textarea" id="field_require_desc"><?php echo @get_post_meta($post_id,"field_require_desc",true);?></textarea>
               <p class="description"><?php _e('The message that will appear when a mandatory field is left blank.',DOMAIN);?></p>
          </td>
    </tr>
	<!-- required field msg end -->
	<tr <?php if( @get_post_meta($post_id,"is_edit",true) == 'false'){?> style="display:none;" <?php }else{?> style="display:block;" <?php }?>>
		<th colspan="2">
			<div class="tevo_sub_title" style="margin-top:0px"><?php _e("Display Options",DOMAIN);?></div>
		</th>
	</tr>
	<!-- is required and required message end-->
	<tr <?php if( @get_post_meta($post_id,"is_edit",true) == 'false'){?> style="display:none;" <?php }else{?> style="display:block;" <?php }?> id="sort_order_id">
          <th>
          	<label for="sort_order" class="form-textfield-label"><?php _e('Position (display order)',DOMAIN);?><span class="required"><?php echo FLD_REQUIRED_TEXT; ?></span></label>
          </th>
          <td>
               <input type="text" class="regular-text" name="sort_order" id="sort_order"  value="<?php echo @get_post_meta($post_id,"sort_order",true);?>" size="50" />
               <p class="description"><?php _e('A numeric value that determines the position of the field inside the submission form. Enter 1 to make the field appear at the top.',DOMAIN);?></p>
          </td>
    </tr>
	<tr <?php if( @get_post_meta($post_id,"is_edit",true) == 'false'){?> style="display:none;" <?php }else{?>style="display:block;"<?php }?>>
          <th>
          	<label for="display_location" class="form-textfield-label"><?php _e('Display location',DOMAIN);?></label>
          </th>
          <td>
               <select name="show_on_page" id="show_on_page" >
                    <option value="admin_side" <?php if( @get_post_meta($post_id,"show_on_page",true)=='admin_side'){ echo 'selected="selected"';}?>><?php _e('Admin side (Backend side) ',DOMAIN);?></option>
                    <option value="user_side" <?php if( @get_post_meta($post_id,"show_on_page",true)=='user_side'){ echo 'selected="selected"';}?>><?php _e('User side (Frontend side)',DOMAIN);?></option> 
                    <option value="both_side" <?php if( @get_post_meta($post_id,"show_on_page",true)=='both_side'){ echo 'selected="selected"';}?>><?php _e('Both',DOMAIN);?></option>
               </select>
               <p class="description"><?php _e('Choose where the field will display; to you (back-end), your visitors (front-end) or both.',DOMAIN);?></p>
          </td>
    </tr>
    
    <!-- Show Display Option -->
    <tr <?php if( @get_post_meta($post_id,"is_edit",true) == 'false'){?> style="display:none;" <?php }else{?> style="display:block;" <?php }?>>
    		<th><label for="display_option" class="form-textfield-label"><?php _e('Show the field in',DOMAIN);?></label></th>
          <td>
          	<fieldset>
               	<input type="checkbox" id="show_on_listing" name="show_on_listing" value="1" <?php if( @get_post_meta($post_id,"show_on_listing",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="show_on_listing" ><?php _e('Category page',DOMAIN);?></label><br />
                    
                    <input type="checkbox" name="show_on_detail" id="show_on_detail" value="1" <?php if( @get_post_meta($post_id,"show_on_detail",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="show_on_detail" ><?php _e('Detail page',DOMAIN);?></label><br />
                    <input type="checkbox" name="show_on_success" id="show_on_success" value="1" <?php if( @get_post_meta($post_id,"show_on_success",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="show_on_success" ><?php _e('Success page (the page that shows after submission)',DOMAIN);?></label><br />
                    <input type="checkbox" name="is_search" id="is_search" value="1" <?php if( @get_post_meta($post_id,"is_search",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="is_search" ><?php _e('Advanced search form',DOMAIN);?></label><br />
                    <input type="checkbox" name="show_in_column" id="show_in_column" value="1" <?php if( @get_post_meta($post_id,"show_in_column",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="show_in_column" ><?php _e('Back-end (as a column in listing areas, e.g. Posts -> All Posts)',DOMAIN);?></label><br />
                    
                    <input type="checkbox" name="show_in_email" id="show_in_email" value="1" <?php if( @get_post_meta($post_id,"show_in_email",true)=='1'){ echo 'checked="checked"';}?>/>&nbsp;<label for="show_in_email" ><?php _e('Confirmation email (sent after successful submission)',DOMAIN);?></label><br />
                    
               </fieldset>
          </td>
    </tr>
    <!--Finish Show Display Option -->
	<tr id="miscellaneous_options" >
		<th colspan="2">
			<div class="tevo_sub_title" style="margin-top:0px"><?php _e("Miscellaneous Options",DOMAIN);?></div>
		</th>
	</tr>
     <!-- css class start -->
     <tr style="display: block;" id="style_class_id">
          <th>
          	<label for="css_class" class="form-textfield-label"><?php _e('CSS class',DOMAIN);?></label>
          </th>
          <td>
               <input type="text" class="regular-text" name="style_class" id="style_class" value="<?php echo @get_post_meta($post_id,"style_class",true); ?>"></div>
               <p class="description"><?php _e('Apply a custom CSS class to the fields label. For more details on this <a href="http://templatic.com/docs/tevolution-guide/#adding_a_cf" title="Add New Custom Field" target="_blank">click here</a>',DOMAIN);?></p>
          </td>
     </tr>
     <!-- css class end -->
     
     <!-- extra prameters -->
     <tr style="display: block;" id="extra_parameter_id">
          <th>
          	<label for="extra_parameter" class="form-textfield-label"><?php _e('Extra parameter',DOMAIN);?></label>
          </th>
          <td>
               <input type="text" class="regular-text" name="extra_parameter" id="extra_parameter" value="<?php echo @get_post_meta($post_id,"extra_parameter",true); ?>"></div>
               <p class="description"><?php _e('Apply an extra parameter to the fields input part. For more information <a href="http://templatic.com/docs/tevolution-guide/#adding_a_cf" title="Add New Custom Field" target="_blank">click here</a>',DOMAIN);?></p>
          </td>
     </tr>
     <!-- extra perameters -->
     <tr style="display:block;">
          <td>
			<?php if(isset($_REQUEST['field_id'])): ?>
               	<input type="submit" name="submit-fields" value="<?php _e('Update changes',DOMAIN);?>" class="button-primary">
               <?php else: ?>
               	<input type="submit" name="submit-fields" value="<?php _e('Save all changes',DOMAIN);?>" class="button-primary"> 
               <?php endif; ?> 
          </td>		
     </tr>
   
	</tbody>
	</table>
</form>
</div>
<script type="text/javascript">
function show_option_add(htmltype){
	if(htmltype=='select' || htmltype=='multiselect' || htmltype=='radio' || htmltype=='multicheckbox')	{
		document.getElementById('ctype_option_tr_id').style.display='block';		
		if(htmltype!='select')
			document.getElementById('ctype_option_title_tr_id').style.display='block';
		else
			document.getElementById('ctype_option_title_tr_id').style.display='none';
	}else{
		document.getElementById('ctype_option_tr_id').style.display='none';	
		document.getElementById('ctype_option_title_tr_id').style.display='none';	
	}
	if(htmltype=='heading_type'){
		jQuery('#heading_type_id').hide();
		jQuery('#default_value_id').hide();
		jQuery('#is_require_id').hide();
		jQuery('#show_on_listing_id').hide();
		jQuery('#is_search_id').hide();
		jQuery('#show_in_column_id').hide();
		jQuery('#show_in_email_id').hide();
		jQuery('#field_require_desc_id').hide();
		jQuery('#validation_type_id').hide();
		jQuery('#style_class_id').hide();
		jQuery('#extra_parameter_id').hide();
		jQuery('#show_on_detail_id').hide();
		jQuery('#show_on_success_id').hide();
		jQuery('#show_on_column_id').hide();
		jQuery('#validation_options').hide();
		jQuery('#miscellaneous_options').hide();
		
	}else{
		<?php if(get_post_meta($post_id,"is_edit",true) == 'true' || get_post_meta($post_id,"is_edit",true) == ''){ ?>
		jQuery('#heading_type_id').show();
		jQuery('#default_value_id').show();
		jQuery('#is_require_id').show();
		jQuery('#show_on_listing_id').show();
		jQuery('#is_search_id').show();
		jQuery('#show_in_column_id').show();
		jQuery('#show_in_email_id').show();
		jQuery('#field_require_desc_id').show();
		jQuery('#validation_type_id').show();
		jQuery('#style_class_id').show();
		jQuery('#extra_parameter_id').show();
		jQuery('#show_on_detail_id').show();
		jQuery('#show_on_success_id').show();
		jQuery('#show_on_column_id').show();
		jQuery('#validation_options').show();
		jQuery('#miscellaneous_options').show();
		<?php } ?>
	}
	if(htmltype == 'image_uploader' || htmltype == 'upload')
	{
		jQuery('#show_in_email_id').hide();
	}
	if(htmltype == 'geo_map')
	 {
		 document.getElementById('htmlvar_name').value='address';
		 document.getElementById('html_var_name').style.display='none';
	 }
	else
	 {
		 document.getElementById('html_var_name').style.display='block';
		 //document.getElementById('htmlvar_name').value='';
	 }
	 
}
if(document.getElementById('ctype').value){
	show_option_add(document.getElementById('ctype').value)	;
}
</script>