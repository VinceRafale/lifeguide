<?php
global $wpdb,$post;
wp_reset_query();
$template_post_type = get_post_meta($post->ID,'submit_post_type',true);
$custom_post_types_args = array();  
	$custom_post_types = get_post_types($custom_post_types_args,'objects');
	
	global $post;
	
	foreach ($custom_post_types as $content_type){
	
		if($content_type->name == $template_post_type)
		{
			$taxonomy =  $content_type->slugs[0];
			if($content_type->name =='post' || strtolower($content_type->name) ==strtolower('posts')){ 
				$taxonomy='category';
			}
		}
	}
$catinfo = templ_get_parent_categories($taxonomy);
if(count($catinfo) == 0)
{
	echo '<span style="font-size:12px; color:red;">'.sprintf(__('You have not created any category for %s post type.So, this listing will be submited as uncategorized.',DOMAIN),$template_post_type).'</span>';
}
global $cat_array;
$total_cp_price = 0;
if(isset($_REQUEST['backandedit']) != '' || (isset($_REQUEST['pid']) && $_REQUEST['pid']!="") ){
	$place_cat_arr = $cat_array;
}
else
{
	for($i=0; $i < count($cat_array); $i++){
		$place_cat_arr[] = @$cat_array[$i]->term_taxonomy_id;
	}
}
$cat_display = "";
$tmpdata = get_option('templatic_settings');
if(isset($tmpdata['templatic-category_type']) && $tmpdata['templatic-category_type'] != "")
 {
	$cat_display = $tmpdata['templatic-category_type'];
 }
if(!$cat_display)
  {
	$cat_display = 'checkbox';
  }
/* Start of checkbox */
if($cat_display == 'checkbox')
{
	if($catinfo) {
		if($cat_display==''){ $cat_display='checkbox'; }
		$counter = 0;
		if(is_active_addons('monetization')){ 
			global $monetization;
			$total_price = $monetization->templ_total_price($taxonomy);
			$onclick = "onclick=displaychk();templ_all_categories($total_price);";
		}else{ $onclick = "onclick=displaychk()";}
		?>
          <div class="cf_checkbox">
		<div class="form_cat" >
		<label><input type="checkbox" name="selectall" id="selectall"  <?php echo $onclick; ?> /><?php _e('Select All',DOMAIN);?></label></div>
		<?php
		foreach($catinfo as $catinfo_obj)
		{
			$counter++;
			$termid = $catinfo_obj->term_taxonomy_id;
			$term_tax_id = $catinfo_obj->term_id;
			$name = $catinfo_obj->name;
			$cp = trim($catinfo_obj->term_price);
			if(!isset($cp) || $cp==''){ $cp = 0; }		
			$cat_term = explode(',',@$_REQUEST['category']);
			if(is_active_addons('monetization') && $cp !='0') { $dispay_price = " (".fetch_currency_with_position($cp).")"; }else{ $dispay_price = ''; } /* cat price works only with monetization */ ?>
			<div class="form_cat">
				<label>
				<input type="checkbox" name="category[]" id="category_<?php echo $counter; ?>" value="<?php echo $term_tax_id.",".$cp ; ?>" class="checkbox" <?php if(isset($place_cat_arr) && in_array($term_tax_id,$place_cat_arr)){ echo 'checked=checked'; }?> onclick="fetch_packages('<?php echo $term_tax_id; ?>',this.form,'<?php echo $cp; ?>');"/>&nbsp;<?php echo $name.$dispay_price; ?>&nbsp;
				</label>
			 </div>		
			<?php
			 $child = templ_get_child_categories($taxonomy,$term_tax_id); /* $termid = parent id */
			 $parent_id = $termid; 			 
			 $i=1;
			 $tmp_term_id=$term_tax_id;
			 foreach($child as $term)
			 { 			
				if($term){
				$child_termid = $term->term_taxonomy_id;
				$term_tax_id = $term->term_id;
				$name = $term->name;
				$child_cp = $term->term_price; 
				if(!$child_cp){ $child_cp =0; }
				if(is_active_addons('monetization') && $child_cp!='0') { $dispay_cprice = " (".fetch_currency_with_position($child_cp).")"; }else{ $dispay_cprice =''; } /* cat price works only with monetization */
				if($term->category_parent!=$parent_id)
				{	$i++;	$parent_id = $term->category_parent; }	
				if($term->category_parent!=0):
					$p=$i*17;								
					$pad = str_repeat('&nbsp;-', $p);						
					if($tmp_term_id==$term->category_parent)
					{
						$i=1;
						$p=$i*17;	
						$i++;
						$pad = str_repeat('&nbsp;-', $p);	
					}
			 ?>
				<div class="form_cat" style="margin-left:<?php echo $p; ?>px;">
					<label><input type="checkbox" name="category[]" id="category_<?php echo $counter; ?>" value="<?php if($child_cp != ""){ echo $term_tax_id.",".$child_cp; }else{ echo $term_tax_id.",".'0'; }?>" class="checkbox" <?php if(isset($place_cat_arr) && in_array($term_tax_id,$place_cat_arr)){echo 'checked="checked"'; }?>  onclick="fetch_packages('<?php echo $child_cp; ?>',this.form)"/>&nbsp;<?php if(@$child_cp != ""){ echo $name.$dispay_cprice; }else{ echo $name.$dispay_cprice; } ?>&nbsp;</label></div>
			<?php endif; }
			}
		}
		echo '</div>';// finish	cf_checkbox div
	}
}
/* End of checkbox */
/* Start of selectbox */
if($cat_display=='select' || $cat_display=='multiselectbox')
{ 
	$args = array('hierarchical' => true ,'hide_empty' => 0, 'orderby' => 'term_group');
	$terms = templ_get_parent_categories($taxonomy);
	
	if($terms) :
		if(is_active_addons('monetization')):
			if($cat_display=='select'):
				$fetch_pkg = "onchange=fetch_packages(this.value,this.form);"; /* FUNCTION FOR FETCH PACKAGES */
			else:
				$fetch_pkg = "onclick=fetch_packages(this.value,this.form);"; /* FUNCTION FOR FETCH PACKAGES */
			endif;
		else:
			$fetch_pkg = '';
		endif;
		
		if($cat_display == 'multiselectbox'){ $multiple =  "multiple=multiple"; }else{ $multiple=''; } /* multi select box */
		$output .= '<select name="category[]" id="select_category" '.$fetch_pkg.' '.$multiple.'>';
		
		$output .= '<option value="">'.__('Select Category',DOMAIN).'</option>';
		foreach($terms as $term){	
			$term_id = $term->term_id;
			$scp = $term->term_price;
			if($scp == ""){
				$scp = 0 ;
			}
			/* price will display only when monetization is activated */
			if(is_active_addons('monetization') && $scp!='0') { $sdisplay_price = " (".fetch_currency_with_position($scp).")"; }else{ $sdisplay_price =''; }
			$term_name = $term->name;
			if(isset($place_cat_arr) && in_array($term_id,$place_cat_arr)){ $selected = 'selected=selected'; }else{ $selected='';} /* category must be selected when gobackand edit /Edit/Renew */
			$output .= '<option value='.$term_id.','.$scp.' '.$selected.'>'.$term_name.$sdisplay_price.'</option>';
			
			$child_terms = templ_get_child_categories($taxonomy,$term_id);		/* get child categories term_id = parent id*/					
			$i=1;
			$parent_id = $term_id;
			$tmp_term_id=$term_id;
			foreach($child_terms as $child_term){ 
				$child_term_id = $child_term->term_id;
				$child_cp = $child_term->term_price;
				if($child_term->category_parent!=$parent_id)
				{	$i++;	$parent_id=$child_term->category_parent; }	
				if($child_term->category_parent!=0):
					$p=$i*2;								
					$pad = str_repeat('-', $p);	
					if($tmp_term_id==$child_term->category_parent)
					{
						$i=1;
						$p=$i*2;	
						$i++;
						$pad = str_repeat('-', $p);	
					}
					/* price will display only when monetization is activated */
					if(is_active_addons('monetization') && $child_cp!='0' ) { $cdisplay_price = " (".fetch_currency_with_position($child_cp).")"; }else{ $cdisplay_price =''; }
					$term_name = $child_term->name;
					if(isset($place_cat_arr) && in_array($child_term_id,$place_cat_arr)){ $cselected = 'selected=selected'; }else{ $cselected='';} /* category must be selected when gobackand edit /Edit/Renew */
					$pad = str_repeat('-', $p);	
					
					$output .= '<option value='.$child_term_id.','.$child_cp.' '.$cselected.'>'.$pad.$term_name.$cdisplay_price.'</option>';										
				endif;
            } //child category foreach loop
		}
		$output .= '</select>';
    echo $output;
	endif;
}
?>
<script type="text/javascript">
function displaychk(){
	dml=document.forms['submit_form'];
	chk = document.getElementsByName('category[]');
	len = chk.length;
	if(document.submit_form.selectall.checked == true) {
		for (i = 0; i < len; i++)
		chk[i].checked = true ;
	} else {
		for (i = 0; i < len; i++)
		chk[i].checked = false ;
	}
}
</script>