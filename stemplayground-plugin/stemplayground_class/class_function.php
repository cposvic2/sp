<?php 
/*
 * Class Custom Post
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$uc_class_fields = array(
	'teacher',
	'grade',
);

// Register class custom post
function uc_register_post_class() {

	register_post_type( 'uc_class',
		array(
			'menu_icon' 	=> 'dashicons-welcome-learn-more',
			'supports' 		=> array( 'title', ),
			'show_ui' 		=> true,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'labels'        => array(
				'name'                	=> __( 'Classes', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name'       	=> __( 'Class', UPTOWNCODE_PLUGIN_NAME ),
				'add_new'             	=> __( 'Add New Class', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item'        	=> __( 'Add New Class', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name'           	=> __( 'Classes', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon'   	=> __( 'Parent Class', UPTOWNCODE_PLUGIN_NAME ),
				'all_items'           	=> __( 'All Classes', UPTOWNCODE_PLUGIN_NAME ),
				'view_item'           	=> __( 'View Class', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item'           	=> __( 'Edit Class', UPTOWNCODE_PLUGIN_NAME ),
				'update_item'         	=> __( 'Update Class', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'        	=> __( 'Search Class', UPTOWNCODE_PLUGIN_NAME ),
				'not_found' 			=> __(  'No Class found', UPTOWNCODE_PLUGIN_NAME ),
				'not_found_in_trash'	=> __(  'No Class found in Trash', UPTOWNCODE_PLUGIN_NAME ),
				'parent' 				=> __(  'Parent Class', UPTOWNCODE_PLUGIN_NAME ),
			)
		)
	);
}
add_action( 'init', 'uc_register_post_class' );
$uc_class_metabox = new VP_Metabox( plugin_dir_path( __FILE__ ) . 'class_metabox.php' );

//Display metabox list of students
function uc_display_class_metabox_b() {
	global $uc_genders, $uc_yesno;
	$post_id = $_GET['post'];
	$list_items = get_uc_students_by_class( $post_id, array('publish', 'private') );
	echo '
	<table class="widefat striped">
		<thead>
			<tr>
				<th>Name</th>
				<th>Gender</th>
				<th>ESL</th>
			</tr>
		</thead>
		<tbody>';

	foreach ( $list_items as $list_item ) {
		$gender = get_post_meta( $list_item['value'], 'gender', true );
		$esl = get_post_meta( $list_item['value'], 'ell', true );
		echo '<tr><th>';
		edit_post_link($list_item['label'], '', '', $list_item['value'] );
		echo '</th>
				<th>'.$uc_genders[$gender].'</th>
				<th>'.$uc_yesno[$esl].'</th>
			</tr>';
	}
	echo '
		</tbody>
	</table>
	';
}

//Display metabox list of signups
function uc_display_class_metabox_c() {
	$post_id = $_GET['post'];
	$list_items = get_uc_signups_by_class( $post_id, array('publish', 'private') );
	echo '
	<table class="widefat striped">
		<thead>
			<tr>
				<th>Signup ID</th>
				<th>Activity</th>
				<th>School</th>
				<th>Status</th>
				<th>Average Score</th>
			</tr>
		</thead>
		<tbody>';

	foreach ( $list_items as $list_item ) {
		$activity_id = get_post_meta( $list_item['value'], 'activity', true );
		$teacher_id = get_post_meta( get_the_id(), 'teacher', true );
		$school_id = get_post_meta( $teacher_id, 'school', true );
		$signup_status = signup_status( get_the_id(), $activity_id );
		$average = get_class_score_average( get_the_id(), $activity_id );
		echo '<tr><th>';
		edit_post_link($list_item['value'], '', '', $list_item['value'] );
		echo '</th><th>';
		edit_post_link(get_the_title( $activity_id ), '', '', $activity_id );
		echo '</th><th>';
		edit_post_link(get_the_title( $school_id ), '', '', $school_id );
		echo '</th>
				<th>'.$signup_status.'</th>
				<th>'.$average.'</th>
			</tr>';
	}
	echo '
		</tbody>
	</table>
	';
}

function uc_register_class_metaboxes() {
    add_meta_box( 'uc_class_metabox_b', __( 'List of Students', UPTOWNCODE_PLUGIN_NAME ), 'uc_display_class_metabox_b', 'uc_class' );
    add_meta_box( 'uc_class_metabox_c', __( 'List of Signups', UPTOWNCODE_PLUGIN_NAME ), 'uc_display_class_metabox_c', 'uc_class' );

}
add_action( 'add_meta_boxes', 'uc_register_class_metaboxes' );

// Modify columns for Classes in Admin
function uc_class_columns( $columns ) {
    unset( $columns['date'] );
    $columns['school_year'] = __( 'School Year', UPTOWNCODE_PLUGIN_NAME );
    $columns['grade'] = __( 'Grade', UPTOWNCODE_PLUGIN_NAME );
    $columns['school'] = __( 'School', UPTOWNCODE_PLUGIN_NAME );
    $columns['teacher'] = __( 'Teacher', UPTOWNCODE_PLUGIN_NAME );
    $columns['students'] = __( 'Students', UPTOWNCODE_PLUGIN_NAME );
    return $columns;
}
add_filter( 'manage_uc_class_posts_columns', 'uc_class_columns' );
function uc_class_custom_column( $column, $post_id ) {
	switch ( $column ) {
		case 'school_year' :
			echo get_school_year_of_post( $post_id ); 
			break;
		case 'grade' :
			$class_grade = get_post_meta( $post_id, 'grade', true );
			$class_grade_title = ( get_term_by('slug', $class_grade, 'uc_grade') ? get_term_by('slug', $class_grade, 'uc_grade')->name : 'Various' );
			echo $class_grade_title; 
			break;
		case 'school' :
			$teacher_id = get_post_meta( $post_id, 'teacher', true );
			$school_id = get_post_meta( $teacher_id, 'school', true );
			edit_post_link(get_the_title( $school_id ), '', '', $school_id );
			break;
		case 'teacher' :
			$teacher_id = get_post_meta( $post_id, 'teacher', true );
			edit_post_link(get_the_title( $teacher_id ), '', '', $teacher_id );
			break;
		case 'students' :
			$value = get_uc_students_by_class( $post_id );
			if ( isset( $value) && $value )
				echo count( $value ); 
			break;
    }
}
add_action( 'manage_uc_class_posts_custom_column' , 'uc_class_custom_column', 10, 2 );

// Returns list of classes
function get_uc_classes( $post_status = 'publish', $school_year = false ) {
	$args = array(
		'post_type' => 'uc_class',
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
	foreach ($wp_posts as $post)
	{
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of classes by grade
function get_uc_classes_by_grade( $grade = 'all', $check_ell = false, $check_at_risk = false, $post_status = 'publish', $school_year = false ) {
	$args = array(
		'post_type' => 'uc_class',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(),
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

	if ( $grade == '' )
		$args['meta_query'][] = array( 'relation' => 'OR', array( 'key' => 'grade', 'compare' => 'NOT EXISTS', 'value' => '' ), array( 'key' => 'grade', 'value' => '' ) );
	elseif ( $grade != 'all' )
		$args['meta_query'][] = array( 'key' => 'grade', 'value' => $grade );

	$wp_posts = get_posts( $args );
	
	$result = array();
	foreach ($wp_posts as $post) {
		if ( !( $check_ell && !is_class_ell( $post->ID ) ) && !( $check_at_risk && !is_class_at_risk( $post->ID ) ) )
			$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of classes by school
function get_uc_classes_by_school( $school_id = '', $check_ell = false, $check_at_risk = false, $grade = 'all', $post_status = 'publish', $school_year = false ) {
	$result = array();

	$args = array(
		'post_type' => 'uc_class',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'relation' => 'OR',
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

	$teachers = get_uc_teachers_by_school( $school_id, $post_status );
	if ( count($teachers) ) {
		foreach ( $teachers as $teacher ) {
			$args['meta_query'][0][] = array(
					'key'     => 'teacher',
					'value'   => $teacher['value'],
			);
		}

		if ( $grade == '' )
			$args['meta_query'][] = array( 'relation' => 'OR', array( 'key' => 'grade', 'compare' => 'NOT EXISTS', 'value' => '' ), array( 'key' => 'grade', 'value' => '' ) );
		elseif ( $grade != 'all' )
			$args['meta_query'][] = array( 'key' => 'grade', 'value' => $grade );

		$wp_posts = get_posts( $args );
		
		
		foreach ($wp_posts as $post) {
			if ( !( $check_ell && !is_class_ell( $post->ID ) ) && !( $check_at_risk && !is_class_at_risk( $post->ID ) ) )
				$result[] = array('value' => $post->ID, 'label' => $post->post_title);
		}	
	}

	return $result;
}
VP_Security::instance()->whitelist_function('get_uc_classes_by_school');

// Returns list of classes by teacher
function get_uc_classes_by_teacher( $teacher_id = '', $post_status = 'publish', $school_year = false ) {

	$args = array(
		'post_type' => 'uc_class',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key'     => 'teacher',
				'value'   => $teacher_id,
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

	$wp_posts = get_posts($args);
	
	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}
VP_Security::instance()->whitelist_function('get_uc_classes_by_teacher');

// Returns list of classes by school's geography
function get_uc_classes_by_school_geography( $geographic_type = '', $school_id = '', $check_ell = false, $check_at_risk = false, $grade = 'all', $post_status = 'publish', $school_year = false ) {
	$args = array(
		'post_type' => 'uc_class',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(),
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

	if ( $grade == '' )
		$args['meta_query'][] = array( 'relation' => 'OR', array( 'key' => 'grade', 'compare' => 'NOT EXISTS', 'value' => '' ), array( 'key' => 'grade', 'value' => '' ) );
	elseif ( $grade != 'all' )
		$args['meta_query'][] = array( 'key' => 'grade', 'value' => $grade );

	$wp_posts = get_posts( $args );
	
	$result = array();
	foreach ($wp_posts as $post) {
		$class_teacher_id = get_post_meta( $post->ID, 'teacher', true );
		$class_school_id = get_post_meta( $class_teacher_id, 'school', true );

		if ( schools_share_same_geographic_type( $class_school_id, $school_id, $geographic_type ) && !( $check_ell && !is_class_ell( $post->ID ) ) && !( $check_at_risk && !is_class_at_risk( $post->ID ) ) ) {
			$result[] = array('value' => $post->ID, 'label' => $post->post_title);
		}
	}
	return $result;
}

// Returns list of classes by geography
function get_uc_classes_by_geography( $geography_array, $post_status = 'publish', $school_year = false ) {
	$teachers = get_uc_teachers_by_geography( $geography_array, $post_status );

	$args = array(
		'post_type' => 'uc_class',
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

	foreach ( $teachers as $teacher )
		$args['meta_query'][] = array( 'key' => 'teacher', 'value'   => $teacher['value'] );

	$result = array();
	$wp_posts = get_posts($args);
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Checks if class is ELL
function is_class_ell( $class_id ) {
	$ell_threshold = vp_option('uc_option.ell_threshold');
	$ell_updated = get_post_meta($class_id, 'ell_updated', true );

	if ( $ell_updated ) {
		$class_ell_percentage = get_post_meta($class_id, 'class_ell_percentage', true );
	} else {
		$students = get_uc_students_by_class( $class_id );
		$ell_student_count = 0;
		foreach ( $students as $student ) {
			if ( get_post_meta( $student['value'], 'ell', true ) == 1 )
				$ell_student_count++;
		}
		if ( count($students) )
			$class_ell_percentage = $ell_student_count / count( $students ) * 100;
		else
			$class_ell_percentage = 0;

		update_post_meta( $class_id, 'ell_updated', 1 );
		update_post_meta( $class_id, 'class_ell_percentage', $class_ell_percentage );
	}

	if ( $class_ell_percentage >= $ell_threshold )
		return true;
	else
		return false;
}

// Checks if class is at-risk
function is_class_at_risk( $class_id ) {
	$teacher_id = get_post_meta( $class_id, 'teacher', true );
	$school_id = get_post_meta( $teacher_id, 'school', true );

	return is_school_at_risk( $school_id );
}

 ?>