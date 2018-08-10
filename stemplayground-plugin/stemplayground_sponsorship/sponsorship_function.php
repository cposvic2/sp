<?php 
/*
 * Sponsorship Custom Post
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$uc_sponsorship_fields = array(
	'user_id',
	'fufilled',
	'sponsor_name',
	'sponsor_twitter',
	'sponsor_email',
	'sponsor_stripe',
	'class',
	'stripe_charge',
);

// Register Sponsorship custom post
function uc_register_post_sponsorship() {

	register_post_type( 'uc_sponsorship',
		array(
			'menu_icon' 	=> 'dashicons-megaphone',
			'supports' 		=> array( '', ),
			'show_ui' 		=> true,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'labels'        => array(
				'name'                	=> __( 'Sponsorships', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name'       	=> __( 'Sponsorship', UPTOWNCODE_PLUGIN_NAME ),
				'add_new'             	=> __( 'Add New Sponsorship', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item'        	=> __( 'Add New Sponsorship', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name'           	=> __( 'Sponsorships', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon'   	=> __( 'Parent Sponsorship', UPTOWNCODE_PLUGIN_NAME ),
				'all_items'           	=> __( 'All Sponsorships', UPTOWNCODE_PLUGIN_NAME ),
				'view_item'           	=> __( 'View Sponsorship', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item'           	=> __( 'Edit Sponsorship', UPTOWNCODE_PLUGIN_NAME ),
				'update_item'         	=> __( 'Update Sponsorship', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'        	=> __( 'Search Sponsorship', UPTOWNCODE_PLUGIN_NAME ),
				'not_found' 			=> __(  'No Sponsorship found', UPTOWNCODE_PLUGIN_NAME ),
				'not_found_in_trash'	=> __(  'No Sponsorship found in Trash', UPTOWNCODE_PLUGIN_NAME ),
				'parent' 				=> __(  'Parent Sponsorship', UPTOWNCODE_PLUGIN_NAME ),
			)
		)
	);
}
add_action( 'init', 'uc_register_post_sponsorship' );
$uc_sponsorship_metabox = new VP_Metabox( plugin_dir_path( __FILE__ ) . 'sponsorship_metabox.php' );

// Modify columns for Sponsorships in Admin
function uc_sponsorship_columns( $columns ) {
	return $columns;
}
add_filter( 'manage_uc_sponsorship_posts_columns', 'uc_sponsorship_columns' );

function uc_sponsorship_custom_column( $column, $post_id ) {
}
add_action( 'manage_uc_sponsorship_posts_custom_column' , 'uc_sponsorship_custom_column', 10, 2 );

// Returns list of Sponsorships
function get_uc_sponsorships( $post_status = 'publish', $school_year = false ) {
	$args = array(
		'post_type' => 'uc_sponsorship',
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
		$result[] = array('value' => $post->ID, 'label' => $post->post_title );
	}
	return $result;
}

function get_uc_sponsorship_for_class( $class_id, $post_status = 'publish', $school_year = false ) {
	$args = array(
		'post_type' => 'uc_sponsorship',
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
	if (!count($wp_posts))
		return null;

	return array('value' => $wp_posts[0]->ID, 'label' => $wp_posts[0]->post_title );
}