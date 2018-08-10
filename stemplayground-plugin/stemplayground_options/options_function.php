<?php 
/*
 * STEM Playground Options
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register Options
function uc_init_options() {
	// Built path to options template array file
	$tmpl_opt  = plugin_dir_path( __FILE__ ) . 'options.php';
	// Initialize the Option's object
	$uc_theme_options = new VP_Option(array(
		'is_dev_mode'           => false,
		'option_key'            => 'uc_option',
		'page_slug'             => 'uc_option',
		'template'              => $tmpl_opt,
		'menu_page' => array(
			'icon_url' 			=> 'dashicons-admin-generic',
			'position' 			=> 60,
		),
		'use_auto_group_naming' => true,
		'use_exim_menu'         => true,
		'minimum_role'          => 'edit_theme_options',
		'layout'                => 'fixed',
		'page_title'            => __( 'STEM Playground Options', UPTOWNCODE_PLUGIN_NAME ),
		'menu_label'            => __( 'STEM Playground Options', UPTOWNCODE_PLUGIN_NAME ),
	));
}
// the safest hook to use, since Vafpress Framework may exists in Theme or Plugin
add_action( 'after_setup_theme', 'uc_init_options' )

 ?>