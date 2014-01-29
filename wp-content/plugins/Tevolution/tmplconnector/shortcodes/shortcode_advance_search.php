<?php
/*
 * Function Name: tevolution_advance_search_page
 * Return: display the advance search form
 */
function tevolution_advance_search_page($atts){
	extract( shortcode_atts( array (
			'post_type'   =>'post',				
			), $atts ) 
		);	
	ob_start();
	global $wp_locale;
	/* include datepicker js file */
	wp_enqueue_script('jquery-ui-datepicker');	
		 //localize our js
		$aryArgs = array(
			'monthNames'        => strip_array_indices( $wp_locale->month ),
			'monthNamesShort'   => strip_array_indices( $wp_locale->month_abbrev ),
			'monthStatus'       => __( 'Show a different month', DOMAIN ),
			'dayNames'          => strip_array_indices( $wp_locale->weekday ),
			'dayNamesShort'     => strip_array_indices( $wp_locale->weekday_abbrev ),
			'dayNamesMin'       => strip_array_indices( $wp_locale->weekday_initial ),
			// is Right to left language? default is false
			'isRTL'             => $wp_locale->is_rtl,
		);
	 
		// Pass the array to the enqueued JS
	wp_localize_script( 'jquery-ui-datepicker', 'objectL11tmpl', $aryArgs );
	remove_filter( 'the_content', 'wpautop' , 12);
	
	?>
	<script>
	function set_adv_search()
	{
		if(document.getElementById('adv_s').value == '')
		{
			document.getElementById('adv_s').value = ' ';
		}
		return true;
	}
	</script>
     <form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="form_front_style">        
          <div class="form_row clearfix">               
               <input class="adv_input" name="s" id="adv_s" type="text" PLACEHOLDER="<?php _e('Search',DOMAIN); ?>" value="" />			  
               <span class="message_error2"  style="color:red;font-size:12px;" id="search_error"></span>			  
          </div>
          <!--Tags -->
          <div class="form_row clearfix">               
               <input class="adv_input" name="tag_s" id="tag_s" type="text"  PLACEHOLDER="<?php _e('Tags',DOMAIN); ?>" value=""  />			  
          </div>
          <!-- Post Type Castegory-->
          <div class="form_row clearfix">
          	<?php
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
			$categories = get_terms($taxonomies[0], 'orderby=count&hide_empty=0');
			?>               
               <select name="category">
                    <option value=""><?php _e("Select Category",DOMAIN);?></value>
                    <?php foreach($categories as $cat_informs){?>
                    <option value="<?php echo $cat_informs->term_id;?>"><?php echo $cat_informs->name;?></value>
                    <?php }?>		
               </select>
          </div>
          <div class="form_row clearfix">              
               <input name="articleauthor" type="text" PLACEHOLDER="<?php _e('Author',DOMAIN); ?>" />
               <label class="adv_author">
               <?php _e('Exact author',DOMAIN);?>
               <input name="exactyes" type="checkbox" value="1" class="checkbox" />	
               </label>
          </div>
          <?php 
		if(function_exists('get_search_post_fields_templ_plugin')){			
			$default_custom_metaboxes = get_search_post_fields_templ_plugin($post_type,'custom_fields','post');
			display_search_custom_post_field_plugin($default_custom_metaboxes,'custom_fields','post');//displaty custom fields html.
			}
		?>
          
          <input type="hidden" name="search_template" value="1"/>
          <!--<input class="adv_input" name="adv_search" id="adv_search" type="hidden" value="1"  />-->
          <input class="adv_input" name="post_type" id="post_type" type="hidden" value="<?php echo $post_type; ?>"  />
          <input type="submit" name="submit" value="<?php _e('Search',DOMAIN); ?>" class="adv_submit"  onclick="return set_adv_search();"/>              
     </form>
     <?php	
	return ob_get_clean();
}
?>