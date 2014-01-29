<?php
add_action('widgets_init','location_plugin_widgets_init');
function location_plugin_widgets_init()
{
	register_widget('widget_location_post_city_id');
}
/*
 * multi city widget class
 * Class name: widget_location_post_city_id
 */
class widget_location_post_city_id extends WP_Widget {
	
	function widget_location_post_city_id() {
	//Constructor
		$widget_ops = array('classname' => 'Multi City Options', 'description' => __('Displays a dropdown for selecting a country, state and city. Use the widget only once per page. Works best in sidebar areas.',LM_DOMAIN) );		
		$this->WP_Widget('widget_post_city_id', __('T &rarr; Select a City',LM_DOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
		global $wpdb,$country_table,$zones_table,$multicity_table;
		$title = empty($instance['title']) ? __('Multicity',LM_DOMAIN) : apply_filters('widget_title', $instance['title']);
		if(!session_id())
			session_start();
		if(isset($_POST['widget_city']) && $_POST['widget_city']!=""){
			$_SESSION['post_city_id']=$_POST['widget_city'];	
		}	
		$country_table = $wpdb->prefix."countries";
		$zones_table =$wpdb->prefix . "zones";	
		$multicity_table = $wpdb->prefix . "multicity";	
		if($wpdb->get_var("SHOW TABLES LIKE '$multicity_table'") == $multicity_table) {
			if(isset($_SESSION['post_city_id']) && $_SESSION['post_city_id']!=''){
				if(get_query_var('city')!='')
					$sql="SELECT * FROM $multicity_table where  	city_slug='".get_query_var('city')."'";
				else
					$sql="SELECT * FROM $multicity_table where city_id=".$_SESSION['post_city_id'];
			}else{
					$sql="SELECT * FROM $multicity_table where is_default=1";
			}
		}
		$default_city = $wpdb->get_results($sql);
		$default_city_id=$default_city[0]->city_id;
		$_SESSION['post_city_id']=$default_city_id;
		$default_country_id=$default_city[0]->country_id;
		$default_zone_id=$default_city[0]->zones_id;
		
		$countryinfo = $wpdb->get_results("SELECT  distinct  c.country_id,c.country_name,GROUP_CONCAT(mc.cityname) as cityname, GROUP_CONCAT(mc.city_slug) as city_slug   FROM $country_table c,$multicity_table mc where mc.city_id in(SELECT distinct meta_value from {$wpdb->prefix}postmeta where `meta_key` LIKE 'post_city_id') AND c.`country_id`=mc.`country_id`  AND c.is_enable=1 group by country_name order by country_name ASC");
		$zoneinfo = $wpdb->get_results("SELECT distinct z.zones_id,z.* FROM $zones_table z, $multicity_table mc where z.zones_id=mc.zones_id AND mc.country_id=$default_country_id  order by zone_name ASC");
		$cityinfo = $wpdb->get_results("SELECT * FROM $multicity_table where zones_id=$default_zone_id AND country_id=$default_country_id order by cityname ASC");
		?>
		<div id="widget_location">
          	<script type="text/javascript">
			jQuery(document).ready(function(){	
				jQuery("#widget_country").change(function(){				
					var country_id = jQuery('#widget_country').val();		
					jQuery.ajax({
						url:ajaxUrl,
						type:'POST',
						data:'action=fill_states_cmb&country_id=' + country_id+'&front=1',
						success:function(results) {				
							jQuery('#widget_zone').html(results);
						}
					});	
				});		
			});
			
			jQuery(document).ready(function(){	
				jQuery("#widget_zone").change(function(){				
					var state_id = jQuery('#widget_zone').val();		
					jQuery.ajax({
						url:ajaxUrl,
						type:'POST',
						data:'action=fill_city_cmb&state_id=' + state_id,
						success:function(results) {				
							jQuery('#widget_city').html(results);
						}
					});	
				});		
			});
			
			jQuery(document).ready(function(){	
				jQuery("#widget_multicity_form").change(function(){					   
					jQuery('#widget_multicity_form').submit();
				});		
			});
			</script>
               <?php echo '<h3 class="widget-title">'.$title.'</h3>';?>
			<ul class="widget_location_nav">
				<li>
					<select name="widget_country" id="widget_country">
						<option value=""><?php _e('All Countries',LM_DOMAIN);?></option>
					<?php foreach($countryinfo as $country): $selected=($country->country_id==$default_country_id)? 'selected':'';
						$country_name=$country->country_name;
						 if (function_exists('icl_register_string')) {									
								icl_register_string('location-manager', 'location_country_'.$country->country_id,$country_name);
								$country_name = icl_t('location-manager', 'location_country_'.$country->country_id,$country_name);
						  }
					?>
						<option value="<?php echo $country->country_id?>" <?php echo $selected;?>><?php echo $country_name;?></option>
					<?php endforeach; ?>
					</select>
				</li>
				<li>
					<select name="widget_zone" id="widget_zone">
						<option value=""><?php _e('All Regions',LM_DOMAIN);?></option>
					<?php foreach($zoneinfo as $zone): $selected=($zone->zones_id ==$default_zone_id)? 'selected':'';
							$zone_name=$zone->zone_name;
							 if (function_exists('icl_register_string')) {									
									icl_register_string('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
									$zone_name = icl_t('location-manager', 'location_zone_'.$zone->zones_id,$zone_name);
							  }	
					?>
						<option value="<?php echo $zone->zones_id?>" <?php echo $selected;?>><?php echo $zone_name;?></option>
					<?php endforeach;?>
					</select>
				</li>
				<li>
					<form name="widget_multicity_form" id="widget_multicity_form" action="" method="post">
						<select name="widget_city" id="widget_city">
							<option value=""><?php _e('All Cities',LM_DOMAIN);?></option>
						<?php foreach($cityinfo as $city): $selected=($city->city_id ==$default_city_id)? 'selected':'';
								   $cityname=$city->cityname;
								   if (function_exists('icl_register_string')) {									
										icl_register_string('location-manager', 'location_city_'.$city->city_slug,$cityname);
										$cityname = icl_t('location-manager', 'location_city_'.$city->city_slug,$cityname);
								   }
						?>
							<option value="<?php echo $city->city_id?>" <?php echo $selected;?>><?php echo $cityname;?></option>
						<?php endforeach;?>
						</select>
					</form>
				</li>
			</ul>
		</div>
		<?php
		
		/* Set the multicity info*/
		location_current_multicity();
		
	}
	function update($new_instance, $old_instance) {
		//save the widget		
		return $new_instance;
	}
	function form($instance) {
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Multicity') );
		$title = strip_tags($instance['title']);
		$desc1 = ($instance['desc1']);
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title',LM_DOMAIN);?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
		<?php
	}
}
?>