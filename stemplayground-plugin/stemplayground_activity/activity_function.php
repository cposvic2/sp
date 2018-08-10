<?php 
/*
 * Activity Custom Post
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$uc_activity_fields = array(
	'availability',
	'difficulty',
	'activity_summary',
	'score_fields',
	'score_titles',
	'score_calculation',
	'required_materials',
	'activity_competition',
	'activity_overview',
	'activity_background_knowledge',
	'activity_scientific_concepts',
	'activity_class_discussion',
	'activity_handout',
	'activity_exam',
	'activity_answer_key',
	'activity_additional',
	'video_engagement_links',
);

// Register activity custom post
function uc_register_post_activity() {

	register_post_type( 'uc_activity',
		array(
			'menu_icon' 	=> 'dashicons-clipboard',
			'supports' 		=> array( 'title', 'editor', 'thumbnail' ),
			'public' => true,
			'show_ui' 		=> true,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => true,
			'labels'        => array(
				'name'                	=> __( 'Activities', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name'       	=> __( 'Activity', UPTOWNCODE_PLUGIN_NAME ),
				'add_new'             	=> __( 'Add New Activity', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item'        	=> __( 'Add New Activity', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name'           	=> __( 'Activities', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon'   	=> __( 'Parent Activity', UPTOWNCODE_PLUGIN_NAME ),
				'all_items'           	=> __( 'All Activities', UPTOWNCODE_PLUGIN_NAME ),
				'view_item'           	=> __( 'View Activity', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item'           	=> __( 'Edit Activity', UPTOWNCODE_PLUGIN_NAME ),
				'update_item'         	=> __( 'Update Activity', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'        	=> __( 'Search Activity', UPTOWNCODE_PLUGIN_NAME ),
				'not_found' 			=> __(  'No Activity found', UPTOWNCODE_PLUGIN_NAME ),
				'not_found_in_trash'	=> __(  'No Activity found in Trash', UPTOWNCODE_PLUGIN_NAME ),
				'parent' 				=> __(  'Parent Activity', UPTOWNCODE_PLUGIN_NAME ),
			),
			'rewrite' 		=> array(
				'slug'                	=> __( 'activities', UPTOWNCODE_PLUGIN_NAME ),
			),
		)
	);
	flush_rewrite_rules();
	register_taxonomy(
		'uc_activity_branch',
		'uc_activity',
		array(
			'labels' => array(
				'name' 				=> __( 'Branches of Science', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name' 	=> __( 'Branch', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'  	=> __( 'Search Branches', UPTOWNCODE_PLUGIN_NAME ),
				'all_items' 		=> __( 'All Branches', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item' 		=> __( 'Parent Branch', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon' => __( 'Parent Branch:', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item' 		=> __( 'Edit Branch', UPTOWNCODE_PLUGIN_NAME ),
				'update_item' 		=> __( 'Update Branch', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item' 		=> __( 'Add New Branch', UPTOWNCODE_PLUGIN_NAME ),
				'new_item_name'		=> __( 'New Branch Name', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name' 		=> __( 'Branches of Science', UPTOWNCODE_PLUGIN_NAME ),
			),
			'hierarchical' => true,
			'public' => true,
			'rewrite' => true
		)
	);
	register_taxonomy(
		'uc_activity_cc',
		'uc_activity',
		array(
			'labels' => array(
				'name' 				=> __( 'Common Core Categories', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name' 	=> __( 'Category', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'  	=> __( 'Search Categories', UPTOWNCODE_PLUGIN_NAME ),
				'all_items' 		=> __( 'All Categories', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item' 		=> __( 'Parent Category', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon' => __( 'Parent Category:', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item' 		=> __( 'Edit Category', UPTOWNCODE_PLUGIN_NAME ),
				'update_item' 		=> __( 'Update Category', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item' 		=> __( 'Add New Category', UPTOWNCODE_PLUGIN_NAME ),
				'new_item_name'		=> __( 'New Category Name', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name' 		=> __( 'Common Core Categories', UPTOWNCODE_PLUGIN_NAME ),
			),
			'hierarchical' => true,
			'public' => true,
			'rewrite' => true
		)
	);
	register_taxonomy(
		'uc_activity_ngss',
		'uc_activity',
		array(
			'labels' => array(
				'name' 				=> __( 'NGSS Categories', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name' 	=> __( 'Category', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'  	=> __( 'Search Categories', UPTOWNCODE_PLUGIN_NAME ),
				'all_items' 		=> __( 'All Categories', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item' 		=> __( 'Parent Category', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon' => __( 'Parent Category:', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item' 		=> __( 'Edit Category', UPTOWNCODE_PLUGIN_NAME ),
				'update_item' 		=> __( 'Update Category', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item' 		=> __( 'Add New Category', UPTOWNCODE_PLUGIN_NAME ),
				'new_item_name'		=> __( 'New Category Name', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name' 		=> __( 'NGSS Categories', UPTOWNCODE_PLUGIN_NAME ),
			),
			'hierarchical' => true,
			'public' => true,
			'rewrite' => true
		)
	);

}
add_action( 'init', 'uc_register_post_activity' );
$uc_activity_metabox = new VP_Metabox( plugin_dir_path( __FILE__ ) . 'activity_metabox.php' );

// Display metabox list of signups
function uc_display_activity_metabox_b() {
	$post_id = intval( $_GET['post'] );
	$list_items = get_uc_signups_by_activity( $post_id, array('publish', 'private') );
	echo '
	<table class="widefat striped">
		<thead>
			<tr>
				<th>Signup ID</th>
				<th>Class</th>
				<th>School</th>
				<th>Status</th>
				<th>Average Score</th>
			</tr>
		</thead>
		<tbody>';

	foreach ( $list_items as $list_item ) {
		$class_id = get_post_meta( $list_item['value'], 'class', true );
		$teacher_id = get_post_meta( $class_id, 'teacher', true );
		$school_id = get_post_meta( $teacher_id, 'school', true );
		$signup_status = signup_status( $class_id, get_the_id() );
		$average = get_class_score_average( $class_id, get_the_id() );
		echo '<tr><th>';
		edit_post_link($list_item['value'], '', '', $list_item['value'] );
		echo '</th><th>';
		edit_post_link(get_the_title( $class_id ), '', '', $class_id );
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

function uc_register_activity_metaboxes() {
    add_meta_box( 'uc_activity_metabox_b', __( 'List of Signups', UPTOWNCODE_PLUGIN_NAME ), 'uc_display_activity_metabox_b', 'uc_activity' );

}
add_action( 'add_meta_boxes', 'uc_register_activity_metaboxes' );

// Returns list of activities
function get_uc_activities( $post_status = 'publish') {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_activity',
		'post_status' => $post_status,
		'posts_per_page' => -1,
	));

	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of available activities
function get_available_uc_activities( $class_id = '', $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_activity',
		'post_status' => $post_status,
		'posts_per_page' => -1,
	));

	$result = array();
	foreach ($wp_posts as $post) {
		if ( !signup_status( $class_id, $post->ID ) )
			$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of active activities
function get_active_uc_activities( $class_id = '', $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_activity',
		'post_status' => $post_status,
		'posts_per_page' => -1,
	));

	$result = array();
	foreach ($wp_posts as $post) {
		if ( signup_status( $class_id, $post->ID ) == 'active' )
			$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of completed activities
function get_completed_uc_activities( $class_id = '', $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_activity',
		'post_status' => $post_status,
		'posts_per_page' => -1,
	));

	$result = array();
	foreach ($wp_posts as $post) {
		if ( signup_status( $class_id, $post->ID ) == 'completed' )
			$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of expired activities
function get_expired_uc_activities( $class_id = '', $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_activity',
		'post_status' => $post_status,
		'posts_per_page' => -1,
	));

	$result = array();
	foreach ($wp_posts as $post) {
		if ( signup_status( $class_id, $post->ID ) == 'expired' )
			$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of activities for a challenge
function get_uc_activities_by_challenge( $challenge_id = '', $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_activity',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key'     => 'challenge',
				'value'   => $challenge_id,
			),
		),
	));
	
	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of score fields for an activity
function get_score_fields_by_activity( $activity_id = '' ) {
	$number_of_scores = get_post_meta( $activity_id, 'score_fields', true );
	$score_titles_text = get_post_meta( $activity_id, 'score_titles', true );
	if (strpos($score_titles_text, ';') !== false) {
		$score_titles = explode(";", $score_titles_text);
	} else {
		$score_titles[0] = $score_titles_text;
	}
	
	$result = array();

	for ($i = 1; $i <= $number_of_scores; $i++) {
		if ( !empty( $score_titles[$i - 1] ) )
			$title = trim( $score_titles[$i - 1] );
		else
			$title = 'Score '.$i;
		$result[] = $title;
	}
	return $result;
}

// Gets score min/maxes
function get_score_ranges( $activity_id ) {
	$return = array();
	$range_text = str_replace(' ', '', strtoupper(get_post_meta( $activity_id, 'score_range', true )));
	
	if (!empty($range_text)) {

		$range_text_array = explode(';', $range_text);
		foreach ($range_text_array as $single_range_text) {
			
			if (strpos($single_range_text, 'SCORE') !== false && strpos($single_range_text, '(') !== false) {
				$range_text_array = explode('(', $single_range_text);

				// First find score number
				$score_num = intval(str_replace('SCORE', '', $range_text_array[0]));
				if (empty($score_num))
					continue;

				// Second find min/max
				$range_text_array[1] = str_replace(')', '', $range_text_array[1]);
				$score_min_max_array = explode(',', $range_text_array[1]);
				$decoded_score_min_max_array = array();
				if (isset($score_min_max_array[0]) && strlen($score_min_max_array[0]))
					$decoded_score_min_max_array['min'] = intval($score_min_max_array[0]);
				if (isset($score_min_max_array[1]) && strlen($score_min_max_array[1]))
					$decoded_score_min_max_array['max'] = intval($score_min_max_array[1]);


				$return[$score_num] = $decoded_score_min_max_array;
			}
		}
	}
	return $return;
}

 ?>