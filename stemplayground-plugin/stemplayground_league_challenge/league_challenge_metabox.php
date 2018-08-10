<?php
/*
 * Activity Metabox
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return array(
	'id'          => 'uc_league_challenge',
	'types'       => array( 'uc_league_challenge' ),
	'title'       => __( 'League Challenge Details', UPTOWNCODE_PLUGIN_NAME ),
	'priority'    => 'high',
	'mode'        => WPALCHEMY_MODE_EXTRACT,
	'template'    => array(
		array(
			'type' 			=> 'radiobutton',
			'name' 			=> 'status',
			'label' 		=> __( 'Status', UPTOWNCODE_PLUGIN_NAME ),
			'default' 		=> 'status-active',
			'items' 		=> array(
				array(
					'value' => 'status-active',
					'label' => __( 'Active', UPTOWNCODE_PLUGIN_NAME ),
				),
				array(
					'value' => 'status-inactive',
					'label' => __( 'Inactive', UPTOWNCODE_PLUGIN_NAME ),
				),
			),
		),
		array(
			'type' => 'date',
			'name' => 'challenge_start',
			'label' => __('Challenge Start Date', UPTOWNCODE_PLUGIN_NAME ),
			'min_date' => '1-1-2000',
			'format' => 'mm-dd-yy',
			'default' => 'today',
		),
		array(
			'type' => 'date',
			'name' => 'challenge_end',
			'label' => __('Challenge End Date', UPTOWNCODE_PLUGIN_NAME ),
			'min_date' => '1-1-2000',
			'format' => 'mm-dd-yy',
			'default' => 'today',
		),
		array(
			'type' => 'multiselect',
			'name' => 'activities',
			'label' => __( 'Activities', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value' => 'get_uc_activities',
					),
			    ),
			),
		),
	),
);
