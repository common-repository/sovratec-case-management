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
?>
 <div class="wrap">

	  <div id="poststuff">
		<div class="config-action-button">
		<a href="<?php echo esc_attr( get_admin_url( get_current_blog_id(), 'admin.php?page=email_management' ) ); ?>" class="config_button button button-primary button-large" > Email Template </a>
		<a href="<?php echo esc_attr( get_admin_url( get_current_blog_id(), 'admin.php?page=workflowstatus_management' ) ); ?>" class="config_button button button-primary button-large" > Workflow Status </a>
		<a href="<?php echo esc_attr( get_admin_url( get_current_blog_id(), 'admin.php?page=casetype_management' ) ); ?>" class="config_button button button-primary button-large" > Case Types And Assignments </a>		
		</div>
	  </div>
	</div>
