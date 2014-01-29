<?php
/*
name : register custom place post type 
description : Register place taxonomy.
*/
define('CUSTOM_POST_TYPE_LISTING','listing');
define('CUSTOM_CATEGORY_TYPE_LISTING','listingcategory');
define('CUSTOM_TAG_TYPE_LISTING','listingtags');
define('CUSTOM_MENU_TITLE_LISTING',__('Listings',DOMAIN));
define('CUSTOM_MENU_NAME_LISTING',__('Listings',DOMAIN));
define('CUSTOM_MENU_SIGULAR_NAME_LISTING',__('Listing',DOMAIN));
define('CUSTOM_MENU_ADD_NEW_LISTING',__('Add Listing',DOMAIN));
define('CUSTOM_MENU_ADD_NEW_ITEM_LISTING',__('Add new listing',DOMAIN));
define('CUSTOM_MENU_EDIT_LISTING',__('Edit',DOMAIN));
define('CUSTOM_MENU_EDIT_ITEM_LISTING',__('Edit listing',DOMAIN));
define('CUSTOM_MENU_NEW_LISTING',__('New listing',DOMAIN));
define('CUSTOM_MENU_VIEW_LISTING',__('View listing',DOMAIN));
define('CUSTOM_MENU_SEARCH_LISTING',__('Search listing',DOMAIN));
define('CUSTOM_MENU_NOT_FOUND_LISTING',__('No listing found',DOMAIN));
define('CUSTOM_MENU_NOT_FOUND_TRASH_LISTING',__('No listing found in trash',DOMAIN));
define('CUSTOM_MENU_CAT_LABEL_LISTING',__('Listing Categories',DOMAIN));
define('CUSTOM_MENU_CAT_TITLE_LISTING',__('Listing Categories',DOMAIN));
define('CUSTOM_MENU_SIGULAR_CAT_LISTING',__('Category',DOMAIN));
define('CUSTOM_MENU_CAT_SEARCH_LISTING',__('Search category',DOMAIN));
define('CUSTOM_MENU_CAT_POPULAR_LISTING',__('Popular categories',DOMAIN));
define('CUSTOM_MENU_CAT_ALL_LISTING',__('All categories',DOMAIN));
define('CUSTOM_MENU_CAT_PARENT_LISTING',__('Parent category',DOMAIN));
define('CUSTOM_MENU_CAT_PARENT_COL_LISTING',__('Parent category:',DOMAIN));
define('CUSTOM_MENU_CAT_EDIT_LISTING',__('Edit category',DOMAIN));
define('CUSTOM_MENU_CAT_UPDATE_LISTING',__('Update category',DOMAIN));
define('CUSTOM_MENU_CAT_ADDNEW_LISTING',__('Add new category',DOMAIN));
define('CUSTOM_MENU_CAT_NEW_NAME_LISTING',__('New category name',DOMAIN));
define('CUSTOM_MENU_TAG_LABEL_LISTING',__('Listing Tags',DOMAIN));
define('CUSTOM_MENU_TAG_TITLE_LISTING',__('Listing Tags',DOMAIN));
define('CUSTOM_MENU_TAG_NAME_LISTING',__('Listing Tags',DOMAIN));
define('CUSTOM_MENU_TAG_SEARCH_LISTING',__('Listing Tags',DOMAIN));
define('CUSTOM_MENU_TAG_POPULAR_LISTING',__('Popular listing tags',DOMAIN));
define('CUSTOM_MENU_TAG_ALL_LISTING',__('All listing tags',DOMAIN));
define('CUSTOM_MENU_TAG_PARENT_LISTING',__('Parent listing tags',DOMAIN));
define('CUSTOM_MENU_TAG_PARENT_COL_LISTING',__('Parent listing tags:',DOMAIN));
define('CUSTOM_MENU_TAG_EDIT_LISTING',__('Edit listing tags',DOMAIN));
define('CUSTOM_MENU_TAG_UPDATE_LISTING',__('Update listing tags',DOMAIN));
define('CUSTOM_MENU_TAG_ADD_NEW_LISTING',__('Add new listing tags',DOMAIN));
define('CUSTOM_MENU_TAG_NEW_ADD_LISTING',__('New listing tag name',DOMAIN));
add_action('admin_init','register_place_post_type');
function register_place_post_type()
{	
	include(TEVOLUTION_DIRECTORY_DIR.'listing/install.php');	
	
}
?>