<?php
/**
 * The Header for our theme.
 *
 * The header template is generally used on every page of your site. Nearly all other templates call it 
 * somewhere near the top of the file. It is used mostly as an opening wrapper, which is closed with the 
 * footer.php file. It also executes key functions needed by the theme, child themes, and plugins. 
 * @package WordPress
 * @subpackage Supreme
 * @since Supreme 1.1
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>
<?php
if(is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php')){
	wp_title();
}else{
 	supreme_document_title(); 
}?>
</title>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>


<link rel="icon" href="http://www.websensemble.com/directory/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="http://www.websensemble.com/directory/favicon.ico" type="image/x-icon" />

<link href='http://fonts.googleapis.com/css?family=Zeyada' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Rock+Salt' rel='stylesheet' type='text/css'>



<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo get_template_directory_uri()."/library/css/admin_style.css"; ?>" type="text/css" media="all" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php 
$supreme2_theme_settings = get_option(supreme_prefix().'_theme_settings');
if ( isset($supreme2_theme_settings['customcss']) && $supreme2_theme_settings['customcss']==1 ) {
	echo '<link href="'.get_template_directory_uri().'/custom.css" rel="stylesheet" type="text/css" />';		
}
if(function_exists('supreme_get_favicon')){
	if(supreme_get_favicon()){ 
		echo '<link rel="shortcut icon" href="'.supreme_get_favicon().'" />';
	}
}
wp_head(); // wp_head 
if(isset($supreme2_theme_settings['enable_sticky_header_menu']) && $supreme2_theme_settings['enable_sticky_header_menu']==1){
	include(get_template_directory().'/js/sticky_menu.php');
}
do_action('supreme_enqueue_script');
?>
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->

<script>

$(document).ready(function() {


	$('#send_friend_id').text('Enviar a un amigo');
	$('a.addtofav').text('Agregar a favoritos');
	
	$('input.sgo').text('Buscar');	
	
	$('input.search-submit').text('Buscar');	
	
	
	/*$('form#ajaxform').hide();*/
	$('#submit').text('Enviar');	
	
	$('#reply-title').text('Dejar un Review');
	

	
	

	
	
	
	
	
});

</script>


</head>
<body style="background-color:#27A3DE" class="<?php supreme_body_class(); ?>">
<?php do_action('after_body');?>
<div class="supreme_wrapper">
<?php do_action( 'open_body' ); // supreme_open_body 
	 $theme_name = get_option('stylesheet');
	 $nav_menu = get_option('theme_mods_'.strtolower($theme_name));
	 remove_action('pre_get_posts', 'home_page_feature_listing');
?>
<div id="mobile_header" class="mobile_header">
     <div class="toggle_wrap clearfix">
          <div class="toggle_mobile_widget">
          	<?php apply_filters('supreme-nav-right',dynamic_sidebar('secondary_navigation_right')); ?>
          </div>
          <div class="toggle_mobile_header"></div>
     </div>
     <div class="mobi-scroll">
          <div id="hide_menu">
          <?php
			apply_filters('tmpl_supreme_header_primary',supreme_header_primary_navigation()); // Loads the menu-primary template. 
			
			if(isset($nav_menu['nav_menu_locations'])  && @$nav_menu['nav_menu_locations']['secondary'] != 0){
				echo '<div id="nav" class="nav_bg">';		
					apply_filters('tmpl_supreme_header_secondary',supreme_header_secondary_mobile_navigation()); // Loads the menu-secondary template.
				echo "</div>";		
			}elseif(is_active_sidebar('mega_menu')){
				if(function_exists('dynamic_sidebar')){
					echo '<div id="nav" class="nav_bg">
							<div id="menu-mobi-secondary" class="menu-container">
								<nav role="navigation" class="wrap">
									<div id="menu-mobi-secondary-title">';
										_e( 'Menu', THEME_DOMAIN );
								echo '</div>';
								dynamic_sidebar('mega_menu'); // jQuery mega menu
					echo "</nav></div></div>";		
				} 
			}
			else{
				 echo '<div id="nav1" class="nav_bg">';
							do_action( 'before_menu_secondary' ); // supreme_before_menu_secondary ?>
							<div id="menu-mobi-secondary1" class="menu-container">
								<nav role="navigation" class="wrap">
									<div id="menu-mobi-secondary-title1"><?php _e( 'Menu', THEME_DOMAIN ); ?></div><!-- #menu-secondary-title -->
									<?php do_action( 'open_menu_secondary' ); // supreme_open_menu_secondary ?>
									<div class="menu">
										<ul id="menu-mobi-secondary-items1">
											<?php wp_list_pages('title_li=&depth=0&child_of=0&number=5&show_home=1&sort_column=ID&sort_order=DESC');?>
										</ul>
									</div>
									<?php do_action( 'close_menu_secondary' ); // supreme_close_menu_secondary  ?>
		
								</nav>
							</div><!-- #menu-secondary .menu-container -->
							<?php do_action( 'after_menu_secondary' ); // supreme_after_menu_secondary 
				echo "</div>";
			}
          ?>
          </div>
     </div>
</div>
<div id="container" class="container-wrap">
<div class="header_container clearfix">
  <div class="header_strip">
    <div class="primary_menu_wrapper clearfix">
      <?php
        	supreme_primary_navigation();
		do_action( 'after_menu_primary' ); // supreme_before_header ?>
    </div>
    <?php do_action( 'before_header' ); // supreme_before_header 
		$header_image = get_header_image();
		if(function_exists('get_header_image_location')){
			$header_image_location = get_header_image_location(); // 0 = before secondary navigation menu, 1 = after secondary navigation menu
		}else{
			$header_image_location = 1;
		} ?>
     <header id="header" class="clearfix">
		<?php do_action( 'open_header' ); // supreme_open_header ?>
          <div class="header-wrap">
			<?php if(supreme_get_settings( 'display_header_text' )){ ?>
               <div id="branding">
                    <hgroup>
					<?php if ( supreme_get_settings( 'supreme_logo_url' ) ) : ?>
                         <div id="site-title">
                         	<a href="<?php echo home_url(); ?>/" title="<?php echo bloginfo( 'name' ); ?>" rel="Home">
                              	<img class="logo" src="<?php echo supreme_get_settings( 'supreme_logo_url' ); ?>" alt="<?php echo bloginfo( 'name' ); ?>" />
                              </a>
                         </div>
                         <?php else :
                        		 supreme_site_title();
                         endif; 
                         if ( !supreme_get_settings( 'supreme_site_description' ) )  : // If hide description setting is un-checked, display the site description. 
                         	supreme_site_description(); 
                         endif; ?>
                    </hgroup>
               </div>
               <!-- #branding -->
               <?php } 
               if ( is_active_sidebar( 'header' ) ) : 
               	apply_filters( 'tmpl-header',supreme_header_sidebar() ); // Loads the sidebar-header. 
               endif; 
               do_action( 'header' ); // supreme_header ?>
          </div>
          <!-- .wrap -->
          <?php if(!empty($header_image) && $header_image_location == 0){ ?>
          	<div class="templatic_header_image"><img src="<?php echo esc_url( $header_image ); ?>" class="header-image" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" /></div>
          <?php }
		
          do_action( 'close_header' ); // supreme_close_header 
          /* Secondary navigation menu for desk top */
          supreme_secondary_navigation(); 
          supreme_sticky_secondary_navigation();
          ?>
     </header>
    <!-- #header -->
  </div>
</div>
<?php 
$tmpdata = get_option('city_googlemap_setting');					
$map_class=(isset($tmpdata['google_map_full_width']) && $tmpdata['google_map_full_width']=='yes')?'clearfix map_full_width':'map_fixed_width';
if((!is_page() && !is_author() && !is_404() && !is_singular()) || (is_front_page() || is_home())):?>
     <div class="home_page_banner clear clearfix <?php echo $map_class;?>">
       <?php if(!empty($header_image) && $header_image_location == 1){ ?>
          <div class="templatic_header_image"><img src="<?php echo esc_url( $header_image ); ?>" class="header-image" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" /></div>
       <?php } 
       do_action( 'before_main' ); // supreme_before_main ?>
     </div>
<?php endif;?>

<div id="main" class="clearfix">
<div class="wrap">
<?php do_action( 'open_main' ); // supreme_open_main ?>