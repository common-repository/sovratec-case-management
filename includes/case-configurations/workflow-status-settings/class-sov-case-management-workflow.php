<?php

/**
 * Fired during plugin activation
 *
 * @link       https://sovratec.com/
 * @since      1.0.0
 *
 * @package    Sov_Case_Management
 * @subpackage Sov_Case_Management/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sov_Case_Management
 * @subpackage Sov_Case_Management/includes
 * @author     Sovratec <https://sovratec.com/>
 */

class Sov_Case_Management_WorkflowStausConfig {
	// Constructor
	function __construct() {
		self::workflow_status_handler();
	}
	public function workflow_status_handler() {
		include_once plugin_dir_path( __FILE__ ) . 'class-sov-case-management-workflow-configurations.php';
	}
}

$case_email_config = new Sov_Case_Management_WorkflowStausConfig();
$case_email_config->workflow_status_handler();
