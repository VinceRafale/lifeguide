<?php 
/*
 * Create the templatic browse by categories widget
 */
	
 // =============================== Top Agents Widget ======================================
class tevolution_author_listing extends WP_Widget {
	function tevolution_author_listing() {
	//Constructor
		$widget_ops = array('classname' => 'widget tevolution_author_listing', 'description' => __("Shows authors with their thumbnails and the number of posts they've submitted. Works best in sidebar areas.",DOMAIN) );
		$this->WP_Widget('tevolution_author_listing', __('T &rarr; Display Authors',DOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
	// prints the widget
		extract($args, EXTR_SKIP);
		echo $before_widget;
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$category = empty($instance['category']) ? '' : apply_filters('widget_category', $instance['category']);
		$no_user = empty($instance['no_user']) ? '5' : apply_filters('widget_no_user', $instance['no_user']);
		$role = empty($instance['role']) ? 'subscriber' : apply_filters('widget_role', $instance['role']);
  			?>
            <div class="featured_agent">
            <?php if($title) { ?>
				<h3 class="widget-title"><?php echo $title; ?></h3> 
            <?php } ?>
            <ul class="featured_agent_list">
                <?php 
				function user_query_count_post_type($args) {
					$tevolution_post_type = get_option('templatic_custom_post');
					$custom_post_type = '';
					$sep = ',';
					if(!empty($tevolution_post_type))
					{
						foreach ($tevolution_post_type as $key => $val):
							$custom_post_type .= "'".$key."'".$sep;
						endforeach;
					}
					$args->query_from = str_replace("post_type = 'post' AND", "post_type IN ($custom_post_type'post') AND ", $args->query_from);
				   
				}
				 add_action('pre_user_query','user_query_count_post_type');
				$args = array(
						'role'         => $role,
						'meta_key'     => '',
						'meta_value'   => '',
						'meta_compare' => '',
						'meta_query'   => array(),
						'include'      => array(),
						'exclude'      => array(),
						'orderby'      => 'post_count',
						'order'        => 'DESC',
						'offset'       => '',
						'search'       => '',
						'number'       => $no_user,
						'count_total'  => false,
						'fields'       => 'all',
						'who'          => ''
					 );
					$listpeoples= get_users($args);
					 remove_action('pre_user_query','user_query_count_post_type');
					if(!empty($listpeoples))
					{
						foreach($listpeoples as $key => $val)
						{ 
							if(tevolution_get_posts_count($val->ID) > 1)
							{
								$listing = __('Posts',DOMAIN);
							}
							elseif(tevolution_get_posts_count($val->ID) <= 1)
							{
								$listing = __('Post',DOMAIN);
							}
							
						?>
							<li class="clearfix">
									<a href="<?php echo get_author_posts_url($val->ID);?>">
								  <?php 
								  
								  if ( get_user_meta($val->ID,'profile_photo',true) != '' ) { echo '<img src="'.get_user_meta($val->ID,'profile_photo',true).'" alt="'.$val->display_name.'" title="'.$val->display_name.'"  width="'.apply_filters('tev_widget_photo_size',60).'"/>'; } 
										else { 		echo get_avatar($val->ID, apply_filters('tev_widget_photo_size',60)); 	}
								  ?></a> 
						   
								<p><a href="<?php echo get_author_posts_url($val->ID);?>"><?php echo $val->display_name; ?> </a> <br /> <?php _e('Submited',DOMAIN)?>: <?php echo tevolution_get_posts_count($val->ID); ?> <?php echo $listing; ?>  </p>
							</li>
					<?php }
					}
					else
					{
						echo '<div>'.sprintf(__('There is no user registered with %s role.',DOMAIN),$role).'</div>';
					}?>
        <?php
	    echo '</ul></div>';
		echo $after_widget;
	}
	function update($new_instance, $old_instance) {
	//save the widget
 		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['category'] = strip_tags($new_instance['category']);
		$instance['no_user'] = strip_tags($new_instance['no_user']);
		$instance['post_link'] = strip_tags($new_instance['post_link']);
		$instance['role'] = strip_tags($new_instance['role']);
		return $instance;
	}
	function form($instance) {
 	//widgetform in backend
		global $wp_roles;
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'category' => '', 'no_user' => '','role'=>'' ) );
		$title = strip_tags($instance['title']);
		$category = strip_tags($instance['category']);
		$no_user = strip_tags($instance['no_user']);
		$post_link = strip_tags($instance['post_link']);
		$role = strip_tags($instance['role']);
?>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title',DOMAIN);?>:
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'role' ); ?>"><?php _e('User role',DOMAIN);?>: 
		<select class="widefat" id="<?php echo $this->get_field_id( 'role' ); ?>" name="<?php echo $this->get_field_name( 'role' ); ?>">
		<option value="" ><?php echo esc_html( __('Select role',DOMAIN) ); ?></option>
		<?php
		foreach ( $wp_roles->role_names as $role => $name ) 
		{ ?>
			<option value="<?php echo esc_attr( $role ); ?>" <?php if($role == $instance['role']) {?> selected="selected" <?php } ?>><?php echo esc_html( $name ); ?></option>
	<?php
		}
		?>
		</select>
	</label>	
</p>
<p>
  <label for="<?php echo $this->get_field_id('no_user'); ?>"><?php _e('Number of posts to display.',DOMAIN);?>:
    <input class="widefat" id="<?php echo $this->get_field_id('no_user'); ?>" name="<?php echo $this->get_field_name('no_user'); ?>" type="text" value="<?php echo esc_attr($no_user); ?>" />
  </label>
</p>
<?php
	}
}
add_action('widgets_init','tev_support_author_listing');
/* show widget only if theme support */
function tev_support_author_listing(){
	if(current_theme_supports('tevolution_author_listing'))
		return register_widget("tevolution_author_listing");
}
?>