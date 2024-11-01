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

class Sov_Case_Management_CaseTypeConfig {
	// Constructor
	public function __construct() {

		self::case_type_handler();

	}

	public function case_type_handler() {
		include_once plugin_dir_path( __FILE__ ) . 'class-sov-case-management-casetype-configurations.php';
	}

}

$case_type_config = new Sov_Case_Management_CaseTypeConfig();
$case_type_config->case_type_handler();
