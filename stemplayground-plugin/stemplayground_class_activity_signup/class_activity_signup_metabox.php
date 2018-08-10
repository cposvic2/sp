<?php
/*
 * Class Metabox
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return array(
	'id'          => 'uc_signup',
	'types'       => array( 'uc_signup' ),
	'title'       => __( 'Signup Details', UPTOWNCODE_PLUGIN_NAME ),
	'priority'    => 'high',
	'mode'        => WPALCHEMY_MODE_EXTRACT,
	'template'    => array(
		array(
			'type' => 'select',
			'name' => 'class',
			'label' => __( 'Class', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value'  => 'get_uc_classes',
					),
			    ),
			),
		),
		array(
			'type' => 'select',
			'name' => 'activity',
			'label' => __( 'Activity', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value'  => 'get_uc_activities',
					),
			    ),
			),
		),
		array(
			'type' => 'date',
			'name' => 'signup_date',
			'label' => __('Signup Date', UPTOWNCODE_PLUGIN_NAME ),
			'min_date' => '1-1-2000',
			'format' => 'mm-dd-yy',
			'default' => 'today',
		),
		array(
			'type' => 'toggle',
			'name' => 'completed',
			'label' => __( 'Completed Activity', UPTOWNCODE_PLUGIN_NAME ),
			'description' => __( "Whether the activity is marked as completed.", UPTOWNCODE_PLUGIN_NAME ),
			'default' => '0',
		),
	),
);
