<?php
/*
@This file contains the all core supreme widgets which work with posts in wp
*/
/* Unregister WP widgets. */
add_action( 'widgets_init', 'supreme_unregister_widgets' );
/* Register Hybrid widgets. */
add_action( 'widgets_init', 'supreme_register_widgets' );
function supreme_register_widgets()
{
	/* Register the advertisement widget. */
	register_widget( 'supreme_advertisements' );
	/* Register the flicker photos widget. */
	register_widget('supreme_flicker_Widget');
	/* Register the archives widget. */
	register_widget('supreme_widget_archives');
	/* Register the authors widget. */
	register_widget( 'supreme_authors_widget' );
	/* Register the bookmarks widget. */
	register_widget( 'supreme_bookmarks_widget' );
	/* Register the calendar widget. */
	register_widget( 'supreme_calendar_widget' );
	/* Register the categories widget. */
	register_widget( 'supreme_categories_widget' );
	/* Register the post formats widget. */
	//register_widget( 'supreme_post_formats_widget' );
	/* Register the nav menu widget. */
	register_widget( 'supreme_nav_menu_widget' );
	/* Register the pages widget. */
	register_widget( 'supreme_pages_widget' );
	/* Register the search widget. */
	register_widget( 'supreme_search_widget' );
	/* Register the tags widget. */
	register_widget( 'supreme_tags_widget' );
	/* Register Google Map Widget */
	register_widget('supreme_google_map');
	/* Register Social media Widget */
	register_widget('supreme_social_media');
	/* Subscriber Widget */
	register_widget('supreme_subscriber_widget');
	/* Testimonial Widget */
	register_widget('supreme_testimonials_widget');
	/* slider Widget */
	register_widget('supreme_banner_slider');
	register_widget('supreme_contact_widget');
	register_widget('supreme_popular_post');
	register_widget('supreme_recent_post');
	register_widget('supreme_recent_review');
	register_widget('templatic_text');
	/*	Code By Templatic End */
}
/**
* Unregister default WordPress widgets that are replaced by the framework's widgets.  Widgets that
* aren't replaced by the framework widgets are not unregistered.
*
* @since 0.3.2
* @access private
* @uses unregister_widget() Unregisters a registered widget.
* @link http://codex.wordpress.org/Function_Reference/unregister_widget
* @return void
*/
function supreme_unregister_widgets()
{
	/* Unregister the default WordPress widgets. */
	unregister_widget( 'WP_Widget_Archives' );
	unregister_widget( 'WP_Widget_Calendar' );
	unregister_widget( 'WP_Widget_Categories' );
	unregister_widget( 'WP_Nav_Menu_Widget' );
	unregister_widget( 'WP_Widget_Pages' );
	unregister_widget( 'WP_Widget_Recent_Posts' );
	unregister_widget( 'WP_Widget_Search' );
	unregister_widget( 'WP_Widget_Tag_Cloud' );
	unregister_sidebar( 'entry' );
}
/*------------------------------------------------------------------
* Create the templatic advertisement widget
-----------------------------------------------------------------*/
if(!class_exists('supreme_advertisements'))
{
	class supreme_advertisements extends WP_Widget
	{
		function supreme_advertisements()
		{
			//Constructor
			$widget_ops = array('classname'  => 'widget Templatic Advertisements','description'=> __('Use this widget to display ads by pasting HTML, JavaScript or IFrame code.',THEME_DOMAIN) );
			$this->WP_Widget('supreme_advertisements', __('T &rarr; Advertisements',THEME_DOMAIN), $widget_ops);
		}
		function widget($args, $instance)
		{
			// prints the widget
			extract($args, EXTR_SKIP);
			$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
			$ads = empty($instance['ads']) ? '<a href="http://www.templatic.com"><img src="'.get_template_directory_uri().'/images/dummy/top-ad.jpg" /></a>' : $instance['ads'];
			echo $before_widget;
			if( $title <> "" )
			{
				echo '<h3 class="widget-title">'.$title.'</h3>';
			}
			?>
<!-- Display advertisment -->
<div class="advertisements"> <?php echo $ads; ?> </div>
<?php
			echo $after_widget;
		}
		function update($new_instance, $old_instance)
		{
			//save the widget			
			return $new_instance;
		}
		function form($instance)
		{
			//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array('title'=> '','ads'  => '') );
			$title = ($instance['title']) ? strip_tags($instance['title']) :'';
			$ads   = ($instance['ads']) ? trim($instance['ads']) : '';
			?>
<p>
  <label for="<?php  echo $this->get_field_id('title'); ?>">
    <?php _e('Title',THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php  echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('ads'); ?>">
    <?php _e('Advertisement code <small>(ex.&lt;a href="#"&gt;&lt;img src="http://templatic.com/banner.png" /&gt;&lt;/a&gt; and google ads code here )</small>',THEME_DOMAIN);?>
    :
    <textarea class="widefat" rows="10" id="<?php echo $this->get_field_id('ads'); ?>" name="<?php echo $this->get_field_name('ads'); ?>"><?php echo esc_attr($ads); ?></textarea>
  </label>
</p>
<?php
		}
	}
}
/*---------------------------------------------------------
Supreme Flicker Widget start
---------------------------------------------------------*/
if(!class_exists('supreme_flicker_Widget'))
{
	class supreme_flicker_Widget extends WP_Widget
	{
		function supreme_flicker_Widget()
		{
			//Constructor
			$widget_ops = array('classname'  => 'widget Flicker Photos ','description'=> __('Showcase your Flickr photos using this simple widget. Works best in sidebar areas.',THEME_DOMAIN) );
			$this->WP_Widget('flicker_Widget', __('T &rarr; Flickr Photos',THEME_DOMAIN), $widget_ops);
		}
		function widget($args, $instance)
		{
			// prints the widget
			extract($args, EXTR_SKIP);
			$title = empty($instance['title']) ? __("Photos From Flickr",THEME_DOMAIN) : apply_filters('widget_title', $instance['title']);
			$desc = empty($instance['desc']) ? __("The Beautiful Natural Scenery Photos",THEME_DOMAIN) : apply_filters('widget_desc', $instance['desc']);
			$flicker_id = empty($instance['flicker_id']) ? '73963570@N00' : apply_filters('widget_flicker_id', $instance['flicker_id']);
			$flicker_number = empty($instance['flicker_number']) ? '8' : apply_filters('widget_flicker_number', $instance['flicker_number']);
			echo $before_widget;?>
<div class="Flicker">
  <?php
				if(function_exists('icl_register_string'))
				{
					icl_register_string(THEME_DOMAIN,'flickr_desc',$desc);
					$desc = icl_t(THEME_DOMAIN,'flickr_desc',$desc);
				}
				?>
  <?php
				if($title != ""):?>
  <h3 class="widget-title"> <?php echo sprintf(__('%s',THEME_DOMAIN),$title);?> </h3>
  <?php endif;?>
  <?php
				if($desc != "")
				{
					echo '<span class="flickr_description">'.$desc.'</span>';
				}
				?>
				<div class="flickr_pics_wrap">
					<?php if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; } ?>
					<script type="text/javascript" src="<?php echo $http; ?>www.flicker.com/badge_code_v2.gne?count=<?php echo $flicker_number; ?>&amp;display=latest&amp;size=s&amp;layout=x&amp;source=user&amp;user=<?php echo $flicker_id; ?>">
					</script>
				</div>
</div>
<?php
			echo $after_widget;
		}
		function update($new_instance, $old_instance)
		{
			//save the widget
			return $new_instance;
		}
		function form($instance)
		{
			//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array('title'=> __("Photos From Flickr",THEME_DOMAIN),'desc' => __("The Beautiful Natural Scenery Photos",THEME_DOMAIN), 'flicker_id' => '73963570@N00', 'flicker_number' => 8 ) );
			?>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>">
    <?php _e('Title:',THEME_DOMAIN);?>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('desc'); ?>">
    <?php _e('Description:',THEME_DOMAIN);?>
    <input class="widefat" id="<?php echo $this->get_field_id('desc'); ?>" name="<?php echo $this->get_field_name('desc'); ?>" type="text" value="<?php echo esc_attr($instance['desc']); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo @$this->get_field_id('flicker_id'); ?>">
    <?php _e('Flickr ID:',THEME_DOMAIN);?>
    <b> (get id <a href="http://idgettr.com/" target="blank"> here </a>) </b>
    <input class="widefat" id="<?php echo @$this->get_field_id('flicker_id'); ?>" name="<?php echo @$this->get_field_name('flicker_id'); ?>" type="text" value="<?php echo esc_attr( @$instance['flicker_id']); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo @$this->get_field_id('flicker_number'); ?>">
    <?php _e('Number of photos:',THEME_DOMAIN);?>
    <input class="widefat" id="<?php echo @$this->get_field_id('flicker_number'); ?>" name="<?php echo @$this->get_field_name('flicker_number'); ?>" type="text" value="<?php echo esc_attr( @$instance['flicker_number']); ?>" />
  </label>
</p>
<?php
		}
	}
}
/*--------------------------------------------------------------------
* Archives widget class.
---------------------------------------------------------------------*/
if(!class_exists('supreme_widget_archives'))
{
	class supreme_widget_archives extends WP_Widget
	{
		/**
		* Set up the widget's unique name, ID, class, description, and other options.
		*
		* @since 1.2.0
		*/
		function __construct()
		{
			/* Set up the widget options. */
			$widget_options = array(
				'classname'  => 'archives',
				'description'=> esc_html__( 'An advanced widget that gives you total control over the output of your archives.', THEME_DOMAIN )
			);
			/* Set up the widget control options. */
			$control_options = array(
				'height'=> 350
			);
			/* Create the widget. */
			$this->WP_Widget(
				'hybrid-archives',			// $this->id_base
				__( 'Archives', THEME_DOMAIN ),	// $this->name
				$widget_options,			// $this->widget_options
				$control_options			// $this->control_options
			);
		}
		/**
		* Outputs the widget based on the arguments input through the widget controls.
		*
		* @since 0.6.0
		*/
		function widget( $sidebar, $instance )
		{
			extract( $sidebar );
			/* Set the $args for wp_get_archives() to the $instance array. */
			$args = $instance;
			/* Overwrite the $echo argument and set it to false. */
			$args['echo'] = false;
			/* Output the theme's $before_widget wrapper. */
			echo $before_widget;
			/* If a title was input by the user, display it. */
			if( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;
			/* Get the archives list. */
			$archives = str_replace( array("\r","\n","\t" ), '', wp_get_archives( $args ) );
			/* If the archives should be shown in a <select> drop-down. */
			if( 'option' == $args['format'] )
			{
				/* Create a title for the drop-down based on the archive type. */
				if( 'yearly' == $args['type'] )
				$option_title = esc_html__( 'Select Year', THEME_DOMAIN );
				elseif( 'monthly' == $args['type'] )
				$option_title = esc_html__( 'Select Month', THEME_DOMAIN );
				elseif( 'weekly' == $args['type'] )
				$option_title = esc_html__( 'Select Week', THEME_DOMAIN);
				elseif( 'daily' == $args['type'] )
				$option_title = esc_html__( 'Select Day', THEME_DOMAIN );
				elseif( 'postbypost' == $args['type'] || 'alpha' == $args['type'] )
				$option_title = esc_html__( 'Select Post', THEME_DOMAIN );
				/* Output the <select> element and each <option>. */
				echo '<p><select name="archive-dropdown" onchange=\'document.location.href=this.options[this.selectedIndex].value;\'>';
				echo '<option value="">' . $option_title . '</option>';
				echo $archives;
				echo '</select></p>';
			}
			/* If the format should be an unordered list. */
			elseif( 'html' == $args['format'] )
			{
				echo '<ul class="xoxo archives">' . $archives . '</ul><!-- .xoxo .archives -->';
			}
			/* All other formats. */
			else
			{
				echo $archives;
			}
			/* Close the theme's widget wrapper. */
			echo $after_widget;
		}
		/**
		* Updates the widget control options for the particular instance of the widget.
		*
		* @since 0.6.0
		*/
		function update( $new_instance, $old_instance )
		{
			$instance = $new_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['before'] = strip_tags( $new_instance['before'] );
			$instance['after'] = strip_tags( $new_instance['after'] );
			$instance['limit'] = strip_tags( $new_instance['limit'] );
			$instance['show_post_count'] = ( isset( $new_instance['show_post_count'] ) ? 1 : 0 );
			return $instance;
		}
		/**
		* Displays the widget control options in the Widgets admin screen.
		*
		* @since 0.6.0
		*/
		function form( $instance )
		{
			/* Set up the default form values. */
			$defaults = array(
				'title'          => esc_attr__( 'Archives', THEME_DOMAIN ),
				'limit'          => 10,
				'type'           => 'monthly',
				'format'         => 'html',
				'before'         => '',
				'after'          => '',
				'show_post_count'=> false
			);
			/* Merge the user-selected arguments with the defaults. */
			$instance = wp_parse_args( (array) $instance, $defaults );
			/* Create an array of archive types. */
			$type = array('alpha'     => esc_attr__( 'Alphabetical', THEME_DOMAIN ),'daily'     => esc_attr__( 'Daily', THEME_DOMAIN ),'monthly'   => esc_attr__( 'Monthly', THEME_DOMAIN ),'postbypost'=> esc_attr__( 'Post By Post', THEME_DOMAIN ),'weekly'    => esc_attr__( 'Weekly',THEME_DOMAIN ),'yearly'    => esc_attr__( 'Yearly', THEME_DOMAIN ) );
			/* Create an array of archive formats. */
			$format = array('custom'=> esc_attr__( 'Custom', THEME_DOMAIN ),'html'  => esc_attr__( 'HTML', THEME_DOMAIN ),'option'=> esc_attr__( 'Option', THEME_DOMAIN ) );
			?>
<p>
  <label for="<?php echo $this->get_field_id( 'title' ); ?>">
    <?php _e( 'Title', THEME_DOMAIN ); ?>: 
  </label>
  <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'limit' ); ?>"> <code> limit </code> </label>
  <input type="text" class="widefat smallfat code" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo esc_attr( $instance['limit'] ); ?>" />
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'type' ); ?>"> <code> type </code> </label>
  <select class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
    <?php
						foreach( $type as $option_value => $option_label )
						{
							?>
    <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['type'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
    <?php
						} ?>
  </select>
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'format' ); ?>"> <code> format </code> </label>
  <select  class="widefat" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>">
    <?php
						foreach( $format as $option_value => $option_label )
						{
							?>
    <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['format'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
    <?php
						} ?>
  </select>
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'before' ); ?>"> <code> before </code> </label>
  <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'before' ); ?>" name="<?php echo $this->get_field_name( 'before' ); ?>" value="<?php echo esc_attr( $instance['before'] ); ?>" />
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'after' ); ?>"> <code> after </code> </label>
  <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'after' ); ?>" name="<?php echo $this->get_field_name( 'after' ); ?>" value="<?php echo esc_attr( $instance['after'] ); ?>" />
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'show_post_count' ); ?>">
    <input class="checkbox" type="checkbox" <?php checked( $instance['show_post_count'], true ); ?> id="<?php echo $this->get_field_id( 'show_post_count' ); ?>" name="<?php echo $this->get_field_name( 'show_post_count' ); ?>" />
    <?php _e( 'Show post count?',THEME_DOMAIN); ?>
    <code> show_post_count </code> </label>
</p>
<div style="clear:both;"> &nbsp; </div>
<?php
		}
	}
}
/*-------------------------------------------------------------
templatic Text widget Class
---------------------------------------------------------------*/
class templatic_text extends WP_Widget
{
	function templatic_text()
	{
		//Constructor
		$widget_ops = array('classname'    => 'widget Templatic Text templatic_text','description'  => __('Arbitrary text or HTML',THEME_DOMAIN),'before_widget'=>'<div class="column_wrap">' );
		$this->WP_Widget('templatic_text', __('Text',THEME_DOMAIN), $widget_ops);
	}
	function widget($args, $instance)
	{
		// prints the widget
		extract($args, EXTR_SKIP);
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$about_us = empty($instance['text']) ? '' : apply_filters('widget_text', $instance['text']);
		echo $before_widget;
		if(function_exists('icl_register_string'))
		{
			icl_register_string(THEME_DOMAIN,'templatic_text_title'.$title,$title);
			$title    = icl_t(THEME_DOMAIN, 'templatic_text_title'.$title,$title);
			icl_register_string(THEME_DOMAIN,'templatic_text_description'.$about_us,$about_us);
			$about_us = icl_t(THEME_DOMAIN, 'templatic_text_description'.$about_us,$about_us);
		}
		if( $title <> "" )
		{
			echo $before_title;
			echo $title;
			echo $after_title;
		}
		?>
<div class="textwidget"> <?php echo $about_us;?> </div>
<?php
		echo $after_widget;
	}
	function update($new_instance, $old_instance)
	{
		//save the widget
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['text'] = $new_instance['text'];
		return $instance;
	}
	function form($instance)
	{
		//widgetform in backend
		$instance = wp_parse_args( (array) $instance, array('title'=> '','text' => '',       ) );
		$title = $instance['title'];
		$text  = trim($instance['text']);
		?>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>">
    <?php _e('Title:',THEME_DOMAIN);?>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('text'); ?>">
    <?php _e('Description:',THEME_DOMAIN);?>
    <textarea class="widefat" name="<?php echo $this->get_field_name('text'); ?>" cols="20" rows="16"><?php echo trim(esc_attr($text)); ?></textarea>
  </label>
</p>
<?php
	}
}
/*-------------------------------------------------------------
Authors Widget Class
--------------------------------------------------------------*/
if(!class_exists('supreme_authors_widget'))
{
	class supreme_authors_widget extends WP_Widget
	{
		/**
		* Set up the widget's unique name, ID, class, description, and other options.
		*
		* @since 1.2.0
		*/
		function __construct()
		{
			/* Set up the widget options. */
			$widget_options = array(
				'classname'  => 'authors',
				'description'=> esc_html__( 'An advanced widget that gives you total control over the output of your author lists.', THEME_DOMAIN )
			);
			/* Set up the widget control options. */
			$control_options = array(
				'height'=> 350
			);
			/* Create the widget. */
			$this->WP_Widget(
				'hybrid-authors',			// $this->id_base
				__( 'Authors', THEME_DOMAIN ),	// $this->name
				$widget_options,			// $this->widget_options
				$control_options			// $this->control_options
			);
		}
		/**
		* Outputs the widget based on the arguments input through the widget controls.
		*
		* @since 0.6.0
		*/
		function widget( $sidebar, $instance )
		{
			extract( $sidebar );
			/* Set the $args for wp_list_authors() to the $instance array. */
			$args = $instance;
			/* Overwrite the $echo argument and set it to false. */
			$args['echo'] = false;
			/* Output the theme's $before_widget wrapper. */
			echo $before_widget;
			/* If a title was input by the user, display it. */
			if( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;
			/* Get the authors list. */
			$authors = str_replace( array("\r","\n","\t" ), '', wp_list_authors( $args ) );
			/* If 'list' is the style and the output should be HTML, wrap the authors in a <ul>. */
			if( 'list' == $args['style'] && $args['html'] )
			$authors = '<ul class="xoxo authors">' . $authors . '</ul><!-- .xoxo .authors -->';
			/* Display the authors list. */
			echo $authors;
			/* Close the theme's widget wrapper. */
			echo $after_widget;
		}
		/**
		* Updates the widget control options for the particular instance of the widget.
		*
		* @since 0.6.0
		*/
		function update( $new_instance, $old_instance )
		{
			$instance = $old_instance;
			$instance = $new_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['feed'] = strip_tags( $new_instance['feed'] );
			$instance['order'] = strip_tags( $new_instance['order'] );
			$instance['orderby'] = strip_tags( $new_instance['orderby'] );
			$instance['number'] = strip_tags( $new_instance['number'] );
			$instance['html'] = ( isset( $new_instance['html'] ) ? 1 : 0 );
			$instance['optioncount'] = ( isset( $new_instance['optioncount'] ) ? 1 : 0 );
			$instance['exclude_admin'] = ( isset( $new_instance['exclude_admin'] ) ? 1 : 0 );
			$instance['show_fullname'] = ( isset( $new_instance['show_fullname'] ) ? 1 : 0 );
			$instance['hide_empty'] = ( isset( $new_instance['hide_empty'] ) ? 1 : 0 );
			return $instance;
		}
		/**
		* Displays the widget control options in the Widgets admin screen.
		*
		* @since 0.6.0
		*/
		function form( $instance )
		{
			/* Set up the default form values. */
			$defaults = array(
				'title'        => esc_attr__( 'Authors', THEME_DOMAIN ),
				'order'        => 'ASC',
				'orderby'      => 'display_name',
				'number'       => '',
				'optioncount'  => false,
				'exclude_admin'=> false,
				'show_fullname'=> true,
				'hide_empty'   => true,
				'style'        => 'list',
				'html'         => true,
				'feed'         => '',
				'feed_image'   => ''
			);
			/* Merge the user-selected arguments with the defaults. */
			$instance = wp_parse_args( (array) $instance, $defaults );
			$order = array('ASC' => esc_attr__( 'Ascending', THEME_DOMAIN ),'DESC'=> esc_attr__( 'Descending', THEME_DOMAIN ) );
			$orderby = array('display_name'=> esc_attr__( 'Display Name', THEME_DOMAIN ),'email'       => esc_attr__( 'Email', THEME_DOMAIN ),'ID'          => esc_attr__( 'ID', THEME_DOMAIN ),'nicename'    => esc_attr__( 'Nice Name', THEME_DOMAIN ),'post_count'  => esc_attr__( 'Post Count', THEME_DOMAIN ),'registered'  => esc_attr__( 'Registered', THEME_DOMAIN ),'url'         => esc_attr__( 'URL', THEME_DOMAIN ),'user_login'  => esc_attr__( 'Login', THEME_DOMAIN ) );
			?>
<div class="hybrid-widget-controls columns-2">
  <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
      <?php _e( 'Title:', THEME_DOMAIN ); ?>
    </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'order' ); ?>"> <code> order </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
      <?php
						foreach( $order as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['order'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"> <code> orderby </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
      <?php
						foreach( $orderby as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['orderby'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'number' ); ?>"> <code> number </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $instance['number'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'style' ); ?>"> <code> style </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
      <?php
						foreach( array( 'list' => esc_attr__( 'List', THEME_DOMAIN), 'none' => esc_attr__( 'None', THEME_DOMAIN ) ) as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['style'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
</div>
<div class="hybrid-widget-controls columns-2 column-last">
  <p>
    <label for="<?php echo $this->get_field_id( 'feed' ); ?>"> <code> feed </code> </label>
    <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'feed' ); ?>" name="<?php echo $this->get_field_name( 'feed' ); ?>" value="<?php echo esc_attr( $instance['feed'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'feed_image' ); ?>"> <code> feed_image </code> </label>
    <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'feed_image' ); ?>" name="<?php echo $this->get_field_name( 'feed_image' ); ?>" value="<?php echo esc_attr( $instance['feed_image'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'html' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['html'], true ); ?> id="<?php echo $this->get_field_id( 'html' ); ?>" name="<?php echo $this->get_field_name( 'html' ); ?>" />
      <?php _e( '<acronym title="Hypertext Markup Language">HTML</acronym>?', THEME_DOMAIN ); ?>
      <code> html </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'optioncount' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['optioncount'], true ); ?> id="<?php echo $this->get_field_id( 'optioncount' ); ?>" name="<?php echo $this->get_field_name( 'optioncount' ); ?>" />
      <?php _e( 'Show post count?', THEME_DOMAIN ); ?>
      <code> optioncount </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'exclude_admin' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['exclude_admin'], true ); ?> id="<?php echo $this->get_field_id( 'exclude_admin' ); ?>" name="<?php echo $this->get_field_name( 'exclude_admin' ); ?>" />
      <?php _e( 'Exclude admin?', THEME_DOMAIN ); ?>
      <code> exclude_admin </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'show_fullname' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['show_fullname'], true ); ?> id="<?php echo $this->get_field_id( 'show_fullname' ); ?>" name="<?php echo $this->get_field_name( 'show_fullname' ); ?>" />
      <?php _e( 'Show full name?', THEME_DOMAIN ); ?>
      <code> show_fullname </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['hide_empty'], true ); ?> id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" />
      <?php _e( 'Hide empty?', THEME_DOMAIN ); ?>
      <code> hide_empty </code> </label>
  </p>
</div>
<div style="clear:both;"> &nbsp; </div>
<?php
		}
	}
}
/*---------------------------------------------------------
* Bookmarks Widget Class
------------------------------------------------------------ */
if(!class_exists('supreme_bookmarks_widget'))
{
	class supreme_bookmarks_widget extends WP_Widget
	{
		/**
		* Set up the widget's unique name, ID, class, description, and other options.
		*/
		function __construct()
		{
			/* Set up the widget options. */
			$widget_options = array(
				'classname'  => 'bookmarks',
				'description'=> esc_html__( 'An advanced widget that gives you total control over the output of your bookmarks (links).', THEME_DOMAIN )
			);
			/* Set up the widget control options. */
			$control_options = array(
				'height'=> 350
			);
			/* Create the widget. */
			$this->WP_Widget(
				'hybrid-bookmarks',		// $this->id_base
				__( 'Bookmarks', THEME_DOMAIN ),	// $this->name
				$widget_options,			// $this->widget_options
				$control_options			// $this->control_options
			);
		}
		/**
		* Outputs the widget based on the arguments input through the widget controls.*/
		function widget( $sidebar, $instance )
		{
			extract( $sidebar );
			/* Set up the $before_widget ID for multiple widgets created by the bookmarks widget. */
			if( !empty( $instance['categorize'] ) )
			$before_widget = preg_replace( '/id="[^"]*"/','id="%id"', $before_widget );
			/* Add a class to $before_widget if one is set. */
			if( !empty( $instance['class'] ) )
			$before_widget = str_replace( 'class="', 'class="' . esc_attr( $instance['class'] ) . ' ', $before_widget );
			/* Set the $args for wp_list_bookmarks() to the $instance array. */
			$args          = $instance;
			/* wp_list_bookmarks() hasn't been updated in WP to use wp_parse_id_list(), so we have to pass strings for ncludes/excludes. */
			if( !empty( $args['category'] ) && is_array( $args['category'] ) )
			$args['category'] = join( ', ', $args['category'] );
			if( !empty( $args['exclude_category'] ) && is_array( $args['exclude_category'] ) )
			$args['exclude_category'] = join( ', ', $args['exclude_category'] );
			if( !empty( $args['include'] ) && is_array( $args['include'] ) )
			$args['include'] = join( ',', $args['include'] );
			if( !empty( $args['exclude'] ) && is_array( $args['exclude'] ) )
			$args['exclude'] = join( ',', $args['exclude'] );
			/* If no limit is given, set it to -1. */
			$args['limit'] = empty( $args['limit'] ) ? - 1 : $args['limit'];
			/* Some arguments must be set to the sidebar arguments to be output correctly. */
			$args['title_li'] = apply_filters( 'widget_title', ( empty( $args['title_li'] ) ? __( 'Bookmarks', THEME_DOMAIN ) : $args['title_li'] ), $instance, $this->id_base );
			$args['title_before'] = $before_title;
			$args['title_after'] = $after_title;
			$args['category_before'] = $before_widget;
			$args['category_after'] = $after_widget;
			$args['category_name'] = '';
			$args['echo'] = false;
			/* Output the bookmarks widget. */
			$bookmarks = str_replace( array("\r","\n","\t" ), '', wp_list_bookmarks( $args ) );
			/* If no title is given and the bookmarks aren't categorized, add a wrapper <ul>. */
			if( empty( $args['title_li'] ) && false === $args['categorize'] )
			$bookmarks = '<ul class="xoxo bookmarks">' . $bookmarks . '</ul>';
			/* Output the bookmarks. */
			echo $bookmarks;
		}
		/** Updates the widget control options for the particular instance of the widget.			 **/
		function update( $new_instance, $old_instance )
		{
			$instance = $old_instance;
			/* Set the instance to the new instance. */
			$instance = $new_instance;
			$instance['title_li'] = strip_tags( $new_instance['title_li'] );
			$instance['limit'] = strip_tags( $new_instance['limit'] );
			$instance['class'] = strip_tags( $new_instance['class'] );
			$instance['search'] = strip_tags( $new_instance['search'] );
			$instance['category_order'] = $new_instance['category_order'];
			$instance['category_orderby'] = $new_instance['category_orderby'];
			$instance['orderby'] = $new_instance['orderby'];
			$instance['order'] = $new_instance['order'];
			$instance['between'] = $new_instance['between'];
			$instance['link_before'] = $new_instance['link_before'];
			$instance['link_after'] = $new_instance['link_after'];
			$instance['categorize'] = ( isset( $new_instance['categorize'] ) ? 1 : 0 );
			$instance['hide_invisible'] = ( isset( $new_instance['hide_invisible'] ) ? 1 : 0 );
			$instance['show_private'] = ( isset( $new_instance['show_private'] ) ? 1 : 0 );
			$instance['show_rating'] = ( isset( $new_instance['show_rating'] ) ? 1 : 0 );
			$instance['show_updated'] = ( isset( $new_instance['show_updated'] ) ? 1 : 0 );
			$instance['show_images'] = ( isset( $new_instance['show_images'] ) ? 1 : 0 );
			$instance['show_name'] = ( isset( $new_instance['show_name'] ) ? 1 : 0 );
			$instance['show_description'] = ( isset( $new_instance['show_description'] ) ? 1 : 0 );
			return $instance;
		}
		/**
		* Displays the widget control options in the Widgets admin screen.
		*
		* @since 0.6.0
		*/
		function form( $instance )
		{
			/* Set up the default form values. */
			$defaults = array(
				'title_li'        => esc_attr__( 'Bookmarks', THEME_DOMAIN ),
				'categorize'      => true,
				'category_order'  => 'ASC',
				'category_orderby'=> 'name',
				'category'         => array(),
				'exclude_category' => array(),
				'limit'           => - 1,
				'order'           => 'ASC',
				'orderby'         => 'name',
				'include'          => array(),
				'exclude'          => array(),
				'search'          => '',
				'hide_invisible'  => true,
				'show_description'=> false,
				'show_images'     => false,
				'show_rating'     => false,
				'show_updated'    => false,
				'show_private'    => false,
				'show_name'       => false,
				'class'           => 'linkcat',
				'link_before'     => '<span>',
				'link_after'      => '</span>',
				'between'         => '<br />',
			);
			/* Merge the user-selected arguments with the defaults. */
			$instance = wp_parse_args( (array) $instance, $defaults );
			$terms     = get_terms( 'link_category' );
			$bookmarks = get_bookmarks( array('hide_invisible'=> false ) );
			$category_order = array('ASC' => esc_attr__( 'Ascending', THEME_DOMAIN ),'DESC'=> esc_attr__( 'Descending', THEME_DOMAIN ) );
			$category_orderby = array('count'=> esc_attr__( 'Count', THEME_DOMAIN ),'ID'   => esc_attr__( 'ID', THEME_DOMAIN ),'name' => esc_attr__( 'Name', THEME_DOMAIN ),'slug' => esc_attr__( 'Slug', THEME_DOMAIN ) );
			$order = array('ASC' => esc_attr__( 'Ascending', THEME_DOMAIN ),'DESC'=> esc_attr__( 'Descending', THEME_DOMAIN ) );
			$orderby = array('id'         => esc_attr__( 'ID', THEME_DOMAIN ),'description'=> esc_attr__( 'Description',  THEME_DOMAIN ),'length'     => esc_attr__( 'Length',  THEME_DOMAIN ),'name'       => esc_attr__( 'Name',  THEME_DOMAIN ),'notes'      => esc_attr__( 'Notes',  THEME_DOMAIN ),'owner'      => esc_attr__( 'Owner',  THEME_DOMAIN ),'rand'       => esc_attr__( 'Random',  THEME_DOMAIN ),'rating'     => esc_attr__( 'Rating',  THEME_DOMAIN ),'rel'        => esc_attr__( 'Rel',  THEME_DOMAIN ),'rss'        => esc_attr__( 'RSS',  THEME_DOMAIN ),'target'     => esc_attr__( 'Target',  THEME_DOMAIN ),'updated'    => esc_attr__( 'Updated',  THEME_DOMAIN ),'url'        => esc_attr__( 'URL',  THEME_DOMAIN ) );
			?>
<div class="hybrid-widget-controls columns-3">
  <p>
    <label for="<?php echo $this->get_field_id( 'title_li' ); ?>">
      <?php _e( 'Title:', THEME_DOMAIN ); ?>
    </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title_li' ); ?>" name="<?php echo $this->get_field_name( 'title_li' ); ?>" value="<?php echo esc_attr( $instance['title_li'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'category_order' ); ?>"> <code> category_order </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'category_order' ); ?>" name="<?php echo $this->get_field_name( 'category_order' ); ?>">
      <?php
						foreach( $category_order as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['category_order'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'category_orderby' ); ?>"> <code> category_orderby </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'category_orderby' ); ?>" name="<?php echo $this->get_field_name( 'category_orderby' ); ?>">
      <?php
						foreach( $category_orderby as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['category_orderby'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'category' ); ?>"> <code> category </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>[]" size="4" multiple="multiple">
      <?php
						foreach( $terms as $term )
						{
							?>
      <option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo ( in_array( $term->term_id, (array) $instance['category'] ) ? 'selected="selected"' : '' ); ?>> <?php echo esc_html( $term->name ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'exclude_category' ); ?>"> <code> exclude_category </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'exclude_category' ); ?>" name="<?php echo $this->get_field_name( 'exclude_category' ); ?>[]" size="4" multiple="multiple">
      <?php
						foreach( $terms as $term )
						{
							?>
      <option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo ( in_array( $term->term_id, (array) $instance['exclude_category'] ) ? 'selected="selected"' : '' ); ?>> <?php echo esc_html( $term->name ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'class' ); ?>"> <code> class </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" value="<?php echo esc_attr( $instance['class'] ); ?>" />
  </p>
</div>
<div class="hybrid-widget-controls columns-3">
  <p>
    <label for="<?php echo $this->get_field_id( 'limit' ); ?>"> <code> limit </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo esc_attr( $instance['limit'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'order' ); ?>"> <code> order </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
      <?php
						foreach( $order as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['order'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"> <code> orderby </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
      <?php
						foreach( $orderby as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['orderby'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'include' ); ?>"> <code> include </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>[]" size="4" multiple="multiple">
      <?php
						foreach( $bookmarks as $bookmark )
						{
							?>
      <option value="<?php echo esc_attr( $bookmark->link_id ); ?>" <?php echo ( in_array( $bookmark->link_id, (array) $instance['include'] ) ? 'selected="selected"' : '' ); ?>> <?php echo esc_html( $bookmark->link_name ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'exclude' ); ?>"> <code> exclude </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>[]" size="4" multiple="multiple">
      <?php
						foreach( $bookmarks as $bookmark )
						{
							?>
      <option value="<?php echo esc_attr( $bookmark->link_id ); ?>" <?php echo ( in_array( $bookmark->link_id, (array) $instance['exclude'] ) ? 'selected="selected"' : '' ); ?>> <?php echo esc_html( $bookmark->link_name ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'search' ); ?>"> <code> search </code> </label>
    <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'search' ); ?>" name="<?php echo $this->get_field_name( 'search' ); ?>" value="<?php echo esc_attr( $instance['search'] ); ?>" />
  </p>
</div>
<div class="hybrid-widget-controls columns-3 column-last">
  <p>
    <label for="<?php echo $this->get_field_id( 'between' ); ?>"> <code> between </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'between' ); ?>" name="<?php echo $this->get_field_name( 'between' ); ?>" value="<?php echo esc_attr( $instance['between'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'link_before' ); ?>"> <code> link_before </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'link_before' ); ?>" name="<?php echo $this->get_field_name( 'link_before' ); ?>" value="<?php echo esc_attr( $instance['link_before'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'link_after' ); ?>"> <code> link_after </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'link_after' ); ?>" name="<?php echo $this->get_field_name( 'link_after' ); ?>" value="<?php echo esc_attr( $instance['link_after'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'categorize' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['categorize'], true ); ?> id="<?php echo $this->get_field_id( 'categorize' ); ?>" name="<?php echo $this->get_field_name( 'categorize' ); ?>" />
      <?php _e( 'Categorize?', THEME_DOMAIN ); ?>
      <code> categorize </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'show_description' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['show_description'], true ); ?> id="<?php echo $this->get_field_id( 'show_description' ); ?>" name="<?php echo $this->get_field_name( 'show_description' ); ?>" />
      <?php _e( 'Show description?', THEME_DOMAIN ); ?>
      <code> show_description </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'hide_invisible' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['hide_invisible'], true ); ?> id="<?php echo $this->get_field_id( 'hide_invisible' ); ?>" name="<?php echo $this->get_field_name( 'hide_invisible' ); ?>" />
      <?php _e( 'Hide invisible?', THEME_DOMAIN ); ?>
      <code> hide_invisible </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'show_rating' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['show_rating'], true ); ?> id="<?php echo $this->get_field_id( 'show_rating' ); ?>" name="<?php echo $this->get_field_name( 'show_rating' ); ?>" />
      <?php _e( 'Show rating?', THEME_DOMAIN ); ?>
      <code> show_rating </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'show_updated' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['show_updated'], true ); ?> id="<?php echo $this->get_field_id( 'show_updated' ); ?>" name="<?php echo $this->get_field_name( 'show_updated' ); ?>" />
      <?php _e( 'Show updated?', THEME_DOMAIN ); ?>
      <code> show_updated </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'show_images' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['show_images'], true ); ?> id="<?php echo $this->get_field_id( 'show_images' ); ?>" name="<?php echo $this->get_field_name( 'show_images' ); ?>" />
      <?php _e( 'Show images?', THEME_DOMAIN ); ?>
      <code> show_images </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'show_name' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['show_name'], true ); ?> id="<?php echo $this->get_field_id( 'show_name' ); ?>" name="<?php echo $this->get_field_name( 'show_name' ); ?>" />
      <?php _e( 'Show name?', THEME_DOMAIN ); ?>
      <code> show_name </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'show_private' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['show_private'], true ); ?> id="<?php echo $this->get_field_id( 'show_private' ); ?>" name="<?php echo $this->get_field_name( 'show_private' ); ?>" />
      <?php _e( 'Show private?', THEME_DOMAIN ); ?>
      <code> show_private </code> </label>
  </p>
</div>
<div style="clear:both;"> &nbsp; </div>
<?php
		}
	}
}
/*----------------------------------------------------------------
* Calendar Widget Class
-----------------------------------------------------------------*/
if(!class_exists('supreme_calendar_widget'))
{
	class supreme_calendar_widget extends WP_Widget
	{
		/**
		* Set up the widget's unique name, ID, class, description, and other options.
		*
		* @since 1.2.0
		*/
		function __construct()
		{
			/* Set up the widget options. */
			$widget_options = array(
				'classname'  => 'calendar',
				'description'=> esc_html__( 'An advanced widget that gives you total control over the output of your calendar.', THEME_DOMAIN )
			);
			/* Set up the widget control options. */
			$control_options = array(
				'height'=> 350
			);
			/* Create the widget. */
			$this->WP_Widget(
				'hybrid-calendar',			// $this->id_base
				__( 'Calendar', THEME_DOMAIN ),	// $this->name
				$widget_options,			// $this->widget_options
				$control_options			// $this->control_options
			);
		}
		/**
		* Outputs the widget based on the arguments input through the widget controls.
		*
		* @since 0.6.0
		*/
		function widget( $sidebar, $instance )
		{
			extract( $sidebar );
			/* Get the $initial argument. */
			$initial = !empty( $instance['initial'] ) ? true : false;
			/* Output the theme's widget wrapper. */
			echo $before_widget;
			/* If a title was input by the user, display it. */
			if( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;
			/* Display the calendar. */
			echo '<div class="calendar-wrap">';
			echo str_replace( array("\r","\n","\t" ), '', get_calendar( $initial, false ) );
			echo '</div><!-- .calendar-wrap -->';
			/* Close the theme's widget wrapper. */
			echo $after_widget;
		}
		/**
		* Updates the widget control options for the particular instance of the widget.
		*
		* @since 0.6.0
		*/
		function update( $new_instance, $old_instance )
		{
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['initial'] = ( isset( $new_instance['initial'] ) ? 1 : 0 );
			return $instance;
		}
		/**
		* Displays the widget control options in the Widgets admin screen.
		*
		* @since 0.6.0
		*/
		function form( $instance )
		{
			/* Set up the default form values. */
			$defaults = array(
				'title'  => esc_attr__( 'Calendar', THEME_DOMAIN ),
				'initial'=> false
			);
			/* Merge the user-selected arguments with the defaults. */
			$instance = wp_parse_args( (array) $instance, $defaults ); ?>
<div class="hybrid-widget-controls columns-1">
  <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
      <?php _e( 'Title:', THEME_DOMAIN ); ?>
    </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
  </p>
  <p>
    <input class="checkbox" type="checkbox" <?php checked( $instance['initial'], true ); ?> id="<?php echo $this->get_field_id( 'initial' ); ?>" name="<?php echo $this->get_field_name( 'initial' ); ?>" />
    <label for="<?php echo $this->get_field_id( 'initial' ); ?>">
      <?php _e( 'One-letter abbreviation?', THEME_DOMAIN ); ?>
      <code>
      <?php _e('initial',THEME_DOMAIN); ?>
      </code> </label>
  </p>
</div>
<?php
		}
	}
}
/*----------------------------------------------------------------
* Categories Widget Class
----------------------------------------------------------------- */
if(!class_exists('supreme_categories_widget'))
{
	class supreme_categories_widget extends WP_Widget
	{
		/*
		* Set up the widget's unique name, ID, class, description, and other options.
		*/
		function __construct()
		{
			/* Set up the widget options. */
			$widget_options = array(
				'classname'  => 'categories',
				'description'=> esc_html__( 'An advanced widget that gives you total control over the output of your category links.', THEME_DOMAIN )
			);
			/* Set up the widget control options. */
			$control_options = array(
				'height'=> 350
			);
			/* Create the widget. */
			$this->WP_Widget(
				'hybrid-categories',		// $this->id_base
				__( 'Categories', THEME_DOMAIN ),	// $this->name
				$widget_options,			// $this->widget_options
				$control_options			// $this->control_options
			);
		}
		/**
		* Outputs the widget based on the arguments input through the widget controls.
		*/
		function widget( $sidebar, $instance )
		{
			extract( $sidebar );
			/* Set the $args for wp_list_categories() to the $instance array. */
			$args = $instance;
			/* Set the $title_li and $echo arguments to false. */
			$args['title_li'] = false;
			$args['echo'] = false;
			/* Output the theme's widget wrapper. */
			echo $before_widget;
			/* If a title was input by the user, display it. */
			if( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;
			/* Get the categories list. */
			$categories = str_replace( array("\r","\n","\t" ), '', wp_list_categories( $args ) );
			/* If 'list' is the user-selected style, wrap the categories in an unordered list. */
			if( 'list' == $args['style'] )
			$categories = '<ul class="xoxo categories">' . $categories . '</ul><!-- .xoxo .categories -->';
			/* Output the categories list. */
			echo $categories;
			/* Close the theme's widget wrapper. */
			echo $after_widget;
		}
		/**
		* Updates the widget control options for the particular instance of the widget.
		*
		* @since 0.6.0
		*/
		function update( $new_instance, $old_instance )
		{
			$instance = $old_instance;
			/* Set the instance to the new instance. */
			$instance = $new_instance;
			/* If new taxonomy is chosen, reset includes and excludes. */
			if( $instance['taxonomy'] !== $old_instance['taxonomy'] && '' !== $old_instance['taxonomy'] )
			{
				$instance['include'] = array();
				$instance['exclude'] = array();
			}
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['taxonomy'] = $new_instance['taxonomy'];
			$instance['depth'] = strip_tags( $new_instance['depth'] );
			$instance['number'] = strip_tags( $new_instance['number'] );
			$instance['child_of'] = strip_tags( $new_instance['child_of'] );
			$instance['current_category'] = strip_tags( $new_instance['current_category'] );
			$instance['feed'] = strip_tags( $new_instance['feed'] );
			$instance['feed_image'] = esc_url( $new_instance['feed_image'] );
			$instance['search'] = strip_tags( $new_instance['search'] );
			$instance['include'] = preg_replace( '/[^0-9,]/', '', $new_instance['include'] );
			$instance['exclude'] = preg_replace( '/[^0-9,]/', '', $new_instance['exclude'] );
			$instance['exclude_tree'] = preg_replace( '/[^0-9,]/', '', $new_instance['exclude_tree'] );
			$instance['hierarchical'] = ( isset( $new_instance['hierarchical'] ) ? 1 : 0 );
			$instance['use_desc_for_title'] = ( isset( $new_instance['use_desc_for_title'] ) ? 1 : 0 );
			$instance['show_count'] = ( isset( $new_instance['show_count'] ) ? 1 : 0 );
			$instance['hide_empty'] = ( isset( $new_instance['hide_empty'] ) ? 1 : 0 );
			return $instance;
		}
		/**
		* Displays the widget control options in the Widgets admin screen.
		*
		* @since 0.6.0
		*/
		function form( $instance )
		{
			/* Set up the default form values. */
			$defaults = array(
				'title'             => esc_attr__( 'Categories', THEME_DOMAIN ),
				'taxonomy'          => 'category',
				'style'             => 'list',
				'include'           => '',
				'exclude'           => '',
				'exclude_tree'      => '',
				'child_of'          => '',
				'current_category'  => '',
				'search'            => '',
				'hierarchical'      => true,
				'hide_empty'        => true,
				'order'             => 'ASC',
				'orderby'           => 'name',
				'depth'             => 0,
				'number'            => '',
				'feed'              => '',
				'feed_type'         => '',
				'feed_image'        => '',
				'use_desc_for_title'=> false,
				'show_count'        => false,
			);
			/* Merge the user-selected arguments with the defaults. */
			$instance = wp_parse_args( (array) $instance, $defaults );
			/* <select> element options. */
			$taxonomies = get_taxonomies( array('show_tagcloud'=> true ), 'objects' );
			$terms = get_terms( $instance['taxonomy'] );
			$style = array('list'=> esc_attr__( 'List', THEME_DOMAIN ),'none'=> esc_attr__( 'None', THEME_DOMAIN ) );
			$order = array('ASC' => esc_attr__( 'Ascending', THEME_DOMAIN ),'DESC'=> esc_attr__( 'Descending', THEME_DOMAIN ) );
			$orderby = array('count'     => esc_attr__( 'Count', THEME_DOMAIN ),'ID'        => esc_attr__( 'ID', THEME_DOMAIN ),'name'      => esc_attr__( 'Name', THEME_DOMAIN ),'slug'      => esc_attr__( 'Slug', THEME_DOMAIN ),'term_group'=> esc_attr__( 'Term Group', THEME_DOMAIN ) );
			$feed_type = array(''    => '','atom'=> esc_attr__( 'Atom', THEME_DOMAIN ),'rdf' => esc_attr__( 'RDF', THEME_DOMAIN ),'rss' => esc_attr__( 'RSS', THEME_DOMAIN ),'rss2'=> esc_attr__( 'RSS 2.0', THEME_DOMAIN ) );
			?>
<div class="hybrid-widget-controls columns-3">
  <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
      <?php _e( 'Title:', THEME_DOMAIN ); ?>
    </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"> <code> taxonomy </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>">
      <?php
						foreach( $taxonomies as $taxonomy )
						{
							?>
      <option value="<?php echo esc_attr( $taxonomy->name ); ?>" <?php selected( $instance['taxonomy'], $taxonomy->name ); ?>> <?php echo esc_html( $taxonomy->labels->singular_name ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'style' ); ?>"> <code> style </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
      <?php
						foreach( $style as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['style'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'order' ); ?>"> <code> order </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
      <?php
						foreach( $order as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['order'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"> <code> orderby </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
      <?php
						foreach( $orderby as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['orderby'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
</div>
<div class="hybrid-widget-controls columns-3">
  <p>
    <label for="<?php echo $this->get_field_id( 'depth' ); ?>"> <code> depth </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'depth' ); ?>" name="<?php echo $this->get_field_name( 'depth' ); ?>" value="<?php echo esc_attr( $instance['depth'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'number' ); ?>"> <code> number </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $instance['number'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'include' ); ?>"> <code> include </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" value="<?php echo esc_attr( $instance['include'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'exclude' ); ?>"> <code> exclude </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo esc_attr( $instance['exclude'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'exclude_tree' ); ?>"> <code> exclude_tree </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'exclude_tree' ); ?>" name="<?php echo $this->get_field_name( 'exclude_tree' ); ?>" value="<?php echo esc_attr( $instance['exclude_tree'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'child_of' ); ?>"> <code> child_of </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'child_of' ); ?>" name="<?php echo $this->get_field_name( 'child_of' ); ?>" value="<?php echo esc_attr( $instance['child_of'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'current_category' ); ?>"> <code> current_category </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'current_category' ); ?>" name="<?php echo $this->get_field_name( 'current_category' ); ?>" value="<?php echo esc_attr( $instance['current_category'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'search' ); ?>"> <code> search </code> </label>
    <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'search' ); ?>" name="<?php echo $this->get_field_name( 'search' ); ?>" value="<?php echo esc_attr( $instance['search'] ); ?>" />
  </p>
</div>
<div class="hybrid-widget-controls columns-3 column-last">
  <p>
    <label for="<?php echo $this->get_field_id( 'feed' ); ?>"> <code> feed </code> </label>
    <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'feed' ); ?>" name="<?php echo $this->get_field_name( 'feed' ); ?>" value="<?php echo esc_attr( $instance['feed'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'feed_type' ); ?>"> <code> feed_type </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'feed_type' ); ?>" name="<?php echo $this->get_field_name( 'feed_type' ); ?>">
      <?php
						foreach( $feed_type as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['feed_type'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'feed_image' ); ?>"> <code> feed_image </code> </label>
    <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'feed_image' ); ?>" name="<?php echo $this->get_field_name( 'feed_image' ); ?>" value="<?php echo esc_attr( $instance['feed_image'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['hierarchical'], true ); ?> id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>" />
      <?php _e( 'Hierarchical?', THEME_DOMAIN ); ?>
      <code> hierarchical </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'use_desc_for_title' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['use_desc_for_title'], true ); ?> id="<?php echo $this->get_field_id( 'use_desc_for_title' ); ?>" name="<?php echo $this->get_field_name( 'use_desc_for_title' ); ?>" />
      <?php _e( 'Use description?', THEME_DOMAIN ); ?>
      <code> use_desc_for_title </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'show_count' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['show_count'], true ); ?> id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" />
      <?php _e( 'Show count?', THEME_DOMAIN ); ?>
      <code> show_count </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['hide_empty'], true ); ?> id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" />
      <?php _e( 'Hide empty?', THEME_DOMAIN ); ?>
      <code> hide_empty </code> </label>
  </p>
</div>
<div style="clear:both;"> &nbsp; </div>
<?php
		}
	}
}
/*----------------------------------------------------------------
* Nav Menu Widget Class
-----------------------------------------------------------------*/
if(!class_exists('supreme_nav_menu_widget'))
{
	class supreme_nav_menu_widget extends WP_Widget
	{
		/**
		* Set up the widget's unique name, ID, class, description, and other options.
		*
		* @since 1.2.0
		*/
		function __construct()
		{
			/* Set up the widget options. */
			$widget_options = array(
				'classname'  => 'nav-menu',
				'description'=> esc_html__( 'An advanced widget that gives you total control over the output of your menus.', THEME_DOMAIN )
			);
			/* Set up the widget control options. */
			$control_options = array(
				'height'=> 350
			);
			/* Create the widget. */
			$this->WP_Widget(
				'hybrid-nav-menu',				// $this->id_base
				__( 'Navigation Menu', THEME_DOMAIN ),	// $this->name
				$widget_options,				// $this->widget_options
				$control_options				// $this->control_options
			);
		}
		/**
		* Outputs the widget based on the arguments input through the widget controls.
		*
		* @since 0.8.0
		*/
		function widget( $sidebar, $instance )
		{
			extract( $sidebar );
			/* Set the $args for wp_nav_menu() to the $instance array. */
			$args = $instance;
			/* Overwrite the $echo argument and set it to false. */
			$args['echo'] = false;
			/* Output the theme's widget wrapper. */
			echo $before_widget;
			/* If a title was input by the user, display it. */
			if( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;
			/* Output the nav menu. */
			echo str_replace( array("\r","\n","\t" ), '', wp_nav_menu( $args ) );
			/* Close the theme's widget wrapper. */
			echo $after_widget;
		}
		/**
		* Updates the widget control options for the particular instance of the widget.
		*
		* @since 0.8.0
		*/
		function update( $new_instance, $old_instance )
		{
			$instance = $old_instance;
			$instance = $new_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['depth'] = strip_tags( $new_instance['depth'] );
			$instance['container_id'] = strip_tags( $new_instance['container_id'] );
			$instance['container_class'] = strip_tags( $new_instance['container_class'] );
			$instance['menu_id'] = strip_tags( $new_instance['menu_id'] );
			$instance['menu_class'] = strip_tags( $new_instance['menu_class'] );
			$instance['fallback_cb'] = strip_tags( $new_instance['fallback_cb'] );
			$instance['walker'] = strip_tags( $new_instance['walker'] );
			return $instance;
		}
		/**
		* Displays the widget control options in the Widgets admin screen.
		*
		* @since 0.8.0
		*/
		function form( $instance )
		{
			/* Set up the default form values. */
			$defaults = array(
				'title'          => esc_attr__( 'Navigation', THEME_DOMAIN ),
				'menu'           => '',
				'container'      => 'div',
				'container_id'   => '',
				'container_class'=> '',
				'menu_id'        => '',
				'menu_class'     => 'nav-menu',
				'depth'          => 0,
				'before'         => '',
				'after'          => '',
				'link_before'    => '',
				'link_after'     => '',
				'fallback_cb'    => 'wp_page_menu',
				'walker'         => ''
			);
			/* Merge the user-selected arguments with the defaults. */
			$instance = wp_parse_args( (array) $instance, $defaults );
			$container = apply_filters( 'wp_nav_menu_container_allowedtags', array('div','nav' ) );
			?>
<div class="hybrid-widget-controls columns-2">
  <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
      <?php _e( 'Title:', THEME_DOMAIN ); ?>
    </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'menu' ); ?>"> <code> menu </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'menu' ); ?>" name="<?php echo $this->get_field_name( 'menu' ); ?>">
      <?php
						foreach( wp_get_nav_menus() as $menu )
						{
							?>
      <option value="<?php echo esc_attr( $menu->term_id ); ?>" <?php selected( $instance['menu'], $menu->term_id ); ?>> <?php echo esc_html( $menu->name ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'container' ); ?>"> <code> container </code> </label>
    <select class="smallfat" id="<?php echo $this->get_field_id( 'container' ); ?>" name="<?php echo $this->get_field_name( 'container' ); ?>">
      <?php
						foreach( $container as $option )
						{
							?>
      <option value="<?php echo esc_attr( $option ); ?>" <?php selected( $instance['container'], $option ); ?>> <?php echo esc_html( $option ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'container_id' ); ?>"> <code> container_id </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'container_id' ); ?>" name="<?php echo $this->get_field_name( 'container_id' ); ?>" value="<?php echo esc_attr( $instance['container_id'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'container_class' ); ?>"> <code> container_class </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'container_class' ); ?>" name="<?php echo $this->get_field_name( 'container_class' ); ?>" value="<?php echo esc_attr( $instance['container_class'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'menu_id' ); ?>"> <code> menu_id </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'menu_id' ); ?>" name="<?php echo $this->get_field_name( 'menu_id' ); ?>" value="<?php echo esc_attr( $instance['menu_id'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'menu_class' ); ?>"> <code> menu_class </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'menu_class' ); ?>" name="<?php echo $this->get_field_name( 'menu_class' ); ?>" value="<?php echo esc_attr( $instance['menu_class'] ); ?>" />
  </p>
</div>
<div class="hybrid-widget-controls columns-2 column-last">
  <p>
    <label for="<?php echo $this->get_field_id( 'depth' ); ?>"> <code> depth </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'depth' ); ?>" name="<?php echo $this->get_field_name( 'depth' ); ?>" value="<?php echo esc_attr( $instance['depth'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'before' ); ?>"> <code> before </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'before' ); ?>" name="<?php echo $this->get_field_name( 'before' ); ?>" value="<?php echo esc_attr( $instance['before'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'after' ); ?>"> <code> after </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'after' ); ?>" name="<?php echo $this->get_field_name( 'after' ); ?>" value="<?php echo esc_attr( $instance['after'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'link_before' ); ?>"> <code> link_before </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'link_before' ); ?>" name="<?php echo $this->get_field_name( 'link_before' ); ?>" value="<?php echo esc_attr( $instance['link_before'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'link_after' ); ?>"> <code> link_after </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'link_after' ); ?>" name="<?php echo $this->get_field_name( 'link_after' ); ?>" value="<?php echo esc_attr( $instance['link_after'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'fallback_cb' ); ?>"> <code> fallback_cb </code> </label>
    <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'fallback_cb' ); ?>" name="<?php echo $this->get_field_name( 'fallback_cb' ); ?>" value="<?php echo esc_attr( $instance['fallback_cb'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'walker' ); ?>"> <code> walker </code> </label>
    <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'walker' ); ?>" name="<?php echo $this->get_field_name( 'walker' ); ?>" value="<?php echo esc_attr( $instance['walker'] ); ?>" />
  </p>
</div>
<?php
		}
	}
}
/*-------------------------------------------------------------
* Pages Widget Class
--------------------------------------------------------------*/
if(!class_exists('supreme_pages_widget'))
{
	class supreme_pages_widget extends WP_Widget
	{
		/**
		* Set up the widget's unique name, ID, class, description, and other options.
		*
		* @since 1.2.0
		*/
		function __construct()
		{
			/* Set up the widget options. */
			$widget_options = array(
				'classname'  => 'pages',
				'description'=> esc_html__( 'An advanced widget that gives you total control over the output of your page links.', THEME_DOMAIN )
			);
			/* Set up the widget control options. */
			$control_options = array(
				'height'=> 350
			);
			/* Create the widget. */
			$this->WP_Widget(
				'hybrid-pages',			// $this->id_base
				__( 'Pages', THEME_DOMAIN),		// $this->name
				$widget_options,			// $this->widget_options
				$control_options			// $this->control_options
			);
		}
		/**
		* Outputs the widget based on the arguments input through the widget controls.
		*
		* @since 0.6.0
		*/
		function widget( $sidebar, $instance )
		{
			extract( $sidebar );
			/* Set the $args for wp_list_pages() to the $instance array. */
			$args = $instance;
			/* Set the $title_li and $echo to false. */
			$args['title_li'] = false;
			$args['echo'] = false;
			/* Open the output of the widget. */
			echo $before_widget;
			/* If a title was input by the user, display it. */
			if( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;
			/* Output the page list. */
			echo '<ul class="xoxo pages">' . str_replace( array("\r","\n","\t" ), '', wp_list_pages( $args ) ) . '</ul>';
			/* Close the output of the widget. */
			echo $after_widget;
		}
		/**
		* Updates the widget control options for the particular instance of the widget.
		*
		* @since 0.6.0
		*/
		function update( $new_instance, $old_instance )
		{
			$instance = $old_instance;
			/* Set the instance to the new instance. */
			$instance = $new_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['depth'] = strip_tags( $new_instance['depth'] );
			$instance['child_of'] = strip_tags( $new_instance['child_of'] );
			$instance['meta_key'] = strip_tags( $new_instance['meta_key'] );
			$instance['meta_value'] = strip_tags( $new_instance['meta_value'] );
			$instance['date_format'] = strip_tags( $new_instance['date_format'] );
			$instance['number'] = strip_tags( $new_instance['number'] );
			$instance['offset'] = strip_tags( $new_instance['offset'] );
			$instance['include'] = preg_replace( '/[^0-9,]/', '', $new_instance['include'] );
			$instance['exclude'] = preg_replace( '/[^0-9,]/', '', $new_instance['exclude'] );
			$instance['exclude_tree'] = preg_replace( '/[^0-9,]/', '', $new_instance['exclude_tree'] );
			$instance['authors'] = preg_replace( '/[^0-9,]/', '', $new_instance['authors'] );
			$instance['post_type'] = $new_instance['post_type'];
			$instance['sort_column'] = $new_instance['sort_column'];
			$instance['sort_order'] = $new_instance['sort_order'];
			$instance['show_date'] = $new_instance['show_date'];
			$instance['link_before'] = $new_instance['link_before'];
			$instance['link_after'] = $new_instance['link_after'];
			$instance['hierarchical'] = ( isset( $new_instance['hierarchical'] ) ? 1 : 0 );
			return $instance;
		}
		/**
		* Displays the widget control options in the Widgets admin screen.
		*
		* @since 0.6.0
		*/
		function form( $instance )
		{
			/* Set up the default form values. */
			$defaults = array(
				'title'       => esc_attr__( 'Pages', THEME_DOMAIN),
				'post_type'   => 'page',
				'depth'       => 0,
				'number'      => '',
				'offset'      => '',
				'child_of'    => '',
				'include'     => '',
				'exclude'     => '',
				'exclude_tree'=> '',
				'meta_key'    => '',
				'meta_value'  => '',
				'authors'     => '',
				'link_before' => '',
				'link_after'  => '',
				'show_date'   => '',
				'hierarchical'=> true,
				'sort_column' => 'post_title',
				'sort_order'  => 'ASC',
				'date_format' => get_option( 'date_format' )
			);
			/* Merge the user-selected arguments with the defaults. */
			$instance = wp_parse_args( (array) $instance, $defaults );
			$post_types = get_post_types( array('public'      => true,'hierarchical'=> true ), 'objects' );
			$sort_order = array('ASC' => esc_attr__( 'Ascending', THEME_DOMAIN ),'DESC'=> esc_attr__( 'Descending', THEME_DOMAIN ) );
			$sort_column = array('post_author'  => esc_attr__( 'Author', THEME_DOMAIN ),'post_date'    => esc_attr__( 'Date', THEME_DOMAIN ),'ID'           => esc_attr__( 'ID', THEME_DOMAIN ),'menu_order'   => esc_attr__( 'Menu Order', THEME_DOMAIN ),'post_modified'=> esc_attr__( 'Modified', THEME_DOMAIN ),'post_name'    => esc_attr__( 'Slug', THEME_DOMAIN ),'post_title'   => esc_attr__( 'Title', THEME_DOMAIN ) );
			$show_date = array(''        => '','created' => esc_attr__( 'Created', THEME_DOMAIN ),'modified'=> esc_attr__( 'Modified', THEME_DOMAIN ) );
			$meta_key = array_merge( array('' ), (array) get_meta_keys() );
			?>
<div class="hybrid-widget-controls columns-3">
  <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
      <?php _e( 'Title:', THEME_DOMAIN ); ?>
    </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'post_type' ); ?>"> <code> post_type </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
      <?php
						foreach( $post_types as $post_type )
						{
							?>
      <option value="<?php echo esc_attr( $post_type->name ); ?>" <?php selected( $instance['post_type'], $post_type->name ); ?>> <?php echo esc_html( $post_type->labels->singular_name ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'sort_order' ); ?>"> <code> sort_order </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'sort_order' ); ?>" name="<?php echo $this->get_field_name( 'sort_order' ); ?>">
      <?php
						foreach( $sort_order as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['sort_order'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'sort_column' ); ?>"> <code> sort_column </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'sort_column' ); ?>" name="<?php echo $this->get_field_name( 'sort_column' ); ?>">
      <?php
						foreach( $sort_column as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['sort_column'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'depth' ); ?>"> <code> depth </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'depth' ); ?>" name="<?php echo $this->get_field_name( 'depth' ); ?>" value="<?php echo esc_attr( $instance['depth'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'number' ); ?>"> <code> number </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $instance['number'] ); ?>" />
  </p>
</div>
<div class="hybrid-widget-controls columns-3">
  <p>
    <label for="<?php echo $this->get_field_id( 'offset' ); ?>"> <code> offset </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'offset' ); ?>" name="<?php echo $this->get_field_name( 'offset' ); ?>" value="<?php echo esc_attr( $instance['offset'] ); ?>"  />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'child_of' ); ?>"> <code> child_of </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'child_of' ); ?>" name="<?php echo $this->get_field_name( 'child_of' ); ?>" value="<?php echo esc_attr( $instance['child_of'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'include' ); ?>"> <code> include </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" value="<?php echo esc_attr( $instance['include'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'exclude' ); ?>"> <code> exclude </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo esc_attr( $instance['exclude'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'exclude_tree' ); ?>"> <code> exclude_tree </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'exclude_tree' ); ?>" name="<?php echo $this->get_field_name( 'exclude_tree' ); ?>" value="<?php echo esc_attr( $instance['exclude_tree'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'meta_key' ); ?>"> <code> meta_key </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'meta_key' ); ?>" name="<?php echo $this->get_field_name( 'meta_key' ); ?>">
      <?php
						foreach( $meta_key as $meta )
						{
							?>
      <option value="<?php echo esc_attr( $meta ); ?>" <?php selected( $instance['meta_key'], $meta ); ?>> <?php echo esc_html( $meta ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'meta_value' ); ?>"> <code> meta_value </code> </label>
    <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'meta_value' ); ?>" name="<?php echo $this->get_field_name( 'meta_value' ); ?>" value="<?php echo esc_attr( $instance['meta_value'] ); ?>" />
  </p>
</div>
<div class="hybrid-widget-controls columns-3 column-last">
  <p>
    <label for="<?php echo $this->get_field_id( 'authors' ); ?>"> <code> authors </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'authors' ); ?>" name="<?php echo $this->get_field_name( 'authors' ); ?>" value="<?php echo esc_attr( $instance['authors'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'link_before' ); ?>"> <code> link_before </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'link_before' ); ?>" name="<?php echo $this->get_field_name( 'link_before' ); ?>" value="<?php echo esc_attr( $instance['link_before'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'link_after' ); ?>"> <code> link_after </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'link_after' ); ?>" name="<?php echo $this->get_field_name( 'link_after' ); ?>" value="<?php echo esc_attr( $instance['link_after'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"> <code> show_date </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>">
      <?php
						foreach( $show_date as $option_value => $option_label )
						{
							?>
      <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['show_date'], $option_value ); ?>> <?php echo esc_html( $option_label ); ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'date_format' ); ?>"> <code> date_format </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'date_format' ); ?>" name="<?php echo $this->get_field_name( 'date_format' ); ?>" value="<?php echo esc_attr( $instance['date_format'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['hierarchical'], true ); ?> id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>" />
      <?php _e( 'Hierarchical?', THEME_DOMAIN); ?>
      <code> hierarchical </code> </label>
  </p>
</div>
<div style="clear:both;"> &nbsp; </div>
<?php
		}
	}
}
/*--------------------------------------------------------
* Search Widget Class
----------------------------------------------------------*/
if(!class_exists('supreme_search_widget'))
{
	class supreme_search_widget extends WP_Widget
	{
		/**
		* Set up the widget's unique name, ID, class, description, and other options.
		*
		* @since 1.2.0
		*/
		function __construct()
		{
			/* Set up the widget options. */
			$widget_options = array(
				'classname'  => 'search',
				'description'=> esc_html__( 'An advanced widget that gives you total control over the output of your search form.', THEME_DOMAIN )
			);
			/* Set up the widget control options. */
			/* Create the widget. */
			$this->WP_Widget(
				'hybrid-search',			// $this->id_base
				__( 'Search', THEME_DOMAIN ),	// $this->name
				@$widget_options,			// $this->widget_options
				@$control_options			// $this->control_options
			);
		}
		/**
		* Outputs the widget based on the arguments input through the widget controls.
		*
		* @since 0.6.0
		*/
		function widget( $sidebar, $instance )
		{
			extract( $sidebar );
			/* Output the theme's $before_widget wrapper. */
			echo $before_widget;
			/* If a title was input by the user, display it. */
			if( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;
			/* If the user chose to use the theme's search form, load it. */
			if( !empty( $instance['theme_search'] ) )
			{
				get_search_form();
			}
			/* Else, create the form based on the user-selected arguments. */
			else
			{
				/* Set up some variables for the search form. */
				if( empty( $instance['search_text'] ) )
				$instance['search_text'] = '';
				$search_text = ( ( is_search() ) ? esc_attr( get_search_query() ) : esc_attr( $instance['search_text'] ) );
				/* Open the form. */
				$search      = '<form method="get" class="search-form" id="search-form' . esc_attr( $this->id_base ) . '" action="' . home_url() . '/"><div>';
				/* If a search label was set, add it. */
				if( !empty( $instance['search_label'] ) )
				$search .= '<label for="search-text' . esc_attr( $this->id_base ) . '">' . $instance['search_label'] . '</label>';
				/* Search form text input. */
				$search .= '<input class="search-text" type="text" name="s" id="search-text' . esc_attr( $this->id_base ) . '" value="' . $search_text . '" onfocus="if(this.value==this.defaultValue)this.value=\'\';" onblur="if(this.value==\'\')this.value=this.defaultValue;" />';
				/* Search form submit button. */
				if( isset($instance['search_submit'] ) && $instance['search_submit'] )
				$search .= '<input class="search-submit button" name="submit" type="submit" id="search-submit' . esc_attr( $this->id_base ). '" value="' . esc_attr( $instance['search_submit'] ) . '" />';
				/* Close the form. */
				$search .= '</div></form>';
				/* Display the form. */
				echo $search;
			}
			/* Close the theme's widget wrapper. */
			echo $after_widget;
		}
		/**
		* Updates the widget control options for the particular instance of the widget.
		*
		* @since 0.6.0
		*/
		function update( $new_instance, $old_instance )
		{
			$instance = $new_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['search_label'] = strip_tags( $new_instance['search_label'] );
			$instance['search_text'] = strip_tags( $new_instance['search_text'] );
			$instance['search_submit'] = strip_tags( $new_instance['search_submit'] );
			$instance['theme_search'] = ( isset( $new_instance['theme_search'] ) ? 1 : 0 );
			return $instance;
		}
		/**
		* Displays the widget control options in the Widgets admin screen.
		*
		* @since 0.6.0
		*/
		function form( $instance )
		{
			/* Set up the default form values. */
			$defaults = array(
				'title'        => esc_attr__( 'Search', THEME_DOMAIN ),
				'theme_search' => false,
				'search_label' => '',
				'search_text'  => '',
				'search_submit'=> ''
			);
			/* Merge the user-selected arguments with the defaults. */
			$instance = wp_parse_args( (array) $instance, $defaults ); ?>
<div class="hybrid-widget-controls columns-2">
  <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
      <?php _e( 'Title:', THEME_DOMAIN ); ?>
    </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'search_label' ); ?>">
      <?php _e( 'Search Label:', THEME_DOMAIN ); ?>
    </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'search_label' ); ?>" name="<?php echo $this->get_field_name( 'search_label' ); ?>" value="<?php echo esc_attr( $instance['search_label'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'search_text' ); ?>">
      <?php _e( 'Search Text:', THEME_DOMAIN ); ?>
    </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'search_text' ); ?>" name="<?php echo $this->get_field_name( 'search_text' ); ?>" value="<?php echo esc_attr( $instance['search_text'] ); ?>" />
  </p>
</div>
<div class="hybrid-widget-controls columns-2 column-last">
  <p>
    <label for="<?php echo $this->get_field_id( 'search_submit' ); ?>">
      <?php _e( 'Search Submit:', THEME_DOMAIN ); ?>
    </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'search_submit' ); ?>" name="<?php echo $this->get_field_name( 'search_submit' ); ?>" value="<?php echo esc_attr( $instance['search_submit'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'theme_search' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['theme_search'], true ); ?> id="<?php echo $this->get_field_id( 'theme_search' ); ?>" name="<?php echo $this->get_field_name( 'theme_search' ); ?>" />
      <?php _e( 'Use theme\'s <code>searchform.php</code>?', THEME_DOMAIN ); ?>
    </label>
  </p>
</div>
<div style="clear:both;"> &nbsp; </div>
<?php
		}
	}
}
/*---------------------------------------------------------------
* Tags Widget Class
----------------------------------------------------------------*/
if(!class_exists('supreme_tags_widget'))
{
	class supreme_tags_widget extends WP_Widget
	{
		/*
		* Set up the widget's unique name, ID, class, description, and other options.
		*/
		function __construct()
		{
			/* Set up the widget options. */
			$widget_options = array(
				'classname'  => 'tags',
				'description'=> esc_html__( 'An advanced widget that gives you total control over the output of your tags.', THEME_DOMAIN )
			);
			/* Set up the widget control options. */
			$control_options = array(
				'height'=> 350
			);
			/* Create the widget. */
			$this->WP_Widget(
				'hybrid-tags',			// $this->id_base
				__( 'Tags', THEME_DOMAIN ),		// $this->name
				$widget_options,			// $this->widget_options
				$control_options			// $this->control_options
			);
		}
		/**
		* Outputs the widget based on the arguments input through the widget controls.
		*
		* @since 0.6.0
		*/
		function widget( $sidebar, $instance )
		{
			extract( $sidebar );
			/* Set the $args for wp_tag_cloud() to the $instance array. */
			$args = $instance;
			/* Make sure empty callbacks aren't passed for custom functions. */
			$args['topic_count_text_callback'] = !empty( $args['topic_count_text_callback'] ) ? $args['topic_count_text_callback'] : 'default_topic_count_text';
			$args['topic_count_scale_callback'] = !empty( $args['topic_count_scale_callback'] ) ? $args['topic_count_scale_callback'] : 'default_topic_count_scale';
			/* If the separator is empty, set it to the default new line. */
			$args['separator'] = !empty( $args['separator'] ) ? $args['separator'] : "\n";
			/* Overwrite the echo argument. */
			$args['echo'] = false;
			/* Output the theme's $before_widget wrapper. */
			echo $before_widget;
			/* If a title was input by the user, display it. */
			if( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;
			/* Get the tag cloud. */
			$tags = str_replace( array("\r","\n","\t" ), ' ', wp_tag_cloud( $args ) );
			/* If $format should be flat, wrap it in the <p> element. */
			if( 'flat' == $instance['format'] )
			$tags = '<p class="' . sanitize_html_class( "{$instance['taxonomy']}-cloud" ) . ' term-cloud">' . $tags . '</p>';
			/* Output the tag cloud. */
			echo $tags;
			/* Close the theme's widget wrapper. */
			echo $after_widget;
		}
		/**
		* Updates the widget control options for the particular instance of the widget.
		*/
		function update( $new_instance, $old_instance )
		{
			$instance = $old_instance;
			/* Set the instance to the new instance. */
			$instance = $new_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['smallest'] = strip_tags( $new_instance['smallest'] );
			$instance['largest'] = strip_tags( $new_instance['largest'] );
			$instance['number'] = strip_tags( $new_instance['number'] );
			$instance['separator'] = strip_tags( $new_instance['separator'] );
			$instance['name__like'] = strip_tags( $new_instance['name__like'] );
			$instance['search'] = strip_tags( $new_instance['search'] );
			$instance['child_of'] = strip_tags( $new_instance['child_of'] );
			$instance['parent'] = strip_tags( $new_instance['parent'] );
			$instance['topic_count_text_callback'] = strip_tags( $new_instance['topic_count_text_callback'] );
			$instance['topic_count_scale_callback'] = strip_tags( $new_instance['topic_count_scale_callback'] );
			$instance['include'] = preg_replace( '/[^0-9,]/', '', $new_instance['include'] );
			$instance['exclude'] = preg_replace( '/[^0-9,]/', '', $new_instance['exclude'] );
			$instance['unit'] = $new_instance['unit'];
			$instance['format'] = $new_instance['format'];
			$instance['orderby'] = $new_instance['orderby'];
			$instance['order'] = $new_instance['order'];
			$instance['taxonomy'] = $new_instance['taxonomy'];
			$instance['link'] = $new_instance['link'];
			$instance['pad_counts'] = ( isset( $new_instance['pad_counts'] ) ? 1 : 0 );
			$instance['hide_empty'] = ( isset( $new_instance['hide_empty'] ) ? 1 : 0 );
			return $instance;
		}
		/**
		* Displays the widget control options in the Widgets admin screen.
		*/
		function form( $instance )
		{
			/* Set up the default form values. */
			$defaults = array(
				'title'                     => esc_attr__( 'Tags', THEME_DOMAIN ),
				'order'                     => 'ASC',
				'orderby'                   => 'name',
				'format'                    => 'flat',
				'include'                   => '',
				'exclude'                   => '',
				'unit'                      => 'pt',
				'smallest'                  => 8,
				'largest'                   => 22,
				'link'                      => 'view',
				'number'                    => 45,
				'separator'                 => ' ',
				'child_of'                  => '',
				'parent'                    => '',
				'taxonomy'                   => array('post_tag' ),
				'hide_empty'                => 1,
				'pad_counts'                => false,
				'search'                    => '',
				'name__like'                => '',
				'topic_count_text_callback' => 'default_topic_count_text',
				'topic_count_scale_callback'=> 'default_topic_count_scale',
			);
			/* Merge the user-selected arguments with the defaults. */
			$instance = wp_parse_args( (array) $instance, $defaults );
			/* <select> element options. */
			$taxonomies = get_taxonomies( array('show_tagcloud'=> true ), 'objects' );
			$link = array('view'=> esc_attr__( 'View', THEME_DOMAIN ),'edit'=> esc_attr__( 'Edit', THEME_DOMAIN ) );
			$format = array('flat'=> esc_attr__( 'Flat', THEME_DOMAIN ),'list'=> esc_attr__( 'List', THEME_DOMAIN ) );
			$order = array('ASC' => esc_attr__( 'Ascending', THEME_DOMAIN ),'DESC'=> esc_attr__( 'Descending', THEME_DOMAIN ),'RAND'=> esc_attr__( 'Random', THEME_DOMAIN ) );
			$orderby = array('count'=> esc_attr__( 'Count', THEME_DOMAIN ),'name' => esc_attr__( 'Name', THEME_DOMAIN ) );
			$unit = array('pt'=> 'pt','px'=> 'px','em'=> 'em','%' => '%' );
			?>
<div class="hybrid-widget-controls columns-3">
  <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
      <?php _e( 'Title:', THEME_DOMAIN ); ?>
    </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"> <code> taxonomy </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>[]" size="4" multiple="multiple">
      <?php
						foreach( $taxonomies as $taxonomy )
						{
							?>
      <option value="<?php echo $taxonomy->name; ?>" <?php selected( in_array( $taxonomy->name, (array)$instance['taxonomy'] ) ); ?>> <?php echo $taxonomy->labels->singular_name; ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'format' ); ?>"> <code> format </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>">
      <?php
						foreach( $format as $option_value => $option_label )
						{
							?>
      <option value="<?php echo $option_value; ?>" <?php selected( $instance['format'], $option_value ); ?>> <?php echo $option_label; ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'order' ); ?>"> <code> order </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
      <?php
						foreach( $order as $option_value => $option_label )
						{
							?>
      <option value="<?php echo $option_value; ?>" <?php selected( $instance['order'], $option_value ); ?>> <?php echo $option_label; ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"> <code> orderby </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
      <?php
						foreach( $orderby as $option_value => $option_label )
						{
							?>
      <option value="<?php echo $option_value; ?>" <?php selected( $instance['orderby'], $option_value ); ?>> <?php echo $option_label; ?> </option>
      <?php
						} ?>
    </select>
  </p>
</div>
<div class="hybrid-widget-controls columns-3">
  <p>
    <label for="<?php echo $this->get_field_id( 'include' ); ?>"> <code> include </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" value="<?php echo esc_attr( $instance['include'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'exclude' ); ?>"> <code> exclude </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" value="<?php echo esc_attr( $instance['exclude'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'number' ); ?>"> <code> number </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $instance['number'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'largest' ); ?>"> <code> largest </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'largest' ); ?>" name="<?php echo $this->get_field_name( 'largest' ); ?>" value="<?php echo esc_attr( $instance['largest'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'smallest' ); ?>"> <code> smallest </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'smallest' ); ?>" name="<?php echo $this->get_field_name( 'smallest' ); ?>" value="<?php echo esc_attr( $instance['smallest'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'unit' ); ?>"> <code> unit </code> </label>
    <select class="smallfat" id="<?php echo $this->get_field_id( 'unit' ); ?>" name="<?php echo $this->get_field_name( 'unit' ); ?>">
      <?php
						foreach( $unit as $option_value => $option_label )
						{
							?>
      <option value="<?php echo $option_value; ?>" <?php selected( $instance['unit'], $option_value ); ?>> <?php echo $option_label; ?> </option>
      <?php
						} ?>
    </select>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'separator' ); ?>"> <code> separator </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'separator' ); ?>" name="<?php echo $this->get_field_name( 'separator' ); ?>" value="<?php echo esc_attr( $instance['separator'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'child_of' ); ?>"> <code> child_of </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'child_of' ); ?>" name="<?php echo $this->get_field_name( 'child_of' ); ?>" value="<?php echo esc_attr( $instance['child_of'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'parent' ); ?>"> <code> parent </code> </label>
    <input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'parent' ); ?>" name="<?php echo $this->get_field_name( 'parent' ); ?>" value="<?php echo esc_attr( $instance['parent'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'link' ); ?>"> <code> link </code> </label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
      <?php
						foreach( $link as $option_value => $option_label )
						{
							?>
      <option value="<?php echo $option_value; ?>" <?php selected( $instance['link'], $option_value ); ?>> <?php echo $option_label; ?> </option>
      <?php
						} ?>
    </select>
  </p>
</div>
<div class="hybrid-widget-controls columns-3 column-last">
  <p>
    <label for="<?php echo $this->get_field_id( 'search' ); ?>"> <code> search </code> </label>
    <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'search' ); ?>" name="<?php echo $this->get_field_name( 'search' ); ?>" value="<?php echo esc_attr( $instance['search'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'name__like' ); ?>"> <code> name__like </code> </label>
    <input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'name__like' ); ?>" name="<?php echo $this->get_field_name( 'name__like' ); ?>" value="<?php echo esc_attr( $instance['name__like'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'topic_count_text_callback' ); ?>"> <code> topic_count_text_callback </code> </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'topic_count_text_callback' ); ?>" name="<?php echo $this->get_field_name( 'topic_count_text_callback' ); ?>" value="<?php echo esc_attr( $instance['topic_count_text_callback'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'topic_count_scale_callback' ); ?>"> <code> topic_count_scale_callback </code> </label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'topic_count_scale_callback' ); ?>" name="<?php echo $this->get_field_name( 'topic_count_scale_callback' ); ?>" value="<?php echo esc_attr( $instance['topic_count_scale_callback'] ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'pad_counts' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['pad_counts'], true ); ?> id="<?php echo $this->get_field_id( 'pad_counts' ); ?>" name="<?php echo $this->get_field_name( 'pad_counts' ); ?>" />
      <?php _e( 'Pad counts?', THEME_DOMAIN ); ?>
      <code> pad_counts </code> </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>">
      <input class="checkbox" type="checkbox" <?php checked( $instance['hide_empty'], true ); ?> id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" />
      <?php _e( 'Hide empty?', THEME_DOMAIN ); ?>
      <code> hide_empty </code> </label>
  </p>
</div>
<div style="clear:both;"> &nbsp; </div>
<?php
		}
	}
}
/**----------------------------------------------------------------------------------------------------------------
* The Google Map widget displays the google map to user. Users will able to see their own address on goole map.
------------------------------------------------------------------------------------------------------------------ */
if(!class_exists('supreme_google_map')){
	class supreme_google_map extends WP_Widget
	{
		function __construct()
		{
			//Constructor
			$description         = esc_html__( 'Display a specific location on the map. Works in almost all widget areas.', THEME_DOMAIN );
			$default_description = apply_filters('google_map_widget_description',$description);
			$widget_options      = array(
				'classname'  => 'googlemap',
				'description'=> apply_filters('google_map_description',$default_description)
			);
			/* Set up the widget control options. */
			$control_options = array(
				'height'=> 350
			);
			/* Create the widget. */
			$this->WP_Widget(
				'templatic_google_map',		// $this->id_base
				__( 'T &rarr; Google Map Location', THEME_DOMAIN ),	// $this->name
				$widget_options,			// $this->widget_options
				$control_options			// $this->control_options
			);
		}
		function widget($args, $instance)
		{
			// prints the widget
			extract($args, EXTR_SKIP);
			$title = empty($instance['title']) ? __("Find Us On Map",THEME_DOMAIN) : apply_filters('widget_title', $instance['title']);
			$address_latitude = empty($instance['address_latitude']) ? '0' : apply_filters('widget_address_latitude', $instance['address_latitude']);
			$address_longitude = empty($instance['address_longitude']) ? '34' : apply_filters('widget_address_longitude', $instance['address_longitude']);
			$address = empty($instance['address']) ? '' : apply_filters('widget_address', $instance['address']);
			$map_type = empty($instance['map_type']) ? 'ROADMAP' : apply_filters('widget_map_type', $instance['map_type']);
			$map_width = empty($instance['map_width']) ? 200 : apply_filters('widget_map_width', $instance['map_width']);
			$map_height = empty($instance['map_height']) ? 425 : apply_filters('widget_map_height', $instance['map_height']);
			$scale = empty($instance['scale']) ? 17 : apply_filters('widget_scale', $instance['scale']);
			echo $before_widget;
			
			if(!empty($instance['title']))
			{
				echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;
			}
			$pin_img = get_template_directory_uri().'/images/contact.png';
			if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; }
			?>
				<script type="text/javascript" src="<?php echo $http; ?>maps.google.com/maps/api/js?sensor=false"></script>
				<?php wp_enqueue_script( 'library-google-infobox', get_template_directory_uri().'/library/js/infobox.js' );	?>
				<script type="text/javascript">
				var geocoder;
				var map;
				var infobox;
				function initialize()
				{
					geocoder = new google.maps.Geocoder();
					var latlng = new google.maps.LatLng(-34.397, 150.644); var myOptions =
					{
						zoom: <?php echo $scale; ?>, center: latlng, mapTypeId: google.maps.MapTypeId.<?php echo $map_type; ?>
					}
					map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
					codeAddress();
				}
				function codeAddress()
				{
					var address = '<?php echo $address; ?>';//document.getElementById("address").value;
					geocoder.geocode(
						{
							'address': address
						}, function(results, status)
						{
							if (status == google.maps.GeocoderStatus.OK)
							{
								map.setCenter(results[0].geometry.location); var marker = new google.maps.Marker(
									{
										map: map, position: results[0].geometry.location,
										icon: '<?php echo $pin_img; ?>',
									}); //Start
								var boxText = document.createElement("div");
								boxText.innerHTML ='<div class=google-map-info><div class=map-inner-wrapper><div class="map-item-info"><p><?php echo $address;?></p></div><div class=map-arrow></div></div></div>';
								var myinfoOptions =
								{
									content: boxText,disableAutoPan: false,maxWidth: 0 ,pixelOffset: new google.maps.Size(-118, -175) ,zIndex: null ,boxStyle:
									{
										opacity: 1 ,width: "240px",
										background:'white',
									},closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif"
									,infoBoxClearance: new google.maps.Size(1, 1)
									,isHidden: false
									,pane: "floatPane"
									,enableEventPropagation: false
								};
								infobox = new InfoBox(myinfoOptions);
								infobox.open(map, marker);
								
								google.maps.event.addListener(marker, 'click', function() {
									infobox.open(map, marker);
								});
								infobox.open(map, marker);
								
								//End
							} else
							{
								alert("Geocode was not successful for the following reason: " + status);
							}
						});
				}
				google.maps.event.addDomListener(window, 'load', initialize);
			</script>
<div class="wid_gmap graybox">
  <div id="map-canvas" style="height:<?php echo $map_height; ?>px; "> </div>
</div>
<?php
			echo $after_widget;
		}
		function update($new_instance, $old_instance)
		{
			//save the widget
			$instance = $old_instance;
			$instance['title'] = ($new_instance['title']);
			$instance['address'] = ($new_instance['address']);
			$instance['address_latitude'] = strip_tags($new_instance['address_latitude']);
			$instance['address_longitude'] = strip_tags($new_instance['address_longitude']);
			$instance['map_width'] = strip_tags($new_instance['map_width']);
			$instance['map_height'] = strip_tags($new_instance['map_height']);
			$instance['map_type'] = strip_tags($new_instance['map_type']);
			$instance['scale'] = strip_tags($new_instance['scale']);
			return $instance;
		}
		function form($instance)
		{
			//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array('title'=> __("Find Us On Map",THEME_DOMAIN)) );
			$title   = ($instance['title']);
			$address = (isset($instance['address'])) ? ($instance['address']) : '230 Vine Street And locations throughout Old City, Philadelphia, PA 19106';
			$address_latitude = (isset($instance['address'])) ? strip_tags($instance['address_latitude']) : '';
			$address_longitude = (isset($instance['address'])) ? strip_tags($instance['address_longitude']) : '';
			$map_width = (isset($instance['address'])) ? strip_tags($instance['map_width']) : '';
			$map_height = (isset($instance['address'])) ? strip_tags($instance['map_height']) : 425;
			$map_type = (isset($instance['address'])) ? strip_tags($instance['map_type']) : 'ROADMAP';
			$scale = (isset($instance['address'])) ? strip_tags($instance['scale']) : 17;
			?>
<p>
  <label for="<?php  echo $this->get_field_id('title'); ?>">
    <?php _e('Title',THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php  echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('address'); ?>">
    <?php  _e('Address: <small>(eg: 230 Vine Street And locations throughout Old City, Philadelphia, PA 19106)</small>',THEME_DOMAIN);?>
    <input type="text" class="widefat" rows="6" cols="20" id="<?php echo $this->get_field_id('address'); ?>" name="<?php echo $this->get_field_name('address'); ?>"  value="<?php echo esc_attr($address); ?>">
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('map_height'); ?>">
    <?php  _e('Map Height in pixcels: <small>(To change, only enter a numeric value)</small>',THEME_DOMAIN);?>
    <input type="text" class="widefat" rows="6" cols="20" id="<?php echo $this->get_field_id('map_height'); ?>" name="<?php echo $this->get_field_name('map_height'); ?>" value="<?php echo esc_attr($map_height); ?>">
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('scale'); ?>">
    <?php  _e('Map Zoom Factor',THEME_DOMAIN);?>
    :
    <select id="<?php echo $this->get_field_id('scale'); ?>" name="<?php echo $this->get_field_name('scale'); ?>">
      <?php
						for($i = 3;$i < 20;$i++){
							?>
      <option value="<?php echo $i;?>" <?php
 if(esc_attr($scale) == $i)
{echo 'selected="selected"';}?> > <?php echo $i;;?> </option>
      <?php
						}
						?>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('map_type'); ?>">
    <?php  _e('Select Map Type',THEME_DOMAIN);?>
    :
    <select id="<?php echo $this->get_field_id('map_type'); ?>" name="<?php echo $this->get_field_name('map_type'); ?>">
      <option value="ROADMAP" <?php
 if(esc_attr($map_type) == 'ROADMAP')
{echo 'selected="selected"';}?> >
      <?php  _e('Road Map',THEME_DOMAIN);?>
      </option>
      <option value="SATELLITE" <?php
 if(esc_attr($map_type) == 'SATELLITE')
{echo 'selected="selected"';}?>>
      <?php  _e('Satellite Map',THEME_DOMAIN);?>
      </option>
    </select>
  </label>
</p>
<?php
		}
	}
}
/*-------------------------------------------------------------------
Social widget START
--------------------------------------------------------------------*/
if(!class_exists('supreme_social_media'))
{
	class supreme_social_media extends WP_Widget
	{
		function supreme_social_media()
		{
			//Constructor
			$widget_ops = array('classname'  => 'widget social_media','description'=> apply_filters('supreme_social_media_description',__('Add icons and links to your social media accounts. Works in header, footer, main content and sidebar widget areas.',THEME_DOMAIN)) );
			$this->WP_Widget('social_media', __('T &rarr; Social Media',THEME_DOMAIN), $widget_ops);
		}
		function widget($args, $instance)
		{
			// prints the widget
			extract($args, EXTR_SKIP);
			echo $before_widget;
			echo '<div class="social_media" >';
			$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
			$social_description = empty($instance['social_description']) ? '' : apply_filters('widget_title', $instance['social_description']);
			$social_link = empty($instance['social_link']) ? '' : apply_filters('widget_social_link', $instance['social_link']);
			$social_icon = empty($instance['social_icon']) ? '' : apply_filters('widget_social_icon', $instance['social_icon']);
			$social_text = empty($instance['social_text']) ? '' : apply_filters('widget_social_text', $instance['social_text']);
			if(function_exists('icl_register_string'))
			{
				icl_register_string(THEME_DOMAIN,'social_media_title',$title);
				$title              = icl_t(THEME_DOMAIN,'social_media_title',$title);
				icl_register_string(THEME_DOMAIN,'social_description',$social_description);
				$social_description = icl_t(THEME_DOMAIN,'social_description',$social_description);
			}
			if($title != "")
			{
				echo $before_title;
				echo $title;
				echo $after_title;
			}
			if($social_description != ""): ?>
<p class="social_description"> <?php echo stripcslashes($social_description);?> </p>
<?php endif;?>
<div class="social_media">
  <ul class="social_media_list">
    <?php
					for($c = 0; $c < count($social_icon); $c++)
					{
						if(function_exists('icl_register_string'))
						{
							icl_register_string(THEME_DOMAIN,@$social_text[$c],@$social_text[$c]);
							$social_text[$c] = icl_t(THEME_DOMAIN,@$social_text[$c],@$social_text[$c]);
						}
						?>
    <li> <a href="<?php echo @$social_link[$c]; ?>" target="_blank" >
      <?php
								if( @$social_icon[$c] != ''):?>
      <span class="social_icon"> <img src="<?php echo @$social_icon[$c];?>" alt="<?php echo sprintf(__('%s',THEME_DOMAIN), @$social_text[$c]);?>" /> </span>
      <?php endif;?>
      <?php echo (isset($social_text[$c]))? sprintf(__('%s',THEME_DOMAIN), @$social_text[$c]):'';?> </a> </li>
    <?php
					}
					?>
  </ul>
</div>
<?php
			echo '</div>';
			echo $after_widget;
		}
		function update($new_instance, $old_instance)
		{
			//save the widget
			return $new_instance;
		}
		function form($instance)
		{
			//widgetform in backend
			$instance = wp_parse_args((array) $instance, array('title' => 'Connect To Us','social_description'=> 'Find Us On Social Sites','social_link'=> array('http://facebook.com/templatic','http://twitter.com/templatic','http://www.youtube.com/user/templatic'),'social_icon'=> array(get_template_directory_uri().'/images/facebook.png',get_template_directory_uri().'/images/twitter.png',get_template_directory_uri().'/images/youtube.png'),'social_text'=>''));
			$title              = strip_tags($instance['title']);
			$social_description = strip_tags($instance['social_description']);
			$social_link1       = ($instance['social_link']);
			$social_icon1       = ($instance['social_icon']);
			$social_text1       = ($instance['social_text']);
			global $social_link,$social_icon,$social_text;
			$text_social_link = $this->get_field_name('social_link');
			$text_social_icon = $this->get_field_name('social_icon');
			$text_social_text = $this->get_field_name('social_text');
			?>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>">
    <?php _e('Title',THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('social_description'); ?>">
    <?php _e('Description',THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php echo $this->get_field_id('social_description'); ?>" name="<?php echo $this->get_field_name('social_description'); ?>" type="text" value="<?php echo esc_attr($social_description); ?>" />
  </label>
</p>
<p> <i> Please enter full URL to your profiles. </i> </p>
<p>
  <label for="<?php echo $this->get_field_id('social_link'); ?>">
    <?php _e('Social Link',THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php echo $this->get_field_id('social_link'); ?>" name="<?php echo $text_social_link; ?>[]" type="text" value="<?php echo esc_attr( @$social_link1[0]); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('social_icon'); ?>">
    <?php _e('Social Icon',THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php echo $this->get_field_id('social_icon'); ?>" name="<?php echo $text_social_icon; ?>[]" type="text" value="<?php echo esc_attr( @$social_icon1[0]); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('social_text1'); ?>">
    <?php _e('Social Text',THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php echo $this->get_field_id('social_text1'); ?>" name="<?php echo $text_social_text; ?>[]" type="text" value="<?php echo esc_attr( @$social_text1[0]); ?>" />
  </label>
</p>
<div id="social_tGroup" class="social_tGroup">
  <?php
				for($i = 1;$i < count($social_link1);$i++)
				{
					if($social_link1[$i] != "")
					{
						$j = $i + 1;
						echo '<div  class="SocialTextDiv'.$j.'">';
						echo '<p>';
						echo '<label>Social Link '.$j;
						echo '<input class="widefat" name="'.$text_social_link.'[]" type="text" value="'.esc_attr($social_link1[$i]).'" />';
						echo '</label>';
						echo '</p>';
						echo '<p>';
						echo '<label>Social Icon '.$j;
						echo ' <input type="text" class="widefat"  name="'.$text_social_icon.'[]" value="'.esc_attr($social_icon1[$i]).'">';
						echo '</label>';
						echo '</p>';
						echo '<p>';
						echo '<label>Social Text '.$j;
						echo ' <input type="text" class="widefat"  name="'.$text_social_text.'[]" value="'.esc_attr($social_text1[$i]).'">';
						echo '</label>';
						echo '</p>';
						echo '</div>';
					}
				}
				?>
</div>
<a
				href="javascript:void(0);" id="addtButton" class="addButton" onclick="social_add_tfields('<?php echo $text_social_link; ?>','<?php echo $text_social_icon; ?>','<?php echo $text_social_text; ?>');"> + Add more </a> &nbsp; | &nbsp; <a
				href="javascript:void(0);" id="removetButton" class="removeButton" onclick="social_remove_tfields();">- Remove </a>
<?php
		}
	}
	add_action('admin_head','supreme_add_script_addnew_1');
	if(!function_exists('supreme_add_script_addnew_1'))
	{
		function supreme_add_script_addnew_1()
		{
			global $social_link,$social_icon,$social_text;
			?>
<script type="application/javascript">
				var social_counter = 2;
				function social_add_tfields(name,ilname,sname)
				{
					var SocialTextDiv = jQuery(document.createElement('div')).attr("class", 'SocialTextDiv' + social_counter);
					SocialTextDiv.html('<p><label>Social Link '+ social_counter+': </label>'+'<input type="text" class="widefat" name="'+name+'[]" id="textbox' + social_counter + '" value="" /></p>');
					SocialTextDiv.append('<p><label>Social Icon '+ social_counter+': </label>'+'<input type="text" class="widefat" name="'+ilname+'[]" id="textbox' + social_counter + '" value="" ></p>');
					SocialTextDiv.append('<p><label>Social Text '+ social_counter+': </label>'+'<input type="text" class="widefat" name="'+sname+'[]" id="textbox' + social_counter + '" value="" ></p>');
					SocialTextDiv.appendTo(".social_tGroup");
					social_counter++;
				}
				function social_remove_tfields()
				{
					if(social_counter-1==1)
					{
						alert("you need one textbox required.");
						return false;
					}
					social_counter--;
					jQuery(".SocialTextDiv" + social_counter).remove();
				}
			</script>
<?php
		}
	}
}
/* -------------------------------------------------
Subscriber widget
---------------------------------------------------*/
if(!class_exists('supreme_subscriber_widget'))
{
	class supreme_subscriber_widget extends WP_Widget
	{
		function supreme_subscriber_widget()
		{
			//Constructor
			$widget_ops = array('classname'  => 'widget Newsletter subscribe','description'=> apply_filters('supreme_subscriber_widget_title',__('Shows a subscribe box with which users can subscribe to your newsletter. Works best in main content, subsidiary and footer areas.',THEME_DOMAIN) ));
			$this->WP_Widget('supreme_subscriber_widget',apply_filters('subscribewidget_filter',__('T &rarr; Newsletter',THEME_DOMAIN)), $widget_ops);
		}
		function widget($args, $instance)
		{
			// prints the widget
			extract($args, EXTR_SKIP);
			global $mailchimp_api_key,$mailchimp_list_id;
			$feedburner_id = empty($instance['feedburner_id']) ? '' : apply_filters('widget_feedburner_id', $instance['feedburner_id']);
			$title = empty($instance['title']) ? __("Subscribe To Newsletter",THEME_DOMAIN) : apply_filters('widget_title', $instance['title']);
			$text = empty($instance['text']) ? __("Subscribe to get our latest news",THEME_DOMAIN) : apply_filters('widget_text', $instance['text']);
			$newsletter_provider = empty($instance['newsletter_provider']) ? 'feedburner' : apply_filters('widget_newsletter_provider', $instance['newsletter_provider']);
			$mailchimp_api_key = empty($instance['mailchimp_api_key']) ? '' : apply_filters('widget_mailchimp_api_key', $instance['mailchimp_api_key']);
			$mailchimp_list_id = empty($instance['mailchimp_list_id']) ? '' : apply_filters('widget_mailchimp_list_id', $instance['mailchimp_list_id']);
			$aweber_list_name = empty($instance['aweber_list_name']) ? '' : apply_filters('widget_aweber_list_name', $instance['aweber_list_name']);
			$feedblitz_list_id = empty($instance['feedblitz_list_id']) ? '' : apply_filters('widget_feedblitz_list_id', $instance['feedblitz_list_id']);
			add_action('wp_footer','attach_mailchimp_js');
			echo $before_widget;
			?>
<div class="subscribe newsletter_subscribe_footer_widget">
  <div class="subscribe_wall">
    <?php
					if(function_exists('icl_register_string'))
					{
						icl_register_string(THEME_DOMAIN,$title,$title);
						$title1 = icl_t(THEME_DOMAIN,$title,$title);
					}
					else
					{
						$title1 = $title;
					}
					if($title1 && current_theme_supports('newsletter_title_abodediv'))
					{
						?>
    <h3 class="widget-title"> <?php echo $title1; ?> </h3>
    <?php
					}  ?>
    <div class="subscribe_cont">
      <?php
						if($title1 && !current_theme_supports('newsletter_title_abodediv'))
						{
							?>
      <h3 class="widget-title"> <?php echo $title1; ?> </h3>
      <?php
						}
						if(function_exists('icl_register_string'))
						{
							icl_register_string(THEME_DOMAIN,$text,$text);
							$text1 = icl_t(THEME_DOMAIN,$text,$text);
						}
						else
						{
							$text1 = $text;
						}
						?>
      <?php
						if($text1)
						{
							?>
      <p> <?php echo $text1; ?> </p>
      <?php
						}?>
      <span class="newsletter_msg" id="newsletter_msg"> </span>
      <?php
						if($newsletter_provider == 'feedburner')
						{
							?>
      <form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $feedburner_id; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true" >
        <input type="text" id="newsletter_name" name="name" value="<?php _e('Your Name',THEME_DOMAIN);?>" class="field" onfocus="if (this.value == 'Your Name') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Your Name';}"  />
        <input type="text" id="newsletter_email" name="email" value="<?php _e('Your Email Address',THEME_DOMAIN);?>" class="field" onfocus="if (this.value == 'Your Email Address') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Your Email Address';}"/>
        <input type="hidden" value="<?php echo $feedburner_id; ?>" name="uri"   />
        <input type="hidden" value="<?php bloginfo('name'); ?>" name="title" />
        <input type="hidden" name="loc" value="en_US"/>
        <input class="replace" type="submit" name="submit" value="<?php _e('Subscribe',THEME_DOMAIN);?>" />
      </form>
      <?php
						}
						elseif($newsletter_provider == 'mailchimp')
						{
							?>
      <input type="text" name="name" id="name" value="<?php _e('Name',THEME_DOMAIN);?>" class="field" onfocus="if (this.value == 'Name') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Name';}"  />
      <input type="text" name="email" id="email" value="" class="field" onfocus="if (this.placeholder == 'Your Email Address') {this.placeholder = '';}" onblur="if (this.placeholder == '') {this.placeholder = 'Your Email Address';}"  placeholder="<?php _e('Your Email Address',THEME_DOMAIN);?>"/>
      <input class="replace" type="submit" name="mailchimp_submit" id="mailchimp_submit" value="<?php _e('Subscribe',THEME_DOMAIN);?>" />
      <span id='process' style='display:none;'> <img src="<?php echo get_template_directory_uri().'/library/images/process.gif'; ?>" alt='Processing..' /> </span>
      <?php
						}
						elseif($newsletter_provider == 'feedblitz')
						{
							?>
      <form Method="POST" action="http://www.feedblitz.com/f/f.fbz?AddNewUserDirect" target="popupwindow" onsubmit="window.open('http://www.feedblitz.com/f/f.fbz?AddNewUserDirect', 'popupwindow', 'scrollbars=yes,width=600,height=730');return true" >
        <input type="text" name="email" id="email" value="" class="field" onfocus="if (this.placeholder == 'Your Email Address') {this.placeholder = '';}" onblur="if (this.placeholder == '') {this.placeholder = 'Your Email Address';}"  placeholder="<?php _e('Your Email Address',THEME_DOMAIN);?>"/>
        <input name="FEEDID" type="hidden" value="<?php echo $feedblitz_list_id;?>">
        <input type="submit" name="feedblitz_submit" value="<?php _e("Subscribe",THEME_DOMAIN);?>">
        <br />
      </form>
      <?php
						}
						elseif($newsletter_provider == 'aweber')
						{
							?>
      <form method="post" action="http://www.aweber.com/scripts/addlead.pl">
        <input type="hidden" name="listname" value="<?php echo $aweber_list_name;?>" />
        <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI'];?>" />
        <input type="hidden" name="meta_adtracking" value="custom form" />
        <input type="hidden" name="meta_message" value="1" />
        <input type="hidden" name="meta_required" value="name,email" />
        <input type="hidden" name="meta_forward_vars" value="1" />
        <input type="text" name="name" id="name" value="" class="field" onfocus="if (this.placeholder == 'Name') {this.placeholder = '';}" onblur="if (this.placeholder == '') {this.placeholder = 'Name';}"  placeholder="<?php _e('Name',THEME_DOMAIN);?>"/>
        <input type="text" name="email" id="email" value="" class="field" onfocus="if (this.placeholder == 'Your Email Address') {this.placeholder = '';}" onblur="if (this.placeholder == '') {this.placeholder = 'Your Email Address';}"  placeholder="<?php _e('Your Email Address',THEME_DOMAIN);?>"/>
        <input type="submit" name="aweber_submit" value="Subscribe" />
      </form>
      <?php
						}	?>
    </div>
  </div>
</div>
<!--End mc_embed_signup-->
<?php
			echo $after_widget;
		}
		function update($new_instance, $old_instance)
		{
			//save the widget			
			return $new_instance;
		}
		function form($instance)
		{
			//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array('title' => __("Subscribe To Newsletter",THEME_DOMAIN),'text' => __("Subscribe to get our latest news",THEME_DOMAIN),'newsletter_provider'=> 'feedburner','feedburner_id'      => '','mailchimp_api_key'  => '','mailchimp_list_id'  => '','aweber_list_name'   => '','feedblitz_list_id'  => '') );
			$feedburner_id       = strip_tags($instance['feedburner_id']);
			$title               = strip_tags($instance['title']);
			$text                = strip_tags($instance['text']);
			$newsletter_provider = strip_tags($instance['newsletter_provider']);
			$mailchimp_api_key   = strip_tags($instance['mailchimp_api_key']);
			$mailchimp_list_id   = strip_tags($instance['mailchimp_list_id']);
			$aweber_list_name    = strip_tags($instance['aweber_list_name']);
			$feedblitz_list_id   = strip_tags($instance['feedblitz_list_id']);?>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>">
    <?php _e('Title:',THEME_DOMAIN);?>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('text'); ?>">
    <?php _e('Text Below Title:',THEME_DOMAIN);?>
    <input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo esc_attr($text); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('newsletter_provider'); ?>">
    <?php _e("Newsletter Provider","supreme");?>
    :
    <select id="<?php echo $this->get_field_id('newsletter_provider'); ?>" name="<?php echo $this->get_field_name('newsletter_provider'); ?>" onchange="show_hide_divs(this.value,'<?php echo $this->get_field_id('feedburner_id1'); ?>','<?php echo $this->get_field_id('mailchimp_id1'); ?>','<?php echo $this->get_field_id('feedblitz_id1'); ?>','<?php echo $this->get_field_id('aweber_id1'); ?>');" >
      <option value="">
      <?php _e("Please select","supreme");?>
      </option>
      <option value="feedburner" <?php
 if("feedburner" == $newsletter_provider)
{echo "selected=selected";}?>>
      <?php _e("Feedburner","supreme");?>
      </option>
      <option value="mailchimp" <?php
 if("mailchimp" == $newsletter_provider)
{echo "selected=selected";}?>>
      <?php _e("MailChimp","supreme");?>
      </option>
      <option value="feedblitz" <?php
 if("feedblitz" == $newsletter_provider)
{echo "selected=selected";}?>>
      <?php _e("FeedBlitz","supreme");?>
      </option>
      <option value="aweber" <?php
 if("aweber" == $newsletter_provider)
{echo "selected=selected";}?>>
      <?php _e("Aweber","supreme");?>
      </option>
    </select>
  </label>
</p>
<p id="<?php echo $this->get_field_id('feedburner_id1'); ?>" style="<?php
 if($newsletter_provider == 'feedburner')
{echo 'display:block';}
else
{echo 'display:none';};?>">
  <label for="<?php echo $this->get_field_id('feedburner_id'); ?>">
    <?php _e('ID:',THEME_DOMAIN);?>
    <input class="widefat" id="<?php echo $this->get_field_id('feedburner_id'); ?>" name="<?php echo $this->get_field_name('feedburner_id'); ?>" type="text" value="<?php echo esc_attr($feedburner_id); ?>" />
  </label>
</p>
<p id="<?php echo $this->get_field_id('mailchimp_id1'); ?>" style="<?php
 if($newsletter_provider == 'mailchimp')
{echo 'display:block';}
else
{echo 'display:none';};?>">
  <label for="<?php echo $this->get_field_id('mailchimp_api_key'); ?>">
    <?php _e('Mailchimp API Key:',THEME_DOMAIN);?>
    <a href="https://us1.admin.mailchimp.com/account/api/" target="_blank"> (?) </a>
    <input class="widefat" id="<?php echo $this->get_field_id('mailchimp_api_key'); ?>" name="<?php echo $this->get_field_name('mailchimp_api_key'); ?>" type="text" value="<?php echo esc_attr($mailchimp_api_key); ?>" />
  </label>
  <label for="<?php echo $this->get_field_id('mailchimp_list_id'); ?>">
    <?php _e('List Id:',THEME_DOMAIN);?>
    <a href="http://kb.mailchimp.com/article/how-can-i-find-my-list-id" target="_blank"> (?) </a>
    <input class="widefat" id="<?php echo $this->get_field_id('mailchimp_list_id'); ?>" name="<?php echo $this->get_field_name('mailchimp_list_id'); ?>" type="text" value="<?php echo esc_attr($mailchimp_list_id); ?>" />
  </label>
</p>
<p id="<?php echo $this->get_field_id('feedblitz_id1'); ?>" style="<?php
 if($newsletter_provider == 'feedblitz')
{echo 'display:block';}
else
{echo 'display:none';};?>">
  <label for="<?php echo $this->get_field_id('feedblitz_list_id'); ?>">
    <?php _e('List ID:',THEME_DOMAIN);?>
    <input class="widefat" id="<?php echo $this->get_field_id('feedblitz_list_id'); ?>" name="<?php echo $this->get_field_name('feedblitz_list_id'); ?>" type="text" value="<?php echo esc_attr($feedblitz_list_id); ?>" />
  </label>
</p>
<p id="<?php echo $this->get_field_id('aweber_id1'); ?>" style="<?php
 if($newsletter_provider == 'aweber')
{echo 'display:block';}
else
{echo 'display:none';};?>">
  <label for="<?php echo $this->get_field_id('aweber_list_name'); ?>">
    <?php _e('List Name:',THEME_DOMAIN);?>
    <input class="widefat" id="<?php echo $this->get_field_id('aweber_list_name'); ?>" name="<?php echo $this->get_field_name('aweber_list_name'); ?>" type="text" value="<?php echo esc_attr($aweber_list_name); ?>" />
  </label>
</p>
<script type="text/javascript">
				function show_hide_divs(newsletter_provider,feedburner_id,mailchimp_id,feedblitz_id,aweber_id)
				{
					if(newsletter_provider == 'feedburner')
					{
						jQuery('#'+feedburner_id).show('slow');
						jQuery('#'+mailchimp_id).hide('slow');
						jQuery('#'+feedblitz_id).hide('slow');
						jQuery('#'+aweber_id).hide('slow');
					}else if(newsletter_provider == 'mailchimp')
					{
						jQuery('#'+mailchimp_id).show('slow');
						jQuery('#'+feedburner_id).hide('slow');
						jQuery('#'+feedblitz_id).hide('slow');
						jQuery('#'+aweber_id).hide('slow');
					}else if(newsletter_provider == 'feedblitz')
					{
						jQuery('#'+feedblitz_id).show('slow');
						jQuery('#'+mailchimp_id).hide('slow');
						jQuery('#'+feedburner_id).hide('slow');
						jQuery('#'+aweber_id).hide('slow');
					}else if(newsletter_provider == 'aweber')
					{
						jQuery('#'+aweber_id).show('slow');
						jQuery('#'+feedblitz_id).hide('slow');
						jQuery('#'+mailchimp_id).hide('slow');
						jQuery('#'+feedburner_id).hide('slow');
					}
				}
			</script>
<?php
		}
	}
	
	if(!function_exists('attach_mailchimp_js'))
	{
		function attach_mailchimp_js()
		{
			global $mailchimp_api_key,$mailchimp_list_id;
			?>
<script type="text/javascript">
				jQuery.noConflict();
				jQuery(document).ready(function()
					{
						jQuery('#mailchimp_submit').click(function()
							{
								jQuery('#process').css('display','block');
								var datastring = '&name=' + escape(jQuery('#name').val()) + '&email=' + escape(jQuery('#email').val()) + '&api_key=<?php echo $mailchimp_api_key;?>&list_id=<?php echo $mailchimp_list_id;?>';
								jQuery.ajax(
									{
										url: '<?php echo get_template_directory_uri().'/library/classes/process_mailchimp.php';?>',
										data: datastring,
										success: function(msg)
										{
											jQuery('#process').css('display','none');
											jQuery('#newsletter_msg').html(msg);
										},
										error: function(msg)
										{
											jQuery('#process').css('display','none');
											jQuery('#newsletter_msg').html(msg);
										}
									});
								return false;
							});
					});
			</script>
<?php
		}
	}
}
/*  Subscriber widget END */
/*  Testimonial widget END */
define('TITLE_TEXT',__('Title',THEME_DOMAIN));
define('SET_TIME_OUT_TEXT',__('Set Time Out',THEME_DOMAIN));
define('SET_THE_SPEED_TEXT',__('Set the speed',THEME_DOMAIN));
define('QUOTE_TEXT',__('Quote text',THEME_DOMAIN));
define('AUTHOR_NAME_TEXT',__('Author name',THEME_DOMAIN));
if(!class_exists('supreme_testimonials_widget'))
{
	class supreme_testimonials_widget extends WP_Widget
	{
		function supreme_testimonials_widget()
		{
			//Constructor
			$widget_ops = array('classname'  => 'widget testimonials','description'=> 'Display a set of sliding testimonials. Works best in sidebar areas.');
			$this->WP_Widget('testimonials_widget',apply_filters('templ_testimonial_widget_title_filter',__('T &rarr; Testimonials',THEME_DOMAIN)), $widget_ops);
		}
		function widget($args, $instance)
		{
			// prints the widget
			extract($args, EXTR_SKIP);
			echo $before_widget;
			$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
			$link_text = empty($instance['link_text']) ? '' : apply_filters('widget_title', $instance['link_text']);
			$link_url = empty($instance['link_url']) ? '' : apply_filters('widget_title', $instance['link_url']);
			$fadin = empty($instance['fadin']) ? '3000' : apply_filters('widget_fadin', $instance['fadin']);
			$fadout = empty($instance['fadout']) ? '2000' : apply_filters('widget_fadout', $instance['fadout']);
			$transition = empty($instance['transition']) ? 'fade' : apply_filters('widget_fadout', $instance['transition']);
			$author_text = empty($instance['author']) ? '' : apply_filters('widget_author', $instance['author']);
			$quote_text = empty($instance['quotetext']) ? '' : apply_filters('widget_quotetext', $instance['quotetext']);
			if($quote_text )
			{
				do_action('testimonial_script',$transition,$fadin,$fadout);
			}?>
<div class="testimonials">
  <?php
				if($title)
				{
					if(function_exists('icl_register_string'))
					{
						icl_register_string(THEME_DOMAIN,'testimonial_title',$title);
						$title = icl_t(THEME_DOMAIN,'testimonial_title',$title);
					}
					?>
  <h3 class="widget-title i_testimonials"> <span> <?php echo $title; ?> </span> </h3>
  <?php
				}?>
  <div id="testimonials" class="testimonials_wrap">
    <?php
					for($c = 0; $c < count($author_text); $c++)
					{
						if( @$author_text[$c] != '')
						{
							?>
    <div class="active">
      <?php
								if(function_exists('icl_register_string'))
								{
									icl_register_string(THEME_DOMAIN,'quote_text'.$c,$quote_text[$c]);
									$quote_text[$c] = icl_t(THEME_DOMAIN,'quote_text'.$c,$quote_text[$c]);
									icl_register_string(THEME_DOMAIN,'author_text'.$c,$author_text[$c]);
									$author_text[$c] = icl_t(THEME_DOMAIN,'author_text'.$c,$author_text[$c]);
								}
								do_action('tmpl_testimonial_add_extra_field',$c,$instance);
								do_action('tmpl_testimonial_quote_text',$c,$instance);
								?>
    </div>
    <?php
						}
					} ?>
  </div>
  <?php
				if($link_url != "" && $link_text != "")
				{
					if(function_exists('icl_register_string'))
					{
						icl_register_string(THEME_DOMAIN,$link_text,$link_text);
						$link_text = icl_t(THEME_DOMAIN,$link_text,$link_text);
					}
					else
					{
						$link_text = __($link_text,THEME_DOMAIN);
					}
					?>
  <a href="<?php echo $link_url; ?>" class="testimonial_external_link"> <?php echo $link_text; ?> </a>
  <?php
				}
				do_action('show_bullet');
				?>
</div>
<?php
			echo $after_widget;
		}
		function update($new_instance, $old_instance)
		{
			//save the widget
			return $new_instance;
		}
		function form($instance)
		{
			//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array('title' => 'People Talking About Templatic','link_text' => '','link_url'  => '','author' => array("Brain Storm","Linda","Mark Anthony"),'quotetext' => array('Templatic offers world class WordPress theme support and unique, highly innovative and professionally useful WordPress themes. So glad to have found you! All the best and many more years of creativity, productivity and success.','Templatic has the best WordPress Themes and an exceptional and out-of-this-world customer service. I always receive a response in less than 24 hours, sometimes in less than one hour, this is amazing. I will recommend it to all my friends. Keep up the good work!','Templatic is reliable, it has a good support, and very accurate. Beside that, it has a big community of members who contribute.'),'fadin'     => '2700','fadout'    => '1500','transition'=> 'fade' ) );
			$title     = strip_tags($instance['title']);
			$link_text = strip_tags($instance['link_text']);
			$link_url  = strip_tags($instance['link_url']);
			$fadin     = ($instance['fadin']);
			$fadout    = ($instance['fadout']);
			$transition= ($instance['transition']);
			$author1   = ($instance['author']);
			$quotetext1= ($instance['quotetext']);
			global $author,$quotetext;
			$text_author    = $this->get_field_name('author');
			$text_quotetext = $this->get_field_name('quotetext');
			?>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>"> <?php echo TITLE_TEXT;?>:
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('fadin'); ?>"> <?php echo SET_TIME_OUT_TEXT;?>:
    <input class="widefat" id="<?php echo $this->get_field_id('fadin'); ?>" name="<?php echo $this->get_field_name('fadin'); ?>" type="text" value="<?php echo esc_attr($fadin); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('fadout'); ?>"> <?php echo SET_THE_SPEED_TEXT;?>:
    <input class="widefat" id="<?php echo $this->get_field_id('fadout'); ?>" name="<?php echo $this->get_field_name('fadout'); ?>" type="text" value="<?php echo esc_attr($fadout); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('transition'); ?>">
    <?php _e("Transition type","supreme");?>
    :
    <select id="<?php echo $this->get_field_id('transition'); ?>" name="<?php echo $this->get_field_name('transition'); ?>" >
      <option value="fade" <?php
 if("fade" == $transition)
{echo "selected=selected";}?>>
      <?php _e("Fade","supreme");?>
      </option>
      <option value="scrollUp" <?php
 if("scrollUp" == $transition)
{echo "selected=selected";}?>>
      <?php _e("Scroll Up","supreme");?>
      </option>
      <option value="scrollRight" <?php
 if("scrollRight" == $transition)
{echo "selected=selected";}?>>
      <?php _e("Scroll Right","supreme");?>
      </option>
      <option value="shuffle" <?php
 if("shuffle" == $transition)
{echo "selected=selected";}?>>
      <?php _e("Shuffle","supreme");?>
      </option>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('quotetext'); ?>">
    <?php _e('Quote text',THEME_DOMAIN);?>
    :
    <textarea class="widefat" id="<?php echo $this->get_field_id('quotetext'); ?>" name="<?php echo $text_quotetext; ?>[]" type="text" ><?php echo esc_attr( @$quotetext1[0]); ?></textarea>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('author'); ?>">
    <?php _e('Author name',THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $text_author; ?>[]" type="text" value="<?php echo esc_attr( @$author1[0]); ?>" />
  </label>
</p>
<?php
			do_action('tmpl_after_testimonial_title',$instance,$this);
			?>
<div id="tGroup" class="tGroup">
  <?php
				for($i = 1;$i < count($author1);$i++){
					if($author1[$i] != ""){
						$j = $i + 1;
						echo '<div  class="TextDiv'.$j.'">';
						echo '<p>';
						echo '<label>'.QUOTE_TEXT.$j;
						echo ': <textarea class="widefat"  name="'.$text_quotetext.'[]" >'.esc_attr($quotetext1[$i]).'</textarea>';
						echo '</label>';
						echo '</p>';
						echo '<p>';
						echo '<label>'.AUTHOR_NAME_TEXT.$j;
						echo ': <input type="text" class="widefat"  name="'.$text_author.'[]" value="'.esc_attr($author1[$i]).'"></label>';
						echo '</label>';
						echo '</p>';
						do_action('tmpl_testimonial_field',$j,$instance,$this);
						echo '</div>';
					}
				}
				?>
</div>
<p>
  <?php
				do_action('add_testimonial_submit',$instance,$text_quotetext,$text_author);
				?>
  <a
					href="javascript:void(0);" id="removetButton" class="removeButton" type="button" onclick="remove_tfields();">- Remove </a> </p>
<p>
  <label for="<?php echo $this->get_field_id('link_text'); ?>">
    <?php _e("Link Text",THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php echo $this->get_field_id('link_text'); ?>" name="<?php echo $this->get_field_name('link_text'); ?>" type="text" value="<?php echo esc_attr($link_text); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('link_url'); ?>">
    <?php _e("Link Url",THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php echo $this->get_field_id('link_url'); ?>" name="<?php echo $this->get_field_name('link_url'); ?>" type="text" value="<?php echo esc_attr($link_url); ?>" />
  </label>
</p>
<?php
		}
	}
}
add_action('tmpl_testimonial_quote_text','add_testimonial_quote_text',10,2);
function add_testimonial_quote_text($c,$instance)
{
	$quote_text = empty($instance['quotetext']) ? '' : apply_filters('widget_quotetext', $instance['quotetext']);
	$author_text = empty($instance['author']) ? '' : apply_filters('widget_author', $instance['author']);
	if(function_exists('icl_register_string'))
	{
		icl_register_string(THEME_DOMAIN,'quote_text'.$c,$quote_text[$c]);
		$quote_text[$c] = icl_t(THEME_DOMAIN,'quote_text'.$c,$quote_text[$c]);
		icl_register_string(THEME_DOMAIN,'author_text'.$c,$author_text[$c]);
		$author_text[$c] = icl_t(THEME_DOMAIN,'author_text'.$c,$author_text[$c]);
	}
	echo  $quote_text[$c];
	if($author_text[$c])
	{
		?>
<cite> - <?php echo $author_text[$c]; ?> </cite>
<?php
	}
}
/*
* templatic Slider widget init
*/
add_action('add_testimonial_submit','add_testimonial_submit_button',10,3);
function add_testimonial_submit_button($instance,$text_quotetext,$text_author)
{
	?>
<a
		href="javascript:void(0);" id="addtButton" class="addButton" type="button" onclick="add_tfields('<?php echo $text_author; ?>','<?php echo $text_quotetext; ?>');">+ Add more </a>
<?php
}
/*-----------------------------------------------
add widget script on <head>
--------------------------------------------------*/
add_action('admin_head','supreme_add_script_addnew_');
if(!function_exists('supreme_add_script_addnew_'))
{
	function supreme_add_script_addnew_()
	{
		global $author,$quotetext;
		?>
<script type="application/javascript">
			var counter1 = 2;
			function add_tfields(name,ilname)
			{
				var newTextBoxDiv = jQuery(document.createElement('div')).attr("class", 'TextDiv' + counter1);
				newTextBoxDiv.html('<p><label>Quote text '+ counter1+': </label>'+'<textarea  class="widefat" name="'+ilname+'[]" id="textbox' + counter1 + '" value="" ></textarea></p>');
				newTextBoxDiv.append('<p><label>Author name '+ counter1+': </label>'+'<input type="text" class="widefat" name="'+name+'[]" id="textbox' + counter1 + '" value="" ></p>');
				newTextBoxDiv.appendTo(".tGroup");
				counter1++;
			}
			function remove_tfields()
			{
				if(counter1-1==1)
				{
					alert("<?php _e('One textbox is required.',THEME_DOMAIN); ?>");
					return false;
				}
				counter1--;
				jQuery(".TextDiv" + counter1).remove();
			}
		</script>
<?php
	}
}
/*-------------------------------------------------------------
Class for home page slider
--------------------------------------------------------------*/
if(!class_exists('supreme_banner_slider') && current_theme_supports('supreme_banner_slider'))
{
	class supreme_banner_slider extends WP_Widget
	{
		function __construct()
		{
			parent::__construct(
				'supreme_banner_slider', // Base ID
				__('T &rarr; Homepage Slider',THEME_DOMAIN), // Name
				array('description'=> __('Display images from a specific category or a collection of static images. The widget works best in the Homepage Slider area.',THEME_DOMAIN),              ) // Args
			);
		}
		function widget($args, $instance)
		{
			extract($args, EXTR_SKIP);
			echo $before_widget;
			/*
			*  Add flexslider script and style sheet in head tag
			*/
			$custom_banner_temp = empty($instance['custom_banner_temp']) ? '' : $instance['custom_banner_temp'];
			$post_type = empty($instance['post_type']) ? 'post,1' : apply_filters('widget_category', $instance['post_type']);
			$title    = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
			$sdesc = empty($instance['sdesc']) ? '' : apply_filters('widget_sdesc', $instance['sdesc']);
			$s1 = empty($instance['s1']) ? '' : apply_filters('widget_s1', $instance['s1']);
			$s1_title_link = empty($instance['s1_title_link']) ? '' : apply_filters('widget_s1_title_link', $instance['s1_title_link']);
			$s1_title = empty($instance['s1_title']) ? '' : apply_filters('widget_s1_title', $instance['s1_title']);
			$animation = empty($instance['animation']) ? 'slide' : apply_filters('widget_number', $instance['animation']);
			$number = empty($instance['number']) ? '5' : apply_filters('widget_number', $instance['number']);
			$height = empty($instance['height']) ? '' : apply_filters('widget_height', $instance['height']);
			$autoplay = empty($instance['autoplay']) ? '' : apply_filters('widget_autoplay', $instance['autoplay']);
			$slideshowSpeed = empty($instance['slideshowSpeed']) ? '' : apply_filters('widget_autoplay', $instance['slideshowSpeed']);
			$sliding_direction = empty($instance['sliding_direction']) ? 'horizontal' : $instance['sliding_direction'];
			$reverse = empty($instance['reverse']) ? 'false' : $instance['reverse'];
			$animation_speed = empty($instance['animation_speed']) ? '2000' : $instance['animation_speed'];
			$content = empty($instance['content']) ? '' : $instance['content'];
			$content_len = empty($instance['content_len']) ? '60' : $instance['content_len'];
			// Carousel Slider Settings
			$is_Carousel = empty($instance['is_Carousel']) ? '' : $instance['is_Carousel'];
			if($is_Carousel){
				$item_width = empty($instance['item_width']) ? 925 : $instance['item_width'];
				//$item_margin = empty($instance['item_margin']) ? '0' : $instance['item_margin'];
				$min_item = empty($instance['min_item']) ? '0' : $instance['min_item'];
				$max_items = empty($instance['max_items']) ? '0' : $instance['max_items'];
				$item_move = empty($instance['item_move']) ? '0' : $instance['item_move'];
			}
			else
			{
				$item_width = empty($instance['item_width']) ? 925 : $instance['item_width'];;
				$min_item = 0;
				$max_items= 0;
				$item_move= 0;
			}
			if($is_Carousel)
			{
				$width = apply_filters('carousel_slider_width',$item_width,12);
				$height= apply_filters('carousel_slider_height',350);
			}
			else
			{
				$width = apply_filters('supreme_slider_width',$item_width,12);
				$height= apply_filters('supreme_slider_height',350);
			}
			$class = "flexslider".rand();
			if($autoplay == '')
			{
				$autoplay = 'false';
			}
			if($slideshowSpeed == '')
			{
				$slideshowSpeed = '300000';
			}
			if($animation_speed == '')
			{
				$animation_speed = '2000';
			}
			if($autoplay == 'false')
			{
				$animation_speed = '300000';
			}
			?>
<script type="text/javascript">
				jQuery(window).load(function()
					{
						jQuery('.<?php echo $class;?>').flexslider(
							{
								animation: '<?php echo $animation;?>',
								slideshow: <?php echo $autoplay;?>,
								direction: "<?php echo $sliding_direction;?>",
								slideshowSpeed: <?php echo $slideshowSpeed;?>,
								<?php if($autoplay=='true'):?>animationSpeed: <?php echo $animation_speed;?>,<?php endif;?>
								animationLoop: true,
								startAt: 0,
								smoothHeight: true,
								easing: "swing",
								pauseOnHover: true,
								video: true,
								<?php if(current_theme_supports('slider_thumb_image') && $custom_banner_temp == '')
								{
									?>
									controlNav: "thumbnails",
									<?php
								} else
								{
									?>
									controlNav: true,
									reverse: <?php echo $reverse;?>,
									<?php
								}?>
								directionNav: true,
								prevText: '<i class="fa fa-chevron-left"></i>',
								nextText: '<i class="fa fa-chevron-right"></i>',
								<?php if(isset($is_Carousel) && $is_Carousel==1)
								{
									?>
									// Carousel Slider Options
									itemWidth: <?php echo $item_width;?>,                   //{NEW} Integer: Box-model width of individual carousel items, including horizontal borders and padding.
									itemMargin: <?php if($min_item!="")
									{
										echo $min_item;
									}else echo '0'?>,                  //{NEW} Integer: Margin between carousel items.
									minItems: <?php echo $min_item;?>,                    //{NEW} Integer: Minimum number of carousel items that should be visible. Items will resize fluidly when below this.
									maxItems: <?php echo $max_items;?>,                    //{NEW} Integer: Maxmimum number of carousel items that should be visible. Items will resize fluidly when above this limit.
									move: <?php echo $item_move;?>,                        //{NEW} Integer: Number of carousel items that should move on animation. If 0, slider will move all visible items.
									<?php
								} ?>
								start: function(slider)
								{
									jQuery('body').removeClass('loading');
								}
							});
					});
				//FlexSlider: Default Settings
			</script>
<!-- flexslider container start -->
<?php
			if($custom_banner_temp != ''){
				$custom_class_name = ' image_slider';
			}
			else
			{
				$custom_class_name = ' post_slider';
			}
			if($is_Carousel){$is_Carousel=' slider_carousel';}
			?>
<div class="flexslider clearfix <?php echo $class.$custom_class_name.$is_Carousel; ?>">
  <?php
				if($title)
				{
					?>
  <h3 class="widget-title"> <?php echo  $title; ?> </h3>
  <?php
				} ?>
  <?php
				if(function_exists('icl_register_string'))
				{
					icl_register_string(THEME_DOMAIN,'slider_description',$sdesc);
					$sdesc = icl_t(THEME_DOMAIN,'slider_description',$sdesc);
				}
				?>
  <?php
				if($sdesc)
				{
					?>
  <p> <?php echo $sdesc; ?> </p>
  <?php
				} ?>
  <div class="slides_container clearfix">
    <?php do_action('templ_slider_search_widget',$instance);// add action for display additional field?>
    <ul class="slides">
      <?php
						if(isset($instance['custom_banner_temp']) && $instance['custom_banner_temp'] == 1):
						if(is_array($s1)):
						for($i = 0;$i < count($s1);$i++):?>
      <?php
						if($s1[$i] != ""): ?>
      <li class="post_img" > <a href="<?php echo $s1_title_link[$i]; ?>" target="_blank"> <img  src="<?php echo $s1[$i]; ?>" alt="" />
        <?php
								if($s1_title[$i] != ""):?>
        <h2>
          <?php
									if(function_exists('icl_register_string'))
									{
										icl_register_string(THEME_DOMAIN,'silder_title'.$i,$s1_title[$i]);
										$s1_title[$i] = icl_t(THEME_DOMAIN,'silder_title'.$i,$s1_title[$i]);
									}
									echo sprintf(__('%s',THEME_DOMAIN), $s1_title[$i]); ?>
        </h2>
        <?php endif;?>
        </a> </li>
      <?php endif;
						endfor;//finish forloop
						endif;
						else:
					
							global $post,$wpdb;
							$counter=0;
							$postperslide = 1;						
							$slider_post=$post_type;
							
							for($k=0;$k<sizeof($slider_post);$k++){
								
								$posttype = explode(',',$slider_post[$k]);				
								$post_type = $posttype[0];
								$catid = $posttype[1];
								$cat_name = @$posttype[2];
								$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));							
								if($post_type=='product')
								{
									$taxonomies[0]=$taxonomies[1];	
								}
								$term = get_term( $catid, $taxonomies[0] );							
								$cat_id[]=$term->term_id;
							
							}
							if(is_plugin_active('woocommerce/woocommerce.php') && $post_type == 'product')
							{
								$taxonomies[0] = $taxonomies[1];
							}
							$args=array(												  
									  'post_type' => $post_type,
									  'posts_per_page' => $number,												  
									  'post_status' => 'publish' ,
									  'tax_query' => array(                
												array(
													'taxonomy' =>$taxonomies[0],
													'field' => 'id',
													'terms' => $cat_id,
													'operator'  => 'IN'
												)            
											 )
									  );	
							$slide = null;
						remove_all_actions('posts_where');
						if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
						{
							add_filter('posts_where', 'location_multicity_where');
						}
						add_filter('posts_join', 'templatic_posts_where_filter');
						$slide   = new WP_Query($args);
						if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
						{
							remove_filter('posts_where', 'location_multicity_where');
						}
						remove_filter('posts_join', 'templatic_posts_where_filter');
						$slider_image_size = apply_filters('slider_image_thumb','large');
						if( $slide->have_posts() )
						{
							while($slide->have_posts()) : $slide->the_post();
							global $post;
							
							
														
							
							
							//check post thumbnail image if available
							$large_image = get_the_image( array('size'  => $slider_image_size,'echo' => false ,'default_image'=>get_template_directory_uri()."/images/noimage.jpg"));
							if(get_post_meta($post->ID,'portfolio_image',true) != '')
							{
								$post_image = "<a class='image_roll' href=".get_permalink($post->ID)." target='_SELF'><img src=".get_post_meta($post->ID,'portfolio_image',true)." title=".$post->post_title." alt=".$post->post_title."/></a>";
								$flag = 0;
							}
							else
							{
								$post_image = get_the_image( array('size'         => $slider_image_size,'echo'         => false ,'width'        => $width,'height'       => $height,'default_image'=>get_template_directory_uri()."/images/noimage.jpg"));
								$flag = 1;
							}
							$post_images = $post_image ;
							$thumb_image = '';
							if($counter == '0' || $counter % $postperslide == 0)
							{
							?>
								<li <?php
								if(current_theme_supports('slider_thumb_image') && $custom_banner_temp == ''){
									$thumb_image_size = apply_filters('slider_thumb_image','thumbnail');?> data-thumb = "<?php get_the_image( array('size' => $thumb_image_size,'echo' => "false" ,'default_image'=>get_template_directory_uri().'/images/noimage.jpg'));?>" <?php } ?> > <?php
							} ?>
        <!-- post start -->
       
		<!-- MATTHIAS -->
        <h1 style="font-size: 24px; font-weight:bold;	font-family: 'Rock Salt', cursive; "> <a href="<?php the_permalink() ?>" rel="bookmark">
              <?php  the_title(); ?>
              </a> </h1>
       

        <div class="post_list">
        

					
        
          <?php
								if(get_post_format($post->ID) == 'image'): ?>
          <div class="post_img">
            <?php
									if($large_image != "")
									{
										echo  $large_image;
									} ?>
          </div>
          <?php
								else: ?>
          <div 
									<?php
									if($flag == 1)
									{
										?> class="post_img" style="width:<?php echo $width."px"; ?>"<?php
									} ?>>
            <?php
									if($post_images != "")
									{
										echo $post_images;
									} ?>
          </div>
          <?php endif;
								if($flag == 1)
								{
									// display only if user not inserted portfolio image
									?>
          <div class="slider-post">
            <?php
										if(current_theme_supports('slider_thumb_image'))
										{
											if(get_post_meta($post->ID,'featured_h',true) == 'h'){
												?>
            <span class="featured_tag">
            <?php _e('Featured',THEME_DOMAIN); ?>
            </span>
            <?php
											}
										}
										?>
            <h2> <a href="<?php the_permalink() ?>" rel="bookmark">
              <?php // MATTHIAS the_title(); ?>
              </a> </h2>
            <?php
										do_action('slider_extra_content',get_the_ID());// do action for display the extra content
										if(current_theme_supports('slider-post-content') && $content )
										{
											global $legnth_content;
											$legnth_content = $content_len;
											add_filter('excerpt_length', 'slider_excerpt_length',20);
											if(is_plugin_active('woocommerce/woocommerce.php') && $post_type == 'product')
											{
												// MATTHIAS echo '<div class="slider_post_excerpt">'.string_limit_words(get_the_excerpt(),$legnth_content).'</div>';
											}
											else
											{
												// MATTHIAS echo '<div class="slider_post_excerpt">'.string_limit_words(get_the_excerpt(),$legnth_content).'</div>';
											}
										}
										?>
          </div>
          <?php
								}
								else
								{
									?>
          <h2> <a href="<?php the_permalink() ?>" rel="bookmark">
            <?php the_title(); ?>
            </a> </h2>
          <?php
								} ?>
        </div>
        <!-- post end -->
        <?php
							$counter++;
							if($counter % $postperslide == 0)
							{
								echo "</li>";
							}
							endwhile;
						}
						wp_reset_query();
						endif;?>
    </ul>
  </div>
</div>
<!-- flexslider container end -->
<?php
			echo $after_widget;
		}
		function update($new_instance, $old_instance)
		{
			//save the widget
			return $new_instance;
		}
		function form($instance)
		{
			//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array('search'            =>'','search_post_type'  =>'','location'          =>'','distance'          =>'','radius'            =>'','post_type'         => '','number'            => '','animation'         =>'fade','slideshowSpeed'    =>'4700','animation_speed'   =>'800','sliding_direction' =>'','reverse'           =>'true','item_width'        =>'','is_Carousel_temp'  =>'','min_item'          =>'','max_items'         =>'','item_move'         =>'','custom_banner_temp'=>'','s1'                => array(get_template_directory_uri().'/images/slide1.jpg',get_template_directory_uri().'/images/slide2.jpg',get_template_directory_uri().'/images/slide1.jpg',get_template_directory_uri().'/images/slide2.jpg'),'s1_title'          => array(__("Slider Title 1",THEME_DOMAIN), __("Slider Title 2",THEME_DOMAIN), __("Slider Title 3",THEME_DOMAIN), __("Slider Title 4",THEME_DOMAIN)),'s1_title_link' => array('#','#','#','#'),'postperslide'      =>'','content_len'       =>'' ,'autoplay'       =>'true' ) );
			// Widget Get Posts settings
			$title              = strip_tags(@$instance['title']);
			$sdesc              = strip_tags(@$instance['sdesc']);
			$custom_banner_temp = (strip_tags($instance['custom_banner_temp'])) ? strip_tags($instance['custom_banner_temp']) : '';
			$post_type = $instance['post_type'];	
			$number             = strip_tags($instance['number']);
			$content            = empty($instance['content'])? '' : strip_tags($instance['content']);
			$content_len = empty($instance['content_len'])? '60' : strip_tags($instance['content_len']);
			// Slider Basic Settings
			$autoplay = empty($instance['autoplay'])? '' : strip_tags($instance['autoplay']);
			$animation         = strip_tags($instance['animation']);
			$slideshowSpeed    = strip_tags($instance['slideshowSpeed']);
			$sliding_direction = strip_tags($instance['sliding_direction']);
			$reverse           = strip_tags($instance['reverse']);
			$animation_speed   = strip_tags($instance['animation_speed']);
			// Carousel Slider Settings
			// Carousel Slider Settings
			$is_Carousel       = empty($instance['is_Carousel'])? '' : strip_tags($instance['is_Carousel']);
			$item_width       = strip_tags($instance['item_width']);
			//$item_margin = strip_tags($instance['item_margin']);
			$min_item         = strip_tags($instance['min_item']);
			$max_items        = strip_tags($instance['max_items']);
			$item_move        = strip_tags($instance['item_move']);
			$is_Carousel_temp = strip_tags($instance['is_Carousel_temp']);
			$item_width       = strip_tags($instance['item_width']);
			//$item_margin = strip_tags($instance['item_margin']);
			$min_item         = strip_tags($instance['min_item']);
			$max_items        = strip_tags($instance['max_items']);
			$item_move        = strip_tags($instance['item_move']);
			$postperslide     = empty($instance['postperslide'])? '' : strip_tags($instance['postperslide']);
			//  If Custom Banner Slider (Settings)
			$s1            = ($instance['s1']);
			$s1_title      = ($instance['s1_title']);
			$s1_title_link = ($instance['s1_title_link']); ?>
<script type="text/javascript">
				function select_custom_image(id,div_custom,div_def){
					var checked=id.checked;
					if(checked){
						jQuery('#'+div_def).slideToggle('slow');
						jQuery('#'+div_custom).slideToggle('slow');
					}else{
						jQuery('#'+div_custom).hide();
						jQuery('#'+div_def).show();
					}
				}
				function select_is_Carousel(id,div_def){
					var checked=id.checked;
					jQuery('#'+div_def).slideToggle('slow');
				}
			</script>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>">
    <?php _e('Slider Title',THEME_DOMAIN); ?>
    :
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('sdesc'); ?>">
    <?php _e('Slider Description',THEME_DOMAIN); ?>
    :
    <textarea class="widefat" id="<?php echo $this->get_field_id('sdesc'); ?>" name="<?php echo $this->get_field_name('sdesc'); ?>" type="text" ><?php echo esc_attr($sdesc); ?></textarea>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('animation'); ?>">
    <?php _e('Animation',THEME_DOMAIN); ?>
    :
    <select class="widefat" name="<?php echo $this->get_field_name('animation'); ?>" id="<?php echo $this->get_field_id('animation'); ?>">
      <option <?php
 if(esc_attr($animation) == 'fade')
{?> selected="selected"<?php }?> value="fade">
      <?php _e("Fade",THEME_DOMAIN);?>
      </option>
      <option <?php
 if(esc_attr($animation) == 'slide')
{?> selected="selected"<?php }?> value="slide">
      <?php _e("Slide",THEME_DOMAIN);?>
      </option>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('autoplay'); ?>">
    <?php _e('Slide show',THEME_DOMAIN); ?>
    :
    <select class="widefat" name="<?php echo $this->get_field_name('autoplay'); ?>" id="<?php echo $this->get_field_id('autoplay'); ?>">
      <option <?php
 if(esc_attr($autoplay) == 'true')
{?> selected="selected"<?php }?> value="true">
      <?php _e("Yes",THEME_DOMAIN);?>
      </option>
      <option <?php
 if(esc_attr($autoplay) == 'false')
{?> selected="selected"<?php }?> value="false">
      <?php _e("No",THEME_DOMAIN);?>
      </option>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('sliding_direction'); ?>">
    <?php _e('Sliding Direction',THEME_DOMAIN); ?>
    :
    <select class="widefat" name="<?php echo $this->get_field_name('sliding_direction'); ?>" id="<?php echo $this->get_field_id('sliding_direction'); ?>">
      <option <?php
 if(esc_attr($sliding_direction) == 'horizontal')
{?> selected="selected"<?php }?> value="horizontal">
      <?php _e("Horizontal",THEME_DOMAIN);?>
      </option>
      <option <?php
 if(esc_attr($sliding_direction) == 'vertical')
{?> selected="selected"<?php }?> value="vertical">
      <?php _e("Vertical",THEME_DOMAIN);?>
      </option>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('reverse'); ?>">
    <?php _e('Reverse Animation Direction',THEME_DOMAIN); ?>
    :
    <select class="widefat" name="<?php echo $this->get_field_name('reverse'); ?>" id="<?php echo $this->get_field_id('reverse'); ?>">
      <option <?php
 if(esc_attr($reverse) == 'false')
{?> selected="selected"<?php }?> value="false">
      <?php _e("False",THEME_DOMAIN);?>
      </option>
      <option <?php
 if(esc_attr($reverse) == 'true')
{?> selected="selected"<?php }?> value="true">
      <?php _e("True",THEME_DOMAIN);?>
      </option>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('slideshowSpeed'); ?>">
    <?php _e('Slide Show Speed',THEME_DOMAIN); ?>
    :
    <input class="widefat" id="<?php echo $this->get_field_id('slideshowSpeed'); ?>" name="<?php echo $this->get_field_name('slideshowSpeed'); ?>" type="text" value="<?php echo esc_attr($slideshowSpeed); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('animation_speed'); ?>">
    <?php _e('Animation Speed',THEME_DOMAIN); ?>
    :
    <input class="widefat" id="<?php echo $this->get_field_id('animation_speed'); ?>" name="<?php echo $this->get_field_name('animation_speed'); ?>" type="text" value="<?php echo esc_attr($animation_speed); ?>" />
  </label>
</p>
<!--is_Carousel -->
<?php if(current_theme_supports('show_carousel_slider')){ ?>
<p> <br/>
  <label for="<?php echo $this->get_field_id('is_Carousel'); ?>">
    <input id="<?php echo $this->get_field_id('is_Carousel'); ?>" name="<?php echo $this->get_field_name('is_Carousel'); ?>" type="checkbox" value="1" <?php
					if($is_Carousel == '1')
					{
						?>checked=checked<?php
					}
					?>style="width:10px;" onclick="select_is_Carousel(this,'<?php echo $this->get_field_id('home_slide_carousel'); ?>');"/>
    <?php _e("<b>Settings for Carousel slider option?</b>",THEME_DOMAIN);?>
  </label>
</p>
<div id="<?php echo $this->get_field_id('home_slide_carousel'); ?>" style="<?php if($is_Carousel == '1'){ ?>display:block;<?php }else{?>display:none;<?php }?>">
  <p>
    <label for="<?php echo $this->get_field_id('item_width'); ?>">
      <?php _e('Item Width <br/><small>(Box-model width of individual items, including horizontal borders and padding.)</small>',THEME_DOMAIN); ?>
      <input class="widefat" id="<?php echo $this->get_field_id('item_width'); ?>" name="<?php echo $this->get_field_name('item_width'); ?>" type="text" value="<?php echo esc_attr($item_width); ?>" />
    </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('min_item'); ?>">
      <?php _e('Min Item <br/><small>(Minimum number of items that should be visible. Items will resize fluidly when below this.)</small>',THEME_DOMAIN); ?>
      <input class="widefat" id="<?php echo $this->get_field_id('min_item'); ?>" name="<?php echo $this->get_field_name('min_item'); ?>" type="text" value="<?php echo esc_attr($min_item); ?>" />
    </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('max_items'); ?>">
      <?php _e('Max Item <br/><small>(Maxmimum number of items that should be visible. Items will resize fluidly when above this limit.)</small>',THEME_DOMAIN); ?>
      <input class="widefat" id="<?php echo $this->get_field_id('max_items'); ?>" name="<?php echo $this->get_field_name('max_items'); ?>" type="text" value="<?php echo esc_attr($max_items); ?>" />
    </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('item_move'); ?>">
      <?php _e('Items Move <br/><small>(Number of items that should move on animation. If 0, slider will move all visible items.)</small>',THEME_DOMAIN); ?>
      <input class="widefat" id="<?php echo $this->get_field_id('item_move'); ?>" name="<?php echo $this->get_field_name('item_move'); ?>" type="text" value="<?php echo esc_attr($item_move); ?>" />
    </label>
  </p>
  <?php
				if(current_theme_supports('postperslide')): ?>
  <p>
    <label for="<?php echo $this->get_field_id('postperslide'); ?>">
      <?php _e('Posts Per Slide <br/><small>Number of items you want to show in one slide. this option is work with LI tag, it will show all images in one LI tag. </small>',THEME_DOMAIN); ?>
      <input class="widefat" id="<?php echo $this->get_field_id('postperslide'); ?>" name="<?php echo $this->get_field_name('postperslide'); ?>" type="text" value="<?php echo esc_attr($postperslide); ?>" />
    </label>
  </p>
  <?php endif; ?>
</div>
<?php } ?>
<!-- Finish is_Carousel -->
<div id="<?php echo $this->get_field_id('home_slide_default_temp'); ?>" style="<?php
 if($custom_banner_temp == '1')
{ ?>display:none;<?php }
else
{?>display:block;<?php }?>">
  <p>
    <label for="<?php echo $this->get_field_id('post_type');?>" >
      <?php _e('Select Category',THEME_DOMAIN);?>
      <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>[]" class="widefat"  multiple="multiple" size="8">
        <?php $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
						foreach ( $taxonomies as $taxonomy ) {	
							$query_label = '';
							if ( !empty( $taxonomy->query_var ) )
								$query_label = $taxonomy->query_var;
							else
								$query_label = $taxonomy->name;
							
							if($taxonomy->labels->name!='Tags' && $taxonomy->labels->name!='Format' && !strstr($taxonomy->labels->name,'tag') && !strstr($taxonomy->labels->name,'Tags') && !strstr($taxonomy->labels->name,'format') && !strstr($taxonomy->labels->name,'Shipping Classes')&& !strstr($taxonomy->labels->name,'Order statuses')&& !strstr($taxonomy->labels->name,'genre')&& !strstr($taxonomy->labels->name,'platform') && !strstr($taxonomy->labels->name,'colour') && !strstr($taxonomy->labels->name,'size')):	
								?>
        <optgroup label="<?php echo esc_attr( $taxonomy->object_type[0])."-".esc_attr($taxonomy->labels->name); ?>">
        <?php
												$terms = get_terms( $taxonomy->name, 'orderby=name&hide_empty=0' );
												foreach ( $terms as $term ) {		
												$term_value=esc_attr($taxonomy->object_type[0]). ',' .$term->term_id.','.$query_label;
							?>
        <!--<option style="margin-left: 8px; padding-right:10px;" value="<?php echo $term_value ?>" <?php if($post_type==$term_value) echo "selected";?>><?php echo '-' . esc_attr( $term->name ); ?></option>-->
        <option style="margin-left: 8px; padding-right:10px;" value="<?php echo $term_value ?>" <?php if(in_array(trim($term_value),$post_type)) echo "selected";?>><?php echo '-' . esc_attr( $term->name ); ?></option>
        <?php } ?>
        </optgroup>
        <?php
								endif;
						}?>
      </select>
    </label>
    <small>
    <?php _e('Select the categories from the post types.','supreme');?>
    </small> </p>
  <p>
    <label for="<?php echo $this->get_field_id('number'); ?>">
      <?php _e('Number of posts:',THEME_DOMAIN);?>
      <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" />
    </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('content_len'); ?>">
      <?php _e('Excerpt Length:',THEME_DOMAIN);?>
      <input class="widefat" id="<?php echo $this->get_field_id('content_len'); ?>" name="<?php echo $this->get_field_name('content_len'); ?>" type="text" value="<?php echo esc_attr($content_len); ?>" />
    </label>
  </p>
  <?php
				if(current_theme_supports('slider-post-content'))
				{
					?>
  <p>
    <label for="<?php echo $this->get_field_id('content'); ?>">
      <input <?php
							if($content)
							{
								echo "checked=checked";
							}?> id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" type="checkbox" value="1"/>
      <b>
      <?php _e('Enable Post Excerpt In Slider',THEME_DOMAIN);?>
      </b> </label>
  </p>
  <?php
				} ?>
</div>
<p> <br/>
  <label for="<?php echo $this->get_field_id('custom_banner_temp'); ?>">
    <input id="<?php echo $this->get_field_id('custom_banner_temp'); ?>" name="<?php echo $this->get_field_name('custom_banner_temp'); ?>" type="checkbox" value="1" <?php
					if($custom_banner_temp == '1')
					{
						?>  checked="checked"  <?php
					} ?>  onclick="select_custom_image(this,'<?php echo $this->get_field_id('home_slide_custom_temp'); ?>','<?php echo $this->get_field_id('home_slide_default_temp'); ?>');" />
    &nbsp;
    <?php _e('<b>Use Custom Images?</b>',THEME_DOMAIN);?>
    <br/>
  </label>
  <br/>
</p>
<div id="<?php echo $this->get_field_id('home_slide_custom_temp'); ?>" class="<?php echo 'home_slide_custom_temp'; ?>" style="<?php
 if($custom_banner_temp == '1')
{ ?>display:block;<?php }
else
{?>display:none;<?php }?>">
  <div id="TextBoxesGroup" class="TextBoxesGroup">
    <div id="TextBoxDiv1" class="TextBoxDiv1">
      <?php	do_action('tmpl_before_slider_title',$instance,$this);	?>
      <p>
        <?php global $textbox_title;
							$textbox_title = $this->get_field_name('s1_title');
							?>
        <label for="<?php echo $this->get_field_id('s1_title'); ?>">
          <?php _e('Banner Slider Title 1',THEME_DOMAIN);?>
          <input type="text" class="widefat"  name="<?php echo $textbox_title; ?>[]" value="<?php echo esc_attr($s1_title[0]); ?>">
        </label>
      </p>
      <?php
						do_action('tmpl_after_slider_title',$instance,$this);
						?>
      <p>
        <?php global $textbox_title_link;
							$textbox_title_link = $this->get_field_name('s1_title_link');
							?>
        <label for="<?php echo $this->get_field_id('s1_title_link'); ?>">
          <?php _e('Banner Slider Title Link 1',THEME_DOMAIN);?>
          <input type="text" class="widefat"  name="<?php echo $textbox_title_link; ?>[]" value="<?php echo esc_attr($s1_title_link[0]); ?>">
        </label>
      </p>
      <p>
        <?php global $textbox_name;
							$textbox_name = $this->get_field_name('s1');
							?>
        <label for="<?php echo $this->get_field_id('s1'); ?>">
          <?php _e('Banner Slider Image 1 full URL <small>(ex.http://templatic.com/images/banner1.png )</small>  :');?>
          <input type="text" class="widefat"  name="<?php echo $textbox_name; ?>[]" value="<?php echo esc_attr($s1[0]); ?>">
        </label>
      </p>
    </div>
    <?php
					for($i = 1;$i < count($s1);$i++){
						if($s1[$i] != ""){
							$j = $i + 1;
							echo '<div  class="TextBoxDiv'.$j.'">';
							echo '<p>';
							echo '<label>'.__('Banner Slider Title',THEME_DOMAIN).$j;
							echo ' <input type="text" class="widefat"  name="'.$textbox_title.'[]" value="'.esc_attr($s1_title[$i]).'">';
							echo '</label>';
							echo '</p>';
							do_action('tmpl_image_link',$j,$instance,$this);
							echo '<p>';
							echo '<label>'.__('Banner Slider Title Link',THEME_DOMAIN).$j;
							echo ' <input type="text" class="widefat"  name="'.$textbox_title_link.'[]" value="'.esc_attr($s1_title_link[$i]).'">';
							echo '</label>';
							echo '</p>';
							echo '<p>';
							echo '<label>'.sprintf(__('Banner Slider Image %s full URL',THEME_DOMAIN),$j);
							echo ' <input type="text" class="widefat"  name="'.$textbox_name.'[]" value="'.esc_attr($s1[$i]).'">';
							echo '</label>';
							echo '</p>';
							echo '</div>';
						}
					}
					?>
  </div>
  <a
					href="javascript:void(0);" id="addButton" class="addButton" onclick="add_textbox('<?php echo $textbox_name;?>','<?php echo $textbox_title_link; ?>','<?php echo $textbox_title;?>');"> <?php _e('+Add more',THEME_DOMAIN); ?> </a> &nbsp; | &nbsp; <a
					href="javascript:void(0);" id="removeButton" class="removeButton" onclick="remove_textbox();"><?php _e('-Remove',THEME_DOMAIN); ?> </a> </div>
<?php
		}
	}
	/*
	* templatic Slider widget init
	*/
	add_action('admin_footer','supreme_multitext_box');
	function supreme_multitext_box()
	{
		global $textbox_name,$textbox_title_link,$textbox_title;
		?>
<script type="application/javascript">
			var banner_counter = 2;
			function add_textbox(name,title_link,title)
			{
				var BannerNewTextBoxDiv = jQuery(document.createElement('div')).attr("class", 'TextBoxDiv' + banner_counter);
				BannerNewTextBoxDiv.html('<p><label>Banner Slider Title '+ banner_counter + ' </label>'+'<input type="text" class="widefat" name="'+title+'[]" id="textbox' + banner_counter + '" value="" ></p><p><label>Banner Slider Title Link '+ banner_counter + ' </label>'+'<input type="text" class="widefat" name="'+title_link+'[]" id="textbox' + banner_counter + '" value="" ></p><p><label>Banner Slider Image '+ banner_counter + ' full URL : </label>'+'<input type="text" class="widefat" name="'+name+'[]" id="textbox' + banner_counter + '" value="" ></p>');
				BannerNewTextBoxDiv.appendTo(".TextBoxesGroup");
				banner_counter++;
			}
			function remove_textbox()
			{
				if(banner_counter-1==1)
				{
					alert("you need one textbox required.");
					return false;
				}
				banner_counter--;
				jQuery(".TextBoxDiv" + banner_counter).remove();
			}
		</script>
<?php
	}
}
/*-------------------------------------------------------------------
Contact Us widget - specially to display contact form in sidebar
----------------------------------------------------------------------*/
if(!class_exists('supreme_contact_widget'))
{
	class supreme_contact_widget extends WP_Widget
	{
		function supreme_contact_widget()
		{
			//Constructor
			$widget_ops = array('classname'  => 'Contact Us','description'=> apply_filters('templ_contact_widget_desc_filter',__('A simple contact form visitors can use to get in touch with you. Works best in sidebar areas.',THEME_DOMAIN)) );
			$this->WP_Widget('supreme_contact_widget', apply_filters('templ_contact_widget_title_filter',__('T &rarr; Contact Us',THEME_DOMAIN)), $widget_ops);
		}
		function widget($args, $instance)
		{
			// prints the widget
			extract($args, EXTR_SKIP);
			$title = empty($instance['title']) ? __("Contact Us",THEME_DOMAIN) : apply_filters('widget_title', $instance['title']);
			$desc1 = empty($instance['desc1']) ? '' : apply_filters('widget_desc1', $instance['desc1']);
			echo $before_widget;
			$a = get_option("recaptcha_options");
			?>
<div class="widget contact_widget" id="contact_widget">
  <?php
				if($title)
				{
					?>
  <h3 class="widget-title"> <?php echo sprintf(__('%s',THEME_DOMAIN), $title);?> </h3>
  <?php
				}?>
  <?php
				$captcha=supreme_get_settings( 'supreme_global_contactus_captcha' );
				if($_POST && $_POST['contact_widget']){
					if($_POST['your-email']){
						function widget_send_contact_email($data){
							$toEmailName = get_option('blogname');
							$toEmail = get_option('admin_email');
							$subject = $data['your-subject'];
							$message = '';
							$message .= '<p>'.__("Dear",THEME_DOMAIN).' '.$toEmailName.',</p>';
							$message .= '<p>'.__("You have an inquiry message. Here are the details",THEME_DOMAIN).',</p>';
							$message .= '<p>'.__("Name",THEME_DOMAIN).' : '.$data['your-name'].'</p>';
							$message .= '<p>'.__("Email",THEME_DOMAIN).' : '.$data['your-email'].'</p>';
							$message .= '<p>'.__("Message",THEME_DOMAIN).' : '.nl2br($data['your-message']).'</p>';
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
							// Additional headers
							$headers .= 'To: '.$toEmailName.' <'.$toEmail.'>' . "\r\n";
							$headers .= 'From: '.$data['your-name'].' <'.$data['your-email'].'>' . "\r\n";
							
							// Mail it
							wp_mail($toEmail, $subject, $message, $headers);
							
							
							if(strstr($_REQUEST['request_url'],'?')){
								if(strstr($_REQUEST['request_url'],'?capt')){
									 $url = explode("?", $_REQUEST['request_url']);
									  $url = $url[0]."?message=success";
								}else{
									$url =  $_REQUEST['request_url'].'&message=success'	;	
								}
							}else{
								$url = $_REQUEST['request_url'].'?message=success'	;
							}
							echo "<script type='text/javascript'>location.href='".$url."#contact_widget';</script>";
						}
						if(file_exists(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php') && is_plugin_active('wp-recaptcha/wp-recaptcha.php') && $captcha == 1 ){
							require_once( get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php');
							$a = get_option("recaptcha_options");
							$privatekey = $a['private_key'];
							$resp = recaptcha_check_answer ($privatekey,getenv("REMOTE_ADDR"),$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);						
							if ($resp->is_valid ==""){
								if(strstr($_REQUEST['request_url'],'?')){
									if(strstr($_REQUEST['request_url'],'?message')){
										 $url = explode("?", $_REQUEST['request_url']);
										  $url = $url[0]."?capt=captch";
									}else{
										$url =  $_REQUEST['request_url'].'&capt=captch'	;	
									}
								}else{
									$url = $_REQUEST['request_url'].'?capt=captch'	;
								}
								echo "<script type='text/javascript'>location.href='".$url."#contact_widget';</script>";
							}else{
								$data = $_POST;
								widget_send_contact_email($data);
							}
						}
						elseif(file_exists(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php') && is_plugin_active('are-you-a-human/areyouahuman.php') && $captcha == 1 )
						{
							require_once( get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php');
							require_once(get_tmpl_plugin_directory().'are-you-a-human/includes/ayah.php');
							$ayah = new AYAH();
							$score = $ayah->scoreResult();
							if(!$score)
							{
								if(strstr($_REQUEST['request_url'],'?'))
									{
										wp_redirect($_REQUEST['request_url'].'&invalid=playthru');exit;
									}
									else
									{
										wp_redirect($_REQUEST['request_url'].'?invalid=playthru');exit;
									}
								}else{
									$data = $_POST;
									widget_send_contact_email($data);
								}
						}
						else{
							$data = $_POST;
							widget_send_contact_email($data);
						}
					}else{
						if(strstr($_REQUEST['request_url'],'?')){
							$url = $_REQUEST['request_url'].'&err=empty';
						}else{
							$url = $_REQUEST['request_url'].'?err=empty';
						}
						echo "<script type='text/javascript'>location.href='".$url."#contact_widget';</script>";
					}
				}
				?>
  <script type="text/javascript">
					var $cwidget = jQuery.noConflict();
					$cwidget(document).ready(function(){
							//global vars
							var contact_widget_frm = $cwidget("#contact_widget_frm");
							var your_name = $cwidget("#widget_your-name");
							var your_email = $cwidget("#widget_your-email");
							var your_subject = $cwidget("#widget_your-subject");
							var your_message = $cwidget("#widget_your-message");
							var your_name_Info = $cwidget("#widget_your_name_Info");
							var your_emailInfo = $cwidget("#widget_your_emailInfo");
							var your_subjectInfo = $cwidget("#widget_your_subjectInfo");
							var your_messageInfo = $cwidget("#widget_your_messageInfo");
							var recaptcha_response_field = $cwidget("#recaptcha_response_field");
							var recaptcha_response_fieldInfo = $cwidget("#recaptcha_response_fieldInfo");
							//On blur
							your_name.blur(validate_widget_your_name);
							your_email.blur(validate_widget_your_email);
							your_subject.blur(validate_widget_your_subject);
							your_message.blur(validate_widget_your_message);
							//On key press
							your_name.keyup(validate_widget_your_name);
							your_email.keyup(validate_widget_your_email);
							your_subject.keyup(validate_widget_your_subject);
							your_message.keyup(validate_widget_your_message);
							//On Submitting
							contact_widget_frm.submit(function()
								{
									if(validate_widget_your_name() & validate_widget_your_email() & validate_widget_your_subject() & validate_widget_your_message()
										<?php 
										 if( $captcha == 1){
										   if(file_exists(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php') && is_plugin_active('wp-recaptcha/wp-recaptcha.php')){
										 ?>
											& validate_widget_recaptcha() 		
										 <?php }
										 }  
										?>
									  ){
									
										hideform();
										return true
									}
									else
									{
										return false;
									}
								});
							//validation functions
							function validate_widget_your_name()
							{
								if($cwidget("#widget_your-name").val() == '')
								{
									your_name.addClass("error");
									your_name_Info.text("<?php _e('Please Enter Name',THEME_DOMAIN); ?>");
									your_name_Info.addClass("message_error");
									return false;
								}
								else
								{
									your_name.removeClass("error");
									your_name_Info.text("");
									your_name_Info.removeClass("message_error");
									return true;
								}
							}
							function validate_widget_your_email()
							{
								var isvalidemailflag = 0;
								if($cwidget("#widget_your-email").val() == '')
								{
									isvalidemailflag = 1;
								}else
								if($cwidget("#widget_your-email").val() != '')
								{
									var a = $cwidget("#widget_your-email").val();
									var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
									//if it's valid email
									if(filter.test(a))
									{
										isvalidemailflag = 0;
									}else
									{
										isvalidemailflag = 1;
									}
								}
								if(isvalidemailflag)
								{
									your_email.addClass("error");
									your_emailInfo.text("<?php _e('Please Enter valid Email',THEME_DOMAIN); ?>");
									your_emailInfo.addClass("message_error");
									return false;
								}else
								{
									your_email.removeClass("error");
									your_emailInfo.text("");
									your_emailInfo.removeClass("message_error");
									return true;
								}
							}
							function validate_widget_your_subject()
							{
								if($cwidget("#widget_your-subject").val() == '')
								{
									your_subject.addClass("error");
									your_subjectInfo.text("<?php _e('Please Enter Subject',THEME_DOMAIN); ?>");
									your_subjectInfo.addClass("message_error");
									return false;
								}
								else
								{
									your_subject.removeClass("error");
									your_subjectInfo.text("");
									your_subjectInfo.removeClass("message_error");
									return true;
								}
							}
							function validate_widget_your_message()
							{
								if($cwidget("#widget_your-message").val() == '')
								{
									your_message.addClass("error");
									your_messageInfo.text("<?php _e('Please Enter Message',THEME_DOMAIN); ?>");
									your_messageInfo.addClass("message_error");
									return false;
								}
								else
								{
									your_message.removeClass("error");
									your_messageInfo.text("");
									your_messageInfo.removeClass("message_error");
									return true;
								}
							}
							function validate_widget_recaptcha()
							{
								if($cwidget("#recaptcha_response_field").val() == '')
								{
									recaptcha_response_field.addClass("error");
									recaptcha_response_fieldInfo.text(" <?php _e("Please enter captcha",THEME_DOMAIN); ?> ");
									recaptcha_response_fieldInfo.addClass("message_error");
									return false;
								}
								else{
									recaptcha_response_field.removeClass("error");
									recaptcha_response_fieldInfo.text("");
									recaptcha_response_fieldInfo.removeClass("message_error");
									return true;
								}
							}
						});
				</script>
  <?php
					if(isset($_REQUEST['capt']) && $_REQUEST['capt'] == 'captch' && !isset($_REQUEST['message'])){
						$blank_field = $a['no_response_error'];
						$incorrect_field = $a['incorrect_response_error'];
				?>
  <p class="error_msg">
    <?php _e($incorrect_field,THEME_DOMAIN);?>
  </p>
  
  <?php } 
  if(isset($_REQUEST['invalid']) == 'playthru') {
			echo '<div class="error_msg">'.__('You need to play the game to contact us.',THEME_DOMAIN).'</div>';
		}
  if(isset($_REQUEST['message']) && $_REQUEST['message'] == 'success'){ ?>
  <p class="success_msg"> <?php echo apply_filters('templ_contact_widget_successmsg_filter',__('Thank you, your information is sent successfully.',THEME_DOMAIN));?> </p>
  <?php }elseif(isset($_REQUEST['err']) && $_REQUEST['err'] == 'empty'){	?>
  <p class="error_msg"> <?php echo apply_filters('templ_contact_widget_errormsg_filter',__('Please fill out all the fields before submitting.',THEME_DOMAIN));?> </p>
  <?php }	?>
  <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" id="contact_widget_frm" name="contact_frm" class="wpcf7-form">
    <input type="hidden" name="contact_widget" value="1" />
    <input type="hidden" name="request_url" value="<?php echo $_SERVER['REQUEST_URI'];?>" />
    <?php do_action('after_contact_form'); ?>
    <div class="form_row ">
      <input type="text" name="your-name" id="widget_your-name" value="" class="textfield" size="40" placeholder="<?php _e('Name*',THEME_DOMAIN);?>" />
      <span id="widget_your_name_Info" class="error">
      <?php _e('',THEME_DOMAIN); ?>
      </span> </div>
    <div class="form_row ">
      <input type="text" name="your-email" id="widget_your-email" value="" class="textfield" size="40" placeholder="<?php _e('Email',THEME_DOMAIN);?> *"/>
      <span id="widget_your_emailInfo"  class="error"> </span> </div>
    <div class="form_row ">
      <input type="text" name="your-subject" id="widget_your-subject" value="" size="40" class="textfield" placeholder="<?php _e('Subject',THEME_DOMAIN);?>  *"/>
      <span id="widget_your_subjectInfo"> </span> </div>
    <?php
					do_action('after_contact_form_end');
					do_action('after_contact_message_start');
					?>
    <div class="form_row clearfix">
      <textarea name="your-message" id="widget_your-message" cols="40" class="textarea textarea2" rows="10" placeholder="<?php _e('Message',THEME_DOMAIN);?>"></textarea>
      <span id="widget_your_messageInfo"  class="error"> </span> </div>
    <?php  do_action('after_contact_message_end'); ?>
    <div class="clearfix">
      <?php 
							if($captcha == 1)
							{
								if(file_exists(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php') && is_plugin_active('wp-recaptcha/wp-recaptcha.php'))
								{							
									require_once(get_tmpl_plugin_directory().'wp-recaptcha/recaptchalib.php');
									echo '<div class="form_row">';
									echo '<label class="recaptcha_claim">'.__('Verify words',THEME_DOMAIN).' <span>*</span></label>';
									$publickey = $a['public_key']; // you got this from the signup page 					
									?>
      <div class="claim_recaptcha_div"><?php echo recaptcha_get_html($publickey); ?><span id="recaptcha_response_fieldInfo"  class="error"></span> </div>
      <?php 
									echo '</div>';
								}
								elseif(file_exists(get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php') && is_plugin_active('are-you-a-human/areyouahuman.php') )
								{
									require_once( get_tmpl_plugin_directory().'are-you-a-human/areyouahuman.php');
									require_once(get_tmpl_plugin_directory().'are-you-a-human/includes/ayah.php');
									$ayah = ayah_load_library();
									echo $ayah->getPublisherHTML();
								}
							}			
						?>
      <input type="submit" value="<?php _e('Send',THEME_DOMAIN);?>" class="b_submit" />
    </div>
  </form>
</div>
<?php
			echo $after_widget;
		}
		function update($new_instance, $old_instance)
		{
			//save the widget
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['desc1'] = ($new_instance['desc1']);
			return $instance;
		}
		function form($instance)
		{
			//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array('title'=> '') );
			$title = (strip_tags($instance['title'])) ? strip_tags($instance['title']) : __("Contact Us",THEME_DOMAIN);
			$desc1 = empty($instance['desc1']) ? '' : ($instance['desc1']);
			?>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>">
    <?php _e('Widget Title',THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<?php
		}
	}
}
/*------------------------------------------------------------------
* Create the templatic recent post widget
*--------------------------------------------------------------------*/
if(!class_exists('supreme_recent_post'))
{
	class supreme_recent_post extends WP_Widget
	{
		function supreme_recent_post()
		{
			//Constructor
			$widget_ops = array('classname'  => 'widget Templatic listing_post','description'=> __('Showcase posts from a particular category and post type. Use in main content, subsidiary and sidebar areas.',THEME_DOMAIN) );
			$this->WP_Widget('supreme_recent_post', __('T &rarr; Display Posts',THEME_DOMAIN), $widget_ops);
		}
		function widget($args, $instance)
		{
			// prints the widget
			extract($args, EXTR_SKIP);
			// defaults
			$instance = wp_parse_args( (array)$instance, array(
					'title'             => 'Blog Listings',
					'post_type'         =>'post',
					'post_type_taxonomy'=> '',
					'post_number'       => 5,
					'orderby'           => 'date',
					'order'             => 'DESC',
					'show_image'        => 0,
					'image_alignment'   => '',
					'image_size'        => '',
					'show_gravatar'     => 0,
					'gravatar_alignment'=> '',
					'gravatar_size'     => '',
					'show_title'        => 0,
					'show_byline'       => 0,
					'post_info'         => '[post_date] ' . __('By', THEME_DOMAIN) . ' [post_author_posts_link] [post_comments]',
					'show_content'      => 'content',
					'content_limit'     => 25,
					'enable_categories' => '',
					'more_text'         => __('[Read More...]', THEME_DOMAIN),
				));
			echo $before_widget;
			// Set up the author bio
			if(!empty($instance['title']))
			echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;
			remove_all_actions('posts_where');
			$taxonomies = get_object_taxonomies( (object) array('post_type'=> $instance['post_type'],'public'   => true,'_builtin' => true ));
			if($instance['post_type_taxonomy'])
			$cat_id = $instance['post_type_taxonomy'];
			else
			{
				$args = array('type'    => 'post','child_of'=> 0,'taxonomy'=> $taxonomies[0]);
				$categories = get_categories( $args );
				foreach($categories as $cat)
				if(isset($cat_id) && $cat_id != ''):
				$cat_id .= $cat->term_id.",";
				endif;
				$cat_id = substr(@$cat_id,0, - 1);
			}
			if(is_plugin_active('woocommerce/woocommerce.php') && $instance['post_type'] == 'product')
			{
				$taxonomies[0] = $taxonomies[1];
			}
			if($instance['post_type_taxonomy'])
			{
				$featured_arg = array('post_type'=> $instance['post_type'],'showposts'=> $instance['post_number'],'orderby'  => $instance['orderby'],'order'    => $instance['order'],'post_status'=>'publish','tax_query' => array(
						array(
							'taxonomy'=>$taxonomies[0],
							'field'   => 'id',
							'terms'    =>array($cat_id),
							'operator'=> 'IN'
						)
					));
			}
			else
			{
				$featured_arg = array('post_type'=> $instance['post_type'],'post_status'=>'publish','showposts'=> $instance['post_number'],'orderby'  => $instance['orderby'],'order'    => $instance['order']
				);
			}
			remove_all_actions('posts_orderby');
			//add_filter('posts_join', 'templatic_posts_where_filter');
			if(is_plugin_active('wpml-translation-management/plugin.php') && function_exists('templatic_widget_wpml_filter'))
			{
				add_filter('posts_where','templatic_widget_wpml_filter');
			}
			
			/* Add Do action for insert extra post where filter */
			do_action('post_listing_widget_before_post_where',$instance);
			$featured_posts = new WP_Query($featured_arg);
			if(function_exists('templatic_widget_wpml_filter'))
			{
				remove_filter('posts_where','templatic_widget_wpml_filter');
			}
			/* Add Do action for remove extra added post where filter */
			do_action('post_listing_widget_after_post_where',$instance);
			//echo $featured_posts->request;
			global $post;
			
			if($featured_posts->have_posts()) :
			?>
<div class="listing_post_wrapper">
  <?php
				while($featured_posts->have_posts()) : $featured_posts->the_post();
				echo '<div '; post_class(); echo '>';
				/*Show gravatar */
				if(!empty($instance['show_gravatar'])) :
				echo '<span class="'.esc_attr($instance['gravatar_alignment']).'">';
				echo get_avatar( get_the_author_meta('ID'), $instance['gravatar_size'] );
				echo '</span>';
				endif;
				/* show post title*/				
				do_action('listing_post_before_image',$instance);
				$a     = get_intermediate_image_sizes();
				$width = '';
				$height= '';
				global $_wp_additional_image_sizes;
				for($i = 0;$i < count($_wp_additional_image_sizes);$i++){
					$a = array_keys($_wp_additional_image_sizes);
					for($k = 0;$k < count($a);$k++){
						if($a[$k] == $instance['image_size']){
							$width = $_wp_additional_image_sizes[$a[$k]]['width'];
							$height= $_wp_additional_image_sizes[$a[$k]]['height'];
						}
					}
				}
				if(!empty($instance['show_image'])) :	
				
						$post_image = '';
						if (has_post_thumbnail( $post->ID  ) ){
							$img_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $instance['image_size'] );
							$post_image = $img_arr[0];
							$style='';							
						}
						elseif(function_exists('bdw_get_images_plugin')){
							$post_img = bdw_get_images_plugin($post->ID,$instance['image_size']);	
							$post_image = @$post_img[0]['file'];
							$style='';
						}
						if(!$post_image)
						{
							$theme_options = get_option(supreme_prefix().'_theme_settings');
							$supreme_display_noimage = $theme_options['supreme_display_noimage'];
							if($supreme_display_noimage){
								$post_image = apply_filters('supreme_noimage-url',get_template_directory_uri()."/images/noimage.jpg");
								
								$style = apply_filters('supreme_noimage-size',"width='50px' height='50px'");
							}
						} 						
						?>
  <figure class="post_fig">
    <?php if($featured){echo '<span class="featured_tag">'.__('Featured',THEME_DOMAIN).'</span>';}?>
    <a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute( 'echo=1' ); ?>" rel="bookmark" class="featured-image-link"><img src="<?php echo $post_image; ?>" width="<?php echo $width;?>" height="<?php echo $height;?>" alt="<?php the_title_attribute( 'echo=1' ); ?>" <?php echo $style; ?>/></a> </figure>
  <?php				
					//echo $image = get_the_image( array( 'echo' => false,'','size' =>$instance['image_size'],'default_image'=>get_template_directory_uri()."/images/noimage.jpg" ) );
				endif;
				do_action('listing_post_after_image',$instance);
				
				if(!empty($instance['show_title'])) :
					printf( '<h2><a href="%s" title="%s">%s</a></h2>', get_permalink(), the_title_attribute('echo=0'), the_title_attribute('echo=0') );
				endif;
				
				if(!empty($instance['show_content'])) :
				echo "<div class='entry-summary'>";
					if($instance['show_content'] == 'excerpt') :
						the_excerpt();
					elseif($instance['show_content'] == 'content-limit') :
						the_content_limit( (int)$instance['content_limit'], esc_html( $instance['more_text'] ) );
					else :
						the_content( esc_html( $instance['more_text'] ) );
					endif;
				echo '</div>';
				endif;
				echo '</div><!--end post_class()-->';
				endwhile; wp_reset_query();
				?>
</div>
<!-- listing_post_wrapper end-->
<?php
			endif;
			echo $after_widget;
		}
		function update($new_instance, $old_instance)
		{
			//save the widget
			return $new_instance;
			//return $instance;
		}
		function form($instance)
		{
			//widgetform in backend
			$instance = wp_parse_args( (array)$instance, array(
					'title'             => 'Blog Listings',
					'post_type'         =>'post',
					'post_type_taxonomy'=> '',
					'post_number'       => 5,
					'orderby'           => 'date',
					'order'             => 'DESC',
					'show_image'        => 0,
					'image_alignment'   => '',
					'image_size'        => '',
					'show_gravatar'     => 0,
					'gravatar_alignment'=> '',
					'gravatar_size'     => '',
					'show_title'        => 0,
					'show_byline'       => 0,
					'post_info'         => '[post_date] ' . __('By', THEME_DOMAIN) . ' [post_author_posts_link] [post_comments]',
					'show_content'      => 'content',
					'content_limit'     => 25,
					'enable_categories' => '',
					'more_text'         => __('[Read More...]', THEME_DOMAIN),
				));
			?>
               <script type="text/javascript">
			function select_show_content_limit(str,div){					
					if(str.value=='content-limit'){						
						jQuery('#'+div).slideToggle('slow');
					}else{
						jQuery('#'+div).hide();
					}
				}
			</script>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>">
    <?php _e('Title:',THEME_DOMAIN);?>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('post_type');?>" >
    <?php _e('Select Post type:',THEME_DOMAIN);?>
    <select  id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widefat">
      <?php
						$all_post_types = get_post_types();
						foreach($all_post_types as $post_types)
						{
							if( $post_types != "page" && $post_types != "attachment" && $post_types != "revision" && $post_types != "nav_menu_item" && $post_types != "product_variation" && $post_types != "shop_order" && $post_types != "shop_coupon" && $post_types != "admanager" )
							{
								?>
      <option value="<?php echo $post_types;?>" <?php
 if($post_types == $instance['post_type'])echo "selected";?>> <?php echo esc_attr($post_types);?> </option>
      <?php
							}
						}
						?>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('post_type_taxonomy');?>" >
    <?php _e('Select Category:',THEME_DOMAIN);?>
    <select id="<?php echo $this->get_field_id('post_type_taxonomy'); ?>" name="<?php echo $this->get_field_name('post_type_taxonomy'); ?>" class="widefat" >
      <option value="">
      <?php _e('---Select Category wise recent post ---',THEME_DOMAIN); ?>
      </option>
      <?php
						$taxonomies = get_taxonomies( array('public'=> true ), 'objects' );
						$taxonomies = apply_filters( 'templatic_exclude_taxonomies',$taxonomies );?>
      <?php
						foreach( $taxonomies as $taxonomy )
						{
							$query_label = '';
							if( !empty( $taxonomy->query_var ) )
							$query_label = $taxonomy->query_var;
							else
							$query_label = $taxonomy->name;
							if($taxonomy->labels->name != 'Tags' && $taxonomy->labels->name != 'Format'):
							?>
      <optgroup label="<?php echo esc_attr( $taxonomy->object_type[0])."-".esc_attr($taxonomy->labels->name); ?>">
      <?php
								$terms = get_terms( $taxonomy->name, 'orderby=name&hide_empty=1' );
								foreach( $terms as $term )
								{
									$term_value = $term->term_id;	?>
      <option style="margin-left: 8px; padding-right:10px;" value="<?php echo $term_value ?>" <?php
 if($instance['post_type_taxonomy'] == $term_value) echo "selected";?>> <?php echo '-' . esc_attr( $term->name ); ?> </option>
      <?php
								} ?>
      </optgroup>
      <?php
							endif;
						}
						?>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('post_number'); ?>">
    <?php _e('Number of posts:',THEME_DOMAIN);?>
    <input class="widefat" id="<?php echo $this->get_field_id('post_number'); ?>" name="<?php echo $this->get_field_name('post_number'); ?>" type="text" value="<?php echo $instance['post_number']; ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('orderby'); ?>">
    <?php _e('Order By', THEME_DOMAIN); ?>
    : </label>
  <select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
    <option style="padding-right:10px;" value="date" <?php selected('date', $instance['orderby']); ?>>
    <?php _e('Date', THEME_DOMAIN); ?>
    </option>
    <option style="padding-right:10px;" value="title" <?php selected('title', $instance['orderby']); ?>>
    <?php _e('Title', THEME_DOMAIN); ?>
    </option>
    <option style="padding-right:10px;" value="ID" <?php selected('ID', $instance['orderby']); ?>>
    <?php _e('ID', THEME_DOMAIN); ?>
    </option>
    <option style="padding-right:10px;" value="comment_count" <?php selected('comment_count', $instance['orderby']); ?>>
    <?php _e('Comment Count', THEME_DOMAIN); ?>
    </option>
    <option style="padding-right:10px;" value="rand" <?php selected('rand', $instance['orderby']); ?>>
    <?php _e('Random', THEME_DOMAIN); ?>
    </option>
  </select>
</p>
<p>
  <label for="<?php echo $this->get_field_id('order'); ?>">
    <?php _e('Sort Order', THEME_DOMAIN); ?>
    : </label>
  <select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
    <option style="padding-right:10px;" value="DESC" <?php selected('DESC', $instance['order']); ?>>
    <?php _e('Descending (3, 2, 1)', THEME_DOMAIN); ?>
    </option>
    <option style="padding-right:10px;" value="ASC" <?php selected('ASC', $instance['order']); ?>>
    <?php _e('Ascending (1, 2, 3)', THEME_DOMAIN); ?>
    </option>
  </select>
</p>
<p>
  <input id="<?php echo $this->get_field_id('show_gravatar'); ?>" type="checkbox" name="<?php echo $this->get_field_name('show_gravatar'); ?>" value="1" <?php checked(1, $instance['show_gravatar']); ?>/>
  <label for="<?php echo $this->get_field_id('show_gravatar'); ?>">
    <?php _e('Show Author Gravatar', THEME_DOMAIN); ?>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('gravatar_size'); ?>">
    <?php _e('Gravatar Size', THEME_DOMAIN); ?>
    : </label>
  <select id="<?php echo $this->get_field_id('gravatar_size'); ?>" name="<?php echo $this->get_field_name('gravatar_size'); ?>">
    <option style="padding-right:10px;" value="45" <?php selected(45, $instance['gravatar_size']); ?>>
    <?php _e('Small (45px)', THEME_DOMAIN); ?>
    </option>
    <option style="padding-right:10px;" value="65" <?php selected(65, $instance['gravatar_size']); ?>>
    <?php _e('Medium (65px)', THEME_DOMAIN); ?>
    </option>
    <option style="padding-right:10px;" value="85" <?php selected(85, $instance['gravatar_size']); ?>>
    <?php _e('Large (85px)', THEME_DOMAIN); ?>
    </option>
    <option style="padding-right:10px;" value="125" <?php selected(125, $instance['gravatar_size']); ?>>
    <?php _e('Extra Large (125px)', THEME_DOMAIN); ?>
    </option>
  </select>
</p>
<p>
  <input id="<?php echo $this->get_field_id('show_image'); ?>" type="checkbox" name="<?php echo $this->get_field_name('show_image'); ?>" value="1" <?php checked(1, $instance['show_image']); ?>/>
  <label for="<?php echo $this->get_field_id('show_image'); ?>">
    <?php _e('Show Featured Image', THEME_DOMAIN); ?>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('image_size'); ?>">
    <?php _e('Image Size', THEME_DOMAIN); ?>
    : </label>
  <?php
				if(function_exists('get_additional_image_sizes'))
				{
					$sizes = get_additional_image_sizes();
				}
				else
				{
					$sizes = supreme_get_additional_image_sizes();
				} ?>
  <select id="<?php echo $this->get_field_id('image_size'); ?>" name="<?php echo $this->get_field_name('image_size'); ?>">
    <option style="padding-right:10px;" value="thumbnail">
    <?php _e('thumb', THEME_DOMAIN); ?>
    (<?php echo get_option('thumbnail_size_w'); ?>x<?php echo get_option('thumbnail_size_h'); ?>) </option>
    <?php
					foreach((array)$sizes as $name => $size) :
					echo '<option style="padding-right: 10px;" value="'.esc_attr($name).'" '.selected($name, $instance['image_size'], FALSE).'>'.esc_html($name).' ('.$size['width'].'x'.$size['height'].')</option>';
					endforeach;
					?>
  </select>
</p>
<p>
  <input id="<?php echo $this->get_field_id('show_title'); ?>" type="checkbox" name="<?php echo $this->get_field_name('show_title'); ?>" value="1" <?php checked(1, $instance['show_title']); ?>/>
  <label for="<?php echo $this->get_field_id('show_title'); ?>">
    <?php _e('Show Post Title', THEME_DOMAIN); ?>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('show_content'); ?>">
    <?php _e('Content Type', THEME_DOMAIN); ?>
    : </label>
  <select id="<?php echo $this->get_field_id('show_content'); ?>" name="<?php echo $this->get_field_name('show_content'); ?>" onchange="select_show_content_limit(this,'<?php echo $this->get_field_id('show_content_limit'); ?>')">
    <option value="content" <?php selected('content' , $instance['show_content'] ); ?>>
    <?php _e('Show Content', THEME_DOMAIN); ?>
    </option>
    <option value="excerpt" <?php selected('excerpt' , $instance['show_content'] ); ?>>
    <?php _e('Show Excerpt', THEME_DOMAIN); ?>
    </option>
    <option value="content-limit" <?php selected('content-limit' , $instance['show_content'] ); ?>>
    <?php _e('Show Content Limit', THEME_DOMAIN); ?>
    </option>
    <option value="" <?php selected('' , $instance['show_content'] ); ?>>
    <?php _e('No Content', THEME_DOMAIN); ?>
    </option>
  </select>
</p>
<div id="<?php echo $this->get_field_id('show_content_limit'); ?>" style="<?php if($instance['show_content']!='content-limit'){echo "display:none";}?>">
     <p>
       <label for="<?php echo $this->get_field_id('content_limit'); ?>">
         <?php _e('Limit content to', THEME_DOMAIN); ?>
       </label>
       <input type="text" id="<?php echo $this->get_field_id('image_alignment'); ?>" name="<?php echo $this->get_field_name('content_limit'); ?>" value="<?php echo esc_attr(intval($instance['content_limit'])); ?>" size="3" />
       <?php _e('characters', THEME_DOMAIN); ?>
     </p>
     <p>
       <label for="<?php echo $this->get_field_id('more_text'); ?>">
         <?php _e('More Text (if applicable)', THEME_DOMAIN); ?>
         : </label>
       <input type="text" id="<?php echo $this->get_field_id('more_text'); ?>" name="<?php echo $this->get_field_name('more_text'); ?>" value="<?php echo esc_attr($instance['more_text']); ?>" />
     </p>
</div>
<?php
		}
	}
}
/*--------------------------------------------------------------
* Recent post widget init
-------------------------------------------------------------*/
if(!class_exists('supreme_popular_post'))
{
	class supreme_popular_post extends WP_Widget
	{
		function supreme_popular_post()
		{
			//Constructor
			$widget_ops = array('classname'  => 'widget Templatic Popular Posts Widget ','description'=> __('Show a list of popular posts from a particular post type. Popularity is determined by total/daily views or comments. Works best in sidebar areas.',THEME_DOMAIN) );
			$this->WP_Widget('templatic_popular_post_technews', __('T &rarr; Popular Posts',THEME_DOMAIN), $widget_ops);
		}
		function widget($args, $instance)
		{
			extract($args, EXTR_SKIP);
			echo $before_widget;
			$title = empty($instance['title']) ? 'Popular Posts' : apply_filters('widget_title', $instance['title']);
			$post_type = empty($instance['post_type']) ? 'post' : apply_filters('widget_post_type', $instance['post_type']);
			$number = empty($instance['number']) ? 5 : apply_filters('widget_number', $instance['number']);
			$slide = empty($instance['slide']) ? 5 : apply_filters('widget_slide', $instance['slide']);
			$popular_per = empty($instance['popular_per']) ? 'comments' : apply_filters('widget_popular_per', $instance['popular_per']);
			$show_excerpt = empty($instance['show_excerpt']) ? 'comments' : apply_filters('widget_show_excerpt', $instance['show_excerpt']);
			$show_excerpt_length = empty($instance['show_excerpt_length']) ? 27 : apply_filters('widget_show_excerpt', $instance['show_excerpt_length']);
			$pagination_position = empty($instance['pagination_position']) ? 0 : apply_filters('widget_pagination_position', $instance['pagination_position']);
			global $wpdb,$posts,$post,$query_string;
			$now       = gmdate("Y-m-d H:i:s",time());
			$lastmonth = gmdate("Y-m-d H:i:s",gmmktime(date("H"), date("i"), date("s"), date("m") - 12,date("d"),date("Y")));
			if($popular_per == 'views')
			{
				$popularposts = "SELECT DISTINCT $wpdb->posts.*, (meta_value+0) AS views FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE  post_status = 'publish' AND meta_key = 'viewed_count' AND post_password = '' AND post_type='$post_type' ORDER BY views DESC LIMIT 0,$number";
			}
			elseif($popular_per == 'dailyviews')
			{
				$popularposts = "SELECT DISTINCT $wpdb->posts.*, (meta_value+0) AS views FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE  post_status = 'publish' AND meta_key = 'viewed_count_daily' AND post_password = '' AND post_type='$post_type' ORDER BY views DESC LIMIT 0,$number";
			}
			else
			{
				$popularposts = "SELECT COUNT(ID) as count FROM $wpdb->posts, $wpdb->comments WHERE comment_approved = '1' AND $wpdb->posts.ID=$wpdb->comments.comment_post_ID AND post_status = 'publish' AND post_date < '$now' AND post_date > '$lastmonth' AND comment_status = 'open' AND post_type='$post_type' LIMIT 0,$number";
			}
			
	
			$totalpost = $wpdb->get_results($popularposts);
			
			if(!empty($totalpost))
			{
				if( $title <> "" )
				{
					echo ' <h3 class="widget-title">'.$title.'</h3>';
				}
			}
			
	
			if(!empty($totalpost) && count($totalpost) > 0)
			{
				@$countpost = (count($totalpost) < $number) ? count($totalpost) : $number ;
				if($popular_per == 'views' || $popular_per == 'dailyviews' )
				{
					$countpost = count($totalpost) ;
				}
				$dot = ceil($countpost / $slide);
			}
			if(is_plugin_active('wpml-translation-management/plugin.php'))
			{
				global $sitepress;
				$current_lang_code = ICL_LANGUAGE_CODE;
				$language          = $current_lang_code;
			}
			if( $pagination_position == 1 && !empty($totalpost) && count($totalpost) > 0)
			{
				?>
<div class="postpagination clearfix">
  <?php 
					if($dot != 1)
					{
						?>
  <a num="1" rel="0" rev="<?php echo $slide; ?>" class="active"> 1 </a>
  <?php
						for($c = 1; $c < $dot; $c++)
						{
							$start = ($c * $slide);
							echo '<a num="'.($c + 1).'" rel="'.$start.'" rev="'.$slide.'">'.($c + 1).'</a>';
						}
						?>
  <?php
					} ?>
</div>
<?php
			}
			if(empty($totalpost) && count($totalpost) > 0 )
			{
				?>
<div class="popular_post templatic_popular_post_technews">
  <ul class=" clearfix list" id="">
    <li>
      <?php _e("There is no post available right now.",THEME_DOMAIN);?>
    </li>
  </ul>
</div>
<?php
			}
			?>
<div class="popular_post templatic_popular_post_technews">
  <ul class="listingview clearfix list" id="list">
  </ul>
</div>
<?php
			if( @$pagination_position != 1 && !empty($totalpost) && count($totalpost) > 0 )
			{
				?>
<div class="postpagination clearfix">
  <?php
					if($dot != 1)
					{
						?>
  <a num="1" rel="0" rev="<?php echo $slide; ?>" class="active"> 1 </a>
  <?php
						for($c = 1; $c < $dot; $c++)
						{
							$start = ($c * $slide);
							echo '<a num="'.($c + 1).'" rel="'.$start.'" rev="'.$slide.'">'.($c + 1).'</a>';
						}
						?>
  <?php
					} ?>
</div>
<?php
			} ?>
<script type="text/javascript">
				jQuery('.postpagination a').click(function()
					{
						var start =  parseInt(jQuery(this).attr('rel'));
						var end =  parseInt(jQuery(this).attr('rev'));
						var num =parseInt(jQuery(this).attr('num'));
						jQuery('.postpagination a').attr('class','');
						jQuery(this).attr('class','active');
						jQuery('#list').load('<?php echo get_template_directory_uri(); ?>/library/functions/loadpopularpost.php', { "limitarr[]": [start, end,(start + end),'<?php echo $post_type;?>',num,'<?php echo $popular_per;?>',<?php echo $number;?>,'<?php echo @$language; ?>','<?php echo $show_excerpt; ?>','<?php echo $show_excerpt_length;?>']}, function(){});
					});
				jQuery('#list').load('<?php echo get_template_directory_uri(); ?>/library/functions/loadpopularpost.php', { "limitarr[]": [0, <?php echo $slide; ?>,<?php echo $number; ?>,'<?php echo $post_type;?>',1,'<?php echo $popular_per;?>',<?php echo $number;?>,'<?php echo @$language; ?>','<?php echo $show_excerpt; ?>','<?php echo $show_excerpt_length; ?>'] }, function(){});
			</script>
<?php
			echo $after_widget;
		}
		function update($new_instance, $old_instance)
		{
			return $new_instance;
		}
		function form($instance)
		{
			$instance = wp_parse_args( (array)$instance, array(
					'title'              => 'Popular Posts',
					'post_type'          =>'post',
					'number'             => 10,
					'slide'              => 3,
					'popular_per'        => 'comments',
					'show_excerpt'       => 1,
					'show_excerpt_length'=> 27,
					'pagination_position'=> 0,
				));
			//widgetform in backend
			?>
<p>
  <label for="<?php  echo $this->get_field_id('title'); ?>">
    <?php _e('Title',THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php  echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('post_type');?>" >
    <?php _e('Select Post type',THEME_DOMAIN);?>
    :
    <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widefat" >
      <?php
						$all_post_types = get_post_types();
						foreach($all_post_types as $post_types)
						{
							if( $post_types != "page" && $post_types != "attachment" && $post_types != "revision" && $post_types != "nav_menu_item" && $post_types != "product" && $post_types != "product_variation" && $post_types != "shop_order" && $post_types != "shop_coupon" && $post_types != "admanager")
							{
								?>
      <option value="<?php echo esc_attr($post_types);?>" <?php
 if($post_types == $instance['post_type'])echo "selected";?>> <?php echo $post_types;?> </option>
      <?php
							}
						}
						?>
    </select>
  </label>
</p>
<p>
  <label for="<?php  echo $this->get_field_id('number'); ?>">
    <?php _e('Total Number of Posts',THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php  echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $instance['number']; ?>" />
  </label>
</p>
<?php
			if(current_theme_supports('excerpt_in_popular_post'))
			{
				?>
<p>
  <?php
					if($instance['show_excerpt'] == 1)
					{
						$chk = "checked=cheked";
					}
					else
					{
						$chk = "";
					} ?>
  <label for="<?php  echo $this->get_field_id('show_excerpt'); ?>">
    <input id="<?php  echo $this->get_field_id('show_excerpt'); ?>" name="<?php echo $this->get_field_name('show_excerpt'); ?>" type="checkbox" value="1" <?php echo $chk; ?>/>
    <?php _e('Show excerpt',THEME_DOMAIN);?>
  </label>
  <br/>
  <small>
  <?php _e("You can change Excerpt length from customizer Listing Page Settings",THEME_DOMAIN);?>
  </small> </p>
<p>
  <label for="<?php  echo $this->get_field_id('show_excerpt_length'); ?>">
    <?php _e('Excerpt Length',THEME_DOMAIN);?>
    :
    <input  class="widefat" id="<?php  echo $this->get_field_id('show_excerpt_length'); ?>" name="<?php echo $this->get_field_name('show_excerpt_length'); ?>" type="text" value="<?php echo $instance['show_excerpt_length']; ?>"/>
  </label>
</p>
<?php
			} ?>
<p>
  <label for="<?php  echo $this->get_field_id('slide'); ?>">
    <?php _e('Number of Posts Per Slide',THEME_DOMAIN);?>
    :
    <input class="widefat" id="<?php  echo $this->get_field_id('slide'); ?>" name="<?php echo $this->get_field_name('slide'); ?>" type="text" value="<?php echo $instance['slide']; ?>" />
  </label>
</p>
<p>
  <label for="<?php  echo $this->get_field_id('popular_per'); ?>">
    <?php _e('Shows post as per view counting/comments',THEME_DOMAIN);?>
    :
    <select class="widefat" id="<?php  echo $this->get_field_id('popular_per'); ?>" onchange="show_hide_info(this.value,'<?php echo $this->get_field_id('daily_view'); ?>')" name="<?php echo $this->get_field_name('popular_per'); ?>">
      <option value="views" <?php
 if($instance['popular_per'] == 'views')
{ ?>selected='selected'<?php } ?>>
      <?php _e('Total views',THEME_DOMAIN); ?>
      </option>
      <option value="dailyviews" <?php
 if($instance['popular_per'] == 'dailyviews')
{ ?>selected='selected'<?php } ?>>
      <?php _e('Daily views',THEME_DOMAIN); ?>
      </option>
      <option value="comments" <?php
 if($instance['popular_per'] == 'comments')
{ ?>selected='selected'<?php } ?>>
      <?php _e('Total comments',THEME_DOMAIN); ?>
      </option>
    </select>
  </label>
</p>
<p id="<?php echo $this->get_field_id('daily_view'); ?>" style="margin:0 0 20px;<?php
 if( @$instance['popular_per'] == "" )
{echo 'display:none';}
elseif($instance['popular_per'] == 'views' || $instance['popular_per'] == 'dailyviews'  )
{ echo 'display:block'; }
else
{ echo 'display:none'; }?>"> <small>
  <?php _e("To make this widget work with daily views/total views, enable view counter from Tevolution - &gt; General Settings - &gt; Detail/Single Page Settings",THEME_DOMAIN);?>
  </small> </p>
<p>
  <label for="<?php  echo $this->get_field_id('pagination_position'); ?>">
    <?php _e('Pagination Position',THEME_DOMAIN);?>
    :
    <select class="widefat" id="<?php  echo $this->get_field_id('pagination_position'); ?>" name="<?php echo $this->get_field_name('pagination_position'); ?>">
      <option value="0" <?php
 if($instance['pagination_position'] == 0)
{ ?>selected='selected'<?php } ?>>
      <?php _e('After Posts',THEME_DOMAIN); ?>
      </option>
      <option value="1" <?php
 if($instance['pagination_position'] == 1)
{ ?>selected='selected'<?php } ?>>
      <?php _e('Before Posts',THEME_DOMAIN); ?>
      </option>
    </select>
  </label>
</p>
<script type="text/javascript">
				function show_hide_info(value,p_id)
				{
					if( "views" == value || "dailyviews" == value )
					{
						document.getElementById(p_id).style.display="block";
					}else
					{
						document.getElementById(p_id).style.display="none";
					}
				}
			</script>
<?php
		}
	}
}
/*-------------------------------------------------------------
Recent reviews widget
---------------------------------------------------------------*/
define('NUMBER_REVIEWS_TEXT',__('Number of Reviews',THEME_DOMAIN));
if(!class_exists('supreme_recent_review'))
{
	class supreme_recent_review extends WP_Widget
	{
		function supreme_recent_review()
		{
			//Constructor
			$widget_ops = array('classname'  => 'widget recent_reviews Recent Review','description'=> __('Display recent reviews from a specific post type. Works best in sidebar areas.',THEME_DOMAIN) );
			$this->WP_Widget('widget_comment', __('T &rarr; Recent Reviews',THEME_DOMAIN), $widget_ops);
		}
		function widget($args, $instance)
		{
			// prints the widget
			extract($args, EXTR_SKIP);
			$title = empty($instance['title']) ? 'Recent Reviews' : apply_filters('widget_title', $instance['title']);
			$post_type = empty($instance['post_type']) ? 'post' : apply_filters('widget_post_type', $instance['post_type']);
			$count = empty($instance['count']) ? '5' : apply_filters('widget_count', $instance['count']);
			echo $before_widget;
			if(function_exists('recent_review_comments'))
			{
				recent_review_comments(30, $count, 100, false,$post_type,$title);
			}
			echo $after_widget;
		}
		function update($new_instance, $old_instance)
		{
			//save the widget
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['post_type'] = strip_tags($new_instance['post_type']);
			$instance['count'] = strip_tags($new_instance['count']);
			return $instance;
		}
		function form($instance)
		{
			//widgetform in backend
			$instance = wp_parse_args( (array) $instance, array('title'    => 'Recent Reviews','post_type'=>'post','count'    => 5 ) );
			$title     = strip_tags($instance['title']);
			$post_type = strip_tags($instance['post_type']);
			$count     = strip_tags($instance['count']);
			?>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>"> <?php echo TITLE_TEXT; ?>:
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('post_type');?>" >
    <?php _e('Select Post:',THEME_DOMAIN);?>
    <select  id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widefat">
      <?php
						$all_post_types = get_post_types();
						foreach($all_post_types as $post_types)
						{
							if( $post_types != "page" && $post_types != "attachment" && $post_types != "revision" && $post_types != "nav_menu_item" && $post_types != "product" && $post_types != "product_variation" && $post_types != "shop_order" && $post_types != "shop_coupon" && $post_types != "admanager")
							{
								?>
      <option value="<?php echo $post_types;?>" <?php
 if($post_types == $post_type)echo "selected";?>> <?php echo esc_attr($post_types);?> </option>
      <?php
							}
						}
						?>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('count'); ?>"> <?php echo NUMBER_REVIEWS_TEXT; ?>:
    <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr($count); ?>" />
  </label>
</p>
<?php
		}
	}
	/*
	name : recent_comments
	description :Function for getting recent comments -- */
	function recent_review_comments($g_size = 30, $no_comments = 10, $comment_lenth = 60, $show_pass_post = false,$post_type = 'post',$title = '')
	{
		global $wpdb, $tablecomments, $tableposts,$rating_table_name;
		$tablecomments = $wpdb->comments;
		$tableposts    = $wpdb->posts;
		$args = array(
			'status' => 'approve',
			'karma' => '',
			'number' => $no_comments,
			'offset' => '',
			'orderby' => 'comment_date',
			'order' => 'DESC',
			'post_type' => $post_type,
		);
		if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
		{
			add_filter('comments_clauses','location_comments_clauses');
		}
		$comments = get_comments($args);
		
		if($comments)
		{
			if( $title <> "")
			{
				echo ' <h3 class="widget-title">'.$title.'</h3>';
			}
			echo '<ul class="recent_comments">';
			foreach($comments as $comment)
			{ 
				$comment_id           = $comment->comment_ID;
				$comment_content      = strip_tags($comment->comment_content);
				$comment_excerpt      = mb_substr($comment_content, 0, $comment_lenth)."";
				$permalink            = get_permalink($comment->ID)."#comment-".$comment->comment_ID;
				$comment_author_email = $comment->comment_author_email;
				$comment_post_ID      = $comment->comment_post_ID;
				$post_title           = stripslashes(get_the_title($comment_post_ID));
				$permalink            = get_permalink($comment_post_ID);
				echo "<li class='clearfix'><span class=\"li".$comment_id."\">";
				if(function_exists('get_avatar'))
				{
					if('' == @$comment->comment_type)
					{
						echo  '<a href="'.$permalink.'">';
						echo get_avatar($comment->comment_author_email, 60);
						echo '</a>';
					}
					elseif( ('trackback' == $comment->comment_type) || ('pingback' == $comment->comment_type) )
					{
						echo  '<a href="'.$permalink.'">';
						echo get_avatar($comment->comment_author_email, 60);
					}
				}
				elseif(function_exists('gravatar'))
				{
					echo  '<a href="'.$permalink.'">';
					echo "<img src=\"";
					if('' == $comment->comment_type)
					{
						echo get_avatar($comment->comment_author_email, 60);
						echo '</a>';
					}
					elseif( ('trackback' == $comment->comment_type) || ('pingback' == $comment->comment_type) )
					{
						echo  '<a href="'.$permalink.'">';
						echo get_avatar($comment->comment_author_email, 60);
						echo '</a>';
					}
					echo "\"  class=\"avatar\" />";
				}
				echo "</span>\n";
				echo '' ;
				echo  '<a href="'.$permalink.'" class="title">'.$post_title.'</a>';
				$tmpdata = get_option('templatic_settings');
				if($tmpdata['templatin_rating'] == 'yes'):
				$post_rating = $wpdb->get_var("select rating_rating from $rating_table_name where comment_id=\"$comment_id\"");
				echo "<div class='rating'>".draw_rating_star_plugin($post_rating)."</div>";
				endif;
				do_action('display_multi_rating',$comment_id);
				echo $comment_excerpt;
				if(function_exists('supreme_prefix')){
					$pref = supreme_prefix();
				}else{
					$pref = sanitize_key( apply_filters( 'hybrid_prefix', get_template() ) );
				}
				$theme_options = get_option($pref.'_theme_settings');
				if($theme_options['templatic_excerpt_link'])
				{
					$read_more = $theme_options['templatic_excerpt_link'];
				}
				else
				{
					$read_more = __('read more',THEME_DOMAIN);
				}
				echo "<a class=\"comment_excerpt\" href=\"" . $permalink . "\" title=\"View the entire comment\">";
				echo $read_more;
				echo "</a>";
				echo '</li>';
			}
			echo "</ul>";
		}
	}
}
add_action('display_multi_rating','display_multi_rating');
function display_multi_rating($comment_id){
	if(is_plugin_active('Templatic-MultiRating/multiple_rating.php') && function_exists('get_single_average_rating_image'))
	{
		?>
<div class="event_rating">
  <div class="event_rating_row"><span class="single_rating"> <?php echo get_single_average_rating_image($comment_id);?> </span></div>
</div>
<?php
	}
}
if(function_exists('is_plugin_active'))
{
	if(is_plugin_active('woocommerce/woocommerce.php'))
	{
		//WOO COOMERCE SHOPPING CART WIDGET FOR SECONDARY MENU.
		if(!class_exists('templatic_woo_shopping_cart_info'))
		{
			class templatic_woo_shopping_cart_info extends WP_Widget
			{
				var $woo_widget_cssclass;
				var $woo_widget_description;
				var $woo_widget_idbase;
				var $woo_widget_name;
				function templatic_woo_shopping_cart_info()
				{
					/* Widget variable settings. */
					$this->woo_widget_cssclass = 'woocommerce widget_shopping_cart';
					$this->woo_widget_description = __( "Display Cart Informations with automatic cart update. Best to use it in \"Header right\" sidebar", THEME_DOMAIN );
					$this->woo_widget_idbase = 'woocommerce_widget_cart';
					$this->woo_widget_name = __( 'T &rarr; WooCommerce Shopping Cart', THEME_DOMAIN );
					//Constructor
					$widget_ops = array('classname'  => 'widget WooCommerce shopping cart info','description'=>  apply_filters('supreme_woo_shop_cart_description',__('Displays Cart Information with automatic cart update. Most suitable widget area for it is "Header right area"',THEME_DOMAIN)) );
					$this->WP_Widget('templatic_woo_shopping_cart_info',$this->woo_widget_name, $widget_ops);
				}
				function widget($args, $instance)
				{
					// prints the widget
					global $woocommerce;
					extract($args, EXTR_SKIP);
					?>
<?php
					if($before_title == '' || $after_title == ''){
						$before_title == '<h3><span>';
						$after_title == '</span></h3>';
					}
					?>
<div class="widget templatic_shooping  widget_shopping_cart">
  <div  id="woo_shoppingcart_box" class="cart_items shoppingcart_box shoppingcart_box_bg" onclick="show_hide_cart_items();" style="cursor:pointer;">
    <?php
							if( empty( $title ) )
							{
								echo $before_title . "Shopping Cart" . $after_title;
							}
							else
							{
								echo $before_title . $title . $after_title;
							}; ?>
    <div id="wocommerce_button">
      <p class="total"> <strong>
        <?php _e( 'Subtotal', THEME_DOMAIN ); ?>
        : </strong> <?php echo $woocommerce->cart->get_cart_subtotal(); ?> </p>
      <p  class="buttons"> <a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" class="button">
        <?php _e( 'View Cart &rarr;', THEME_DOMAIN ); ?>
        </a> <a href="<?php echo $woocommerce->cart->get_checkout_url(); ?>" class="button checkout">
        <?php _e( 'Checkout &rarr;', THEME_DOMAIN ); ?>
        </a> </p>
    </div>
  </div>
  <div id="woo_shopping_cart" style="display:none">
    <div class="widget_shopping_cart_content">
      <?php
								echo '<ul class="cart_list product_list_widget ';
								if($hide_if_empty) echo 'hide_cart_widget_if_empty';
								echo '">';
								if( sizeof( $woocommerce->cart->get_cart() ) > 0 )
								{
									foreach( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item )
									{
										$_product = $cart_item['data'];
										if( $_product->exists() && $cart_item['quantity'] > 0 )
										{
											echo '<li><a href="'.get_permalink($cart_item['product_id']).'">';
											echo $_product->get_image(). '</a><a href="'.get_permalink($cart_item['product_id']).'">';
											echo apply_filters('woocommerce_cart_widget_product_title', $_product->get_title(), $_product)."</a>";
											$product_price = get_option('woocommerce_display_cart_prices_excluding_tax') == 'yes' || $woocommerce->customer->is_vat_exempt() ? $_product->get_price_excluding_tax() : $_product->get_price();
											$product_price = apply_filters('woocommerce_cart_item_price_html', woocommerce_price( $product_price ), $cart_item, $cart_item_key );
											echo '<span class="quantity">' . $cart_item['quantity'] . ' &times; ' . $product_price . '</span></li>';
										}
									}
								}
								echo '</ul>';
								echo '<div class="woo_checkout_btn"><p class=""><strong>' . __('Subtotal', THEME_DOMAIN) . ':</strong> '. $woocommerce->cart->get_cart_subtotal() . '</p>';
								do_action( 'woocommerce_widget_shopping_cart_before_buttons' );
								echo '<div class="buttons"><a href="' . $woocommerce->cart->get_cart_url() . '" class="button">' . __('Checkout &rarr;', THEME_DOMAIN) . '</a></div></div>';
								?>
    </div>
  </div>
  <script type="text/javascript">
							function show_hide_cart_items()
							{
								var dis = document.getElementById('woo_mob_shopping_cart').style.display;
								if(dis == 'none')
								{
									jQuery("#wocommerce_button").css('display','none');
									jQuery("#woo_mob_shopping_cart").animate(
										{
											height:'toggle'
										});
								}else
								{
									jQuery("#woo_mob_shopping_cart").animate(
										{
											height:'toggle'
										});
									jQuery("#wocommerce_button").css('display','block');
								}
							}
						</script>
</div>
<?php
				}
				function update($new_instance, $old_instance)
				{
					//save the widget
					$instance = $old_instance;
					return $instance;
				}
				function form($instance)
				{
					//widgetform in backend
					$instance = wp_parse_args( (array) $instance, array(''=> ' ' ) );
				}
			}
			register_widget('templatic_woo_shopping_cart_info');
		}
	}
}
?>