<?php
/* direction widget map*/
class widget_googlemap_diection_widget extends WP_Widget {

	function widget_googlemap_diection_widget() {	
		$widget_ops = array('classname' => 'widget googlemap direction widget
		widget', 'description' => __('Shows a map of the posts location. By entering their address, visitors can also get directions to the location. Use the widget in detail page sidebar areas.',DOMAIN) );		
		$this->WP_Widget('widget_googlemap_diection_widget', __('T &rarr; Detail Page Map',DOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
		global $current_cityinfo;
		$width = empty($instance['width']) ? '940' : apply_filters('widget_width', $instance['width']);
      	$heigh = empty($instance['heigh']) ? '425' : apply_filters('widget_heigh', $instance['heigh']);		
			
		if(is_single()){ 
			global $post;
			if(get_post_meta(get_the_ID(),'_event_id',true)){
				$post->ID=get_post_meta(get_the_ID(),'_event_id',true);
			}	

			$geo_latitude = get_post_meta($post->ID,'geo_latitude',true);
			$geo_longitude = get_post_meta($post->ID,'geo_longitude',true);
			$address = get_post_meta($post->ID,'address',true);
			$map_type =get_post_meta($post->ID,'map_view',true);			
			$googlemap_setting=get_option('city_googlemap_setting');
			if($address && $googlemap_setting['direction_map']!='yes'){				
			?>
				   <div id="tevolution_location_map" class="widget">
						<div class="tevolution_google_map" id="tevolution_detail_google_map_id"> 
						<?php include_once (plugin_dir_path( __FILE__ ).'google_map_detail.php');?> 
						</div>  <!-- google map #end -->
				   </div>
			<?php
			}
		}
          
	}
	/*Widget update function */
	function update($new_instance, $old_instance) {
		//save the widget
		return $new_instance;
	}
	
	/*Widget admin form display function */
	function form($instance) {
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'width' => '', 'heigh' => '') );		
		$width = strip_tags($instance['width']);
		$heigh = strip_tags($instance['heigh']);
		?>
	
		<p>
		 <label for="<?php echo $this->get_field_id('heigh'); ?>"><?php _e('Map Height <small>(default height: 425px) to change, only enter a numeric value.)</small>',DOMAIN);?>:
		 <input class="widefat" id="<?php echo $this->get_field_id('heigh'); ?>" name="<?php echo $this->get_field_name('heigh'); ?>" type="text" value="<?php echo attribute_escape($heigh); ?>" />
		 </label>
	    </p>
	    <?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("widget_googlemap_diection_widget");') );
?>