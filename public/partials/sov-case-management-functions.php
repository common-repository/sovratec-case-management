<?php

/**
 * Collection of all functions and dependencies
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
<?php

function scm_case_data( $caseIDargs = null ) {
	global $wpdb;
	$current_user = get_current_user_id();
	$data         = array();
	if ( $caseIDargs == null ) {
		// Get all cases
		$caseArray = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT casetable.case_id as ID, 
				casetype.case_type as CaseTypeName, 
				CONCAT(casetable.first_name, ' ', casetable.last_name) as CaseFullName,
				casetable.email as CaseEmail,
				casetable.case_status as CaseStatus, 
				casetable.case_description as CaseDescription 
				FROM {$wpdb->prefix}case_management_case casetable
				INNER JOIN {$wpdb->prefix}case_management_case_type casetype
				ON casetable.case_type = casetype.case_type_id 
				WHERE casetable.user_id = %d",
				$current_user
			)
		);

		foreach ( $caseArray as $case ) {
			$caseID          = $case->ID;
			$caseType        = $case->CaseTypeName;
			$caseName        = $case->CaseFullName;
			$caseEmail       = $case->CaseEmail;
			$dtime           = new DateTime( $case->created_on );
			$caseCreated     = $dtime->format( 'M d, Y' );
			$caseStatus      = $case->CaseStatus;
			$caseDescription = $case->CaseDescription;
			$data[]          = array(
				'ID'          => $caseID,
				'Type'        => $caseType,
				'Name'        => $caseName,
				'Email'       => $caseEmail,
				'Status'      => $caseStatus,
				'caseCreated' => $caseCreated,
				'Description' => $caseDescription,
			);
		}
	} else {

			$caseArray = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT casetable.case_id as ID, 
					casetype.case_type as CaseTypeName, 
					CONCAT(casetable.first_name, ' ', casetable.last_name) as CaseFullName,
					casetable.email as CaseEmail,
					casetable.case_status as CaseStatus, 
					casetable.case_description as CaseDescription 
					FROM {$wpdb->prefix}case_management_case casetable
					INNER JOIN {$wpdb->prefix}case_management_case_type casetype
					ON casetable.case_type = casetype.case_type_id 
						WHERE casetable.case_id = %d",
					$caseIDargs
				)
			);

		foreach ( $caseArray as $case ) {
			$caseID          = $case->ID;
			$caseType        = $case->CaseTypeName;
			$caseName        = $case->CaseFullName;
			$caseEmail       = $case->CaseEmail;
			$dtime           = new DateTime( $case->created_on );
			$caseCreated     = $dtime->format( 'M d, Y' );
			$caseStatus      = $case->CaseStatus;
			$caseDescription = $case->CaseDescription;

			$data[] = array(
				'ID'          => $caseID,
				'Type'        => $caseType,
				'Name'        => $caseName,
				'Email'       => $caseEmail,
				'Status'      => $caseStatus,
				'caseCreated' => $caseCreated,
				'Description' => $caseDescription,
			);
		}
	}

	$response['data']         = ! empty( $data ) ? $data : array();
	$response['recordsTotal'] = ! empty( $data ) ? count( $data ) : 0;

	return $response;
}

// Check if case exist or not
function is_scm_case_exist( $caseID ) {
	global $wpdb;
	$caseCountAndData = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) AS case_id_count  FROM {$wpdb->prefix}case_management_case WHERE case_id  = %d", $caseID ) );
	return $caseCountAndData;
}

function scm_case_types() {
	global $wpdb;
	$caseTypeArray = $wpdb->get_results( "SELECT case_type_id, case_type FROM {$wpdb->prefix}case_management_case_type WHERE is_active = 1" );
	return $caseTypeArray;
}
