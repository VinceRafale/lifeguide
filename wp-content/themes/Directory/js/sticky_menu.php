







<script>

$(document).ready(function() {
	alert('.');
});

</script>



<script type="text/javascript">/* sticky navigation for mobile view*/
jQuery.noConflict();jQuery(function(){var e=jQuery("#mobile_header");var t=jQuery("#mobile_header").css("display");var n=e.offset().top;var r=jQuery("#branding");var i=false;if(t=="block"){var s=jQuery(window);s.scroll(function(){var e=s.scrollTop();var t=e>n})}})
/* sticky navigation for desk top*/
// Stick the #nav to the top of the window
jQuery(document).ready(function(){jQuery(window).scroll(function(){var e=jQuery(window).scrollTop();

if(e>220){ 

	jQuery(".sticky_main").fadeIn(); 
	jQuery(".sticky_main").css('visibility','visible'); 
	
	
}else{ 

	jQuery(".sticky_main").fadeOut(); 
	jQuery(".sticky_main").css('visibility','hidden'); 

}})})
</script>