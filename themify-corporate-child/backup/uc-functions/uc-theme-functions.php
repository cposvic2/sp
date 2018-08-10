<?php 

// List of authority pages, and the highest cap needed to view them
$authority_templates = array(
	'page-activity-sheet.php' => 'edit_activities',
	'single-uc_activity.php' => 'edit_activities',
	'page-activity-board.php' => 'edit_activities',
	'page-groups.php' => 'edit_groups',
	'page-student.php' => 'edit_students',
	'page-classes.php' => 'edit_class',
	'page-school.php' => 'edit_school',
);

// List of authority post types, and the highest cap needed to view them
$authority_post_types = array(
	'uc_activity' => 'edit_activities',
);

// The page the user should be redirected to based off highest cap
$highest_capability_pages = array(
	'edit_activities' => 'page-activity-board.php',
	'edit_groups' => 'page-groups.php',
	'edit_students' => 'page-student.php',
	'edit_class' => 'page-classes.php',
	'edit_school' => 'page-school.php',
	'read' => '/signup-confirmation/',
);

/**
 * Creates activity_sheet_page global variable
 * 
 */
$pages = get_pages(array(
	'meta_key' => '_wp_page_template',
	'meta_value' => 'page-activity-board.php'
));
if ( $pages )
	$activity_sheet_page = $pages[0];
else
	$activity_sheet_page = false;

/**
 * Creates PHP session
 * 
 */
function uc_start_session() {
	if(!session_id()) {
		session_start();
	}
}
add_action('init', 'uc_start_session', 1);

function uc_end_session() {
	session_destroy ();
}
add_action('wp_logout', 'uc_end_session');
add_action('wp_login', 'uc_end_session');


/**
 * PRD for post submissions
 * 
 */
function uc_post_submit_handler() {
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		if(isset( $_POST['action']) ) {
			if ( $_POST['action'] == 'activeClass' ) {
				// Nonce check
				if ( wp_verify_nonce( $_POST['active_class_noncename'], 'active_class') ) {
					if( isset( $_POST['activeClass']) && is_numeric( $_POST['activeClass'] ) ) {
						$class_id = intval( $_POST['activeClass'] );
						$user_id = get_current_user_id();
						$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
						if ( is_post_of_teacher( $class_id, $teacher_id ) ) {
							$_SESSION['activeClass'] = $class_id;
						}
					}
				}
				// Redirect to this page.
				header("Location: " . $_SERVER['REQUEST_URI']);
				exit();
			} elseif ( $_POST['action'] == 'activitySignup' ) {
				if ( wp_verify_nonce( $_POST['activity_signup_noncename'], 'activity_signup') ) {
					$class_id = intval( $_POST['classID'] );
					$activity_id = intval( $_POST['activityID'] );
					$user_id = get_current_user_id();
					$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
					$signup_status = signup_status( $class_id, $activity_id );

					if ( is_post_of_teacher( $class_id, $teacher_id, 'uc_class' ) && $signup_status === false && !is_class_at_maximum_signups( $class_id ) ) {
						sign_up_for_activity( $class_id, $activity_id );
					}
				}
				// Redirect to this page.
				header("Location: " . $_SERVER['REQUEST_URI']);
				exit();
			} elseif ( $_POST['action'] == 'signupSubmit' ) {
				// Nonce check
				if ( wp_verify_nonce( $_POST['update_user_noncename'], 'update_user') ) {

					$username = ( isset( $_POST['uname'] ) ? sanitize_user( $_POST['uname'] ) : false );
					$password = ( isset( $_POST['upass'] ) ? $_POST['upass'] : false );
					$first_name = ( isset( $_POST['ufirst'] ) ? sanitize_text_field( $_POST['ufirst'] ) : '' ); 
					$last_name = ( isset( $_POST['ulast'] ) ? sanitize_text_field( $_POST['ulast'] ) : '' ); 
					$email = ( isset( $_POST['uemail'] ) ? sanitize_email( $_POST['uemail'] ) : false ); 
					$birthday = ( isset( $_POST['ubd'] ) && strtotime( $_POST['ubd'] ) ? date("m-d-Y", strtotime( sanitize_text_field( $_POST['ubd'] ))) : false ); 
					$gender = ( isset( $_POST['ugen'] ) ? sanitize_text_field( $_POST['ugen'] ) : false );
					$stem_proficient = ( isset( $_POST['ustem'] ) ? intval( $_POST['ustem'] ) : 0 );
					$college_science_major = ( isset( $_POST['usci'] ) ? intval( $_POST['usci'] ) : 0 );
					$ambassador = ( isset( $_POST['uamb'] ) ? intval( $_POST['uamb'] ) : 0 );
					$school_at_risk = ( isset( $_POST['uatrisk'] ) ? intval( $_POST['uatrisk'] ) : 0 );
					$stem_competitions = ( isset( $_POST['ucomp'] ) ? intval( $_POST['ucomp'] ) : 0 );

					if (username_exists( $username )) {
						$GLOBALS['response'] = array(
							'status' => 'REQUEST_DENIED',
							'reason' => vp_option('uc_option.us_uname_taken')
						);
						return false;
					}

					$user_created = create_unverified_teacher( 
						$username, 
						$password, 
						$first_name, 
						$last_name, 
						$email, 
						$birthday, 
						$gender, 
						$stem_proficient, 
						$college_science_major,
						$ambassador,
						$school_at_risk,
						$stem_competitions
						);

					// Redirect to email verification page if user was created
					if ( $user_created ) {
						send_confirmation_email( $user_created );
						header("Location: " . home_url() . '/signup-confirmation/' );
						exit();
					} else {
						$GLOBALS['response'] = array(
							'status' => 'REQUEST_DENIED',
							'reason' => vp_option('uc_option.us_submit_error')
						);
					}
				}
			} elseif ( $_POST['action'] == 'updateSchool' ) {
				global $google_api_key;

				// Nonce check and make sure user can edit schools
				if ( wp_verify_nonce( $_POST['update_school_noncename'], 'update_school') && current_user_can('edit_school') ) {

					$sctitle = ( isset( $_POST['sctitle'] ) ? ucwords(sanitize_text_field( $_POST['sctitle'] )) : '' );
					$scnontrad = ( !empty( $_POST['scnontrad'] ) ? 1 : 0 ); 
					$scaddress1 = ( isset( $_POST['scaddress1'] ) ? sanitize_text_field( $_POST['scaddress1'] ) : false ); 
					$scaddress2 = ( isset( $_POST['scaddress2'] ) ? sanitize_text_field( $_POST['scaddress2'] ) : false ); 
					$sccity = ( isset( $_POST['sccity'] ) ? sanitize_text_field( $_POST['sccity'] ) : false ); 
					$sccounty = ( isset( $_POST['sccounty'] ) ? sanitize_text_field( $_POST['sccounty'] ) : false ); 
					$scstate = ( isset( $_POST['scstate'] ) ? sanitize_text_field( $_POST['scstate'] ) : false ); 
					$sczip = ( isset( $_POST['sczip'] ) ? sanitize_text_field( $_POST['sczip'] ) : false );
					$sccountry = ( isset( $_POST['sccountry2'] ) ? sanitize_text_field( $_POST['sccountry2'] ) : false);

					$geocode_link = 'https://maps.googleapis.com/maps/api/geocode/json?address='.
						urlencode($scaddress1).'+'.
						urlencode($scaddress2).'+'.
						urlencode($sccity).'+'.
						urlencode($scstate).'+'.
						urlencode($sczip).'+'.
						urlencode($sccountry).
						'&key='.$google_api_key;

					$geocode_file = file_get_contents($geocode_link);
					$geocode_results = json_decode($geocode_file);

					if ( $geocode_results->status = "OK" ) {

						$latitude = $geocode_results->results[0]->geometry->location->lat;
						$longitude = $geocode_results->results[0]->geometry->location->lng;

						foreach ( $geocode_results->results[0]->address_components as $address_component ) {
							if ( in_array('subpremise', $address_component->types) )
								$scaddress2 = $address_component->short_name;
							if ( in_array('street_number', $address_component->types) )
								$street_number = $address_component->short_name;
							if ( in_array('route', $address_component->types) )
								$route = $address_component->short_name;
							if ( in_array('locality', $address_component->types) )
								$sccity = $address_component->short_name;
							if ( in_array('administrative_area_level_2', $address_component->types) )
								$sccounty = $address_component->short_name;
							if ( in_array('administrative_area_level_1', $address_component->types) )
								$scstate = $address_component->short_name;
							if ( in_array('postal_code', $address_component->types) )
								$sczip = $address_component->short_name;
						}

						if ( isset($street_number) && isset($route) )
							$scaddress1 = $street_number . ' ' . $route;

					}

					$user_id = get_current_user_id();
					$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );

					$school_created = create_uc_school( $user_id, $sctitle, $scnontrad, $latitude, $longitude, $scaddress1, $scaddress2, $sccity, $sccounty, $scstate, $sczip, $sccountry);

					// Redirect if school was created
					if ( $school_created ) {

						// Give next cap cap
						if ( !current_user_can( 'edit_class' ) ) {
							$user = wp_get_current_user();
							$user->add_cap( 'edit_class' );
						}

						update_post_meta( $teacher_id, 'school', $school_created );

						wp_redirect( admin_url() );
						exit();
					}
				}
				header("Location: " . $_SERVER['REQUEST_URI'] . '?r=e');
				exit();
			} elseif ( $_POST['action'] == 'saveSchool' ) {

				// Nonce check and make sure user can edit schools
				if ( wp_verify_nonce( $_POST['save_school_noncename'], 'save_school') && current_user_can('edit_school') && is_numeric( $_POST['scschool'] ) ) {

					$school_id = intval( $_POST['scschool'] );
					
					if ( get_post_type ( $school_id ) == 'uc_school' ) {

						$user_id = get_current_user_id();
						$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
						update_post_meta( $teacher_id, 'school', $school_id );

						// Give next cap cap
						if ( !current_user_can( 'edit_class' ) ) {
							$user = wp_get_current_user();
							$user->add_cap( 'edit_class' );
						}
						wp_redirect( admin_url() );
						exit();
					}
				}
			}
		}
	}
}
add_action('init', 'uc_post_submit_handler', 1);


/**
 * Redirection rules
 * 
 */
function wp_redirect_visitors() {
	global $authority_templates, $authority_post_types, $highest_capability_pages;
	$page_template = get_page_template_slug();
	$page_slug = get_post_field( 'post_name', get_post() );
	$post_type = get_post_type();
	
	if ( is_user_logged_in() ) { // For logged in users

		// Get latest page user is allowed to go to
		$uc_user_highest_cap = get_uc_user_highest_cap();

		foreach ( $highest_capability_pages as $key => $highest_capability_page ) {
			if ( $uc_user_highest_cap == $key ) {
				if ( $highest_capability_page[0] == "/" ) {
					$uc_user_highest_page = home_url() . $highest_capability_page;
				} else {
					$uc_user_highest_template = $highest_capability_page;
					$pages = get_pages( array(
						'meta_key' => '_wp_page_template',
						'meta_value' => $uc_user_highest_template
					));
					if ( $pages )
						$uc_user_highest_page = get_permalink( $pages[0] );
					else
						$uc_user_highest_page = home_url();
				}
			}
		}
		
		// If user tries to jump ahead, kick them back
		if ( $page_template ) {
			if ( array_key_exists( $page_template, $authority_templates ) ) {
				$capability = $authority_templates[ $page_template ];
				if ( !current_user_can( $capability ) ) {
					wp_redirect( $uc_user_highest_page );
					exit;
				}
			}
		}
		if ( $post_type ) {
			if ( !is_admin() && array_key_exists( $post_type, $authority_post_types ) ) {
				$capability = $authority_post_types[ $post_type ];
				if ( !current_user_can( $capability ) ) {
					wp_redirect( $uc_user_highest_page );
					exit;
				}
			}
		}

		// If user is logged in, don't let them sign up
		if ( $page_template == 'page-signup.php' ) {
			wp_redirect( $uc_user_highest_page );
			exit;
		}

		// If activity sheet is not set, go to activity board
		if ( $page_template == 'page-activity-sheet.php' && !isset( $_GET["a"] ) ) {
			wp_redirect( $uc_user_highest_page );
			exit;
		}

	} else { // For not logged in users

		// If user isn't logged in, boot them to the homepage
		if ( $page_template ) {
			if ( array_key_exists( $page_template, $authority_templates ) ) {
				wp_redirect( home_url() );
				exit;
			}
		}
		if ( $post_type ) {
			if ( array_key_exists( $post_type, $authority_post_types ) && $post_type != 'uc_activity' ) { // Condition added for non-logged-in users to see uc_activity
				wp_redirect( home_url() );
				exit;
			}
		}
	}

	// Rules for confirming users
	if ( $page_slug == 'confirm' ) {
		$verified_user = false;

		// Try confirming user first
		if ( isset( $_GET["confirmation_token"] ) ) {
			$email_confirmation_token = sanitize_text_field( $_GET["confirmation_token"] );
			$verified_user = confirm_uc_user( $email_confirmation_token );
		}

		// If they weren't confirmed, go to the highest page
		if ( !$verified_user ) {
			if ( is_user_logged_in() )
				wp_redirect( $uc_user_highest_page );
			else
				wp_redirect( home_url() );
			exit;
		}
	}
}

function get_permalink_from_template($template) {
	if (!empty($template)) {
		$pages = get_pages( array(
			'meta_key' => '_wp_page_template',
			'meta_value' => $template
		));
		if ( $pages )
			return get_permalink( $pages[0] );
	}
	return home_url();
}

// Extra redirect rules that must happen at init
function init_redirect_visitors() {

	// If non-admin tries to go to admin pages, redirect them to the highest capability page. The other redirect rules will get them to where they should be
	if ( is_admin() && ! current_user_can( 'edit_posts' ) && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		global $highest_capability_pages;

		$uc_user_highest_template = $highest_capability_pages['edit_activities'];
		$pages = get_pages( array(
			'meta_key' => '_wp_page_template',
			'meta_value' => $uc_user_highest_template
		));
		if ( $pages )
			$uc_user_highest_page = get_permalink( $pages[0] );
		else
			$uc_user_highest_page = home_url();

		wp_redirect( $uc_user_highest_page);
		exit;
	}

}
add_action( 'wp', 'wp_redirect_visitors' );
add_action( 'init', 'init_redirect_visitors' );


/**
 * Remove Admin bar for Activity Sheets
 * 
 */
function hide_admin_bar( $content ) {
	if ( is_page_template( 'page-activity-sheet.php' ) ) {
		return false;
	} else {
		return $content;
	}
}
add_filter( 'show_admin_bar', 'hide_admin_bar' );

function display_activity_card( $activity_id, $is_league_challenge_activity = false ) {

	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $activity_id ), 'activity-board' );

	echo '
		<div class="activity-card">
			<a href="'.esc_url( get_permalink( $activity_id ) ).'">
				<div class="activity" >
					<div class="activity-image-container">
						<div class="activity-image" style="background-image: url('.$image[0].')"></div>
					</div>
					<div class="activity-title">'.get_the_title( $activity_id ).'</div>
					<div class="separator"></div>
					<div class="activity-description">
						'.get_post_meta( $activity_id, 'activity_summary', true ).'
					</div>
					'.( $is_league_challenge_activity ? '<div class="league-challenge-card"></div>' : '' ).'
				</div>
			</a>
		</div>';
}


/**
 * Prints team table
 * 
 */
function display_team_table( $group_rankings ) {

	echo '
	<div class="uc-table-container">
		<table class="uc-table" id="class-table">
			<thead>
				<tr>
					<th>Rank</th>
					<th>Team Number</th>
					<th>Students</th>
					<th>Final Score</th>
				</tr>
			</thead>
			<tbody>';

	$i = 0;

	while ( $i < count($group_rankings) ) {
		echo '
			<tr>
				<td>'.($i+1).'</td>
				<td>'.get_the_title( $group_rankings[$i]['id'] ).'</td>
				<td>
				';
		echo get_group_student_names( $group_rankings[$i]['id'] );
		echo '
				</td>
				<td>'.$group_rankings[$i]['score'].'</td>
			</tr>	
		';
		$i++;
	}
	echo '
			</tbody>
		</table>
	</div>
	';

}

/**
 * Prints groups
 * 
 */
function display_groups( $uc_class_id = 0, $uc_activity_id = false, $editable = true ) {

	$group_size = vp_option('uc_option.group_size');

	if ( !$uc_class_id || !is_numeric( $uc_class_id ) )
		return false;
	if ( $uc_activity_id && !is_numeric( $uc_activity_id ) )
		return false;

	$current_school_year = get_current_school_year();
	$uc_students = get_uc_students_by_class( $uc_class_id, 'publish', $current_school_year );

	if ( $uc_activity_id )
		$uc_groups = get_uc_groups_by_activity( $uc_class_id, $uc_activity_id, 'all', 'publish', $current_school_year );
	else
		$uc_groups = get_default_uc_groups_by_class( $uc_class_id, 'publish', $current_school_year );


	if ( $uc_students < $group_size + 1 )
		$ideal_group_number = 1;
	else
		$ideal_group_number = floor ( count( $uc_students ) / $group_size );

	foreach ( $uc_groups as $uc_group_key => $uc_group ) {
		$uc_groups[$uc_group_key]['students'] = array();
		$uc_group_students = get_post_meta( $uc_group['value'], 'students', true );
		foreach ( $uc_students as $student_key => $uc_student ) {
			if ( in_array( $uc_student['value'], $uc_group_students ) ) {
				unset($uc_students[$student_key]);
				$uc_groups[$uc_group_key]['students'][] = $uc_student;
			}
		}
	}

	if ( $uc_activity_id && $editable ) {
		$score_fields = get_score_fields_by_activity( get_the_id() );
	}

	 ?>
	 <div class="uc-groups-container<?php echo ( $editable ? ' uc-groups-container-editable' : '') ?>">
		 <div class="uc-drag-container student-container<?php echo ( $editable ? ' uc-drag-container-editable' : '') ?>">
			<div class="uc-drag-container-label"><?php echo ( $uc_activity_id ? 'Unassigned students for this activity' : 'Unassigned students' ) ?></div>
			<div class="uc-drag-draggable-container">
				<?php foreach( $uc_students as $uc_student ) {
					$uc_student_id = $uc_student['value'];
					$uc_student_name =  $uc_student['label'];
					$gender = get_post_meta( $uc_student_id, 'gender', true );
				 ?>
					<div class="uc-drag" data-sid="<?php echo $uc_student_id ?>">
						<div class="uc-drag-inner <?php echo $gender ?>">
							<?php echo $uc_student_name ?>
						</div>
					</div>

				<?php } ?>

				<div class="clear"></div>
			</div>
		</div>
		<div class="groups" data-count="<?php echo $ideal_group_number ?>">
			<?php
			$groupcount = 1;
			foreach ( $uc_groups as $uc_group ) {
				 ?>
				<div class="uc-drag-container group-container ui-droppable<?php echo ( $editable ? ' uc-drag-container-editable' : '') ?>" data-ref="<?php echo $uc_group['value']; ?>">
					<div class="uc-drag-container-label">Team <?php echo $groupcount ?></div>
					<div class="uc-drag-draggable-container">
						<?php foreach( $uc_group['students'] as $uc_student ) {
							$uc_student_id = $uc_student['value'];
							$uc_student_name =  $uc_student['label'];
							$gender = get_post_meta( $uc_student_id, 'gender', true );
						 ?>
							<div class="uc-drag" data-sid="<?php echo $uc_student_id ?>">
								<div class="uc-drag-inner <?php echo $gender ?>">
									<?php echo $uc_student_name ?>
								</div>
							</div>

						<?php } ?>
						<div class="clear"></div>
					</div>
					<?php if ( $uc_activity_id && $editable ) {
					
					$count = 0;
					 ?>
					<div class="uc-group-score">
						<div class="uc-drag-container-label">Score</div>
							<?php foreach( $score_fields as $score_field ) {
									$count++; ?><div class="score-group">
									<div class="score-group-title"><?php echo $score_field ?></div>
									<input type="text" name="score_<?php echo $count ?>" class="uc-input input-large uc-activity-score score-<?php echo $count ?>" value="">
								</div><?php } ?><div class="score-group">
								<div class="score-group-title">Teamwork Score</div>
								<select name="teamwork_score" class="uc-input input-large uc-activity-score teamwork-score" value="">
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3" selected>3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
							</div>
					</div>
					<?php } elseif ( $uc_activity_id && !$editable ) {
					$group_score = get_post_meta( $uc_group['value'], 'score', true );
					$teamwork_score = get_post_meta( $uc_group['value'], 'teamwork_score', true );
					$final_score = calculate_final_score( $group_score, $teamwork_score, $uc_activity_id );
					 ?>
					<div class="uc-group-score">
						<div class="uc-drag-container-label">Score</div>
						<div class="score-group">
							<div class="score-group-title">Team Score</div>
							<div class="score-result-value"><span class="score"><?php echo $group_score ?></span></div>
						</div><div class="score-group">
							<div class="score-group-title">Teamwork Score</div>
							<div class="score-result-value"><span class="score"><?php echo $teamwork_score ?></span></div>
						</div><div class="score-group">
							<div class="score-group-title">Final Score</div>
							<div class="score-result-value"><span class="score"><?php echo $final_score ?></span></div>
						</div>
					</div>
					<?php } ?>
				</div>
			<?php 
			$groupcount++;
			}
			if ( $editable ) {
				for ( $i = $groupcount; $i <= $ideal_group_number; $i++) { ?>
					<div class="uc-drag-container group-container ui-droppable uc-drag-container-editable empty"><div class="uc-drag-container-label">Team <?php echo $i ?></div><div class="uc-drag-draggable-container"><div class="clear"></div></div>
					<?php
					if ( $uc_activity_id ) {
					$count = 0;
					?>
					<div class="uc-group-score">
						<div class="uc-drag-container-label">Score</div>
							<?php foreach( $score_fields as $score_field ) {
									$count++; ?><div class="score-group">
									<div class="score-group-title"><?php echo $score_field ?></div>
									<input type="text" name="score_<?php echo $count ?>" class="uc-input input-large uc-activity-score score-<?php echo $count ?>" value="">
								</div><?php } ?><div class="score-group">
								<div class="score-group-title">Teamwork Score</div>
								<select name="teamwork_score" class="uc-input input-large uc-activity-score teamwork-score" value="">
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3" selected>3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
							</div>
					</div><?php
					} ?></div><?php
				}
			} ?>
		</div>
	</div>
	<?php
}

add_filter( 'wp_nav_menu_items', 'add_login_to_nav', 10, 2 );
function add_login_to_nav( $items, $args ) {
	if (is_user_logged_in()) {
		$items .= '<li><a href="'.wp_logout_url(get_home_url()).'">Log out</a></li>';
	} else {
		$signup_pages = get_pages( array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-signup.php'
		));
		if ( $signup_pages )
			$signup_page = get_permalink( $signup_pages[0] );
		else
			$signup_page = home_url();

		$items .= '<li><a href="'.wp_login_url().'" style="display:inline-block;padding-left:0px;padding-right:0px">Login</a>/<a href="'.$signup_page.'" style="display:inline-block;padding-right:0px;padding-left:0px">Sign Up</a></li>';
	}
	return $items;
}

// School Map Shortcode
function create_school_map() {
	global $google_api_key;
	wp_enqueue_script( 'google_maps_api', 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key='.$google_api_key );
	wp_enqueue_script( 'school_map_script', UPTOWNCODE_THEME_URL . '/assets/js/schoolmap.js' );
	$return = '
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key='.$google_api_key.'"></script>
	<script type="text/javascript" src="'.UPTOWNCODE_THEME_URL.'/assets/js/schoolmap.js"></script>
	<div class="uc-school-marker-map-container">
		<div class="uc-school-marker-map"></div>
		<div class="stemplayground-map-logo"></div>
		<div class="stemplayground-map-search">
			<input type="text" class="stemplayground-map-search-input" name="search" placeholder="Search locations" autocomplete="off">
		</div>
	</div>';
	$uc_schools = get_uc_schools();
	foreach ($uc_schools as $uc_school) {
		$school_verified = get_post_meta( $uc_school['value'], 'school_verified', true );
		$non_traditional = get_post_meta( $uc_school['value'], 'non_traditional', true );
		$latitude = get_post_meta( $uc_school['value'], 'latitude', true );
		$longitude = get_post_meta( $uc_school['value'], 'longitude', true );

		if (!!$school_verified && !$non_traditional ) {
			if (!$latitude && !$longitude) {
				// Get Geocode results
				$streetaddress = get_post_meta( $uc_school['value'], 'streetaddress', true );
				$city = get_post_meta( $uc_school['value'], 'city', true );
				$state = get_post_meta( $uc_school['value'], 'state', true );
				$country = get_post_meta( $uc_school['value'], 'country', true );

				$data = array(
					'address' => $streetaddress.' '.$city.' '.$state.' '.$country,
					'key'=>$google_key);
				$jsonAddress = 'https://maps.googleapis.com/maps/api/geocode/json?'.http_build_query($data);
				$json = json_decode(file_get_contents($jsonAddress));
				if ( $json->status != 'OVER_QUERY_LIMIT' && $json->status != "ZERO_RESULTS" && $json->status != 'INVALID_REQUEST' ) {
					$return_array['status'] = 'invalid';
					$latitude = $json->results[0]->geometry->location->lat;
					$longitude = $json->results[0]->geometry->location->lng;
					update_post_meta( $uc_school['value'], 'latitude', $latitude );
					update_post_meta( $uc_school['value'], 'longitude', $longitude );
				}
			}
			if ($latitude && $longitude) {
				$schools[] = array(
					'lat' => $latitude,
					'long' => $longitude,
					'name' => $uc_school['label']
				);
			}
		}
	}

	$return .='
	<script>
	var schools = '.json_encode($schools).'
	</script>';
    return $return;
}
add_shortcode('create_school_map', 'create_school_map');

// Activity Cards
function create_activity_cards( $atts ) {
	$create_activity_cards_atts = shortcode_atts( array(
		'count' => 6,
	), $atts );
	$activities = get_uc_activities();
	$return = '<div class="activities-container">';
	for ($i = 0; $i < $create_activity_cards_atts['count']; $i++) {
		if (empty($activities[$i]))
			break;
		$activity = $activities[$i];
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $activity['value']), 'activity-board' );
		$return .= '
			<div class="activity-card">
				<a href="'.esc_url( get_permalink( $activity['value'] ) ).'">
					<div class="activity" >
						<div class="activity-image-container">
							<div class="activity-image" style="background-image: url('.$image[0].')"></div>
						</div>
						<div class="activity-title">'.$activity['label'].'</div>
						<div class="separator"></div>
						<div class="activity-description">
							'.get_post_meta( $activity['value'], 'activity_summary', true ).'
						</div>
					</div>
				</a>
			</div>';
	}
	$return .= '</div>';
    return $return;
}
add_shortcode('create_activity_cards', 'create_activity_cards');

 ?>