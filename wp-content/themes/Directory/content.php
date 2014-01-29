<?php // supreme_open_entry
	$post_type = get_post_type($post->ID);
	do_action( 'open_entry'.$post_type );
	
	$featured=get_post_meta(get_the_ID(),'featured_c',true);
		$featured=($featured=='c')?'featured_c':'';
		
		if(isset($_REQUEST['sort']) && $_REQUEST['sort'] =='favourites'){
			$post_type_tag = $post->post_type;
		}else{
			$post_type_tag = '';
		}
	
	if ( is_sticky() && is_home() && ! is_paged() ) : ?>
<div class="featured-post">
  <?php _e( 'Featured post', THEME_DOMAIN ); ?>
</div>
<?php endif;?>
<?php 		
		/* get the image code - show image if Display imege option is enable from backend - Start */
		$theme_options = get_option(supreme_prefix().'_theme_settings');
		$supreme_display_image = $theme_options['supreme_display_image'];
		if ( current_theme_supports( 'get-the-image' ) && $supreme_display_image ) :
		do_action('supreme_before-image'.$post_type);
		$image = get_the_image( array( 'echo' => false,'' ) );
		
		if ( $image && has_post_thumbnail() ) : ?>
<figure class="post_fig">
  <?php if($featured){echo '<span class="featured_tag">'.__('Featured',THEME_DOMAIN).'</span>';} ?>
  <a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute( 'echo=1' ); ?>" rel="bookmark" class="featured-image-link"><img src="<?php get_the_image( array( 'size' => 'thumbnail', 'link_to_post' => false ) ); ?>"/></a> </figure>
<?php 
		else : 
			$post_image = '';
			if(function_exists('bdw_get_images_plugin')){
				$post_img = bdw_get_images_plugin($post->ID,'thumb');					
				$post_image = @$post_img[0]['file'];
			}
			
			if(!$post_image)
			{
				$theme_options = get_option(supreme_prefix().'_theme_settings');
				$supreme_display_noimage = $theme_options['supreme_display_noimage'];				
				if($supreme_display_noimage){
					
					$post_image = apply_filters('supreme_noimage-url',get_template_directory_uri()."/images/noimage.jpg");
				}
			}
			if(is_home() || is_front_page())
			{
				$featured=get_post_meta(get_the_ID(),'featured_h',true);
				$featured=($featured=='h')?'featured_c':'';
			}
			else
			{
				$featured=get_post_meta(get_the_ID(),'featured_c',true);
				$featured=($featured=='c')?'featured_c':'';
			}			
			
			if($post_image!=''){
		?>
<figure class="post_fig">
  <?php if($featured){echo '<span class="featured_tag">'.__('Featured',THEME_DOMAIN).'</span>';}?>
  <a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute( 'echo=1' ); ?>" rel="bookmark" class="featured-image-link"><img src="<?php echo apply_filters('supreme_post_images',$post_image); ?>" alt="<?php the_title_attribute( 'echo=1' ); ?>"/></a> </figure>
<?php 
			}
		endif;
		
		do_action('supreme_after-image'.$post_type);
		endif;
		/* get the image code - show image if Display imege option is enable from backend - Start */		
		?>
<header class="entry-header">
  <?php do_action('supreme_before-title_'.$post_type);?>
  <h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', THEME_DOMAIN ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">
    <?php the_title(); ?>
    </a></h2>
  <?php  
			do_action('tevolution_title_text');
				if(!is_author()){
					//apply_filters('supreme-post-info',supreme_core_post_info($post)); //do not show by line for blog post page for home page.
				}
			
			do_action('supreme_after-title_'.$post_type);			
		?>
  <?php 
		do_action( 'tmpl-before-entry'.$post_type); // Loads the sidebar-entry
		$theme_options = get_option(supreme_prefix().'_theme_settings');
		$supreme_archive_display_excerpt = $theme_options['supreme_archive_display_excerpt'];
		if(is_author() && (!isset($_REQUEST['sort']))){
			do_action('templ_show_edit_renew_delete_link');	
		}
		
		if( $supreme_archive_display_excerpt) { ?>
  <?php 
			if(is_tevolution_active() && tmpl_donot_display_description()){
			
			}else{ ?>
  <div class="entry-summary">
    <?php the_excerpt(); ?>
    <?php do_action('single_post_custom_fields'); ?>
  </div>
  <!-- .entry-summary -->
  <?php } ?>
  <?php }else{ 
			if(is_tevolution_active() && tmpl_donot_display_description()){ ?>
  <?php }else{ ?>
  <div class="entry-content">
    <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_DOMAIN ) ); ?>
    <?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', THEME_DOMAIN ), 'after' => '</div>' ) ); ?>
    <?php do_action('single_post_custom_fields'); ?>
  </div>
  <!-- .entry-content -->
  <?php	}
		} 
		if(!is_author())
		{
			apply_filters( 'tmpl-after-entry',supreme_sidebar_entry() ); // Loads the sidebar-entry
		}
		$taxonomies =  supreme_get_post_taxonomies($post);
		$cat_slug = $taxonomies [0];
		$tag_slug = $taxonomies [1];
		$theme_options = get_option(supreme_prefix().'_theme_settings');
		$display_post_terms = $theme_options['display_post_terms'];
		if($display_post_terms)
		{
			supreme_entry_meta(); 			
		}
		do_action('supreme_aftercontent'.$post_type);
		
		do_action( 'close_entry'.$post_type); // supreme_close_entry ?>
  <!-- #post -->
</header>
<!-- .entry-header -->
