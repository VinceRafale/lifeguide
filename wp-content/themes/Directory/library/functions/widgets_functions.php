<?php
/*
Name : supreme_sidebar_after_content
Description : Displays widgets for the After Content dynamic sidebar if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_sidebar_after_content(){
		
	if ( is_active_sidebar( 'after-content' ) ) : ?>
<?php do_action( 'after_sidebar_after_content' ); // supreme_after_sidebar_after_content ?>
<div id="sidebar-after-content" class="sidebar sidebar-inter-content">
  <?php do_action( 'open_sidebar_after_content' ); // supreme_open_sidebar_after_content 
		dynamic_sidebar( 'after-content' ); 
		do_action( 'close_sidebar_after_content' ); // supreme_close_sidebar_after_content ?>
</div>
<!-- #sidebar-after-content -->
<?php do_action( 'after_sidebar_after_content' ); // supreme_after_sidebar_after_content 
	
	endif; 
}
/*
Name : supreme_sidebar_after_header
Description : Displays widgets for the After header dynamic sidebar if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_sidebar_after_header(){
		
	if ( is_active_sidebar( 'after-header' ) ) : 
		do_action( 'before_sidebar_after_header' ); // supreme_before_sidebar_after_header ?>
<div id="sidebar-after-header" class="sidebar sidebar-1c sidebar-after-header clearfix">
  <div class="sidebar-wrap">
    <?php do_action( 'open_sidebar_after_header' ); // supreme_open_sidebar_after_header 
			dynamic_sidebar( 'after-header' ); 
			do_action( 'close_sidebar_after_header' ); // supreme_close_sidebar_after_header ?>
  </div>
  <!-- .sidebar-wrap -->
</div>
<!-- #sidebar-after-header -->
<?php do_action( 'after_sidebar_after_header' ); // supreme_after_sidebar_after_header 
	endif; 
}
/*
Name : supreme_sidebar_after_singular
Description : Displays widgets for the After singular post dynamic sidebar if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_sidebar_after_singular(){
		
	if ( is_active_sidebar( 'after-singular' ) ) : ?>
<?php do_action( 'after_sidebar_after_singular' ); // supreme_after_sidebar_after_singular ?>
<div id="sidebar-after-singular" class="sidebar sidebar-inter-content">
  <?php do_action( 'open_sidebar_after_singular' ); // supreme_open_sidebar_after_singular ?>
  <?php dynamic_sidebar( 'after-singular' ); ?>
  <?php do_action( 'close_sidebar_after_singular' ); // supreme_close_sidebar_after_singular ?>
</div>
<!-- #sidebar-after-singular -->
<?php do_action( 'after_sidebar_after_singular' ); // supreme_after_sidebar_after_singular 
	endif; 
}
/*
Name : supreme_sidebar_before_content
Description : Displays widgets for the before content dynamic sidebar if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_sidebar_before_content(){
		
	if ( is_active_sidebar( 'before-content' ) ) : ?>
<?php do_action( 'before_sidebar_before_content' ); // supreme_before_sidebar_before_content ?>
<div id="sidebar-before-content" class="sidebar sidebar-inter-content">
  <?php do_action( 'open_sidebar_before_content' ); // supreme_open_sidebar_before_content 
		 dynamic_sidebar( 'before-content' ); 
		 do_action( 'close_sidebar_before_content' ); // supreme_close_sidebar_before_content ?>
</div>
<!-- #sidebar-before-content -->
<?php do_action( 'after_sidebar_before_content' ); // supreme_after_sidebar_before_content 
	endif; 
}
/*
Name : supreme_sidebar_entry
Description : Displays widgets for the before content and after title on first listing of listing page dynamic sidebar if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_sidebar_entry(){
		
	if ( is_active_sidebar( 'entry' ) ) :
	do_action( 'before_sidebar_entry' ); // rainbow_before_sidebar_entry ?>
<div id="sidebar-entry" class="sidebar">
  <?php do_action( 'open_sidebar_entry' ); // rainbow_open_sidebar_entry
		dynamic_sidebar( 'entry' );
		do_action( 'close_sidebar_entry' ); // rainbow_close_sidebar_entry ?>
</div>
<!-- #sidebar-entry -->
<?php do_action( 'after_sidebar_entry' ); // rainbow_after_sidebar_entry
	endif; 
}
/*
Name : supreme_header_sidebar
Description : Displays widgets for header dynamic sidebar if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_header_sidebar(){
		
	if ( is_active_sidebar( 'header' ) ) : ?>
<!-- #sidebar-header right start -->
<?php do_action( 'before_sidebar_header' ); // supreme_before_sidebar_header ?>
<div id="sidebar-header" class="sidebar">
  <?php do_action( 'open_sidebar_header' ); // supreme_open_sidebar_header 
				
				dynamic_sidebar( 'header' );
				
				do_action( 'close_sidebar_header' ); // supreme_close_sidebar_header ?>
</div>
<!-- #sidebar-header right end -->
<?php do_action( 'after_sidebar_header' ); // supreme_after_sidebar_header 
	endif;
}
/*
Name : supreme_front_page_sidebar
Description : Displays widgets in post listing  page sidebar area if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_front_page_sidebar(){
	
	if ( is_active_sidebar( 'front-page-sidebar' ) ) : ?>
<?php do_action( 'before_front-page-sidebar' ); // supreme_before_sidebar_primary ?>
<aside id="sidebar-front_page" class="front-page-sidebar sidebar">
  <?php do_action( 'open_front-page-sidebar' ); // supreme_open_sidebar_primary
			dynamic_sidebar( 'front-page-sidebar' ); 
			do_action( 'close_front-page-sidebar' ); // supreme_close_sidebar_primary ?>
</aside>
<!-- #sidebar-front-page-sidebar -->
<?php do_action( 'after_front-page-sidebar' ); // supreme_after_sidebar_primary 
	else:
		if(!supreme_is_layout1c())
				get_sidebar();
	endif;
}
/*
Name : supreme_post_detail_sidebar
Description : Displays widgets in post detail page sidebar area if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_post_detail_sidebar(){
	
	if ( is_active_sidebar( 'post-detail-sidebar' ) ) : ?>
<?php do_action( 'before_post-detail-sidebar' ); // supreme_before_sidebar_primary ?>
<aside id="sidebar-post-detail" class="post-detail-sidebar sidebar">
  <?php do_action( 'open_post-detail-sidebar' ); // supreme_open_sidebar_primary
			dynamic_sidebar( 'post-detail-sidebar' ); 
			do_action( 'close_post-detail-sidebar' ); // supreme_close_sidebar_primary ?>
</aside>
<!-- #sidebar-front-page-sidebar -->
<?php do_action( 'after_post-detail-sidebar' ); // supreme_after_sidebar_primary 
	else:
		if(!supreme_is_layout1c())
		 get_sidebar();
	endif;
}
/*
Name : supreme_post_listing_sidebar
Description : Displays widgets in Post/Blog Listing page sidebar area if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_post_listing_sidebar(){
	
	if ( is_active_sidebar( 'post-listing-sidebar' ) ) : ?>
<?php do_action( 'before_post-lisitng-sidebar' ); // supreme_before_sidebar_primary ?>
<aside id="sidebar-post-listing" class="post-listing-sidebar sidebar">
  <?php do_action( 'open_post-listing-sidebar' ); // supreme_open_sidebar_primary
			dynamic_sidebar( 'post-listing-sidebar' ); 
			do_action( 'close_post-listing-sidebar' ); // supreme_close_sidebar_primary ?>
</aside>
<!-- #sidebar-front-page-sidebar -->
<?php do_action( 'after_post-listing-sidebar' ); // supreme_after_sidebar_primary 
	else:
		if(!supreme_is_layout1c())
			get_sidebar();
	endif;
}
/*
Name : supreme_primary_sidebar
Description : Displays widgets in sidebar area if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_primary_sidebar(){
	if ( is_active_sidebar( 'primary-sidebar' ) ) : ?>
<?php do_action( 'before_sidebar_primary' ); // supreme_before_sidebar_primary ?>
<aside id="sidebar-primary" class="sidebar">
  <?php do_action( 'open_sidebar_primary' ); // supreme_open_sidebar_primary
			dynamic_sidebar( 'primary-sidebar' ); 
			do_action( 'close_sidebar_primary' ); // supreme_close_sidebar_primary ?>
</aside>
<!-- #sidebar-primary -->
<?php do_action( 'after_sidebar_primary' ); // supreme_after_sidebar_primary 
	endif;
}
/*
Name : supreme_subsidiary_sidebar
Description : Displays widgets in subsidiary area ,above footer if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_subsidiary_sidebar(){
	
	if ( is_active_sidebar( 'subsidiary' ) ) :
	do_action( 'before_sidebar_subsidiary' ); // supreme_before_sidebar_subsidiary ?>
<div id="sidebar-subsidiary" class="sidebar sidebar-1c sidebar-subsidiary clearfix">
  <div class="sidebar-wrap">
    <?php do_action( 'open_sidebar_subsidiary' ); // supreme_open_sidebar_subsidiary 
			dynamic_sidebar( 'subsidiary' );
			do_action( 'close_sidebar_subsidiary' ); // supreme_close_sidebar_subsidiary ?>
  </div>
  <!-- .sidebar-wrap -->
</div>
<!-- #sidebar-subsidiary -->
<?php do_action( 'after_sidebar_subsidiary' ); // supreme_after_sidebar_subsidiary 
	endif;
}
/*
Name : supreme_subsidiary_2c_sidebar
Description : Displays widgets in subsidiary two column ,above footer if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_subsidiary_2c_sidebar(){
	
	if ( is_active_sidebar( 'subsidiary-2c' ) ) :
	do_action( 'before_sidebar_subsidiary_2c' ); // supreme_before_sidebar_subsidiary_2c ?>
<div id="sidebar-subsidiary-2c" class="sidebar sidebar-2c sidebar-subsidiary clearfix">
  <div class="sidebar-wrap">
    <?php do_action( 'open_sidebar_subsidiary_2c' ); // supreme_open_sidebar_subsidiary_2c 
			dynamic_sidebar( 'subsidiary-2c' );
			do_action( 'close_sidebar_subsidiary_2c' ); // supreme_close_sidebar_subsidiary_2c ?>
  </div>
  <!-- .sidebar-wrap -->
</div>
<!-- #sidebar-subsidiary-2c -->
<?php do_action( 'after_sidebar_subsidiary_2c' ); // supreme_after_sidebar_subsidiary_2c
	endif;
}
/*
Name : supreme_subsidiary_3c_sidebar
Description : Displays widgets in subsidiary three column ,above footer if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_subsidiary_3c_sidebar(){
	
	if ( is_active_sidebar( 'subsidiary-3c' ) ) :
	do_action( 'before_sidebar_subsidiary_3c' ); // supreme_before_sidebar_subsidiary_3c ?>
<div id="sidebar-subsidiary-3c" class="sidebar sidebar-3c sidebar-subsidiary clearfix">
  <div class="sidebar-wrap">
    <?php do_action( 'open_sidebar_subsidiary_3c' ); // supreme_open_sidebar_subsidiary_3c 
			dynamic_sidebar( 'subsidiary-3c' );
			do_action( 'close_sidebar_subsidiary_3c' ); // supreme_close_sidebar_subsidiary_3c ?>
  </div>
  <!-- .sidebar-wrap -->
</div>
<!-- #sidebar-subsidiary-3c -->
<?php do_action( 'after_sidebar_subsidiary_3c' ); // supreme_after_sidebar_subsidiary_3c
	endif;
}
/*
Name : supreme_subsidiary_4c_sidebar
Description : Displays widgets in subsidiary four column ,above footer if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_subsidiary_4c_sidebar(){
	
	if ( is_active_sidebar( 'subsidiary-4c' ) ) :
	do_action( 'before_sidebar_subsidiary_4c' ); // supreme_before_sidebar_subsidiary_4c ?>
<div id="sidebar-subsidiary-4c" class="sidebar sidebar-4c sidebar-subsidiary">
  <div class="sidebar-wrap">
    <?php do_action( 'open_sidebar_subsidiary_4c' ); // supreme_open_sidebar_subsidiary_4c
			dynamic_sidebar( 'subsidiary-4c' );
			do_action( 'close_sidebar_subsidiary_4c' ); // supreme_close_sidebar_subsidiary_4c ?>
  </div>
  <!-- .sidebar-wrap -->
</div>
<!-- #sidebar-subsidiary-4c -->
<?php do_action( 'after_sidebar_subsidiary_4c' ); // supreme_after_sidebar_subsidiary_4c 
	endif;
}
/*
Name : supreme_widget_template
Description : Displays widgets in subsidiary four column ,above footer if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_widget_template(){
	
if ( is_active_sidebar( 'widgets-template' ) ) :
	do_action( 'before_sidebar_widgets_template' ); // supreme_before_sidebar_widgets_template ?>
<div id="sidebar-widgets-template" class="sidebar">
  <?php do_action( 'open_sidebar_widgets_template' ); // supreme_open_sidebar_widgets_template
		dynamic_sidebar( 'widgets-template' );
		do_action( 'close_sidebar_widgets_template' ); // supreme_close_sidebar_widgets_template ?>
</div>
<!-- #sidebar-widgets-template -->
<?php do_action( 'after_sidebar_widgets_template' ); // supreme_after_sidebar_widgets_template 
	endif;
}
/*
Name : supreme_contact_page_sidebar
Description : Displays widgets in contact page sidebar, if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_contact_page_sidebar(){
	
if ( is_active_sidebar( 'contact_page_sidebar' ) ) :
	do_action( 'before_contact_page_sidebar' ); // supreme_before_sidebar_widgets_template ?>
<aside id="sidebar-contact_page_sidebar" class="sidebar">
  <?php do_action( 'open_sidebar_widgets_template' ); // supreme_open_sidebar_widgets_template
		dynamic_sidebar('contact_page_sidebar' );
		do_action( 'close_sidebar_widgets_template' ); // supreme_close_sidebar_widgets_template ?>
</aside>
<!-- #sidebar-widgets-template -->
<?php do_action( 'after_contact_page_sidebar' ); // supreme_after_sidebar_widgets_template 
	else:
		get_sidebar();
	endif;
}
/*
Name : supreme_widget_template
Description : Displays widgets in subsidiary four column ,above footer if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_contact_page_widget(){
	
if ( is_active_sidebar( 'contact_page_widget' ) ) : ?>
<div class="cont_wid_area clearfix">
  <?php
	do_action( 'before_contact_page_widget' ); // supreme_before_sidebar_widgets_template
		
		do_action( 'open_contact_page_widget' ); // supreme_open_sidebar_widgets_template
		dynamic_sidebar( 'contact_page_widget' );
		do_action( 'close_contact_page_widget' ); // supreme_close_sidebar_widgets_template
	do_action( 'after_contact_page_widget' ); // supreme_after_sidebar_widgets_template ?>
</div>
<?php
	endif;
} 
/*
Name : supreme_woocommerce_sidebar
Description : Displays widgets in post listing  page sidebar area if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_woocommerce_sidebar(){
	
	if ( is_active_sidebar( 'supreme_woocommerce' ) && !supreme_is_layout1c() ) : ?>
<?php do_action( 'before_woo-sidebar' ); // supreme_before_sidebar_primary ?>
<aside id="sidebar-woo_page" class="woo-page-sidebar sidebar">
  <?php do_action( 'open_front-page-sidebar' ); // supreme_open_sidebar_primary
			dynamic_sidebar( 'supreme_woocommerce' ); 
			do_action( 'close_woo-sidebar' ); // supreme_close_sidebar_primary ?>
</aside>
<!-- #sidebar-front-page-sidebar -->
<?php do_action( 'after_woo-sidebar' ); // supreme_after_sidebar_primary 
	else:
		if(!supreme_is_layout1c())
				get_sidebar();
	endif;
}
/*
Name : supreme_footer_widgets
Description : Displays widgets in footer.
*/
function supreme_footer_widgets(){
	
	if ( is_active_sidebar( 'footer' ) ) : 
		do_action( 'before_footer' ); // supreme_before_sidebar_primary 
			dynamic_sidebar( 'footer' ); 
		do_action( 'after_footer' ); // supreme_after_sidebar_primary 
	endif;
}
/*
Name : supreme_author_page_sidebar
Description : Displays widgets in Post/Blog Listing page sidebar area if any have been added to the sidebar through the widgets screen in the admin by the user.  Otherwise, nothing is displayed.
*/
function supreme_author_page_sidebar(){
	
	if ( is_active_sidebar( 'author-page-sidebar' ) ) : ?>
<?php do_action( 'before_author-page-sidebar' ); // supreme_before_sidebar_primary ?>
<aside id="sidebar-post-listing" class="post-listing-sidebar sidebar">
  <?php do_action( 'open_author-page-sidebar' ); // supreme_open_sidebar_primary
			dynamic_sidebar( 'author-page-sidebar' ); 
			do_action( 'close_author-page-sidebar' ); // supreme_close_sidebar_primary ?>
</aside>
<!-- #sidebar-front-page-sidebar -->
<?php do_action( 'after_author-page-sidebar' ); // supreme_after_sidebar_primary 
	else:
		if(!supreme_is_layout1c())
			get_sidebar();
	endif;
}
?>