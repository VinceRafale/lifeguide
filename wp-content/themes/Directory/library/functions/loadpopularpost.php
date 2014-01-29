<?php
require_once("../../../../../wp-load.php");
global $wpdb,$posts,$post,$query_string;
if(is_plugin_active('wpml-translation-management/plugin.php'))
{
	global $sitepress;
	$sitepress->switch_lang($_REQUEST['limitarr'][7]);
}
$ppost = get_option('widget_templatic_popular_post_technews');
	foreach($ppost as $key=>$value)
	{		
		$popular_per= @$value['popular_per'];
		$show_excerpt= @$value['show_excerpt'];
		$show_excerpt_length= @$value['show_excerpt_length'];
		$number= @$value['number'];		
		break;
	}
	$posthtml = '';		
	$start = $_REQUEST['limitarr'][0];
	$end = $_REQUEST['limitarr'][1];
	$total = $_REQUEST['limitarr'][2];
	$post_type = $_REQUEST['limitarr'][3];
	$num=$_REQUEST['limitarr'][4];
	$popular_per=$_REQUEST['limitarr'][5];
	$number=$_REQUEST['limitarr'][6];
	$show_excerpt=$_REQUEST['limitarr'][8];
	$show_excerpt_length=$_REQUEST['limitarr'][9];
	if(isset($number))
		$_SESSION['total'] = $number;		
	
	if(($start + $end) > $_SESSION['total'])
	{
		$end =   ($_SESSION['total'] - $start );
	}			
		
	//$popular_per = $ppost[3]['popular_per'];	
	if($popular_per == 'views'){		
		$args_popular=array(
					'post_type'=>$post_type,
					'post_status'=>'publish',
					'posts_per_page' => $end,
					'paged'=>$num,
					'meta_key'=>'viewed_count',
					'orderby' => 'meta_value_num',
					'meta_value_num'=>'viewed_count',
					'order' => 'DESC'
					);
	}elseif($popular_per == 'dailyviews'){
		$args_popular=array(
					'post_type'=>$post_type,
					'post_status'=>'publish',
					'posts_per_page' => $end,
					'paged'=>$num,
					'meta_key'=>'viewed_count_daily',
					'orderby' => 'meta_value_num',
					'meta_value_num'=>'viewed_count_daily',
					'order' => 'DESC'
					);
	}else{		
		$args_popular=array(
					'post_type'=>$post_type,
					'post_status'=>'publish',
					'posts_per_page' => $end,
					'paged'=>$num,					
					'orderby' => 'comment_count',					
					'order' => 'DESC'
					);
	}
remove_all_actions('posts_orderby');
function short_time_diff( $from, $to = '' ) {
    $diff = human_time_diff($from,$to);
    $replace = array(
        'min' => __('min',THEME_DOMAIN),
        'mins' => __('mins',THEME_DOMAIN),
        'hour' => __('hours',THEME_DOMAIN),
        'hours' => __('hours',THEME_DOMAIN),
        'day' => __('day',THEME_DOMAIN),
        'days' => __('days',THEME_DOMAIN),
        'week' => __('week',THEME_DOMAIN),
        'weeks' => __('weeks',THEME_DOMAIN),
        'month' => __('month',THEME_DOMAIN),
        'months' => __('months',THEME_DOMAIN),
        'year' => __('year',THEME_DOMAIN),
        'years' => __('years',THEME_DOMAIN),
    );
    return strtr($diff,$replace);
}
if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
	add_filter('posts_where', 'location_multicity_where');
}
$popular_post_query = new WP_Query($args_popular);	
if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
	remove_filter('posts_where', 'location_multicity_where');
}
$length = 0;
if( @$show_excerpt_length){
	$length = $show_excerpt_length;
}else{
	$length = 75;
}
if($popular_post_query):
	$post_excerpt = '';
	$post_content = '';
	while ($popular_post_query->have_posts()) : $popular_post_query->the_post();
		$post_title = stripslashes($post->post_title);
		if($post->post_excerpt != ""){
			$post_excerpt = strip_tags(excerpt($length));
		}else{
			$post_excerpt = strip_tags(content($length));
		}	
		//echo $post_excerpt;
		$guid = get_permalink($post->ID);
		
		$comments = $post->comment_count.' '.__('Comment',THEME_DOMAIN);
		if($post->comment_count > 1){
			$comments = $post->comment_count.' '.__('Comments',THEME_DOMAIN);
		}
		$posthtml .= '<li class="clearfix">';			
		$posthtml .= apply_filters('popular_post_thumb_image','');
		
		$meta_admin = apply_filters('load_popular_post_filter','');
		
		if($show_excerpt ==1){
			$post_content = "<p>".$post_excerpt."</p>";
		}
		
		if(isset($post->comment_date) && strtotime($post->comment_date) != 0) {
			$du = strtotime($post->comment_date);
		} else {
			$du = strtotime($post->post_date);
		}
		$fv = short_time_diff($du, current_time('timestamp')). " " . __('ago',THEME_DOMAIN);
		$fv = __($fv,THEME_DOMAIN);
		if($popular_per == 'views' || $popular_per == 'dailyviews'){
			if($popular_per == 'dailyviews'): $views= get_post_meta($post->ID,'viewed_count_daily',true); else: $views= get_post_meta($post->ID,'viewed_count',true); endif;
			if($views <= 1){ $views = $views." ".__('view',THEME_DOMAIN); }else{ $views = $views." ".__('views',THEME_DOMAIN);  }
			$posthtml .= '<div class="post_data"><h3><a href="'.$guid.'" title="'.$post_title.'">'.$post_title.'</a></h3><p>'.$meta_admin.'<span class="views">'.$views.'</span><span class="date">'.$fv.'</span></p>'.$post_content.'</div></li>';
		}else{
			$posthtml .= '<div class="post_data"><h3><a href="'.$guid.'" title="'.$post_title.'">'.$post_title.'</a></h3><p>'.$meta_admin.'<span class="views"> <a href="'.$guid.'#comments">'.$comments.'</a></span><span class="date">'.$fv.'</span></p>'.$post_content.'</div></li>';
		}	
	endwhile;
	echo $posthtml;	
else:?>
<p>
  <?php _e('No Popular post fond.',THEME_DOMAIN);?>
</p>
<?php
endif;
/**--- Function : Count/fetch the daily views and total views EOF--**/
?>
