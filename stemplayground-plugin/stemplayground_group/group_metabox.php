<?php
/*
 * Group Metabox
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'id'          => 'uc_group',
	'types'       => array( 'uc_group' ),
	'title'       => __( 'Group Details', UPTOWNCODE_PLUGIN_NAME ),
	'priority'    => 'high',
	'mode'        => WPALCHEMY_MODE_EXTRACT,
	'template'    => array(
		array(
			'type' => 'toggle',
			'name' => 'default_group',
			'label' => __( 'Default Group', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '0',
		),
		array(
			'type' => 'select',
			'name' => 'teacher',
			'label' => __( 'Teacher', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value'  => 'get_uc_teachers',
					),
				),
			),
		),
		array(
			'type' => 'select',
			'name' => 'class',
			'label' => __( 'Class', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'binding',
						'field'  => 'teacher',
						'value'  => 'get_uc_classes_by_teacher',
					),
				),
			),
		),
		array(
			'type' => 'multiselect',
			'name' => 'students',
			'label' => __( 'List of Students', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'binding',
						'field'  => 'class',
						'value'  => 'get_uc_students_by_class',
					),
				),
			),
		),
		array(
			'type' => 'select',
			'name' => 'signup',
			'label' => __( 'Associated Signup', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value'  => 'get_uc_signups',
					),
				),
			),
		),
		array(
			'type' => 'textbox',
			'name' => 'score',
			'label' => __( 'Group Score', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
			'validation' => 'numeric',
		),
		array(
			'type' => 'select',
			'name' => 'teamwork_score',
			'label' => __( 'Teamwork Score', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				array(
					'value' => '1',
					'label' => '1',
				),
				array(
					'value' => '2',
					'label' => '2',
				),
				array(
					'value' => '3',
					'label' => '3',
				),
				array(
					'value' => '4',
					'label' => '4',
				),
				array(
					'value' => '5',
					'label' => '5',
				),
			),
		),
	),
);
