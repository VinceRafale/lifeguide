<?php
define( 'DOING_AJAX', true );
require("../../../../../../wp-load.php");
?>
<ul>
<li>
    <input type="checkbox" name="selectall" id="selectall" class="checkbox" onclick="displaychk_frm();" />
    <label for="selectall">&nbsp;<?php _e('Select All',DOMAIN); ?></label>
</li>
<?php
if($_REQUEST['post_type'] == 'all' || $_REQUEST['post_type'] == 'all,')
{
	$custom_post_types_args = array();
	$custom_post_types = get_option("templatic_custom_post");
	get_wp_category_checklist_plugin('category','');
	foreach ($custom_post_types as $content_type=>$content_type_label) {
		@get_wp_category_checklist_plugin($content_type_label['slugs'][0],'');
	}
}
else
{
	$my_post_type = explode(",",substr($_REQUEST['post_type'],0,-1));
	//get_wp_category_checklist_plugin('category','');
	foreach($my_post_type as $_my_post_type)
	{
		@get_wp_category_checklist_plugin($_my_post_type,'');
	}
}
?>
</ul>