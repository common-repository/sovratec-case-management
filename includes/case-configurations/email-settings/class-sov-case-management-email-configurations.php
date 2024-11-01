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
class Sov_Case_Management_CaseType {

	function __construct() {
		$this->configurationFormDisplay();
	}

	public function configurationFormDisplay() {
		global $wpdb;
		if ( isset( $_POST['config_form_submitted'] ) ) {
			$saveStatus    = $this->save_config_data( $_POST );
			$nonce         = wp_create_nonce( 'my-nonce' );
			$approveAction = admin_url( 'admin.php?page=email_management&nonce=' . $nonce );
			esc_attr_e( 'Please wait...', 'woo-return-exchange' );
			wp_redirect( $approveAction );
			exit;
		} else {
			$configDataArray = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wp_return_exchange_configuration WHERE config_id = 1" ) );
			?>
<div class="wrap">

			<?php
			$nonce = sanitize_text_field( $_REQUEST['nonce'] );
			if ( wp_verify_nonce( $nonce, 'my-nonce' ) ) {
				$message = 'Configurations has been Updated!';
			} else {
				$message = '';
			}
			?>

			<?php
			if ( $message ) :
				?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
	<strong>Success!</strong> <?php echo esc_attr( $message ); ?>
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	</div>
				<?php
			endif;
			include_once plugin_dir_path( __FILE__ ) . '/views/add-emailtemplate.php';
		}//end of if-else
	}

	public function save_config_data( $config_data ) {
		global $wpdb;
		$flag = 0;
		if ( isset( $config_data['config_form_submitted'] ) ) {
			// create record if not exists
			$recordExist = $wpdb->get_var( "SELECT config_id FROM {$wpdb->prefix}case_management_configuration WHERE config_id = 1" );
			if ( ! $recordExist ) {
				$returnStatus = $wpdb->insert(
					$wpdb->prefix . 'case_management_configuration',
					array(
						'email_from'             => $config_data['email_from'],
						'scm_admin'              => $config_data['scmSuperAdmin'],
						'email_to_user'          => $config_data['caseManagementNewCaseCreated'],
						'email_to_admin'         => $config_data['caseManagementNewCasePending'],
						'email_to_assignee'      => $config_data['caseManagementNewCasePendingAssignee'],
						'email_to_case_complete' => $config_data['caseManagementNewCaseComplete'],
					)
				);
			} else {
				$returnStatus = $wpdb->update(
					$wpdb->prefix . 'case_management_configuration',
					array(
						'email_from'             => $config_data['email_from'],
						'scm_admin'              => $config_data['scmSuperAdmin'],
						'email_to_user'          => $config_data['caseManagementNewCaseCreated'],
						'email_to_admin'         => $config_data['caseManagementNewCasePending'],
						'email_to_assignee'      => $config_data['caseManagementNewCasePendingAssignee'],
						'email_to_case_complete' => $config_data['caseManagementNewCaseComplete'],
					),
					array(
						'config_id' => 1,
					)
				);
			}

			if ( ! ( $returnStatus == false ) ) {
				$flag = 1;
			} else {
				$flag = 0;
			}

			return ( $flag == 1 ) ? 1 : 0;
		}
	}//end save_config_data()

}

$wp_list_table = new Sov_Case_Management_CaseType();
