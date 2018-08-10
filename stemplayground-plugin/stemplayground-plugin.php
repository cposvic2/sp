<?php
/*
Plugin Name: Stem Playground Plugin
Plugin URI: http://www.stemplayground.com
Description: This plugin adds required functionality for Stem Playground.
Version: 1.0.0
Author: Uptown Code
Author URI: http://www.uptowncode.com
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define('UPTOWNCODE_PLUGIN_NAME', 'stemplayground_plugin' );
define('UPTOWNCODE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define('UPTOWNCODE_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

// Creates global variables
require_once UPTOWNCODE_PLUGIN_PATH . '/global.php';


/**
 * Load all custom post modules
 */
require_once UPTOWNCODE_PLUGIN_PATH . 'vafpress-framework/bootstrap.php';
require_once UPTOWNCODE_PLUGIN_PATH . 'stripe/init.php';
require_once UPTOWNCODE_PLUGIN_PATH . 'fpdf/fpdf.php';

/**
 * Setup Stemplayground Plugin
 */
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_functions.php');
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_widgets.php');
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_options/options_function.php');
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_league_challenge/league_challenge_function.php');
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_class_activity_signup/class_activity_signup_function.php');
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_activity/activity_function.php');
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_group/group_function.php');
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_student/student_function.php');
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_class/class_function.php');
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_school/school_function.php');
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_teacher/teacher_function.php');
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_user/user_function.php');
include( UPTOWNCODE_PLUGIN_PATH . 'stemplayground_sponsorship/sponsorship_function.php');
include( UPTOWNCODE_PLUGIN_PATH . 'email_functions.php');

if ( is_admin() )
	require_once UPTOWNCODE_PLUGIN_PATH . '/admin.php';

$admin_capabilities = array( 'edit_school', 'edit_class', 'edit_students', 'edit_groups', 'edit_activities' );

// Create custom roles and capabilities
function uc_plugin_activation() {
	global $admin_capabilities;
	$admin_roles = array( 'administrator', 'author', 'editor');
	$capabilities = array(
		'read' => true,
		'level_0' => true
		);

	
	$sponsor_role = add_role( 'sponsor', 'Sponsor', $capabilities );
	$teacher_role = add_role( 'teacher', 'Teacher', $capabilities );
	$unverified_teacher_role = add_role( 'unverified_teacher', 'Unverified Teacher', $capabilities );

	foreach ( $admin_roles as $admin_role ) {
		foreach ( $admin_capabilities as $admin_capability ) {
			$role = get_role( $admin_role );
			$role->add_cap( $admin_capability );
		}
	}

	$role = get_role( 'teacher' );
	$role->add_cap( 'edit_school');
}
register_activation_hook( __FILE__, 'uc_plugin_activation' );
// uc_plugin_activation();