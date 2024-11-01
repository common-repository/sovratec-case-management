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

class Sov_Case_Management_CaseEmailConfig {
	// Constructor
	function __construct() {
		add_action( 'wp_ajax_save_casetype', array( $this, 'save_case_type' ) );
		add_action( 'wp_ajax_nopriv_save_casetype', array( $this, 'save_case_type' ) );
		add_action( 'wp_ajax_delete_case_type', array( $this, 'delete_case_type' ) );
		add_action( 'wp_ajax_nopriv_delete_case_type', array( $this, 'delete_case_type' ) );
		add_action( 'wp_ajax_get_case_type', array( $this, 'get_case_type' ) );
		add_action( 'wp_ajax_nopriv_get_case_type', array( $this, 'get_case_type' ) );
		self::case_type_handler();
	}

	// ajax functions
	public function save_case_type() {

	}
	public function case_type_handler() {
		include_once plugin_dir_path( __FILE__ ) . 'class-sov-case-management-email-configurations.php';
	}

		// ajax functions
	public function delete_case_type() {

	}
			// ajax functions
	public function get_case_type() {

	}
}

$case_email_config = new Sov_Case_Management_CaseEmailConfig();
$case_email_config->case_type_handler();
