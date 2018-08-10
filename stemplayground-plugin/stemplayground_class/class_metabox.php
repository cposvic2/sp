<?php
/*
 * Class Metabox
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return array(
	'id'          => 'uc_class',
	'types'       => array( 'uc_class' ),
	'title'       => __( 'Class Details', UPTOWNCODE_PLUGIN_NAME ),
	'priority'    => 'high',
	'mode'        => WPALCHEMY_MODE_EXTRACT,
	'template'    => array(
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
			'type' 			=> 'select',
			'name' 			=> 'grade',
			'label' 		=> __( 'Grade', UPTOWNCODE_PLUGIN_NAME ),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value' => 'get_uc_grades',
					),
			    ),
			),
		),
	),
);
