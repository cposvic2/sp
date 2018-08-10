<?php 
/*
 * 
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_register_script( 'stemplayground-admin', UPTOWNCODE_PLUGIN_URL . 'assets/js/stemplayground-admin.js', array( 'jquery' ) );

add_action( 'admin_enqueue_scripts', 'enqueue_uc_admin_scripts_and_styles' );
function enqueue_uc_admin_scripts_and_styles() {
	// Enqueue parent style
	wp_enqueue_style( 'uc-admin-style', UPTOWNCODE_PLUGIN_URL . '/assets/css/admin-style.css' );
	wp_enqueue_script( 'stemplayground-admin' );
}

function custom_themify_hide_custom_panel( $meta ) {
	global $custom_post_types;
	$post_id = $_GET['post'];
	$post_type = $_GET['post_type'];
	if ( in_array( get_post_type( $post_id ), $custom_post_types ) || in_array( $post_type, $custom_post_types ) ) {
		return array();
	} else {
		return $meta;
	}
	return array();
};

add_filter( 'themify_do_metaboxes', 'custom_themify_hide_custom_panel', 99 );

// Reports page
function uc_register_custom_menu_page() {
	add_menu_page(
		__( 'Search Reports', UPTOWNCODE_PLUGIN_NAME ),
		__( 'Reports', UPTOWNCODE_PLUGIN_NAME ),
		'manage_options',
		'uc-reports',
		'uc_reports_callback',
		'dashicons-chart-pie',
		6
	);
}
add_action( 'admin_menu', 'uc_register_custom_menu_page' );

function uc_reports_callback() {
	echo '
<div class="wrap">
	<h2>Search Reports</h2>
	<form>
		<h2 class="title">Who</h2>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="uc_post_type">Find All</label></th>
					<td>
						<select name="uc_post_type" id="uc_post_type">
							<option value="uc-school">Schools</option>
							<option value="uc-teacher">Teachers</option>
							<option value="uc-class">Classes</option>
							<option value="uc-group">Teams</option>
							<option value="uc-student">Students</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<h2 class="title">When</h2>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="country">School Year</label></th>
					<td>
						<select name="school_year" class="" >
								<option value="all">All</option>';
	$school_years = get_school_years();
	foreach ( $school_years as $school_year ) {
		echo '<option value="'. $school_year['value'] .'">'. $school_year['label'] .'</option>';
	}
	echo '
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<h2 class="title">Where</h2>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="country">Country</label></th>
					<td>
						<select name="country" class="uc-change" data-update="country" >
								<option value=""></option>';
	$countries = get_countries();
	foreach ( $countries as $country ) {
		echo '<option value="'. $country['value'] .'">'. $country['label'] .'</option>';
	}
	echo '
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="state">State</label></th>
					<td>
						<select name="state" class="uc-change update-country" data-update="state">
							<option value="" disabled selected></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">City/County Search</th>
					<td>
						<fieldset>
							<label for="city_county_search_city"><input type="radio" name="city_county_search" id="city_county_search_city" value="city">Search City</label><br>
							<label for="city_county_search_county"><input type="radio" name="city_county_search" id="city_county_search_county" value="county">Search County</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="county">County</label></th>
					<td>
						<select name="county" class="uc-change update-state" data-update="county">
							<option value="" disabled selected></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="city">City</label></th>
					<td>
						<select name="city" class="uc-change update-state" data-update="city">
							<option value="" disabled selected></option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<h2 class="title">Attributes</h2>
		<table class="form-table">
			<tbody>
				<tr class="uc_display display-uc-student display-uc-group display-uc-class display-uc-teacher" style="display: none">
					<th scope="row"><label for="grade">Grade</label></th>
					<td>
						<select name="grade" class="">
							<option value="all" selected>All</option>
							<option value="">Various</option>';
	$grades = get_uc_grades();
	foreach ( $grades as $grade ) {
		echo '<option value="'. $grade['value'] .'">'. $grade['label'] .'</option>';
	}
	echo '
						</select>
					</td>
				</tr>
				<tr class="uc_display display-uc-student display-uc-teacher" style="display: none">
					<th scope="row"><label for="gender">Gender</label></th>
					<td>
						<select name="gender" class="">
							<option value="all" selected>All</option>';
	$genders = get_genders();
	foreach ( $genders as $gender ) {
		echo '<option value="'. $gender['value'] .'">'. $gender['label'] .'</option>';
	}
	echo '
						</select>
					</td>
				</tr>
				<tr class="uc_display display-uc-school">
					<th scope="row">At-Risk/ESL</th>
					<td>
						<fieldset>
							<label for="majority_at_risk"><input type="checkbox" name="majority_at_risk" id="majority_at_risk" value="1">Majority At-Risk</label><br>
							<label for="majority_esl"><input type="checkbox" name="majority_esl" id="majority_esl" value="1">Majority ESL</label>
						</fieldset>
					</td>
				</tr>
				<tr class="uc_display display-uc-teacher" style="display: none">
					<th scope="row">Teacher Attributes</th>
					<td>
						<fieldset>
							<label for="stem_proficient"><input type="checkbox" name="stem_proficient" id="stem_proficient" value="1">Proficient in STEM</label><br>
							<label for="stem_major"><input type="checkbox" name="stem_major" id="stem_major" value="1">Science/STEM Major</label><br>
							<label for="stem_ambassador"><input type="checkbox" name="stem_ambassador" id="stem_ambassador" value="1">STEM Playground Ambassador</label>
						</fieldset>
					</td>
				</tr>
				<tr class="uc_display display-uc-group display-uc-class display-uc-teacher" style="display: none">
					<th scope="row">At-Risk/ESL</th>
					<td>
						<fieldset>
							<label for="in_majority_at_risk"><input type="checkbox" name="in_majority_at_risk" id="in_majority_at_risk" value="1">In Majority At-Risk Schools</label><br>
							<label for="in_majority_esl"><input type="checkbox" name="in_majority_esl" id="in_majority_esl" value="1">In Majority ESL Schools</label>
						</fieldset>
					</td>
				</tr>
				<tr class="uc_display display-uc-student" style="display: none">
					<th scope="row">ESL</th>
					<td>
						<fieldset>
							<label for="esl_status"><input type="checkbox" name="esl_status" id="esl_status" value="1">Is ESL</label>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>
		';
	wp_nonce_field( 'get_reports', 'get_reports_noncename' );
	echo '
		<input type="hidden" name="action" value="admin_search">
		<p class="submit">
			<input type="submit" name="submit" class="button button-primary report-search" value="Search">
		</p>
	</form>
	<div class="search-results">
	</div>
</div>';
}

add_action( 'wp_ajax_get_geography', 'uc_get_geography_callback' );
// Used on Admin Reports page to update selects
function uc_get_geography_callback() {

	$response = array();

	$user_id = get_current_user_id();
	$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );

	if ( !wp_verify_nonce( $_POST['_wpnonce'], 'get_reports') ) { // Nonce check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'Did not pass security check';
	} elseif ( !current_user_can( 'edit_posts' ) ) { // User check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User does not have privileges';
	}

	if ( !isset( $response['status'] ) ) {

		if ( isset( $_POST['a'] ) && isset( $_POST['b'] ) && isset( $_POST['d'] ) ) {

			$response['status'] = 'OK';

			$type = sanitize_text_field( $_POST['a'] );
			$query = sanitize_text_field( $_POST['b'] );
			$querytype = sanitize_text_field( $_POST['d'] );

			switch ( $type ) {
				case 'state':
					$results = get_administrative_areas_level_1( $query );
					break;
				case 'city':
					$results = get_cities_by_state( $query );
					break;
				case 'county':
					$results = get_counties_by_state( $query );
					break;
				case 'school':
					$results = get_uc_schools_by_geography( array( array( 'type' => $querytype, 'value' => $query ) ) );
					break;
				default:
					break;
			}
			if ( count($results) )
				$response['results'] = $results;
			else
				$response['results'] = false;
		} else {
			$response['status'] = 'INVALID_REQUEST';
			$response['reason'] = 'Invalid request';
		}
	}
	echo json_encode( $response );
	wp_die();
}

add_action( 'wp_ajax_admin_search', 'uc_admin_search_callback' );
// Used on Admin Reports page to search
function uc_admin_search_callback() {
	$time_pre = microtime(true);
	$response = array();

	$user_id = get_current_user_id();
	$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );

	if ( !wp_verify_nonce( $_POST['_wpnonce'], 'get_reports') ) { // Nonce check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'Did not pass security check';
	} elseif ( !current_user_can( 'edit_posts' ) ) { // User check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User does not have privileges';
	}

	if ( !isset( $response['status'] ) ) {
		if ( isset( $_POST['a'] ) )
			$search_type = sanitize_text_field( $_POST['a'] );
		else {
			$response['status'] = 'INVALID_REQUEST';
			$response['reason'] = 'Invalid request';
		}
	}

	if ( !isset( $response['status'] ) ) {
		global $countries, $uc_genders;

		// Build the Geography array
		$geography_array = array();
		if ( isset( $_POST['country'] ) && strlen($_POST['country']) )
			$geography_array[] = array( 'type' => 'country', 'value' => sanitize_text_field( $_POST['country'] ) );
		if ( isset( $_POST['state'] ) && strlen($_POST['state']) )
			$geography_array[] = array( 'type' => 'state', 'value' => sanitize_text_field( $_POST['state'] ) );
		if ( isset( $_POST['city'] ) && strlen($_POST['city']) )
			$geography_array[] = array( 'type' => 'city', 'value' => sanitize_text_field( $_POST['city'] ) );
		if ( isset( $_POST['county'] ) && strlen($_POST['county']) )
			$geography_array[] = array( 'type' => 'county', 'value' => sanitize_text_field( $_POST['county'] ) );

		// Collect the other data
		if ( isset( $_POST['majority_at_risk'] ) && is_numeric($_POST['majority_at_risk']) )
			$majority_at_risk = intval( $_POST['majority_at_risk'] );
		if ( isset( $_POST['majority_esl'] ) && is_numeric($_POST['majority_esl']) )
			$majority_esl = intval( $_POST['majority_esl'] );
		if ( isset( $_POST['grade'] ) && strlen($_POST['grade']) && sanitize_text_field( $_POST['grade'] ) != 'all' )
			$grade = sanitize_text_field( $_POST['grade'] );
		if ( isset( $_POST['gender'] ) && strlen($_POST['gender']) && sanitize_text_field( $_POST['gender'] ) != 'all' )
			$gender = sanitize_text_field( $_POST['gender'] );
		if ( isset( $_POST['stem_proficient'] ) && is_numeric($_POST['stem_proficient']) )
			$stem_proficient = intval( $_POST['stem_proficient'] );
		if ( isset( $_POST['stem_major'] ) && is_numeric($_POST['stem_major']) )
			$stem_major = intval( $_POST['stem_major'] );
		if ( isset( $_POST['stem_ambassador'] ) && is_numeric($_POST['stem_ambassador']) )
			$stem_ambassador = intval( $_POST['stem_ambassador'] );
		if ( isset( $_POST['in_majority_at_risk'] ) && is_numeric($_POST['in_majority_at_risk']) )
			$in_majority_at_risk = intval( $_POST['in_majority_at_risk'] );
		if ( isset( $_POST['in_majority_esl'] ) && is_numeric($_POST['in_majority_esl']) )
			$in_majority_esl = intval( $_POST['in_majority_esl'] );
		if ( isset( $_POST['esl_status'] ) && is_numeric($_POST['esl_status']) )
			$esl_status = intval( $_POST['esl_status'] );
		if ( isset( $_POST['school_year'] ) && is_numeric($_POST['school_year']) && sanitize_text_field( $_POST['school_year'] ) != 'all' )
			$school_year = intval( $_POST['school_year'] );
		else
			$school_year = false;

		switch ( $search_type ) {
			case 'uc-school':
				$results = get_uc_schools_by_geography( $geography_array );
				$response['status'] = 'OK';
				$response['results'] = $results;
				$response['columns'] = array( 'School Name', 'Address', 'Address 2', 'City', 'County', 'State', 'Zip', 'Country', 'Majority At-Risk', 'Majority ESL', '# of Teachers', '# of Students' );
				$response['rows'] = array();
				foreach ( $results as $result ) {
					if ( !( $majority_at_risk && !is_school_at_risk( $result['value']) ) && !( $majority_esl && !is_school_ell( $result['value']) ) ) {
						
						$response['rows'][] = array(
							array( 'text' => $result['label'], 'link' => get_edit_post_link($result['value'] ) ),
							array( 'text' => get_post_meta( $result['value'], 'streetaddress', true ) ),
							array( 'text' => get_post_meta( $result['value'], 'streetaddress2', true ) ),
							array( 'text' => get_post_meta( $result['value'], 'city', true ) ),
							array( 'text' => get_post_meta( $result['value'], 'county', true ) ),
							array( 'text' => get_post_meta( $result['value'], 'state', true ) ),
							array( 'text' => get_post_meta( $result['value'], 'zip', true ) ),
							array( 'text' => $countries[get_post_meta( $result['value'], 'country', true )] ),
							array( 'text' => is_school_at_risk( $result['value']) ? "Yes" : "No" ),
							array( 'text' => is_school_ell( $result['value']) ? "Yes" : "No" ),
							array( 'text' => count(get_uc_teachers_by_school( $result['value'] )) ),
							array( 'text' => count(get_uc_students_by_school( $result['value'] )) ),
						);
					}
				}
				break;
			case 'uc-teacher':
				$results = get_uc_teachers_by_geography( $geography_array );
				$activities = get_uc_activities();
				$response['status'] = 'OK';
				$response['results'] = $results;
				$response['columns'] = array( 'Teacher Name', 'E-Mail Address', 'STEM Playground Ambassador', 'School Name', 'Address', 'Address 2', 'City', 'County', 'State', 'Zip', 'Country', 'Majority At-Risk', 'Majority ESL', 'Class Name', 'Grade' );
				$response['rows'] = array();
				$classes = array();
				foreach ( $results as $key => $result ) {
					$school_id = get_post_meta( $result['value'], 'school', true );
					if (( isset($grade) && false ) ||
						( isset($gender) && !(get_post_meta( $result['value'], 'gender', true ) == $gender ) ) ||
						( $stem_proficient && !get_post_meta( $result['value'], 'stem_proficient', true ) ) ||
						( $stem_major && !get_post_meta( $result['value'], 'college_science', true ) ) ||
						( $stem_ambassador && !get_post_meta( $result['value'], 'ambassador', true ) ) ||
						( $in_majority_at_risk && !is_school_at_risk( $school_id ) ) ||
						( $in_majority_esl && !is_school_ell( $school_id ) ) ) {

						unset( $results[$key] );
					} else {
						$results[$key]['classes'] = get_uc_classes_by_teacher( $result['value'], 'publish', $school_year );
						if ( isset($grade )) {
							foreach ( $results[$key]['classes'] as $class_key => $class ) {
								if ( get_post_meta( $class['value'], 'grade', true ) != $grade )
									unset( $results[$key]['classes'][$class_key] );
							}
						}
						$classes = array_merge( $classes, $results[$key]['classes'] );
					}
				}

				foreach( $activities as $key => $activity ) {
					$response['columns'][] = 'Rank for '.$activity['label'];
					$activities[$key]['rankings'] = get_class_rankings( $classes, $activity['value'], 'type2', 'publish', $school_year );
				}
				$response['columns'][] = 'Average Rank';

				$response['classes'] = $classes;

				foreach ( $results as $result ) {
					$school_id = get_post_meta( $result['value'], 'school', true );
					$teacher_name = get_post_meta( $result['value'], 'last_name', true ) . ', ' . get_post_meta( $result['value'], 'first_name', true );
					$i = 0;

					if ( count($result['classes']) ) {
						foreach ( $result['classes'] as $class ) {
							$class_grade = get_post_meta( $class['value'], 'grade', true );
							$class_grade_output = ( $class_grade ? $class_grade : 'Various' );
							$next_row = array(
								array( 'text' => $teacher_name, 'link' => get_edit_post_link($result['value'] ) ),
								array( 'text' => get_post_meta( $result['value'], 'email', true ) ),
								array( 'text' => get_post_meta( $result['value'], 'ambassador', true ) ? 'Yes' : 'No' ),
								array( 'text' => get_the_title( $school_id ), 'link' => get_edit_post_link( $school_id ) ),
								array( 'text' => get_post_meta( $school_id, 'streetaddress', true ) ),
								array( 'text' => get_post_meta( $school_id, 'streetaddress2', true ) ),
								array( 'text' => get_post_meta( $school_id, 'city', true ) ),
								array( 'text' => get_post_meta( $school_id, 'county', true ) ),
								array( 'text' => get_post_meta( $school_id, 'state', true ) ),
								array( 'text' => get_post_meta( $school_id, 'zip', true ) ),
								array( 'text' => $countries[get_post_meta( $school_id, 'country', true )] ),
								array( 'text' => is_school_at_risk( $school_id ) ? "Yes" : "No" ),
								array( 'text' => is_school_ell( $school_id ) ? "Yes" : "No" ),
								array( 'text' => $class['label'], 'link' => get_edit_post_link( $class['value'] ) ),
								array( 'text' => $class_grade_output, )
							);

							$rank_sum = 0;

							foreach( $activities as $activity ) {
								$next_row[] = array( 'text' => $activity['rankings'][$class['value']]['rank'] );
								$rank_sum += $activity['rankings'][$class['value']]['rank'];
							}

							$next_row[] = array( 'text' => $rank_sum/count($activities) );
							$response['rows'][] = $next_row;
						}
					} else {
						$next_row = array(
							array( 'text' => $result['label'], 'link' => get_edit_post_link($result['value'] ) ),
							array( 'text' => get_post_meta( $result['value'], 'email', true ) ),
							array( 'text' => get_post_meta( $result['value'], 'ambassador', true ) ? 'Yes' : 'No' ),
							array( 'text' => get_the_title( $school_id ), 'link' => get_edit_post_link( $school_id ) ),
							array( 'text' => get_post_meta( $school_id, 'streetaddress', true ) ),
							array( 'text' => get_post_meta( $school_id, 'streetaddress2', true ) ),
							array( 'text' => get_post_meta( $school_id, 'city', true ) ),
							array( 'text' => get_post_meta( $school_id, 'county', true ) ),
							array( 'text' => get_post_meta( $school_id, 'state', true ) ),
							array( 'text' => get_post_meta( $school_id, 'zip', true ) ),
							array( 'text' => $countries[get_post_meta( $school_id, 'country', true )] ),
							array( 'text' => is_school_at_risk( $result['value']) ? "Yes" : "No" ),
							array( 'text' => is_school_ell( $result['value']) ? "Yes" : "No" ),
							array( 'text' => '' ),
							array( 'text' => '' )
						);

						foreach( $activities as $activity )
							$next_row[] = array( 'text' => '' );

						$next_row[] = array( 'text' => '' );
						$response['rows'][] = $next_row;
					}
				}
				break;
			case 'uc-class':
				$results = get_uc_classes_by_geography( $geography_array );
				$activities = get_uc_activities();
				$response['status'] = 'OK';
				$response['columns'] = array( 'Class Name', 'Teacher Name', 'E-Mail Address', 'Grade', 'School', 'City', 'County', 'State', 'Zip', 'Country', 'Majority At-Risk', 'Majority ESL', '# of Activities Completed' );
				$response['rows'] = array();
				foreach ( $results as $key => $result ) {
					$teacher_id = get_post_meta( $result['value'], 'teacher', true );
					$school_id = get_post_meta( $teacher_id, 'school', true );
					$class_grade = get_post_meta( $result['value'], 'grade', true );
					$class_grade_title = ( get_term_by('slug', $class_grade, 'uc_grade') ? get_term_by('slug', $class_grade, 'uc_grade')->name : 'Various' );
					if (( $school_year && !($school_year == get_school_year_of_post($result['value'])) ) ||
						( isset($grade) && !($grade == $class_grade) ) ||
						( $in_majority_at_risk && !is_school_at_risk( $school_id ) ) || 
						( $in_majority_esl && !is_school_ell( $school_id ) ) ) {

						unset( $results[$key] );
					}
				}

				foreach( $activities as $key => $activity ) {
					$response['columns'][] = 'Rank for '.$activity['label'];
					$activities[$key]['rankings'] = get_class_rankings( $results, $activity['value'], 'type2', 'publish', $school_year );
				}
				$response['columns'][] = 'Average Rank';

				$response['rankings'] = $activities;

				foreach ( $results as $result ) {
					$teacher_id = get_post_meta( $result['value'], 'teacher', true );
					$school_id = get_post_meta( $teacher_id, 'school', true );

					$teacher_name = get_post_meta( $teacher_id, 'last_name', true ) . ', ' . get_post_meta( $teacher_id, 'first_name', true );
					$next_row = array(
						array( 'text' => $result['label'], 'link' => get_edit_post_link( $result['value'] ) ),
						array( 'text' => $teacher_name, 'link' => get_edit_post_link( $teacher_id ) ),
						array( 'text' => get_post_meta( $teacher_id, 'email', true ) ),
						array( 'text' => $class_grade_title ),
						array( 'text' => get_the_title($school_id), 'link' => get_edit_post_link( $school_id ) ),
						array( 'text' => get_post_meta( $school_id, 'city', true ) ),
						array( 'text' => get_post_meta( $school_id, 'county', true ) ),
						array( 'text' => get_post_meta( $school_id, 'state', true ) ),
						array( 'text' => get_post_meta( $school_id, 'zip', true ) ),
						array( 'text' => $countries[get_post_meta( $school_id, 'country', true )] ),
						array( 'text' => is_school_at_risk( $school_id ) ? "Yes" : "No" ),
						array( 'text' => is_school_ell( $school_id ) ? "Yes" : "No" ),
						array( 'text' => count( get_uc_signups_by_class( $result['value'], 'publish', 'completed' ) ) ),
					);

					$rank_sum = 0;
					foreach( $activities as $activity ) {
						$next_row[] = array( 'text' => $activity['rankings'][$result['value']]['rank'] );
						$rank_sum += $activity['rankings'][$result['value']]['rank'];
					}

					$next_row[] = array( 'text' => $rank_sum/count($activities) );

					$response['rows'][] = $next_row;
				}
				break;
			case 'uc-group':
				$results = get_uc_groups_by_geography( $geography_array );
				$response['status'] = 'OK';
				$response['columns'] = array( 'Team ID', 'Teacher Name', 'Grade', 'School', 'City', 'State', 'Country', 'Activity', 'Signup Status', 'Score' );
				$group_size = vp_option('uc_option.group_size');
				for ($i=0; $i <= $group_size; $i++) { 
					$response['columns'][] = 'Student '.($i+1);
				}
				$response['rows'] = array();
				foreach ( $results as $result ) {
					$class_id = get_post_meta( $result['value'], 'class', true );
					$teacher_id = get_post_meta( $result['value'], 'teacher', true );
					$teacher_name = get_post_meta( $teacher_id, 'last_name', true ) . ', ' . get_post_meta( $teacher_id, 'first_name', true );
					$school_id = get_post_meta( $teacher_id, 'school', true );
					$group_grade = get_post_meta( $class_id, 'grade', true );
					
					if (!( $school_year && !($school_year == get_school_year_of_post($result['value'])) ) && 
						!( isset($grade) && !($grade == $group_grade) ) && 
						!( $in_majority_at_risk && !is_school_at_risk( $school_id ) ) && 
						!( $in_majority_esl && !is_school_ell( $school_id ) ) ) {

						$group_grade_title = ( get_term_by('slug', $group_grade, 'uc_grade') ? get_term_by('slug', $group_grade, 'uc_grade')->name : 'Various' );
						$signup_id = get_post_meta( $result['value'], 'signup', true );
						$activity_id = get_post_meta( $signup_id, 'activity', true );
						$group_score = get_post_meta( $result['value'], 'score', true );
						$teamwork_score = get_post_meta( $result['value'], 'teamwork_score', true );
						$final_score = calculate_final_score( $group_score, $teamwork_score, $activity_id );

						$next_row = array(
							array( 'text' => $result['value'], 'link' => get_edit_post_link($result['value'] ) ),
							array( 'text' => $teacher_name, 'link' => get_edit_post_link( $teacher_id ) ),
							array( 'text' => $group_grade_title ),
							array( 'text' => get_the_title($school_id), 'link' => get_edit_post_link( $school_id ) ),
							array( 'text' => get_post_meta( $school_id, 'city', true ) ),
							array( 'text' => get_post_meta( $school_id, 'state', true ) ),
							array( 'text' => $countries[get_post_meta( $school_id, 'country', true )] ),
							array( 'text' => get_the_title($activity_id), 'link' => get_edit_post_link( $activity_id ) ),
							array( 'text' => signup_status_of_signup( $signup_id ) ),
							array( 'text' => ( $final_score ? $final_score : 'None' ) ),
						);

						$students = get_post_meta( $result['value'], 'students', true );

						for ($i=0; $i <= $group_size; $i++) { 
							if ( $students[$i] )
								$next_row[] = array( 'text' => get_student_name($students[$i]), 'link' => get_edit_post_link( $students[$i] ) );
							else
								$next_row[] = array( 'text' => '' );
						}
						$response['rows'][] = $next_row;
					}
				}
				break;
			case 'uc-student':
				$results = get_uc_students_by_geography( $geography_array );
				$activities = get_uc_activities();
				$response['status'] = 'OK';
				$response['columns'] = array( 'Name', 'Grade', 'Gender', 'Teacher', 'Class', 'School', 'City', 'County', 'State', 'Zip', 'Country', 'In At-Risk School', 'ESL' );
				$response['rows'] = array();
				foreach ( $results as $key => $result ) {
					$student_gender = get_post_meta( $result['value'], 'gender', true );
					$student_grade = get_post_meta( $result['value'], 'grade', true );
					$student_grade_title = ( get_term_by('slug', $student_grade, 'uc_grade') ? get_term_by('slug', $student_grade, 'uc_grade')->name : 'Various' );
					$student_ell = get_post_meta( $result['value'], 'ell', true );
					if (( $school_year && !($school_year == get_school_year_of_post($result['value'])) ) ||
						( isset($grade) && !( $student_grade == $grade ) ) ||
						( isset($gender) && !( $student_gender == $gender ) ) ||
						( $esl_status && $student_ell != 1 ) ){

						unset( $results[$key] );
					}
				}

				foreach( $activities as $key => $activity ) {
					$groups = array();
					foreach ( $results as $key2 => $result ) {
						$student_group = get_group_of_student( $result['value'], $activity['value'] );
						$results[$key2]['group'][$activity['value']] = $student_group;
						if ( $student_group )
							$groups[] = $student_group;
					}
					
					$response['columns'][] = 'Rank for '.$activity['label'];
					$activities[$key]['rankings'] = get_group_rankings( $groups, $activity['value'], 'type2' );
				}
				$response['columns'][] = 'Average Rank';

				$response['activities'] = $activities;

				foreach ( $results as $result ) {
					$teacher_id = get_post_meta( $result['value'], 'teacher', true );
					$teacher_name = get_post_meta( $teacher_id, 'last_name', true ) . ', ' . get_post_meta( $teacher_id, 'first_name', true );
					$class_id = get_post_meta( $result['value'], 'class', true );
					$school_id = get_post_meta( $teacher_id, 'school', true );
					$next_row = array(
						array( 'text' => $result['label'], 'link' => get_edit_post_link( $result['value'] ) ),
						array( 'text' => $student_grade_title ),
						array( 'text' => $uc_genders[$student_gender] ),
						array( 'text' => $teacher_name, 'link' => get_edit_post_link( $teacher_id ) ),
						array( 'text' => get_the_title($class_id), 'link' => get_edit_post_link( $class_id ) ),
						array( 'text' => get_the_title($school_id), 'link' => get_edit_post_link( $school_id ) ),
						array( 'text' => get_post_meta( $school_id, 'city', true ) ),
						array( 'text' => get_post_meta( $school_id, 'county', true ) ),
						array( 'text' => get_post_meta( $school_id, 'state', true ) ),
						array( 'text' => get_post_meta( $school_id, 'zip', true ) ),
						array( 'text' => $countries[get_post_meta( $school_id, 'country', true )] ),
						array( 'text' => is_school_at_risk( $school_id ) ? "Yes" : "No" ),
						array( 'text' => $student_ell == 1 ? "Yes" : "No" ),
					);

					$rank_sum = 0;
					foreach( $activities as $activity ) {
						$student_group = $result['group'][$activity['value']];
						if ( $student_group )
							$this_rank = $activity['rankings'][$student_group['value']]['rank'];
						else
							$this_rank = count($results);

						$next_row[] = array( 'text' => $this_rank );
						$rank_sum += $this_rank;
					}
					$next_row[] = array( 'text' => $rank_sum/count($activities) );
						
					$response['rows'][] = $next_row;
				}
				break;
			default:
				$response['status'] = 'INVALID_REQUEST';
				$response['reason'] = 'Invalid request';
				break;
		}
	}
	$time_post = microtime(true);
	$response['time'] = $time_post - $time_pre;
	echo json_encode( $response );
	wp_die();
}



 ?>