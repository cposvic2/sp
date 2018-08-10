<?php 
/*
 * Teacher Custom Post
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$uc_teacher_fields = array( 
	'first_name', 
	'last_name',
	'mailing_list', 
	'convertkit_id', 
	'email', 
	'birthdate', 
	'gender', 
	'stem_proficient', 
	'college_science', 
	'ambassador', 
	'school', 
	'stem_competitions', 
	'school_at_risk',
	'notes',
);

// Register teacher custom post
function uc_register_post_teacher() {

	register_post_type( 'uc_teacher',
		array(
			'menu_icon' 	=> 'dashicons-businessman',
			'supports' 		=> array( 'title', ),
			'show_ui' 		=> true,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'labels'        => array(
				'name'                	=> __( 'Teachers', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name'       	=> __( 'Teacher', UPTOWNCODE_PLUGIN_NAME ),
				'add_new'             	=> __( 'Add New Teacher', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item'        	=> __( 'Add New Teacher', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name'           	=> __( 'Teachers', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon'   	=> __( 'Parent Teacher', UPTOWNCODE_PLUGIN_NAME ),
				'all_items'           	=> __( 'All Teachers', UPTOWNCODE_PLUGIN_NAME ),
				'view_item'           	=> __( 'View Teacher', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item'           	=> __( 'Edit Teacher', UPTOWNCODE_PLUGIN_NAME ),
				'update_item'         	=> __( 'Update Teacher', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'        	=> __( 'Search Teacher', UPTOWNCODE_PLUGIN_NAME ),
				'not_found' 			=> __(  'No Teacher found', UPTOWNCODE_PLUGIN_NAME ),
				'not_found_in_trash'	=> __(  'No Teacher found in Trash', UPTOWNCODE_PLUGIN_NAME ),
				'parent' 				=> __(  'Parent Teacher', UPTOWNCODE_PLUGIN_NAME ),
			)
		)
	);
}
add_action( 'init', 'uc_register_post_teacher' );
$uc_teacher_metabox = new VP_Metabox( plugin_dir_path( __FILE__ ) . 'teacher_metabox.php' );

// Display metabox list of classes
function uc_display_teacher_metabox_b() {
	$post_id = intval( $_GET['post'] );
	$list_items = get_uc_classes_by_teacher( $post_id );
	echo '<ul>';
	foreach ( $list_items as $list_item ) {
		echo '<li>';
		edit_post_link('edit', '(', ')', $list_item['value'] );
		echo ' ' . $list_item['label'] . '</li>';
	}
	echo '</ul>';
}

// Display metabox list of students
function uc_display_teacher_metabox_c() {
	$post_id = intval( $_GET['post'] );
	$list_items = get_uc_students_by_teacher( $post_id );
	echo '<ul>';
	foreach ( $list_items as $list_item ) {
		echo '<li>';
		edit_post_link('edit', '(', ')', $list_item['value'] );
		echo ' ' . $list_item['label'] . '</li>';
	}
	echo '</ul>';
}

// Display metabox of associated users
function uc_display_teacher_metabox_d() {
	$post_id = intval( $_GET['post'] );
	$list_items = get_users_by_teacher( $post_id );
	echo '<ul>';
	foreach ( $list_items as $list_item ) {
		echo '<li>';
		echo '(<a href="'. get_edit_user_link( $list_item['value'] ) .'">edit</a>)';
		echo ' ' . $list_item['label'] . '</li>';
	}
	echo '</ul>';
}

// Add additional metaboxes
function uc_register_teacher_metaboxes() {
    add_meta_box( 'uc_teacher_metabox_d', __( 'Associated Users', UPTOWNCODE_PLUGIN_NAME ), 'uc_display_teacher_metabox_d', 'uc_teacher', 'advanced', 'high' );
	add_meta_box( 'uc_teacher_metabox_b', __( 'List of Classes', UPTOWNCODE_PLUGIN_NAME ), 'uc_display_teacher_metabox_b', 'uc_teacher' );
	add_meta_box( 'uc_teacher_metabox_c', __( 'List of Students', UPTOWNCODE_PLUGIN_NAME ), 'uc_display_teacher_metabox_c', 'uc_teacher' );
}
add_action( 'add_meta_boxes', 'uc_register_teacher_metaboxes' );

// Returns list of teachers
function get_uc_teachers( $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_teacher',
		'post_status' => $post_status,
		'posts_per_page' => -1,
	));

	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of teachers for a school
function get_uc_teachers_by_school( $school_id = '', $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_teacher',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key'     => 'school',
				'value'   => $school_id,
			),
		),
	));
	
	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}
VP_Security::instance()->whitelist_function('get_uc_teachers_by_school');

// Returns list of teachers by geography
function get_uc_teachers_by_geography( $geography_array, $post_status = 'publish' ) {
	$schools = get_uc_schools_by_geography( $geography_array, $post_status );

	$args = array(
		'post_type' => 'uc_teacher',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			'relation' => 'OR',
		),
	);

	foreach ( $schools as $school )
		$args['meta_query'][] = array( 'key' => 'school', 'value'   => $school['value'] );

	$result = array();
	$wp_posts = get_posts($args);
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Get step teacher is on
function get_uc_teacher_step( $teacher_id ) {
	// Does the teacher have school info?
	$school_id = get_post_meta( $teacher_id, 'school', true );
	if ( !$school_id )
		return 'edit_school';

	// Does the teacher have class info?
	$classes = get_uc_classes_by_teacher( $teacher_id );
	if ( !count( $classes ) )
		return 'edit_class';

	// Does the teacher have student info?
	$students = get_uc_students_by_teacher( $teacher_id );
	if ( !count( $students ) )
		return 'edit_students';

	// Does the teacher have team info?
	$groups = get_uc_groups_by_teacher( $teacher_id );
	if ( !count( $groups ) )
		return 'edit_groups';

	// All info? Return activity
	return 'edit_activities';
}

 ?>