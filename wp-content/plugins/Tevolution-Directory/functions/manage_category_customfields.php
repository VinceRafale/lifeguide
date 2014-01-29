<?php
/*
 * Add The listing custom categories fields for 
 */	 
add_action( 'init', 'directory_category_custom_field');
function directory_category_custom_field()
{
	global $wpdb;	
	
	/*
	 * created and edit the listing custom post type category custom field store in term table
	 */
	
	add_action('edited_listingcategory','directory_custom_fields_AlterFields');
	add_action('created_listingcategory','directory_custom_fields_AlterFields');
	/*add_action('edited_listingtags','directory_custom_fields_AlterFields');
	add_action('created_listingtags','directory_custom_fields_AlterFields');*/
	
	add_filter('manage_listingcategory_custom_column', 'manage_directory_category_columns', 10, 3);
	//add_filter('manage_listingtags_custom_column', 'manage_directory_category_columns', 10, 3);
	add_filter('manage_edit-listingcategory_columns', 'directory_category_columns');
	//add_filter('manage_edit-listingtags_columns', 'directory_category_columns');
	/*
	 * created and edit the event custom post type category custom field store in term table
	 */
	//if(isset($_GET['taxonomy']) && ($_GET['taxonomy']== 'listingcategory' || $_GET['taxonomy']== 'listingtags')) 
	if(isset($_GET['taxonomy']) && ($_GET['taxonomy']== 'listingcategory')) 
	{
		$taxnow=$_GET['taxonomy'];
		add_action($taxnow.'_edit_form_fields','directory_custom_fields_EditFields',11);
		add_action($taxnow.'_add_form_fields','directory_custom_fields_AddFieldsAction',11);		
	}
}
function directory_custom_fields_EditFields($tag)
{
	directory_custom_fields_AddFields($tag,'edit');	
}
function directory_custom_fields_AddFieldsAction($tag)
{
	directory_custom_fields_AddFields($tag,'add');
}
/*
 * Function Name: directory_custom_fields_AddFields
 * display custom field in event and listing category page
 */
function directory_custom_fields_AddFields($tag,$screen)
{	
	$tax = @$tag->taxonomy;
	?>
     	<div class="form-field-category">
		<tr class="form-field form-field-category">
			<th scope="row" valign="top"><label for="cat_icon"><?php _e("Map Marker", DOMAIN); ?></label></th>
			<td> 
                    <input id="cat_icon" type="text" size="60" name="cat_icon" value="<?php echo (@$tag->term_icon)? @$tag->term_icon:''; ?>"/>	
                    <?php _e('Or',DOMAIN);?>
                    <a class="button upload_button" title="Add city background image" id="cat_icon" data-editor="cat_upload_icon" href="#">
                    <span class="wp-media-buttons-icon"></span><?php _e('Browse',DOMAIN);?>	</a>		
                    <p class="description"><?php _e('It will appear on the homepage Google map for listings placed in this category. ',DOMAIN);?></p>    
			</td>
		</tr>
		</div>
	<?php
}
/*
 * Function Name: directory_custom_fields_AlterFields
 * add/ edit listing and event custom taxonomy custom field 
 */
function directory_custom_fields_AlterFields($termId)
{
	global $wpdb;
	$term_table=$wpdb->prefix."terms";		
	$cat_icon=$_POST['cat_icon'];		
	//update the service price value in terms table field	
	if(isset($_POST['cat_icon']) && $_POST['cat_icon']!=''){
		$sql="update $term_table set term_icon='".$cat_icon."' where term_id=".$termId;
		$wpdb->query($sql);
	}
	
}
/*
 * Function Name: directory_category_columns
 * manage columns for event and listing custom taxonomy
 */
function directory_category_columns($columns)
{
	$columns['icon'] = __('Map Marker',DOMAIN);	
	if(isset($_GET['taxonomy']) && $_GET['taxonomy']=='ecategory')
		$columns['posts'] = __('Events',DOMAIN);
	if(isset($_GET['taxonomy']) && $_GET['taxonomy']=='listingcategory')
		$columns['posts'] = __('Listings',DOMAIN);
	
	return $columns;	
}
/*
 * Function Name: manage_directory_category_columns
 * display listing and event custom taxonomy custom field display in category columns
 */
function manage_directory_category_columns($out, $column_name, $term_id){
	global $wpdb;
	$term_table=$wpdb->prefix."terms";		
	$sql="select * from $term_table where term_id=".$term_id;
	$term=$wpdb->get_results($sql);	
	
	switch ($column_name) {
		case 'icon':					
				 $out= ($term[0]->term_icon)?'<img src="'.$term[0]->term_icon.'" >':'<img src="'.TEVOLUTION_DIRECTORY_URL.'images/pin.png" >';
			break; 
		default:
			break;
	}
	return $out;	
}
add_filter( 'manage_edit-listing_sortable_columns', 'templatic_edit_listing_columns',11 ) ;
function templatic_edit_listing_columns( $columns ) {
	$columns['address'] = 'address';	
	$columns['posted_on'] = 'posted_on';
	return $columns;
}
?>