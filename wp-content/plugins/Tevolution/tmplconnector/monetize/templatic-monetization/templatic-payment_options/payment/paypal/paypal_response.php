<?php
global $trans_id;
define('PAYPAL_MSG',__('Processing for Paypal, Please wait ....',DOMAIN));
$paymentOpts = templatic_get_payment_options($_REQUEST['paymentmethod']);
$merchantid = $paymentOpts['merchantid'];

if($_REQUEST['page'] == 'upgradenow'){
	$suburl ="&upgrade=pkg";
}

$returnUrl = site_url("/")."?ptype=return&pmethod=paypal&trans_id=".$trans_id.$suburl;
$cancel_return = site_url("/")."?ptype=cancel&pmethod=paypal&trans_id=".$trans_id;
$notify_url = site_url("/")."?ptype=notifyurl&pmethod=paypal&trans_id=".$trans_id;
$currency_code = templatic_get_currency_type();
global $payable_amount,$post_title,$last_postid;
$post = get_post($last_postid);
$post_title = $post->post_title;
$user_info = get_userdata($post->post_author);
$address1 = get_post_meta($post->post_author,'address');
$address2 = get_post_meta($post->post_author,'area');
$country = get_post_meta($post->post_author,'add_country');
$state = get_post_meta($post->post_author,'add_state');
$city = get_post_meta($post->post_author,'add_city');


$price_package_id=get_post_meta($last_postid,'package_select',true);
$package_amount=get_post_meta($price_package_id,'package_amount',true);
$validity=get_post_meta($price_package_id,'validity',true);
$validity_per=get_post_meta($price_package_id,'validity_per',true);
$recurring=get_post_meta($price_package_id,'recurring',true);
$billing_num=get_post_meta($price_package_id,'billing_num',true);
$billing_per=get_post_meta($price_package_id,'billing_per',true);
$billing_cycle=get_post_meta($price_package_id,'billing_cycle',true);
if($recurring==1){
	$c=$billing_num;
	if($billing_per=='M'){
		$rec_type=sprintf('%d Month', $c);
		$cycle= 'Month';
	}elseif($billing_per=='D'){
		$rec_type=sprintf('%d Week', $c/7);
		$cycle= 'Week';
	}else{
		$rec_type=sprintf('%d Year', $c);
		$cycle= 'Year';
	}
				
	$c_recurrence=$rec_type;
	//$c_duration='FOREVER';
	$c_duration=$billing_cycle.' '.$cycle;	
	
}
?>
<!--<form name="frm_payment_method" action="https://www.paypal.com/cgi-bin/webscr" method="post">-->
<form name="frm_payment_method" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="business" value="<?php echo $user_info->user_login; ?>">
<input name="address1" value="<?php echo $address1[0]; ?>" type="hidden">
<input name="address2" value="<?php echo $address2[0]; ?>" type="hidden">
<input name="first_name" value="<?php if($user_info->first_name){ echo $user_info->first_name; }else{ echo $user_info->user_login; } ?>" type="hidden">
<input name="middle_name" value="<?php echo $user_info->middle_name;; ?>" type="hidden">
<input name="last_name" value="<?php echo $user_info->last_name;; ?>" type="hidden">
<input name="lc" value="<?php echo ""; ?>" type="hidden">
<input name="country" value="<?php echo $country[0]; ?>" type="hidden">
<input name="state" value="<?php echo $state[0]; ?>" type="hidden">
<input name="city" value="<?php echo $city[0]; ?>" type="hidden">
<input name="on0" value="" type="hidden">
<?php if($recurring == '1') { ?>
<input type="hidden" value="<?php echo $payable_amount;?>" name="amount"/>
<input type="hidden" value="<?php echo $payable_amount;?>" name="a3"/>
<input type="hidden" value="<?php echo $billing_per;?>" name="t3"/>
<input type="hidden" value="<?php echo $billing_num;?>" name="p3"/>
<input type="hidden" value="<?php echo $billing_cycle;?>" name="srt"/>
<input type="hidden" value="1" name="src"/>
<input type="hidden" value="<?php echo $returnUrl;?>&pid=<?php echo $last_postid;?>&trans_id=<?php echo $trans_id; ?>" name="return"/>
<input type="hidden" value="<?php echo $cancel_return;?>&pid=<?php echo $last_postid;?>&trans_id=<?php echo $trans_id; ?>" name="cancel_return"/>
<input type="hidden" value="<?php echo $notify_url;?>" name="notify_url"/>
<input type="hidden" value="subscr_cancel" name="txn_type"/>
<input type="hidden" value="_xclick-subscriptions" name="cmd"/>
<?php }  else { ?>
<input type="hidden" value="<?php echo $payable_amount;?>" name="amount"/>
<input type="hidden" value="<?php echo $returnUrl;?>&pid=<?php echo $last_postid;?>&trans_id=<?php echo $trans_id; ?>" name="return"/>
<input type="hidden" value="<?php echo $cancel_return;?>&pid=<?php echo $last_postid;?>&trans_id=<?php echo $trans_id; ?>" name="cancel_return"/>
<input type="hidden" value="<?php echo $notify_url;?>" name="notify_url"/>
<input type="hidden" value="_xclick" name="cmd"/>
<?php }?>
<input type="hidden" value="<?php echo $post_title;?>" name="item_name"/>
<input type="hidden" value="<?php echo $merchantid;?>" name="business"/>
<input type="hidden" value="<?php echo $currency_code;?>" name="currency_code"/>
<input type="hidden" value="<?php echo $last_postid;?>" name="custom" />
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="no_shipping" value="1">
</form>
<div class="wrapper" >
<div class="clearfix container_message" style=" width:100%;text-align:center;">
	<h2 class="head2"><?php _e(PAYPAL_MSG);?></h2>
 </div>
</div>
<script>
setTimeout("document.frm_payment_method.submit()",50); 
</script> <?php exit;?>