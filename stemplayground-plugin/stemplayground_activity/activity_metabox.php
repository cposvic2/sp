<?php
/*
 * Activity Metabox
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return array(
	'id'          => 'uc_activity',
	'types'       => array( 'uc_activity' ),
	'title'       => __( 'Activity Options', UPTOWNCODE_PLUGIN_NAME ),
	'priority'    => 'high',
	'mode'        => WPALCHEMY_MODE_EXTRACT,
	'template'    => array(
		array(
			'type' 			=> 'radiobutton',
			'name' 			=> 'availability',
			'label' 		=> __( 'Availability', UPTOWNCODE_PLUGIN_NAME ),
			'default' 		=> 'status-available',
			'items' 		=> array(
				array(
					'value' => 'status-available',
					'label' => __( 'Available', UPTOWNCODE_PLUGIN_NAME ),
				),
				array(
					'value' => 'status-unavailable',
					'label' => __( 'Unavailable', UPTOWNCODE_PLUGIN_NAME ),
				),
			),
		),
		array(
			'type' 			=> 'radiobutton',
			'name' 			=> 'difficulty',
			'label' 		=> __( 'Level of Difficulty', UPTOWNCODE_PLUGIN_NAME ),
			'description'	=> __('Level of difficulty to set up activity.', UPTOWNCODE_PLUGIN_NAME ),
			'default' 		=> 'difficulty-minimal',
			'items' 		=> array(
				array(
					'value' => 'difficulty-minimal',
					'label' => __( 'Minimal', UPTOWNCODE_PLUGIN_NAME ),
				),
				array(
					'value' => 'difficulty-moderate',
					'label' => __( 'Moderate', UPTOWNCODE_PLUGIN_NAME ),
				),
			),
		),
		array(
			'type' => 'textarea',
			'name' => 'activity_summary',
			'label' => __( 'Activity Summary', UPTOWNCODE_PLUGIN_NAME ),
			'description'	=> __('Brief summary of activity. Used for Activity Board and Goal content of Activity Sheet', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'upload',
			'name' => 'activity_banner',
			'label' => __( 'Activity Banner', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'textarea',
			'name' => 'video_engagement_links',
			'label' => __( 'Video Engagement Links', UPTOWNCODE_PLUGIN_NAME ),
			'description'	=> __('Links to Youtube Videos that will be added to the Activity Page. Separate each link by a semicolon (;).', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'textarea',
			'name' => 'video_engagement_text',
			'label' => __( 'Video Engagement Text', UPTOWNCODE_PLUGIN_NAME ),
			'description'	=> __('Text before Video Engagement Links.', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'slider',
			'name' => 'score_fields',
			'label' => __('Number of Score Fields', UPTOWNCODE_PLUGIN_NAME),
			'description' => __('The number of score fields for this activity.', UPTOWNCODE_PLUGIN_NAME),
			'min' => '1',
			'max' => '10',
			'step' => '1',
			'default' => '1',
		),
		array(
			'type' => 'textbox',
			'name' => 'score_titles',
			'label' => __( 'Score Field Names', UPTOWNCODE_PLUGIN_NAME ),
			'description'	=> __('Specify the name of each field, separated by a semicolon. Any unnamed fields will default to "Score X"', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'textarea',
			'name' => 'score_range',
			'label' => __( 'Score Ranges', UPTOWNCODE_PLUGIN_NAME ),
			'description'	=> __('Ranges for each score type. Formats must be in this format: "ScoreX(min,max)", with each type separated by a semicolon. Ex. "Score1(0,100);Score2(5-50)". Not case-sensitive. If there is an error in the format, the system will assume no score range for that score type.', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'textarea',
			'name' => 'score_calculation',
			'label' => __( 'Score Calculation', UPTOWNCODE_PLUGIN_NAME ),
			'description'	=> __('Specify the mathematical formula for calculating the final score. Each Score Field should be typed in with the format "Score1", "Score2", etc.', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'toggle',
			'name' => 'score_higher',
			'label' => __( 'Higher score is better', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '0',
		),
		array(
			'type' => 'wpeditor', //wpeditor
			'name' => 'required_materials',
			'label' => __( 'Required Materials', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'wpeditor', //wpeditor
			'name' => 'activity_competition',
			'label' => __( 'Competition', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'wpeditor', //wpeditor
			'name' => 'activity_overview',
			'label' => __( 'Overview of Activity', UPTOWNCODE_PLUGIN_NAME ),
			'description'	=> __('Used for Supporting Instruction section of Activity Sheet.', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'wpeditor', //wpeditor
			'name' => 'activity_background_knowledge',
			'label' => __( 'Background Knowledge', UPTOWNCODE_PLUGIN_NAME ),
			'description'	=> __('Used for Supporting Instruction section of Activity Sheet.', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'wpeditor', //wpeditor
			'name' => 'activity_scientific_concepts',
			'label' => __( 'Scientific Concepts', UPTOWNCODE_PLUGIN_NAME ),
			'description'	=> __('Used for Supporting Instruction section of Activity Sheet.', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'wpeditor', //wpeditor
			'name' => 'activity_class_discussion',
			'label' => __( 'Class Discussion', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),		
		array(
			'type' => 'upload',
			'name' => 'activity_handout',
			'label' => __( 'Activity Handout', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'upload',
			'name' => 'activity_exam',
			'label' => __( 'Activity Exam', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'upload',
			'name' => 'activity_answer_key',
			'label' => __( 'Activity Answer Key', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'upload',
			'name' => 'activity_additional',
			'label' => __( 'Additional Activity Materials', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
	),
);
