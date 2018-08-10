<?php

function uc_get_email_text( $option, $teacher_id, $configuration = array() ) {
	$return = array(
		'content' => vp_option('uc_option.email_'.$option),
		'subject' => vp_option('uc_option.subj_'.$option),
		'address' => get_post_meta($teacher_id, 'email', true),
	);

	$replacements = array(
		'!first_name!' => get_post_meta($teacher_id, 'first_name', true),
		'!last_name!' => get_post_meta($teacher_id, 'last_name', true),
		'!user_email!' => $return['address'],
	);

	switch ($option) {
		case 'upload_receipt':
			$activity_id = $configuration['activity_id'];
			$replacements['!activity_name!'] = get_the_title($activity_id);
			$replacements['!results_link!'] = esc_url(get_permalink($activity_id));
			$ab_pages = get_pages( array(
				'meta_key' => '_wp_page_template',
				'meta_value' => 'page-activity-board.php'
			));
			if ( $ab_pages )
				$ab_url = get_permalink( $ab_pages[0] );
			else
				$ab_url = home_url();
			$replacements['!activity_board_link!'] = $ab_url;
			break;
		case 'missing_students':
		case 'missing_teams':
		case 'no_activity_30':
		case 'no_activity_60':
		case 'no_activity_90':
			$class_id = $configuration['class_id'];
			$replacements['!class_name!'] = get_the_title($class_id);
			break;
		case 'enroll_receipt':
		case 'expire_7_days':
		case 'expire_1_day':
			$signup_id = $configuration['signup_id'];
			$class_id = get_post_meta($signup_id, 'class', true);
			$activity_id = get_post_meta($signup_id, 'activity', true);
			$signup_date = DateTime::createFromFormat( 'm-d-Y', get_post_meta( $signup_id, 'signup_date', true ) );
			$expiration_length = vp_option('uc_option.activity_expiration_length');
			$expiration_length = new DateInterval('P'.$expiration_length.'D');
			$expiration_date = clone $signup_date;
			$expiration_date->add( $expiration_length );
			$replacements['!class_name!'] = get_the_title($class_id);
			$replacements['!activity_name!'] = get_the_title($activity_id);
			$replacements['!materials_required!'] = get_post_meta($activity_id, 'required_materials', true);
			$replacements['!activity_expire_date!'] = $expiration_date->format('M j Y');
			$replacements['!signup_date!'] = $signup_date->format('M j Y');
			break;
		case 'admin_notify':
			$return['address'] = $configuration['notify_email'];
			$replacements['!notify_email!'] = $configuration['notify_email'];
			break;
	}

	foreach ($replacements as $search => $replace) {
		$return['content'] = str_replace($search, $replace, $return['content']);
		$return['subject'] = str_replace($search, $replace, $return['subject']);
	}
	return $return;
}

function uc_email_missing_data( $teacher_id ) {
	$current_school_year = get_current_school_year();
	$uc_classes = get_uc_classes_by_teacher( $teacher_id, 'publish', $current_school_year );
	if (!count($uc_classes)) {
		$email = uc_get_email_text( 'missing_class', $teacher_id );
		stemplayground_send_email( $email['address'], $email['subject'], $email['content'] );
		return;
	}

	$first_class = end($uc_classes);
	$class_id = $first_class['value'];
	$uc_students = get_uc_students_by_class( $class_id, 'publish', $current_school_year );
	if (!count($uc_students)) {
		$email = uc_get_email_text( 'missing_students', $teacher_id, array('class_id' => $class_id) );
		stemplayground_send_email( $email['address'], $email['subject'], $email['content'] );
		return;
	}

	$uc_groups = get_default_uc_groups_by_class( $class_id, 'publish', $current_school_year );
	if (!count($uc_groups)) {
		$email = uc_get_email_text( 'missing_teams', $teacher_id, array('class_id' => $class_id) );
		stemplayground_send_email( $email['address'], $email['subject'], $email['content'] );
	}
}
add_action( 'uc_email_missing_data', 'uc_email_missing_data' );

function uc_email_admin_notify( $teacher_id, $notify_email ) {
	$teacher_id = get_post_meta( $teacher_id, 'teacher', true );
	$email = uc_get_email_text( 'admin_notify', $teacher_id, array('notify_email' => $notify_email) );
	stemplayground_send_email( $email['address'], $email['subject'], $email['content'] );
}

function uc_email_no_activity_30( $class_id ) {
	$teacher_id = get_post_meta( $class_id, 'teacher', true );
	$email = uc_get_email_text( 'no_activity_30', $teacher_id, array('class_id' => $class_id) );
	stemplayground_send_email( $email['address'], $email['subject'], $email['content'] );
}
add_action( 'uc_email_no_activity_30', 'uc_email_no_activity_30' );

function uc_email_no_activity_60( $class_id ) {
	$teacher_id = get_post_meta( $class_id, 'teacher', true );
	$email = uc_get_email_text( 'no_activity_60', $teacher_id, array('class_id' => $class_id) );
	stemplayground_send_email( $email['address'], $email['subject'], $email['content'] );
}
add_action( 'uc_email_no_activity_60', 'uc_email_no_activity_60' );

function uc_email_no_activity_90( $class_id ) {
	$teacher_id = get_post_meta( $class_id, 'teacher', true );
	$email = uc_get_email_text( 'no_activity_90', $teacher_id, array('class_id' => $class_id) );
	stemplayground_send_email( $email['address'], $email['subject'], $email['content'] );
}
add_action( 'uc_email_no_activity_90', 'uc_email_no_activity_90' );

function uc_email_enroll_receipt( $signup_id ) {
	$class_id = get_post_meta( $signup_id, 'class', true );
	$teacher_id = get_post_meta( $class_id, 'teacher', true );
	$email = uc_get_email_text( 'enroll_receipt', $teacher_id, array('signup_id' => $signup_id) );
	stemplayground_send_email( $email['address'], $email['subject'], $email['content'] );
}

function uc_email_expire_7_days( $signup_id ) {
	$class_id = get_post_meta( $signup_id, 'class', true );
	$activity_id = get_post_meta( $signup_id, 'activity', true );
	$signup_status = signup_status( $class_id, $activity_id );
	if ($signup_status !== 'completed') {
		$teacher_id = get_post_meta( $class_id, 'teacher', true );
		$email = uc_get_email_text( 'expire_7_days', $teacher_id, array('signup_id' => $signup_id) );
		stemplayground_send_email( $email['address'], $email['subject'], $email['content'] );	
	}
}
add_action( 'uc_email_expire_7_days', 'uc_email_expire_7_days' );

function uc_email_expire_1_day( $signup_id ) {
	$class_id = get_post_meta( $signup_id, 'class', true );
	$activity_id = get_post_meta( $signup_id, 'activity', true );
	$signup_status = signup_status( $class_id, $activity_id );
	if ($signup_status !== 'completed') {
		$teacher_id = get_post_meta( $class_id, 'teacher', true );
		$email = uc_get_email_text( 'expire_1_day', $teacher_id, array('signup_id' => $signup_id) );
		stemplayground_send_email( $email['address'], $email['subject'], $email['content'] );	
	}
}
add_action( 'uc_email_expire_1_day', 'uc_email_expire_1_day' );

function uc_email_upload_receipt( $signup_id ) {
	$class_id = get_post_meta( $signup_id, 'class', true );
	$teacher_id = get_post_meta( $class_id, 'teacher', true );
	$email = uc_get_email_text( 'upload_receipt', $teacher_id, array('signup_id' => $signup_id) );
	stemplayground_send_email( $email['address'], $email['subject'], $email['content'] );	
}

function stemplayground_send_email( $recipient, $subject, $content, $plaintext = null ) {
	$boundary = uniqid('np');
	$headers = 'From: STEM Playground <no-reply@stemplayground.org>' . "\r\n";
	$headers .= 'Content-Type: multipart/alternative;boundary="' . $boundary . '"' . "\r\n";

	//here is the content body
	$message = "This is a MIME encoded message.";
	$message .= "\r\n\r\n--" . $boundary . "\r\n";
	$message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";

	//Plain text body
	if (!is_null($plaintext)) {
		$message .= wp_strip_all_tags($plaintext) . "\n";
	} else {
		$message .= wp_strip_all_tags($content) . "\n";
	}
	$message .= "\r\n\r\n--" . $boundary . "\r\n";
	$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";

	//Html body
	$message .= '<html>
		<head>
		<title>'.$subject.'</title> 
		</head>
		<body>';
	$message .= wpautop($content);
	$message .= '</body>
		</html>';
	
	$message .= "\r\n\r\n--" . $boundary . "--";

	return mail( $recipient, $subject, $message, $headers );
}

function uc_should_schedule_email_missing_data( $teacher_id, $should = true ) {
	$timestamp = get_the_time('U', $teacher_id) + 259200; // +3 days
	uc_should_schedule_email( $should, $timestamp, 'uc_email_missing_data', array($teacher_id) );
}

function uc_should_schedule_email_no_activity( $class_id, $should = true ) {
	$initial_timestamp = get_the_time('U', $class_id);

	// Set 30 days
	$interval = 2592000; // 30 days
	$timestamp = $initial_timestamp + $interval;
	uc_should_schedule_email( $should, $timestamp, 'uc_email_no_activity_30', array($class_id) );

	// Set 60 days
	$interval = 5184000; // 60 days
	$timestamp = $initial_timestamp + $interval;
	uc_should_schedule_email( $should, $timestamp, 'uc_email_no_activity_60', array($class_id) );

	// Set 90 days
	$interval = 7776000; // 90 days
	$timestamp = $initial_timestamp + $interval;
	uc_should_schedule_email( $should, $timestamp, 'uc_email_no_activity_90', array($class_id) );
}

function uc_should_schedule_email_expire( $signup_id, $should = true ) {
	$expiration_date = get_signup_expiration_date( $signup_id );
	$expiration_timestamp = $expiration_date->getTimestamp();

	$timestamp2 = time() + 60; // Plus 1 mins

	// Set 7 days
	$interval = 604800; // 7 days
	$timestamp = $expiration_timestamp - $interval; 
	uc_should_schedule_email( $should, $timestamp2, 'uc_email_expire_7_days', array($signup_id) );

	// Set 1 day
	$interval = 86400; // 1 day
	$timestamp = $expiration_timestamp - $interval;
	uc_should_schedule_email( $should, $timestamp2, 'uc_email_expire_1_day', array($signup_id) );

	uc_should_schedule_email( false, $timestamp2, 'uc_email_expire_7_days', array($signup_id) );
	uc_should_schedule_email( false, $timestamp2, 'uc_email_expire_1_day', array($signup_id) );
}

function uc_should_schedule_email( $should, $timestamp, $hook, $args ) {
	if ( $should ) {
		wp_schedule_single_event( $timestamp, $hook, $args );
	} else {
		wp_unschedule_event( $timestamp, $hook, $args );
	}
}

function stemplayground_test_shortcode($atts = []) {
	$user_id = 1; // Cory user ID
	$teacher_id = get_user_meta( $user_id, 'teacher_id', 'true' );
	$email = uc_get_email_text( 'upload_receipt', $teacher_id, array('activity_id'=>9264) );
	//stemplayground_send_email( $email['address'], $email['subject'], $email['content'] );
	//uc_email_missing_data( $teacher_id );
	//uc_should_schedule_email_expire( 41641 );
	return 'email: '.$email['address'].'<br>
	subject: '.$email['subject'].'<br>
	message: '.$email['content'];
}

add_shortcode('stemplayground_test_shortcode', 'stemplayground_test_shortcode');