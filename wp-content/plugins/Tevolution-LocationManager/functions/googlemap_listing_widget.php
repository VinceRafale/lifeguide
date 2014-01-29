<?php
class widget_category_googlemap_widget extends WP_Widget {
	function widget_category_googlemap_widget() {	
		$widget_ops = array('classname' => 'widget Google Map in Listing page', 'description' => __('Show a map on category pages while operating multiple cities. Use in category page sidebar and category page - below header areas.',LM_DOMAIN) );		
		$this->WP_Widget('category_googlemap', __('T &rarr; Category Page Map - multi city',LM_DOMAIN), $widget_ops);
	}
	function widget($args, $instance) {		
      	$heigh = empty($instance['heigh']) ? 425 : apply_filters('widget_heigh', $instance['heigh']);
      	$clustering = empty($instance['clustering']) ? '' : apply_filters('widget_heigh', $instance['clustering']);
		global $current_cityinfo,$wp_query;
		
		$templatic_settings=get_option('templatic_settings');
		
		$taxonomy= get_query_var( 'taxonomy' );
		$slug=get_query_var( get_query_var( 'taxonomy' ) );
		$term=get_term_by( 'slug',$slug , $taxonomy ) ;
		
		$term_icon=$term->term_icon;	
		if($term_icon=='')
			$term_icon = TEMPL_PLUGIN_URL.'tmplconnector/monetize/images/pin.png';
		/*Get the directory listing page map settings */
		$templatic_settings=get_option('templatic_settings');	
		$googlemap_setting=get_option('city_googlemap_setting');
		$current_term = $wp_query->get_queried_object();
		
		if($templatic_settings['category_map']=='yes' && $googlemap_setting['category_googlemap_widget']!='yes' && get_post_type()!='' && !is_search()){			
			add_filter('posts_where', 'location_multicity_where');
			if(get_post_type() =='event' && is_plugin_active('Tevolution-Events/events.php')){
				add_filter('posts_where', 'event_manager_posts_where');
			}
			if(is_tax()){
				$args4 = array(
					'post_type' => get_post_type(),
					'post_status'  => 'publish',
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
				$args4 = array(
					'post_type' => get_post_type(),					
					'posts_per_page' => -1,
					'post_status'   => 'publish',
				);
			}
			
			$query = new WP_Query( $args4 );
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
				$marker_title = str_replace($srcharr,$replarr,$post->post_title);
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
					$timing=get_post_meta(get_the_ID(),'listing_timing',true);
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
				$review=($comment_count <=1 )? __('review',LM_DOMAIN):__('reviews',LM_DOMAIN);
				if($lat && $lng && !in_array($post_id,$pids))
				{ 
					if(!isset($more)){ $more =''; }
					$retstr ="{";
					$retstr .= "'name':'$marker_title',";
					$retstr .= "'location': [$lat,$lng],";
					$retstr .= "'message':'<div class=\"google-map-info $image_class forrent\"><div class=\"map-inner-wrapper\"><div class=\"map-item-info\">$post_image";
					$retstr .= "<h6><a href=\"$plink\" class=\"ptitle\" ><span>$title</span></a></h6>";
					
					if($address){$retstr .= "<p class=address >$address</p>";}				
					if($timing){$retstr .= "<p class=timing>$timing</p>";}
					if($contact){$retstr .= "<p class=contact >$contact</p>";}
					if($website){$retstr .= '<p class=website><a href= '.$website.'>'.$website.'</a></p>';}
					if($templatic_settings['templatin_rating']=='yes'){
						$rating=draw_rating_star_plugin(get_post_average_rating(get_the_ID()));
						$retstr .= '<div class=map_rating>'.str_replace('"','',$rating).' <span><a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
					}elseif(is_plugin_active('Templatic-MultiRating/multiple_rating.php') && function_exists('single_average_rating')){
						$rating=get_single_average_rating(get_the_ID());
						$retstr .= '<div class=map_rating>'.stripcslashes(str_replace('"','',$rating)).'<span> <a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
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
		
		
	$map_type =($current_cityinfo['map_type'] != '')? $current_cityinfo['map_type']: 'ROADMAP';		
	$latitude    = $current_cityinfo['lat'];
	$longitude   = $current_cityinfo['lng'];	
	$map_display = $current_cityinfo['is_zoom_home'];
	$zoom_level  = ($current_cityinfo['scall_factor'])? $current_cityinfo['scall_factor'] :'13';
	
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
		<?php if($map_display == 1) { ?>
		var multimarkerdata = new Array();
		<?php }?>
		var zoom_option = '<?php echo $map_display; ?>';
		var markers = {<?php echo $catinfo_arr;?>};
		var clustering = '<?php echo $clustering;?>';
		
		//var markers = '';
		var map = null;
		var mgr = null;
		var mc = null;
		var markerClusterer;
		var mClusterer = null;
		var markerArray = [];
		var showMarketManager = false;
		var PIN_POINT_ICON_HEIGHT = 32;
		var PIN_POINT_ICON_WIDTH = 20;				
		var infobox;
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
					   <?php if($map_display == 1) { ?>
						 multimarkerdata[i]  = new google.maps.LatLng(details.location[0], details.location[1]);
					   <?php } ?>
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
					if(clustering !=1)
						markerClusterer = new MarkerClusterer(map, markers[level],{
						maxZoom: 0,
						gridSize: 10,
						styles: null,
						infoOnClick: 1,
						infoOnClickZoom: 18,
						});
				 }
				  <?php if($map_display == 1) { ?>
					 var latlngbounds = new google.maps.LatLngBounds();
					for ( var j = 0; j < multimarkerdata.length; j++ )
						{
						 latlngbounds.extend( multimarkerdata[ j ] );
						}
					   map.fitBounds( latlngbounds );
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
	endif;	
	}
	/*Widget update function */
	function update($new_instance, $old_instance) {
		//save the widget
		return $new_instance;
	}
	/*Widget admin form display function */
	function form($instance) {
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'width' => '', 'heigh' => 500, 'clustering' => '') );		
		$width = strip_tags($instance['width']);
		$heigh = strip_tags($instance['heigh']);
		$clustering = strip_tags($instance['clustering']);
		?>
	
		<p>
		 <label for="<?php echo $this->get_field_id('heigh'); ?>"><?php _e('Map Height: <small>(Default is 500px. To change enter a numeric value.)</small>',LM_DOMAIN);?>
		 <input class="widefat" id="<?php echo $this->get_field_id('heigh'); ?>" name="<?php echo $this->get_field_name('heigh'); ?>" type="text" value="<?php echo esc_attr($heigh); ?>" />
		 </label>
	    </p>
		<p>
		<?php if($clustering) { $checked = "checked=checked"; }else{ $checked =''; } ?>
		 <label for="<?php echo $this->get_field_id('clustering'); ?>">
		 <input  id="<?php echo $this->get_field_id('clustering'); ?>" name="<?php echo $this->get_field_name('clustering'); ?>" type="checkbox" value="1" <?php echo $checked; ?>/>&nbsp;<?php _e('Disable Clustering',LM_DOMAIN);?></label>
	    </p>
	    <?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("widget_category_googlemap_widget");') );
?>