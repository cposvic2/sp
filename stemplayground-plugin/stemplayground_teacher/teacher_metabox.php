<?php
/*
 * Teacher Metabox
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return array(
	'id'          => 'uc_teacher',
	'types'       => array( 'uc_teacher' ),
	'title'       => __( 'Teacher Details', UPTOWNCODE_PLUGIN_NAME ),
	'priority'    => 'high',
	'mode'        => WPALCHEMY_MODE_EXTRACT,
	'template'    => array(
		array(
			'type' => 'toggle',
			'name' => 'ambassador',
			'label' => __( 'STEM Playground Ambassador', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '0',
		),
		array(
			'type' => 'textbox',
			'name' => 'first_name',
			'label' => __( 'First Name', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
			'validation' => 'minlength[1]',
		),
		array(
			'type' => 'textbox',
			'name' => 'last_name',
			'label' => __( 'Last Name', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
			'validation' => 'minlength[1]',
		),
		array(
			'type' => 'textbox',
			'name' => 'mailing_list',
			'label' => __( 'Mailing list opt-in', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'textbox',
			'name' => 'convertkit_id',
			'label' => __( 'ConvertKit ID', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'textbox',
			'name' => 'email',
			'label' => __( 'Email Address', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
			'validation' => 'minlength[1]|email',
		),
		array(
			'type' => 'date',
			'name' => 'birthdate',
			'label' => __('Birthday', UPTOWNCODE_PLUGIN_NAME ),
			'min_date' => '1-1-1900',
			'format' => 'mm-dd-yy',
			'default' => 'today',
		),
		array(
			'type' 			=> 'radiobutton',
			'name' 			=> 'gender',
			'label' 		=> __( 'Gender', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value'  => 'get_genders',
					),
			    ),
			),
			'default' => array(
				'{{first}}',
			),
		),
		array(
			'type' => 'toggle',
			'name' => 'stem_proficient',
			'label' => __( 'Proficiency in STEM', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '0',
		),
		array(
			'type' => 'toggle',
			'name' => 'college_science',
			'label' => __( 'College Science Major', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '0',
		),
		array(
			'type' => 'toggle',
			'name' => 'ambassador',
			'label' => __( 'STEM Playground Ambassador', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '0',
		),
		array(
			'type' => 'select',
			'name' => 'school',
			'label' => __( 'School', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value' => 'get_uc_schools',
					),
			    ),
			),
		),
		array(
			'type' => 'radiobutton',
			'name' => 'stem_competitions',
			'label' => __( 'STEM Competitions', UPTOWNCODE_PLUGIN_NAME ),
			'description' => __( "Whether this teacher's district participates in Middle School- or High School-level STEM competitions.", UPTOWNCODE_PLUGIN_NAME ),
			'items' 		=> array(
				array(
					'value' => '1',
					'label' => __( 'Yes', UPTOWNCODE_PLUGIN_NAME ),
				),
				array(
					'value' => '2',
					'label' => __( 'No', UPTOWNCODE_PLUGIN_NAME ),
				),
				array(
					'value' => '0',
					'label' => __( "Don't know", UPTOWNCODE_PLUGIN_NAME ),
				),
			),
		),
		array(
			'type' => 'radiobutton',
			'name' => 'school_at_risk',
			'label' => __( 'Students At-Risk', UPTOWNCODE_PLUGIN_NAME ),
			'description' => __( "Whether this teacher believes the majority of their students are at-risk.", UPTOWNCODE_PLUGIN_NAME ),
			'items' 		=> array(
				array(
					'value' => '1',
					'label' => __( 'Yes', UPTOWNCODE_PLUGIN_NAME ),
				),
				array(
					'value' => '2',
					'label' => __( 'No', UPTOWNCODE_PLUGIN_NAME ),
				),
				array(
					'value' => '0',
					'label' => __( "No opinion", UPTOWNCODE_PLUGIN_NAME ),
				),
			),
		),
		array(
			'type' => 'textarea',
			'name' => 'notes',
			'label' => __( 'Notes', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
	),
);
