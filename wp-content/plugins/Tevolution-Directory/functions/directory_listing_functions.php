<?php
add_action('directory_before_container_breadcrumb','directory_breadcrumb');
/*
 * display the bread crumb
 * Function Name:single_post_type_breadcrumb 
 */
function directory_breadcrumb()
{
	if ( current_theme_supports( 'breadcrumb-trail' ) && supreme_get_settings('supreme_show_breadcrumb')){
		breadcrumb_trail( array( 'separator' => '&raquo;' ) );
	}
}
add_action('directory_subcategory','directory_subcategory');
add_action('directory_after_subcategory','directory_sorting_option');
add_action('directory_category_page_image','directory_category_page_image');
add_action('templ_post_title','directory_listing_after_title',13);
add_action('directory_after_taxonomies','directory_after_taxonomies_content');
/* Archive Page */
add_action('directory_archive_page_image','directory_category_page_image');
add_action('directory_after_archive_title','directory_sorting_option');
/*Search Page */
add_action('directory_after_search_title','directory_sorting_option',11);
if(isset($_REQUEST['nearby']) && $_REQUEST['nearby'] == 'search')
{
	add_action('directory_after_search_title','directory_listing_city_name');
}
/*Remove Tevolution favourite html function */
add_action('init','remove_tevolution_favourites',11);
function remove_tevolution_favourites(){
	remove_action('templ_post_title','tevolution_favourite_html',11,@$post);
}
/*
 * Function Name: directory_subcategory
 * Return: display the listing category sub categories
 */
function directory_subcategory(){
	global $wpdb,$wp_query;	
	$term_id = $wp_query->get_queried_object_id();
	$taxonomy_name = CUSTOM_CATEGORY_TYPE_LISTING;	
	do_action('tevolution_category_query');
	$featured_catlist_list =  wp_list_categories('title_li=&child_of=' . $term_id .'&echo=0&taxonomy='.$taxonomy_name.'&show_count=0&hide_empty=1&pad_counts=0&show_option_none=&orderby=name&order=ASC');
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{
		remove_filter( 'terms_clauses','locationwise_change_category_query',10,3 );	
	}
	if(!strstr(@$featured_catlist_list,'No categories'))
	{
		echo '<div id="sub_listing_categories">';
		echo '<ul>';
		echo $featured_catlist_list;
		echo '</ul>';
		echo '</div>';
	}
}
/*
 * Function Name: directory_sorting_option
 * Return: display the sorting option on front page listing
 */
function directory_sorting_option(){	
	global $wpdb,$wp_query;
	if($wp_query->found_posts==0 && (!isset($_REQUEST['directory_sortby'])))
		return '';
	
	$current_term = $wp_query->get_queried_object();
	$templatic_settings=get_option('templatic_settings');
	$googlemap_setting=get_option('city_googlemap_setting');
	/*custom post type permalink */
	if(!is_tax() && is_archive() && !is_search())
	{			
		$post_type=(get_post_type()!='')? get_post_type() : get_query_var('post_type');
		$permalink = get_post_type_archive_link($post_type);
		$permalink=str_replace('&event_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
	}elseif(is_search()){
		$search_query_str=str_replace('&directory_sortby=alphabetical&sortby='.@$_REQUEST['sortby'],'',$_SERVER['QUERY_STRING']);
		$permalink= site_url()."?".$search_query_str;
	}else{
		$permalink=($current_term->slug) ?  get_term_link($current_term->slug, $current_term->taxonomy):'';
		if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!='')
			$permalink=str_replace('&directory_sortby=alphabetical&sortby='.$_REQUEST['sortby'],'',$permalink);
		
	}
	
	$post_type=get_post_type_object( get_post_type());
		
	if(false===strpos($permalink,'?')){
	    $url_glue = '?';
	}else{
		$url_glue = '&amp;';	
	}
	?>
     <div class='directory_manager_tab clearfix'>
     	<div class="sort_options">
          <?php if(have_posts()!=''):?>
		<ul class='view_mode viewsbox'>
			<li><a class='switcher first gridview <?php if($templatic_settings['default_page_view']=="gridview"){echo 'active';}?>' id='gridview' href='#'><?php _e('GRID VIEW',DOMAIN);?></a></li>
			<li><a class='switcher last listview  <?php if($templatic_settings['default_page_view']=="listview"){echo 'active';}?>' id='listview' href='#'><?php _e('LIST VIEW',DOMAIN);?></a></li>
			<?php if(isset($googlemap_setting['category_googlemap_widget']) && $googlemap_setting['category_googlemap_widget']=='yes'):?> <li><a class='map_icon <?php if($templatic_settings['default_page_view']=="mapview"){echo 'active';}?>' id='event_map' href='#'><?php _e('MAP',DOMAIN);?></a></li><?php endif;?>
		</ul>	
	 <?php endif;?>
	<?php 
	if(isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby']=='alphabetical'){
		$_SESSION['alphabetical']='1';	
	}else{
		unset($_SESSION['alphabetical']);
	}
	if($templatic_settings['sorting_type']!='normal' && !empty($templatic_settings['sorting_option']) && have_posts()!=''){
		
		if(!empty($templatic_settings['sorting_option'])){
	?>
          <form action="<?php echo directory_full_url(); ?>" method="get" id="directory_sorting" name="directory_sorting">
               <select name="directory_sortby" id="directory_sortby" onchange="sort_as_set(this.value)">
                    <option value=""><?php echo sprintf(__('Sort %s',DOMAIN) ,$post_type->labels->name);?></option>
                     <?php if(!empty($templatic_settings['sorting_option']) && in_array('title_alphabetical',$templatic_settings['sorting_option'])):?>
                    <option value="alphabetical" <?php echo ((isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby']=="alphabetical") || (isset($_SESSION['alphabetical']) && $_SESSION['alphabetical']==1))? 'selected':'';?>><?php _e('Alphabetical',DOMAIN);?></option>
                    <?php endif;?>
                    <?php if(!empty($templatic_settings['sorting_option']) && in_array('title_asc',$templatic_settings['sorting_option'])):?>
                    <option value="title_asc" <?php echo (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby']=="title_asc")? 'selected':'';?>><?php _e('Title Ascending',DOMAIN);?></option>
                    <?php endif;?>
                    <?php if(!empty($templatic_settings['sorting_option']) && in_array('title_desc',$templatic_settings['sorting_option'])):?>
                    <option value="title_desc" <?php echo (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby']=="title_desc")? 'selected':'';?>><?php _e('Title Descending',DOMAIN);?></option>
                    <?php endif;?>
                    
                    <?php if(!empty($templatic_settings['sorting_option']) && in_array('date_asc',$templatic_settings['sorting_option'])):?>
                    <option value="date_asc" <?php echo (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby']=="date_asc")? 'selected':'';?>><?php _e('Publish Date Ascending',DOMAIN);?></option>
                    <?php endif;?>
                    
                    <?php if(!empty($templatic_settings['sorting_option']) && in_array('date_desc',$templatic_settings['sorting_option'])):?>
                    <option value="date_desc" <?php echo (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby']=="date_desc")? 'selected':'';?>><?php _e('Publish Date Descending',DOMAIN);?></option>
                    <?php endif;?>
                    
                    <?php if(get_post_type()=='event'):?>
                    	<?php if(!empty($templatic_settings['sorting_option']) && in_array('stdate_low_high',$templatic_settings['sorting_option'])):?>
                         <option value="stdate_low_high" <?php echo (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby']=="stdate_low_high")? 'selected':'';?>><?php _e('Start Date low to high',DOMAIN);?></option>
                         <?php endif;?>
                         
                    	<?php if(!empty($templatic_settings['sorting_option']) && in_array('stdate_high_low',$templatic_settings['sorting_option'])):?>     
                         <option value="stdate_high_low" <?php echo (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby']=="stdate_high_low")? 'selected':'';?>><?php _e('Start Date high to low',DOMAIN);?></option>
                         <?php endif;?>
                    <?php endif;?>
                    <?php do_action('sorting_option',get_post_type());?>
                    
                    <?php if(!empty($templatic_settings['sorting_option']) && in_array('reviews',$templatic_settings['sorting_option'])):?>
                     <option value="reviews" <?php echo (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby']=="reviews")? 'selected':'';?>><?php _e('Reviews',DOMAIN);?></option>
                    <?php endif;?>
                    
                    <?php if(!empty($templatic_settings['sorting_option']) && in_array('rating',$templatic_settings['sorting_option'])):?>
                     <option value="rating" <?php echo (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby']=="rating")? 'selected':'';?>><?php _e('Rating',DOMAIN);?></option>
                    <?php endif;?>
                    
                    <?php if(!empty($templatic_settings['sorting_option']) && in_array('random',$templatic_settings['sorting_option'])):?>
                     <option value="random" <?php echo (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby']=="random")? 'selected':'';?>><?php _e('Random',DOMAIN);?></option>
                    <?php endif;?>
               </select>
          </form>
		  <script type="text/javascript">
				function sort_as_set(val)
				{
					if(document.getElementById('directory_sortby').value)
					{
						<?php if(strstr(directory_full_url(),'?')): ?>
							window.location = '<?php echo directory_full_url(); ?>'+'&directory_sortby='+val;
						<?php else: ?>
							window.location = '<?php echo directory_full_url(); ?>'+'?directory_sortby='+val;
						<?php endif; ?>
					}
				}
			</script>
     <?php
		}
	}
	?>
     	</div><!--END sort_options div -->
     </div><!-- END directory_manager_tab Div -->
    
    <?php if($templatic_settings['sorting_type']=='normal' && !empty($templatic_settings['sorting_option'])){?>
    <div class="normal_sorting_option">
    	<ul class="sorting_option">
		     <?php if(!empty($templatic_settings['sorting_option']) && in_array('title_alphabetical',$templatic_settings['sorting_option'])):?>
          	<li><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical'; ?>"><?php _e('Alphabetical',DOMAIN);?></a></li>
               <?php endif;?>
          	<?php if(!empty($templatic_settings['sorting_option']) && in_array('title_asc',$templatic_settings['sorting_option'])):?>
          	<li><a href="<?php echo $permalink.$url_glue . 'directory_sortby=title_asc'; ?>"><?php _e('Title Ascending',DOMAIN);?></a></li>
               <?php endif;?>
               
               <?php if(!empty($templatic_settings['sorting_option']) && in_array('title_desc',$templatic_settings['sorting_option'])):?>
			<li><a href="<?php echo $permalink.$url_glue . 'directory_sortby=title_desc'; ?>"><?php _e('Title Descending',DOMAIN);?></a></li>
               <?php endif;?>
               
               <?php if(!empty($templatic_settings['sorting_option']) && in_array('date_asc',$templatic_settings['sorting_option'])):?>
               <li><a href="<?php echo $permalink.$url_glue . 'directory_sortby=date_asc'; ?>"><?php _e('Publish Date Ascending',DOMAIN);?></a></li>
               <?php endif;?>
               
               <?php if(!empty($templatic_settings['sorting_option']) && in_array('date_desc',$templatic_settings['sorting_option'])):?>
               <li><a href="<?php echo $permalink.$url_glue . 'directory_sortby=date_desc'; ?>"><?php _e('Publish Date Descending',DOMAIN);?></a></li>
               <?php endif;?>
               
               <?php if(get_post_type()=='event'):?>
				<?php if(!empty($templatic_settings['sorting_option']) && in_array('stdate_low_high',$templatic_settings['sorting_option'])):?>
                    <li><a href="<?php echo $permalink.$url_glue . 'directory_sortby=stdate_low_high'; ?>"><?php _e('Start Date low to high',DOMAIN);?></a></li>
                    <?php endif;?>
                    
                    <?php if(!empty($templatic_settings['sorting_option']) && in_array('stdate_high_low',$templatic_settings['sorting_option'])):?>
                    <li><a href="<?php echo $permalink.$url_glue . 'directory_sortby=stdate_high_low'; ?>"><?php _e('Start Date high to low',DOMAIN);?></a></li>
                    <?php endif;?>
               <?php endif;?>
               
               <?php if(!empty($templatic_settings['sorting_option']) && in_array('reviews',$templatic_settings['sorting_option'])):?>
               <li><a href="<?php echo $permalink.$url_glue . 'directory_sortby=reviews'; ?>"><?php _e('Reviews',DOMAIN);?></a></li>
               <?php endif;?> 
               
               <?php if(!empty($templatic_settings['sorting_option']) && in_array('rating',$templatic_settings['sorting_option'])):?>
               <li><a href="<?php echo $permalink.$url_glue . 'directory_sortby=rating'; ?>"><?php _e('Rating',DOMAIN);?></a></li>
               <?php endif;?> 
               <?php if(!empty($templatic_settings['sorting_option']) && in_array('random',$templatic_settings['sorting_option'])):?>
               <li><a href="<?php echo $permalink.$url_glue . 'directory_sortby=random'; ?>"><?php _e('Random',DOMAIN);?></a></li>
               <?php endif;?>             
          </ul>
    </div>
    <?php }?> 
     
    <?php if((isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby']=='alphabetical') || (isset($_SESSION['alphabetical']) && $_SESSION['alphabetical']==1)):?>
    <div id="directory_sort_order_alphabetical" class="sort_order_alphabetical">
	    <ul>
			<li class="<?php echo (!isset($_REQUEST['sortby']))?'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical';?>"><?php _e('All',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='a')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=a';?>"><?php _e('A',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='b')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=b';?>"><?php _e('B',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='c')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=c';?>"><?php _e('C',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='d')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=d';?>"><?php _e('D',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='e')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=e';?>"><?php _e('E',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='f')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=f';?>"><?php _e('F',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='g')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=g';?>"><?php _e('G',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='h')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=h';?>"><?php _e('H',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='i')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=i';?>"><?php _e('I',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='j')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=j';?>"><?php _e('J',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='k')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=k';?>"><?php _e('K',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='l')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=l';?>"><?php _e('L',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='m')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=m';?>"><?php _e('M',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='n')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=n';?>"><?php _e('N',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='o')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=o';?>"><?php _e('O',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='p')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=p';?>"><?php _e('P',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='q')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=q';?>"><?php _e('Q',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='r')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=r';?>"><?php _e('R',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='s')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=s';?>"><?php _e('S',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='t')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=t';?>"><?php _e('T',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='u')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=u';?>"><?php _e('U',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='v')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=v';?>"><?php _e('V',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='w')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=w';?>"><?php _e('W',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='x')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=x';?>"><?php _e('X',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='y')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=y';?>"><?php _e('Y',DOMAIN);?></a></li>
			<li class="<?php echo (isset($_REQUEST['sortby']) && $_REQUEST['sortby']=='z')? 'active':''?>"><a href="<?php echo $permalink.$url_glue . 'directory_sortby=alphabetical&sortby=z';?>"><?php _e('Z',DOMAIN);?></a></li>
	    </ul>
    </div>
    <?php endif;
}
/*
 * Function Name: directory_category_page_image
 * Return: display the listing image 
 */
function directory_category_page_image(){
	global $post,$wpdb,$wp_query;
	
	$featured=get_post_meta(get_the_ID(),'featured_c',true);
	$featured=($featured=='c')?'featured_c':'';
	
	if(isset($_REQUEST['sort']) && $_REQUEST['sort'] =='favourite'){
		$post_type_tag = "-".$post->post_type;
	}else{
		$post_type_tag = '';
	}
	 if ( has_post_thumbnail()):
		echo '<a href="'.get_permalink().'" class="listing_img">';
		if($featured){echo '<span class="featured_tag">'.__('Featured',DOMAIN).$post_type_tag.'</span>';}
		the_post_thumbnail('directory-listing-image'); 
		echo '</a>';
	else:
		if(function_exists('bdw_get_images_plugin'))
		{
			$post_img = bdw_get_images_plugin(get_the_ID(),'directory-listing-image');						
			$thumb_img = $post_img[0]['file'];
			$attachment_id = $post_img[0]['id'];
			$attach_data = get_post($attachment_id);
			$img_title = $attach_data->post_title;
			$img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
		}
		?>
	    <a href="<?php the_permalink();?>" class="listing_img">
       <?php if($featured){echo '<span class="featured_tag">'.__('Featured',DOMAIN).$post_type_tag.'</span>';}?>
	    <?php if($thumb_img):?>
		    <img src="<?php echo $thumb_img; ?>"  alt="<?php echo $img_alt; ?>" title="<?php echo $img_title; ?>" />
	    <?php else:?>    
			<img src="<?php echo TEVOLUTION_DIRECTORY_URL; ?>images/noimage-220x150.jpg" alt="" />
	    <?php endif;?>
	    </a>	
   <?php endif;
}
/*
 * Function Name: directory_listing_after_title
 * Return: display the listing rating, listing address and contact info after listing title
 */
function directory_listing_after_title(){
	global $post,$htmlvar_name,$posttitle,$wp_query;	
	
	$is_archive = get_query_var('is_ajax_archive');
	
	if((is_archive() || $is_archive == 1) && $post->post_type ==CUSTOM_POST_TYPE_LISTING && !is_search()){
		$post_id=get_the_ID();		
		$tmpdata = get_option('templatic_settings');
		
		$address=get_post_meta($post->ID,'address',true);
		$phone=get_post_meta($post->ID,'phone',true);
		$time=get_post_meta($post->ID,'listing_timing',true);
		echo ($htmlvar_name['Contact Information']['phone'] && $phone)? '<p class="phone">'.$phone.'</p>' : '';
		echo ($htmlvar_name['[#taxonomy_name#]']['address'] && $address)? '<p class="address">'.$address.'</p>' : '';	
		echo ($htmlvar_name['[#taxonomy_name#]']['listing_timing'] && $time)? '<p class="time">'.$time.'</p>' : '';	
		
		if((!empty($htmlvar_name['Contact Information'])) && ($htmlvar_name['Contact Information']['twitter']  || $htmlvar_name['Contact Information']['facebook'] || $htmlvar_name['Contact Information']['google_plus']))
		{
			$twitter=get_post_meta($post->ID,'twitter',true);
			$facebook=get_post_meta($post->ID,'facebook',true);
			$google_plus=get_post_meta($post->ID,'google_plus',true);
			echo "<div class='social_wrapper'>";
				
			if($twitter != '' && $htmlvar_name['Contact Information']['twitter'])
			{
			?>
				<a class='twitter <?php echo $htmlvar_name['Contact Information']['twitter']['style_class']; ?>' href="<?php echo $twitter;?>"><label><?php _e('twitter',DOMAIN); ?></label></a>
			<?php
			}
			if($facebook != '' && $htmlvar_name['Contact Information']['facebook'])
			{
			?>
				<a class='facebook <?php echo $htmlvar_name['Contact Information']['facebook']['style_class']; ?>' href="<?php echo $facebook;?>"><label><?php _e('facebook',DOMAIN); ?></label></a>
			<?php
			}
			if($google_plus != '' && $htmlvar_name['Contact Information']['google_plus'])
			{
			?>
				<a class='google_plus <?php echo $htmlvar_name['Contact Information']['google_plus']['style_class']; ?>' href="<?php echo $google_plus;?>"><label><?php _e('Google+',DOMAIN); ?></label></a>
			<?php
			}
			echo "</div>";
		}
		$j=0;
		if(!empty($htmlvar_name)){
			foreach($htmlvar_name as $key=>$value){
				$i=0;
				foreach($value as $k=>$val){
					$key = ($key=='[#taxonomy_name#]')?'Listing Information': $key;
					if($k!='post_title' && $k!='post_content' && $k!='post_excerpt' && $k!='post_images' && $k!='listing_timing' && $k!='address' && $k!='listing_logo' && $k!='post_tags' && $k!='map_view'  && $k!='phone' && $k!='twitter' && $k!='facebook' && $k!='google_plus' && $k!='contact_info')
					{
						
						
						$field= get_post_meta(get_the_ID(),$k,true);				
						if($val['type'] == 'multicheckbox' && $field!=""):						
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
						<p class='<?php echo $k;?>'><label><?php echo $val['label']; ?></label> : <?php echo substr($checkbox_value,0,-1);?></p>
						<?php 
						elseif($val['type']=='radio'):
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
						
						if($val['type'] != 'multicheckbox' && $val['type'] != 'radio' &&$field!=''):?>                              
						<p class='<?php echo $val['style_class'];?>'><label><?php echo $val['label']; ?></label> : <?php echo $field;?></p>
						<?php
						endif;
					
					}// End If condition
					
					$j++;
				}// End second foreach
			}//foreach loop
		}//htmlvar_name if condition
		
	}
	
	if(is_search() && $post->post_type ==CUSTOM_POST_TYPE_LISTING){
		$address=get_post_meta($post->ID,'address',true);
		$phone=get_post_meta($post->ID,'phone',true);
		$time=get_post_meta($post->ID,'listing_timing',true);
		echo ($phone)? '<p class="phone">'.$phone.'</p>' : '';
		echo '<p class="address">'.$address.'</p>';
		echo ($time)? '<p class="time">'.$time.'</p>' : '';	
	}
}
add_action('home_featured_after_title','home_featured_after_title');
function home_featured_after_title($instance)
{
	
	global $post,$htmlvar_name,$posttitle,$wp_query;
	$my_post_type = empty($instance['post_type']) ? 'listing' : $instance['post_type'];
	
	
	$is_archive = get_query_var('is_ajax_archive');
	
	$post_id=get_the_ID();
	$post_id=get_the_ID();
	$tmpdata = get_option('templatic_settings');
	
		
	if(!empty($htmlvar_name['Contact Information']) && ($htmlvar_name['Contact Information']['twitter']  || $htmlvar_name['Contact Information']['facebook'] || $htmlvar_name['Contact Information']['google_plus']))
	{
		$twitter=get_post_meta($post->ID,'twitter',true);
		$facebook=get_post_meta($post->ID,'facebook',true);
		$google_plus=get_post_meta($post->ID,'google_plus',true);
		echo "<div class='social_wrapper'>";
			
		if($twitter != '' && $htmlvar_name['Contact Information']['twitter'])
		{
		?>
			<a class='twitter' href="<?php echo $twitter;?>"><label><?php _e('twitter',DOMAIN); ?></label></a>
		<?php
		}
		if($facebook != '' && $htmlvar_name['Contact Information']['facebook'])
		{
		?>
			<a class='facebook' href="<?php echo $facebook;?>"><label><?php _e('facebook',DOMAIN); ?></label></a>
		<?php
		}
		if($google_plus != '' && $htmlvar_name['Contact Information']['google_plus'])
		{
		?>
			<a class='google_plus' href="<?php echo $google_plus;?>"><label><?php _e('Google+',DOMAIN); ?></label></a>
		<?php
		}
		echo "</div>";
	}
	$j=0;
	if(!empty($htmlvar_name)){		
		foreach($htmlvar_name as $key=>$value){
			$i=0;
			if(!empty($value)){
				foreach($value as $k=>$val){
				
					$key = ($key=='[#taxonomy_name#]')?'Listing Information': $key;
					if($k =='post_content')
					{ 
						$read_more = empty($instance['read_more']) ? '' : $instance['read_more'];
					?>
						<div class='<?php echo $my_post_type?>_content' itemprop="description">
							 <?php directory_content_limit( (int)$instance['content_limit'] ); ?>                                   
							<?php if($read_more) { ?>
								<span class="view_more"><a href="<?php echo get_permalink($post->post_id); ?>"><?php echo $read_more;?></a></span><?php }
							?>
					    </div>
							<?php
					}
					if($k!='post_title'   && $k!='post_excerpt' && $k!='post_images' && $k!='st_time' && $k!='end_date' && $k!='st_date' && $k!='end_time' && $k!='address' && $k!='phone' && $k != 'twitter' && $k != 'facebook' && $k != 'google_plus' && $k != 'listing_timing')
					{						
						//if($i==0){echo '<h4 class="custom_field_headding">'.$key.'</h4>';}
						$field= get_post_meta($post->ID,$k,true);
						if($val['type'] == 'multicheckbox' && $field!=""): ?>
						<p class='<?php echo $k;?>'><label><?php echo $val['label']; ?></label>: <?php echo implode(",",$field); ?></p>
						<?php endif;
						if($val['type'] != 'multicheckbox' && $field!=''):
							
							?>                             
								<p class='<?php echo $k;?>'><label><?php echo $val['label']; ?></label>: <?php echo $field;?></p>
							<?php
							
						endif;
					}
					$i++;
					$j++;
				}// Foreach
			}// value if condition
		}//foreach loop
	}//htmlvar_name if condition
}
add_action('directory_the_comment','directory_the_comment');
function directory_the_comment()
{
	$number = doubleval( get_comments_number(get_the_ID()) );
	$comments_link = '';
	if($number <=0){
		$review = apply_filters('tev_review_text','review');
	}else{
		$review = apply_filters('tev_review_text','reviews');
	}
	if ( 0 == $number )		
		$comments_link = '<span class="comment"><a href="' . get_permalink() . '#respond" title="' . sprintf( esc_attr__( 'review on %1$s', DOMAIN ), the_title_attribute( 'echo=0' ) ) . '">' . sprintf( 0, number_format_i18n( $number ) ).' '.sprintf( esc_attr__($review) ) . '</a></span>  ';
	elseif ( 1 == $number )
		$comments_link = '<span class="comment"><a href="' . get_comments_link() . '" title="' . sprintf( esc_attr__( 'review on %1$s', DOMAIN ), the_title_attribute( 'echo=0' ) ) . '">' . sprintf( 1, number_format_i18n( $number ) ).' '.sprintf( esc_attr__($review) ) . '</a></span>  ';
	elseif ( 1 < $number )
		$comments_link = '<span class="comment"><a href="' . get_comments_link() . '" title="' . sprintf( esc_attr__( 'review on %1$s', DOMAIN ), the_title_attribute( 'echo=0' ) ) . '">' . sprintf( '%s', number_format_i18n( $number ) ).' '.sprintf( esc_attr__($review) ) . '</a></span>  ';
	echo $comments_link;
}
add_action('directory_after_loop_taxonomy','directory_categories_google_map');
add_action('directory_after_loop_archive','directory_categories_google_map');
add_action('directory_before_loop_search','directory_categories_google_map');
function directory_categories_google_map(){
	global $current_cityinfo,$wp_query;
	$heigh =apply_filters('directory_google_map_heigh', '500');
	$templatic_settings=get_option('templatic_settings');
	$taxonomy= get_query_var( 'taxonomy' );
	$slug=get_query_var( get_query_var( 'taxonomy' ) );
	$term=get_term_by( 'slug',$slug , $taxonomy ) ;
	if($term):
		$term_icon=$term->term_icon;
	else:
		$term_icon='';
	endif;
	
	
	if(!isset($term_icon) || $term_icon==''){
		$term_icon = TEVOLUTION_DIRECTORY_URL.'images/pin.png'; }
	/*Get the directory listing page map settings */
	$templatic_settings=get_option('templatic_settings');
	$googlemap_setting=get_option('city_googlemap_setting');
	$current_term = $wp_query->get_queried_object();
	
	if($templatic_settings['category_map']=='yes' && $googlemap_setting['category_googlemap_widget']=='yes' && get_post_type()!='' && !is_search()){
		
		if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
		{
			add_filter('posts_where', 'location_multicity_where');
		}
		
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
		$query = new WP_Query( $args );		
	}else{
		$query = $wp_query;
	}	
	
	$cat_name = single_cat_title('',false);		
	$srcharr = array("'");
	$replarr = array("\'");
	if ($query ->have_posts() && $googlemap_setting['category_googlemap_widget']=='yes'): 
		while ($query ->have_posts()) : $query ->the_post(); 
			global $post;
			$ID = get_the_ID();
			$title = get_the_title(get_the_ID());
			$marker_title = str_replace("'","\'",$post->post_title);
			$plink = get_permalink(get_the_ID());
			$lat = get_post_meta(get_the_ID(),'geo_latitude',true);
			$lng = get_post_meta(get_the_ID(),'geo_longitude',true);					
			$address = str_replace($srcharr,$replarr,get_post_meta(get_the_ID(),'address',true));
			$contact = str_replace($srcharr,$replarr,(get_post_meta(get_the_ID(),'phone',true)));
			$website = get_post_meta(get_the_ID(),'website',true);
			
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
			if ( has_post_thumbnail()){
				$post_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail');						
				$post_images= @$post_img[0];
			}else{
				$post_img = bdw_get_images_plugin(get_the_ID(),'thumbnail');					
				$post_images = @$post_img[0]['file'];
			}
			if($post_images)
				$post_image='<div class=map-item-img><img width="150" height="150" class="map_image" src="'.$post_images.'" /></div>';
			else
				$post_image='';
			
			$image_class=($post_image)?'map-image' :'';
			$comment_count= count(get_comments(array('post_id' => $ID)));
			$review=($comment_count <=1 )? __('review',DOMAIN):__('reviews',DOMAIN);
			if($lat && $lng)
			{ 
				$retstr ="{";
				$retstr .= "'name':'$marker_title',";
				$retstr .= "'location': [$lat,$lng],";
				$retstr .= "'message':'<div class=\"google-map-info $image_class forrent\"><div class=\"map-inner-wrapper\"><div class=\"map-item-info\">$post_image";
				$retstr .= "<h6><a href=\"$plink\" class=\"ptitle\" ><span>$title</span></a></h6>";				
				if($address){$retstr .= "<p class=address>$address</p>";}				
				if($timing){$retstr .= "<p class=timing style=\"font-size:10px;\">$timing</p>";}
				if($contact){$retstr .= "<p class=contact style=\"font-size:10px;\">$contact</p>";}
				if($website){$retstr .= '<p class=website><a href= \"'.$website.'\">'.$website.'</a></p>';}
				if($templatic_settings['templatin_rating']=='yes'){
					$rating=draw_rating_star_plugin(get_post_average_rating(get_the_ID()));
					$retstr .= "<p class=\"map_rating\">$rating</p>";
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
		
		endwhile;
		if($content_data)	
			$catinfo_arr= "'$term->name':[".implode(',',$content_data)."]";		
		wp_reset_query();
		wp_reset_postdata();
		
	/* $current_cityinfo variable not set or empty then set the city wise google map setting */	
	if(!isset($current_cityinfo) || empty($current_cityinfo)){
		$city_map_setting=get_option('city_googlemap_setting');
		$current_cityinfo=array(
						    'map_type'=>$city_map_setting['map_city_type'],
						    'lat'     =>$city_map_setting['map_city_latitude'],
						    'lng'     =>$city_map_setting['map_city_longitude'],
						    'is_zoom_home' =>$city_map_setting['set_zooming_opt'],
						    'scall_factor' =>$city_map_setting['map_city_scaling_factor'],
						    );
	}
	$maptype=($current_cityinfo['map_type'] != '')? $current_cityinfo['map_type']: 'ROADMAP';
	
	$latitude    = $current_cityinfo['lat'];
	$longitude   = $current_cityinfo['lng'];
	$map_type    = $current_cityinfo['map_type'];
	$map_display = $current_cityinfo['is_zoom_home'];
	$zoom_level  = $current_cityinfo['scall_factor'];	
	
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
		
		//var markers = '';
		var map = null;
		var markerArray = [];
		var mgr = null;
		var mc = null;
		var markerClusterer;
		var mClusterer = null;
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
			  map.fitBounds(bounds);
			  var center = bounds.getCenter();	
			  map.setCenter(center);
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
	<div id="directory_listing_map" class="listing_map" style="<?php if($templatic_settings['default_page_view']=='mapview'){echo 'visibility: visible; height: auto;';}else{echo 'visibility:hidden; height:0;';} ?>">
		<div class="map_sidebar_listing">
		<div class="top_banner_section_in clearfix">
			<div class="TopLeft"><span id="triggermap"></span></div>
			<div class="TopRight"></div>
			<div class="iprelative">
			<div id="map_canvas" style="width: 100%; height:<?php echo $heigh;?>px" class="map_canvas"></div>
               </div>
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
add_action('wp_head','directory_listing_remove_shortcode_p_tag');
function directory_listing_remove_shortcode_p_tag()
{
	if(is_page())
	{
		global $post;
		if($post->ID == get_option('woocommerce_cart_page_id') || $post->ID == get_option('woocommerce_checkout_page_id') || $post->ID == get_option('woocommerce_pay_page_id') || $post->ID == get_option('woocommerce_thanks_page_id') || $post->ID == get_option('woocommerce_myaccount_page_id') || $post->ID == get_option('woocommerce_edit_address_page_id') || $post->ID == get_option('woocommerce_view_order_page_id') || $post->ID == get_option('woocommerce_change_password_page_id') || $post->ID == get_option('woocommerce_logout_page_id') || $post->ID == get_option('woocommerce_lost_password_page_id') )
		{
			remove_filter( 'the_content', 'wpautop',12 );
		}
	}
}
add_action('directory_display_rating','directory_display_rating');
function directory_display_rating($post_id){
	
	if(is_plugin_active('Templatic-MultiRating/multiple_rating.php') && function_exists('get_single_page_average_rating_image'))
	{
		?>
           <div class="listing_rating">
			<div class="directory_rating_row"><span class="single_rating"> <?php echo get_single_page_average_rating_image($post_id);?> </span></div>
		</div>
          <?php
	}
}
/*
 * Function Name: directory_after_taxonomies_content
 * Return: display rating views, and other content
 */
function directory_after_taxonomies_content(){	
	global $post,$htmlvar_name,$templatic_settings;	
	$is_archive = get_query_var('is_ajax_archive');
	if((is_archive() || $is_archive == 1) && $post->post_type ==CUSTOM_POST_TYPE_LISTING){			
		echo '<div class="rev_pin">';
		echo '<ul>';
		$post_id=get_the_ID();
		$googlemap_setting=get_option('city_googlemap_setting');	
		$comment_count= count(get_comments(array('post_id' => $post_id)));
		$review=($comment_count <=1 )? __('review',DOMAIN):__('reviews',DOMAIN);
		$review=apply_filters('tev_review_text',$review);
		?>
          <?php if(current_theme_supports('tevolution_my_favourites') ):?> 
               <li><?php tevolution_favourite_html();?></li>
          <?php endif;?>               
		<li class="review"> <?php echo '<a href="'.get_permalink($post_id).'#comments">'.$comment_count.' '.$review.'</a>';?></li>
		<?php if(@$googlemap_setting['category_googlemap_widget']!='yes' && @$templatic_settings['pippoint_oncategory'] !=1):?> 
          	<li class='pinpoint'><a id="pinpoint_<?php echo $post_id;?>" class="ping" href="#map_canvas"><?php _e('Pinpoint',DOMAIN);?></a></li>               
		<?php endif;
		
		echo '</ul>';
		echo '</div>';
	}
}
function directory_listing_city_name()
{
	global $post;
	echo sprintf(__('We have found these results for listings matching your search criteria.',DOMAIN),$post->post_type); 
}
?>