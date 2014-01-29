<?php
/*
NAME : SUBMIT EVENT CANCELLATION FILE
DESCRIPTION : THIS FILE WILL BE CALLED IF THE USER CANCEL THE PROCESS OF SUBMITTING EVENT.
*/
$page_title = PAY_CANCELATION_TITLE;
global $page_title;?>
<?php get_header(); ?>
<?php if ( get_option( 'ptthemes_breadcrumbs' ) == 'Yes') {  ?>
<div class="breadcrumb_in"><a href="<?php echo site_url(); ?>"><?php _e('Home'); ?></a> &raquo; <?php echo $page_title; ?></div><?php } ?>
<div class="content-title"><?php echo $page_title; ?></div>
<div class="post-content">
<?php 
$filecontent = stripslashes(get_option('post_payment_cancel_msg_content'));
if(!$filecontent)
{
	$filecontent = PAY_CANCEL_MSG;
}
$store_name = '<a href="'.site_url().'">'.get_option('blogname').'</a>';
$search_array = array('[#site_name#]','[#admin_email#]');
$replace_array = array($store_name,get_option('admin_email'));
$filecontent = str_replace($search_array,$replace_array,$filecontent);
echo $filecontent;
?> 
</div> <!-- content #end -->
<div id="sidebar">
<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>