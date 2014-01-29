/* Location Horizontal open close script */
jQuery(document).ready(function() {
	jQuery("#horizontal_show_togglebox_wrap").click(function(){
		jQuery("#horizontal_show_togglebox-button").toggleClass('horizontal_open');
		jQuery("#horizontal_show_togglebox_wrap").toggleClass('horizontal_toggle_open');
	});	
});
/* Location vertical open close script */
jQuery(document).ready(function() {
	jQuery("#show_togglebox_wrap").click(function(){
		jQuery("#show_togglebox-button").toggleClass('customizer_open');
	});	
});
/* Location navigation open close script */
jQuery(document).ready(function(){jQuery("#directorytab").click(function(){jQuery("#directory_location_navigation").toggleClass("horizontal_open");jQuery("#horizontal_show_togglebox-button").toggleClass('horizontal_open');jQuery("#directorytab").toggleClass("directorytab_open")})})
function set_default_city(str,city_id){
	
	var city_id=city_id;
	var city_name=jQuery('#'+str.id).attr( "class" );	
	jQuery.ajax({
		url:ajaxUrl,
		type:'POST',
		data:'action=default_city&city_id=' + city_id,
		success:function(results) {					
			jQuery('#city_default_'+city_id).css({'font-weight':'bold'}); 
			jQuery("#city_default_"+city_id).after('&nbsp;<span style="color:green;" class="default_city" id="set_default_city_'+city_id+'">Default City</span>');
			jQuery('#city_default_'+results).removeAttr('style');
			jQuery('#set_default_city_'+results ).remove();
			jQuery('#default_city_name').html('Default City ('+city_name+')');
		}
	});
	
}
function change_default_city_set(str){	
	if(str.value=='location_tracking'){
		jQuery('#location_tracking' ).show();
		jQuery('#default_city_set_msg' ).hide();
		jQuery('#nearest_city_set_msg' ).hide();
	}else if(str.value=='nearest_city'){
		jQuery('#location_tracking' ).hide();
		jQuery('#default_city_set_msg' ).hide();
		jQuery('#nearest_city_set_msg' ).show();
	}else{
		jQuery('#location_tracking' ).hide();
		jQuery('#nearest_city_set_msg' ).hide();
		jQuery('#default_city_set_msg' ).show();
	}
}
/* Start City background color picker */
function show_colorpicker(id)
{
	document.getElementsByName(id)[0].style.display = '';				
}
/*END City background color picker */
/* Fill Zone select box according to country */
function fill_zones_cmb(str,f){
	
	var country_id=str.value;
	var front='';
	if(f==1){
		front='&front=1';	
	}
	document.getElementById('process_state').style.display="";
	jQuery.ajax({
		url:ajaxUrl,
		type:'POST',
		data:'action=fill_states_cmb&country_id=' + country_id+front,
		success:function(results) {	
			document.getElementById('process_state').style.display="none";
			jQuery('#zones_id').html(results);
		}
	});
	
}
function fill_city_cmb(str){
	
	var state_id=str.value;	
	document.getElementById('process_city').style.display="";
	if(state_id==''){
		var country_id=document.getElementById('country_id');
		fill_multicity_cmb(country_id,'');
		
	}else{
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			data:'action=fill_city_cmb&state_id=' + state_id,
			success:function(results) {	
				document.getElementById('process_city').style.display="none";
				jQuery('#city_id').html(results);
			}
		});
		}
	
}
function fill_multicity_cmb(str,f){
	
	var country_id=str.value;
	var front='';
	if(f==1){
		front='&front=1';	
	}
	document.getElementById('process_city').style.display="";
	jQuery.ajax({
		url:ajaxUrl,
		type:'POST',
		data:'action=fill_multicity_cmb&country_id=' + country_id+front,
		success:function(results) {	
			document.getElementById('process_city').style.display="none";
			jQuery('#city_id').html(results);
		}
	});
	
}
jQuery(document).ready(function(){	
	jQuery("#header_country").change(function(){				
	    	var country_id = jQuery('#header_country').val();		
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			data:'action=fill_states_cmb&country_id=' + country_id+'&front=1&header=1',
			success:function(results) {			
				results=results.split('++');
				jQuery('#header_zone').html(results[0]);
				jQuery('#header_city').html(results[1]);
			}
		});
	});
});
jQuery(document).ready(function(){	
	jQuery("#header_zone").change(function(){				
	    	var state_id = jQuery('#header_zone').val();		
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			data:'action=fill_city_cmb&state_id=' + state_id+'&front=1',
			success:function(results) {				
				jQuery('#header_city').html(results);
			}
		});	
	});		
});
jQuery(document).ready(function(){	
	jQuery("#header_city").change(function(){					   
		jQuery('#multicity_form').submit();
	});		
});
/*Advance Search shortcode country wise field zone and city */
jQuery(document).ready(function(){	
	jQuery("#adv_country").change(function(){				
	    	var country_id = jQuery('#adv_country').val();		
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			data:'action=fill_states_cmb&country_id=' + country_id+'&front=1',
			success:function(results) {				
				jQuery('#adv_zone').html(results);
			}
		});	
	});		
});
jQuery(document).ready(function(){	
	jQuery("#adv_zone").change(function(){				
	    	var state_id = jQuery('#adv_zone').val();		
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			data:'action=fill_city_cmb&state_id=' + state_id,
			success:function(results) {				
				jQuery('#adv_city').html(results);
			}
		});	
	});		
});
/*Finish the advance search shortcode multicity */
/*Set Cookies Function */
function setCookie(name,value,days) {
   if (days) {
	  var date = new Date();
	  date.setTime(date.getTime()+(days*24*60*60*1000));
	  var expires = "; expires="+date.toGMTString();
   }
   else var expires = "";
   document.cookie = name+"="+value+expires+"; path=/";
}
function getCookie(name) {
   var nameEQ = name + "=";
   var ca = document.cookie.split(';');
   for(var i=0;i < ca.length;i++) {
	  var c = ca[i];
	  while (c.charAt(0)==' ') c = c.substring(1,c.length);
	  if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
   }
   return null;
}
function deleteCookie(name) {
   setCookie(name,"",-1);
}
function manage_city_validation(){
	jQuery.noConflict();
	var country_id = jQuery('#country_id').val();
	var zones_id = jQuery('#zones_id').val();
	var address = jQuery("#address").val();
	var check=0;
	jQuery("input[name='city_post_type[]']").each(function() {
		if (jQuery(this).attr('checked'))
		{
			check=1;
		}
	});
	
	var flg=0;
	if(country_id==''){
		jQuery('#admin_country_id').addClass('form-invalid');
		flg=1;
	}else{
		jQuery('#admin_country_id').removeClass('form-invalid');			
	}
	
	if(zones_id==''){
		jQuery('#admin_zones_id').addClass('form-invalid');
		flg=1;
	}else{
		jQuery('#admin_zones_id').removeClass('form-invalid');			
	}
	
	if(address==''){
		jQuery('#admin_city_name').addClass('form-invalid');
		flg=1;
	}else{
		jQuery('#admin_city_name').removeClass('form-invalid');			
	}
	
	if(check==0){
		jQuery('#admin_post_type').addClass('form-invalid');
		flg=1;
	}else{
		jQuery('#admin_post_type').removeClass('form-invalid');
	}
	
	if(flg==1){
		return false;	
	}else{
		return true;	
	}
}
function toggle_post_type(){
	var div1 = document.getElementById('toggle_postID');
	if (div1.style.display == 'none') {
		div1.style.display = 'block';
	} else {
		div1.style.display = 'none';
	}
	
	if(document.getElementById('toggle_post_type').getAttribute('class') == 'paf_row toggleoff'){		
		jQuery("#toggle_post_type").removeClass("paf_row toggleoff").addClass("paf_row toggleon");
	} else {		
		jQuery("#toggle_post_type").removeClass("paf_row toggleon").addClass("paf_row toggleoff");
	}
	
	if(document.getElementById('toggle_post_type').getAttribute('class').search('toggleoff')!=-1 && document.getElementById('toggle_post_type').getAttribute('class').search('map_category_fullscreen') !=-1){		
		jQuery("#toggle_post_type").removeClass("paf_row toggleoff map_category_fullscreen").addClass("paf_row toggleon map_category_fullscreen");
	} 
	if(document.getElementById('toggle_post_type').getAttribute('class').search('toggleon') !=-1 && document.getElementById('toggle_post_type').getAttribute('class').search('map_category_fullscreen') !=-1){
		jQuery("#toggle_post_type").removeClass("paf_row toggleon map_category_fullscreen").addClass("paf_row toggleoff map_category_fullscreen");
	}
}
jQuery(document).ready(function(){	
	jQuery("#directory_location_navigation #directorytab").click(function(){
		jQuery("#location_loading").show();
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',
			data:'action=tev_ajax_headerlocation',
			success:function(results) {	
				jQuery("#location_loading").hide();
				jQuery('#horizontal_header_location').html(results);
			}
		});	
	});		
});