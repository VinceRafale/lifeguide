<div class="wrap">
<div id="icon-edit" class="icon32"><br></div>
<h2><?php _e('Monetization',DOMAIN);?></h2>
<p class="tevolution_desc"><?php _e('Tevolution provides a lot of options using which you can monetize your site. You can charge your users for submitting their listing in different ways using price packages, we have a wide range of payment gateways using which you can charge your users. What&acute;s more ? You can even add discount coupon codes to give seasonal discount to your users.',DOMAIN); ?></p>
<?php if(@$message){?>
<div class="updated fade below-h2" id="message" style="padding:5px; font-size:12px; width:47%" >
  <?php echo $message;?>
</div>
<?php }?>
<div id="icon-options-general" class="icon32"><br></div>
	<h2 class="nav-tab-wrapper">
	<?php  
	  	$tab = '';
		if(isset($_REQUEST['tab']))
		{
			$tab = $_REQUEST['tab'];
		}
		$class = ' nav-tab-active'; ?>
	
	 <a id="packages_settings" class='nav-tab<?php if($tab == 'packages' || $tab == '' ) echo $class;  ?>' href='?page=monetization&tab=packages'><?php _e('Price Packages',DOMAIN); ?> </a>
	 <a id="payment_options_settings" class='nav-tab<?php if($tab == 'payment_options') echo $class;  ?>' href='?page=monetization&tab=payment_options'><?php _e('Payment Gateways',DOMAIN); ?> </a>
	 <a class='nav-tab<?php if($tab == 'manage_coupon') echo $class;  ?>' href='?page=monetization&tab=manage_coupon'><?php _e('Manage Coupons',DOMAIN); ?> </a>
    </h2>
	<?php
		if($tab == 'payment_options' )
		{ 
			/* to fetch current installed payment add-ons */
			payment_option_plugin_function();
		}
		elseif( $tab == 'manage_coupon'  )
		{
			manage_coupon_plugin_function();
		}
		else
		{
			if((isset($_REQUEST['action']) && $_REQUEST['action'] == 'add_package') || (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit'))
			 {
				 
				 include (TEMPL_MONETIZATION_PATH."add_price_packages.php");
			 }
			 else
			 {
				if($tab == 'packages' || $tab == ''){
				 include (TEMPL_MONETIZATION_PATH."price_packages_list.php"); }
			 }
		}		
?>
</div>