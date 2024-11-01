<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://sovratec.com/
 * @since             1.0.0
 * @package           Sov_Case_Management
 *
 * @wordpress-plugin
 * Plugin Name:       Sovratec Case Management
 * Plugin URI:        https://sovratec.com/plugins/return-exchange
 * Description:       Case management system for clients
 * Version:           1.0.0
 * Author:            Sovratec
 * Author URI:        https://sovratec.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sovratec-return-exchange
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WCMS_RETURN_EXCHANGE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sov-case-management-activator.php
 */
function WCMS_activate_sov_case_management() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sov-case-management-activator.php';
	Sov_Case_Management_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function WCMS_deactivate_sov_case_management() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sov-case-management-deactivator.php';
	Sov_Case_Management_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'WCMS_activate_sov_case_management' );
register_deactivation_hook( __FILE__, 'WCMS_deactivate_sov_case_management' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sov-case-management.php';

/**
 * Create the shortcode to print the map
 */
function WCMS_clientview_shortcode( $atts ) {
	ob_start();
	require plugin_dir_path( __FILE__ ) . 'public/partials/sov-case-management-public-display.php';
	return ob_get_clean();
}
add_shortcode( 'sovcms_client_view', 'WCMS_clientview_shortcode' );

// Add link to woocommerce account page
function WCMS_add_plugin_link( $menu_links ) {
	$return_exchange_link = strtolower( str_replace( ' ', '-', trim( 'Case Management Client View' ) ) );
	$new                  = array( $return_exchange_link => 'Case Management' );
	$menu_links           = array_slice( $menu_links, 0, 1, true )
	+ $new
	+ array_slice( $menu_links, 1, null, true );
	return $menu_links;
}
add_filter( 'woocommerce_account_menu_items', 'WCMS_add_plugin_link' );

// Second Filter to Redirect the WooCommerce endpoint to custom URL
function WCMS_plugin_hook_endpoint( $url, $endpoint, $value, $permalink ) {
	$return_exchange_link = strtolower( str_replace( ' ', '-', trim( 'Case Management Client View' ) ) );
	if ( $endpoint === $return_exchange_link ) {
		// Add return/exchange url
		$url = site_url() . '/' . $return_exchange_link . '/';
	}
	return $url;
}
add_filter( 'woocommerce_get_endpoint_url', 'WCMS_plugin_hook_endpoint', 10, 4 );

// allow widget text to run shortcodes
add_filter( 'widget_text', 'do_shortcode' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function WCMS_run_sov_case_management() {
	$plugin = new Sov_Case_Management();
	$plugin->run();
}
WCMS_run_sov_case_management();
