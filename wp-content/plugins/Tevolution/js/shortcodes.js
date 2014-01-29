jQuery(document).ready(function($) {
    tinymce.create('tinymce.plugins.tevolution_shortcodes', {
        init : function(td, url) { var url = url;  },  
		createControl:function(d,t)
				{
					var td = tinymce.activeEditor;
					if(d=="tevolution_shortcodes"){
							var d = t.createSplitButton( "tevolution_shortcodes",{
								title: 'Tevolution shortcodes'
							});
							var a=this;
							d.onRenderMenu.add(function(d,b){
									
											b.add({title : 'Submit Form', onclick : function() {
												tinyMCE.activeEditor.execCommand('mceInsertContent',false,'[submit_form post_type="your post type slug"]');
											}});
											b.add({title : 'Registration Form', onclick : function() {
												tinyMCE.activeEditor.execCommand('mceInsertContent',false,'[tevolution_register]');
											}});
											
											b.add({title : 'Login Form', onclick : function() {
												tinyMCE.activeEditor.execCommand('mceInsertContent',false,'[tevolution_login]');
											}});
											
											b.add({title : 'Edit Profile Form/Page', onclick : function() {
												tinyMCE.activeEditor.execCommand('mceInsertContent',false,'[tevolution_profile]');
											}});
											
											b.add({title : 'Advance Search Form', onclick : function() {
												tinyMCE.activeEditor.execCommand('mceInsertContent',false,'[advance_search_page post_type="your post type slug"]');
											}});
																						
											b.add({title : 'User Listing', onclick : function() {
												tinyMCE.activeEditor.execCommand('mceInsertContent',false,'[tevolution_author_list role="" users_per_page=""]');
											}});
											
											b.add({title : 'All Taxonomies Map', onclick : function() {
												tinyMCE.activeEditor.execCommand('mceInsertContent',false,'[tevolution_listings_map post_type="your post types,your post type" image="thumbnail" latitude="" longitude="" map_type="ROADMAP" zoom_level="13"]');
											}});
											
											b.add({title : 'Map', onclick : function() {
												tinyMCE.activeEditor.execCommand('mceInsertContent',false,'[map_page post_type="your post type slug" image="thumbnail" latitude="" longitude="" map_type="ROADMAP" map_display="ROADMAP" zoom_level="13" height="500"]');
											}});
											
										
											
											 /*b.add({title : 'Nearest City Map', onclick : function() {
												tinyMCE.activeEditor.execCommand('mceInsertContent',false,'[CURRENTCITY-DIRECTORYMAP post_type="your post type slug" showfullmap="1" latitude="" map_type="ROADMAP" map_display="ROADMAP" width="" height="500" showclustering=""]');
											}});
											
											b.add({title : 'City Map', onclick : function() {
												tinyMCE.activeEditor.execCommand('mceInsertContent',false,'[TCITY-DIRECTORYMAP cityid="enter any city id" post_type="your post type slug" map_type="" showfullmap="1" latitude="" map_type="ROADMAP" map_display="ROADMAP" width="10%" height="500" showclustering=""]');
											}});*/
											
											});
						return d
					}
					return null
				}		
    });
    tinymce.PluginManager.add('tevolution_shortcodes', tinymce.plugins.tevolution_shortcodes);
});