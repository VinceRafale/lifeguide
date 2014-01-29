<?php
add_action('widgets_init','directory_googlemap_widgets_init');
function directory_googlemap_widgets_init(){
	register_widget('widget_googlemap_homepage');
}
/* BOF - Home page Google map widget - FOr multicity */
class widget_googlemap_homepage extends WP_Widget {
	function widget_googlemap_homepage() {	
		$widget_ops = array('classname' => 'widget Google Map in Home page', 'description' => __('Display a Google map with custom icons and marker clusters while operating multiple cities. Widget works best inside the Homepage Slider or Homepage - Main Content area.',LM_DOMAIN) );		
		$this->WP_Widget('googlemap_homepage', __('T &rarr; Homepage Map - multi city',LM_DOMAIN), $widget_ops);
	}
	function widget($arg, $instance) {
		global $current_cityinfo;
		$height = empty($instance['height']) ? '425' : apply_filters('widget_height', $instance['height']);
		$clustering = empty($instance['clustering']) ? '' : apply_filters('widget_clustering', $instance['clustering']);
		$city_category_id=($current_cityinfo['categories']!='')? explode(',',$current_cityinfo['categories']) :array();
		if(!empty($city_category_id)){
			$post_info=(strstr($current_cityinfo['post_type'],','))? explode(',',$current_cityinfo['post_type']):array($current_cityinfo['post_type']) ;		
		}else{
			$post_info=array();
		}
		
		$mapcategory_info =get_newmap_categoryinfo($post_info);
		//$mappost_info =get_map_postinfo($post_info);
		
		$tmpdata = get_option('templatic_settings');			
		$maptype=($current_cityinfo['map_type'] != '')? $current_cityinfo['map_type']: 'ROADMAP';		
		$latitude    = $current_cityinfo['lat'];
		$longitude   = $current_cityinfo['lng'];
		$map_type    = ($current_cityinfo['map_type']) ? $current_cityinfo['map_type'] : 'ROADMAP';
		$map_display = $current_cityinfo['is_zoom_home'];
		$zoom_level  = ($current_cityinfo['scall_factor']) ? $current_cityinfo['scall_factor'] : 3;
		
		wp_print_scripts( 'google-maps-apiscript' );		
		wp_print_scripts( 'google-clusterig' );		
		wp_print_scripts( 'widget-googlemap-js' );
		
		$google_map_customizer=get_option('google_map_customizer');// store google map customizer required formate.
		?>		
        <script type="text/javascript">
		var map_latitude= '<?php echo $latitude?>';
		var map_longitude= '<?php echo $longitude?>';
		var map_zomming_fact= <?php echo $zoom_level;?>;		
		<?php if($map_display == 1) { ?>
		var multimarkerdata = new Array();
		<?php }?>
		var zoom_option = '<?php echo $map_display; ?>';
		var markers = '';		
		var markerArray = [];
		var ClustererMarkers=[];
		var m_counter=0;
		var map = null;
		var mgr = null;
		var mc = null;		
		var mClusterer = null;
		var showMarketManager = false;
		var PIN_POINT_ICON_HEIGHT = 32;
		var PIN_POINT_ICON_WIDTH = 20;
		var clustering = '<?php echo $clustering; ?>';
		var infobox;
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
		google.maps.event.addDomListener(window, 'load', newgooglemap_initialize);
		</script>         
          <div id="map_sidebar" class="map_sidebar">
          <div class="top_banner_section_in clearfix">
               <div class="TopLeft"><span id="triggermap"></span></div>
               <div class="TopRight"></div>
               <div class="iprelative">
               	<div id="map_canvas" style="width: 100%; height:<?php echo $height;?>px" class="map_canvas"></div>               
                    <div id="map_loading_div" style="width: 100%; height:<?php echo $height;?>px; display: none;"></div>                     
                    <div id="map_marker_nofound"><?php _e('<p>Your selected category do not have any records yet at your current location.</p>',LM_DOMAIN) ?></div>     
               </div>             
              
               <form id="ajaxform" name="slider_search" class="pe_advsearch_form" action="javascript:void(0);"  onsubmit="return(new_googlemap_ajaxSearch());">
                	<div class="paf_search"><input  type="text" class="" id="search_string" name="search_string" value="" placeholder="<?php _e('Title or Keyword',LM_DOMAIN);?>" onclick="this.placeholder=''" onmouseover="this.placeholder='<?php _e('Title or Keyword',DOMAIN);?>'"/></div>
               
               <?php if($post_info):$tevolution_post=get_option('templatic_custom_post');	?>
               	<div class="paf_row map_post_type" id="toggle_postID" style="display:block;">
                    <?php for($c=0;$c<count($post_info);$c++):
							if($post_info[$c])
							{	?>
					<div class="mw_cat_title">
                    <label><input type="checkbox" data-category="<?php echo str_replace("&",'&amp;',$post_info[$c]).'categories';?>" onclick="newgooglemap_initialize(this);"  value="<?php echo str_replace("&",'&amp;',$post_info[$c]);?>"  <?php if(!empty($_POST['posttype']) && !in_array(str_replace("&",'&amp;',$post_info[$c]) ,$_POST['posttype'])):?> <?php else:?> checked="checked" <?php endif;?> class="<?php echo str_replace("&",'&amp;',$post_info[$c]).'custom_categories';?>" id="<?php echo str_replace("&",'&amp;',$post_info[$c]).'custom_categories';?>" name="posttype[]"> <?php echo ($tevolution_post[$post_info[$c]]['label'])? $tevolution_post[$post_info[$c]]['label']: ucfirst($post_info[$c]);?></label><span id='<?php echo $post_info[$c].'_toggle';?>' class="toggle_post_type toggleon" onclick="custom_post_type_taxonomy('<?php echo $post_info[$c].'categories';?>',this)"></span></div>
                        
                         <div class="custom_categories <?php echo str_replace("&",'&amp;',$post_info[$c]).'custom_categories';?>" id="<?php echo str_replace("&",'&amp;',$post_info[$c]).'categories';?>" >
                         	 <?php foreach($mapcategory_info[$post_info[$c]] as $key => $value){ ?>
                    				<label><input type="checkbox" onclick="newgooglemap_initialize(this);"  value="<?php echo $value['term_id'];?>"  <?php if(!empty($_POST['categoryname']) && !in_array($key,$_POST['categoryname'])):?> <?php else:?> checked="checked" <?php endif;?> id="<?php echo $key;?>" name="categoryname[]"><img height="14" width="8" alt="" src="<?php echo $value['icon']?>"> <?php echo $value['name']?></label>
                    
                    <?php }?>
                         </div>
                         
                    <?php }
						endfor;?>
                    </div>
                    <div id="toggle_post_type" class="paf_row toggleon" onclick="toggle_post_type();"></div>
                    <?php endif;?>
               </form>     
               
          </div>
          </div>
          <script>
		var maxMap = document.getElementById( 'triggermap' );		
		google.maps.event.addDomListener(maxMap, 'click', showFullscreen);
		function showFullscreen() {
			  // window.alert('DIV clicked');
			    jQuery('#map_sidebar').toggleClass('map-fullscreen');
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
		$instance = wp_parse_args( (array) $instance, array(  'height' => 500,'clustering'=> '') );		
		$height = strip_tags($instance['height']);
		$clustering = strip_tags($instance['clustering']);
		?>
	
		<p>
		 <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Map Height: <small>(Default is 500px. To change enter a numeric value.)</small>',LM_DOMAIN);?>
		 <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo attribute_escape($height); ?>" />
		 </label>
	    </p>
		<p>
		<?php if($clustering) { $checked = "checked=checked"; }else{ $checked =''; } ?>
		 <label for="<?php echo $this->get_field_id('clustering'); ?>">
		 <input id="<?php echo $this->get_field_id('clustering'); ?>" name="<?php echo $this->get_field_name('clustering'); ?>" type="checkbox" value="1" <?php echo $checked; ?>/>
		 <?php _e('Disable Clustering',LM_DOMAIN); ?></label>
	    </p>
	    <?php
	}
}
/* EOF - Home page Google map widget - FOr multicity */

/* Function Name: get_newmap_categoryinfo
   Return: return the categories array of selected city
*/
function get_newmap_categoryinfo($post_type){
	
	global $current_cityinfo;
	$city_category_id=($current_cityinfo['categories']!='')? explode(',',$current_cityinfo['categories']) :array();
	
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
		$categoriesinfo='';
		foreach($catname_arr as $cat)	{			
			if(@$cat->term_icon)
				$term_icon=$cat->term_icon;
			else
				$term_icon=TEVOLUTION_LOCATION_URL.'images/pin.png';			
			
			if(in_array(@$cat->term_id,$city_category_id) || in_array('all',$city_category_id)){
				$categoriesinfo[]=array('term_id'=>$cat->term_id, 'slug'=>$cat->slug,'name'=>$cat->name,'icon'=>$term_icon);	
			}
			
		}
		if(!empty($city_category_id)){
			$catinfo_arr[$post_type[$i]]=$categoriesinfo;
		}
	}		
	return $catinfo_arr;
}

/*
 * Function Name: googlemap_initialize
 * Return: send the google map marker popup info in jason
 */
add_action('wp_ajax_nopriv_googlemap_initialize','googlemap_initialize');
add_action('wp_ajax_googlemap_initialize','googlemap_initialize');
function googlemap_initialize(){
	global $wpdb,$current_cityinfo;
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
		if ( false === ( $catname_arr = get_transient( '_tevolution_query_googlemapwidget'.$post_type[$i]) )  && get_option('tevolution_cache_disable')==1 ) {
			$catname_arr=get_categories( $r );
			set_transient( '_tevolution_query_googlemapwidget'.$post_type[$i], $catname_arr, 12 * HOUR_IN_SECONDS );				
		}elseif(get_option('tevolution_cache_disable')==''){
			$catname_arr=get_categories( $r );
		}	
		
		foreach($catname_arr as $cat){
			if(!in_array($cat->term_id,$categoryname))
				continue;
				
			$cat_ID.=$cat->term_id.',';
		}
		$args3=array('post_type'      => trim($post_type[$i]),
				   'posts_per_page' => 400,
				   'post_status'    => 'publish',
				   'tax_query'      => array(
									  array(
										 'taxonomy' => $taxonomies[0],
										 'field'    => 'id',
										 'terms'    => explode(',',$cat_ID),
										 'operator' => 'IN'
									  )
								   ),
				   );			
		add_filter( 'posts_where', 'google_search_posts_where', 10, 2 );
		$post_details= new WP_Query($args3);		
		remove_filter( 'posts_where', 'google_search_posts_where', 10, 2 );
		$content_data='';					
		if ($post_details->have_posts()) :			
			while ( $post_details->have_posts() ) : $post_details->the_post();
					global $post;
					$post_categories = get_the_terms( get_the_ID() ,$taxonomies[0]);
					foreach($post_categories as $post_category)
					if($post_category->term_icon){
						$term_icon=$post_category->term_icon;
					}else{
						$term_icon=TEVOLUTION_LOCATION_URL.'images/pin.png';
					}
					$ID =get_the_ID();				
					$title = get_the_title($ID);
					$plink = get_permalink($ID);
					$lat = get_post_meta($ID,'geo_latitude',true);
					$lng = get_post_meta($ID,'geo_longitude',true);					
					$address = str_replace($srcharr,$replarr,(get_post_meta($ID,'address',true)));
					$contact = str_replace($srcharr,$replarr,(get_post_meta($ID,'phone',true)));
					$website = get_post_meta($ID,'website',true);
					if(!strstr($website,'http') && $website)
						$website = 'http://'.$website;
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
					
					$image_class=($post_image)?'map-image' :'';					
						
					$comment_count= count(get_comments(array('post_id' => $ID)));
					$review=($comment_count <=1 )? __('review',LM_DOMAIN):__('reviews',LM_DOMAIN);	
					
					if(($lat && $lng )&& !in_array($ID,$pids))
					{ 	
						$retstr ='{';
						$retstr .= '"name":"'.str_replace($title_srcharr,$title_replarr,$post->post_title).'",';
						$retstr .= '"location": ['.$lat.','.$lng.'],';
						$retstr .= '"message":"<div class=\"google-map-info '.$image_class.'\"><div class=map-inner-wrapper><div class=map-item-info>'.$post_image;
						$retstr .= '<h6><a href='.$plink.' class=ptitle><span>'.$title.'</span></a></h6>';							
						if($address){$retstr .= '<p class=address>'.$address.'</p>';}
						if($contact){$retstr .= '<p class=contact>'.$contact.'</p>';}
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
function google_search_posts_where( $where, &$wp_query){
	global $wpdb;
	
	if(isset($_SESSION['post_city_id']) && $_SESSION['post_city_id']!=''){
		$where .= " AND $wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='post_city_id' and FIND_IN_SET( ".$_SESSION['post_city_id'].", pm.meta_value ))";
	}
	
	if(isset($_REQUEST['search_string']) && $_REQUEST['search_string']!=''){
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $_REQUEST['search_string']) ) . '%\'';
		
		$where .= " OR  ($wpdb->posts.ID in (select p.ID from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p where c.name like '".esc_sql( like_escape( $_REQUEST['search_string']) )."' and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.post_status = 'publish' group by  p.ID))";
	}	
	return $where;	
}
add_action('wp_head','location_google_map_responsive');
function location_google_map_responsive(){
	$city_googlemap_setting = get_option('city_googlemap_setting'); 		
	if(strtolower(@$city_googlemap_setting['google_map_hide']) == strtolower('yes')){ ?>
		<style type='text/css'>
			@media only screen and (max-width: 719px){
				.map_sidebar{ display:none; }
			}
		</style>
	<?php }	
}
?>