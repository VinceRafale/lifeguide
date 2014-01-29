<?php
/**
 * Comment Template
 *
 * The comment template displays an individual comment. This can be overwritten by templates specific
 * to the comment type (comment.php, comment-{$comment_type}.php, comment-pingback.php, 
 * comment-trackback.php) in a child theme.
 *
 * @package supreme
 * @subpackage Template
 */
	global $post, $comment;
	
	remove_action( 'comment_text', 'display_rating_star' );
	$tmpdata = get_option('templatic_settings');
	if($tmpdata['templatin_rating']=='yes'){
		add_action( 'comment_rate', 'display_rating_star' );
	}
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
  <?php do_action( 'before_comment' ); // supreme_before_comment ?>
  <div id="comment-<?php comment_ID(); ?>" class="comment-wrap">
    <?php do_action( 'open_comment' ); // supreme_open_comment ?>
    <div class="comment-header comment-author vcard"> 
    <?php $author = get_comment_author( $comment->comment_ID );
		$url = get_comment_author_url( $comment->comment_ID );
    		$author_id = $comment->user_id;		
    		if(get_user_meta($author_id,'profile_photo',true)){
			$size = ( ( $comment_list_args['avatar_size'] ) ? $comment_list_args['avatar_size'] : 80 );			
			echo '<a href="' . esc_url( $url ) . '" rel="external nofollow" title="' . esc_attr( $author ) . '"><img class="avatar avatar-'.absint( $size ).' photo" width="'.absint( $size ).'" height="'.absint( $size ).'" src="'.get_user_meta($author_id,'profile_photo',true).'" /></a>';
		}else{    			
    			echo supreme_avatar();
		}
		
		echo apply_atomic_shortcode( 'comment_meta', '<div class="comment-author">[comment-author]</div>' ); 			
		?>
    </div>
    <!-- .comment-meta -->
    <div class="comment-content comment">
      <?php if ( '0' == $comment->comment_approved ) : ?>
      <?php echo apply_atomic_shortcode( 'comment_moderation', '<p class="alert moderation">' . __( 'Your comment is awaiting moderation.', THEME_DOMAIN ) . '</p>' ); ?>
      <?php endif; ?>
      <?php comment_text( $comment->comment_ID );
			do_action( 'comment_rate');		  ?>
    </div>
    <!-- .comment-content -->
    <?php echo apply_atomic_shortcode( 'comment_meta', '<div class="comment-meta">[comment-published] <span class="comment-reply">[comment-reply-link]</span></div>' ); ?>
    <?php do_action( 'close_comment' ); // supreme_close_comment ?>
  </div>
  <!-- #comment-## -->
  <?php do_action( 'after_comment' ); // supreme_after_comment ?>