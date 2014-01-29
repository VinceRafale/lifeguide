<div class="wrap tevolution-table">
<div class="icon32" id="icon-index"><br/></div><h2><?php _e('Tevolution',DOMAIN);  tevolution_version();
?></h2>
<?php 
$tab = @$_REQUEST['tab'];
switch($tab){
	case 'overview':
		$oclass = "nav-tab-active";
		$title = __("Install add-ons",DOMAIN);
		$oaclass ="active";
		$sclass ='';
		$class ='';
		break;
    case 'setup-steps':
		$sclass = "nav-tab-active";
		$title = __("Setup steps",DOMAIN);
		$oclass="";
		$eclass='';
		$pclass='';
		$class="tevolution_setup_boxes";
		break;
	case 'extend':
		$eclass = "nav-tab-active";
		$title = __("Extend",DOMAIN);
		$eaclass ="active";
		$oclass='';
		$pclass='';
		$sclass="";
		$class="tevolution_setup_boxes";
		break; 	
	case 'payment-gateways':
		$pclass = "nav-tab-active";
		$title = __("Payment Gateways",DOMAIN);
		$class="tevolution_setup_boxes";
		$sclass="";
		$oclass='';
		$eclass='';
		break;
	case '':
		$oclass = "nav-tab-active";
		$title = __("Overview",DOMAIN);
		$eclass='';
		$class="";
		$pclass='';
		$sclass="";
		break;
} ?>
<h2 class="nav-tab-wrapper">
	<a href="?page=templatic_system_menu&amp;tab=overview" class="nav-tab <?php echo $oclass;  ?>"><?php _e('Install add-ons',DOMAIN); ?></a>
     <a href="?page=templatic_system_menu&amp;tab=extend" class="nav-tab <?php echo $eclass; ?>"><?php _e('Extend',DOMAIN); ?></a>
     <a href="?page=templatic_system_menu&amp;tab=payment-gateways" class="nav-tab <?php echo $pclass; ?>"><?php _e('Payment gateways',DOMAIN); ?></a>
	<a href="?page=templatic_system_menu&amp;tab=setup-steps" class="nav-tab <?php echo $sclass; ?>"><?php _e('Setup steps',DOMAIN); ?></a>
</h2>
<?php do_action('tevolution_plugin_list'); ?>
<div id="tevolution_bundled_boxes" class="<?php echo $class; ?>">