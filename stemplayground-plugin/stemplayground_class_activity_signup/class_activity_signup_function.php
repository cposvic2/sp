<?php 
/*
 * Class Activity Signup Custom Post
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$uc_signup_fields = array(
	'class',
	'activity',
	'signup_date',
	'completed',
);

// Register class activity signup custom post
function uc_register_post_signup() {

	register_post_type( 'uc_signup',
		array(
			'menu_icon' 	=> 'dashicons-update',
			'supports' 		=> array( 'title', ),
			'show_ui' 		=> true,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'labels'        => array(
				'name'                	=> __( 'Signups', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name'       	=> __( 'Signup', UPTOWNCODE_PLUGIN_NAME ),
				'add_new'             	=> __( 'Add New Signup', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item'        	=> __( 'Add New Signup', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name'           	=> __( 'Signups', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon'   	=> __( 'Parent Signup', UPTOWNCODE_PLUGIN_NAME ),
				'all_items'           	=> __( 'All Signups', UPTOWNCODE_PLUGIN_NAME ),
				'view_item'           	=> __( 'View Signup', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item'           	=> __( 'Edit Signup', UPTOWNCODE_PLUGIN_NAME ),
				'update_item'         	=> __( 'Update Signup', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'        	=> __( 'Search Signup', UPTOWNCODE_PLUGIN_NAME ),
				'not_found' 			=> __(  'No Signup found', UPTOWNCODE_PLUGIN_NAME ),
				'not_found_in_trash'	=> __(  'No Signup found in Trash', UPTOWNCODE_PLUGIN_NAME ),
				'parent' 				=> __(  'Parent Signup', UPTOWNCODE_PLUGIN_NAME ),
			)
		)
	);
}
add_action( 'init', 'uc_register_post_signup' );
$uc_class_activity_signup = new VP_Metabox( plugin_dir_path( __FILE__ ) . 'class_activity_signup_metabox.php' );

// Modify columns for Signups in Admin
function uc_signup_columns( $columns ) {
	unset( $columns['date'] );
	$columns['school_year'] = __( 'School Year', UPTOWNCODE_PLUGIN_NAME );
	$columns['class'] = __( 'Class', UPTOWNCODE_PLUGIN_NAME );
	$columns['activity'] = __( 'Activity', UPTOWNCODE_PLUGIN_NAME );
	$columns['status'] = __( 'Status', UPTOWNCODE_PLUGIN_NAME );
	return $columns;
}
add_filter( 'manage_uc_signup_posts_columns', 'uc_signup_columns' );
function uc_signup_custom_column( $column, $post_id ) {
	switch ( $column ) {
		case 'school_year' :
			echo get_school_year_of_post( $post_id ); 
			break;
		case 'class' :
			$class_id = get_post_meta( $post_id, 'class', true );
			edit_post_link(get_the_title( $class_id ), '', '', $class_id );
			break;
		case 'activity' :
			$activity_id = get_post_meta( $post_id, 'activity', true );
			edit_post_link(get_the_title( $activity_id ), '', '', $activity_id );
			break;
		case 'status' :
			echo signup_status_of_signup( $post_id );
			break;
	}
}
add_action( 'manage_uc_signup_posts_custom_column' , 'uc_signup_custom_column', 10, 2 );

//Display metabox list of groups
function uc_display_signup_metabox_b() {
	$post_id = intval( $_GET['post'] );
	$class_id = get_post_meta( $post_id, 'class', 'true' );
	$activity_id = get_post_meta( $post_id, 'activity', 'true' );

	$list_items = get_uc_groups_by_activity( $class_id, $activity_id, array('publish', 'private') );
	echo '
	<table class="widefat striped">
		<thead>
			<tr>
				<th>Group ID</th>
				<th>Students</th>
				<th>Score</th>
				<th>Teamwork Score</th>
				<th>Final Score</th>
			</tr>
		</thead>
		<tbody>';

	foreach ( $list_items as $list_item ) {
		$students = get_post_meta( $list_item['value'], 'students', true );
		$group_score = get_post_meta( $list_item['value'], 'score', true );
		$teamwork_score = get_post_meta( $list_item['value'], 'teamwork_score', true );
		$final_score = calculate_final_score( $group_score, $teamwork_score, $activity_id );

		echo '<tr><th>';
		edit_post_link($list_item['value'], '', '', $list_item['value'] );
		echo '</th><th>';
		$i = 0;
		while ( $i < count($students) ) {
			edit_post_link( get_student_name( $students[$i] ), '', '', $students[$i] );
			$i++;
			if ( $i != count($students) )
				echo ', ';
		}
		echo '</th>
				<th>'.$group_score.'</th>
				<th>'.$teamwork_score.'</th>
				<th>'.$final_score.'</th>
			</tr>';
	}
	echo '
		</tbody>
	</table>
	';
}
function uc_register_signup_metaboxes() {
	add_meta_box( 'uc_signup_metabox_b', __( 'List of Groups', UPTOWNCODE_PLUGIN_NAME ), 'uc_display_signup_metabox_b', 'uc_signup' );
}
add_action( 'add_meta_boxes', 'uc_register_signup_metaboxes' );

// Returns list of signups
function get_uc_signups( $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_signup',
		'post_status' => $post_status,
		'posts_per_page' => -1,
	));

	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of signups for a class
function get_uc_signups_by_class( $class_id = '', $post_status = 'publish', $query_signup_status = false ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_signup',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key'     => 'class',
				'value'   => $class_id,
			),
		),
	));
	
	$result = array();
	foreach ($wp_posts as $post) {
		if ( $query_signup_status ) {
			if ( signup_status( $class_id, get_post_meta( $post->ID, 'activity', true ) ) == $query_signup_status ) {
				$result[] = array('value' => $post->ID, 'label' => $post->post_title);
			}
		} else {
			$result[] = array('value' => $post->ID, 'label' => $post->post_title);
		}
	}
	return $result;
}

// Returns list of signups for an activity
function get_uc_signups_by_activity( $activity_id = '', $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_signup',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key'     => 'activity',
				'value'   => $activity_id,
			),
		),
	));
	
	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns signup post of class and activity, if any
function get_signup( $class_id = '', $activity_id = '', $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_signup',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'activity',
				'value'   => $activity_id,
			),
			array(
				'key'     => 'class',
				'value'   => $class_id,
			),
		),
	));
	if ( count( $wp_posts ) <> 0 ) {
		$signup = $wp_posts[0];
		return $signup->ID;
	} else 
		return false;
}

// Returns activity signup status of a class
function signup_status( $class_id = '', $activity_id = '', $post_status = 'publish' ) {
	$signup_id = get_signup( $class_id, $activity_id, $post_status );
	
	return signup_status_of_signup( $signup_id );
}

// Returns activity signup status of a class
function signup_status_of_signup( $signup_id = '' ) {
	if ( $signup_id && intval($signup_id) ) {
		$completed = get_post_meta( $signup_id, 'completed', true );

		if ( $completed ) {
			return 'completed';
		}

		$expiration_date = get_signup_expiration_date( $signup_id );
		if ( $expiration_date ) {
			$current_date = DateTime::createFromFormat( 'm-d-Y', current_time( 'm-d-Y') );	
			if ( $current_date > $expiration_date ) {
				return 'expired';
			} else {
				return 'active';
			}
		}
	}

	return false;
}

function get_signup_expiration_date( $signup_id ) {
	$signup_date_meta = get_post_meta( $signup_id, 'signup_date', true );
	if ( $signup_date_meta ) {
		$signup_date = DateTime::createFromFormat( 'm-d-Y', $signup_date_meta );
		$expiration_length = new DateInterval('P'.vp_option('uc_option.activity_expiration_length').'D');
		$expiration_date = clone $signup_date;
		$expiration_date->add( $expiration_length );
		return $expiration_date;
	}
	return false;
}

// Determines if class has reached maxiumum signups
function is_class_at_maximum_signups( $class_id = '' ) {
	$signup_maximum = vp_option('uc_option.signup_maximum');
	$active_activities = get_active_uc_activities( $class_id );
	if ( count( $active_activities ) >= $signup_maximum )
		return true;
	else
		return false;
}

// Signs up for activity
function sign_up_for_activity( $class_id = '', $activity_id = '', $post_status = 'publish' ) {
	if ( intval( $class_id ) && intval( $activity_id ) ) {
		$user_id = get_current_user_id();
		$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
		$class_name = get_the_title( $class_id );
		$activity_name = get_the_title( $activity_id );

		global $uc_signup_fields;

		$new_signup = array (
			'post_title' => $class_name . ' signup for ' . $activity_name,
			'post_type' => 'uc_signup',
			'post_author' => $user_id,
			'post_status' => $post_status,
		);
		$signup_id = wp_insert_post( $new_signup );
		update_post_meta( $signup_id, 'uc_signup_fields', $uc_signup_fields );
		update_post_meta( $signup_id, 'class', $class_id );
		update_post_meta( $signup_id, 'activity', $activity_id );
		update_post_meta( $signup_id, 'signup_date', current_time( 'm-d-Y' ) );

		$seed_groups = get_default_uc_groups_by_class( $class_id );
		$grouparray = array();

		foreach ( $seed_groups as $seed_group ) {
			$grouparray[] = get_post_meta( $seed_group['value'], 'students', true );
		}

		if ( uc_update_groups( $grouparray, $teacher_id, $class_id, 0, $activity_id, $signup_id ) ) {
			uc_email_enroll_receipt( $signup_id );
			// Add email reminders
			uc_should_schedule_email_expire( $signup_id );
			// In case there were email reminders for this class, remove them
			uc_should_schedule_email_no_activity( $class_id, false );
			return true;
		} 
	} 
	return false;
}