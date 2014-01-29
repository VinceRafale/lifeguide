/* Style File - jQuery plugin for styling file input elements
 * Copyright (c) 2007-2008 Mika Tuupola
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 * Based on work by Shaun Inman
 * http://www.shauninman.com/archive/2007/09/10/styling_file_inputs_with_css_and_the_dom
 * Revision: $Id: jquery.filestyle.js 303 2008-01-30 13:53:24Z tuupola $
 */
(function($) {
    $.fn.filestyle = function(options) {
        /* TODO: This should not override CSS. */
        return this.each(function() {
            var self = this;
			if(upload_single_title!=""){
				upload_single_title = upload_single_title;
			}else{
				upload_single_title = "Upload Image";
			}
			
			var wrapper = $('<div id="upload_button_secondary_btn" class="upload button secondary_btn"> <span class="upload_title">'+upload_single_title+'</span>').css({"display": "inline","position": "relative","overflow": "hidden"});
			var filename = $('<input type="hidden" class="file">').addClass($(self).attr("class")).css({"display": "inline"});
			
			$(self).before(filename);
			$(self).wrap(wrapper);
			$(self).css({"position": "absolute","display": "inline","cursor": "pointer","opacity": "0","left": "0","right": "0","top": "0","bottom": "0"});
			
			$('#upload_button_secondary_btn').after('<span class="file_value" style="display:inline-block;margin-left:5px"></span>');
			$(self).bind("change", function() {
				filename.val($(self).val());
				$('.file_value').html($(self).val().split('\\').pop());
            	});      
        });
    };
})(jQuery);
jQuery(document).ready(function() {
	jQuery("input[type=file]").filestyle();
}); 