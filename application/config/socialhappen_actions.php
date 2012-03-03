<?php
/**
 * Socialhappen Actions
 * Please add predefined socialhappen app actions (action_id >= 1000)
 * Any name can be used
 */
// Default actions
$config['platform_action_view'] = 1001;
$config['platform_action_share'] = 1002;

/**
 * Socialhappen Achievements
 * Please add app's achievement infos related to actions above
 * The name must be 'achievement_infos'
 */
// Default achievements
$config['achievement_infos'] = array(
	array(
		'info' => array(
			'name' => 'First share',
			'description' => 'Shared video for the first time',
			'criteria_string' => array('Share = 1')
		),
		'criteria' => array(
			"action.{$config['platform_action_share']}.app_install.{app_install_id}.count" => 1
		)
	),
	array(
		'info' => array(
			'name' => 'Super sharer',
			'description' => 'Shared video 10 times',
			'criteria_string' => array('Share = 10')
		),
		'criteria' => array(
			"action.{$config['platform_action_share']}.app_install.{app_install_id}.count" => 10
		)
	),
);