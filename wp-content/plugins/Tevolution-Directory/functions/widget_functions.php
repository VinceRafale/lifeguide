<?php
/* Widgets - widget_functions.php */
/*
 * Home page display google map as per current city related post type data on map
 */
add_action('widgets_init','directory_plugin_widgets_init');
function directory_plugin_widgets_init()
{	
	register_widget('directory_neighborhood');
	register_widget('directory_search_location');
	register_widget('directory_featured_homepage_listing');
	register_widget('directory_featured_category_list');
	register_widget('directory_mile_range_widget');
	register_widget('directory_related_event_widget');
}
/*   
	Name : directory_neighborhood
	Desc: neighborhood posts Widget (particular category) 
*/
class directory_neighborhood extends WP_Widget {
	function directory_neighborhood() {
	//Constructor
		$widget_ops = array('classname' => 'widget In the neighborhood', 'description' => __('Display posts that are in the vicinity of the post that is currently displayed. Use in detail page sidebar areas.',DOMAIN) );
		$this->WP_Widget('directory_neighborhood', __('T &rarr; In The Neighbourhood',DOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);		
		$title = empty($instance['title']) ? __("Nearest Listing",DOMAIN) : apply_filters('widget_title', $instance['title']);
		$post_type = empty($instance['post_type']) ? 'listing' : apply_filters('widget_post_type', $instance['post_type']);
		$post_number = empty($instance['post_number']) ? '5' : apply_filters('widget_post_number', $instance['post_number']);
		$radius = empty($instance['radius']) ? '0' : apply_filters('widget_radius', $instance['radius']);
		$closer_factor = empty($instance['closer_factor']) ? 0 : apply_filters('widget_closer_factor', $instance['closer_factor']);
		$show_list = empty($instance['show_list']) ? '0' : apply_filters('widget_show_list', $instance['show_list']);
		$radius_measure= empty($instance['radius_measure']) ? '0' : apply_filters('widget_radius_measure', $instance['radius_measure']);		
		
		global $miles,$wpdb,$post,$single_post,$wp_query,$current_cityinfo;
		global $current_post;
 		$current_post = $post->ID;	
		//get the current post details
		$current_post_details=get_post($post->ID);
		echo $before_widget;
		?>          
		<div class="neighborhood_widget">
		<?php
          echo '<h3 class="widget-title">'.$title.'</h3>';
		if($show_list){
			$miles=(strtolower($radius_measure) == strtolower('Kilometer'))? $radius * 0.621: $radius;
				
			add_filter('posts_where','directory_nearby_filter');
			if(is_plugin_active('wpml-translation-management/plugin.php')){
				add_filter('posts_where', 'wpml_listing_milewise_search_language');
			}
			$args = array(
				'post__not_in'        => array($current_post) ,
				'post_status'         => 'publish',
				'post_type'           => $post_type,
				'posts_per_page'      => $post_number,				
				'ignore_sticky_posts' => 1,
				'orderby'             => 'rand'
			);
			if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
			{
				add_filter('posts_where', 'location_multicity_where');
			}
			$wp_query_near = new WP_Query($args);			
			if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
			{
				remove_filter('posts_where', 'location_multicity_where');
			}
			if(is_plugin_active('wpml-translation-management/plugin.php')){
				remove_filter('posts_where', 'wpml_listing_milewise_search_language');
			}
			if($wp_query_near->have_posts()):
				echo '<ul class="nearby_distance">';
				while($wp_query_near->have_posts())
				{
					$wp_query_near->the_post();
					echo '<li class="nearby clearfix">';
					
					if ( has_post_thumbnail()){
						$post_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'directory-neighbourhood-thumb');						
						$post_images=$post_img[0];
					}else{
						$post_img = bdw_get_images_plugin(get_the_ID(),'directory-neighbourhood-thumb');					
						$post_images = $post_img[0]['file'];
					}
					$image=($post_images)?$post_images : TEVOLUTION_DIRECTORY_URL.'images/no-image.png';
					?>
                         <div class='nearby_image'>
                         <a href="<?php echo get_permalink($post->post_id); ?>">
                         	<img src="<?php echo $image?>" alt="<?php echo get_the_title($post->post_id); ?>" title="<?php echo get_the_title($post->post_id); ?>" class="thumb <?php echo (!$post_images)?'no_image':''?>" />
                         </a>
                         </div>
                         <div class='nearby_content'>
                         	<h4><a href="<?php echo get_permalink($post->post_id); ?>"><?php the_title(); ?></a></h4>
							<p class="address"><?php $address = get_post_meta(get_the_ID(),'address',true); echo $address; ?></p>
                         </div>
					<?php
					echo '</li>';
                         
				}
				echo '</ul>';
			else:
          		echo sprintf(__('Sorry! There is no near by results found',DOMAIN));
			endif;
			remove_filter('posts_where','nearby_filter'); 
			wp_reset_query();
			
		}else{
			
			$geo_latitude = (get_post_meta($post->ID,'geo_latitude',true))?get_post_meta($post->ID,'geo_latitude',true): $_SESSION['custom_fields']['geo_latitude'];
			if($geo_latitude)
			{
				$geo_latitude_arr = explode('.',$geo_latitude);
				$geo_latitude=($geo_latitude_arr[1])? $geo_latitude_arr[0].'.'.substr($geo_latitude_arr[1],0,$closer_factor): $geo_latitude_arr[0];
			}
			$geo_longitude = (get_post_meta($post->ID,'geo_longitude',true))?get_post_meta($post->ID,'geo_longitude',true) : $_SESSION['custom_fields']['geo_longitude'];
			if($geo_longitude)
			{
				$geo_latitude_arr = explode('.',$geo_longitude);
				$geo_longitude=($geo_latitude_arr[1])?$geo_latitude_arr[0].'.'.substr($geo_latitude_arr[1],0,$closer_factor) :$geo_latitude_arr[0];			
			}
			if($current_cityinfo['city_id'])
			{ 
				$post_city_id = $current_cityinfo['city_id'];				
				$post_lat = $wpdb->get_col("select m.post_id from $wpdb->posts p,$wpdb->postmeta m where p.ID=m.post_id AND p.post_type='".$post_type."' AND  m.meta_key like \"geo_latitude\" and (m.meta_value like\"$geo_latitude%\") and m.post_id!=\"$current_post\" and m.post_id in (select post_id from $wpdb->postmeta where meta_key='post_city_id' and ($wpdb->postmeta.meta_value = \"$post_city_id\" or $wpdb->postmeta.meta_value='' or $wpdb->postmeta.meta_value='0'))");
				
				$post_lng = $wpdb->get_col("select m.post_id from $wpdb->posts p, $wpdb->postmeta m where p.ID=m.post_id AND p.post_type='".$post_type."' AND m.meta_key like \"geo_longitude\" and (m.meta_value like\"$geo_longitude%\") and m.post_id!=\"$current_post\" and m.post_id in (select post_id from $wpdb->postmeta where meta_key='post_city_id' and ($wpdb->postmeta.meta_value = \"$post_city_id\" or $wpdb->postmeta.meta_value='' or $wpdb->postmeta.meta_value='0'))");				
			}else
			{
				$post_lat = $wpdb->get_col("select m.post_id from $wpdb->posts p, $wpdb->postmeta m where p.ID=m.post_id AND p.post_type='".$post_type."' AND m.meta_key like \"geo_latitude\" and (m.meta_value like\"$geo_latitude%\") and m.post_id!=\"$current_post\"");
				$post_lng = $wpdb->get_col("select m.post_id from $wpdb->posts p, $wpdb->postmeta m where p.ID=m.post_id AND p.post_type='".$post_type."' AND m.meta_key like \"geo_longitude\" and (m.meta_value like\"$geo_longitude%\") and m.post_id!=\"$current_post\"");
			}			
			
			if(1)
			{
				$post_id_arr = array();
				if($post_lat && $post_lng)
				{
					$post_id_arr = array_intersect($post_lat,$post_lng);
				}
				$post_id_arr = array_slice($post_id_arr,0,$post_number);
				$post_ids = implode(',',$post_id_arr);
			}			
			
			if($post_ids)
			{	
				$args = array(
					'post__in'            => explode(',',$post_ids) ,
					'post_type'           => $post->post_type,
					'posts_per_page'      => $post_number,
					'ignore_sticky_posts' => 1,
					'orderby'             => 'rand'
				);
				if(is_plugin_active('wpml-translation-management/plugin.php')){
					add_filter('posts_where', 'wpml_listing_milewise_search_language');
				}
				$latest_menus = new WP_Query($args);
				if(is_plugin_active('wpml-translation-management/plugin.php')){
					remove_filter('posts_where', 'wpml_listing_milewise_search_language');
				}				
			}			
			if($latest_menus):
				echo '<ul class="nearby_distance">';
				while($latest_menus->have_posts())
				{
					$latest_menus->the_post();
					echo '<li class="nearby clearfix">';
					
					if ( has_post_thumbnail()){
						$post_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'directory-neighbourhood-thumb');						
						$post_images= @$post_img[0];
					}else{
						$post_img = bdw_get_images_plugin(get_the_ID(),'directory-neighbourhood-thumb');					
						$post_images = @$post_img[0]['file'];
					}
					$image=($post_images)?$post_images : TEVOLUTION_DIRECTORY_URL.'images/no-image.png';
					?>
                         <div class='nearby_image'>
                               <a href="<?php echo get_permalink($post->post_id); ?>">
                                   <img src="<?php echo $image; ?>" alt="<?php echo get_the_title($post->post_id); ?>" title="<?php echo get_the_title($post->post_id); ?>" class="thumb <?php echo (!$post_images)?'no_image':''?>" />
                               </a>
                         </div>
                         <div class='nearby_content'>
                        		<h4><a href="<?php echo get_permalink($post->post_id); ?>"><?php the_title(); ?></a></h4>
						<p class="address"><?php $address = get_post_meta($post->post_id,'address',true); echo $address; ?></p>
                         </div>
					<?php
					echo '</li>';
                         
				}
				echo '</ul>';
			else:
				echo sprintf(__('No se econtraron lugares cerca!',DOMAIN));
			endif;
		}		
		
		?>         
          </div>
		<?php
		echo $after_widget;
	}
	function update($new_instance, $old_instance) {
		//save the widget		
		return $new_instance;
	}
	function form($instance) {
		//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array( 'title' => __("Nearest Listing",DOMAIN), 'post_type' => 'listing', 'post_number' => 5, 'closer_factor'=>2 ) );
			$title = strip_tags($instance['title']);
			$post_type = strip_tags($instance['post_type']);
			$post_number = strip_tags($instance['post_number']);
			$post_link = strip_tags($instance['post_link']);
			$closer_factor = strip_tags($instance['closer_factor']);
			$show_list = strip_tags($instance['show_list']);
			$distance_factor = strip_tags($instance['radius']);
			$radius_measure=strip_tags($instance['radius_measure']);
		?>
          <script type="text/javascript">										
			function select_show_list(id,div_def,div_custom)
			{
				var checked=id.checked;
				jQuery('#'+div_def).slideToggle('slow');
				jQuery('#'+div_custom).slideToggle('slow');
			}			
		</script>
          <p>
               <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title',DOMAIN);?>
               <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
               </label>
          </p>
          <p>
               <label for="<?php echo $this->get_field_id('post_type');?>" ><?php _e('Select Post:',DOMAIN);?>     </label>	
               <select  id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widefat">        	
				<?php
                    $all_post_types = get_option("templatic_custom_post");
                    foreach($all_post_types as $key=>$post_types){
					?>
					<option value="<?php echo $key;?>" <?php if($key== $post_type)echo "selected";?>><?php echo esc_attr($post_types['label']);?></option>
					<?php
                    }
                    ?>	
               </select>
          </p>
          
          <p>
               <label for="<?php echo $this->get_field_id('post_number'); ?>"><?php _e('Number of posts',DOMAIN);?>
               <input class="widefat" id="<?php echo $this->get_field_id('post_number'); ?>" name="<?php echo $this->get_field_name('post_number'); ?>" type="text" value="<?php echo attribute_escape($post_number); ?>" />
               </label>
          </p>
          
          <p>
               <label for="<?php echo $this->get_field_id('show_list'); ?>">
               <input id="<?php echo $this->get_field_id('show_list'); ?>" name="<?php echo $this->get_field_name('show_list'); ?>" type="checkbox" value="1" <?php if($show_list =='1'){ ?>checked=checked<?php } ?>style="width:10px;" onclick="select_show_list(this,'<?php echo $this->get_field_id('show_list_normal'); ?>','<?php echo $this->get_field_id('show_list_distance'); ?>');" />
               <?php _e('<b>Show list by distance?</b>',DOMAIN);?>              
               </label>
          
          </p>          
          
		<p id="<?php echo $this->get_field_id('show_list_normal'); ?>" style="<?php if($show_list =='1'){ ?>display:none;<?php }else{?>display:block;<?php }?>">
               <label for="<?php echo $this->get_field_id('closer_factor'); ?>"><?php _e('Show Listings From',DOMAIN);?>
               <select id="<?php echo $this->get_field_id('closer_factor'); ?>" name="<?php echo $this->get_field_name('closer_factor'); ?>">
                    <option value="0" <?php if(attribute_escape($closer_factor)=='0'){ echo 'selected="selected"';} ?>><?php _e('So Far Away',DOMAIN);?></option>
                    <option value="1" <?php if(attribute_escape($closer_factor)=='1'){ echo 'selected="selected"';} ?>><?php _e('Far Away',DOMAIN);?></option>
                    <option value="2" <?php if(attribute_escape($closer_factor)=='2'){ echo 'selected="selected"';} ?>><?php _e('At Some Distance',DOMAIN);?></option>
                    <option value="3" <?php if(attribute_escape($closer_factor)=='3'){ echo 'selected="selected"';} ?>><?php _e('Nearer',DOMAIN);?></option>
                    <option value="4" <?php if(attribute_escape($closer_factor)=='4'){ echo 'selected="selected"';} ?>><?php _e('Very Near',DOMAIN);?></option>
               </select>
               </label>
		</p> 
          
          <div id="<?php echo $this->get_field_id('show_list_distance'); ?>" style="<?php if($show_list =='1'){ ?>display:block;<?php }else{?>display:none;<?php }?>"> 
          <p>            
               <label for="<?php echo $this->get_field_id('radius'); ?>"><?php _e('Select Distance',DOMAIN);?>
               <select id="<?php echo $this->get_field_id('radius'); ?>" name="<?php echo $this->get_field_name('radius'); ?>">
                    <option value="1" <?php if(attribute_escape($distance_factor)=='1'){ echo 'selected="selected"';} ?>><?php _e('1',DOMAIN); ?></option>
                    <option value="5" <?php if(attribute_escape($distance_factor)=='5'){ echo 'selected="selected"';} ?>><?php _e('5',DOMAIN); ?></option>
                    <option value="10" <?php if(attribute_escape($distance_factor)=='10'){ echo 'selected="selected"';} ?>><?php _e('10',DOMAIN); ?></option>
                    <option value="100" <?php if(attribute_escape($distance_factor)=='100'){ echo 'selected="selected"';} ?>><?php _e('100',DOMAIN); ?></option>
                    <option value="1000" <?php if(attribute_escape($distance_factor)=='1000'){ echo 'selected="selected"';} ?>><?php _e('1000',DOMAIN); ?></option>
                    <option value="5000" <?php if(attribute_escape($distance_factor)=='5000'){ echo 'selected="selected"';} ?>><?php _e('5000',DOMAIN); ?></option>      
               </select>
               </label>             
		</p> 
          <p>            
               <label for="<?php echo $this->get_field_id('radius_measure'); ?>"><?php _e('Display By',DOMAIN);?>
               <select id="<?php echo $this->get_field_id('radius_measure'); ?>" name="<?php echo $this->get_field_name('radius_measure'); ?>">
                    <option value="kilometer" <?php if(attribute_escape($radius_measure)=='kilometer'){ echo 'selected="selected"';} ?>><?php _e('Kilometers',DOMAIN); ?></option>
                    <option value="miles" <?php if(attribute_escape($radius_measure)=='miles'){ echo 'selected="selected"';} ?>><?php _e('Miles',DOMAIN); ?></option>                    
               </select>
               </label>             
		</p> 
          </div>
		<?php
	}
}
/* End of directory_neighborhood*/
/*
	Name : directory_search_location
    Desc : location wise search widget
*/
class directory_search_location extends WP_Widget {
	function directory_search_location() {
		//Constructor
		$widget_ops = array('classname' => 'search_location', 'description' => __('Enter an address to get a list of nearby posts. Use in header and sidebar widget areas.',DOMAIN) );
		$this->WP_Widget('directory_search_location', __('T &rarr; Search by Address',DOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
		// prints the widget
		extract($args, EXTR_SKIP);
		$title = empty($instance['title']) ? 'Search Near By Location' : apply_filters('widget_title', $instance['title']);
		$post_type = empty($instance['post_type']) ? 'listing' : apply_filters('widget_post_type', $instance['post_type']);
		$miles_search = empty($instance['miles_search']) ? '' : apply_filters('widget_miles_search', $instance['miles_search']);
		$radius_measure= empty($instance['radius_measure']) ? 'miles' : apply_filters('widget_radius_measure', $instance['radius_measure']);		
		$radius_type=($radius_measure=='miles')? __('Miles',DOMAIN) : __('Kilometers',DOMAIN);
		echo $before_widget;
		$first_char_post_type = substr($post_type, 0, 1);		
		$vowel = array('a','e','i','o','u');
		if(in_array($first_char_post_type[0],$vowel))
		{
			$vowel_text = __('an',DOMAIN);
		}
		else
		{
			$vowel_text = __('a',DOMAIN);
		}

		$search_txt= __('Qué?',DOMAIN);
		if($miles_search==1){
			$class=' search_by_mile_active';
		}
		$search_id= rand();
		$distance_factor = @$_REQUEST['radius'];
		if(isset($_REQUEST['location'])) { $location= @$_REQUEST['location']; }else{$location='';  }
		if(isset($_REQUEST['s'])) { $what= @$_REQUEST['s']; }else{$what='';  }
		echo '<div class="search_nearby_widget'.$class.'">';
		if($instance['title']){echo '<h3 class="widget-title">'.$title.'</h3>';}
		?>
		<form method="get" id="searchform" action="<?php echo home_url(); ?>/">
          	<?php foreach($post_type as $val):?>
               <input type="hidden" name="post_type[]" value="<?php echo $val;?>" />
               <?php endforeach;?>
          	<input type="hidden" name="nearby" value="search" />
               <input type="text" value="<?php echo $what; ?>" name="s" id="search_near-<?php echo $search_id;?>" class="searchpost" onfocus="if (this.placeholder == '<?php echo $search_txt;?>') {this.placeholder = '';}" onblur="if (this.placeholder == '') {this.placeholder = '<?php echo $search_txt;?>';}" placeholder="<?php if(isset($_REQUEST['s']) && trim($_REQUEST['s']) == '') { echo $search_txt;} else { echo $search_txt; }?>"/>
               
               <input type="text" name="location" id="location" class="location" onfocus="if (this.placeholder == '<?php echo 'Dónde?' /*_e('Where?',DOMAIN); */?>') {this.placeholder = '';}" onblur="if (this.placeholder == '') {this.placeholder = '<?php echo 'Dónde?' /*_e('Where?',DOMAIN); */?>';}" value="<?php echo $location; ?>"  placeholder="<?php _e('Dónde?',DOMAIN); ?>"/>
               <?php if($miles_search==1):?>
                <select id="radius" name="radius">
                    <option value=''><?php _e('Within?',DOMAIN); ?></option>
                    <option value="1" <?php if(isset($distance_factor) && attribute_escape($distance_factor)=='1'){ echo 'selected="selected"';} ?>>1 <?php echo ($radius_measure=='miles')? __('Mile',DOMAIN) : __('Kilometer',DOMAIN);; ?></option>
                    <option value="5" <?php if(isset($distance_factor) && attribute_escape($distance_factor)=='5'){ echo 'selected="selected"';} ?>>5 <?php echo $radius_type; ?></option>
                    <option value="10" <?php if(isset($distance_factor) && attribute_escape($distance_factor)=='10'){ echo 'selected="selected"';} ?>>10 <?php echo $radius_type; ?></option>
                    <option value="100" <?php if(isset($distance_factor) && attribute_escape($distance_factor)=='100'){ echo 'selected="selected"';} ?>>100 <?php echo $radius_type; ?></option>
                    <option value="1000" <?php if(isset($distance_factor) && attribute_escape($distance_factor)=='1000'){ echo 'selected="selected"';} ?>>1000 <?php echo $radius_type; ?></option>
                    <option value="5000" <?php if(isset($distance_factor) && attribute_escape($distance_factor)=='5000'){ echo 'selected="selected"';} ?>> 5000 <?php echo $radius_type; ?></option>      
               </select>               
               
                <!-- Matthias -->
               
               <?php endif;?>
               <input type="hidden" name="radius_type" value="<?php echo $radius_measure?>" />
               <input type="submit" class="sgo" onclick="find_click();" value="<?php echo _e('Buscar',DOMAIN); ?>" />
          </form>
		<script type="text/javascript">
			function find_click()
			{
				if(jQuery('#search_near-<?php echo $search_id;?>').val() == '')
				{
					jQuery('#search_near-<?php echo $search_id;?>').val(' ');
				}
				if(jQuery('#location').val() == '<?php echo __('Address',DOMAIN); ?>')
				{
					jQuery('#location').val('');
				}
			}
          </script>
		<?php
		echo '</div>';
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance) {
		//save the widget
		return $new_instance;
	}
	function form($instance) {
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Search Near By Location' ,'post_type' => 'post' ) );		
		$title = strip_tags($instance['title']);
		$post_type = $instance['post_type'];
		$miles_search=strip_tags($instance['miles_search']);
		$radius_measure=strip_tags($instance['radius_measure']);		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title',DOMAIN);?>:
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('post_type');?>" ><?php _e('Select Post:',DOMAIN);?>     </label>	
			<select  id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>[]" multiple="multiple" class="widefat">        	
				<?php
                    $all_post_types = get_option("templatic_custom_post");
                    foreach($all_post_types as $key=>$post_types){
					?>
					<option value="<?php echo $key;?>" <?php if(in_array($key,$post_type))echo "selected";?>><?php echo esc_attr($post_types['label']);?></option>
					<?php
                    }
                    ?>	
			</select>
		</p>
           <p>
               <label for="<?php echo $this->get_field_id('miles_search'); ?>">
               <input id="<?php echo $this->get_field_id('miles_search'); ?>" name="<?php echo $this->get_field_name('miles_search'); ?>" type="checkbox" value="1" <?php if($miles_search =='1'){ ?>checked=checked<?php } ?>style="width:10px;"  />
               <?php _e('<b>Search By Distance?</b>',DOMAIN);?>              
               </label>
          </p>   
            <p>            
               <label for="<?php echo $this->get_field_id('radius_measure'); ?>"><?php _e('Search By',DOMAIN);?>
               <select id="<?php echo $this->get_field_id('radius_measure'); ?>" name="<?php echo $this->get_field_name('radius_measure'); ?>">
                    <option value="kilometer" <?php if(attribute_escape($radius_measure)=='kilometer'){ echo 'selected="selected"';} ?>><?php _e('Kilometers',DOMAIN); ?></option>
                    <option value="miles" <?php if(attribute_escape($radius_measure)=='miles'){ echo 'selected="selected"';} ?>><?php _e('Miles',DOMAIN); ?></option>                    
               </select>
               </label>             
		</p> 
		<?php			
	}
}
/* End of location wise search widget */
/*
	Class: directory_featured_homepage_listing
	Desc: Widget of show the featured listing on home page
*/
class directory_featured_homepage_listing extends WP_Widget {
	
	function directory_featured_homepage_listing() {
		//Constructor
		global $thumb_url;
		$widget_ops = array('classname' => 'widget special', 'description' =>__('Showcase posts from any post type, including those created by you. Featured posts are displayed at the top. Works best in the Homepage - Main Content area.',DOMAIN)) ;
		$this->WP_Widget('directory_featured_homepage_listing',__('T &rarr; Homepage Featured Posts',DOMAIN), $widget_ops);
	}
	
	function widget($args, $instance) {
		// prints the widget
		global $current_cityinfo,$htmlvar_name;
		extract($args, EXTR_SKIP);		
		//echo $before_widget;
		$title = empty($instance['title']) ? __("Featured Listing",DOMAIN) : apply_filters('widget_title', $instance['title']);
		$category = empty($instance['category']) ? '' : apply_filters('widget_category', $instance['category']);
		$number = empty($instance['number']) ? '5' : apply_filters('widget_number', $instance['number']);
		$my_post_type = empty($instance['post_type']) ? 'listing' : apply_filters('widget_post_type', $instance['post_type']);
		$link = empty($instance['link']) ? '#' : apply_filters('widget_link', $instance['link']);
		$text = empty($instance['text']) ? '' : apply_filters('widget_text', $instance['text']);
		$view = empty($instance['view']) ? 'list' : apply_filters('widget_view', $instance['view']);
		$read_more = empty($instance['read_more']) ? '' : apply_filters('widget_read_more', $instance['read_more']);
		
		global $post,$wpdb;
		$post_widget_count = 1;
		
		$cus_post_type = empty($instance['post_type']) ? 'listing' : $instance['post_type'];
	
		$heading_type = directory_fetch_heading_post_type($cus_post_type);
		
		if(count($heading_type) > 0)
		{
			foreach($heading_type as $key=> $heading)
			{	
				$htmlvar_name[$heading] = get_directory_listing_customfields($cus_post_type,$heading,$key);//custom fields for custom post type..
			}
		}
		remove_filter('pre_get_posts', 'home_page_feature_listing');
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $my_post_type,'public'   => true, '_builtin' => true ));
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			if(@$category!=""){
				$category_ID =  get_term_by( 'slug',$category,  $taxonomies[0] );	
				$category=$category_ID->slug;
				$title=ucfirst($category_ID->name);
			}
		}
		
		if($category!=""){
			$args=array(
					'post_type' => $my_post_type,
					'posts_per_page' => $number,				
					'post_status' => 'publish',				
					'tax_query' => array(
								array(
									'taxonomy' => $taxonomies[0],
									'field' => 'slug',
									'terms' => explode(",",$category),								
								)
						),			
			);
		}else{
			if(is_active_addons('custom_taxonomy')){
				$args=array(
					'post_type' => $my_post_type,
					'post_status' => 'publish',				
					'posts_per_page' => $number,
					);
			}
		}
		$my_query = null;
		
		remove_filter('posts_orderby', 'home_page_feature_listing_orderby');
		add_filter('posts_orderby', 'directory_feature_listing_orderby');
		if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
		{
			add_filter('posts_where', 'location_multicity_where');
		}
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			add_filter('posts_where', 'wpml_listing_milewise_search_language');
		}
		$my_query = new WP_Query($args);	
	
		if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
		{
			add_filter('posts_where', 'location_multicity_where');
		}
		if(is_plugin_active('wpml-translation-management/plugin.php')){
			remove_filter('posts_where', 'wpml_listing_milewise_search_language');
		}
		global $htmlvar_name;
		$heading_type = directory_fetch_heading_post_type($my_post_type);
		if(count($heading_type) > 0)
		{
			foreach($heading_type as $key=>$heading)
			{	
				$htmlvar_name[$heading] = get_directory_listing_customfields($my_post_type,$heading,$key);//custom fields for custom post type..
			}
		}
		
		?>
        <div id="widget_loop_<?php echo $my_post_type?>" class="widget widget_loop_taxonomy widget_loop_<?php echo $my_post_type?> <?php echo $view?>">			
          <?php if( $my_query->have_posts()): ?>
			<?php if($title){?><h3 class="widget-title"><span><?php echo $title;?></span><?php if($link){?><a href="<?php echo $link;?>" class="more" ><?php echo $text; ?></a><?php }?></h3> <?php }?>
			<!-- widget_loop_taxonomy_wrap START -->
			<div class="widget_loop_taxonomy_wrap">
          	<?php while($my_query->have_posts()) : $my_query->the_post();?> 
				<!-- inside loop div start -->
               	<div id="<?php echo $my_post_type.'_'.get_the_ID(); ?>" <?php if((get_post_meta($post->ID,'featured_h',true) == 'h')){ post_class('post featured_post');} else { post_class('post');}?>>
               	 
				<?php   $post_id=get_the_ID();
						if(get_post_meta(get_the_ID(),'_event_id',true)){ $post_id=get_post_meta(get_the_ID(),'_event_id',true); }
                              if ( has_post_thumbnail()){
                                   $post_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'directory-listing-image');						
                                   $post_images= @$post_img[0];
                              }else{
                                   $post_img = bdw_get_images_plugin($post_id,'directory-listing-image');					
                                   $post_images = @$post_img[0]['file'];
                              }
                              $image=($post_images)?$post_images : TEVOLUTION_DIRECTORY_URL.'images/noimage-220x150.jpg';
					     $featured=get_post_meta(get_the_ID(),'featured_h',true);
						$tmpdata = get_option('templatic_settings');
                              ?>
							  <!-- start fp_image -->
                              <div class='<?php echo $my_post_type?>_image fp_image'>
								<a href="<?php echo get_permalink($post->post_id); ?>">
                              	<?php if($featured=='h'){echo '<span class="featured_tag">'.__('Featured',DOMAIN).'</span>';}?>
                                   <img src="<?php echo $image?>" alt="<?php echo get_the_title($post->post_id); ?>" title="<?php echo get_the_title($post->post_id); ?>" class="thumb borde1"/></a>
                              </div>
							  <!-- End fp_image-->
                              <!-- start fp_entry -->
                              <div class="fp_entry">
							<?php do_action('home_featured_before_title');
                                   $post_type= $post->post_type; 							
                                   do_action('supreme_before-title_'.$post_type);
                                   ?>							
                                   <h2 itemprop="name"><a href="<?php echo get_permalink($post->post_id); ?>"><?php the_title(); ?></a></h2> <?php do_action('tevolution_title_text',$post);  ?>                                         
                                   <?php 
                                   do_action('directory_featured_widget_listing_postinfo');
                                   do_action('supreme_after-title_'.$post_type);	
                                   
                                   do_action('home_featured_after_title',$instance);
                                   do_action('home_featured_before_content');
                                   do_action('home_featured_after_content');
                                   do_action('templ_the_taxonomies');
                                   
                                   echo "<div class='rev_pin'><ul>";								
                                   if(current_theme_supports('tevolution_my_favourites') && function_exists('tevolution_favourite_html')){
								echo '<li>';
								tevolution_favourite_html();	
								echo '</li>';
                                   }
                                   echo '<li>';
                                   do_action('directory_the_comment');   
                                   echo '</li></ul></div>'; ?>
                              </div> <!-- End fp_entry -->
				</div> <!-- inside loop div end -->     
            <?php endwhile; wp_reset_query();?>
			</div>
			<!-- widget_loop_taxonomy_wrap eND -->
			<?php endif; ?>
			</div> <!-- widget_loop_taxonomy -->
          <?php
		
	 	//echo $after_widget;
	}
	
	
	function update($new_instance, $old_instance) {
		//save the widget
		return $new_instance;
	}
	function form($instance) {
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'title' => __("Featured Listing",DOMAIN), 'category' => '', 'number' => 5 , 'post_type' => 'listing' , 'link' => '#', 'text' => __("View All",DOMAIN), 'view' => 'list','read_more' => '' ) );
		$title = strip_tags($instance['title']);
		$category = strip_tags($instance['category']);
		$number = strip_tags($instance['number']);
		$my_post_type = strip_tags($instance['post_type']);
		$link = strip_tags($instance['link']);
		$text = strip_tags($instance['text']);
		$view = strip_tags($instance['view']);
		$read_more = strip_tags($instance['read_more']);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title',DOMAIN);?>: 
               	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
               </label>
          </p>
		<p>
          	<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('View All Text',DOMAIN);?>: 
              		<input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo attribute_escape($text); ?>" />
               </label>
          </p>
		<p>
          	<label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('View All Link URL: (ex.http://templatic.com/events)',DOMAIN);?> 
               	<input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo attribute_escape($link); ?>" />
               </label>
		</p>
		<p>
               <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts',DOMAIN);?>:
               	<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo attribute_escape($number); ?>" />
               </label>
		</p>
          <p>
               <label for="<?php echo $this->get_field_id('view'); ?>"><?php _e('View',DOMAIN)?>:
               <select id="<?php echo $this->get_field_id('view'); ?>" name="<?php echo $this->get_field_name('view'); ?>">
                         <option value="list" <?php if($view == 'list'){ echo 'selected="selected"';}?>><?php _e('List view',DOMAIN);?></option>
                         <option value="grid" <?php if($view == 'grid'){ echo 'selected="selected"';}?>><?php _e('Grid view',DOMAIN);?></option>
               </select>
               </label>
          </p>
		<p>
               <label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post Type',DOMAIN)?>:
               <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
				 <?php
					$all_post_types = get_option("templatic_custom_post");
					foreach($all_post_types as $key=>$post_type){?>
						<option value="<?php echo $key;?>" <?php if($key== $my_post_type)echo "selected";?>><?php echo esc_attr($post_type['label']);?></option>
				<?php }?>	
               </select>
               </label>
		</p>
           <p>
             <label for="<?php echo $this->get_field_id('content_limit'); ?>"><?php _e('Limit content to', DOMAIN); ?>: </label> <input type="text" id="<?php echo $this->get_field_id('image_alignment'); ?>" name="<?php echo $this->get_field_name('content_limit'); ?>" value="<?php echo esc_attr(intval($instance['content_limit'])); ?>" size="3" /> <?php _e('characters', DOMAIN); ?>
          </p>
		  <p>
          	<label for="<?php echo $this->get_field_id('read_more'); ?>"><?php _e('Read More Text',DOMAIN);?>: 
              		<input class="widefat" id="<?php echo $this->get_field_id('read_more'); ?>" name="<?php echo $this->get_field_name('read_more'); ?>" type="text" value="<?php echo attribute_escape($read_more); ?>" />
               </label>
          </p>
		<p>
               <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Categories: (<code>SLUGs</code> separated by commas)',DOMAIN);?>
                    <input class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" type="text" value="<?php echo attribute_escape($category); ?>" />
               </label>
		</p>
		<?php
	}
}
/* End directory_featured_homepage_listing widget */
if(!function_exists('directory_content_limit')){
	function directory_content_limit($max_char, $more_link_text = '', $stripteaser = true, $more_file = '') {	
		global $post;	
		
		$content = get_the_content();
		$content = strip_tags($content);
		$content = substr($content, 0, $max_char);
		$content = substr($content, 0, strrpos($content, " "));
		$more_link_text='<a href="'.get_permalink().'">'.$more_link_text.'</a>';
		$content = $content." ".$more_link_text;
		echo $content;	
	}
}
/*
 * Class Name: directory_featured_category_list
 * Return: display all the category list on home page
 */
class directory_featured_category_list extends WP_Widget {
		function directory_featured_category_list() {
		//Constructor
			$widget_ops = array('classname' => 'widget all_category_list_widget', 'description' => __('Shows a list of all categories and their sub-categories. Works best in main content and subsidiary areas.',DOMAIN) );		
			$this->WP_Widget('directory_featured_category_list', __('T &rarr; All Categories List',DOMAIN), $widget_ops);
		}
		function widget($args, $instance) 
		{
		// prints the widget
			global $current_cityinfo;
			extract($args, EXTR_SKIP);
			$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
			$post_type = empty($instance['post_type']) ? 'listing' : apply_filters('widget_post_type', $instance['post_type']);
			$category_level = empty($instance['category_level']) ? '1' : apply_filters('widget_category_level', $instance['category_level']);
			$number_of_category = ($instance['number_of_category'] =='') ? '6' : apply_filters('widget_number_of_category', $instance['number_of_category']);
			
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
			$args5=array(
					'orderby'    => 'name',
					'taxonomy'   => $taxonomies[0],
					'order'      => 'ASC',
					'parent'     => '0',
					'show_count' => 0,
					'hide_empty' => 0,
					'pad_counts' => true,					
				);
			
			echo $before_widget;
			
			/* set wp_categories on transient */
			if ( false === ( $categories = get_transient( '_tevolution_query_catwidget'.$post_type) ) && get_option('tevolution_cache_disable')==1 ) {
				$categories=get_categories($args5);
				set_transient( '_tevolution_query_catwidget'.$post_type, $categories, 12 * HOUR_IN_SECONDS );
			}elseif(get_option('tevolution_cache_disable')==''){
				$categories=get_categories($args5);
			}
			
			if($title){echo '<h3 class="widget-title">'.$title.'</h3>'; } ?>
			<div class="category_list_wrap">
            <?php 
			if(!isset($categories['errors'])):
				foreach($categories as $category) 
				{	
					/* set child wp_categories on transient */
					
					$transient_name=(!empty($current_cityinfo))? $current_cityinfo['city_slug']: '';					
					if ( false === ( $featured_catlist_list = get_transient( '_tevolution_query_catwidget'.$category->term_id.$post_type.$transient_name) ) && get_option('tevolution_cache_disable')==1 ) {
						do_action('tevolution_category_query');						
						$featured_catlist_list =  wp_list_categories('title_li=&child_of=' . $category->term_id .'&echo=0&depth='.$category_level.'&number='.$number_of_category.'&taxonomy='.$taxonomies[0].'&show_count=1&hide_empty=0&pad_counts=0&show_option_none=');
						set_transient( '_tevolution_query_catwidget'.$category->term_id.$post_type.$transient_name, $featured_catlist_list, 12 * HOUR_IN_SECONDS );				
					}elseif(get_option('tevolution_cache_disable')==''){
						do_action('tevolution_category_query');
						$featured_catlist_list =  wp_list_categories('title_li=&child_of=' . $category->term_id .'&echo=0&depth='.$category_level.'&number='.$number_of_category.'&taxonomy='.$taxonomies[0].'&show_count=1&hide_empty=0&pad_counts=0&show_option_none=');
					}
					if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
					{
						remove_filter( 'terms_clauses','locationwise_change_category_query',10,3 );	
					}
					?>	
                        <div class="category_list">
							<h3><a href="<?php echo get_term_link($category->slug, $category->taxonomy);?>"><?php echo $category->name; ?></a></h3>                         
							<?php
								if( @$featured_catlist_list != "" ){
									if($number_of_category !=0){
							?>
										<ul>
											<?php echo $featured_catlist_list; ?>
											<li class="view">
												<a href="<?php echo get_term_link($category->slug, $category->taxonomy);?>">
													<?php _e('View all &raquo;',DOMAIN)?>
												</a> 
											</li>                                        
										</ul>
						<?php 	
									}
								}
						?>
                         </div>   
					<?php
				 }
			 else:
				echo '<p>'. __('Invalid Category.',DOMAIN).'</p>';
			 endif; ?>
             </div>
             <?php echo $after_widget;
		}
		function update($new_instance, $old_instance) {
			//save the widget	
			global $wpdb;
			$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name like '%s'",'%_tevolution_query_catwidget%' ));
			return $new_instance;
		}
		function form($instance) {
			//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'category_level' => '1','number_of_category' => '5') );		
			$title = strip_tags($instance['title']);
			$my_post_type = ($instance['post_type']) ? $instance['post_type'] : 'listing';
			$category_level = ($instance['category_level']);
			$number_of_category = ($instance['number_of_category']);
			?>
               <p>
                    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:',DOMAIN);?> 
                         <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
                    </label>
               </p>
				<p>
               	<label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post Type:',DOMAIN)?>
                    <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
                          <?php
						$all_post_types = get_option("templatic_custom_post");
						foreach($all_post_types as $key=>$post_type){?>
							<option value="<?php echo $key;?>" <?php if($key== $my_post_type)echo "selected";?>><?php echo esc_attr($post_type['label']);?></option>
					<?php }?>	
                    </select>
                    </label>
               </p> 
				<p>
                    <label for="<?php  echo $this->get_field_id('category_level'); ?>"><?php _e('Category Level',DOMAIN);?>: 
                         <select id="<?php  echo $this->get_field_id('category_level'); ?>" name="<?php echo $this->get_field_name('category_level'); ?>">
                         <?php
                         for($i=1;$i<=10;$i++)
                         {?>
                         	<option value="<?php echo $i;?>" <?php if(attribute_escape($category_level)==$i){?> selected="selected" <?php } ?>><?php echo $i;?></option>
                         <?php
                         }?>
                         </select>
                    </label>
               </p> 
			 <p>
               	<label for="<?php  echo $this->get_field_id('number_of_category'); ?>"><?php _e('Number of child categories',DOMAIN);?>: <input class="widefat" id="<?php  echo $this->get_field_id('number_of_category'); ?>" name="<?php echo $this->get_field_name('number_of_category'); ?>" type="text" value="<?php echo attribute_escape($number_of_category); ?>" />
                    </label>
               </p>     
		
		<?php
		}
}
/*
directory_mile_range_widget : Miles wise searching widget 
*/
class directory_mile_range_widget extends WP_Widget {
	function directory_mile_range_widget() {
		//Constructor
		$widget_ops = array('classname' => 'search_miles_range', 'description' => __('Search through nearby posts by setting a range. Use in category page sidebar areas.',DOMAIN) );
		$this->WP_Widget('directory_mile_range_widget', __('T &rarr; Search by Miles Range',DOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
		// prints the widget
		extract($args, EXTR_SKIP);
		$title = empty($instance['title']) ? 'Search Near By Miles Range' : apply_filters('widget_title', $instance['title']);
		$post_type = empty($instance['post_type']) ? 'listing' : apply_filters('widget_post_type', $instance['post_type']);
		$miles_search = empty($instance['miles_search']) ? '' : apply_filters('widget_miles_search', $instance['miles_search']);
		$max_range = empty($instance['max_range']) ? '' : apply_filters('widget_max_range', $instance['max_range']);
		echo $before_widget;
		$search_txt=sprintf(__('Find a %s',DOMAIN),$post_type);
		echo '<div class="search_nearby_widget">';
		if($title){echo '<h3 class="widget-title">'.$title.'</h3>';}
		global $wpdb,$wp_query;
		$current_term = $wp_query->get_queried_object();
		
		wp_enqueue_script('directory-search-script', TEVOLUTION_DIRECTORY_URL.'js/search_map_script.js',array( 'jquery' ),'',false);
		
		if(is_tax()){
			$list_id='loop_'.$post_type.'_taxonomy';
			$page_type='taxonomy';
		}else{
			$list_id='loop_'.$post_type.'_archive';
			$page_type='archive';
		}
		
		?>
		<form method="get" id="searchform" action="<?php echo home_url(); ?>/">
          	<input type="hidden" name="post_type" value="<?php echo $post_type;?>" />          	
               <?php
			wp_enqueue_script("jquery-ui-slider");			
			?>               
               <div class="search_range">
                  <label><?php _e('Rango en Millas:',DOMAIN); ?></label>
                  <input type="text" name="radius" id="radius_range" value="<?php echo $max_range; ?>" style="border:0; font-weight:bold;"  readonly="readonly"/>
              </div>              
              <div id="radius-range"></div>
              <script type="text/javascript">		    
				jQuery('#radius-range').bind('slidestop', function(event, ui) {				
				var miles_range=jQuery('#radius_range').val();
				var list_id='<?php echo $list_id?>';	
				jQuery('.'+list_id+'_process').remove();
				jQuery('#'+list_id ).before( "<p class='<?php echo $list_id.'_process';?>' style='text-align:center';><img src='<?php echo TEVOLUTION_DIRECTORY_URL.'images/process.gif';?>'  alt='Processing..'/></p>" );
				<?php
				if(isset($_SERVER['QUERY_STRING'])){
					$query_string='&'.$_SERVER['QUERY_STRING'];
				}
				?>
				jQuery.ajax({
					url:ajaxUrl,
					type:'POST',			
					data:'action=<?php echo $post_type."_search";?>&posttype=<?php echo $post_type;?>&miles_range='+miles_range+'&page_type=<?php echo $page_type.$query_string;?>',
					success:function(results){
						jQuery('.'+list_id+'_process').remove();
						jQuery('#'+list_id).html(results);
						jQuery('#listpagi').remove();
					}
				});
				
				jQuery.ajax({
					url:ajaxUrl,
					type:'POST',			
					data:'action=<?php echo $post_type."_search_map";?>&posttype=<?php echo $post_type;?>&miles_range='+miles_range+'&page_type=<?php echo $page_type.$query_string;?>',
					success:function(results){						
						miles_googlemap(results);
					}
				});	
			});
			jQuery(function(){jQuery("#radius-range").slider({range:true,min:1,max:<?php echo $max_range; ?>,values:[1,<?php echo $max_range; ?>],slide:function(e,t){jQuery("#radius_range").val(t.values[0]+" - "+t.values[1])}});jQuery("#radius_range").val(jQuery("#radius-range").slider("values",0)+" - "+jQuery("#radius-range").slider("values",1))})
		    </script>
            
          </form>		
		<?php
		echo '</div>';
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance) {
		//save the widget
		return $new_instance;
	}
	function form($instance) {
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Search Nearby Miles Range', 'max_range' => 500, 'post_type' => 'listing' ) );		
		$title = strip_tags(@$instance['title']);
		$post_type = strip_tags(@$instance['post_type']);
		$max_range = strip_tags(@$instance['max_range']);
		$miles_search=strip_tags(@$instance['miles_search']);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title',DOMAIN);?>:
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('max_range'); ?>"><?php _e('Max Range',DOMAIN);?>:
			<input class="widefat" id="<?php echo $this->get_field_id('max_range'); ?>" name="<?php echo $this->get_field_name('max_range'); ?>" type="text" value="<?php echo attribute_escape($max_range); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('post_type');?>" ><?php _e('Select Post Type:');?>     </label>	
			<select  id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widefat">        	
				<?php
                    $all_post_types = get_option("templatic_custom_post");
                    foreach($all_post_types as $key=>$post_types){
					?>
					<option value="<?php echo $key;?>" <?php if($key== $post_type)echo "selected";?>><?php echo esc_attr($post_types['label']);?></option>
					<?php
                    }
                    ?>	
			</select>
		</p>           
		<?php			
	}
}
/* End directory_mile_range_widget */
/*
	Name : slider_search_option	
	Desc : Add the JS Of sliding search(miles wise searching) in footer
*/
function slider_search_option(){	
	?><script type="text/javascript">	  
		jQuery(function(){jQuery("#radius-range").slider({range:true,min:1,max:500,values:[1,500],slide:function(e,t){jQuery("#radius_range").val(t.values[0]+" - "+t.values[1])}});jQuery("#radius_range").val(jQuery("#radius-range").slider("values",0)+" - "+jQuery("#radius-range").slider("values",1))})
	   </script>
     <?php	
}
/* 
   Class: directory_related_event_widget
   Desc: Show the meta box the events in edit listing page - to select the events of that places
 */
class directory_related_event_widget extends WP_Widget {
	function directory_related_event_widget() {
		//Constructor
		$widget_ops = array('classname' => 'directory_related_event', 'description' => 'Allows visitors to connect their Listings with Events while editing them in the front-end. Use the widget inside the Primary Sidebar area.');
		$this->WP_Widget('directory_related_event_widget', __('T &rarr; Add Related Event',DOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
		// prints the widget
		extract($args, EXTR_SKIP);
		$title = empty($instance['title']) ? 'Related Event' : apply_filters('widget_title', $instance['title']);
		$description = empty($instance['description']) ? '' : apply_filters('widget_description', $instance['description']);
		
		if((isset($_REQUEST['pid']) && $_REQUEST['pid']!= '') && (isset($_REQUEST['action']) && $_REQUEST['action']== 'edit') && get_post_type($_REQUEST['pid']) == 'listing')
		{
			echo $before_widget;
			$submit_post = get_post($_REQUEST['pid']); 
			global $wpdb,$post;			
			if(is_array($_POST['event_for_listing']))
			{
				$event_for_listing =  implode(',',$_POST['event_for_listing']);
				
				$event_listing = explode(",",get_post_meta($post->ID,'event_for_listing',true));
				
				update_post_meta($_REQUEST['pid'],'event_for_listing',$event_for_listing); // booked tickets
			
				echo "<div class='updated'>".__('Related event saved successfully.',DOMAIN)."</div>";
			
			}
			global $wpdb,$post;
			?>
			<form action="" name="related_event" id="related_event" method="post">
			<?php
			$post_id = $_REQUEST['pid'];
			
			if($title){echo '<h3 class="widget-title">'.$title.'</h3>';}			
			
			$args = array(				
				'order' => 'ASC',
				'orderby' => 'title',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'post_type' => 'event',
				'author'=> $submit_post->post_author,
			);			
			$get_posts = new WP_Query;
			$get_event = $get_posts->query( $args );
			if(get_post_meta($post_id,'event_for_listing',true)):
				$event_for_listing = explode(',',get_post_meta($post_id,'event_for_listing',true));
			else:
				$event_for_listing = '';
			endif;
			echo "<div>";
			
			if(!empty($get_event)){
			if(empty($event_for_listing)){ $default ='selected=selected'; }else{ $default=''; }
				echo "<select name='event_for_listing[]' id='event_for_listing' multiple='multiple' class='clearfix' style='padding:2px;  width:80%;'>";
					
				echo "<option value='0' $default>".__("Select an events",DOMAIN)."</option>";
				foreach($get_event as $event_d){
					setup_postdata($event_d);
					if(in_array($event_d->ID,$event_for_listing)){ $selected = 'selected=selected'; }else{ $selected='';}
					echo "<option value='".$event_d->ID."' $selected>".$event_d->post_title."</option>";	
				}
				echo "</select>";
			}else{
				_e('Currently no event created by the owner of this list/place.',DOMAIN);
			}
			echo "</div>";
			if($description)
			{
				echo '<p class="description">'.$description.'</p>';
			}
			?>
				<input type="submit" alt="" class="normal_button main_btn"  value="<?php _e('Save',DOMAIN);?>"> 
				</form>
			<?php
			echo $after_widget;
		}
	}
	
	function update($new_instance, $old_instance) {
		//save the widget
		return $new_instance;
	}
	function form($instance) {
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'title' => '',''=>'') );		
		$title = strip_tags($instance['title']);
		$description = ($instance['description']); ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title',DOMAIN);?>:
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description',DOMAIN);?>:
			<input class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" type="text" value="<?php echo attribute_escape($description); ?>" />
			</label>
		</p>
		<?php			
	}
}
/* End directory_related_event_widget widget */
?>