<?php 
/*
    File contain the code for color options in customizer 
*/

ob_start();
	$content_dir = explode('/',WP_CONTENT_DIR);
	$file = substr($file,0,stripos($file, $content_dir[1]));
	//require($file . "/wp-load.php");
	global $wpdb;
	if(function_exists('supreme_get_setting')){
		$color1 = supreme_get_setting( 'color_picker_color1' );
		$color2 = supreme_get_setting( 'color_picker_color2' );
		$color3 = supreme_get_setting( 'color_picker_color3' );
		$color4 = supreme_get_setting( 'color_picker_color4' );
		$color5 = supreme_get_setting( 'color_picker_color5' );
		$color6 = supreme_get_setting( 'color_picker_color6' );
	}else{
		$supreme_theme_settings = get_option(supreme_prefix().'_theme_settings');
		if(isset($supreme_theme_settings[ 'color_picker_color1' ]) && $supreme_theme_settings[ 'color_picker_color1' ] !=''):
			$color1 = $supreme_theme_settings[ 'color_picker_color1' ];
		else:
			$color1 ='';
		endif;
		
		if(isset($supreme_theme_settings[ 'color_picker_color2' ]) && $supreme_theme_settings[ 'color_picker_color2' ] !=''):
			$color2 = $supreme_theme_settings[ 'color_picker_color2' ];
		else:
			$color2 = '';
		endif;
		
		if(isset($supreme_theme_settings[ 'color_picker_color3' ]) && $supreme_theme_settings[ 'color_picker_color3' ] !=''):
			$color3 = $supreme_theme_settings[ 'color_picker_color3' ];
		else:
			$color3 ='';
		endif;
		
		if(isset($supreme_theme_settings[ 'color_picker_color4' ]) && $supreme_theme_settings[ 'color_picker_color4' ] !=''):
			$color4 = $supreme_theme_settings[ 'color_picker_color4' ];
		else:
			$color4 = '';
		endif;
		
		if(isset($supreme_theme_settings[ 'color_picker_color5' ]) && $supreme_theme_settings[ 'color_picker_color5' ] !=''):
			$color5 = $supreme_theme_settings[ 'color_picker_color5' ];
		else:
			$color5 ='';
		endif;
		
		if(isset($supreme_theme_settings[ 'color_picker_color6' ]) && $supreme_theme_settings[ 'color_picker_color6' ] !=''):
			$color6 = $supreme_theme_settings[ 'color_picker_color6' ];
		else:
			$color6 ='';
		endif;
	}




//Change color of body background
if($color1 != "#" || $color1 != ""){?>

	body,
    .map_full_width,
    #header, .sidebar-after-header, #main, .sidebar-subsidiary, div#menu-subsidiary, body .nav_bg .widget-nav-menu, .widget.templatic_slider, #sidebar-subsidiary, #sidebar-subsidiary-2c, #sidebar-subsidiary-3c, .sidebar-after-header, .sidebar-subsidiary, .footer_top .footer-wrap, .home .map_fixed_width,
    .home .map_full_width,
    .breadcrumb,
    body .event_manager_tab ul.event_type li a.active, .wrap404 {
    	background-color: <?php echo $color1;?>;
		}
<?php //event manager plugin compatible css ?>
body.tevolution-event-manager .ui-widget-header .ui-state-active a, body.tevolution-directory .ui-widget-header .ui-state-active a:link, body.tevolution-directory .ui-widget-header .ui-state-active a:visited,
    .author_custom_post_wrapper ul li a.nav-author-post-tab-active,
				body.woocommerce div.product .woocommerce-tabs ul.tabs li.active, body.woocommerce-page div.product .woocommerce-tabs ul.tabs li.active, body.woocommerce #content div.product .woocommerce-tabs ul.tabs li.active, body.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li.active
		{background: <?php echo $color1;?> !important}
        
    body .secondary_btn, button, a.button:hover, input[type="reset"], input[type="submit"], input[type="button"], a.button, .button, .uploadfilebutton, .upload, body.woocommerce a.button, body.woocommerce button.button, body.woocommerce input.button, body.woocommerce #respond input#submit, body.woocommerce #content input.button, body.woocommerce-page a.button, body.woocommerce-page button.button, body.woocommerce-page input.button, body.woocommerce-page #respond input#submit, body.woocommerce-page #content input.button, #searchform input[type="submit"], body.woocommerce .widget_layered_nav_filters ul li a, body.woocommerce-page .widget_layered_nav_filters ul li a, div.woocommerce form.track_order input.button, body.woocommerce a.button.alt, body.woocommerce button.button.alt, body.woocommerce input.button.alt, body.woocommerce #respond input#submit.alt, body.woocommerce #content input.button.alt, body.woocommerce-page a.button.alt, body.woocommerce-page button.button.alt, body.woocommerce-page input.button.alt, body.woocommerce-page #respond input#submit.alt, body.woocommerce-page #content input.button.alt,
				body .mega-menu ul.mega li:hover a, body .mega-menu ul.mega li a:hover, body .mega-menu ul.mega li.current-menu-item a, body .mega-menu ul.mega li.current-page-item a,
				body .mega-menu ul.mega li .sub-container.non-mega .sub a:hover, body .mega-menu ul.mega li .sub-container.non-mega li a:hover, body .mega-menu ul.mega li .sub-container.non-mega li.current-menu-item a, body .mega-menu ul.mega .sub li.mega-hdr li a:hover, body .mega-menu ul.mega li .sub li.mega-hdr a.mega-hdr-a:hover, #footer .footer_bottom, .loop-nav span.previous:hover, .loop-nav span.next:hover, .pagination .page-numbers:hover, .comment-pagination .page-numbers:hover, .bbp-pagination .page-numbers:hover, body .pagination .current, body .pos_navigation .post_left a:hover, body .pos_navigation .post_right a:hover, body.singular #content .claim-post-wraper ul li a:hover, body.tevolution-event-manager .get_direction .b_getdirection,
				body #loop_event_taxonomy.list .post .entry .date, body #loop_event_archive.list .post .entry .date, body.woocommerce nav.woocommerce-pagination ul li a:hover, body.woocommerce-page nav.woocommerce-pagination ul li a:hover, body.woocommerce #content nav.woocommerce-pagination ul li a:hover, body.woocommerce-page #content nav.woocommerce-pagination ul li a:hover, body.woocommerce nav.woocommerce-pagination ul li span.current, body.woocommerce-page nav.woocommerce-pagination ul li span.current, body.woocommerce #content nav.woocommerce-pagination ul li span.current, body.woocommerce-page #content nav.woocommerce-pagination ul li span.current, body div.product form.cart .button, body #content div.product form.cart .button, body.woocommerce .quantity .plus, body.woocommerce-page .quantity .plus, body.woocommerce #content .quantity .plus, body.woocommerce-page #content .quantity .plus, body.woocommerce .quantity .minus, body.woocommerce-page .quantity .minus, body.woocommerce #content .quantity .minus, body.woocommerce-page #content .quantity .minus,
				body.woocommerce a.button.alt:hover, body.woocommerce-page a.button.alt:hover, body.woocommerce button.button.alt:hover, body.woocommerce-page button.button.alt:hover, body.woocommerce input.button.alt:hover, body.woocommerce-page input.button.alt:hover, body.woocommerce #respond input#submit.alt:hover, body.woocommerce-page #respond input#submit.alt:hover, body.woocommerce #content input.button.alt:hover, body.woocommerce-page #content input.button.alt:hover, article.event .entry-header span.date, .widget #wp-calendar caption, .widget #wp-calendar th, .uploadfilebutton:hover, body .secondary_btn:hover, body .ui-datepicker-trigger, body .ui-datepicker-trigger:hover
	{color: <?php echo $color1;?>}

#footer .footer_bottom a:hover
{color: <?php echo $color1;?> !important}
        
    body .event_manager_tab ul.event_type li a.active,
    body.tevolution-event-manager .ui-widget-header .ui-state-active a, body.tevolution-directory .ui-widget-header .ui-state-active a:link, body.tevolution-directory .ui-widget-header .ui-state-active a:visited,
    body.tevolution-directory .ui-widget-header .ui-state-active a, body.tevolution-directory .ui-widget-header .ui-state-active a:link, body.tevolution-directory .ui-widget-header .ui-state-active a:visited
				{border-bottom-color: <?php echo $color1;?>}
				body.woocommerce div.product .woocommerce-tabs ul.tabs li.active, body.woocommerce-page div.product .woocommerce-tabs ul.tabs li.active, body.woocommerce #content div.product .woocommerce-tabs ul.tabs li.active, body.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li.active
				{border-bottom-color: <?php echo $color1;?> !important}

<?php }








//Change blue color 
if($color2 != "#" || $color2 != ""){?>
.primary_menu_wrapper,
    div#menu-secondary .wrap, div#menu-secondary1 .wrap, div#menu-subsidiary .wrap, .nav_bg .widget-nav-menu nav,
    div#menu-secondary .menu ul ul, div#menu-secondary1 .menu ul ul, div#menu-subsidiary .menu ul ul, .nav_bg .widget-nav-menu ul ul,
    div#menu-primary .menu ul ul,
    .widget #wp-calendar caption,
    #footer .footer_bottom,
    .tags a:hover, .tagcloud a:hover, .browse_by_tag a:hover,
    body .ui-datepicker-trigger:hover,
    button:hover,  input[type="reset"]:hover,  input[type="submit"]:hover,  input[type="button"]:hover,  a.button:hover,  .button:hover, .uploadfilebutton:hover,
    .submitbutton, body.woocommerce a.button.alt, body.woocommerce button.button.alt, body.woocommerce input.button.alt, body.woocommerce #respond input#submit.alt, body.woocommerce #content input.button.alt, body.woocommerce-page a.button.alt, body.woocommerce-page button.button.alt, body.woocommerce-page input.button.alt, body.woocommerce-page #respond input#submit.alt, body.woocommerce-page #content input.button.alt,
    body.woocommerce .quantity .plus:hover, body.woocommerce-page .quantity .plus:hover, body.woocommerce #content .quantity .plus:hover, body.woocommerce-page #content .quantity .plus:hover, body.woocommerce .quantity .minus:hover, body.woocommerce-page .quantity .minus:hover, body.woocommerce #content .quantity .minus:hover, body.woocommerce-page #content .quantity .minus:hover,
    body .main_btn,
    .stickyheader .header_container,
    body .mega-menu .nav_bg,
    body .mega-menu ul.mega li ul.sub-menu,
    #silde_gallery .flex-direction-nav li a { background-color: <?php echo $color2;?>; }
        
    body .templatic_advanced_search #widget_searchform .ui-datepicker-trigger:hover,
    .upload:hover, body.woocommerce a.button:hover, body.woocommerce button.button:hover, body.woocommerce input.button:hover, body.woocommerce #respond input#submit:hover, body.woocommerce #content input.button:hover, body.woocommerce-page a.button:hover, body.woocommerce-page button.button:hover, body.woocommerce-page input.button:hover, body.woocommerce-page #respond input#submit:hover, body.woocommerce-page #content input.button:hover, #content input.button:hover, #searchform input[type="submit"]:hover, body.woocommerce .widget_layered_nav_filters ul li a:hover, body.woocommerce-page .widget_layered_nav_filters ul li a:hover, div.woocommerce form.track_order input.button:hover, body.woocommerce a.button.alt:hover, body.woocommerce button.button.alt:hover, body.woocommerce input.button.alt:hover, body.woocommerce #respond input#submit.alt:hover, body.woocommerce #content input.button.alt:hover, body.woocommerce-page a.button.alt:hover, body.woocommerce-page button.button.alt:hover, body.woocommerce-page input.button.alt:hover, body.woocommerce-page #respond input#submit.alt:hover, body.woocommerce-page #content input.button.alt:hover,
				body .mega-menu ul.mega li .sub-container.non-mega .sub a:hover, body .mega-menu ul.mega li .sub-container.non-mega li a:hover, body .mega-menu ul.mega li .sub-container.non-mega li.current-menu-item a
				{ background: <?php echo $color2;?>}
    
    a,
    .social_media ul li a:hover abbr,
    .byline a:hover, .entry-meta a:hover,
    #site-title, #site-title1,
    #site-title a, #site-title1 a,
    #breadcrumb a:hover,  .breadcrumb a:hover,
    .byline a:hover, .entry-meta a:hover,
    .entry-meta .category a:hover, .entry-meta .post_tag a:hover,
    .post_info_meta a:hover,
    .comment-meta a:hover,
    #respond #cancel-comment-reply-link,
    .templatic_twitter_widget .twit_time,
    #recentcomments a,
    .arclist h2,
    .arclist ul li a:hover,
    .arclist ul li .arclist_date a:hover,
    body.woocommerce div.product .woocommerce-tabs ul.tabs li.active, body.woocommerce-page div.product .woocommerce-tabs ul.tabs li.active, body.woocommerce #content div.product .woocommerce-tabs ul.tabs li.active, body.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li.active,
    #content ul.products li.product:hover h3,
    #content ul.products li.product .price,
    #content ul.products li.product .price .from, #content ul.products li.product .price del,
    body.woocommerce p.stars a:hover:before, body.woocommerce p.stars a:focus:before, body.woocommerce p.stars a:active:before, body.woocommerce p.stars a.active:before, body.woocommerce-page p.stars a:hover:before, body.woocommerce-page p.stars a:focus:before, body.woocommerce-page p.stars a:active:before, body.woocommerce-page p.stars a.active:before,
    body.woocommerce div.product span.price, body.woocommerce-page div.product span.price, body.woocommerce #content div.product span.price, body.woocommerce-page #content div.product span.price, body.woocommerce div.product p.price, body.woocommerce-page div.product p.price, body.woocommerce #content div.product p.price, body.woocommerce-page #content div.product p.price,
    .show_review_form,
    body #sub_event_categories ul li a,
    body .all_category_list_widget .category_list h3 a:hover,
	.widget .follow_us_twitter:hover,
    .listing_post .hentry h2 a,
    .home_page_banner .flexslider ul li .post_list .slider-post h2 a,
    .attending_event span.fav span.span_msg a:hover,
    body .widget #wp-calendar .calendar_tooltip .event_title,
<?php //directory plugin compatible css ?>
.all_category_list_widget .category_list h3 a:hover, body #sub_listing_categories ul li a,
body .all_category_list_widget .category_list ul li a,
ul li a, ol li a, body .related_post_grid_view li h3 a, del span.amount
{ color: <?php echo $color2;?> }
        
    body.home .fav .addtofav:hover,
    body.home .fav .removefromfav:hover,
    body #content .people_info h3 a,
    body #content .add_to_my_calendar .addtocalendar ul li a:hover
				{color: <?php echo $color2;?> !important}
    
    .social_media ul li a:hover abbr,
    .nav_bg .widget input[type="text"]:focus, .mega-menu .widget .search-form input:focus,
    .recent_comments li span a img:hover,
    body table.calendar_widget td.date_n div span.calendar_tooltip { border-color: <?php echo $color2;?>; }
<?php }
	
	
	
	
	
	
	
	
	
	
	//Change color of page content
	if($color3 != "#" || $color3 != ""){?>
.widget #wp-calendar th { background-color: <?php echo $color3;?>; }
    
    body,
    a:hover,
    .widget h3, .widget.title, .widget-title, .widget-search .widget-title,
    .tags a, .tagcloud a, .browse_by_tag a,
    .social_media ul li a abbr,
    .loop-nav span.previous, .loop-nav span.next, .pagination .page-numbers, .comment-pagination .page-numbers, body .pos_navigation .post_left a, body .pos_navigation .post_right a,
    input[type="date"],  input[type="datetime"],  input[type="datetime-local"],  input[type="email"],  input[type="month"],  input[type="number"],  input[type="password"],  input[type="search"],  input[type="tel"],  input[type="text"],  input.input-text,  input[type="time"],  input[type="url"],  input[type="week"],  select,  textarea,
    .entry-meta .category a, .entry-meta .post_tag a,
    .post_info_meta a,
    .comment-author,
    #respond #cancel-comment-reply-link:hover,
    .widget .follow_us_twitter,
    .listing_post .post h2 a,
    .arclist ul li .arclist_date, .arclist ul li .arclist_date a,
    .arclist ul li span.arclist_comment a,
    ins span.amount,
    body .fav .addtofav:hover,
    body .package label h3,
    body.woocommerce div.product .woocommerce-tabs ul.tabs li a, body.woocommerce-page div.product .woocommerce-tabs ul.tabs li a, body.woocommerce #content div.product .woocommerce-tabs ul.tabs li a, body.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li a,
	body .fav .addtofav,
    body .fav .removefromfav,
    body .widget table.calendar_widget td.date_n div span.calendar_tooltip small,
    body .widget table.calendar_widget td.date_n div span.calendar_tooltip small .wid_event_list b.label,
<?php //directory plugin compatible css ?>
body #loop_listing_taxonomy .post .entry p, body #loop_listing_archive .post .entry p,
    body.tevolution-directory .ui-widget-header li a,
    body .ui-widget-content,
<?php //event manager plugin compatible css ?>
body .event_manager_tab ul.event_type li a,
    body.tevolution-event-manager .ui-widget-header li a,
    body #loop_event_taxonomy .post .entry p, body #loop_event_archive .post .entry p,
	body #content .claim_ownership .claimed,
    body .author_custom_post_wrapper ul li a,
    body.woocommerce nav.woocommerce-pagination ul li a, body.woocommerce-page nav.woocommerce-pagination ul li a, body.woocommerce #content nav.woocommerce-pagination ul li a, body.woocommerce-page #content nav.woocommerce-pagination ul li a,
    .upload.button span.upload_title, .upload.button span,
    body .hentry.error, body.woocommerce .star-rating:before, body.woocommerce-page .star-rating:before, #content ul.products li.product a .star-rating,
				.shop_table th, body.woocommerce .woocommerce-message, body.woocommerce .woocommerce-error, body.woocommerce .woocommerce-info, body.woocommerce-page .woocommerce-message, body.woocommerce-page .woocommerce-error, body.woocommerce-page .woocommerce-info, body.woocommerce #payment div.payment_box, body.woocommerce-page #payment div.payment_box
				{ color: <?php echo $color3;?>; }
        
    body #map_canvas .google-map-info .map-inner-wrapper .map-item-info h6 a { color: <?php echo $color3;?> !important; }    

	.social_media ul li a abbr,
    body .secondary_btn { border-color: <?php echo $color3;?>; }
<?php }





//Change color of lighter-blue
if($color4 != "#" || $color4 != ""){?>
div#menu-secondary .menu li a, div#menu-secondary1 .menu li a, div#menu-subsidiary .menu li a, .nav_bg .widget-nav-menu li a,
    div#menu-primary .menu li a,
    #footer .footer_bottom .menu a:hover,
    #footer .credit,
    #footer .credit a,
    body .toggle_handler #directorytab,
    body .toggle_handler #directorytab i,
    body .mega-menu ul.mega li a,
    body .mega-menu ul.mega li .sub .row li a,
    #footer .footer_bottom a,
    body .toggle_handler.primary_location #directorytab,
				body .mega-menu ul.mega li .sub a,
				body .mega-menu ul.mega li .sub li.mega-hdr a.mega-hdr-a,
				div#menu-secondary .menu li a:hover, div#menu-secondary1 .menu li a:hover, div#menu-secondary .menu li:hover > a, div#menu-secondary1 .menu li:hover > a, div#menu-secondary .menu li.current-menu-item > a, div#menu-secondary1 .menu li.current-menu-item > a, div#menu-subsidiary .menu li.current-menu-item > a
				{color: <?php echo $color4;?>}

<?php }









//Change color of lighter-gray
if($color5 != "#" || $color5 != ""){?>

				.post_info_meta a,
    .byline,
    .byline a,
    #breadcrumb .trail-end, .breadcrumb .trail-end,
    .entry-meta,
    .comment-meta span.comment-reply:after,
    .widget-widget_rss ul li span.rss-date,  .widget-widget_rss ul li cite,
    .nav_bg .widget input[type="text"], .mega-menu .widget .search-form input,
    .arclist ul li span.arclist_comment,
    div#menu-mobi-primary .menu li a,
    div#menu-mobi-secondary .menu li a, div#menu-mobi-secondary1 .menu li a,  div#menu-subsidiary .menu li a,
    .woocommerce-checkout .form-row .chzn-container-single .chzn-single,
    .gfield_description,
    body .message_note, body .form_row span.message_note, body .form_row .description,
    body .form_row label:hover,
    .flex-control-paging li a,
    body .user_dsb_cf label,
<?php //directory plugin compatible css ?>
				body #loop_listing_taxonomy.list .post .entry .bottom_line a, body #loop_listing_archive.list .post .entry .bottom_line a,
    body.tevolution-directory .post-meta,
    .comment-meta .published,
<?php //event manager plugin compatible css ?>
				body #loop_event_atteding_list.list .post .entry .bottom_line a,
    body.tevolution-event-manager .post-meta,
    body #loop_event_taxonomy.list .post .entry .bottom_line a, body #loop_event_taxonomy.list .post .entry .bottom_line a,
<?php //tevolution compatible css ?>
				body .form_row span.error_message, body .error, body.message_error,
    body .event_show_event button,
    body.woocommerce div.product .woocommerce-tabs ul.tabs li a:hover, body.woocommerce-page div.product .woocommerce-tabs ul.tabs li a:hover, body.woocommerce #content div.product .woocommerce-tabs ul.tabs li a:hover, body.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li a:hover, .listing_post .hentry h2 a:hover,
    body.woocommerce #reviews #comments ol.commentlist li .meta, body.woocommerce-page #reviews #comments ol.commentlist li .meta {
		color: <?php echo $color5; ?>;
    	}
        
    body .message_note, body .form_row span.message_note, body .form_row .description {
    	color: <?php echo $color5; ?> !important;
        }
<?php }









//Change color of black
if($color6 != "#" || $color6 != ""){?>
.loop-nav span.previous:hover, .loop-nav span.next:hover, .pagination .page-numbers:hover, .comment-pagination .page-numbers:hover, .bbp-pagination .page-numbers:hover, body .pagination .current, body .pos_navigation .post_left a:hover, body .pos_navigation .post_right a:hover,
    body .ui-datepicker-trigger,
    button,  input[type="reset"],  input[type="submit"],  input[type="button"],  a.button,  .button,
    body .secondary_btn:hover,
    .loop-nav span.previous:hover, .loop-nav span.next:hover, .pagination .page-numbers:hover, .comment-pagination .page-numbers:hover, .bbp-pagination .page-numbers:hover, body .pagination .current, body .pos_navigation .post_left a:hover, body .pos_navigation .post_right a:hover,
    div#menu-mobi-secondary .menu li a:hover, div#menu-mobi-secondary1 .menu li a:hover,  div#menu-subsidiary .menu li a:hover,
    div#menu-mobi-secondary .menu li li:hover > a, div#menu-mobi-secondary1 .menu li li:hover > a,  div#menu-mobi-secondary .menu li li a:hover,  div#menu-mobi-secondary1 .menu li li a:hover,  div#menu-subsidiary .menu li li a:hover,  .nav_bg .widget-nav-menu li li a:hover,
    body.woocommerce nav.woocommerce-pagination ul li a:hover, body.woocommerce-page nav.woocommerce-pagination ul li a:hover, body.woocommerce #content nav.woocommerce-pagination ul li a:hover, body.woocommerce-page #content nav.woocommerce-pagination ul li a:hover, body.woocommerce nav.woocommerce-pagination ul li span.current, body.woocommerce-page nav.woocommerce-pagination ul li span.current, body.woocommerce #content nav.woocommerce-pagination ul li span.current, body.woocommerce-page #content nav.woocommerce-pagination ul li span.current,
	.flex-control-paging li a:hover, .flex-control-paging li a.flex-active,
    body .recurrence_text,
<?php //directory plugin compatible css ?>
body #content .claim-post-wraper ul li a:hover,
<?php //event manager plugin compatible css ?>
body #loop_event_atteding_list.list .post .entry .date,
    body #loop_event_taxonomy.list .post .entry .date, body #loop_event_archive.list .post .entry .date,
    body .sort_order_alphabetical ul li.active a, body .sort_order_alphabetical ul li a:hover,
    body.woocommerce .quantity .plus, body.woocommerce-page .quantity .plus, body.woocommerce #content .quantity .plus, body.woocommerce-page #content .quantity .plus, body.woocommerce .quantity .minus, body.woocommerce-page .quantity .minus, body.woocommerce #content .quantity .minus, body.woocommerce-page #content .quantity .minus,
    body .ui-datepicker-calendar th,
    body .widget_loop_taxonomy .post .fp_entry .date,
    body article .entry-header span.date,
    #silde_gallery .flex-direction-nav li a:hover
				{background-color: <?php echo $color6;?>}
        
     body .templatic_advanced_search #widget_searchform .ui-datepicker-trigger,
     .upload.button:hover,
     body.woocommerce a.button, body.woocommerce button.button, body.woocommerce input.button, body.woocommerce #respond input#submit, body.woocommerce #content input.button, body.woocommerce-page a.button, body.woocommerce-page button.button, body.woocommerce-page input.button, body.woocommerce-page #respond input#submit, body.woocommerce-page #content input.button, #searchform input[type="submit"], body.woocommerce .widget_layered_nav_filters ul li a, body.woocommerce-page .widget_layered_nav_filters ul li a, div.woocommerce form.track_order input.button, body.woocommerce a.button.alt, body.woocommerce button.button.alt, body.woocommerce input.button.alt, body.woocommerce #respond input#submit.alt, body.woocommerce #content input.button.alt, body.woocommerce-page a.button.alt, body.woocommerce-page button.button.alt, body.woocommerce-page input.button.alt, body.woocommerce-page #respond input#submit.alt, body.woocommerce-page #content input.button.alt,
    body #content .upload.button:hover, #uploadimage:hover, body.woocommerce .widget_price_filter .ui-slider .ui-slider-handle, body.woocommerce-page .widget_price_filter .ui-slider .ui-slider-handle {
    	background: <?php echo $color6;?>
        }
    
    .post_info_meta,
    .comment-meta a,
    h1, h2, h3, h4, h5, h6,
    input[type="date"]:focus,  input[type="datetime"]:focus,  input[type="datetime-local"]:focus,  input[type="email"]:focus,  input[type="month"]:focus,  input[type="number"]:focus,  input[type="password"]:focus,  input[type="search"]:focus,  input[type="tel"]:focus,  input[type="text"]:focus,  input.input-text:focus,  input[type="time"]:focus,  input[type="url"]:focus,  input[type="week"]:focus,  select:focus,  textarea:focus,
    #breadcrumb, .breadcrumb,
    #breadcrumb a,  .breadcrumb a,
    .entry-meta .category, .entry-meta .post_tag,
    .comment-reply-link, .comment-reply-login,
    .view_counter b,
    .arclist ul li a,
    .flex-direction-nav li a:hover,
    .attending_event span.fav span.span_msg a,
    .attending_event span.fav span.span_msg,
    .attending_event span.fav a.addtofav,
    body .sort_order_alphabetical ul li a,
    body.woocommerce div.product .woocommerce-tabs ul.tabs li.active a, body.woocommerce-page div.product .woocommerce-tabs ul.tabs li.active a, body.woocommerce #content div.product .woocommerce-tabs ul.tabs li.active a, body.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li.active a,
    body #sub_event_categories ul li a:hover,
	body .all_category_list_widget .category_list ul li a:hover,
    body .user_dsb_cf span,
<?php //directory plugin compatible css ?>
body #loop_listing_taxonomy.list .post .entry .bottom_line, body #loop_listing_archive.list .post .entry .bottom_line,
    body #loop_listing_taxonomy .post .entry .phone, body #loop_listing_archive .post .entry .phone,
    body.tevolution-directory .post-meta a,
    body.directory-single-page .hentry .entry-header-title .entry-header-custom-wrap p label,
    body #content .claim-post-wraper ul li a,
    body #content .claim-post-wraper ul li a:after,
    .comment-author cite,
<?php //event manager plugin compatible css ?>
body #loop_event_atteding_list.list .post .entry .bottom_line,
    body.tevolution-event-manager .post-meta a,
    body.event-single-page .hentry .entry-header-title .entry-header-custom-wrap p label,
    body.tevolution-event-manager.event-single-page .entry-content h2,
    body #loop_event_taxonomy .post .entry p strong, body #loop_event_archive .post .entry p strong,
    body #loop_event_taxonomy.grid .post .entry .date, body #loop_event_archive.grid .post .entry .date,
    body .all_category_list_widget .category_list h3 a,
    body #loop_event_taxonomy.list .post .entry .bottom_line, body #loop_event_taxonomy.list .post .entry .bottom_line,
    body .event-organizer .event-organizer-right label,
    body .ui-widget-content a,
    body .widget_loop_taxonomy.grid .post .fp_entry .date,
				body #sub_listing_categories ul li a:hover,
				ul li a:hover, ol li a:hover,
				.error_404 h4, body .related_post_grid_view li h3 a:hover,
				body.woocommerce div.product .woocommerce-tabs ul.tabs li.active a, body.woocommerce-page div.product .woocommerce-tabs ul.tabs li.active a, body.woocommerce #content div.product .woocommerce-tabs ul.tabs li.active a, body.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li.active a, body.woocommerce div.product .woocommerce-tabs ul.tabs li a:hover, body.woocommerce-page div.product .woocommerce-tabs ul.tabs li a:hover, body.woocommerce #content div.product .woocommerce-tabs ul.tabs li a:hover, body.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li a:hover, #recentcomments a:hover
				{ color: <?php echo $color6;?> }
        
    body #map_canvas .google-map-info .map-inner-wrapper .map-item-info a:hover {
    	color: <?php echo $color6;?> !important;
    	}
        
    .loop-nav span.previous:hover, .loop-nav span.next:hover, .pagination .page-numbers:hover, .comment-pagination .page-numbers:hover, .bbp-pagination .page-numbers:hover, body .pagination .current, body .pos_navigation .post_left a:hover, body .pos_navigation .post_right a:hover,
    body.woocommerce nav.woocommerce-pagination ul li a:hover, body.woocommerce-page nav.woocommerce-pagination ul li a:hover, body.woocommerce #content nav.woocommerce-pagination ul li a:hover, body.woocommerce-page #content nav.woocommerce-pagination ul li a:hover, body.woocommerce nav.woocommerce-pagination ul li span.current, body.woocommerce-page nav.woocommerce-pagination ul li span.current, body.woocommerce #content nav.woocommerce-pagination ul li span.current, body.woocommerce-page #content nav.woocommerce-pagination ul li span.current,
    .flex-control-paging li a:hover, .flex-control-paging li a.flex-active {
    	border-color: <?php echo $color6;?>;
        }
        
     .d_location_type_navigation {
     	border-color: <?php echo $color6;?> !important;
        }
<?php }


$color_data = ob_get_contents();
ob_clean();
if(isset($color_data) && $color_data !=''){ 
    file_put_contents(trailingslashit(get_template_directory())."library/css/admin_style.css" , $color_data); 
}
?>