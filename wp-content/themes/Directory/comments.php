<?php
/**
 * Comments Template
 *
 * Lists comments and calls the comment form.  Individual comments have their own templates.  The 
 * hierarchy for these templates is $comment_type.php, comment.php.
 *
 * @package supreme
 * @subpackage Template
 */
/* Kill the page if trying to access this template directly. */
if ( 'comments.php' == basename( $_SERVER['SCRIPT_FILENAME'] ) )
	die( __( 'Please do not load this page directly. Thanks!', THEME_DOMAIN ) );
/* If a post password is required or no comments are given and comments/pings are closed, return. */
if ( post_password_required() || ( !have_comments() && !comments_open() && !pings_open() ) )
	return;
?>
<div id="comments-template">
  <div class="comments-wrap">
    <div id="comments">
      <?php if ( have_comments() ) : 
			
				do_action("show_comment");
			?>
      <h3 id="comments-number" class="comments-header">
        <?php 
		global $post;
		if($post->post_type == 'post')
		{
			comments_number( __( 'No Comment', THEME_DOMAIN ), __( 'One Comment', THEME_DOMAIN ), __( '% Comments', THEME_DOMAIN ) ); 
		}
		else
		{
			comments_number( __( 'No Review', THEME_DOMAIN ), __( 'One Review', THEME_DOMAIN ), __( '% Reviews', THEME_DOMAIN ) ); 
		}?>
      </h3>
      <?php do_action( 'before_comment_list' );// supreme_before_comment_list ?>
      <?php if ( get_option( 'page_comments' ) ) : ?>
      <div class="comment-navigation comment-pagination"> <span class="page-numbers"><?php printf( __( 'Page %1$s of %2$s', THEME_DOMAIN ), ( get_query_var( 'cpage' ) ? absint( get_query_var( 'cpage' ) ) : 1 ), get_comment_pages_count() ); ?></span>
        <?php paginate_comments_links(); ?>
      </div>
      <!-- .comment-navigation -->
      <?php endif; ?>
      <ol class="comment-list">
        <?php wp_list_comments( supreme_list_comments_args() ); ?>
      </ol>
      <!-- .comment-list -->
      <?php do_action( 'after_comment_list' ); // supreme_after_comment_list ?>
      <?php endif; ?>
      <?php if ( pings_open() && !comments_open() ) : ?>
      <p class="comments-closed pings-open"> <?php printf( __( 'Comments are closed, but <a href="%1$s" title="Trackback URL for this post">trackbacks</a> and pingbacks are open.', THEME_DOMAIN ), get_trackback_url() ); ?> </p>
      <!-- .comments-closed .pings-open -->
      <?php elseif ( !comments_open() ) : ?>
      <p class="comments-closed">
        <?php _e( 'Comments are closed.', THEME_DOMAIN ); ?>
      </p>
      <!-- .comments-closed -->
      <?php endif; ?>
    </div>
    <!-- #comments -->
    <?php 
	
					if(get_option('default_comment_status') =='open'){
						comment_form(); } // Loads the comment form.  ?>
  </div>
  <!-- .comments-wrap -->
</div>
<!-- #comments-template -->