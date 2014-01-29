<?php
/*
 * Function Name: get_the_directory_taxonomies
 * Return: product category and tag
 */
function get_the_directory_taxonomies()
{
	global $post;
	$taxonomy_category = get_the_taxonomies();		
	$taxonomy_category = str_replace(CUSTOM_MENU_CAT_LABEL_LISTING.':',__('Posted In ',DOMAIN),$taxonomy_category[CUSTOM_CATEGORY_TYPE_LISTING]);		
	$taxonomy_category = substr($taxonomy_category,0,-1);	
	return $taxonomy_category;
}
function get_the_directory_tag()
{
	global $post;
	$taxonomy_tag = get_the_taxonomies();		
	$taxonomy_tag = str_replace(CUSTOM_MENU_TAG_TITLE_LISTING.':',__('Tagged In ',DOMAIN), @$taxonomy_tag[CUSTOM_TAG_TYPE_LISTING]);		
	$taxonomy_tag = substr($taxonomy_tag,0,-1);	
	return $taxonomy_tag;
}
/*
 * Function Name: directory_single_soical_link
 * Return: display the social link
 *
 */
//add_action('directory_after_post_title','directory_single_soical_link');
function directory_single_soical_link(){	
	global $post;	
	if (is_single() && $post->post_type==CUSTOM_POST_TYPE_LISTING) 
	{
		$post_img = bdw_get_images_plugin($post->ID,'thumb');
		$post_images = $post_img[0];
		$title=urlencode($post->post_title);
		$url=urlencode(get_permalink($post->ID));
		$summary=urlencode(htmlspecialchars($post->post_content));
		$image=$post_images;
		$settings = get_option( "templatic_settings" );
		echo '<div class="share_link">';
			if($settings['facebook_share_detail_page'] == 'yes')
			  {
				?>
				<a onClick="window.open('//www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $title;?>&amp;p[summary]=<?php echo $summary;?>&amp;p[url]=<?php echo $url; ?>&amp;&amp;p[images][0]=<?php echo $image;?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)" id="facebook_share_button"><?php _e('Facebook Share.',T_DOMAIN); ?></a>
				<?php
			  }
			if($settings['google_share_detail_page'] == 'yes'): ?>
				<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
				<div class="g-plus" data-action="share" data-annotation="bubble"></div> 
			<?php endif;
			
			if($settings['twitter_share_detail_page'] == 'yes'): ?>
					<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-text='<?php echo htmlentities($post->post_content);?>' data-url="<?php echo get_permalink($post->ID); ?>" data-counturl="<?php echo get_permalink($post->ID); ?>"><?php _e('Tweet',T_DOMAIN); ?></a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			<?php endif;
			
			if(@$settings['pintrest_detail_page']=='yes'):?>
			<!-- Pinterest -->
			<div class="pinterest"> 
				<a href="//pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;media=<?php echo $image; ?>&amp;description=<?php the_title(); ?>" >Pin It</a>
				<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>                    
			</div>
			<?php endif; 
		echo '</div>';
	}
}
add_action('directory_inside_container_breadcrumb','directory_detail_custom_field');
function directory_detail_custom_field(){
	if(is_single() && get_post_type()==CUSTOM_POST_TYPE_LISTING){
		global $wpdb,$post,$htmlvar_name,$pos_title;
		
		$cus_post_type = get_post_type();
		$heading_type = directory_fetch_heading_post_type(get_post_type());
		
		if(count($heading_type) > 0)
		{
			foreach($heading_type as $key=>$heading)
			{	
				$htmlvar_name[$heading] = get_directory_single_customfields(get_post_type(),$heading,$key);//custom fields for custom post type..
			}
		}		
		return $htmlvar_name;
	}
}
/*
 * Function name: get_directory_listing_customfields
 * Return: return array for event listing custom fields
 */
function get_directory_single_customfields($post_type,$heading='',$heading_key=''){	
	global $wpdb,$post,$posttitle;
	$args = array( 'post_type' => 'custom_fields',
				'posts_per_page' => -1	,
				'post_status' => array('publish'),
				'meta_query' => array('relation' => 'AND',
								array(
									'key'     => 'post_type_'.$post_type.'',
									'value'   => $post_type,
									'compare' => '=',
									'type'    => 'text'
								),		
								array(
									'key'     => 'is_active',
									'value'   =>  '1',
									'compare' => '='
								),
								array(
									'key'     => 'show_on_detail',
									'value'   =>  '1',
									'compare' => '='
								),
								array(
									'key'     => 'heading_type',
									'value'   =>  array('basic_inf',$heading),
									'compare' => 'IN'
								)
							),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value',
				'order' => 'ASC'
		);
	
	remove_all_actions('posts_where');		
	$post_query = null;
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	//$post_query = new WP_Query($args);	
	$post_query = get_transient( '_tevolution_query_single_'.trim($post_type).'_'.trim($heading_key) );		
	if ( false === $post_query && get_option('tevolution_cache_disable')==1 ) {
		$post_query = new WP_Query($args);
		set_transient( '_tevolution_query_single_'.trim($post_type).'_'.trim($heading_key), $post_query, 12 * HOUR_IN_SECONDS );
	}elseif(get_option('tevolution_cache_disable')==''){
		$post_query = new WP_Query($args);
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	
	$htmlvar_name='';
	if($post_query->have_posts())
	{
		while ($post_query->have_posts()) : $post_query->the_post();
			$ctype = get_post_meta($post->ID,'ctype',true);
			$post_name=get_post_meta($post->ID,'htmlvar_name',true);
			$style_class=get_post_meta($post->ID,'style_class',true);
			$option_title=get_post_meta($post->ID,'option_title',true);
			$option_values=get_post_meta($post->ID,'option_values',true);
			$htmlvar_name[$post_name] = array( 'type'=>$ctype,
									    	'label'=> $post->post_title,
										'style_class'=>$style_class,
										'option_title'=>$option_title,
										'option_values'=>$option_values,
										);			
		endwhile;
		wp_reset_query();
	}
	return $htmlvar_name;
	
}
/*
 * Function name: directory_preview_page_fields_collection
 * Return : display the additional custom field
 */
add_action('directory_preview_page_fields_collection','directory_preview_page_fields_collection');
function directory_preview_page_fields_collection(){
	
	$session=$_SESSION['custom_fields'];
	$cur_post_type='listing';
	$heading_type = directory_fetch_heading_post_type(get_post_type());		
	if(count($heading_type) > 0)
	{
		foreach($heading_type as $key=>$heading)
		{	
			$htmlvar_name[$heading] = get_directory_single_customfields($cur_post_type,$heading,$key);//custom fields for custom post type..
		}
	}
	
	$j=0;
	if(!empty($htmlvar_name)){
		echo '<div class="listing_custom_field">';
		foreach($htmlvar_name as $key=>$value){
			$i=0;
			foreach($value as $k=>$val){
				$key = ($key=='[#taxonomy_name#]')?'Listing Information': $key;
				if($k!='post_title' && $k!='post_content' && $k!='post_excerpt' && $k!='post_images' && $k!='listing_timing' && $k!='listing_logo' && $k!='video' && $k!='post_tags' && $k!='map_view' && $k!='proprty_feature' && $k!='phone' && $k!='email' && $k!='website' && $k!='twitter' && $k!='facebook' && $k!='google_plus' && $k!='address')
				{
					
					
					$field=$session[$k];
					if($val['type'] == 'multicheckbox' && $field!=""):						
						if($i==0){echo '<h4 class="custom_field_headding">'.$key.'</h4>';$i++;}	
						$option_values = explode(",",$val['option_values']);				
						$option_titles = explode(",",$val['option_title']);
						for($i=0;$i<count($option_values);$i++){
							if(in_array($option_values[$i],$field)){
								if($option_titles[$i]!=""){
									$checkbox_value .= $option_titles[$i].',';
								}else{
									$checkbox_value .= $option_values[$i].',';
								}
							}
						}	
						?>
						<p class='<?php echo $k;?>'><label><?php echo $val['label']; ?></label> : <?php echo substr($checkbox_value,0,-1); ?></p>
					<?php 
					elseif($val['type'] == 'radio' && $field!=''):
						$option_values = explode(",",$val['option_values']);				
						$option_titles = explode(",",$val['option_title']);
						for($i=0;$i<count($option_values);$i++){
							if($field == $option_values[$i]){
								if($option_titles[$i]!=""){
									$rado_value = $option_titles[$i];
								}else{
									$rado_value = $option_values[$i];
								}							
								?>
								<p class='<?php echo $k;?>'><label><?php echo $val['label']; ?></label>: <?php echo $rado_value;?></p>
								<?php
							}
						}	
					endif;
					$field=stripslashes($session[$k]);
					if($val['type'] != 'multicheckbox' && $val['type'] != 'radio' &&$field!=''):
					if($i==0){echo '<h4 class="custom_field_headding">'.$key.'</h4>';$i++;}?>                              
					<p class='<?php echo $k;?>'><label><?php echo $val['label']; ?></label> : <?php echo $field;?></p>
					<?php
					endif;
				
				}// End If condition
				
				$j++;
			}// End second foreach
		}// END First foreach
		echo '</div>';
	}
}
?>