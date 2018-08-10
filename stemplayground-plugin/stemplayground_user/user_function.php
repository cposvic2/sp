<?php 
/*
 * User Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
		exit;
}

// Register teacher custom post
add_action( 'show_user_profile', 'uc_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'uc_extra_user_profile_fields' );
function uc_extra_user_profile_fields( $user ) {
	global $admin_capabilities;
	$admin_settings = array();
	$user_teacher = get_the_author_meta( 'teacher_id', $user->ID );
	foreach ($admin_capabilities as $admin_capability) {
		$admin_settings[$admin_capability] = (user_can( $user, $admin_capability ) ? 1 : 0 );
	}
?>
	<h3><?php __("STEM Playground User Data", UPTOWNCODE_PLUGIN_NAME ); ?></h3>
	<table class="form-table">
		<tr>
			<th><label for="stripe_id"><?php _e("Stripe ID"); ?></label></th>
			<td><input class="regular-text" type="text" id="stripe_id" name="stripe_id" value="<?php echo get_the_author_meta( 'stripe_id', $user->ID ); ?>"></td>
		</tr>
		<tr>
			<th><label for="teacherid"><?php _e("Associated Teacher"); ?></label></th>
			<td>
				<select name="teacher_id" id="teacher-id">
					<option value="0"></option>
					<?php
						$teachers = get_uc_teachers();
						foreach ( $teachers as $teacher ) {
							echo '<option value="'. $teacher['value'] .'" '. selected( $teacher['value'], $user_teacher, false) .'>'. $teacher['label'] .'</option>';
						}
					?>
				</select>&nbsp;<a href="<?php echo get_edit_post_link( $user_teacher ); ?>">Edit teacher</a>
		</td>
		<tr class="show-admin-bar user-admin-bar-front-wrap">
			<th scope="row">STEM Playground Capabilities</th>
			<td>
				<p>Check if this user can:</p>
				<p>
					<input type="checkbox" id="edit_school" name="edit_school" value="1" <?php checked( $admin_settings['edit_school'] ); ?>> <label for="edit_school">Edit their school</label>
					<br>
					<input type="checkbox" id="edit_class" name="edit_class" value="1" <?php checked( $admin_settings['edit_class'] ); ?>> <label for="edit_class">Edit their classes</label>
					<br>
					<input type="checkbox" id="edit_students" name="edit_students" value="1" <?php checked( $admin_settings['edit_students'] ); ?>> <label for="edit_students">Edit their students</label>
					<br>
					<input type="checkbox" id="edit_groups" name="edit_groups" value="1" <?php checked( $admin_settings['edit_groups'] ); ?>> <label for="edit_groups">Edit their teams</label>
					<br>
					<input type="checkbox" id="edit_activities" name="edit_activities" value="1" <?php checked( $admin_settings['edit_activities'] ); ?>> <label for="edit_activities">Edit their activity signups</label>
				</p>
			</td>
		</tr>
	</table>
<?php
}

add_action( 'personal_options_update', 'uc_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'uc_save_extra_user_profile_fields' );
function uc_save_extra_user_profile_fields( $user_id ) {
	$saved = false;
	if ( current_user_can( 'edit_user', $user_id ) ) {
		update_user_meta( $user_id, 'stripe_id', intval( $_POST['stripe_id'] ) );
		update_user_meta( $user_id, 'teacher_id', intval( $_POST['teacher_id'] ) );
		
		global $admin_capabilities;
		$user = get_userdata( $user_id );
		foreach ($admin_capabilities as $admin_capability) {
			$val = intval( $_POST[$admin_capability] );
			if (!user_can( $user_id, $admin_capability ) && $val)
				$user->add_cap($admin_capability);
			elseif (user_can( $user_id, $admin_capability ) && !$val)
				$user->remove_cap($admin_capability);
		}

		$saved = true;
	}
	return $saved;
}

// Returns list of users with Teacher ID
function get_users_by_teacher( $teacher_id = '' ) {
	$wp_users = get_users(array(
		'meta_key'     => 'teacher_id',
		'meta_value'   => $teacher_id,
	));

	$result = array();
	foreach ($wp_users as $user) {
		$result[] = array( 'value' => $user->ID, 'label' => $user->display_name );
	}
	return $result;
}

// Returns list of users with email confirmation token
function get_users_by_email_confirmation_token( $email_confirmation_token = '' ) {
	$wp_users = get_users(array(
		'meta_key'     => 'email_confirmation_token',
		'meta_value'   => $email_confirmation_token,
	));

	$result = array();
	foreach ($wp_users as $user) {
		$result[] = array( 'value' => $user->ID, 'label' => $user->display_name );
	}
	return $result;
}

function create_unverified_teacher( $user_login = false, $user_pass = false, $first_name = '', $last_name = '', $user_email = false, $birthdate = false, $gender = false, $stem_proficient = false, $college_science = false, $ambassador = false, $school_at_risk = false, $stem_competitions = false, $mailing_list = false ) {

	if ( $user_login && $user_pass && $first_name && $last_name && $user_email ) {

		$userdata = array(
			'user_login'  =>  $user_login,
			'user_pass'  =>  $user_pass,
			'user_email'  =>  $user_email,
			'first_name'  =>  $first_name,
			'last_name'  =>  $last_name,
			'role'  =>  'unverified_teacher',
		);
		$user_id = wp_insert_user( $userdata );

		if ( !is_wp_error( $user_id ) ) {

			$new_teacher = array (
				'post_title' => $first_name . ' ' . $last_name,
				'post_type' => 'uc_teacher',
				'post_author' => $user_id,
				'post_status' => 'publish',
			);
			$teacher_id = wp_insert_post( $new_teacher );

			update_user_meta( $user_id, 'teacher_id', $teacher_id );

			$email_confirmation_token = hash( 'md5', $user_id );
			update_user_meta( $user_id, 'email_confirmation_token', $email_confirmation_token );

			global $uc_teacher_fields;
			update_post_meta( $teacher_id, 'uc_teacher_fields', $uc_teacher_fields );
			update_post_meta( $teacher_id, 'first_name', $first_name );
			update_post_meta( $teacher_id, 'first_name', $first_name );
			update_post_meta( $teacher_id, 'last_name', $last_name );
			update_post_meta( $teacher_id, 'email', $user_email );
			update_post_meta( $teacher_id, 'birthdate', $birthdate );
			update_post_meta( $teacher_id, 'gender', $gender );
			update_post_meta( $teacher_id, 'stem_proficient', $stem_proficient );
			update_post_meta( $teacher_id, 'college_science', $college_science );
			update_post_meta( $teacher_id, 'ambassador', $ambassador );
			update_post_meta( $teacher_id, 'school_at_risk', $school_at_risk );
			update_post_meta( $teacher_id, 'stem_competitions', $stem_competitions );
			update_post_meta( $teacher_id, 'mailing_list', $mailing_list );

			return $user_id;
		}
	} 
	return false;
}

function create_sponsor_user( $user_login = null, $user_pass = null, $name = null, $user_email = null, $twitter = null, $stripe_id = null ) {

	if ( $user_login && $user_pass && $name && $user_email ) {

		$userdata = array(
			'user_login'  =>  $user_login,
			'user_pass'  =>  $user_pass,
			'user_email'  =>  $user_email,
			'first_name'  =>  $name,
			'role'  =>  'sponsor',
		);
		$user_id = wp_insert_user( $userdata );

		if ( !is_wp_error( $user_id ) ) {
			update_user_meta( $user_id, 'stripe_id', $stripe_id );
			update_user_meta( $user_id, 'twitter', $twitter );
			return $user_id;
		}
	} 
	return false;
}

// Confirm user based off security token
function confirm_uc_user( $email_confirmation_token = '' ) {
	$users = get_users_by_email_confirmation_token( $email_confirmation_token );

	foreach ( $users as $user ) {
		if ( !user_can( $user['value'], 'see_teacher_pages' ) )
			$user_id = wp_update_user( array( 'ID' => $user['value'], 'role' => 'teacher' ) );
	}
	if ( isset($user_id) && !is_wp_error($user_id) )
		return $user_id;
	else
		return false;
}

// Get highest user cap
function get_uc_user_highest_cap() {

	if ( !current_user_can( 'edit_school' ) )
		return 'read';

	if ( !current_user_can( 'edit_class' ) )
		return 'edit_school';

	if ( !current_user_can( 'edit_students' ) )
		return 'edit_class';

	if ( !current_user_can( 'edit_groups' ) )
		return 'edit_students';

	if ( !current_user_can( 'edit_activities' ) )
		return 'edit_groups';

	return 'edit_activities';
}

 ?>