<?php
global $wp_query,$wpdb;
/* Add action 'templatic_general_setting_data' for display rating*/
add_action('after_detail_page_setting','rating_setting_data',12);
/*
 * Function Name: rating_setting_data;
 * Argument: add rating option in tevolution 
 */
function rating_setting_data($column)
{
	$tmpdata = get_option('templatic_settings');
	if(!is_plugin_active('Templatic-MultiRating/multiple_rating.php')){
	?>               
		<tr>
			<th><?php _e('Show ratings',DOMAIN);?></th>
			<td>
				<label for="rating_yes"><input id="rating_yes" type="checkbox" name="templatin_rating" value="yes" <?php if($tmpdata['templatin_rating']=='yes')echo 'checked';?> />&nbsp;<?php _e('Enable',DOMAIN);?></label><br />                        
				<p class="description"><?php _e('Once enabled, the comment form will allow visitors to leave a star rating on their reviews. For more comprehensive ratings check out the <a href="http://templatic.com/directory-add-ons/star-rating-plugin-multirating/" title="Multi Rating" target="_blank"> Multi Rating add-on </a>',DOMAIN); ?></p>
			</td>
		</tr>
    <?php
	}
}
$tmpdata = get_option('templatic_settings');
if($tmpdata){
	if(isset($tmpdata['templatin_rating']) && $tmpdata['templatin_rating']=='yes')
	{
		if(file_exists(TEMPL_MONETIZE_FOLDER_PATH . 'templatic-ratings/templatic_post_rating.php'))
		{
			include_once (TEMPL_MONETIZE_FOLDER_PATH . 'templatic-ratings/templatic_post_rating.php');
		}
		if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.'templatic-ratings/language.php'))
		{
			include (TEMPL_MONETIZE_FOLDER_PATH . "templatic-ratings/language.php");
		}
	}
}
?>