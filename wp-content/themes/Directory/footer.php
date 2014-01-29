<?php
/**
 * Footer Template
 *
 * The footer template is generally used on every page of your site. Nearly all other
 * templates call it somewhere near the bottom of the file. It is used mostly as a closing
 * wrapper, which is opened with the header.php file. It also executes key functions needed
 * by the theme, child themes, and plugins. 
 *
 * @package supreme
 * @subpackage Template
 */
 do_action( 'close_main' ); // supreme_close_main ?>
</div>
<!-- .wrap -->
</div>
<!-- #main -->
<?php do_action( 'after_main' ); // supreme_after_main ?>
</div>
<!-- #container -->
<?php do_action( 'close_body' ); // supreme_close_body 
		
	apply_filters('tmpl-subsidiary',supreme_subsidiary_sidebar() ); // Loads the sidebar-subsidiary 
	apply_filters('tmpl-subsidiary-2c',supreme_subsidiary_2c_sidebar() ); // Loads the sidebar-subsidiary 
	apply_filters('tmpl-subsidiary-3c',supreme_subsidiary_3c_sidebar() ); // Loads the sidebar-subsidiary
	apply_filters('tmpl-subsidiary-4c',supreme_subsidiary_4c_sidebar() ); // Loads the sidebar-subsidiary 
	apply_filters('tmpl_subsidiary_nav',supreme_subsidiary_navigation()); // Loads the menu-subsidiary.php template.
	do_action( 'before_footer' ); // supreme_before_footer ?>
<footer id="footer" class="clearfix">
  <?php do_action( 'open_footer' ); // supreme_open_footer 
	if(is_active_sidebar('footer')):
	?>
  <div class="footer_top clearfix">
    <div class="footer-wrap clearfix">
      <div class="footer_widget_wrap">
        <?php apply_filters('tmpl_supreme_footer_widgets' ,supreme_footer_widgets()); //load footer widgets ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <div class="footer_bottom clearfix">
    <div class="footer-wrap clearfix">
      <?php apply_filters('tmpl_supreme_footer_nav',supreme_footer_navigation()); // Loads the menu-footer. 
            if(supreme_get_settings('footer_insert')){
            ?>
      <div class="footer-content"> <?php echo apply_atomic_shortcode( 'footer_content', supreme_get_settings( 'footer_insert' ) ); ?> </div>
      <!-- .footer-content -->
      <?php }else{ 
            if(!is_active_sidebar('footer')):
            ?>
      <div class="footer-content"> <?php echo '<p class="copyright">&copy; 2014 <a href="http://suisseworks.com">TravelGuide CostaRica</a>. &nbsp;powered by <a href="http://templatic.com" class="footer-logo"><img src="'.get_template_directory_uri().'/library/images/templatic-wordpress-themes.png" alt="suisseworks" /></a></p>'; ?> </div>
      <!-- .footer-content -->
      <?php	endif; }	
          do_action( 'footer' ); // supreme_footer ?>
    </div>
    <!-- .wrap -->
  </div>
  <?php do_action( 'close_footer' ); // supreme_close_footer ?>
</footer>
<!-- #footer -->
<?php do_action( 'after_footer' ); // supreme_after_footer 
	wp_footer(); // wp_footer 
	do_action('before_body_end',10); ?>
</div>
</body>
</html>