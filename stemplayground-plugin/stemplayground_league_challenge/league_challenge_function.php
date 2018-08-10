<?php 
/*
 * League Challenge Custom Post
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$uc_league_challenge_fields = array(
	'status',
	'challenge_start',
	'challenge_end',
	'activities',
);

// Register league challenge custom post
function uc_register_post_league_challenge() {

	register_post_type( 'uc_league_challenge',
		array(
			'menu_icon' 	=> 'dashicons-awards',
			'supports' 		=> array( 'title', 'thumbnail' ),
			'show_ui' 		=> true,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'labels'        => array(
				'name'                	=> __( 'League Challenges', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name'       	=> __( 'Challenge', UPTOWNCODE_PLUGIN_NAME ),
				'add_new'             	=> __( 'Add New Challenge', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item'        	=> __( 'Add New Challenge', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name'           	=> __( 'Challenges', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon'   	=> __( 'Parent Challenge', UPTOWNCODE_PLUGIN_NAME ),
				'all_items'           	=> __( 'All Challenges', UPTOWNCODE_PLUGIN_NAME ),
				'view_item'           	=> __( 'View Challenge', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item'           	=> __( 'Edit Challenge', UPTOWNCODE_PLUGIN_NAME ),
				'update_item'         	=> __( 'Update Challenge', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'        	=> __( 'Search Challenge', UPTOWNCODE_PLUGIN_NAME ),
				'not_found' 			=> __(  'No Challenge found', UPTOWNCODE_PLUGIN_NAME ),
				'not_found_in_trash'	=> __(  'No Challenge found in Trash', UPTOWNCODE_PLUGIN_NAME ),
				'parent' 				=> __(  'Parent Challenge', UPTOWNCODE_PLUGIN_NAME ),
			)
		)
	);
}
add_action( 'init', 'uc_register_post_league_challenge' );
$uc_league_challenge_metabox = new VP_Metabox( plugin_dir_path( __FILE__ ) . 'league_challenge_metabox.php' );

// Returns list of challenges
function get_uc_league_challenges( $post_status = 'publish') {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_league_challenge',
		'post_status' => $post_status,
		'posts_per_page' => -1,
	));

	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns current league challenge
function get_current_uc_league_challenge() {

	// If different challenges are ever added, create a new post meta for uc_challenge, and search for that in this query
	$wp_posts = get_posts(array(
		'post_type' => 'uc_league_challenge',
		'post_status' => 'publish',
		'posts_per_page' => -1,
	));

	foreach ($wp_posts as $post) {
		$challenge_start = DateTime::createFromFormat( 'm-d-Y', get_post_meta( $post->ID, 'challenge_start', true ) );
		$challenge_end = DateTime::createFromFormat( 'm-d-Y', get_post_meta( $post->ID, 'challenge_end', true ) );
		$current_date = DateTime::createFromFormat( 'm-d-Y', current_time( 'm-d-Y') );


		if ( $current_date > $challenge_start && $current_date < $challenge_end ) {
			$result = array('value' => $post->ID, 'label' => $post->post_title);
			break;
		} else {
			$result = false;
		}
	}
	return $result;
}

// Returns an array of statuses challenge activities
function get_uc_challenge_activities_status( $class_id, $challenge_id ) {
	$activities = get_post_meta( $challenge_id, 'activities', true );

	$result = array();
	$result['statuses'] = array();
	$result['eligible'] = true;

	if ( $activities ) {
		$result['uncompleted'] = count( $activities );
		foreach ( $activities as $activity_id ) {
			$status = signup_status( $class_id, $activity_id );

			$result['statuses'][$activity_id] = $status;
			if ( $status == 'expired' )
				$result['eligible'] = false;
			if ( $status == 'completed' )
				$result['uncompleted']--;
		}
	} else
		return false;

	return $result;
}

// Returns list of challenges winners
function get_uc_league_challenge_winners( $challenge_id = '', $region = 'global' ) {
	$possible_regions = array( 'global', 'country', 'state' );
	$wp_posts = get_posts(array(
		'post_type' => 'uc_league_challenge',
		'post_status' => $post_status,
		'posts_per_page' => -1,
	));

	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

 ?>