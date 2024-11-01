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
class Sov_Case_Management_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 * Install database table and other dependencies
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;
		$queries         = array();
		$charset_collate = $wpdb->get_charset_collate();

		// Case Management - Main Table
		array_push(
			$queries,
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}case_management_case` (
				`case_id` bigint(20) NOT NULL AUTO_INCREMENT,
				`first_name` varchar(255) NOT NULL,
				`last_name` varchar(255) NOT NULL,
				`phone_no` varchar(25) NOT NULL,
				`email` varchar(255) NOT NULL,
				`address_1` varchar(255) NOT NULL,
				`address_2` varchar(255) NOT NULL,
				`city` varchar(255) NOT NULL,
				`state` varchar(255) NOT NULL,
				`zip` varchar(255) NOT NULL,
				`case_description` text NOT NULL,
				`case_type` bigint(20) NOT NULL,
				`case_status` enum('New','Processing','Completed') NOT NULL,
				`workflow_status` int(10) NOT NULL DEFAULT 0,				
				`case_resolution` text NOT NULL,
				`assign_to` bigint(20) NOT NULL,
				`user_id` bigint(20) NOT NULL,
				`org_id` bigint(20) NOT NULL,
				`is_active` tinyint(1) NOT NULL DEFAULT 1,
				`case_priority` tinyint(4) NOT NULL DEFAULT 1,
				`case_start_date` datetime DEFAULT NULL,
				`case_end_date` datetime DEFAULT NULL,
				`created_on` datetime NOT NULL,
				`updated_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`completed_date` datetime DEFAULT NULL,
				PRIMARY KEY(case_id)
				) $charset_collate;"
		);

		// Case Management - Comments Table
		array_push(
			$queries,
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}case_management_comments` (
				`comment_id` bigint(20) NOT NULL AUTO_INCREMENT,
				`comment_description` text NOT NULL,
				`case_id` bigint(20) NOT NULL,
				`user_id` bigint(20) NOT NULL,
				`created_date` datetime NOT NULL,
				`last_updated_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY(comment_id),
				CONSTRAINT {$wpdb->prefix}case_management_comments_ibfk_1 FOREIGN KEY (case_id)
    			REFERENCES {$wpdb->prefix}case_management_case(case_id)
			  ) $charset_collate;"
		);

		// Case Management - Case Type Table
		array_push(
			$queries,
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}case_management_case_type` (
				`case_type_id` bigint(20) NOT NULL AUTO_INCREMENT,
				`case_type` varchar(255) NOT NULL,
				`case_type_details` varchar(255) NOT NULL,
				`created_by` bigint(20) NOT NULL,
				`assing_to` bigint(20) NOT NULL,
				`is_active` tinyint(1) NOT NULL DEFAULT 1,
				`case_type_sla` int(11) NOT NULL DEFAULT 0,
				`created_date` datetime NOT NULL,
				`last_updated_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY(case_type_id)
			  ) $charset_collate;"
		);

		// Case Management - Workflow Type Table
		array_push(
			$queries,
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}case_management_workflow_list` (
				`workflow_id` bigint(20) NOT NULL,
				`case_type_id` bigint(20) NOT NULL,
				`workflow_item` text COLLATE utf8mb4_unicode_ci NOT NULL,
				`created_by` bigint(20) NOT NULL,
				`is_active` tinyint(1) NOT NULL DEFAULT 1,
				`created_date` datetime NOT NULL,
				`last_updated_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY(workflow_id),
				CONSTRAINT {$wpdb->prefix}case_management_workflow_list_ibfk_1 FOREIGN KEY (case_type_id)
    			REFERENCES {$wpdb->prefix}case_management_case_type(case_type_id)
				ON DELETE CASCADE
				) $charset_collate;"
		);

		// Case Management - Case Priority Table
		array_push(
			$queries,
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}case_management_priority` (
				`priority_id` bigint(20) NOT NULL,
				`priority` varchar(255) NOT NULL,
				`is_active` tinyint(1) NOT NULL DEFAULT 1,
				`created_by` bigint(20) NOT NULL DEFAULT 1,
				`created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				) $charset_collate;"
		);

		// Case Management - Attachments Table
		array_push(
			$queries,
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}case_management_attachments` (
				`attachment_id` bigint(20) NOT NULL AUTO_INCREMENT,
				`case_id` bigint(20) NOT NULL,
				`file_name` varchar(255) NOT NULL,
				`user_id` bigint(20) NOT NULL,
				`comment_id` bigint(20) NOT NULL,
				`attachment_type` enum('case','comment') NOT NULL DEFAULT 'case',
				`attachment_doc_type` varchar(255) NOT NULL,
				`created_date` datetime NOT NULL,
				`last_updated_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY(attachment_id),
				CONSTRAINT {$wpdb->prefix}case_management_attachments_ibfk_1 FOREIGN KEY (case_id)
    			REFERENCES {$wpdb->prefix}case_management_case(case_id)
			  ) $charset_collate;"
		);

		// Case Management - Configurations Table
		array_push(
			$queries,
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}case_management_configuration` (
				`config_id` bigint(20) NOT NULL,
				`org_id` bigint(20) NOT NULL DEFAULT 1,
				`email_from` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
				`scm_admin` bigint(20) NOT NULL,
				`email_to_user` text COLLATE utf8mb4_unicode_ci NOT NULL,
				`email_to_admin` text COLLATE utf8mb4_unicode_ci NOT NULL,
				`email_to_assignee` text COLLATE utf8mb4_unicode_ci NOT NULL,
				`email_to_case_complete` text COLLATE utf8mb4_unicode_ci NOT NULL,
				PRIMARY KEY(config_id)
			  )  $charset_collate;"
		);

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		foreach ( $queries as $key => $sql ) {
			dbDelta( $sql );
		}

		// Create plugin pages on activation
		$check_page_exist = get_page_by_title( 'SOV Case Management', 'OBJECT', 'page' );
		// Check if the page already exists
		if ( empty( $check_page_exist ) ) {
			$page_id = wp_insert_post(
				array(
					'comment_status' => 'close',
					'ping_status'    => 'close',
					'post_author'    => get_current_user_id(),
					'post_title'     => ucwords( 'SOV Case Management' ),
					'post_name'      => strtolower( str_replace( ' ', '-', trim( 'SOV Case Management' ) ) ),
					'post_status'    => 'publish',
					'post_content'   => '[sovcms_client_view]',
					'post_type'      => 'page',
					'post_parent'    => '',
				)
			);
		} else {
			// Make sure page only contains shortcode nothing else
			$existing_page_content = array(
				'ID'           => $check_page_exist->ID,
				'post_title'   => 'SOV Case Management',
				'post_content' => '[sovcms_client_view]',
			);
			$page_id               = wp_update_post( $existing_page_content );
		}
	}
}
