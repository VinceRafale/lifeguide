<?php 
$activated = @$_REQUEST['activated'];
$deactivate = @$_REQUEST['deactivate'];
if($activated){
templatic_module_activationmsg('bulk_upload','Bulk Upload','',$mod_message=__('Now move on to the',DOMAIN).' <a href='.admin_url('/admin.php?page=bulk_upload').'>'.__('Bulk upload',DOMAIN).'</a> '.__('section to import/export .csv files.',DOMAIN),$realted_mod =''); 
}else{
templatic_module_activationmsg('bulk_upload','Bulk Upload','',$mod_message='',$realted_mod =''); 
}?>
<div id="templatic_bulkupload" class="widget_div">
	
	<div class="t_dashboard_icon"><img class="dashboard_img" id="bulk_image" src = "<?php echo TEMPL_PLUGIN_URL.'tmplconnector/monetize/images/bulk.png'; ?>" /></div>
	<div class="inside">
		<div class="t_module_desc">
         <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=bulk_upload"><h3 class="hndle"><span><?php _e('Bulk Import / Export',DOMAIN); ?></span></h3></a>
        <p class="mod_desc"><?php
		_e('Use this feature to import .csv content from other sites. If the theme you&lsquo;re using doesn&lsquo;t support .csv exports connect to your database and export wp_posts table content into a .csv file.',DOMAIN);
		?></p>
		</div>
        
		<?php if(!is_active_addons('bulk_upload')) { ?>
		<div id="publishing-action" class="settings_style">
			<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&activated=bulk_upload&true=1";?>" class=" button-primary"><i class="fa fa-check"></i>&nbsp;<?php _e('Activate &rarr;',DOMAIN); ?></a>
		</div>
		<?php } 
		if (is_active_addons('bulk_upload')) : ?>
		<div id="publishing-action" class="settings_style">
				<a class="templatic-tooltip set_lnk" href="<?php echo site_url()."/wp-admin/admin.php?page=bulk_upload";?>"><i class="fa fa-gear"></i></a>
				<a href="<?php echo site_url()."/wp-admin/admin.php?page=templatic_system_menu&deactivate=bulk_upload&true=0";?>" class="button"><i class="fa fa-times"></i>&nbsp;<?php _e('Deactivate ',DOMAIN); ?></a> 
		</div><?php endif; ?>
	</div>
</div>