<?php

// Creating League Challenge widget 
class uc_lc_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'uc_lc_widget', 
			__('STEM League Challenge Link', UPTOWNCODE_PLUGIN_NAME ), 
			array( 'description' => __( 'Displays a link to the League Challenge Results (if available)', UPTOWNCODE_PLUGIN_NAME ), ) 
		);
	}

	public function widget( $args, $instance ) {
		global $active_class;
		if ( can_display_awards( $active_class ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
			// before and after widget arguments are defined by themes
			echo $args['before_widget'];
			if ( ! empty( $title ) )
				echo $args['before_title'] . $title . $args['after_title'];

			$pages = get_pages( array(
				'meta_key' => '_wp_page_template',
				'meta_value' => 'page-league-challenge.php'
			));
			if ( $pages )
				$ch_view_results_url = get_permalink( $pages[0] );
			else
				$ch_view_results_url = home_url();

			echo '<div><a href="'. $ch_view_results_url .'" >'. $instance['link_text'] .'</a></div>';
		}

		echo $args['after_widget'];
	}
			
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) )
			$title = $instance[ 'title' ];
		else
			$title = __( 'League Challenge Results', UPTOWNCODE_PLUGIN_NAME );

		if ( isset( $instance[ 'link_text' ] ) )
			$link_text = $instance[ 'link_text' ];
		else
			$link_text = __( 'View your League Challenge results', UPTOWNCODE_PLUGIN_NAME );
		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link_text' ); ?>"><?php _e( 'Link text:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>" type="text" value="<?php echo esc_attr( $link_text ); ?>" />
		</p>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = !empty($new_instance['title']) ? strip_tags( $new_instance['title'] ) : '';
		$instance['link_text'] = !empty($new_instance['link_text']) ? strip_tags( $new_instance['link_text'] ) : '';
		return $instance;
	}
}

// Creating Activity Results widget 
class uc_activity_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'uc_activity_widget', 
			__('STEM Activity Results', UPTOWNCODE_PLUGIN_NAME ), 
			array( 'description' => __( 'Displays links to individual Activity results', UPTOWNCODE_PLUGIN_NAME ), ) 
		);
	}

	public function widget( $args, $instance ) {
		global $active_class;
		$activities = get_completed_uc_activities( $active_class );

		if ( count($activities) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
			// before and after widget arguments are defined by themes
			echo $args['before_widget'];
			if ( ! empty( $title ) )
				echo $args['before_title'] . $title . $args['after_title'];

			echo '<ul>';
			foreach( $activities as $activity ) {
				echo '<li><a href="'.esc_url( get_permalink( $activity['value'] ) ).'">'.$activity['label'].'</a></li>';
			}
			echo '</ul>';
			echo $args['after_widget'];
		}
	}
			
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) )
			$title = $instance[ 'title' ];
		else
			$title = __( 'Activity Results', UPTOWNCODE_PLUGIN_NAME );

		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = !empty($new_instance['title']) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}

// Creating My Info widget 
class uc_myinfo_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'uc_myinfo_widget', 
			__('STEM My Info', UPTOWNCODE_PLUGIN_NAME ), 
			array( 'description' => __( 'Displays links to Teacher pages', UPTOWNCODE_PLUGIN_NAME ), ) 
		);
	}

	public function widget( $args, $instance ) {
		global $active_class;
		$my_links = array(
			array(
				'value' => 'my_school',
				'template' => 'page-school.php',
				'cap' => 'edit_school',
				'show_link' => true
			),
			array(
				'value' => 'my_class',
				'template' => 'page-classes.php',
				'cap' => 'edit_class',
				'show_link' => false
			),
			array(
				'value' => 'my_teams',
				'template' => 'page-groups.php',
				'cap' => 'edit_groups',
				'show_link' => false
			),
			array(
				'value' => 'my_students',
				'template' => 'page-student.php',
				'cap' => 'edit_students',
				'show_link' => false
			),
		);

		if ( is_user_logged_in() ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
			// before and after widget arguments are defined by themes
			echo $args['before_widget'];
			if ( ! empty( $title ) )
				echo $args['before_title'] . $title . $args['after_title'];			

			foreach($my_links as $my_link) {
				if ( current_user_can($my_link['cap']) ) {
					$pages = get_pages( array(
						'meta_key' => '_wp_page_template',
						'meta_value' => $my_link['template']
					));
					if ( $pages )
						$my_link_url = get_permalink( $pages[0] );
					else
						$my_link_url = home_url();

					if ( $my_link['show_link'] )
						echo '<li><a href="'.esc_url( $my_link_url ).'">'.$instance[$my_link['value']].'</a> (<a href="mailto:'.$instance['link_email'].'">'.$instance['link_text'].'</a>)</li>';
					else
						echo '<li><a href="'.esc_url( $my_link_url ).'">'.$instance[$my_link['value']].'</a></li>';
				}
			}
			echo '</ul>';
		}

		echo $args['after_widget'];
	}
			
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) )
			$title = $instance[ 'title' ];
		else
			$title = __( 'My Info', UPTOWNCODE_PLUGIN_NAME );

		if ( isset( $instance[ 'link_email' ] ) )
			$link_email = $instance[ 'link_email' ];
		else
			$link_email = __( 'placeholder@stemplayground.org', UPTOWNCODE_PLUGIN_NAME );

		if ( isset( $instance[ 'link_text' ] ) )
			$link_text = $instance[ 'link_text' ];
		else
			$link_text = __( 'Email to change school', UPTOWNCODE_PLUGIN_NAME );

		if ( isset( $instance[ 'my_school' ] ) )
			$my_school = $instance[ 'my_school' ];
		else
			$my_school = __( 'My School', UPTOWNCODE_PLUGIN_NAME );
		if ( isset( $instance[ 'my_class' ] ) )
			$my_class = $instance[ 'my_class' ];
		else
			$my_class = __( 'My Class', UPTOWNCODE_PLUGIN_NAME );
		if ( isset( $instance[ 'my_teams' ] ) )
			$my_teams = $instance[ 'my_teams' ];
		else
			$my_teams = __( 'My Teams', UPTOWNCODE_PLUGIN_NAME );
		if ( isset( $instance[ 'my_students' ] ) )
			$my_students = $instance[ 'my_students' ];
		else
			$my_students = __( 'My Students', UPTOWNCODE_PLUGIN_NAME );

		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link_email' ); ?>"><?php _e( 'Email:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link_email' ); ?>" name="<?php echo $this->get_field_name( 'link_email' ); ?>" type="email" value="<?php echo esc_attr( $link_email ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link_text' ); ?>"><?php _e( 'Link text:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>" type="text" value="<?php echo esc_attr( $link_text ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_school' ); ?>"><?php _e( 'My School text:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'my_school' ); ?>" name="<?php echo $this->get_field_name( 'my_school' ); ?>" type="text" value="<?php echo esc_attr( $my_school ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_class' ); ?>"><?php _e( 'My Class text:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'my_class' ); ?>" name="<?php echo $this->get_field_name( 'my_class' ); ?>" type="text" value="<?php echo esc_attr( $my_class ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_teams' ); ?>"><?php _e( 'My Teams text:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'my_teams' ); ?>" name="<?php echo $this->get_field_name( 'my_teams' ); ?>" type="text" value="<?php echo esc_attr( $my_teams ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_students' ); ?>"><?php _e( 'My Students text:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'my_students' ); ?>" name="<?php echo $this->get_field_name( 'my_students' ); ?>" type="text" value="<?php echo esc_attr( $my_students ); ?>" />
		</p>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = !empty($new_instance['title']) ? strip_tags( $new_instance['title'] ) : '';
		$instance['link_email'] = !empty($new_instance['link_email']) ? strip_tags( $new_instance['link_email'] ) : '';
		$instance['link_text'] = !empty($new_instance['link_text']) ? strip_tags( $new_instance['link_text'] ) : '';
		$instance['my_school'] = !empty($new_instance['my_school']) ? strip_tags( $new_instance['my_school'] ) : '';
		$instance['my_class'] = !empty($new_instance['my_class']) ? strip_tags( $new_instance['my_class'] ) : '';
		$instance['my_teams'] = !empty($new_instance['my_teams']) ? strip_tags( $new_instance['my_teams'] ) : '';
		$instance['my_students'] = !empty($new_instance['my_students']) ? strip_tags( $new_instance['my_students'] ) : '';
		return $instance;
	}
}


function uc_load_widgets() {
	register_widget( 'uc_lc_widget' );
	register_widget( 'uc_activity_widget' );
	register_widget( 'uc_myinfo_widget' );
}
add_action( 'widgets_init', 'uc_load_widgets' );