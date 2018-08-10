<?php 
/*
 * Student Custom Post
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$uc_student_fields = array(
	'firstname',
	'lastinitial',
	'grade',
	'teacher',
	'class',
	'gender',
	'ell',
);

// Register student custom post
function uc_register_post_student() {

	register_post_type( 'uc_student',
		array(
			'menu_icon' 	=> 'dashicons-universal-access-alt',
			'supports' 		=> array( '', ),
			'show_ui' 		=> true,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'labels'        => array(
				'name'                	=> __( 'Students', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name'       	=> __( 'Student', UPTOWNCODE_PLUGIN_NAME ),
				'add_new'             	=> __( 'Add New Student', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item'        	=> __( 'Add New Student', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name'           	=> __( 'Students', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon'   	=> __( 'Parent Student', UPTOWNCODE_PLUGIN_NAME ),
				'all_items'           	=> __( 'All Students', UPTOWNCODE_PLUGIN_NAME ),
				'view_item'           	=> __( 'View Student', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item'           	=> __( 'Edit Student', UPTOWNCODE_PLUGIN_NAME ),
				'update_item'         	=> __( 'Update Student', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'        	=> __( 'Search Student', UPTOWNCODE_PLUGIN_NAME ),
				'not_found' 			=> __(  'No Student found', UPTOWNCODE_PLUGIN_NAME ),
				'not_found_in_trash'	=> __(  'No Student found in Trash', UPTOWNCODE_PLUGIN_NAME ),
				'parent' 				=> __(  'Parent Student', UPTOWNCODE_PLUGIN_NAME ),
			)
		)
	);
	register_taxonomy(
		'uc_grade',
		array( 'uc_student', 'uc_class', 'uc_activity' ),
		array(
			'labels' => array(
				'name' 				=> __( 'Grades', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name' 	=> __( 'Grade', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'  	=> __( 'Search Grades', UPTOWNCODE_PLUGIN_NAME ),
				'all_items' 		=> __( 'All Grades', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item' 		=> __( 'Parent Grade', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon' => __( 'Parent Grade:', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item' 		=> __( 'Edit Grade', UPTOWNCODE_PLUGIN_NAME ),
				'update_item' 		=> __( 'Update Grade', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item' 		=> __( 'Add New Grade', UPTOWNCODE_PLUGIN_NAME ),
				'new_item_name'		=> __( 'New Grade Name', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name' 		=> __( 'Grades', UPTOWNCODE_PLUGIN_NAME ),
			),
			'hierarchical' => true,
			'public' => true,
			'rewrite' => true
		)
	);
}
add_action( 'init', 'uc_register_post_student' );
$uc_student_metabox = new VP_Metabox( plugin_dir_path( __FILE__ ) . 'student_metabox.php' );

// Get full name of student
function get_student_name( $post_id = false ) {
	if ( $post_id ) {
		$fullname = '';
		$firstname = get_post_meta( $post_id, 'firstname', true );
		$lastinitial = get_post_meta( $post_id, 'lastinitial', true );
		if ( isset( $firstname) && $firstname )
			$fullname .= $firstname . ' ';
		if ( isset( $lastinitial) && $lastinitial )
			$fullname .= $lastinitial;
		return $fullname;
	} else
	return false;
}

// Modify columns for Students in Admin
function uc_student_columns( $columns ) {
	unset( $columns['title'] );
	$columns['name'] = __( 'Name', UPTOWNCODE_PLUGIN_NAME );
	$columns['school_year'] = __( 'School Year', UPTOWNCODE_PLUGIN_NAME );
	$columns['grade'] = __( 'Grade', UPTOWNCODE_PLUGIN_NAME );
	$columns['school'] = __( 'School', UPTOWNCODE_PLUGIN_NAME );
	$columns['teacher'] = __( 'Teacher', UPTOWNCODE_PLUGIN_NAME );
	return $columns;
}
add_filter( 'manage_uc_student_posts_columns', 'uc_student_columns' );
function uc_student_custom_column( $column, $post_id ) {
	switch ( $column ) {
		case 'name' :
			$fullname = get_student_name( $post_id );
			echo '<a class="row-title" href="'.get_edit_post_link( $post_id ).'" title="Edit “'.$fullname.'”">'.$fullname.'</a>';
			break;
		case 'school_year' :
			echo get_school_year_of_post( $post_id ); 
			break;
		case 'grade' :
			$student_grade = get_post_meta( $post_id, 'grade', true );
			$class_grade_title = ( get_term_by('slug', $student_grade, 'uc_grade') ? get_term_by('slug', $student_grade, 'uc_grade')->name : 'Various' );
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
	}
}
add_action( 'manage_uc_student_posts_custom_column' , 'uc_student_custom_column', 10, 2 );

// Returns list of students
function get_uc_students( $post_status = 'publish', $school_year = false ) {
	$args = array(
		'post_type' => 'uc_student',
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
		$fullname = get_student_name( $post->ID );
		$result[] = array('value' => $post->ID, 'label' => $fullname );
	}
	return $result;
}

// Returns list of students in a class
function get_uc_students_by_class( $class_id = '', $post_status = 'publish', $school_year = false ) {
	$args = array(
		'post_type' => 'uc_student',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
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
		$fullname = get_student_name( $post->ID );
		$result[] = array('value' => $post->ID, 'label' => $fullname );
	}
	return $result;
}
VP_Security::instance()->whitelist_function('get_uc_students_by_class');

// Returns list of students of a teacher
function get_uc_students_by_teacher( $teacher_id = '', $post_status = 'publish', $school_year = false ) {
	$args = array(
		'post_type' => 'uc_student',
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
	
	$wp_posts = get_posts( $args );
	$result = array();
	foreach ($wp_posts as $post) {
		$fullname = get_student_name( $post->ID );
		$result[] = array('value' => $post->ID, 'label' => $fullname );
	}
	return $result;
}

// Returns list of students in a school
function get_uc_students_by_school( $school_id = '', $post_status = 'publish', $school_year = false ) {
	$result = array();
	$teachers = get_uc_teachers_by_school( $school_id, $post_status );

	if ( count($teachers) ) {
		$args = array(
			'post_type' => 'uc_student',
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
			$args['meta_query'][] = array( 'key' => 'teacher', 'value' => $teacher['value'] );
		$wp_posts = get_posts($args);
		
		
		foreach ($wp_posts as $post) {
			$fullname = get_student_name( $post->ID );
			$result[] = array('value' => $post->ID, 'label' => $fullname );
		}
	}

	return $result;
}

// Returns list of students by geography
function get_uc_students_by_geography( $geography_array, $post_status = 'publish', $school_year = false ) {
	$teachers = get_uc_teachers_by_geography( $geography_array, $post_status );

	$args = array(
		'post_type' => 'uc_student',
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
		$fullname = get_student_name( $post->ID );
		$result[] = array('value' => $post->ID, 'label' => $fullname );
	}
	return $result;
}

// Returns list of grades
function get_uc_grades( ) {
	$wp_terms = get_terms( 'uc_grade', array(
		'orderby' => 'slug',
		'hide_empty' => 0,
	));
	$result = array();
	foreach ( $wp_terms as $term )
		$result[] = array('value' =>$term->slug, 'label' => $term->name );
	return $result;
}

 ?>