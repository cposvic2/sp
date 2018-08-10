<?php 
/*
 * Group Custom Post
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$uc_group_fields = array(
	'default_group',
	'teacher',
	'class',
	'students',
	'signup',
	'score',
	'teamwork_score',
);

// Register group custom post
function uc_register_post_group() {

	register_post_type( 'uc_group',
		array(
			'menu_icon' 	=> 'dashicons-groups',
			'supports' 		=> array( 'title', ),
			'show_ui' 		=> true,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'labels'        => array(
				'name'                	=> __( 'Groups', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name'       	=> __( 'Group', UPTOWNCODE_PLUGIN_NAME ),
				'add_new'             	=> __( 'Add New Group', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item'        	=> __( 'Add New Group', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name'           	=> __( 'Groups', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon'   	=> __( 'Parent Group', UPTOWNCODE_PLUGIN_NAME ),
				'all_items'           	=> __( 'All Groups', UPTOWNCODE_PLUGIN_NAME ),
				'view_item'           	=> __( 'View Group', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item'           	=> __( 'Edit Group', UPTOWNCODE_PLUGIN_NAME ),
				'update_item'         	=> __( 'Update Group', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'        	=> __( 'Search Group', UPTOWNCODE_PLUGIN_NAME ),
				'not_found' 			=> __(  'No Group found', UPTOWNCODE_PLUGIN_NAME ),
				'not_found_in_trash'	=> __(  'No Group found in Trash', UPTOWNCODE_PLUGIN_NAME ),
				'parent' 				=> __(  'Parent Group', UPTOWNCODE_PLUGIN_NAME ),
			)
		)
	);
}
add_action( 'init', 'uc_register_post_group' );
$uc_group_metabox = new VP_Metabox( plugin_dir_path( __FILE__ ) . 'group_metabox.php' );

// Modify columns for Groups in Admin
function uc_group_columns( $columns ) {
    unset( $columns['date'] );
    $columns['school_year'] = __( 'School Year', UPTOWNCODE_PLUGIN_NAME );
    $columns['class'] = __( 'Class', UPTOWNCODE_PLUGIN_NAME );
    $columns['activity'] = __( 'Activity', UPTOWNCODE_PLUGIN_NAME );
    $columns['signup'] = __( 'Signup', UPTOWNCODE_PLUGIN_NAME );
    return $columns;
}
add_filter( 'manage_uc_group_posts_columns', 'uc_group_columns' );
function uc_group_custom_column( $column, $post_id ) {
	switch ( $column ) {
		case 'school_year' :
			echo get_school_year_of_post( $post_id ); 
			break;
		case 'class' :
			$class_id = get_post_meta( $post_id, 'class', true );
			edit_post_link(get_the_title( $class_id ), '', '', $class_id );
			break;
		case 'activity' :
			$signup_id = get_post_meta( $post_id, 'signup', true );
			$activity_id = get_post_meta( $signup_id, 'activity', true );
			if ( isset( $activity_id) && $activity_id )
				edit_post_link(get_the_title( $activity_id ), '', '', $activity_id );
			elseif ( get_post_meta( $post_id, 'default_group', true ) )
				echo "Default";
			break;
		case 'signup' :
			$signup_id = get_post_meta( $post_id, 'signup', true );
			if ( isset( $signup_id) && $signup_id )
				edit_post_link( $signup_id, '', '', $signup_id );
			break;
    }
}
add_action( 'manage_uc_group_posts_custom_column' , 'uc_group_custom_column', 10, 2 );

// Returns list of groups for use in dropdowns
function get_uc_groups( $post_status = 'publish', $school_year = false ) {
	$args = array(
		'post_type' => 'uc_group',
		'post_status' => $post_status,
		'posts_per_page' => -1,
	);

	if ( $school_year ) {
		$school_year_array = get_school_year( $school_year );
		$args['date_query'] = array(
			array(
				'after' => $school_year_array['start'],
				'before' => $school_year_array['end'],
				'inclusive' => true,
			)
		);
	}

	$wp_posts = get_posts( $args );
	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns default groups by class
function get_default_uc_groups_by_class( $class_id = '', $post_status = 'publish', $school_year = false ) {
	$args = array(
		'post_type' => 'uc_group',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'default_group',
				'value'   => '1',
			),
			array(
				'key'     => 'class',
				'value'   => $class_id,
			),
		),
	);

	if ( $school_year ) {
		$school_year_array = get_school_year( $school_year );
		$args['date_query'] = array(
			array(
				'after' => $school_year_array['start'],
				'before' => $school_year_array['end'],
				'inclusive' => true,
			)
		);
	}
	
	$wp_posts = get_posts( $args );
	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns Group ID of student
function get_group_of_student( $student_id = '', $activity_id = '' ) {
	$class_id = get_post_meta( $student_id, 'class', true );

	$groups = get_uc_groups_by_activity( $class_id, $activity_id );

	if ( is_array($groups) ) {
		foreach ( $groups as $group ) {
			$students = get_post_meta( $group['value'], 'students', true );
			if ( in_array($student_id, $students) )
				return $group;
		}
	}
	return false;
}

// Returns groups by activity
function get_uc_groups_by_activity( $class_id = '', $activity_id = '', $grade = 'all', $post_status = 'publish', $school_year = false ) {

	$signup_id = get_signup( $class_id, $activity_id, $post_status );

	if ( $signup_id ) {
		$args = array(
			'post_type' => 'uc_group',
			'post_status' => $post_status,
			'posts_per_page' => -1,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => 'signup',
					'value'   => $signup_id,
				),
				array(
					'key'     => 'class',
					'value'   => $class_id,
				),
			),
		);

		if ( $school_year ) {
			$school_year_array = get_school_year( $school_year );
			$args['date_query'] = array(
				array(
					'after' => $school_year_array['start'],
					'before' => $school_year_array['end'],
					'inclusive' => true,
				)
			);
		}

		$wp_posts = get_posts( $args );
		$result = array();
		foreach ($wp_posts as $post) {
			$result[] = array('value' => $post->ID, 'label' => $post->post_title);
		}
		return $result;
	} else
		return false;
}

// Returns groups by school
function get_uc_groups_by_school( $school_id = '', $activity_id = '', $check_ell = false, $check_at_risk = false, $grade = 'all', $post_status = 'publish', $school_year = false ) {

	$classes = get_uc_classes_by_school( $school_id, $check_ell, $check_at_risk, $grade, $post_status ) ;

	if ( count($classes) ) {

		$args = array(
			'post_type' => 'uc_group',
			'post_status' => $post_status,
			'posts_per_page' => -1,
			'meta_query' => array(
				'relation' => 'OR',
			),
		);

		if ( $school_year ) {
			$school_year_array = get_school_year( $school_year );
			$args['date_query'] = array(
				array(
					'after' => $school_year_array['start'],
					'before' => $school_year_array['end'],
					'inclusive' => true,
				)
			);
		}

		foreach ( $classes as $class ) {
			$args['meta_query'][] = array(
					'key'     => 'class',
					'value'   => $class['value'],
			);
		}

		$wp_posts = get_posts( $args );

		$result = array();
		foreach ($wp_posts as $post) {
			$signup_id = get_post_meta( $post->ID, 'signup', true );
			$group_activity_id = get_post_meta( $signup_id, 'activity', true );
			if ( $group_activity_id == $activity_id )
				$result[] = array('value' => $post->ID, 'label' => $post->post_title);
		}
		return $result;
	} else
		return false;
}

// Returns groups by geography
function get_uc_groups_by_geography( $geography_array, $post_status = 'publish', $school_year = false ) {
	$teachers = get_uc_teachers_by_geography( $geography_array, $post_status );

	$args = array(
		'post_type' => 'uc_group',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			'relation' => 'OR'
		),
	);

	if ( $school_year ) {
		$school_year_array = get_school_year( $school_year );
		$args['date_query'] = array(
			array(
				'after' => $school_year_array['start'],
				'before' => $school_year_array['end'],
				'inclusive' => true,
			)
		);
	}

	foreach ( $teachers as $teacher )
		$args['meta_query'][] = array( 'key' => 'teacher', 'value'   => $teacher['value'] );

	$result = array();
	$wp_posts = get_posts($args);
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns groups by geography
function get_uc_groups_by_school_geography( $geographic_type = '', $school_id = '', $activity_id = false, $check_ell = false, $check_at_risk = false, $grade = 'all', $post_status = 'publish', $school_year = false ) {

	$classes_to_search = get_uc_classes_by_school_geography( $geographic_type, $school_id, $check_ell, $check_at_risk, $grade, $post_status );

	$args = array(
		'post_type' => 'uc_group',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			'relation' => 'AND'
		),
	);

	$class_args = array(
		'key' => 'class',
		'compare' => 'IN',
		'value' => array(),
	);

	if ( $school_year ) {
		$school_year_array = get_school_year( $school_year );
		$args['date_query'] = array(
			array(
				'after' => $school_year_array['start'],
				'before' => $school_year_array['end'],
				'inclusive' => true,
			)
		);
	}

	foreach ( $classes_to_search as $class_to_search ) {
		$class_args['value'][] = $class_to_search['value'];
	}
	$args['meta_query'][] = $class_args;
	if ( $activity_id ) {
		$args['meta_query'][] = array(
			'relation' => 'OR',
			array(
				'key'     => 'default_group',
				'value'   => '0',
			),
			array(
				'key'     => 'default_group',
				'compare'   => 'NOT EXISTS',
			),
		);
	}
	$wp_posts = get_posts( $args );

	$result = array();
	foreach ($wp_posts as $post) {
		if ( $activity_id ) {
			$signup = get_post_meta( $post->ID, 'signup', true );
			$signup_activity = get_post_meta( $signup, 'activity', true );
			if ( $signup_activity == $activity_id ) {
				$result[] = array('value' => $post->ID, 'label' => $post->post_title);
			}
		} else {
			$result[] = array('value' => $post->ID, 'label' => $post->post_title);
		}
	}
	return $result;
}

// Updates groups
function uc_update_groups( $grouparray = false, $teacher_id = '', $class_id = '', $default_groups = 1, $activity_id = '', $signup_id = false, $post_status = 'publish', $school_year = false ) {

	if ( ( !$default_groups && $activity_id && $signup_id && $teacher_id && $class_id && $grouparray ) || ( $default_groups && $teacher_id && $class_id && $grouparray ) ) {
		
		// Remove existing groups
		if ( $default_groups ) {
			$old_groups = get_default_uc_groups_by_class( $class_id, $post_status, $school_year );
		} else {
			$old_groups = get_uc_groups_by_activity( $class_id, $activity_id, $post_status, $school_year );
		}	

		if ( $old_groups ) {
			foreach ( $old_groups as $old_group ) {
				wp_delete_post( $old_group['value'], true );
			}
		}

		global $uc_group_fields;
		$groupnum = 0;

		$newgroups = array();

		foreach ( $grouparray as $group ) {
			$groupnum++;

			$new_group = array (
				'post_title' => name_group( $default_groups, $groupnum, $class_id, $activity_id ),
				'post_type' => 'uc_group',
				'post_author' => $user_id,
				'post_status' => $post_status
			);

			$group_id = wp_insert_post( $new_group );
			update_post_meta( $group_id, 'teacher', $teacher_id );
			update_post_meta( $group_id, 'default_group', $default_groups );
			update_post_meta( $group_id, 'students', $group );
			update_post_meta( $group_id, 'uc_group_fields', $uc_group_fields );
			update_post_meta( $group_id, 'class', $class_id );
			if ( $signup_id )
				update_post_meta( $group_id, 'signup', $signup_id );

			$newgroups[] = $group_id;
		}
		return $newgroups;
	}
	return false;
}

// Get group student names
function get_group_student_names( $group_id ) {
	$uc_students = get_post_meta( $group_id, 'students', true );

	$i = 0;
	$student_names = '';
	while ( $i < count($uc_students) ) {
		$student_names .= get_student_name( $uc_students[$i] );
		$i++;
		if ( $i < count($uc_students) )
			$student_names .=  ', ';
	}
	return $student_names;
}

// Checks if group is ELL
function is_group_ell( $group_id ) {
	$class_id = get_post_meta( $group_id, 'class', true );

	return is_class_ell( $school_id );
}

// Checks if group is at-risk
function is_group_at_risk( $class_id ) {
	$class_id = get_post_meta( $group_id, 'class', true );

	return is_class_at_risk( $school_id );
}


 ?>