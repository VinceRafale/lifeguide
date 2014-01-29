<?php
header('Content-Description: File Transfer');
header("Content-type: application/force-download");
header('Content-Disposition: inline; filename="transaction.csv"');
$file = dirname(__FILE__);
$content_dir = explode('/',WP_CONTENT_DIR);
$file = substr($file,0,stripos($file, $content_dir[1]));
require($file . "/wp-load.php");
session_start();
global $wpdb,$current_user,$transection_db_table_name,$qry_string;
_e("Title,Pay Date,Billing name,Pay Method,Amount\r\n",DOMAIN);
$transinfo = $wpdb->get_results($_SESSION['query_string']);
$totamt=0;
if($transinfo)
{
	foreach($transinfo as $priceinfoObj)
	{
		$totamt = $totamt + $priceinfoObj->payable_amt;
		$post_title = str_replace(',',' ',$priceinfoObj->post_title);
		$billing_add = str_replace(array(',','<br />'),' ',$priceinfoObj->billing_add);
		echo "$post_title,".date_i18n(get_option('date_format'),strtotime($priceinfoObj->payment_date)).",$priceinfoObj->billing_name,$priceinfoObj->payment_method,".tmpl_fetch_currency().number_format($priceinfoObj->payable_amt,2)." \r";
 }
echo " , , , Total Amount :, ".fetch_currency_with_position($totamt)."\r\n";

}else
{
_e("No record available",DOMAIN);

}?>  