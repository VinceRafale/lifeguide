<?php
/*
 * Add action for display the Digital Download bundle box in tevolution plugin dashboard.
 */
add_action('templconnector_bundle_box','add_tevolution_directory_bundle_box');
function add_tevolution_directory_bundle_box()
{
	$activated = @$_REQUEST['activated'];
	$deactivate = @$_REQUEST['deactivate'];
	if(function_exists('templatic_module_activationmsg'))
	{
		if($activated)		
			templatic_module_activationmsg('tevolution_directory','Tevolution Directory','',$mod_message='',$realted_mod =''); 
		else
			templatic_module_activationmsg('tevolution_directory','Tevolution Directory','',$mod_message='',$realted_mod =''); 	
	}
?>
    <div id="templatic_tevolution_directory" class="widget_div">
        <div title="Click to toggle" class="handlediv"></div>
            <h3 class="hndle"><span><?php _e('Tevolution Directory',DOMAIN); ?></span></h3>
        <div class="inside">
        	  <img class="dashboard_img" src="<?php echo TEVOLUTION_DIRECTORY_URL?>images/icon-directory.png" />
            <?php
            _e('A directory module from Templatic that helps you to enhance your website with some brilliant features like showing map, places, sorting places with the help of map etc. This feature gives you freedom and flexibility to add,sort and monitor your places from your back-end. ',DOMAIN);
            ?>
            <div class="clearfixb"></div>          
            <?php if(!is_active_addons('tevolution_directory')) :?>
            <div id="publishing-action">
                <a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&activated=tevolution_directory&true=1";?>" class="templatic-tooltip button-primary"><?php _e('Activate &rarr;',DOMAIN); ?></a>
            </div>
            <?php  endif;?>
    <?php  if (is_active_addons('tevolution_directory')) : ?>
            <div class="settings_style">
            <a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&deactivate=tevolution_directory&true=0";?>" class="deactive_lnk"><?php _e('Deactivate ',DOMAIN); ?></a>|
            <a class="templatic-tooltip set_lnk" href="<?php echo site_url()."/wp-admin/admin.php?page=directory_settings";?>"><?php _e('Settings',DOMAIN); ?></a>           
            </div>
    <?php endif; ?>
        </div>
    </div>
<?php
}
?>