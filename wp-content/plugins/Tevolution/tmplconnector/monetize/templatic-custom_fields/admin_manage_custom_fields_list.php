<?php
/* Custom Fields Listing page */
if(@$_REQUEST['pagetype']=='delete')
{
	$postid = $_REQUEST['field_id'];
	wp_delete_post($postid);
	$url = site_url().'/wp-admin/admin.php';
	echo '<form action="'.$url.'" method="get" id="frm_custom_field" name="frm_custom_field">
	<input type="hidden" value="custom_fields" name="page"><input type="hidden" value="delsuccess" name="custom_field_msg">
	</form>
	<script>document.frm_custom_field.submit();</script>
	';exit;	
}
?>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h2><?php _e('Manage custom fields',DOMAIN);?> 
	<a id="add_custom_fields" class="add-new-h2" href="<?php echo site_url().'/wp-admin/admin.php?page=custom_fields&action=addnew';?>" title="<?php _e('Add custom field',DOMAIN);?>" name="btnviewlisting"/><?php _e('Add a custom field',DOMAIN); ?>
	</a></h2>
    
    <p class="tevolution_desc"><?php _e('Within this section you can define new fields for your website (mostly used for the submission forms). Created fields can be both category and post type specific. For more details on them <a href ="http://templatic.com/docs/tevolution-guide/#customfields" target="blank">click here</a>',DOMAIN);?></p>
	<p class="tevolution_desc"><?php _e('<b>Restrictions</b>',DOMAIN);?></p>
	<ul class="tevolution_list">
		<li><?php _e('Do not delete default fields that are automatically assigned to each new post type. Default fields include: Post category, Post title, Post content, Post excerpt and Post images.',DOMAIN);?></li>
		<li><?php _e('Display location for some default fields cannot be changed.',DOMAIN);?></li>
	</ul>
	<p class="tevolution_desc"><?php _e('<b>Quick Tips</b>',DOMAIN);?></p>
	<ul class="tevolution_list">
		<li><?php _e('You can change the "Display location" option for each field by just dragging them up and down on this page.',DOMAIN);?></li>
		<li><?php _e('To show more custom fields per page click on the "Screen Options" button in the top right corner.',DOMAIN);?></li>
		<li><?php _e('If you ever decide to start over and delete your created custom fields just click on the "Reset All Custom Fields" button below.',DOMAIN);?></li>
	</ul>
    <form action="" method="post">
    	<input type="hidden" name="custom_reset"  value="1" />
		 <input type="submit" name="reset_custom_fields" value="<?php _e('Reset All Custom Fields',DOMAIN);?>" class="button-primary reset_custom_fields" />
    </form>
	
	<?php if(isset($_REQUEST['custom_field_msg']))
	{?>
		<div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px;" >
			<?php if($_REQUEST['custom_field_msg']=='delsuccess'){
					_e('Custom field deleted successfully.',DOMAIN);	
				} if($_REQUEST['custom_field_msg']=='success'){
					if($_REQUEST['custom_msg_type']=='add') {
						_e('Custom field created successfully.',DOMAIN);
					} else {
						_e('Custom field updated successfully.',DOMAIN);
					}
				}
			?>
		</div>
	<?php }
	wp_enqueue_script( 'jquery-ui-sortable' );?>
    <form name="post_custom_fields" id="post_custom_fields" action="" method="post">
		<?php
			$custom_fields_list_table = new custom_fields_list_table();
			$custom_fields_list_table->prepare_items();
			$custom_fields_list_table->search_box('search', 'search_field');
			$custom_fields_list_table->display();
		?>
	</form>    
</div>
<?php
?>