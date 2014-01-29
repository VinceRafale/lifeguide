<?php
/*
 * Function name: templ_add_admin_menu_
 * Return: display the admin submenu page of tevolution menu page
 */
 

include_once(plugin_dir_path( __FILE__ ).'direction_map_widget.php');

add_action('after_listing_page_setting','google_map_listing_map_setting');
function google_map_listing_map_setting(){
	$tmpdata = get_option('templatic_settings');	
	?>
     <tr>
          <th valign="top"><label><?php _e('Show all posts on category map',DOMAIN);?></label></th>
          <td>
               <label for="category_map"><input type="checkbox" id="category_map" name="category_map" value="yes" <?php if(@$tmpdata['category_map']=='yes') echo 'checked';?>/>&nbsp;<?php _e('Enable',DOMAIN);?></label>
               <p class="description"><?php _e('With large categories this can significantly increase category page load time. When this option is disabled, the map will only show posts from the current page.',DOMAIN);?></p>
          </td>
     </tr>    
     <?php
}


add_action('templ_add_admin_menu_', 'googlemap_setting_add_page_menu', 20);
function googlemap_setting_add_page_menu(){
	$menu_title2 = __('Map Settings', DOMAIN);
	global $location_settings_option;
	$location_settings_option=add_submenu_page('templatic_system_menu', $menu_title2, $menu_title2,'administrator', 'googlemap_settings', 'googlemap_settings');		
	if(!get_option('maps_setting')){
		$city_googlemap_setting=array('map_city_name'             => 'New york',
								'map_city_latitude'         => '40.70591499925218',
								'map_city_longitude'        => '-73.9780035',
								'map_city_type'             => 'ROADMAP',
								'map_city_scaling_factor'   => '12',
								'set_zooming_opt'           => '0',
								'category_googlemap_widget' => 'yes',
								'direction_map'             => 'yes',
								'google_map_full_width'     => 'yes',
								);
		
		update_option('city_googlemap_setting',$city_googlemap_setting);	
		update_option('maps_setting',1);
	}
}
function googlemap_settings(){
	
	echo '<div class="wrap">';
	echo '<div id="icon-options-general" class="icon32"><br></div>';
	echo "<h2>".__('Map Settings',DOMAIN)."</h2>";
	echo '<p class="tevolution_desc">'.__('Use this settings area to tweak the maps on your site. If you happen to have "Tevolution - Location Manager" disabled, use the "Single city map settings" area to define map properties for your single city.',DOMAIN).'</p>';
	if(isset($_POST['map_setting_submit'])){
		update_option('city_googlemap_setting',$_POST);
		
		echo '<div id="setting-error-settings_updated" class="updated settings-error">';
		echo '<p>';
		echo '<strong>'.__('Settings saved',DOMAIN).'</strong>';
		echo '</p>';
		echo '</div>';
	}
	
	$googlemap_setting=get_option('city_googlemap_setting');		
	?>
     <form name="" action="" method="post">
          <table class="form-table">
          	<tbody>               
               	<?php do_action('before_map_setting');?>
                    
               	<tr valign="top">
                    	<th colspan="2">
                         	<div class="tevo_sub_title"><?php _e('Single city map settings',DOMAIN);?></div>
                              <p class="description"><?php _e('',DOMAIN);?></p>
                         </th>
                    </tr>
               	<tr valign="top">
                    	<th scope="row"><label for="map_city_name"><?php _e('City name',DOMAIN);?></label></th>
                         <td><input id="map_city_name" type="text" name="map_city_name" value="<?php echo $googlemap_setting['map_city_name'];?>" /></td>
                    </tr>
                    <tr valign="top">
                    	<th scope="row"><label for="map_city_latitude"><?php _e('City latitude',DOMAIN);?></label></th>
                         <td><input id="map_city_latitude" type="text" name="map_city_latitude" value="<?php echo $googlemap_setting['map_city_latitude'];?>" /><p class="description"><?php _e('Enter the latitude for the city defined above. Generate the value using <a href="http://itouchmap.com/latlong.html" target="_blank">this website</a>.',DOMAIN)?></p></td>
                    </tr>
                    <tr valign="top">
                    	<th scope="row"><label for="map_city_longitude"><?php _e('City longitude',DOMAIN);?></label></th>
                         <td><input id="map_city_longitude" type="text" name="map_city_longitude" value="<?php echo $googlemap_setting['map_city_longitude'];?>" /><p class="description"><?php _e('Enter the longitude for the city defined above. Generate the value using <a href="http://itouchmap.com/latlong.html" target="_blank">this website</a>.',DOMAIN)?></p></td>
                    </tr>
                    <tr valign="top">
                    	<th scope="row"><label for="map_city_type"><?php _e('Map type',DOMAIN);?></label></th>
                         <td>
                              <fieldset> 
                                   <input type="radio" id="roadmap" name="map_city_type" value="ROADMAP" <?php if($googlemap_setting['map_city_type']=='ROADMAP'){echo 'checked';}?>  /> <label for="roadmap"> <?php _e('Road Map',DOMAIN);?></label>
                                   
                                   <input type="radio" id="terrain" name="map_city_type" value="TERRAIN" <?php if($googlemap_setting['map_city_type']=='TERRAIN'){echo 'checked';}?>/> <label for="terrain"><?php _e('Terrain Map',DOMAIN);?></label>
                                   
                                   <input type="radio" id="satellite" name="map_city_type" value="SATELLITE" <?php if($googlemap_setting['map_city_type']=='SATELLITE'){echo 'checked';}?>/> <label for="satellite"><?php _e('Satellite Map',DOMAIN);?></label>
                                   
                              </fieldset> <p class="description"><?php _e('The selection made here will affect your homepage and category page map.',DOMAIN)?></p> 
                                                   	
                        	</td>
                    </tr>
                    <tr valign="top">
                    	<th scope="row"><label for="map_city_scaling_factor"><?php _e('Map scaling factor',DOMAIN);?></label></th>
                         <td>
                         	<select id="map_city_scaling_factor" name="map_city_scaling_factor">
								<?php for($sf=1; $sf < 20 ; $sf++){ ?>
									<?php 
									$sf1=($googlemap_setting['map_city_scaling_factor'] !='')?$googlemap_setting['map_city_scaling_factor'] :'13';
									if($sf == $sf1){ $sel ="selected=selected"; }else{ $sel =''; }
									?>
									<option value="<?php echo $sf; ?>" <?php echo $sel; ?>><?php echo $sf; ?></option>
								<?php } ?>							
						</select> <p class="description"><?php _e('Set to zoom level for the map. The higher the number, the larger the zoom. To show a city area set the factor to around 13.',DOMAIN)?></p>                         	
                          </td>
                    </tr>
                    
                    <tr valign="top">
                    	<th scope="row"><label for="map_city_display"><?php _e('Map display',DOMAIN);?></label></th>
                         <td>
                         	<fieldset>
                                   <input type="radio" id="set_zooming_opt" name="set_zooming_opt" value="0"  <?php if($googlemap_setting['set_zooming_opt']=='0'){echo 'checked';}?>/> <label for="set_zooming_opt"> <?php _e('According to Map Scaling factor',DOMAIN);?></label>                 
                                   <input type="radio" id="set_zooming_opt1" name="set_zooming_opt"  value="1" <?php if($googlemap_setting['set_zooming_opt']=='1'){echo 'checked';}?> /> <label for="set_zooming_opt1"><?php _e('Fit all available listings',DOMAIN);?></label>
                              </fieldset> <p class="description"><?php _e('If "Fit all available listings" is selected the map scaling factor set above is ignored. The zoom factor will be set automatically so that all listings fit the screen.',DOMAIN)?></p>                        	
                         </td>
                    </tr>
                    <tr valign="top">
                    	<th colspan="2">
                         	<div class="tevo_sub_title"><?php _e('Other map settings',DOMAIN);?></div>
                              <p class="description"><?php _e('',DOMAIN);?></p>
                         </th>
                    </tr>
                    <tr>
                         <td valign="top"><label><?php _e('Show map view in category pages',DOMAIN);?></label></td>
                         <td>
                              <label for="category_googlemap_widget"><input type="checkbox" id="category_googlemap_widget" name="category_googlemap_widget" value="yes" <?php if($googlemap_setting['category_googlemap_widget']=='yes') echo 'checked';?>/>&nbsp;<?php _e('Enable',DOMAIN);?></label>
                              <p class="description"><?php _e('Disable this option only if you want to use a widget to display the map on category pages. Enabling the option will prevent map widgets from working.',DOMAIN);?></p>
                         </td>
                    </tr>
                    <tr>
                         <td valign="top"><label><?php _e('Show "Map" tab in detail pages',DOMAIN);?></label></td>
                         <td>
                              <label for="direction_map"><input type="checkbox" id="direction_map" name="direction_map" value="yes" <?php if($googlemap_setting['direction_map']=='yes') echo 'checked';?>/>&nbsp;<?php _e('Enable',DOMAIN);?></label><br/>                    		<p class="description"><?php _e('Disable this only if you want to use a widget to display a map on the detail page. This option prevents map widgets on detail pages from working.',DOMAIN);?></p>
                         </td>
                    </tr>
                    
                    <tr>
                         <td valign="top"><label><?php _e('Hide maps on mobile devices',DOMAIN);?></label></td>
                         <td>
                              <label for="google_map_hide"><input type="checkbox" id="google_map_hide" name="google_map_hide" value="yes" <?php if( @$googlemap_setting['google_map_hide']=='yes') echo 'checked';?>/>&nbsp;<?php _e('Enable',DOMAIN);?></label><br/>                    		<p class="description"><?php _e('With this option enabled, maps won&rsquo;t be shown on mobile phones and tablets.',DOMAIN);?></p>
                         </td>
                    </tr>
                    <?php if(current_theme_supports('map_fullwidth_support')) :?>		
                    <tr>
                         <td valign="top"><label><?php _e('Show map in full width',DOMAIN);?></label></td>
                         <td>
                              <label for="google_map_full_width"><input type="checkbox" id="google_map_full_width" name="google_map_full_width" value="yes" <?php if($googlemap_setting['google_map_full_width']=='yes') echo 'checked';?>/>&nbsp;<?php _e('Enable',DOMAIN);?></label><br/>                    		<p class="description"><?php _e('Stretches the homepage map across the full width of the screen. This setting will be applied to any widget inserted in the "Home Page Slider" area.',DOMAIN);?></p>
                         </td>
                    </tr>
                    <?php endif;?>
                    
                    <?php do_action('after_map_setting');?>
               </tbody>
          </table>
          <p class="submit">
			<input id="submit" class="button button-primary" type="submit" value="<?php _e('Save Changes',DOMAIN);?>" name="map_setting_submit">
          </p>
     </form>     
     <?php
	echo '</div>';
}


/*
 * Function Name: google_maps_widgets_init 
 * Return: homepage and listing page map widget register
 */

add_action('widgets_init','google_maps_widgets_init');
function google_maps_widgets_init()
{
	register_widget('widget_homepagemap');
	register_widget('widget_listingpagemap');
}

/*
 * Class Name: widget_homepagemap
 * Create Home map widget
 */
class widget_homepagemap extends WP_Widget {
	function widget_homepagemap() {	
		$widget_ops = array('classname' => 'widget homepagemap', 'description' => __('Use it while operating a single city. Edit the map location in Tevolution &raquo; map settings. Widget works best inside the Homepage Slider or Homepage - Main Content area.',DOMAIN) );		
		$this->WP_Widget('homepagemap', __('T &rarr; Homepage Map - single city',DOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
		global $wp_query;
		$height = empty($instance['height']) ? '425' : apply_filters('widget_height', $instance['height']);
		$post_type = empty($instance['post_type']) ? '' : apply_filters('widget_post_type', $instance['post_type']);
		$mapcategory_info =get_googlemap_categoryinfo($post_type);		
		
		$googlemap_setting=get_option('city_googlemap_setting');
		$map_type    = ($googlemap_setting['map_city_type'] != '')? $googlemap_setting['map_city_type']: 'ROADMAP';		
		$latitude    = $googlemap_setting['map_city_latitude'];
		$longitude   = $googlemap_setting['map_city_longitude'];	
		$map_display = ($googlemap_setting['set_zooming_opt']!='')? $googlemap_setting['set_zooming_opt']:'0';
		$zoom_level  = ($googlemap_setting['map_city_scaling_factor'])? $googlemap_setting['map_city_scaling_factor'] :'13';
		
		wp_print_scripts( 'google-maps-apiscript' );
		wp_print_scripts( 'google-clusterig-v3' );
		wp_print_scripts( 'google-clusterig' );
		wp_print_scripts( 'google-infobox-v3' );
		
		$google_map_customizer=get_option('google_map_customizer');// store google map customizer required formate.
		?>
          <script type='text/javascript' src="<?php echo plugin_dir_url( __FILE__ );?>google_map.js" ></script>
          <script type="text/javascript">
			var map_latitude= '<?php echo $latitude?>';
			var map_longitude= '<?php echo $longitude?>';
			var map_zomming_fact= <?php echo $zoom_level;?>;
			var zoom_option = '<?php echo $map_display; ?>';
			var markers = '';
			var markerArray = [];
			var map = null;
			var mgr = null;
			var mClusterer = null;
			var PIN_POINT_ICON_HEIGHT = 32;
			var PIN_POINT_ICON_WIDTH = 20;
			var infowindow = new google.maps.InfoWindow();
			var infoBubble;
			function initialize(){
				var myOptions = {
					scrollwheel: false,
					zoom: map_zomming_fact,
					center: new google.maps.LatLng(map_latitude, map_longitude),
					mapTypeId: google.maps.MapTypeId.<?php echo $map_type;?>
				}
				map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
				var styles = [<?php echo substr($google_map_customizer,0,-1);?>];			
				map.setOptions({styles: styles});
				// Initialize Fluster and give it a existing map		 
				mgr = new MarkerManager( map );
			}
			
			google.maps.event.addDomListener(window, 'load', initialize);
			google.maps.event.addDomListener(window, 'load', googlemap_initialize);
		</script>
          <div class="map_sidebar">
               <div class="top_banner_section_in clearfix">
               	<div class="TopLeft"><span id="triggermap"></span></div>
               	<div class="TopRight"></div>
                    <div class="iprelative">
                         <div id="map_canvas" style="width: 100%; height:<?php echo $height;?>px" class="map_canvas"></div>               
                         <div id="map_loading_div" style="width: 100%; height:<?php echo $height;?>px; display: none;""></div>                     
                         <div id="map_marker_nofound"><?php _e('<h3>No Records Found</h3><p>Sorry, no records were found. Please adjust your search criteria and try again.</p>',DOMAIN) ?></div>     
                    </div>             
               
                    <form id="ajaxform" name="slider_search" class="pe_advsearch_form" action="javascript:void(0);"  onsubmit="return(googlemap_ajaxSearch());">
                    	<div class="paf_search"><input  type="text" class="" id="search_string" name="search_string" value="" placeholder="<?php _e('Title or Keyword',DOMAIN);?>" onclick="this.placeholder=''" onmouseover="this.placeholder='<?php _e('Title or Keyword',DOMAIN);?>'"/></div>
					<?php 
			
					if($post_type):
									
					?>
                              <div class="paf_row map_post_type" id="toggle_postID" style="display:block;">
							<?php for($c=0;$c<count($post_type);$c++):
                                   if($post_type[$c])
                                   { $obj = get_post_type_object($post_type[$c]);
								 
										$name = $obj->labels->name; // to get taxonomy name 
										if (function_exists('icl_register_string')) {									
											icl_register_string(DOMAIN, $name,$name);
											$name = icl_t(DOMAIN, $name,$name);		
										}
										?>
                                        <div class="mw_cat_title">
                                             <label>
                                             	<input type="checkbox" onclick="googlemap_initialize(this,'');"  value="<?php echo str_replace("&",'&amp;',$post_type[$c]);?>"  <?php if(!empty($_POST['posttype']) && !in_array(str_replace("&",'&amp;',$post_type[$c]) ,$_POST['posttype'])):?> <?php else:?> checked="checked" <?php endif;?> id="<?php echo str_replace("&",'&amp;',$post_type[$c]).'custom_categories';?>" name="posttype[]"> <?php echo ucfirst($name);?>
                                             </label>
                                             	<span id='<?php echo $post_type[$c].'_toggle';?>' class="toggle_post_type toggleon" onclick="custom_post_type_taxonomy('<?php echo $post_type[$c].'_category';?>',this)"></span>
                                         </div>
                                         <div class="custom_categories <?php echo str_replace("&",'&amp;',$post_type[$c]).'custom_categories';?>" id="<?php echo $post_type[$c].'_category';?>">
                                             <?php foreach($mapcategory_info[$post_type[$c]] as $key => $value){ ?>
                                             	<label for="<?php echo $key;?>">
                                                  <input type="checkbox" onclick="googlemap_initialize(this,'<?php echo str_replace("&",'&amp;',$post_type[$c]);?>');"  value="<?php echo $value['term_id'];?>"  <?php if(!empty($_POST['categoryname']) && !in_array($key,$_POST['categoryname'])):?> <?php else:?> checked="checked" <?php endif;?> id="<?php echo $key;?>" name="categoryname[]"><img height="14" width="8" alt="" src="<?php echo $value['icon']?>"> <?php echo $value['name']?>
                                                  </label>
                                             
                                             <?php }?>
                                        </div>
                                   <?php }
                                   endfor;?>
                              </div>
                              <div id="toggle_post_type" class="paf_row toggleon" onclick="googlemap_toggle_post_type();"></div>
                         <?php endif;?>
                    </form>     
               
               </div>
          </div>
          
          <script>
		var maxMap = document.getElementById( 'triggermap' );		
		google.maps.event.addDomListener(maxMap, 'click', showFullscreen);
		function showFullscreen() {
			  // window.alert('DIV clicked');
				jQuery('#map_canvas').toggleClass('map-fullscreen');
				jQuery('.map_category').toggleClass('map_category_fullscreen');
				jQuery('.map_post_type').toggleClass('map_category_fullscreen');
				jQuery('#toggle_post_type').toggleClass('map_category_fullscreen');
				jQuery('#trigger').toggleClass('map_category_fullscreen');
				jQuery('body').toggleClass('body_fullscreen');
				jQuery('#loading_div').toggleClass('loading_div_fullscreen');
				jQuery('#advmap_nofound').toggleClass('nofound_fullscreen');
				jQuery('#triggermap').toggleClass('triggermap_fullscreen');
				
				jQuery('.TopLeft').toggleClass('TopLeft_fullscreen');		

					 //map.setCenter(darwin);
					 window.setTimeout(function() { 
					var center = map.getCenter(); 
					google.maps.event.trigger(map, 'resize'); 
					map.setCenter(center); 
			   		}, 100);			 }
		</script>
          <?php
		
	}
	
	/*Widget update function */
	function update($new_instance, $old_instance) {
		//save the widget		
		return $new_instance;
	}
	/*Widget admin form display function */
	function form($instance) {
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'width' => '', 'height' => '','post_type'=>'') );				
		$height = strip_tags($instance['height']);
		$post_type=$instance['post_type'];		
		?>
          <p>
               <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Map Height <small>(default height: 425px) to change, only enter a numeric value.)</small>',DOMAIN);?>:
               <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo attribute_escape($height); ?>" />
               </label>
          </p>
          <div class="googlemap_post_type clearfix">
               <span><label for="<?php echo $this->get_field_id('post_type');?>" ><?php _e('Select Post:',DOMAIN);?>     </label></span>
               <span>	               
               <?php
               $all_post_types = get_option("templatic_custom_post");
               foreach($all_post_types as $key=>$post_types){
                    ?>
                    <input id="widget_home_<?php echo $key;?>" type="checkbox" name="<?php echo $this->get_field_name('post_type'); ?>[]" value="<?php echo $key;?>" <?php if(in_array($key,$post_type)){echo 'checked';}?>  />&nbsp;&nbsp;<label for="widget_home_<?php echo $key;?>"><?php echo esc_attr(ucfirst($post_types['label']));?></label> <br />
                    <?php
               }
               ?>	
               </span>
          </div>
	    <?php
	}
}




/*
 * Class Name: widget_listingpagemap
 * Create listing map widget
 */
class widget_listingpagemap extends WP_Widget {
	function widget_listingpagemap() {	
		$widget_ops = array('classname' => 'widget listingpagemap', 'description' => __('Show a map on category pages while operating a single city. Use in category page sidebar and category page - below header areas.',DOMAIN) );		
		$this->WP_Widget('listingpagemap', __('T &rarr; Category Page Map - single city',DOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
		global $wp_query;
		$heigh = empty($instance['heigh']) ? '425' : apply_filters('widget_heigh', $instance['heigh']);
		
		$templatic_settings=get_option('templatic_settings');
		$googlemap_setting=get_option('city_googlemap_setting');
		$taxonomy= get_query_var( 'taxonomy' );
		$slug=get_query_var( get_query_var( 'taxonomy' ) );
		$term=get_term_by( 'slug',$slug , $taxonomy ) ;
		
		$term_icon=$term->term_icon;	
		if($term_icon=='')
			$term_icon = TEMPL_PLUGIN_URL.'tmplconnector/monetize/images/pin.png';
		/*Get the directory listing page map settings */		
		$current_term = $wp_query->get_queried_object();
		if($templatic_settings['category_map']=='yes' && $googlemap_setting['category_googlemap_widget']!='yes' && get_post_type()!='' && !is_search()){
			if(is_tax()){
				$args = array(
					'post_type' => get_post_type(),
					'tax_query' => array(
						array(
							'taxonomy' => $taxonomy,
							'field' => 'slug',
							'terms' => $term
						)
					),
					'posts_per_page' => -1
				);
			}else{
				$args = array(
					'post_type' => get_post_type(),					
					'posts_per_page' => -1
				);
			}
			$query = get_transient( '_tevolution_query_googlemap_single_'.trim(get_post_type()).'_'.trim($term->slug) );
			if ( false === $query && get_option('tevolution_cache_disable')==1) {
				$query = new WP_Query( $args );
				set_transient( '_tevolution_query_googlemap_single_'.trim(get_post_type()).'_'.trim($term->slug), $query, 12 * HOUR_IN_SECONDS );
			}elseif(get_option('tevolution_cache_disable')==''){
				$query = new WP_Query( $args );
			}
		}else{
			$query = $wp_query;
		}
		
		$pids=array("");
		$cat_name = single_cat_title('',false);
		$srcharr = array("'");
		$replarr = array("\'");	
		if ($query->have_posts() && $googlemap_setting['category_googlemap_widget']!='yes') :
			while ($query->have_posts()) : $query->the_post(); 
				global $post;
				$ID = get_the_ID();
				$post_id=get_the_ID();
				if(get_post_meta($post_id,'_event_id',true)){
					$post_id=get_post_meta($post_id,'_event_id',true);
				}
				$title = get_the_title(get_the_ID());
				$marker_title = str_replace("'","\'",$post->post_title);
				$plink = get_permalink(get_the_ID());
				$lat = get_post_meta(get_the_ID(),'geo_latitude',true);
				$lng = get_post_meta(get_the_ID(),'geo_longitude',true);					
				$address = get_post_meta(get_the_ID(),'address',true);
				$address = str_replace($srcharr,$replarr,$address);
				if(is_search()){
					$taxonomies = get_object_taxonomies( (object) array( 'post_type' => get_post_type(),'public'   => true, '_builtin' => true ));				
					$post_categories = get_the_terms( get_the_ID() ,$taxonomies[0]);
					foreach($post_categories as $post_category)
					if($post_category->term_icon){
						$term_icon=$post_category->term_icon;
					}
				}
				if(get_post_type()=='listing'){
					$timing=get_post_meta(get_the_ID(),'place_timing',true);
					$contact=get_post_meta(get_the_ID(),'phone',true);
				}
				if(get_post_type()=='event'){
					$st_time=get_post_meta(get_the_ID(),'st_time',true);
					$end_time=get_post_meta(get_the_ID(),'end_time',true);
					$timing=$st_time.' To '.$end_time;
					$contact=get_post_meta(get_the_ID(),'phone',true);
				}
				if ( has_post_thumbnail()){
					$post_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail');						
					$post_images=$post_img[0];
				}else{
					$post_img = bdw_get_images_plugin($post_id,'thumbnail');					
					$post_images = $post_img[0]['file'];
				}
				if($post_images)
					$post_image='<div class=map-item-img><img width="150" height="150" class="map_image" src="'.$post_images.'" /></div>';
				else
					$post_image='';
				
				$image_class=($post_image)?'map-image' :'';
				$comment_count= count(get_comments(array('post_id' => $ID)));
				$review=($comment_count <=1 )? __('review',DOMAIN):__('reviews',DOMAIN);
				if($lat && $lng && !in_array($post_id,$pids))
				{ 
					$retstr ="{";
					$retstr .= "'name':'$marker_title',";
					$retstr .= "'location': [$lat,$lng],";
					$retstr .= "'message':'<div class=\"google-map-info $image_class\"><div class=\"map-inner-wrapper\"><div class=\"map-item-info\">$post_image";
					$retstr .= "<h6><a href=\"$plink\" class=\"ptitle\" ><span>$title</span></a></h6>";					
					if($address){$retstr .= "<p class=address>$address</p>";}				
					if($timing){$retstr .= "<p class=pcontact >$timing</p>";}
					if($contact){$retstr .= "<p class=pcontact>$contact</p>";}
					if($templatic_settings['templatin_rating']=='yes'){
						$rating=draw_rating_star_plugin(get_post_average_rating(get_the_ID()));
						$retstr .= '<div class=map_rating>'.str_replace('"','',$rating).' <span><a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
					}elseif(is_plugin_active('Templatic-MultiRating/multiple_rating.php') && function_exists('single_average_rating')){
						$rating=get_single_average_rating(get_the_ID());
						$retstr .= '<div class=map_rating>'.stripcslashes(str_replace('"','',$rating)).'<span><a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
					}
					$retstr .= "</div></div></div>";
					$retstr .= "',";
					$retstr .= "'icons':'$term_icon',";
					$retstr .= "'pid':'$ID'";
					$retstr .= "}";						
					$content_data[] = $retstr;
				}		
				$pids[]=$post_id;
			endwhile;
			if($content_data)	
				$catinfo_arr= "'$term->name':[".implode(',',$content_data)."]";	
			wp_reset_query();
			
			$googlemap_setting=get_option('city_googlemap_setting');
			
			$map_type    = ($googlemap_setting['map_city_type'] != '')? $googlemap_setting['map_city_type']: 'ROADMAP';		
			$latitude    = $googlemap_setting['map_city_latitude'];
			$longitude   = $googlemap_setting['map_city_longitude'];	
			$map_display = ($googlemap_setting['set_zooming_opt']!='')? $googlemap_setting['set_zooming_opt']:'0';
			$zoom_level  = ($googlemap_setting['map_city_scaling_factor'])? $googlemap_setting['map_city_scaling_factor'] :'13';
			
			
			wp_print_scripts( 'google-maps-apiscript' );
			wp_print_scripts( 'google-clusterig' );
			wp_print_scripts( 'google-clusterig-v3' );
			wp_print_scripts( 'google-infobox-v3' );
			
			$google_map_customizer=get_option('google_map_customizer');// store google map customizer required formate.
			?>
               <script type="text/javascript">
			var CITY_MAP_CENTER_LAT= '<?php echo $latitude?>';
			var CITY_MAP_CENTER_LNG= '<?php echo $longitude?>';
			var CITY_MAP_ZOOMING_FACT= <?php echo $zoom_level;?>;
			var infowindow;			
			var zoom_option = '<?php echo $map_display; ?>';
			var markers = {<?php echo $catinfo_arr;?>};			
			
			var map = null;
			var mgr = null;	
			var markerArray = [];
			var markerClusterer;	
			var mClusterer = null;
			var PIN_POINT_ICON_HEIGHT = 32;
			var PIN_POINT_ICON_WIDTH = 20;				
			var infoBubble;
			function initialize() {
				  bounds = new google.maps.LatLngBounds(); 
				  var myOptions = {
					scrollwheel: false,  
					zoom: CITY_MAP_ZOOMING_FACT,
					center: new google.maps.LatLng(CITY_MAP_CENTER_LAT, CITY_MAP_CENTER_LNG),
					mapTypeId: google.maps.MapTypeId.<?php echo $map_type;?>
				  }
				   map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
				   var styles = [<?php echo substr($google_map_customizer,0,-1);?>];			
		  		   map.setOptions({styles: styles});
				   mgr = new MarkerManager( map );
				   google.maps.event.addListener(mgr, 'loaded', function() {
				 
					  if (markers) {				  
						 for (var level in markers) {					 	
							for (var i = 0; i < markers[level].length; i++) {						
							   var details = markers[level][i];					  
							   var image = new google.maps.MarkerImage(details.icons);
							   var myLatLng = new google.maps.LatLng(details.location[0], details.location[1]);							   
							   markers[level][i] = new google.maps.Marker({
														  title: details.name,
														  content: details.message,
														  position: myLatLng,
														  icon: image,
														  clickable: true,
														  draggable: false,
														  flat: true
													   });					   
							   
							 markerArray[i] = markers[level][i];
							 infoBubble = new InfoBubble({
								maxWidth: 210,
								minWidth: 210,
								minHeight: 'auto',
								padding: 0,
								content:details.message,
								borderRadius:0, // 4,
								borderWidth: 3,
								borderColor: '#939393',
								overflow: 'visible',
							  });
							attachMessage(markers[level][i], details.message);
							bounds.extend(myLatLng);
							//alert(details.pid);
							   var pinpointElement = document.getElementById( 'pinpoint_'+details.pid );
							   if ( pinpointElement ) { 
							   <?php if($templatic_settings['pippoint_effects'] == 'hover') { ?>
									google.maps.event.addDomListener( pinpointElement, 'mouseover', (function( theMarker ) {								
									 return function() {
										google.maps.event.trigger( theMarker, 'click' );
									 };
								  })(markers[level][i]) );
								  <?php }else{ ?>
								   google.maps.event.addDomListener( pinpointElement, 'click', (function( theMarker ) {
									 return function() {
										google.maps.event.trigger( theMarker, 'click' );
									 };
								  })(markers[level][i]) );
								  
								  <?php } ?>
							   }
								   
							}
							mgr.addMarkers( markers[level], 0 );
							markerClusterer = new MarkerClusterer(map, markers[level],{
										maxZoom: 0,
										gridSize: 10,
										styles: null,
										infoOnClick: 1,
										infoOnClickZoom: 18,
										});
							
						 }
						  <?php if($map_display == 1) { ?>
							  map.fitBounds(bounds);
							  var center = bounds.getCenter();	
							  map.setCenter(center);
						  <?php } ?>
						 mgr.refresh();
					  }
					 
				   });
				   
						
					// but that message is not within the marker's instance data 
					function attachMessage(marker, msg) {
					  var myEventListener = google.maps.event.addListener(marker, 'click', function() {
							infoBubble.setContent( msg );	
							infoBubble.open(map, marker);
					  });
					}
					
				}
				
				
				google.maps.event.addDomListener(window, 'load', initialize);
			</script>
               <div id="listing_google_map" class="listing_google_map" >
                    <div class="map_sidebar">
                    <div class="top_banner_section_in clearfix">
                         <div class="TopLeft"><span id="triggermap"></span></div>
                         <div class="TopRight"></div>
                         <div id="map_canvas" style="width: 100%; height:<?php echo $heigh;?>px" class="map_canvas"></div>
                    </div>
                    </div>
               </div>
               <script>
			var maxMap = document.getElementById( 'triggermap' );		
			google.maps.event.addDomListener(maxMap, 'click', showFullscreen);
			function showFullscreen() {
				  // window.alert('DIV clicked');
					jQuery('#map_canvas').toggleClass('map-fullscreen');
					jQuery('.map_category').toggleClass('map_category_fullscreen');
					jQuery('.map_post_type').toggleClass('map_category_fullscreen');
					jQuery('#trigger').toggleClass('map_category_fullscreen');
					jQuery('body').toggleClass('body_fullscreen');
					jQuery('#loading_div').toggleClass('loading_div_fullscreen');
					jQuery('#advmap_nofound').toggleClass('nofound_fullscreen');
					jQuery('#triggermap').toggleClass('triggermap_fullscreen');
					jQuery('.TopLeft').toggleClass('TopLeft_fullscreen');		
		
						 //map.setCenter(darwin);
						 window.setTimeout(function() { 
						var center = map.getCenter(); 
						google.maps.event.trigger(map, 'resize'); 
						map.setCenter(center); 
						}, 100);			 }
			</script>     
               <?php
		endif;// Finish have_posts if condition
		
		
	}// Finish The widget function
	
	/*Widget update function */
	function update($new_instance, $old_instance) {
		//save the widget
		return $new_instance;
	}
	/*Widget admin form display function */
	function form($instance) {
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'width' => '', 'height' => '') );		
		$width = strip_tags($instance['width']);
		$height = strip_tags($instance['height']);
		?>
          <p>
               <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Map Height <small>(default height: 425px) to change, only enter a numeric value.)</small>',DOMAIN);?>:
               <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo attribute_escape($height); ?>" />
               </label>
          </p>
	    <?php
	}
}


/*
 * Function Name: get_googlemap_categoryinfo
 * Return: fetch the custom post type category
 *
 */

function get_googlemap_categoryinfo($post_type){
	
	for($i=0;$i<count($post_type);$i++){		
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type[$i],'public'   => true, '_builtin' => true ));
		$cat_args = array(
					'taxonomy'     =>$taxonomies[0],
					'orderby'      => 'name', 				
					'hierarchical' => 'true',
					'title_li'     =>''
				);	
		$r = wp_parse_args( $cat_args);	
		$catname_arr=get_categories( $r );	
		$categoriesinfo='';
		foreach($catname_arr as $cat)	{				
			if($cat->term_icon)
				$term_icon=$cat->term_icon;
			else
				$term_icon=TEVOLUTION_DIRECTORY_URL.'images/pin.png';
			
			$categoriesinfo[]=array('term_id'=>$cat->term_id, 'slug'=>$cat->slug,'name'=>$cat->name,'icon'=>$term_icon);	
		}
		if(!empty($categoriesinfo)){
			$catinfo_arr[$post_type[$i]]=$categoriesinfo;
		}
	}		
	return $catinfo_arr;	
}


add_action('wp_ajax_nopriv_google_map_initialize','google_map_initialize');
add_action('wp_ajax_google_map_initialize','google_map_initialize');
function google_map_initialize(){

	global $wpdb;
	$j=0;
	$pids=array("");
	$srcharr = array('"');
	$replarr = array('\"');
	$title_srcharr = array('"');
	$title_replarr = array('\"');
	$post_type =(explode(',',substr($_REQUEST['posttype'],0,-1)));
	$categoryname =(explode(',',substr($_REQUEST['categoryname'],0,-1)));
	$templatic_settings=get_option('templatic_settings');
	for($i=0;$i<count($post_type);$i++){
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type[$i],'public'   => true, '_builtin' => true ));	
		$cat_args = array(
					'taxonomy'=>$taxonomies[0],
					'orderby' => 'name', 				
					'hierarchical' => 'true',
					'title_li'=>''
				);
		$r = wp_parse_args( $cat_args);	
		$catname_arr=get_categories( $r );	
		foreach($catname_arr as $cat)	{
			$catname=$cat->slug;
			if(!in_array($cat->term_id,$categoryname))
				continue;
				
			$cat_ID=$cat->term_id;		
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type[$i],'public'   => true, '_builtin' => true ));
				
			$args=array( 
					   'post_type'      => trim($post_type[$i]),
					   'posts_per_page' => 200,
					   'post_status'    => 'publish',     
					   'tax_query'      => array(                
										  array(
											 'taxonomy' =>$taxonomies[0],
											 'field'    => 'id',
											 'terms'    => explode(',',$cat_ID),
											 'operator' => 'IN'
										  )            
									   ), 					  
					 'orderby' => 'RAND',
					   );
			
			add_filter( 'posts_where', 'googlesearch_posts_where', 10, 2 );
			$post_details= new WP_Query($args);
			remove_filter( 'posts_where', 'googlesearch_posts_where', 10, 2 );
			$content_data='';					
			if ($post_details->have_posts()) :				
				while ( $post_details->have_posts() ) : $post_details->the_post();
						global $post;
						$ID =get_the_ID();				
						$title = get_the_title($ID);
						$plink = get_permalink($ID);
						$lat = get_post_meta($ID,'geo_latitude',true);
						$lng = get_post_meta($ID,'geo_longitude',true);					
						$address = stripcslashes(str_replace($srcharr,$replarr,(get_post_meta($ID,'address',true))));
						$contact = str_replace($srcharr,$replarr,(get_post_meta($ID,'phone',true)));
						$website = str_replace($srcharr,$replarr,(get_post_meta($ID,'website',true)));			
						/*Fetch the image for display in map */
						if ( has_post_thumbnail()){
							$post_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail');						
							$post_images=$post_img[0];
						}else{
							$post_img = bdw_get_images_plugin($ID,'thumbnail');					
							$post_images = $post_img[0]['file'];
						}
						
						
						if($post_images)
							$post_image='<div class=map-item-img><img src='.$post_images.' width=150 height=150/></div>';
						else
							$post_image='';
						
						if($cat->term_icon)
							$term_icon=$cat->term_icon;
						else
							$term_icon=TEVOLUTION_DIRECTORY_URL.'images/pin.png';
						
						$image_class=($post_image)?'map-image' :'';
						$comment_count= count(get_comments(array('post_id' => $ID)));
						$review=($comment_count <=1 )? __('review',DOMAIN):__('reviews',DOMAIN);
						if(($lat && $lng )&& !in_array($ID,$pids))
						{ 	
							$retstr ='{';
							$retstr .= '"name":"'.str_replace($title_srcharr,$title_replarr,$post->post_title).'",';
							$retstr .= '"location": ['.$lat.','.$lng.'],';
							$retstr .= '"message":"<div class=\"google-map-info '.$image_class.'\"><div class=map-inner-wrapper><div class=map-item-info>'.$post_image;
							$retstr .= '<h6><a href='.$plink.' class=ptitle ><span>'.$title.'</span></a></h6>';
							
							if($address){$retstr .= '<p class=address>'.$address.'</p>';}
							if($contact){$retstr .= '<p class=website>'.$contact.'</p>';}
							if($website){$retstr .= '<p class=website><a href= '.$website.'>'.$website.'</a></p>';}
							if($templatic_settings['templatin_rating']=='yes'){
								$rating=draw_rating_star_plugin(get_post_average_rating(get_the_ID()));
								$retstr .= '<div class=map_rating>'.str_replace('"','',$rating).' <span><a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
							}elseif(is_plugin_active('Templatic-MultiRating/multiple_rating.php') && function_exists('single_average_rating')){
								$rating=get_single_average_rating(get_the_ID());
								$retstr .= '<div class=map_rating>'.stripcslashes(str_replace('"','',$rating)).'<span><a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
							}
							$retstr .= '</div></div></div>';
							$retstr .= '",';
							$retstr .= '"icons":"'.$term_icon.'",';
							$retstr .= '"pid":"'.$ID.'"';
							$retstr .= '}';
							$content_data[] = $retstr;
							$j++;
						}	
						
						$pids[]=$ID;
						
				endwhile;	
				wp_reset_query();
				wp_reset_postdata();
			endif;
			
			if($content_data)	
				$cat_content_info[]= implode(',',$content_data);
				
		}	
	}
	
	//
	if($cat_content_info)
	{
		echo '[{"totalcount":"'.$j.'",'.substr(implode(',',$cat_content_info),1).']';
	}else
	{
		echo '[{"totalcount":"0"}]';
	}
	
	exit;
}



/*
 * Function name: google_search_posts_where
 * Return : pass the search post title
 */
function googlesearch_posts_where( $where, &$wp_query){
	global $wpdb;
	if(isset($_REQUEST['search_string']) && $_REQUEST['search_string']!=''){
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $_REQUEST['search_string']) ) . '%\'';
	}	
	return $where;	
}


add_action('wp_head','google_maps_responsive');
function google_maps_responsive(){
	$city_googlemap_setting = get_option('city_googlemap_setting'); 		
	if(strtolower( @$city_googlemap_setting['google_map_hide']) == strtolower('yes')){ ?>
		<style type='text/css'>
			@media only screen and (max-width: 719px){
				.map_sidebar{ display:none; }
			}
		</style>
	<?php }	
}
?>