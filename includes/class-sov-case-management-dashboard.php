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

class Sov_Case_Management_Dashboard {

	public function __construct() {

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/case-management-admin-tabs/class-sov-case-management-tabs.php';

	}
}
$wp_list_table = new Sov_Case_Management_Dashboard();
