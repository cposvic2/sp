<?php
/*
 * Activity Metabox
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return array(
	'id'          => 'uc_student',
	'types'       => array( 'uc_student' ),
	'title'       => __( 'Student Details', UPTOWNCODE_PLUGIN_NAME ),
	'priority'    => 'high',
	'mode'        => WPALCHEMY_MODE_EXTRACT,
	'template'    => array(
		array(
			'type' => 'textbox',
			'name' => 'firstname',
			'label' => __( 'First Name', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
			'validation' => 'alphabet|minlength[1]',
		),
		array(
			'type' => 'textbox',
			'name' => 'lastinitial',
			'label' => __( 'Last Initial', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
			'validation' => 'minlength[1]|maxlength[1]|alphabet',
		),
		array(
			'type' => 'select',
			'name' => 'grade',
			'label' => __( 'Grade', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value' => 'get_uc_grades',
					),
			    ),
			),
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
			'type' => 'radiobutton',
			'name' => 'gender',
			'label' => __( 'Gender', UPTOWNCODE_PLUGIN_NAME ),
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
			'type' => 'select',
			'name' => 'ell',
			'label' => __( 'English as Second Language', UPTOWNCODE_PLUGIN_NAME ),
			'description' => __( "Whether the student is learning English as a Second Language.", UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value'  => 'get_yesno',
					),
			    ),
			),
		),
	),
);
