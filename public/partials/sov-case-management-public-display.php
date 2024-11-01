<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://sovratec.com/
 * @since      1.0.0
 *
 * @package    Sov_Case_Management
 * @subpackage Sov_Case_Management/public/partials
 */

?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php require_once 'sov-case-management-functions.php'; ?>
<?php

// Only allow loggedin user to access
if ( ! is_user_logged_in() ) {
	?>
<p> Access is required to view Case Manager, please log in.</p>
<span> Redirecting...</span>
	<script><?php echo( "location.href = '" . esc_url( wp_login_url() ) . "';" ); ?></script>
<?php }

if ( isset( $_GET['action'] ) && ( sanitize_text_field( $_GET['action'] ) == 'view' ) ) {
	include 'sov-case-management-editcase-display.php';
} elseif ( isset( $_GET['action'] ) && ( sanitize_text_field( $_GET['action'] ) == 'new' ) ) {
	include 'sov-case-management-newcase-display.php';
} else {
	include 'sov-case-management-case-display.php';
}
?>
