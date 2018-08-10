<?php
/*
 * Activity Metabox
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return array(
	'id'          => 'uc_school',
	'types'       => array( 'uc_school' ),
	'title'       => __( 'School Details', UPTOWNCODE_PLUGIN_NAME ),
	'priority'    => 'high',
	'mode'        => WPALCHEMY_MODE_EXTRACT,
	'template'    => array(
		array(
			'type' => 'toggle',
			'name' => 'school_verified',
			'label' => __( 'School Verified', UPTOWNCODE_PLUGIN_NAME ),
			'description'	=> __('By checking this option, you allow this school to populate the School Search when a user is selecting their school. Verify all information for the school is valid before selecting this checkbox.', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '0',
		),
		array(
			'type' => 'toggle',
			'name' => 'non_traditional',
			'label' => __( 'Non-Traditional School', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '0',
		),
		array(
			'type' => 'textbox',
			'name' => 'streetaddress',
			'label' => __( 'Street Address', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
			'validation' => '',
		),
		array(
			'type' => 'textbox',
			'name' => 'streetaddress2',
			'label' => __( 'Street Address 2', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
			'validation' => '',
		),
		array(
			'type' => 'textbox',
			'name' => 'city',
			'label' => __( 'City', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
			'validation' => '',
		),
		array(
			'type' => 'textbox',
			'name' => 'county',
			'label' => __( 'County', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
			'validation' => '',
		),
		array(
			'type' => 'textbox',
			'name' => 'zip',
			'label' => __( 'Zipcode', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'select',
			'name' => 'country',
			'label' => __( 'Country', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value' => 'get_countries',
					),
			    ),
			),
		),
		array(
			'type' => 'select',
			'name' => 'state',
			'label' => __( 'State/Province', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'binding',
						'field'  => 'country',
						'value'  => 'get_administrative_areas_level_1',
					),
			    ),
			),
		),
		array(
			'type' => 'textarea',
			'name' => 'notes',
			'label' => __( 'Notes', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'textbox',
			'name' => 'latitude',
			'label' => __( 'Latitude', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
			'validation' => '',
		),
		array(
			'type' => 'textbox',
			'name' => 'longitude',
			'label' => __( 'Longitude', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
			'validation' => '',
		),
	),
);
