<?php
global $wp_query,$wpdb;
/**-- condition for activate booking system --**/
update_option('no_alive_days',1);
if(@$_REQUEST['activated'] == 'tevolution_location' && @$_REQUEST['true']==1)
{
	if(!is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{		
		$current = get_option( 'active_plugins' );
	     $plugin = plugin_basename( trim( 'Tevolution-LocationManager/location-manager.php') );	
	     if ( !in_array( $plugin, $current ) ) {
		   $current[] = $plugin;
		   sort( $current );		  
		   update_option( 'active_plugins', $current );		  
	     }
	}
	
	update_option('tevolution_location','Active');
	update_option('monetization','Active');
	update_option('currency_symbol','$');
	update_option('currency_code','USD');
	update_option('currency_pos','1');
	update_option('templatic-login','Active');		
}else if(isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'tevolution_location' && isset($_REQUEST['true']) && $_REQUEST['true']==0)
{
	delete_option('tevolution_location');
}
global $pagenow;
if($pagenow =='plugins.php'){
	 add_action('init','location_default_permalink_set');
}
/*
Function Name : location_default_permalink_set
Description : set permalink on set of new taxonomy
*/
function location_default_permalink_set(){
global $pagenow;
	if ( 'plugins.php' == $pagenow || 'themes.php' == $pagenow){ // Test if theme is activate
		//Set default permalink to postname start
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$wp_rewrite->flush_rules();
		if(function_exists('flush_rewrite_rules')){
		flush_rewrite_rules(true);  
		}
	//Set default permalink to postname end
	}
}
?>