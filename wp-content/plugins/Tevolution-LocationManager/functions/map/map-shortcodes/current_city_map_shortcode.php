<?php
add_action('init','directory_map_shortcode_');
/* 
Function Name : directory_map_shortcode
Description : function to add shortcode start */
function directory_map_shortcode_(){
	add_shortcode( 'CURRENTCITY-DIRECTORYMAP', 'yourcity_directory_map_' );
}
/* end */
/*
Function Name : tcity_directory_map
args : atts - to pass attributes
Description : Add Map on page using shortcode
*/
function yourcity_directory_map_( $atts ) {     	
	 global $wpdb,$country_table,$zones_table,$multicity_table,$current_cityinfo,$templatic_settings,$post,$wp_query,$short_code_city_id;
	 $atts = shortcode_atts(
		array(
			'post_type' 	 => false,
			'width' 	      => '100%',
			'height' 	      => '400px',
			'map_type' 	 => 'map_type',
			'showmap' 	 => 1,
			'listing' 	 => 1,
			'showfullmap'   => 1,
			'showclustering'=>1
		),
		$atts
	);
	ob_start();	 
	wp_print_scripts( 'google-maps-apiscript' );
	wp_print_scripts( 'google-clusterig-v3' );
	wp_print_scripts( 'google-clusterig' );
	wp_print_scripts( 'google-map-js' );
	wp_print_scripts( 'google-infobox-v3' );
	$home_url2 = get_permalink($post->ID);	
	
	$lat=$_COOKIE['c_latitude'];
	$long=$_COOKIE['c_longitude'];
	$sql="SELECT distinct city_id, cityname,city_slug FROM  $multicity_table, {$wpdb->prefix}postmeta WHERE meta_key='post_city_id' AND meta_value=city_id and  truncate((degrees(acos( sin(radians(lat)) * sin( radians('".$lat."')) + cos(radians(lat)) * cos( radians('".$lat."')) * cos( radians(lng - '".$long."') ) ) ) * 69.09),1) ORDER BY truncate((degrees(acos( sin(radians(lat)) * sin( radians('".$lat."')) + cos(radians(lat)) * cos( radians('".$lat."')) * cos( radians(lng - '".$long."') ) ) ) * 69.09),1) ASC LIMIT 0,1";
	$nearest_result=$wpdb->get_results($sql);
	$city_id =$nearest_result[0]->city_id;	
	/* get city information BOF */
	$country_table = $wpdb->prefix."countries";
	$zones_table =$wpdb->prefix . "zones";	
	$multicity_table = $wpdb->prefix . "multicity";	
	if($wpdb->get_var("SHOW TABLES LIKE '$multicity_table'") == $multicity_table) {
		$cityinfo = $wpdb->get_results("SELECT mc.*,mc.message as msg,c.country_name,c.message,c.country_flg,z.zone_name FROM $multicity_table mc,$zones_table z,$country_table c where c.country_id=mc.country_id AND z.zones_id=mc.zones_id AND  mc.city_id =".$city_id." order by cityname   ASC");	
	}
	$post_info=(strstr($atts['post_type'],','))? explode(',',$atts['post_type']):array($atts['post_type']) ;	
	$short_code_city_id=$cityinfo[0]->city_id;
	$current_cityinfo=array('city_id'      =>$cityinfo[0]->city_id,
					    'country_id'   =>$cityinfo[0]->country_id,
					    'zones_id'     =>$cityinfo[0]->zones_id,
					    'cityname'     =>$cityinfo[0]->cityname,
					    'city_slug'    =>$cityinfo[0]->city_slug,
					    'city_code'    =>$cityinfo[0]->city_code,
					    'lat'          =>$cityinfo[0]->lat,
					    'lng'          =>$cityinfo[0]->lng,
					    'scall_factor' =>$cityinfo[0]->scall_factor,
					    'is_zoom_home' =>$cityinfo[0]->is_zoom_home,
					    'map_type'     =>$cityinfo[0]->map_type,
					    'post_type'    =>$cityinfo[0]->post_type,
					    'color'        =>$cityinfo[0]->color,
					    'message'      =>$cityinfo[0]->msg,
					    'color'        =>$cityinfo[0]->color,
					    'images'       =>$cityinfo[0]->images,
					    'country_name' =>$cityinfo[0]->country_name,
					    'country_flg'  =>$cityinfo[0]->country_flg,
					    'zone_name'    =>$cityinfo[0]->zone_name,
					    );
	
	/* get city information EOF */
	$latitude        = $current_cityinfo['lat'];
	$longitude       = $current_cityinfo['lng'];
	$map_type        = 'sepia';
	$showlisting     = $atts['listing'];
	$showmap         = $atts['showmap'];
	$slider          = $atts['slider'];
	$map_display     = $current_cityinfo['is_zoom_home'];
	$zoom_level      = $current_cityinfo['scall_factor'];	
	$map_clustering  = $atts['showclustering'];	
	$heigh = $atts['height'];
	$mapcategory_info =googlemap_city_initialization($post_info,$current_cityinfo['city_id']); 	
		if($templatic_settings['pippoint_effects'] =='click'){ $class="wmap_static"; }else{ $class="wmap_scroll"; }
		
	$google_map_customizer=get_option('google_map_customizer');// store google map customizer required formate.
	?>  
    <div class="city_map_frame <?php echo $class; ?>">    
        <script type="text/javascript">
		var map_latitude= '<?php echo $latitude?>';
		var map_longitude= '<?php echo $longitude?>';
		var map_zomming_fact= <?php echo $zoom_level;?>;
		var infowindow;
		<?php if($map_display == 1) { ?>
		var multimarkerdata = new Array();
		<?php }?>
		var zoom_option = '<?php echo $map_display; ?>';
		var markers ='';
		var input = jQuery.parseJSON('<?php echo $mapcategory_info; ?>');
		var markerArray = [];	
		var pipointeffect = '<?php echo $templatic_settings['pippoint_effects']; ?>';
		var map = null;
		var mgr = null;		
		var mc = null;		
		var mClusterer = null;
		var showMarketManager = false;
		var PIN_POINT_ICON_HEIGHT = 32;
		var PIN_POINT_ICON_WIDTH = 20;
		var infowindow = new google.maps.InfoWindow();
		var infoBubble;
		function initialize(){
			bounds = new google.maps.LatLngBounds(); 		
			var myOptions = {
				zoom: map_zomming_fact,
				center: new google.maps.LatLng(map_latitude, map_longitude),
				mapTypeId: google.maps.MapTypeId.<?php echo $map_type;?>
			}
			map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
			var styles = [<?php echo substr($google_map_customizer,0,-1);?>];			
		     map.setOptions({styles: styles});
			// Initialize Fluster and give it a existing map		 
			mgr = new MarkerManager( map );	
		
			google.maps.event.addListener(mgr, 'loaded', function() {
				markers=input;
				var totalcount = input[0].totalcount;
				if(mClusterer != null)
				{
					mClusterer.clearMarkers();
				}
				mClusterer = null;	
				if(totalcount > 0){		
					for (var i = 0; i < input.length; i++) {							 
						var details = input[i];	
						//alert(details+"=="+details.location[0]+"==="+details.location[1]);
						var image = new google.maps.MarkerImage(details.icons,new google.maps.Size(PIN_POINT_ICON_WIDTH, PIN_POINT_ICON_HEIGHT));
						var coord = new google.maps.LatLng(details.location[0], details.location[1]);			
						markers[i]  = new google.maps.Marker({
										position: coord,
										title: details.name,
										content:details.message,
										visible: true,
										clickable: true,
										map: map,
										icon: details.icons
									});
						markers[i]['infowindow'] = new google.maps.InfoWindow({
							content: details.message
						});
						bounds.extend(coord);
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
						attachMessage(markers[i], details.message);
						
						
				if(pipointeffect =='hover'){
					
						 var pinpointElement = document.getElementById( 'pinpoint_'+details.pid );
									   if ( pinpointElement ) { 
									
										  google.maps.event.addDomListener( pinpointElement, 'mouseover', (function( theMarker ) {
											 return function() { 
												google.maps.event.trigger( theMarker, 'click' );
											 };
										  })(markers[i]) );
										 
									   }
				}else{
					 var pinpointElement = document.getElementById( 'pinpoint_'+details.pid );
								   if ( pinpointElement ) { 
								
									  google.maps.event.addDomListener( pinpointElement, 'click', (function( theMarker ) {
										 return function() { 
											google.maps.event.trigger( theMarker, 'click' );
										 };
									  })(markers[i]) );
									 
								   }
				
				}		
			
						mgr.addMarkers( markers[i], 0 );					
					}	
					if(zoom_option==1){
							map.fitBounds(bounds);
							var center = bounds.getCenter();
							map.setCenter(center);
					}					
					mClusterer = new MarkerClusterer(map, markers,{
						maxZoom: 0,
						gridSize: 10,
						styles: null,
						infoOnClick: 1,
						infoOnClickZoom: 18,
						});
					
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
		<?php if($showmap ==1 ){ ?>
          <div class="map_sidebar">
          <div class="top_banner_section_in clearfix">
               <div class="TopLeft"><span id="triggermap"></span></div>
               <div class="TopRight"></div>
               <div class="iprelative">
               	<div id="map_canvas" style="width: 100%; height:<?php echo $heigh;?>px" class="map_canvas"></div>               
                    <div id="map_loading_div" style="width: 100%; height:<?php echo $heigh;?>px; display: none;"></div>                     
                    <div id="map_marker_nofound"><?php _e('<p>Your selected category do not have any records yet at your current location.</p>',LM_DOMAIN) ?></div>     
               </div>   
                <form id="ajaxform" name="slider_search" class="" action="javascript:void(0);"  onsubmit="return(new_googlemap_ajaxSearch());">
                	
				<?php if($post_info):?>
               	<div class="paf_row map_post_type" id="toggle_postID" style="display:none;">
                    <?php for($c=0;$c<count($post_info);$c++): ?>
					<label><input type="checkbox" onclick="newgooglemap_initialize(this);"  value="<?php echo str_replace("&",'&amp;',$post_info[$c]);?>"  <?php if(!empty($_POST['posttype']) && !in_array(str_replace("&",'&amp;',$post_info[$c]) ,$_POST['posttype'])):?> <?php else:?> checked="checked" <?php endif;?> id="<?php echo str_replace("&",'&amp;',$post_info[$c]);?>" name="posttype[]"> <?php echo $post_info[$c];?></label><span id='<?php echo $post_info[$c].'_toggle';?>' class="toggle_post_type toggleoff" onclick="custom_post_type_taxonomy('<?php echo $post_info[$c].'_category';?>',this)"></span>
                         <div class="custom_categories" id="<?php echo $post_info[$c].'_category';?>" style="display:none;">
                         	 <?php foreach($mapcategory_info[$post_info[$c]] as $key => $value){ ?>
                    				<label><input type="checkbox" onclick="newgooglemap_initialize(this);"  value="<?php echo $value['slug'];?>"  <?php if(!empty($_POST['categoryname']) && !in_array($key,$_POST['categoryname'])):?> <?php else:?> checked="checked" <?php endif;?> id="<?php echo $key;?>" name="categoryname[]<?php //echo $key;?>"><img height="14" width="8" alt="" src="<?php echo $value['icon']?>"> <?php echo $value['name']?></label>
                    
                    <?php }?>
                         </div>
                         
                    <?php endfor;?>
                    </div>
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
		 <?php } ?>
		<?php if($showlisting ==1 ){ ?>
		<div id="cities_post">
		<?php
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$args = array('post_type'      => $post_info,
				    'posts_per_page' =>get_option('posts_per_page'),
				    'paged'          =>$paged,
				    'post_status'    => 'publish',
				    'orderby'        => 'meta_value',
				    'order'          => 'ASC',
				    );
		
		add_filter('posts_where', 'location_shortcode_multicity_where');
		$result = new WP_Query( $args );
		remove_filter('posts_where', 'location_shortcode_multicity_where');
		$wp_query=$result;		
		$pcount=0; 
		while ( $result->have_posts() ) : 
				$result->the_post(); 
				location_listing_format($post);
		
		endwhile;
		
		?>
		</div>
           <div id="listpagi">
               <div class="pagination pagination-position">
               	<?php if(function_exists('pagenavi_plugin')) { pagenavi_plugin(); } ?>
               </div>
          </div>
		 </div>
		<?php 
		wp_reset_query(); 
	} 
	
	return ob_get_clean();
}
function googlemap_city_initialization(	$post_info,$city_id){
	global $wpdb,$current_cityinfo;
	$j=0;
	$pids=array("");
	$post_type =$post_info;
	$templatic_settings=get_option('templatic_settings');
	//$categoryname =(explode(',',substr($post_info,0,-1)));
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
			
				
			$cat_ID=$cat->term_id;		
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type[$i],'public'   => true, '_builtin' => true ));
				
			$args=array( 
					   'post_type'      => trim($post_type[$i]),
					   'posts_per_page' => -1    ,
					   'post_status'    => 'publish',
					   'order_by'       =>'date',
					   'order'          => 'ASC'
					   );
			add_filter('posts_where', 'location_multicity_where');
			$post_details= new WP_Query($args);			
			remove_filter('posts_where', 'location_multicity_where');
			$content_data='';					
			if ($post_details->have_posts()) :
				$srcharr = array("'");
				$replarr = array("\'");					
				while ( $post_details->have_posts() ) : $post_details->the_post();				
						$ID =get_the_ID();				
						$title = get_the_title($ID);
						$plink = get_permalink($ID);
						$lat = get_post_meta($ID,'geo_latitude',true);
						$lng = get_post_meta($ID,'geo_longitude',true);					
						$address = stripcslashes(str_replace($srcharr,$replarr,get_post_meta(get_the_ID(),'address',true)));						
						$contact = str_replace($srcharr,$replarr,(get_post_meta(get_the_ID(),'phone',true)));
						$website = get_post_meta(get_the_ID(),'website',true);		
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
							$term_icon=TEVOLUTION_LOCATION_URL.'images/pin.png';
							
						$image_class=($post_image)?'map-image' :'';
						$comment_count= count(get_comments(array('post_id' => $ID)));
						$review=($comment_count <=1 )? __('review',LM_DOMAIN):__('reviews',LM_DOMAIN);
						if(($lat && $lng )&& !in_array($ID,$pids))
						{ 
							$retstr ='{';
							$retstr .= '"name":"'.$title.'",';
							$retstr .= '"location": ['.$lat.','.$lng.'],';
							$retstr .= '"message":"<div class=\"google-map-info '.$image_class.'\"><div class=map-inner-wrapper><div class=map-item-info>'.$post_image;
							$retstr .= '<h6><a href='.$plink.' class=ptitle style=color:#444444;font-size:14px;><span>'.$title.'</span></a></h6>';
							if($address){$retstr .= '<p>'.$address.'</p>';}
							if($contact){$retstr .= "<p class=contact >$contact</p>";}
							if($website){$retstr .= '<p class=website><a href= \"'.$website.'\">'.$website.'</a></p>';}
							if($templatic_settings['templatin_rating']=='yes'){
								$rating=draw_rating_star_plugin(get_post_average_rating(get_the_ID()));
								$retstr .= "<p class=\"map_rating\">$rating</p>";
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
			endif;
			
			if($content_data)	
				$cat_content_info[]= implode(',',$content_data);
				
		}	
	}
	
	//
	if($cat_content_info)
	{
		return '[{"totalcount":"'.$j.'",'.substr(implode(',',$cat_content_info),1).']';
	}else
	{
		return '[{"totalcount":"0"}]';
	}
}
?>