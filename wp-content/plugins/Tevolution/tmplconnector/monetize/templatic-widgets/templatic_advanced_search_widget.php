<?php
/*
 * Create the templatic advanced search widget
 */
class templatic_advanced_search extends WP_Widget {
	function templatic_advanced_search() {
	//Constructor
		$widget_ops = array('classname' => 'widget templatic-advanced-search', 'description' => __('Display search fields for a specific post type. Custom fields selected to show inside the Advanced search form will also show inside this widget. Works best in sidebar areas.',DOMAIN),'before_widget'=>'<div class="column_wrap">' );
		$this->WP_Widget('templatic_advanced_search', __('T &rarr; Advanced Search',DOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
	// prints the widget
		extract($args, EXTR_SKIP);
	
		$title = empty($instance['title']) ? __("Advanced Search",DOMAIN) : apply_filters('widget_title', $instance['title']); 		
		$post_type = empty($instance['post_type']) ? 'post' : apply_filters('widget_post_type', $instance['post_type']);
		echo $before_widget;
		if (function_exists('icl_register_string')) {	
			icl_register_string(DOMAIN,'templatic_about_title'.$title,$title);
			$title = icl_t(DOMAIN, 'templatic_about_title'.$title,$title);
		}
		if ( $title <> "" ) { 
			echo $before_title;
			echo $title;
			echo $after_title;
		}
		?>
          <div class="templatic_advanced_search">
          <?php echo do_shortcode("[advance_search_page post_type='".$post_type."']"); ?>
          </div>
        <?php		
		echo $after_widget;
	}
	function update($new_instance, $old_instance) {
	//save the widget
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['post_type'] = $new_instance['post_type'];
		return $instance;
	}
	function form($instance) {
	//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'title' => '',  'post_type' => '',) );
		$title = ($instance['title']) ? $instance['title'] : __("Advanced Search",DOMAIN);
		$current_post_type = ($instance['post_type']) ? $instance['post_type'] : 'post';
	?>
	<p>
	  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:',DOMAIN);?>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	  </label>
	</p>	
	<p>
    	<label for="<?php echo $this->get_field_id('post_type');?>" ><?php _e('Post Type:',DOMAIN);?>     </label>	
    	<select  id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widefat">        	
			<option value="post"><?php _e("Post");?></option>
    <?php
		$all_post_types = get_option("templatic_custom_post");
		foreach($all_post_types as $key=>$post_type){ ?>
			<option value="<?php echo $key;?>" <?php if($key == $current_post_type){ echo 'selected="selected"';}?>><?php echo esc_attr($post_type['label']);?></option>
     <?php }?>	
    	</select>
   
    	<span class="description"><?php _e('Select the post type you wish to display on an advanced search form.',DOMAIN);?></span>
    </p>
	<?php
	}
}
/*
 * templatic about us widget init
 */
add_action( 'widgets_init', create_function('', 'return register_widget("templatic_advanced_search");') );
?>