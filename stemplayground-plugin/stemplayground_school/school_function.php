<?php 
/*
 * School Custom Post
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$uc_school_fields = array(
	'school_verified',
	'streetaddress',
	'streetaddress2',
	'city',
	'county',
	'zip',
	'state',
	'country',
	'non_traditional',
	'notes',
	'latitude',
	'longitude',
);


// Register school custom post
function uc_register_post_school() {

	register_post_type( 'uc_school',
		array(
			'menu_icon' 	=> 'dashicons-admin-home',
			'supports' 		=> array( 'title', ),
			'show_ui' 		=> true,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'labels'        => array(
				'name'                	=> __( 'Schools', UPTOWNCODE_PLUGIN_NAME ),
				'singular_name'       	=> __( 'School', UPTOWNCODE_PLUGIN_NAME ),
				'add_new'             	=> __( 'Add New School', UPTOWNCODE_PLUGIN_NAME ),
				'add_new_item'        	=> __( 'Add New School', UPTOWNCODE_PLUGIN_NAME ),
				'menu_name'           	=> __( 'Schools', UPTOWNCODE_PLUGIN_NAME ),
				'parent_item_colon'   	=> __( 'Parent School', UPTOWNCODE_PLUGIN_NAME ),
				'all_items'           	=> __( 'All Schools', UPTOWNCODE_PLUGIN_NAME ),
				'view_item'           	=> __( 'View School', UPTOWNCODE_PLUGIN_NAME ),
				'edit_item'           	=> __( 'Edit School', UPTOWNCODE_PLUGIN_NAME ),
				'update_item'         	=> __( 'Update School', UPTOWNCODE_PLUGIN_NAME ),
				'search_items'        	=> __( 'Search School', UPTOWNCODE_PLUGIN_NAME ),
				'not_found' 			=> __(  'No School found', UPTOWNCODE_PLUGIN_NAME ),
				'not_found_in_trash'	=> __(  'No School found in Trash', UPTOWNCODE_PLUGIN_NAME ),
				'parent' 				=> __(  'Parent School', UPTOWNCODE_PLUGIN_NAME ),
			)
		)
	);
}
add_action( 'init', 'uc_register_post_school' );
$uc_school_metabox = new VP_Metabox( plugin_dir_path( __FILE__ ) . 'school_metabox.php' );

// Display metabox list of classes
function uc_display_school_metabox_b() {
	$post_id = intval( $_GET['post'] );
	$list_items = get_uc_classes_by_school( $post_id, false, false, 'all', array('publish', 'private') );
	echo '
	<table class="widefat striped">
		<thead>
			<tr>
				<th>Class</th>
				<th>Teacher</th>
				<th>Grade</th>
				<th>At Risk</th>
				<th>ESL</th>
			</tr>
		</thead>
		<tbody>';

	foreach ( $list_items as $list_item ) {
		$teacher_id = get_post_meta( $list_item['value'], 'teacher', true );
		$grade = get_post_meta( $list_item['value'], 'grade', true );
		echo '<tr><th>';
		edit_post_link($list_item['label'], '', '', $list_item['value'] );
		echo '</th><th>';
		edit_post_link(get_the_title( $teacher_id ), '', '', $teacher_id );
		echo '</th><th>'.$grade.'</th>
				<th>'.( is_class_at_risk($list_item['value']) ? 'Yes' : 'No' ).'</th>
				<th>'.( is_class_ell($list_item['value']) ? 'Yes' : 'No' ).'</th>
			</tr>';
	}
	echo '
		</tbody>
	</table>
	';
}

// Display metabox list of teachers
function uc_display_school_metabox_c() {
	$post_id = intval( $_GET['post'] );
	$list_items = get_uc_teachers_by_school( $post_id, array('publish', 'private') );
	echo '<ul>';
	foreach ( $list_items as $list_item ) {
		echo '<li>';
		edit_post_link('edit', '(', ')', $list_item['value'] );
		echo ' ' . $list_item['label'] . '</li>';
	}
	echo '</ul>';
}

// Add additional metaboxes
function uc_register_school_metaboxes() {
	add_meta_box( 'uc_school_metabox_b', __( 'List of Classes', UPTOWNCODE_PLUGIN_NAME ), 'uc_display_school_metabox_b', 'uc_school' );
	add_meta_box( 'uc_school_metabox_c', __( 'List of Teachers', UPTOWNCODE_PLUGIN_NAME ), 'uc_display_school_metabox_c', 'uc_school' );
}
add_action( 'add_meta_boxes', 'uc_register_school_metaboxes' );

// Modify columns for Schools in Admin
function uc_school_columns( $columns ) {
	$columns['verified'] = __( 'Verified', UPTOWNCODE_PLUGIN_NAME );
	$columns['nontraditional'] = __( 'Non-Traditional', UPTOWNCODE_PLUGIN_NAME );
	$columns['streetaddress'] = __( 'Street Address', UPTOWNCODE_PLUGIN_NAME );
	$columns['city'] = __( 'City', UPTOWNCODE_PLUGIN_NAME );
	$columns['state'] = __( 'State', UPTOWNCODE_PLUGIN_NAME );
	$columns['country'] = __( 'Country', UPTOWNCODE_PLUGIN_NAME );
	return $columns;
}
add_filter( 'manage_uc_school_posts_columns', 'uc_school_columns' );
function uc_school_custom_column( $column, $post_id ) {
	switch ( $column ) {
		case 'verified' :
			echo ( get_post_meta( $post_id, 'school_verified', true ) ? 'Yes' : 'No' );
			break;
		case 'nontraditional':
			echo ( get_post_meta( $post_id, 'non_traditional', true ) ? 'Yes' : 'No' );
			break;
		case 'streetaddress':
			echo get_post_meta( $post_id, 'streetaddress', true );
			break;
		case 'city':
			echo get_post_meta( $post_id, 'city', true );
			break;
		case 'state':
			echo get_post_meta( $post_id, 'state', true );
			break;
		case 'country':
			global $countries;
			$school_country = get_post_meta( $post_id, 'country', true );
			echo (isset($countries[$school_country]) ? $countries[$school_country] : '');
			break;
	}
}
add_action( 'manage_uc_school_posts_custom_column' , 'uc_school_custom_column', 10, 2 );

// Adds Quick Edit options
function display_school_quickedit_options( $column_name, $post_type ) {
	switch ( $post_type ) {
		case 'uc_school':
			?>
			<fieldset class="inline-edit-col-right">
				<div class="inline-edit-group">
					<label>
						<span class="title">Verified</span>
						<input type="checkbox" name="school_verified" value="" />
					 </label>
				</div>
			</fieldset>
			<?php
			break;
	}
}
add_action( 'quick_edit_custom_box', 'display_school_quickedit_options', 10, 2 );

// Adds Quick Edit script
function quick_edit_script() {
	wp_enqueue_script( 'custom-quick-edit', UPTOWNCODE_PLUGIN_URL . '/assets/js/quick_edit.js', array( 'jquery', 'inline-edit-post' ), '', true );
}
add_action( 'admin_print_scripts-edit.php', 'quick_edit_script' );

// Saves Quick Edit
add_action( 'save_post','uc_quick_edit_save_post', 10, 2 );
function uc_quick_edit_save_post( $post_id, $post ) {

   // don't save for autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	// dont save for revisions
	if ( isset( $post->post_type ) && $post->post_type == 'revision' )
		return;

	switch( $post->post_type ) {
		case 'uc_school':
			if ( isset( $_REQUEST['school_verified'] ) )
				update_post_meta($post_id, 'school_verified', TRUE);
			else
				update_post_meta($post_id, 'school_verified', FALSE);
			break;
   }
}

// Returns list of schools
function get_uc_schools( $post_status = 'publish') {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_school',
		'post_status' => $post_status,
		'posts_per_page' => -1,
	));

	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of schools by country
function get_uc_schools_by_country( $country, $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_school',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key'     => 'country',
				'value'   => $country,
			),
		),
	));

	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of schools by zip code
function get_uc_schools_by_zip_code( $zip, $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_school',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key'     => 'zip',
				'value'   => $zip,
			),
		),
	));

	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of schools by zip code and country
function get_uc_schools_by_zip_code_and_country( $zip, $country, $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_school',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key'     => 'zip',
				'value'   => $zip,
			),
			array(
				'key'     => 'country',
				'value'   => $country,
			),
			array(
				'key'     => 'school_verified',
				'value'   => 1,
			),
		),
	));

	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of schools by state
function get_uc_schools_by_state( $state, $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_school',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key'     => 'state',
				'value'   => $state,
			),
		),
	));

	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of schools by county
function get_uc_schools_by_county( $county, $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_school',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key'     => 'county',
				'value'   => $county,
			),
		),
	));

	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of schools by city
function get_uc_schools_by_city( $city, $post_status = 'publish' ) {
	$wp_posts = get_posts(array(
		'post_type' => 'uc_school',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key'     => 'city',
				'value'   => $city,
			),
		),
	));

	$result = array();
	foreach ($wp_posts as $post) {
		$result[] = array('value' => $post->ID, 'label' => $post->post_title);
	}
	return $result;
}

// Returns list of schools by geography
function get_uc_schools_by_geography( $geography_array, $post_status = 'publish' ) {
	$args = array(
		'post_type' => 'uc_school',
		'post_status' => $post_status,
		'posts_per_page' => -1,
		'meta_query' => array(
			'relation' => 'AND',
		),
	);

	$result = array();
	if ( is_array($geography_array) ) {
		foreach ($geography_array as $geography) {
			$args['meta_query'][] = array(
				'key'     => $geography['type'],
				'value'   => $geography['value'],
			);
		}
		$wp_posts = get_posts($args);
		foreach ($wp_posts as $post) {
			$result[] = array('value' => $post->ID, 'label' => $post->post_title);
		}

	}
	return $result;
}

// Creates school
function create_uc_school( $user_id, $sctitle = '', $scnontrad = 0, $latitude = 0, $longitude = 0, $scaddress1 = false, $scaddress2 = false, $sccity = false, $sccounty = false, $scstate = false, $sczip = false, $sccountry = false, $post_status = 'publish' ) {

	if ( strlen( $sctitle ) && $scaddress1 && $sccity && $sccountry ) {

		$new_school = array (
			'post_title' => $sctitle,
			'post_type' => 'uc_school',
			'post_author' => $user_id,
			'post_status' => $post_status
		);

		$school_id = wp_insert_post( $new_school );

		if ( !is_wp_error( $school_id ) ) {

			global $uc_school_fields;
			update_post_meta( $school_id, 'school_verified', 0 );
			update_post_meta( $school_id, 'non_traditional', $scnontrad );
			update_post_meta( $school_id, 'latitude', $latitude );
			update_post_meta( $school_id, 'longitude', $longitude );
			update_post_meta( $school_id, 'uc_school_fields', $uc_school_fields );
			update_post_meta( $school_id, 'streetaddress', $scaddress1 );
			update_post_meta( $school_id, 'streetaddress2', $scaddress2 );
			update_post_meta( $school_id, 'city', $sccity );
			update_post_meta( $school_id, 'county', $sccounty );
			update_post_meta( $school_id, 'state', $scstate );
			update_post_meta( $school_id, 'zip', $sczip );
			update_post_meta( $school_id, 'country', $sccountry );

			return $school_id;
		}
	} 
	return false;
}

// Checks if school is at-risk
function is_school_at_risk( $school_id ) {
	$at_risk_threshold = vp_option('uc_option.at_risk_threshold');
	$teachers = get_uc_teachers_by_school( $school_id );
	$at_risk_teachers_count = 0;

	if ( count( $teachers ) ) {
		foreach ( $teachers as $teacher ) {
			if ( get_post_meta( $teacher['value'], 'school_at_risk', true ) == 1 )
				$at_risk_teachers_count++;

		}

		if ( $at_risk_teachers_count / count( $teachers ) * 100 >= $at_risk_threshold )
			return true;
		else
			return false;
	} else
		return false;
}

// Checks if school is ELL
function is_school_ell( $school_id ) {
	$school_ell_threshold = vp_option('uc_option.school_ell_threshold');
	$ell_updated = get_post_meta( $school_id, 'ell_updated', true );

	if ( $ell_updated ) {
		$school_ell_percentage = get_post_meta( $school_id, 'school_ell_percentage', true );
	} else {
		$classes = get_uc_classes_by_school( $school_id );
		$ell_classes_count = 0;
		foreach ( $classes as $class ) {
			if ( is_class_ell( $class['value'] ) )
				$ell_classes_count++;
		}
		$school_ell_percentage = $ell_classes_count / count( $classes ) * 100;

		update_post_meta( $school_id, 'ell_updated', 1 );
		update_post_meta( $school_id, 'school_ell_percentage', $school_ell_percentage );
	}

	if ( $school_ell_percentage >= $school_ell_threshold )
		return true;
	else
		return false;
}

// Check if two schools are in same geographic area
function schools_share_same_geographic_type( $school_id_a, $school_id_b, $geographic_type = 'country' ) {

	switch ( $geographic_type ) {
		case 'city':
		case 'county':
		case 'state':
			$state_a = get_post_meta( $school_id_a, 'state', true );
			$state_b = get_post_meta( $school_id_b, 'state', true );
			if ( $state_a != $state_b )
				return false;
			break;
		case 'country':
			$country_a = get_post_meta( $school_id_a, 'country', true );
			$country_b = get_post_meta( $school_id_b, 'country', true );
			if ( $country_a != $country_b )
				return false;
			break;
		case 'global':
			return true;
			break;
		default:
			return false;
			break;
	}

	if ( $geographic_type == 'city' ) {
		$city_a = get_post_meta( $school_id_a, 'city', true );
		$city_b = get_post_meta( $school_id_b, 'city', true );
		if ( $city_a != $city_b )
				return false;
	}
	if ( $geographic_type == 'county' ) {
		$county_a = get_post_meta( $school_id_a, 'county', true );
		$county_b = get_post_meta( $school_id_b, 'county', true );
		if ( $county_a != $county_b )
				return false;
	}

	return true;
}

VP_Security::instance()->whitelist_function('get_administrative_areas_level_1');

 ?>