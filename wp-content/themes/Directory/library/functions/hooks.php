<?php
	add_action('init','supreme_init_filters');
	/* Add entry-specific filters. */
	function supreme_init_filters(){
		add_action( 'entry-title', 'supreme_entry_title' );
		add_action( 'entry-author', 'supreme_entry_author' );
		
		if(supreme_get_settings('enable_comments_on_post')){
			add_action( 'entry-comments-link', 'supreme_entry_comments_link' );
		}else{
			remove_action('entry-comments-link','supreme_entry_comments_link');
		}
		add_action( 'entry-published', 'supreme_entry_published' );
		add_action( 'entry-edit-link', 'supreme_entry_edit_link' );
		add_action( 'entry-permalink', 'supreme_entry_permalink' );
    }
/**
 * Displays the edit link for an individual post.
 *
 * @since 0.7.0
 * @access public
 * @param array $args
 * @return string
 */
function supreme_entry_edit_link( $args) {
	$post_type = get_post_type_object( get_post_type() );
	if ( !current_user_can( $post_type->cap->edit_post, get_the_ID() ) )
		return '';
	$args = wp_parse_args( array( 'before' => '', 'after' => ' ' ), $args );
	echo $args['before'] . '&nbsp;<span class="edit"><a class="post-edit-link" href="' . esc_url( get_edit_post_link( get_the_ID() ) ) . '" title="' . sprintf( esc_attr__( 'Edit %1$s', THEME_DOMAIN ), $post_type->labels->singular_name ) . '">' . __( 'Edit', THEME_DOMAIN ) . '</a></span>' . $args['after'];
}
/**
 Name:supreme_entry_published
 Description: Displays the published date of an individual post.
 */
function supreme_entry_published( $args ) {
	$args =  wp_parse_args( array( 'before' => __('On',THEME_DOMAIN)." ", 'after' => ' ', 'format' => get_option( 'date_format' ) ), $args );
	$published = '<abbr class="published" title="' . sprintf( get_the_time( esc_attr__( 'l, F jS, Y, g:i a', THEME_DOMAIN ) ) ) . '">' . sprintf( get_the_time( $args['format'] ) ) . '</abbr>';
	echo $args['before'] . $published . $args['after'];
}
/**
 * Displays a post's number of comments wrapped in a link to the comments area.
 */
function supreme_entry_comments_link( $args ) {
	$comments_link = '';
	$number = doubleval( get_comments_number() );
	$args = shortcode_atts( array( 'zero' => __( 'Leave a response', THEME_DOMAIN ), 'one' => apply_filters('comment_response_link',__( '%1$s Response', THEME_DOMAIN )), 'more' => __( '%1$s Responses', THEME_DOMAIN ), 'css_class' => 'comments-link', 'none' => '', 'before' => '', 'after' => '' ), $args );
	if ( 0 == $number && !comments_open() && !pings_open() ) {
		if ( $args['none'] )
			$comments_link = '<span class="' . esc_attr( $args['css_class'] ) . '">' . sprintf( $args['none'], number_format_i18n( $number ) ) . '</span>';
	}
	elseif ( 0 == $number )
		$comments_link = '<a class="' . esc_attr( $args['css_class'] ) . '" href="' . get_permalink() . '#respond" title="' . sprintf( esc_attr__( 'Comment on %1$s', THEME_DOMAIN ), the_title_attribute( 'echo=0' ) ) . '">' . sprintf( $args['zero'], number_format_i18n( $number ) ) . '</a> ';
	elseif ( 1 == $number )
		$comments_link = '<a class="' . esc_attr( $args['css_class'] ) . '" href="' . get_comments_link() . '" title="' . sprintf( esc_attr__( 'Comment on %1$s', THEME_DOMAIN ), the_title_attribute( 'echo=0' ) ) . '">' . sprintf( $args['one'], number_format_i18n( $number ) ) . '</a> ';
	elseif ( 1 < $number )
		$comments_link = '<a class="' . esc_attr( $args['css_class'] ) . '" href="' . get_comments_link() . '" title="' . sprintf( esc_attr__( 'Comment on %1$s', THEME_DOMAIN ), the_title_attribute( 'echo=0' ) ) . '">' . sprintf( $args['more'], number_format_i18n( $number ) ) . '</a> ';
	if ( $comments_link )
		$comments_link = $args['before'] . $comments_link . $args['after'];
	echo $comments_link;
}
/**
 * Displays an individual post's author with a link to his or her archive.
 */
function supreme_entry_author( $args ) {
	$args = wp_parse_args( array( 'before' => __( 'Published by', THEME_DOMAIN )." ", 'after' => ' ' ), $args );
	$author = '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a></span>';
	echo $args['before'] . $author . $args['after'];
}
/**
 * Displays a post's title with a link to the post.
 *
 * @since 0.7.0
 * @access public
 * @return string
 */
function supreme_entry_title( $args ) {
	global $post;
	$args = wp_parse_args( array( 'permalink' => true ), $args );
	$tag = is_singular() ? 'h1' : 'h2';
	$class = sanitize_html_class( get_post_type() ) . '-title entry-title';
	$title = the_title( "<{$tag} class='{$class}'><a href='" . get_permalink() . "'>", "</a></{$tag}>", false );
	if ( empty( $title ) && !is_singular() && $post->post_title !='')
		$title = "<{$tag} class='{$class}'><a href='" . get_permalink() . "'>" . __( '(Untitled)', THEME_DOMAIN ) . "</a></{$tag}>";
	if ( is_singular() && $post->post_title !='')
		$title = the_title("<{$tag} class='{$class}'>", "</{$tag}>", false );
	echo $title;
}
/**
 * Returns the output of the [entry-permalink] shortcode, which is a link back to the post permalink page.
 *
 * @since 1.3.0.
 * @param array $attr The shortcode arguments.
 * @return string A permalink back to the post.
 */
function supreme_entry_permalink( $args ) {
	$args = wp_parse_args( array( 'before' => '', 'after' => '' ), $args );
	echo $args['before'] . '<a href="' . esc_url( get_permalink() ) . '" class="permalink">' . __( 'Permalink', THEME_DOMAIN ) . '</a>' . $args['after'];
}
// Callouts
function st_callout( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'width' => '',
		'align' => ''
    ), $atts));
	$style;
	if ($width || $align) {
	 $style .= 'style="';
	 if ($width) $style .= 'width:'.$width.'px;';
	 if ($align == 'left' || 'right') $style .= 'float:'.$align.';';
	 if ($align == 'center') $style .= 'margin:0px auto;';
	 $style .= '"';
	}
   return '<div class="cta" '.$style.'>' . do_shortcode($content) . '</div><div class="clear"></div>';
}
add_shortcode('callout', 'st_callout');
// Buttons
function st_button( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'link' => '',
		'size' => 'medium',
		'color' => '',
		'target' => '_self',
		'caption' => '',
		'align' => 'right'
    ), $atts));	
	$button;
	$button .= '<div class="button '.$size.' '. $align.'">';
	$button .= '<a target="'.$target.'" class="button '.$color.'" href="'.$link.'">';
	$button .= $content;
	if ($caption != '') {
	$button .= '<br /><span class="btn_caption">'.$caption.'</span>';
	};
	$button .= '</a></div>';
	return $button;
}
add_shortcode('button', 'st_button');
// Tabs
add_shortcode( 'tabgroup', 'st_tabgroup' );
function st_tabgroup( $atts, $content ){
	
$GLOBALS['tab_count'] = 0;
do_shortcode( $content );
if( is_array( $GLOBALS['tabs'] ) ){
	
foreach( $GLOBALS['tabs'] as $tab ){
$tabs[] = '<li><a href="#'.$tab['id'].'">'.$tab['title'].'</a></li>';
$panes[] = '<li id="'.$tab['id'].'Tab">'.$tab['content'].'</li>';
}
$return = "\n".'<!-- the tabs --><ul class="tabs">'.implode( "\n", $tabs ).'</ul>'."\n".'<!-- tab "panes" --><ul class="tabs-content">'.implode( "\n", $panes ).'</ul>'."\n";
}
return $return;
}
add_shortcode( 'tab', 'st_tab' );
function st_tab( $atts, $content ){
extract(shortcode_atts(array(
	'title' => '%d',
	'id' => '%d'
), $atts));
$x = $GLOBALS['tab_count'];
$GLOBALS['tabs'][$x] = array(
	'title' => sprintf( $title, $GLOBALS['tab_count'] ),
	'content' =>  do_shortcode($content),
	'id' =>  $id );
$GLOBALS['tab_count']++;
}
// Toggle
function st_toggle( $atts, $content = null ) {
	extract(shortcode_atts(array(
		 'title' => '',
		 'style' => 'list'
    ), $atts));
	output;
	$output .= '<div class="'.$style.'"><p class="trigger"><a href="#">' .$title. '</a></p>';
	$output .= '<div class="toggle_container"><div class="block">';
	$output .= do_shortcode($content);
	$output .= '</div></div></div>';
	return $output;
	}
add_shortcode('toggle', 'st_toggle');
/*-----------------------------------------------------------------------------------*/
// Latest Posts
// Example Use: [latest excerpt="true" thumbs="true" width="50" height="50" num="5" cat="8,10,11"]
/*-----------------------------------------------------------------------------------*/
function st_latest($atts, $content = null) {
	extract(shortcode_atts(array(
	"offset" => '',
	"num" => '5',
	"thumbs" => 'false',
	"excerpt" => 'false',
	"length" => '50',
	"morelink" => '',
	"width" => '100',
	"height" => '100',
	"type" => 'post',
	"parent" => '',
	"cat" => '',
	"orderby" => 'date',
	"order" => 'ASC'
	), $atts));
	global $post;
	
	$do_not_duplicate[] = $post->ID;
	$args = array(
	  'post__not_in' => $do_not_duplicate,
		'cat' => $cat,
		'post_type' => $type,
		'post_parent'	=> $parent,
		'showposts' => $num,
		'orderby' => $orderby,
		'offset' => $offset,
		'order' => $order
		);
	// query
	$myposts = new WP_Query($args);
	
	// container
	$result='<div id="category-'.$cat.'" class="latestposts">';
	while($myposts->have_posts()) : $myposts->the_post();
		// item
		$result.='<div class="latest-item clearfix">';
		// title
		if ($excerpt == 'true') {
			$result.='<h4><a href="'.get_permalink().'">'.the_title("","",false).'</a></h4>';
		} else {
			$result.='<div class="latest-title"><a href="'.get_permalink().'">'.the_title("","",false).'</a></div>';			
		}
		
		
		// thumbnail
		if (has_post_thumbnail() && $thumbs == 'true') {
			$result.= '<img alt="'.get_the_title().'" class="alignleft latest-img" src="'.get_bloginfo('template_directory').'/thumb.php?src='.get_image_path().'&amp;h='.$height.'&amp;w='.$width.'"/>';
		}
		// excerpt		
		if ($excerpt == 'true') {
			// allowed tags in excerpts
			$allowed_tags = '<a>,<i>,<em>,<b>,<strong>,<ul>,<ol>,<li>,<blockquote>,<img>,<span>,<p>';
		 	// filter the content
			$text = preg_replace('/\[.*\]/', '', strip_tags(get_the_excerpt(), $allowed_tags));
			// remove the more-link
			$pattern = '/(<a.*?class="more-link"[^>]*>)(.*?)(<\/a>)/';
			// display the new excerpt
			$content = preg_replace($pattern,"", $text);
			$result.= '<div class="latest-excerpt">'.st_limit_words($content,$length).'</div>';
		}
		
		// excerpt		
		if ($morelink) {
			$result.= '<a class="more-link" href="'.get_permalink().'">'.$morelink.'</a>';
		}
		
		// item close
		$result.='</div>';
  
	endwhile;
		wp_reset_postdata();
	
	// container close
	$result.='</div>';
	return $result;
}
add_shortcode("latest", "st_latest");
// Example Use: [latest excerpt="true" thumbs="true" width="50" height="50" num="5" cat="8,10,11"]
/*-----------------------------------------------------------------------------------*/
// Creates an additional hook to limit the excerpt
/*-----------------------------------------------------------------------------------*/
function st_limit_words($string, $word_limit) {
	// creates an array of words from $string (this will be our excerpt)
	// explode divides the excerpt up by using a space character
	$words = explode(' ', $string);
	// this next bit chops the $words array and sticks it back together
	// starting at the first word '0' and ending at the $word_limit
	// the $word_limit which is passed in the function will be the number
	// of words we want to use
	// implode glues the chopped up array back together using a space character
	return implode(' ', array_slice($words, 0, $word_limit));
}
// Related Posts - [related_posts]
add_shortcode('related_posts', 'st_related_posts');
function st_related_posts( $atts ) {
	extract(shortcode_atts(array(
	    'limit' => '5',
	), $atts));
	global $wpdb, $post, $table_prefix;
	if ($post->ID) {
		$retval = '<div class="st_relatedposts">';
		$retval .= '<h4>Related Posts</h4>';
		$retval .= '<ul>';
 		// Get tags
		$tags = wp_get_post_tags($post->ID);
		$tagsarray = array();
		foreach ($tags as $tag) {
			$tagsarray[] = $tag->term_id;
		}
		$tagslist = implode(',', $tagsarray);
		// Do the query
		$q = "SELECT p.*, count(tr.object_id) as count
			FROM $wpdb->term_taxonomy AS tt, $wpdb->term_relationships AS tr, $wpdb->posts AS p WHERE tt.taxonomy ='post_tag' AND tt.term_taxonomy_id = tr.term_taxonomy_id AND tr.object_id  = p.ID AND tt.term_id IN ($tagslist) AND p.ID != $post->ID
				AND p.post_status = 'publish'
				AND p.post_date_gmt < NOW()
 			GROUP BY tr.object_id
			ORDER BY count DESC, p.post_date_gmt DESC
			LIMIT $limit;";
		$related = $wpdb->get_results($q);
 		if ( $related ) {
			foreach($related as $r) {
				$retval .= '<li><a title="'.wptexturize($r->post_title).'" href="'.get_permalink($r->ID).'">'.wptexturize($r->post_title).'</a></li>';
			}
		} else {
			$retval .= '
	<li>No related posts found</li>';
		}
		$retval .= '</ul>';
		$retval .= '</div>';
		return $retval;
	}
	return;
}
/*
Name :supreme_core_post_info
Description :detail  post information, return authorname ,published name, comments link ,edit link and permalink
*/
function supreme_core_post_info($post){
	echo '<div class="byline">';
		$theme_options = get_option(supreme_prefix().'_theme_settings');
		$display_author_name = $theme_options['display_author_name'];
		$display_response = $theme_options['display_post_response'];
		if($display_author_name){
			do_action('entry-author'); 
		}
		$theme_options = get_option(supreme_prefix().'_theme_settings');
		$display_publish_date = $theme_options['display_publish_date'];
		if($display_publish_date){
			do_action('entry-published');
		} 
		if($display_response){
			do_action('entry-comments-link');
		}
		 do_action('entry-edit-link'); 		
	echo '</div>';
	
}
/*
Name :supreme_front_post_info
Description :detail  post information, return authorname ,published name, comments link ,edit link and permalink for home page
*/
function supreme_front_post_info(){
	global $post;
	echo '		<div class="byline">';
		 if(supreme_get_settings( 'display_author_name' )){
			do_action('entry-author'); 
		}
		if(supreme_get_settings( 'display_publish_date' )){
			do_action('entry-published');
		} 
		 do_action('entry-comments-link');
		 do_action('entry-permalink');	
	echo '</div>';
	
}
/*
Name :supreme_single_post_info
Description :detail  post information, return authorname ,published name, comments link ,edit link and permalink for detail page
*/
add_action('supreme-single-post-info','supreme_single_post_info');
function supreme_single_post_info(){
	echo '		<div class="byline">';
	    do_action('entry-author'); 
		do_action('entry-published');
		do_action('entry-comments-link');
		do_action('entry-edit-link'); 	
		do_action('entry-extra-parameters'); 	
	
	echo '</div>';
	
}
/*
Name :supreme_gallery_post_info
Description :detail  post information, return authorname ,published name and permalink for detail page
*/
function supreme_gallery_post_info(){
		echo '		<div class="byline">';
		do_action('entry-author'); 
		do_action('entry-published');
		do_action('entry-permalink');	
		do_action('entry-edit-link');
		echo '</div>';	
}
/*
Name :supreme_status_post_info
Description :detail  post information, return authorname ,published name and permalink for listing page of post format status
*/
function supreme_content_format_post_info(){
	echo '	<div class="byline">';
		$display_author_name = supreme_get_settings( 'display_author_name' );
		$display_publish_date = supreme_get_settings( 'display_publish_date' );
		$display_post_response = supreme_get_settings( 'display_post_response' );
		if($display_author_name){
			do_action('entry-author'); 
		}
		if($display_publish_date){
			do_action('entry-published');
		}
		
		if($display_post_response){
			do_action('entry-comments-link');
		}
		
		do_action('entry-permalink');
		do_action('entry-edit-link');	
	echo '</div>';
	
}
?>