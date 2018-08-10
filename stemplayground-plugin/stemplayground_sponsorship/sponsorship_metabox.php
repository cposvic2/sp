<?php
/*
 * Sponsorship Metabox
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return array(
	'id'          => 'uc_sponsorship',
	'types'       => array( 'uc_sponsorship' ),
	'title'       => __( 'Sponsorship Details', UPTOWNCODE_PLUGIN_NAME ),
	'priority'    => 'high',
	'mode'        => WPALCHEMY_MODE_EXTRACT,
	'template'    => array(
		array(
			'type' => 'toggle',
			'name' => 'fufilled',
			'label' => __( 'Sponsorship Fufilled', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '0',
		),
		array(
			'type' => 'textbox',
			'name' => 'user_id',
			'label' => __( 'User ID', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'textbox',
			'name' => 'sponsor_name',
			'label' => __( 'Sponsor Name/Business Name', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'textbox',
			'name' => 'sponsor_twitter',
			'label' => __( 'Sponsor Twitter Handle', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'textbox',
			'name' => 'sponsor_email',
			'label' => __( 'Sponsor Email Address', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
		array(
			'type' => 'textbox',
			'name' => 'sponsor_stripe',
			'label' => __( 'Sponsor Stripe ID', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
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
			'type' => 'textbox',
			'name' => 'stripe_charge',
			'label' => __( 'Stripe Charge', UPTOWNCODE_PLUGIN_NAME ),
			'default' => '',
		),
	),
);
