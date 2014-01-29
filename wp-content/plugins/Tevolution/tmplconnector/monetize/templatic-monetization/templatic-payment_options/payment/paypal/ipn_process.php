<?php
/*
NAME : IPN PROCESS FILE FOR PAYPAL
DESCRIPTION : THIS FILE WILL BE CALLED ON SUCCESSFUL PAYMENT VIA PAYPAL. THE CODE MENTIONED IN THIS FILE WILL FETCH THE POSTED EVENT DATA AND ACCORDINGLY IT WILL SEND EMAIL TO THE ADMIN AS WELL AS THE USER.
*/
global $wpdb;
$url = 'https://www.paypal.com/cgi-bin/webscr';
$postdata = '';
foreach($_POST as $i => $v) 
{
	$postdata .= $i.'='.urlencode($v).'&amp;';
}
$postdata .= 'cmd=_notify-validate';
 
$web = parse_url($url);
if ($web['scheme'] == 'https') 
{
	$web['port'] = 443;
	$ssl = 'ssl://';
} 
else 
{
	$web['port'] = 80;
	$ssl = '';
}
$fp = @fsockopen($ssl.$web['host'], $web['port'], $errnum, $errstr, 30);
 
if (!$fp) 
{
	echo $errnum.': '.$errstr;
}
else
{
	fputs($fp, "POST ".$web['path']." HTTP/1.1\r\n");
	fputs($fp, "Host: ".$web['host']."\r\n");
	fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	fputs($fp, "Content-length: ".strlen($postdata)."\r\n");
	fputs($fp, "Connection: close\r\n\r\n");
	fputs($fp, $postdata . "\r\n\r\n");
 
	while(!feof($fp)) 
	{
		$info[] = @fgets($fp, 1024);
	}
	fclose($fp);
	$info = implode(',', $info);
	if (eregi('VERIFIED', $info)) 
	{
		$to = get_site_emailId_plugin();
		
		// yes valid, f.e. change payment status
		$postid = $_POST['custom'];
		$item_name = $_POST['item_name'];
		$txn_id = $_POST['txn_id'];
		$payment_status       = $_POST['payment_status'];
		$payment_type         = $_POST['payment_type'];
		$payment_date         = date_i18n(get_option('date_format'),strtotime($_POST['payment_date']));
		$txn_type             = $_POST['txn_type'];
		
		/*$post_default_status = get_property_default_status();
		if($post_default_status=='')
		{
			$post_default_status = 'publish';
		}
		set_property_status($postid,$post_default_status);*/
		
		/*updating transaction table start*/
		
		global $wpdb,$transection_db_table_name,$current_user;
		$user_id = $current_user->ID;
		$sql = "select max(trans_id) as trans_id,status from $transection_db_table_name where user_id = $user_id and status=0";
		$sql_data = $wpdb->get_row($sql);
		$wpdb->query("update $transection_db_table_name set status=1 where trans_id=$sql_data->trans_id");	
		$wpdb->query("UPDATE $wpdb->posts SET post_status='".fetch_posts_default_paid_status()."' where ID = '".$postid."'");
	
		/*updating transaction table ends here*/
	
		$transaction_details .= "--------------------------------------------------\r";
		$transaction_details .= "Payment Details for Place Listing ID #$postid\r";
		$transaction_details .= "--------------------------------------------------\r";
		$transaction_details .= " Place Listing Title: $item_name \r";
		$transaction_details .= "--------------------------------------------------\r";
		$transaction_details .= "Trans ID: $txn_id\r";
		$transaction_details .= "  Status: $payment_status\r";
		$transaction_details .= "    Type: $payment_type\r";
		$transaction_details .= "  Date: $payment_date\r";
		$transaction_details .= "  Method: $txn_type\r";
		$transaction_details .= "--------------------------------------------------\r";
		$transaction_details = __($transaction_details,DOMAIN);
		$subject = get_option('post_payment_success_admin_email_subject');
		if(!$subject)
		{
			$subject = __("Place Listing Submitted and Payment Success Confirmation Email",DOMAIN);
		}
		$content = get_option('post_payment_success_admin_email_content');
		$store_name = get_option('blogname');
		$fromEmail = 'Admin';
		$search_array = array('[#to_name#]','[#information_details#]','[#site_name#]');
		$replace_array = array($fromEmail,$transaction_details,$store_name);
		$filecontent = str_replace($search_array,$replace_array,$content);
		@mail($to,$subject,$filecontent,$headerarr); // email to admin
		
		$productinfosql = "select ID,post_title,guid,post_author from $wpdb->posts where ID = $postid";
		$productinfo = $wpdb->get_results($productinfosql);
		foreach($productinfo as $productinfoObj)
		{
			$post_link = site_url().'/?ptype=preview&alook=1&pid='.$postid;
			$post_title = '<a href="'.$post_link.'">'.stripslashes($productinfoObj->post_title).'</a>'; 
			$aid = $productinfoObj->post_author;
			$userInfo = get_author_info($aid);
			$to_name = $userInfo->user_nicename;
			$to_email = $userInfo->user_email;
			$user_email = $userInfo->user_email;
		}
		$transaction_details .= "Information Submitted URL\r";
		$transaction_details .= "--------------------------------------------------\r";
		$transaction_details .= "  $post_title\r";
		$transaction_details = __($transaction_details,DOMAIN);
		
		$subject = get_option('post_payment_success_client_email_subject');
		if(!$subject)
		{
			$subject = __("Place Listing Submitted and Payment Success Confirmation Email",DOMAIN);
		}
		$content = get_option('post_payment_success_client_email_content');
		$store_name = get_option('blogname');
		$search_array = array('[#to_name#]','[#information_details#]','[#site_name#]');
		$replace_array = array($to_name,$transaction_details,$store_name);
		$content = str_replace($search_array,$replace_array,$content);
		
		@mail($user_email,$subject,$content,$headerarr);// email to client
	}
	else
	{
		// invalid, log error or something
	}
}
?>