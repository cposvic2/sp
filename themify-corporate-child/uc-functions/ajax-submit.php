<?php 

function hook_javascript() {
	echo '
	<script type="text/javascript">
		var ajaxurl = "'. admin_url('admin-ajax.php') .'";
	</script>';
}
add_action('wp_head','hook_javascript');


add_action( 'wp_ajax_get_team_datapoints', 'uc_get_team_datapoints_callback' );
// Used on Activity page to get team results used for charts
function uc_get_team_datapoints_callback() {
	global $administrative_area_level_1_array, $countries;
	$user_id = get_current_user_id();
	$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );

	if ( !wp_verify_nonce( $_POST['_wpnonce'], 'update_results') ) { // Nonce check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'Did not pass security check';
	} elseif ( !$teacher_id ) { // Teacher check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User error';
	} elseif ( !current_user_can('edit_activities') ) { // User check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User does not have edit privileges';
	}

	if ( !isset( $response['status'] ) ) {
		if ( ( isset( $_POST['gid'] ) && intval( $_POST['gid'] ) ) ) {
			$group_id = intval( $_POST['gid'] );
			if ( !is_post_of_teacher( $group_id, $teacher_id, 'uc_group' ) ) { // Is this a class of the teacher?
				$response['status'] = 'REQUEST_DENIED';
				$response['reason'] = 'Not group of user';
			}
		} else {
			$response['status'] = 'INVALID_REQUEST';
			$response['reason'] = 'Invalid request';
		}
	}

	if ( !isset( $response['status'] ) ) {

		// Make Team datapoints
		$signup_id = get_post_meta( $group_id, 'signup', true );
		$activity_id = get_post_meta( $signup_id, 'activity', true );
		$score = get_post_meta( $group_id, 'score', true );
		$teamwork_score = get_post_meta( $group_id, 'teamwork_score', true );
		$group_score = calculate_final_score( $score, $teamwork_score, $activity_id );
		$group_title = get_the_title( $group_id );
		$class_id = get_post_meta( $group_id, 'class', true );
		$school_id = get_post_meta( $teacher_id, 'school', true );
		$grade = get_post_meta( $class_id, 'grade', true );
		$current_school_year = get_current_school_year();

		$datapoints = array();

		$i = 0;
		$team_text = $group_title.': '.$group_score.'<br>'.get_group_student_names( $group_id );
		
		$datapoints[] = array(
			'y' => $group_score,
			'label' => $group_title,
			'text' => $team_text
		);

		$geographic_searches = array( 
			'school' => array( 'value' => get_the_title( $school_id ), 'label' => 'school' ),
			'city' => array( 'value' => get_post_meta( $school_id, 'city', true ), 'label' => 'city' ),
			'county' => array( 'value' => get_post_meta( $school_id, 'county', true ), 'label' => 'county' ),
			'state' => array( 'value' => get_post_meta( $school_id, 'state', true ), 'label' => $administrative_area_level_1_array[get_post_meta( $school_id, 'country', true )]['name'] ),
			'country' => array( 'value' => get_post_meta( $school_id, 'country', true ), 'label' => 'country' ),
			'global' => array( 'value' => 'global', 'label' => 'Global' ),
		);

		foreach ( $geographic_searches as $key => $geographic_search ) {
			if ( $geographic_search['value'] )
				$class_results[$key] = get_group_results_by_demographic( 'standard', $key, $school_id, $activity_id, $class_id, $group_id, $grade, 10, 'publish', $current_school_year );
		}
		$response['test'] = $class_results;

		foreach ( $class_results as $key => $class_result ) {

			if ( $key == 'global' ) {
				$label = $geographic_searches[$key]['label'];
				$end_text = ' globally';
			} else {
				$label = 'My ' .ucfirst( $geographic_searches[$key]['label'] );
				$end_text = ' in our '.$geographic_searches[$key]['label'];
			}

			$class_text = $group_title .' Score: '.$group_score.'<br>'
				.ucfirst($label)
				.' Average: '
				.$class_result["average"]
				.'<br>We were #'
				.$class_result["place"]
				.' of '.$class_result["total"]
				.' '
				.( $class_result["total"] > 1 ? 'teams' : 'team' )
				.$end_text;

			$j = 0;
			$class_text .= '<br>The top performing teams came from the following schools:<br>';

			while ( $j < count($class_result['top_results']) ) {
				$this_school_country_value = get_post_meta( $class_result['top_results'][$j]['id'], 'country', true );
				$this_school_state = $administrative_area_level_1_array[$this_school_country_value]['values'][get_post_meta( $class_result['top_results'][$j]['id'], 'state', true )];
				$this_school_country = $countries[$this_school_country_value];
				$class_text .=  '#'.($j+1).' '.$class_result['top_results'][$j]['label'] . ( $this_school_state ? ', '.$this_school_state : '' ).', '.$this_school_country;
				$j++;
				if ( $j < count($class_result['top_results']) )
					$class_text .= '<br>';
			}

			$datapoints[] = array(
				'y' => $class_result["average"],
				'label' => $label,
				'text' => $class_text
			);
		}
		// End Make Team datapoints

		$response['status'] = 'OK';
		$response['team_datapoints'] = $datapoints;
	}
	echo json_encode( $response );
	wp_die();
}

add_action( 'wp_ajax_get_schools', 'uc_get_schools_callback' );
// Used on Schools page to get schools of zip code
function uc_get_schools_callback() {

	$response = array();

	$user_id = get_current_user_id();
	$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );

	if ( !wp_verify_nonce( $_POST['_wpnonce'], 'update_school') ) { // Nonce check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'Did not pass security check';
	} elseif ( !$teacher_id ) { // Teacher check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User error';
	} elseif ( !current_user_can('edit_school') ) { // User check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User does not have edit privileges';
	}

	if ( !isset( $response['status'] ) ) {
		global $countries, $administrative_area_level_1_array;

		if ( ( isset( $_POST['zip'] ) && intval( $_POST['zip'] ) && isset( $_POST['country'] ) && array_key_exists($_POST['country'], $countries) ) ) {
			$zip = intval( $_POST['zip'] );
			$country = sanitize_text_field( $_POST['country'] );

			$schools = get_uc_schools_by_zip_code_and_country( $zip, $country );

			$sc_search_results = vp_option('uc_option.sc_search_results');
			$sc_search_result = vp_option('uc_option.sc_search_result');
			$sc_search_result_0 = vp_option('uc_option.sc_search_result_0');

			$response['status'] = 'OK';
			$response['count'] = count( $schools );

			if ( array_key_exists( $country, $administrative_area_level_1_array ) ) {
				$response['admin_level_1'] = ucfirst($administrative_area_level_1_array[$country]['name']);
				$response['admin_level_1_values'] = $administrative_area_level_1_array[$country]['values'];
			}

			if ( count( $schools) ) {
				if ( count( $schools ) == 1 )
					$response['response'] = $sc_search_result;
				else
					$response['response'] = str_replace('!number!', count( $schools), $sc_search_results);

				$response['count'] = count( $schools );
				$response['results'] = $schools ;

			} else {
				$response['response'] = $sc_search_result_0;
			}
		} else {
			$response['status'] = 'INVALID_REQUEST';
			$response['reason'] = 'Invalid request';
		}
	}
	echo json_encode( $response );
	wp_die();
}

add_action( 'wp_ajax_update_class', 'uc_update_class_callback' );
// Used when adding or updating class information
function uc_update_class_callback() {

	$response = array();

	$user_id = get_current_user_id();
	$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );

	if ( !wp_verify_nonce( $_POST['_wpnonce'], 'update_classes') ) { // Nonce check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'Did not pass security check';
	} elseif ( !$teacher_id ) { // Teacher check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User error';
	} elseif ( !current_user_can('edit_class') ) { // User check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User does not have edit privileges';
	}

	if ( !isset( $response['status'] ) ) {
		if ( isset( $_POST['classid'] ) ) {
			// This is an update

			// Check the class ID
			if ( !is_numeric( $_POST['classid'] ) )
				$response['status'] = 'INVALID_REQUEST';
			else {
				$class_id = intval( $_POST['classid'] );

				// Check whether this is teacher's class
				if ( !is_post_of_teacher( $class_id, $teacher_id, 'uc_class' ) ) {
					$response['status'] = 'REQUEST_DENIED';
					$response['reason'] = 'Not class of user';
				}
			}

			if ( !isset( $response['status'] ) ) { // Check any inputs
				if ( strlen( $_POST['classname'] ) || isset( $_POST['classgrade'] ) ) {
					if ( strlen( $_POST['classname'] ) )
						$classname = sanitize_text_field( $_POST['classname'] );
					if ( isset( $_POST['classgrade'] ) )
						$classgrade = sanitize_text_field( $_POST['classgrade'] );
				} else
					$response['status'] = 'INVALID_REQUEST';
			}

		} else {
			// This is a new class

			// Check max class size
			$max_classes = vp_option('uc_option.class_maximum');
			$current_school_year = get_current_school_year();
			$classes = get_uc_classes_by_teacher( $teacher_id, 'publish', $current_school_year );
			if ( $max_classes && count($classes) >= $max_classes ) {
				$class_max_error = vp_option('uc_option.class_max_error');
				$response['status'] = 'REQUEST_DENIED';
				$response['reason'] = str_replace( '!maximum!',$max_classes , $class_max_error);
			}

			if ( !isset( $response['status'] ) ) { // Check all inputs

				// Required
				if ( strlen( $_POST['classname'] ) )
					$classname = sanitize_text_field( $_POST['classname'] );
				else
					$response['status'] = 'INVALID_REQUEST';

				// Optional
				if ( strlen( $_POST['classgrade'] ) )
					$classgrade = sanitize_text_field( $_POST['classgrade'] );
			}
		}
	}

	if ( !isset( $response['status']  ) ) { // POST data is verified

		// Check previous class names
		if ( isset( $classname ) ) {
			$classes = get_uc_classes_by_teacher( $teacher_id ); // All classes, not just this school year
			foreach ( $classes as $class ) {
				if ( $class['value']!= $class_id && $class['label'] == str_replace('\\','',$classname) ) {
					$class_same_name_error = vp_option('uc_option.class_same_name_error');
					$response['status'] = 'REQUEST_DENIED';
					$response['reason'] = $class_same_name_error;
				}
			}
		}

		if ( !isset( $response['status'] ) ) {

			if ( !isset( $class_id ) ) { // Make new post

				$new_class = array (
					'post_type' => 'uc_class',
					'post_author' => $user_id,
					'post_status' => 'publish',
				);
				$class_id = wp_insert_post( $new_class );

				if ( !$class_id )
					$response['status'] = 'UNKNOWN_ERROR';
				else {
					$response['new'] = $class_id;
					update_post_meta( $class_id, 'teacher', $teacher_id );
				}
			} else {
				$response['updated'] = $class_id;
			}

			global $uc_class_fields;

			update_post_meta( $class_id, 'uc_class_fields', $uc_class_fields );

			// Update class name
			if ( isset( $classname) ) {
				$updated_post = array(
					'ID'           => $class_id,
					'post_title'   => $classname,
				);
				wp_update_post( $updated_post );

				$response['meta']['classname'] = $classname;
			}

			if ( isset($classgrade) ) {
				update_post_meta( $class_id, 'grade', $classgrade );
				$grades = get_uc_grades();
				$response['meta']['classgrade'] = ( $classgrade ? $classgrade : 'Various' );
			} elseif ( isset( $new_class ) )
				$response['meta']['classgrade'] = 'Various';

			// Reset ELL check
			$school_id = get_post_meta( $teacher_id, 'school', true );
			update_post_meta( $school_id, 'ell_updated', 0 );

			// Give next cap
			if ( !current_user_can( 'edit_students' ) ) {
				$user = wp_get_current_user();
				$user->add_cap( 'edit_students' );
				// Add email reminders too since this is their first time creating a class
				uc_should_schedule_email_no_activity( $class_id );
			}
			// Since this is an AJAX update, give proceed
			global $highest_capability_pages;

			$pages = get_pages( array(
				'meta_key' => '_wp_page_template',
				'meta_value' => $highest_capability_pages['edit_students']
			));
			if ( $pages )
				$proceed_url = get_permalink( $pages[0] );
			else
				$proceed_url = home_url();

			$response['proceed']['url'] = $proceed_url;
			$response['proceed']['text'] = vp_option('uc_option.class_proceed');;

			$response['status'] = 'OK';
		}
	}
	echo json_encode( $response );
	wp_die();
}

add_action( 'wp_ajax_update_student', 'uc_update_student_callback' );
// Used when adding or updating student information
function uc_update_student_callback() {

	$response = array();

	$user_id = get_current_user_id();
	$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );

	if ( !wp_verify_nonce( $_POST['_wpnonce'], 'update_students') ) { // Nonce check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'Did not pass security check';
	} elseif ( !$teacher_id ) { // Teacher check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User error';
	} elseif ( !current_user_can('edit_students') ) { // User check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User does not have edit privileges';
	}


	if ( !isset( $response['status'] ) ) {
		if ( isset( $_POST['studentid'] ) ) {
			// This is an update

			// Check the student ID
			if ( !is_numeric( $_POST['studentid'] ) ) {
				$response['status'] = 'INVALID_REQUEST';
				$response['reason'] = 'Student ID is invalid';
			} else {
				$student_id = intval( $_POST['studentid'] );

				// Check whether this is teacher's student
				if ( !is_post_of_teacher( $student_id, $teacher_id, 'uc_student' ) ) {
					$response['status'] = 'REQUEST_DENIED';
					$response['reason'] = 'Not student of user';
				}
			}

			if ( !isset( $response['status'] ) ) { // Check any inputs

				if ( strlen( $_POST['stfirstname'] ) || strlen( $_POST['stlastinitial'] ) >= 0 || strlen( $_POST['stgrade'] ) || is_numeric( $_POST['stclass'] ) || strlen( $_POST['stgender'] ) ||  isset( $_POST['stell'] ) ) {
					if ( strlen( $_POST['stfirstname'] ) )
						$firstname = sanitize_text_field( $_POST['stfirstname'] );
					if ( strlen( $_POST['stlastinitial'] ) >= 0 )
						$lastinitial = substr( sanitize_text_field( $_POST['stlastinitial'] ), 0, 1);
					if ( strlen( $_POST['stgrade'] ) )
						$grade = sanitize_text_field( $_POST['stgrade'] );
					if ( is_numeric( $_POST['stclass'] ) )
						$class_id = intval( $_POST['stclass'] );
					if ( strlen( $_POST['stgender'] ) )
						$gender = sanitize_text_field( $_POST['stgender'] );
					if ( isset( $_POST['stell'] ) )
						$ell = sanitize_text_field( $_POST['stell'] );

				} else {
					$response['status'] = 'INVALID_REQUEST';
					$response['reason'] = 'Request is invalid';
				}
			}

		} else {
			// This is a new class

			// Perform any future checks here

			if ( !isset( $response['status'] ) ) { // Check all inputs

				// Required
				if ( strlen( $_POST['stfirstname'] ) )
					$firstname = sanitize_text_field( $_POST['stfirstname'] );
				else
					$response['status'] = 'INVALID_REQUEST';

				// Required
				if ( strlen( $_POST['stlastinitial'] ) >= 0 )
					$lastinitial = substr( sanitize_text_field( $_POST['stlastinitial'] ), 0, 1);
				else
					$response['status'] = 'INVALID_REQUEST';

				// Required
				if ( strlen( $_POST['stgrade'] ) )
					$grade = sanitize_text_field( $_POST['stgrade'] );
				else
					$response['status'] = 'INVALID_REQUEST';

				// Required
				if ( is_numeric( $_POST['stclass'] ) )
					$class_id = intval( $_POST['stclass'] );
				else
					$response['status'] = 'INVALID_REQUEST';

				// Required
				if ( strlen( $_POST['stgender'] ) )
					$gender = sanitize_text_field( $_POST['stgender'] );
				else
					$response['status'] = 'INVALID_REQUEST';

				// Optional
				if ( isset( $_POST['stell'] ) )
					$ell = sanitize_text_field( $_POST['stell'] );


			}
		}
	}

	if ( !isset( $response['status']  ) ) { // POST data is verified

		// Check whether class is teacher's class
		if ( isset($class_id) && !is_post_of_teacher( $class_id, $teacher_id, 'uc_class' ) ) {
			$response['status'] = 'REQUEST_DENIED';
			$response['reason'] = 'Not class of user';
		}
		$st_max_students = vp_option('uc_option.st_max_students');
		$current_school_year = get_current_school_year();
		$existing_students = get_uc_students_by_class( $class_id, 'publish', $current_school_year );
		if ( $st_max_students && count( $existing_students ) >= $st_max_students ) {
			$st_max_students_text = vp_option('uc_option.st_max_students_text');
			$st_max_students_text = str_replace( '!students!', $st_max_students , $st_max_students_text);
			$response['status'] = 'REQUEST_DENIED';
			$response['reason'] = $st_max_students_text;
		}

		if ( !isset( $response['status'] ) ) {

			if ( !isset( $student_id ) ) { // Make new post

				$new_student = array (
					'post_type' => 'uc_student',
					'post_author' => $user_id,
					'post_status' => 'publish',
				);
				$student_id = wp_insert_post( $new_student );

				if ( !$student_id )
					$response['status'] = 'UNKNOWN_ERROR';
				else {
					$response['new'] = $student_id;
					update_post_meta( $student_id, 'teacher', $teacher_id );
				}
			} else {
				$response['updated'] = $student_id;
			}

			global $uc_student_fields;

			update_post_meta( $student_id, 'uc_student_fields', $uc_student_fields );

			if ( isset($firstname) ) {
				update_post_meta( $student_id, 'firstname', $firstname );
				$response['meta']['firstname'] = $firstname;
			}
			if ( isset($lastinitial) ) {
				update_post_meta( $student_id, 'lastinitial', $lastinitial );
				$response['meta']['lastinitial'] = $lastinitial;
			}
			if ( isset($grade) ) {
				update_post_meta( $student_id, 'grade', $grade );
				$response['meta']['grade'] = $grade;
			}
			if ( isset($class_id) ) {
				update_post_meta( $student_id, 'class', $class_id );
				$response['meta']['class_id'] = $class_id;
			}
			if ( isset($gender) ) {
				global $uc_genders;
				update_post_meta( $student_id, 'gender', $gender );
				$response['meta']['gender'] = $uc_genders[$gender];
			}
			if ( isset($ell) ) {
				global $uc_yesno;
				update_post_meta( $student_id, 'ell', $ell );
				$response['meta']['ell'] = $uc_yesno[$ell];
			}

			// Reset ELL check
			$school_id = get_post_meta( $class_id, 'school', true );
			update_post_meta( $class_id, 'ell_updated', 0 );
			update_post_meta( $school_id, 'ell_updated', 0 );

			// Give next cap
			if ( !current_user_can( 'edit_groups' ) ) {
				$user = wp_get_current_user();
				$user->add_cap( 'edit_groups' );
			}
			// Since this is an AJAX update, give proceed
			global $highest_capability_pages;

			$pages = get_pages( array(
				'meta_key' => '_wp_page_template',
				'meta_value' => $highest_capability_pages['edit_groups']
			));
			if ( $pages )
				$proceed_url = get_permalink( $pages[0] );
			else
				$proceed_url = home_url();
				
			if (count(get_uc_students_by_teacher( $teacher_id )) >= 2) {
				$response['proceed']['url'] = $proceed_url;
				$response['proceed']['text'] = vp_option('uc_option.st_proceed');
			}

			$number_of_students = count(get_uc_students_by_class( $class_id, 'publish', $current_school_year ));
			$group_size = vp_option('uc_option.group_size');
			$response['status'] = 'OK';
			$response['count'] = $number_of_students;
			$response['above_min'] = $number_of_students > $group_size ? true : false;

		}
	}
	echo json_encode( $response );
	wp_die();
}


add_action( 'wp_ajax_remove_post', 'uc_remove_post_callback' );
// Used when adding or updating student information
function uc_remove_post_callback() {

	// Nonce check
	check_ajax_referer( 'remove_post' );

	$user_id = get_current_user_id();
	$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
	if ( !$teacher_id ) {
		echo ('-1');
		wp_die();
	}

	if ( isset( $_POST['postid'] ) && $_POST['postid'] ) {
		if ( is_numeric( $_POST['postid'] ) ) {
			$postid = intval( $_POST['postid'] );

			if ( !is_post_of_teacher( $postid, $teacher_id ) ) {
				echo ('-1');
				wp_die();
			}

			wp_trash_post( $postid );
			echo $postid;
		} else {
			echo '-1';
		}
	} else {
		echo '-1';
	}
	wp_die();
}

add_action( 'wp_ajax_update_groups', 'uc_update_groups_callback' );
// Used when adding or updating groups
function uc_update_groups_callback() {

	$response = array();

	$user_id = get_current_user_id();
	$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );

	if ( !wp_verify_nonce( $_POST['_wpnonce'], 'update_groups') ) { // Nonce check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'Did not pass security check';
	} elseif ( !$teacher_id ) { // Teacher check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User error';
	} elseif ( !current_user_can('edit_groups') ) { // User check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User does not have edit privileges';
	}

	if ( !isset( $response['status'] ) ) {

		// Is this a default group? If not, is this for an activity?
		if ( isset( $_POST['def'] ) && $_POST['def'] && isset( $_POST['class'] ) ) {

			if ( $_POST['def'] == 1 && is_numeric( $_POST['class'] ) ) {
				$default_groups = 1;
				$class_id = intval( $_POST['class'] );
			} else
				$response['status'] = 'INVALID_REQUEST';

		} elseif ( isset( $_POST['act'] ) && isset( $_POST['class'] ) ) {

			if ( is_numeric( $_POST['act'] ) && is_numeric( $_POST['class'] ) ) {
				$default_groups = 0;
				$class_id = intval( $_POST['class'] );
				$activity_id = intval( $_POST['act'] );
				$signup_id = get_signup( $class_id, $activity_id );
			} else
				$response['status'] = 'INVALID_REQUEST';

		} else
			$response['status'] = 'INVALID_REQUEST';
	}

	if ( !isset($response['status']) && !is_post_of_teacher( $class_id, $teacher_id, 'uc_class' ) ) {
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'Not class of user';
	}

	if ( !isset($response['status']) ) {

		$number_of_students = 0;
		// Verify data
		if ( isset( $_POST['groups'] ) && is_numeric( $_POST['groups'] ) ) {
			$groups = intval( $_POST['groups'] );
			$grouparray = array();
			for ( $groupnum = 0; $groupnum < $groups; $groupnum++ ) {
				if ( isset( $_POST['group'.$groupnum] ) && is_numeric( $_POST['group'.$groupnum] ) ) {
					$students = intval( $_POST['group'.$groupnum] );
					$studentsarray = array();
					for ( $studentnum = 0; $studentnum < $students; $studentnum++ ) {
						if ( isset( $_POST['g'.$groupnum.'_'.$studentnum] ) && is_numeric( $_POST['g'.$groupnum.'_'.$studentnum] ) ) {
							$studentsarray[$studentnum] = intval( $_POST['g'.$groupnum.'_'.$studentnum] );
							$number_of_students++;
							if ( !isset($response['status']) && !is_post_of_teacher( $studentsarray[$studentnum], $teacher_id, 'uc_student' ) ) {
								$response['status'] = 'REQUEST_DENIED';
								$response['reason'] = 'Not student of user';
							}
						} else {
							$response['status'] = 'INVALID_REQUEST';
						}
					}
					$grouparray[$groupnum] = $studentsarray;
				} else {
					$response['status'] = 'INVALID_REQUEST';
				}
			}
		} else {
			$response['status'] = 'INVALID_REQUEST';
		}
	}

	// Group number check
	if ( !isset($response['status']) ) {
		$group_size = vp_option('uc_option.group_size');
		if ( $number_of_students <= $group_size + 1 ) {
			if ( $groups > 1 )
				$show_error = true;
		} else {
			$allowed_groups_larger_than_max = $number_of_students % $group_size;
			foreach ( $grouparray as $single_group ) {
				if ( count($single_group) == $group_size + 1 && $allowed_groups_larger_than_max ) {
					$allowed_groups_larger_than_max--;
				} elseif ( count($single_group) != $group_size ) {
					$show_error = true;
				}
			}
		}
		if ( $show_error ) {
			$group_size_error = vp_option('uc_option.group_size_error');
			$group_size_error = str_replace( '!teamsize!', $group_size , $group_size_error);
			$group_size_error = str_replace( '!teamsize+1!', $group_size+1 , $group_size_error);

			$response['status'] = 'REQUEST_DENIED';
			$response['reason'] = $group_size_error;
		}
	}

	if ( !isset($response['status']) ) {
		$new_groups = uc_update_groups( $grouparray, $teacher_id, $class_id, $default_groups, $activity_id, $signup_id );
		if ( is_array( $new_groups ) ) {

			// Give next cap
			if ( !current_user_can( 'edit_activities' ) ) {
				$user = wp_get_current_user();
				$user->add_cap( 'edit_activities' );

				// Since this is an AJAX update, give proceed
				global $highest_capability_pages;

				$pages = get_pages( array(
					'meta_key' => '_wp_page_template',
					'meta_value' => $highest_capability_pages['edit_activities']
				));
				if ( $pages )
					$proceed_url = get_permalink( $pages[0] );
				else
					$proceed_url = home_url();

				$response['proceed']['url'] = $proceed_url;
				$response['proceed']['text'] = vp_option('uc_option.group_proceed');;
			}
			$response['status'] = 'OK';
			$response['new'] = $new_groups;
		} else
			$response['status'] = 'UNKNOWN_ERROR';
	}
	echo json_encode( $response );
	wp_die();
}


add_action( 'wp_ajax_submit_score', 'uc_submit_score_callback' );
// Used when submitting scores for an activity
function uc_submit_score_callback() {

	$response = array();

	$user_id = get_current_user_id();
	$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );

	if ( !wp_verify_nonce( $_POST['_wpnonce'], 'update_groups') ) { // Nonce check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'Did not pass security check';
	} elseif ( !$teacher_id ) { // Teacher check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User error';
	} elseif ( !current_user_can('edit_activities') ) { // User check
		$response['status'] = 'REQUEST_DENIED';
		$response['reason'] = 'User does not have edit privileges';
	}

	// Make sure data is entered correctly
	if ( !isset($response['status']) ) {
		if ( isset( $_POST['ac'] ) && is_numeric( $_POST['ac'] ) 
			&& isset( $_POST['cl'] ) && is_numeric( $_POST['cl'] ) 
			&& isset( $_POST['groups'] ) && is_numeric( $_POST['groups'] ) ) {

			$activity_id = intval( $_POST['ac'] );
			$class_id = intval( $_POST['cl'] );
			$signup_id = get_signup( $class_id, $activity_id );
			$current_school_year = get_current_school_year();
			$groups = get_uc_groups_by_activity( $class_id, $activity_id, 'all', 'publish', $current_school_year );
			$score_fields = get_post_meta( $activity_id, 'score_fields', true );
			$submitted_group_number = intval( $_POST['groups'] );
			$submitted_groups = [];

		} else 
			$response['status'] = 'INVALID_REQUEST';
	}

	// Verify data
	if ( !isset($response['status']) ) {
		if ( !is_post_of_teacher( $class_id, $teacher_id, 'uc_class' ) ) { // Is this a class of the teacher?
			$response['status'] = 'REQUEST_DENIED';
			$response['reason'] = 'Not class of user';
		} elseif ( !$signup_id ) { // Is there a signup?
			$response['status'] = 'REQUEST_DENIED';
			$response['reason'] = 'Not signed up for this activity';
		} elseif ( signup_status($class_id, $activity_id ) != 'active' ) {
			$response['status'] = 'REQUEST_DENIED';
			$response['reason'] = 'This activity is not active for this class';
		} elseif ( !count($groups) ) { // Are groups set?
			$response['status'] = 'REQUEST_DENIED';
			$response['reason'] = 'No groups are set for this activity';
		} elseif ( count($groups) != $submitted_group_number ) { // Do groups match?
			$response['status'] = 'REQUEST_DENIED';
			$response['reason'] = 'Teams must be saved before submitting';
		}
	}

	// Verify group score data
	if ( !isset($response['status']) ) {
		$score_field_names = get_score_fields_by_activity( $activity_id );
		$score_ranges = get_score_ranges( $activity_id );

		for ($i = 0; $i < $submitted_group_number; $i++ ) {
			if ( isset( $_POST['g'.$i] ) && is_numeric( $_POST['g'.$i] ) ) {

				$submitted_groups[$i]['id'] = floatval( $_POST['g'.$i] );

				// Validate submitted groups match with saved groups
				$matched_group = false;
				foreach ( $groups as $group_key => $group ) {
					if ( $submitted_groups[$i]['id'] == $group['value'] ) {
						$matched_group = true;
						unset( $groups[$group_key] );
					}
				}

				if ( !isset($response['status']) ) {
					if ( !is_post_of_teacher( $submitted_groups[$i]['id'], $teacher_id ) ) {
						$response['status'] = 'REQUEST_DENIED';
						$response['reason'] = 'Not team of user';
					} elseif ( !$matched_group ) {
						$response['status'] = 'REQUEST_DENIED';
						$response['reason'] = 'Teams must be saved before submitting';
					}
				}

				// Validate scores
				for ($j = 0; $j < $score_fields; $j++) {
					if ( isset( $_POST['g'.$i.'-'.$j] ) && is_numeric( $_POST['g'.$i.'-'.$j] ) ) {
						$submitted_groups[$i]['scores'][] = intval( $_POST['g'.$i.'-'.$j] );
					} else {
						$response['status'] = 'INVALID_REQUEST';
					}
				}

				// Validate score min/max
				for ($j = 0; $j < $score_fields; $j++) {
					if (isset($score_ranges[$j+1])) {
						if (isset($score_ranges[$j+1]['min']) && $score_ranges[$j+1]['min'] > $_POST['g'.$i.'-'.$j]) {
							$response['status'] = 'REQUEST_DENIED';
							$a_min_error = vp_option('uc_option.a_min_error');
							$a_min_error = str_replace('!name!', $score_field_names[$j], $a_min_error);
							$a_min_error = str_replace('!score!', $score_ranges[$j+1]['min'], $a_min_error);
							$response['reason'] = $a_min_error;
						}
						if (isset($score_ranges[$j+1]['max']) && $score_ranges[$j+1]['max'] < $_POST['g'.$i.'-'.$j]) {
							$response['status'] = 'REQUEST_DENIED';
							$a_max_error = vp_option('uc_option.a_max_error');
							$a_max_error = str_replace('!name!', $score_field_names[$j], $a_max_error);
							$a_max_error = str_replace('!score!', $score_ranges[$j+1]['max'], $a_max_error);
							$response['reason'] = $a_max_error;
						}
					}
				}

				// Validate teamwork score
				if ( isset( $_POST['g'.$i.'-t'] ) && is_numeric( $_POST['g'.$i.'-t'] ) ) {
					$submitted_groups[$i]['teamwork-score'] = intval( $_POST['g'.$i.'-t'] );
				} else {
					$response['status'] = 'INVALID_REQUEST';
				}
			} else {
				$response['status'] = 'INVALID_REQUEST';
			}
		}
	}

	if ( !isset($response['status']) ) {
		// Did all groups match with submitted groups?
		if ( count( $groups ) != 0 ) {
			$response['status'] = 'REQUEST_DENIED';
			$response['reason'] = 'Teams must be saved before submitting';
		}
	}

	if ( !isset($response['status'])) {
		// Data is verified, update
		$current_school_year = get_current_school_year();
		$groups = get_uc_groups_by_activity( $class_id, $activity_id, 'all', 'publish', $current_school_year );
		foreach ( $submitted_groups as $submitted_group ) {
			if ( !isset($response['status']) ) {
				$score = calculate_score( $activity_id, $submitted_group['scores'] );
				
				if ( $score !== false ) {
					update_post_meta( $submitted_group['id'], 'score', $score );
					update_post_meta( $submitted_group['id'], 'teamwork_score', $submitted_group['teamwork-score'] );
				} else {
					$response['status'] = 'REQUEST_DENIED';
					$response['reason'] = 'Score calculation error. Please contact admin.';
				}
			}
		}

		if ( !isset($response['status']) ) {
			// Remove reminders
			uc_should_schedule_email_expire( $signup_id, false );
			update_post_meta( $signup_id, 'completed', '1' );
			$response['status'] = 'OK';
			$response['updated'] = $submitted_groups;
			uc_email_upload_receipt( $signup_id );
		}
	}
	echo json_encode( $response );
	wp_die();
}