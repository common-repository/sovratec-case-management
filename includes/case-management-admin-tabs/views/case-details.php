<?php
// check if action is valid
if ( empty( $_GET['action'] ) || empty( $_GET['id'] ) || empty( $_GET['tab'] ) ) {
	die( 'Invalid URL!!' );
}

// get case data
$caseData    = scm_admin_case_data( sanitize_text_field( $_GET['id'] ) );
$assignIDInt = intval( $caseData['data'][0]['Assign'] );
$user_data   = null;
$full_name   = '';
if ( $assignIDInt > 0 ) {
	// get assigned user data here
	$author_info = get_userdata( $assignIDInt );
	if ( ( ! $author_info->first_name ) || ( ! $author_info->last_name ) ) {
		$full_name = $author_info->display_name;
	} else {
		$full_name = $author_info->first_name . ' ' . $author_info->last_name;
	}

	$user_data[] = array(
		'id'   => $author_info->ID,
		'text' => $full_name,
	);
}

?>

<div class="main-form-heading" >
<div id="overlayWrap" class="overlayWrap"></div>
	<h2 style="text-align: left;padding: 2px 0px;font-size: 20px;font-weight: 600;margin-top: 35px;margin-left: -30%;">Case <?php echo esc_attr( '#' . sanitize_text_field( $_GET['id'] ) ); ?> Details</h2>
	<form method="POST" action="update_admin_case" id="caseview_form" class="caseview_form" ></form>
	<div id="message-error"> </div>
	<div id="message-success"> </div>

	<div class="row g-3 scm-case-container">
	<div class="case-number-left">Case No. <?php echo esc_attr( $caseData['data'][0]['ID'] ); ?></div>
		<fieldset class="border p-2">
			<legend  class="float-none w-auto p-2">User Details</legend>

			<div class="row col-md-12" >

				<div class="col-md-6 p-3">
						<label for="scm_firstname" class="form-label"><?php esc_attr_e( 'First Name', 'sov-case-management' ); ?></label>
						<input type="text" name="scm_firstname" class="scm_firstname form-control" id="scm_firstname" value="<?php echo esc_attr( $caseData['data'][0]['FirstName'] ); ?>"  readonly="readonly">
				</div>

				<div class="col-md-6 p-3">					
						<label for="scm_lastname" class="form-label"><?php esc_attr_e( 'Last Name', 'sov-case-management' ); ?></label>
						<input type="text" name="scm_lastname" class="scm_lastname form-control" id="scm_lastname" value="<?php echo esc_attr( $caseData['data'][0]['LastName'] ); ?>"  readonly="readonly">
				</div>

			</div>

			<div class="row col-md-12" >

				<div class="col-md-6 p-3">					
						<label for="scm_caseemail" class="form-label"><?php esc_attr_e( 'Email', 'sov-case-management' ); ?></label>
						<input type="text" name="scm_caseemail" class="scm_caseemail form-control" id="scm_caseemail" value="<?php echo esc_attr( $caseData['data'][0]['Email'] ); ?>"  readonly="readonly">
				</div>

				<div class="col-md-6 p-3">
						<label for="scm_casephone" class="form-label"><?php esc_attr_e( 'Phone', 'sov-case-management' ); ?></label>
						<input type="text" name="scm_casephone" class="scm_casephone form-control" id="scm_casephone" value="<?php echo esc_attr( $caseData['data'][0]['Phone'] ); ?>"  readonly="readonly">
				</div>

			</div>

			<div class="row col-md-12" >

				<div class="col-md-6 p-3">
					<label for="scm_addressone" class="form-label"><?php esc_attr_e( 'Address 1', 'sov-case-management' ); ?></label>
					<input type="text" class="scm_addressone form-control" id="scm_addressone" name="scm_addressone" placeholder="Address Line 1" value="<?php echo esc_attr( $caseData['data'][0]['Add1'] ); ?>"  readonly="readonly">
				</div>

				<div class="col-md-6 p-3">
					<label for="scm_addresstwo" class="form-label"><?php esc_attr_e( 'Address 2', 'sov-case-management' ); ?></label>
					<input type="text" class="scm_addresstwo form-control" id="scm_addresstwo" name="scm_addresstwo" placeholder="Address Line 2" value="<?php echo esc_attr( $caseData['data'][0]['Add2'] ); ?>"  readonly="readonly">
				</div>

			</div>

			<div class="row col-md-12" >

				<div class="col-md-6 p-3">
					<label for="scm_address_city" class="form-label"><?php esc_attr_e( 'City', 'sov-case-management' ); ?></label>
					<input type="text" class="scm_address_city form-control" id="scm_address_city" name="scm_address_city" placeholder="City" value="<?php echo esc_attr( $caseData['data'][0]['City'] ); ?>"  readonly="readonly">
				</div>

				<div class="col-md-3 p-3">
					<label for="scm_address_state" class="form-label"><?php esc_attr_e( 'State', 'sov-case-management' ); ?></label>
					<input type="text" class="scm_address_state form-control" id="scm_address_state" name="scm_address_state" placeholder="State" value="<?php echo esc_attr( $caseData['data'][0]['State'] ); ?>"  readonly="readonly">
				</div>

				<div class="col-md-3 p-3">
					<label for="scm_address_zip" class="form-label"><?php esc_attr_e( 'Zip', 'sov-case-management' ); ?></label>
					<input type="text" class="scm_address_zip form-control" id="scm_address_zip" name="scm_address_zip" placeholder="Zip" value="<?php echo esc_attr( $caseData['data'][0]['Zip'] ); ?>"  readonly="readonly">
				</div>

			</div>

				</fieldset> <!-- User Details Section Ends here -->

				<fieldset class="border p-2">
					   <legend  class="float-none w-auto p-2">Case Details</legend>
			<div class="row col-md-12" >

				<div class="col-md-6 p-3">
				<label for="scm_assignedto" class="form-label" ><?php esc_attr_e( 'Assigned To', 'sov-case-management' ); ?></label>					
				<select name="scm_assignedto" class="scm_assignedto form-control" id="scm_assignedto" form="caseview_form">  
					<?php
					if ( $user_data ) {
						echo '<option value="' . esc_attr( $user_data[0]['id'] ) . '">' . esc_attr( $user_data[0]['text'] ) . '</option>';
					} else {
						echo '<option value="0">--Select User--</option>';
					}
					?>
				</select>
			</div>	

				<div class="col-md-6 p-3">				 
							<label for="scm_casetype" class="form-label"><?php esc_attr_e( 'Case Type', 'sov-case-management' ); ?></label>				 
							<input type="text" name="scm_casetype" class="scm_casetype form-control" id="scm_casetype" value="<?php echo esc_attr( $caseData['data'][0]['Type'] ); ?>"  readonly="readonly">					                   
				</div>
			</div>
			
			<div class="row col-md-12" >

				<div class="col-md-6 p-3">
					
						<label for="scm_casestatus" class="form-label"><?php esc_attr_e( 'Case Status', 'sov-case-management' ); ?></label>
					
				<select name="scm_casestatus" class="scm_casestatus form-control" id="scm_casestatus"  form="caseview_form">					
					 <option value="Processing" 
					 <?php
						if ( $caseData['data'][0]['Status'] == 'Processing' ) {
							echo esc_attr( 'Selected' );}
						?>
						 >Processing</option>
					 <option value="Completed" 
					 <?php
						if ( $caseData['data'][0]['Status'] == 'Completed' ) {
							echo esc_attr( 'Selected' );}
						?>
						 >Completed</option>
						</select>					                     
				</div>


				<div class="col-md-6 p-3">
						<label for="scm_workflowstatus" class="form-label"><?php esc_attr_e( 'Workflow Status', 'sov-case-management' ); ?></label>
				<select name="scm_workflowstatus" class="scm_workflowstatus form-control" id="scm_workflowstatus"  form="caseview_form">  
					<option value="0">Please Select Workflow Status</option>
					 <?php

						$caseTypeIDVal = $caseData['data'][0]['CaseTypeID'];

						$workflow_data = $wpdb->get_results(
							$wpdb->prepare(
								"SELECT *
							FROM {$wpdb->prefix}case_management_workflow_list 
							WHERE case_type_id  = %d AND is_active = 1",
								$caseTypeIDVal
							)
						);
						// echo $caseData['data'][0]['WorkflowStatus'];
						foreach ( $workflow_data as $workflow_list_item ) {

							$is_selected = ( $caseData['data'][0]['WorkflowStatus'] == $workflow_list_item->workflow_id ) ? 'selected' : '';

							echo '<option value="' . esc_attr( $workflow_list_item->workflow_id ) . '" ' . esc_attr( $is_selected ) . '>' . esc_attr( $workflow_list_item->workflow_item ) . '</option>';
						}
						?>
						</select>
										 
				</div>

			</div>

			<div class="row col-md-12" >

				<div class="col-md-6 p-3">
						<label for="scm_casepriority" class="form-label"><?php esc_attr_e( 'Case Priority', 'sov-case-management' ); ?></label>					
				<select name="scm_casepriority" class="scm_casepriority form-control" id="scm_casepriority" form="caseview_form">  
					<option value="0">Please Select Case Priority</option>
					 <?php

						$casePriorityDVal = $caseData['data'][0]['CasePriority'];

						$priority_data = $wpdb->get_results(
							"SELECT *
							FROM {$wpdb->prefix}case_management_priority 
							WHERE is_active = 1"
						);
						foreach ( $priority_data as $priority_item ) {

							$is_selected = ( $caseData['data'][0]['CasePriority'] == $priority_item->priority_id ) ? 'selected' : '';

							echo '<option value="' . esc_attr( $priority_item->priority_id ) . '" ' . esc_attr( $is_selected ) . '>' . esc_attr( $priority_item->priority ) . '</option>';
						}
						?>
						</select>
										 
				</div>

				

			</div>

			<div class="row col-md-12" >
			
				<div class="col-md-6 p-3">
						<label for="scm_case_start_date" class="form-label"><?php esc_attr_e( 'Start Date', 'sov-case-management' ); ?></label>
						<input type="text" name="scm_case_start_date" 	class="scm_case_start_date form-control" id="scm_case_start_date"  value="<?php echo ( $caseData['data'][0]['CaseStart'] != null ) ? esc_attr( date( 'm/d/Y H:i', strtotime( $caseData['data'][0]['CaseStart'] ) ) ) : ''; ?>" form="caseview_form" disabled>
				</div>

				<div class="col-md-6 p-3">					
					<label for="scm_case_end_date" class="form-label"><?php esc_attr_e( 'End Date', 'sov-case-management' ); ?></label>					
					<input type="text" name="scm_case_end_date" 	class="scm_case_end_date form-control" 	id="scm_case_end_date" value="<?php echo ( $caseData['data'][0]['CaseEnd'] != null ) ? esc_attr( date( 'm/d/Y H:i', strtotime( $caseData['data'][0]['CaseEnd'] ) ) ) : ''; ?>" form="caseview_form" disabled>
				</div>

			</div>

				<div class="col-md-12 p-3">					
						<label for="scm_case_description_admin" class="form-label"><?php esc_attr_e( 'Description', 'sov-case-management' ); ?></label>					
						<textarea name="scm_case_description_admin_noedit" class="scm_case_description_admin_noedit mceNonEditable form-control" id="scm_case_description_admin_noedit"  rows="4" cols="8"  readonly><?php echo esc_attr( $caseData['data'][0]['Description'] ); ?> </textarea>					                    
				</div>

				<div class="col-md-12 p-3">
						<label for="scm_case_attachments" class="form-label"><?php esc_attr_e( 'Attachments', 'sov-case-management' ); ?></label>					
						<ul class="file-list form-control">

						<?php
						$case_id_files = sanitize_text_field( $_GET['id'] );
						$counter_file  = 0;
						$FilesArray    = $wpdb->get_results(
							$wpdb->prepare(
								"SELECT *
							FROM {$wpdb->prefix}case_management_attachments
							WHERE case_id = %d  AND attachment_type = 'case'",
								$case_id_files
							)
						);

						foreach ( $FilesArray as $file ) {
							$year      = date( 'Y', strtotime( $file->created_date ) );
							$month     = date( 'm', strtotime( $file->created_date ) );
							$customdir = 'case_management/public/uploads/' . $year . '/' . $month . '/';
							$dirPath   = plugin_dir_url( '../' ) . $customdir . ( $file->file_name );

							echo '<li>' . esc_attr( ( ++$counter_file ) ) . '. <a target="_blank" href="' . esc_attr( $dirPath ) . '">' . esc_attr( $file->file_name ) . '</a></li>';
						}
						if ( empty( $FilesArray ) ) {
							echo '<li>No Attachments!</li>';
						}
						?>
							</ul>				                 
				</div>

					</fieldset> <!-- Case Details Ends Here -->

				<fieldset class="border p-2">
					<legend  class="float-none w-auto p-2">Case Activity</legend>
				<div class="col-md-12 p-3">
						<label for="scm_case_comments" class="form-label"><?php esc_attr_e( 'Comments', 'sov-case-management' ); ?></label>
					<?php
					// Check if there is any previous comments
					$case_id_files = sanitize_text_field( $_GET['id'] );
					$allowed_html  = array(
						'a'     => array(
							'href'   => array(),
							'title'  => array(),
							'target' => array(),
						),
						'table' => array(),
						'td'    => array(),
						'tr'    => array(),
						'ul'    => array(),
						'li'    => array(),
					);

					$comments_lists = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT * FROM
								{$wpdb->prefix}case_management_comments
							WHERE case_id = %d ORDER BY created_date DESC",
							$case_id_files
						)
					);

					if ( ! empty( $comments_lists ) ) {

						echo '<div class="comment-load-area" id="comment-load-area"><table class="comment-list" id="comment-list-table" summary="Comments listing table"><tr><th scope="col">Date</th><th scope="col">Comments</th><th scope="col">Attachments</th></tr>';

						foreach ( $comments_lists as $comment ) {

							$comment_date     = date( 'M d, Y H:i', strtotime( $comment->created_date ) );
							$comment_val      = $comment->comment_description;
							$files_list       = null;
							$comment_id_query = $comment->comment_id;
							$author_info      = get_userdata( $comment->user_id );
							if ( ( ! $author_info->first_name ) || ( ! $author_info->last_name ) ) {
								$full_name = $author_info->display_name;
							} else {
								$full_name = $author_info->first_name . ' ' . $author_info->last_name;
							}

							// output comment date and comment itself
							echo '<tr>
						<td>' . esc_attr( $full_name ) . ' on ' . esc_attr( $comment_date ) . '</td>
						<td>' . esc_attr( $comment_val ) . '</td>';

							// query files associated with the current case
							$FilesArray = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT * FROM
									{$wpdb->prefix}case_management_comments wc
									INNER JOIN {$wpdb->prefix}case_management_attachments wa ON
									wc.comment_id = wa.comment_id
									WHERE wc.comment_id = %d  AND wa.attachment_type = 'comment'",
									$comment_id_query
								)
							);

							// get ready the files list
							if ( $FilesArray ) {
								$counter_file = 0;
								$files_list   = '<ul class="comment-files-listing" >';

								foreach ( $FilesArray as $file ) {
									$year      = date( 'Y', strtotime( $file->created_date ) );
									$month     = date( 'm', strtotime( $file->created_date ) );
									$customdir = 'case_management/public/uploads/' . $year . '/' . $month . '/';
									$dirPath   = plugin_dir_url( '../' ) . $customdir . ( $file->file_name );


									$files_list .= '<li>' . ( ++$counter_file ) . '. <a target="_blank" href="' . $dirPath . '">' . $file->file_name . '</a></li>';

								}

								$files_list .= '</ul>';

								echo '<td>' . wp_kses( $files_list, $allowed_html ) . '</td></tr>';

							} else {
								echo '<td><ul class="comment-files-listing"><li> No Attachments! </li></ul></td>';
							}
						}
						// closing all tags
						echo '</table></div>';
					}
					?>
						<textarea name="scm_case_comments" class="scm_case_comments form-control" id="scm_case_comments"  rows="4" cols="8" ></textarea>

						<input type="file" name="attachments" id="attachments" class="attachments multi" maxlength="5" data-maxfile="10240" multiple>
						<button id="submit-comment" class="submit-comment button button-primary button-large" style="margin-top: -30px;width: 90px;margin-right: 70px;float: right;" value="Publish">Comment</button>
						<div id="message-success-comment" ></div>
										
				</div>

				<div class="col-md-12 p-3">
					
						<label for="scm_case_recent_update" class="form-label"><?php esc_attr_e( 'Latest Action', 'sov-case-management' ); ?></label>
				<?php
					$RecentUpdate = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT * FROM
									{$wpdb->prefix}case_management_case
								WHERE case_id  = %d LIMIT 1",
							$case_id_files
						)
					);

					foreach ( $RecentUpdate as $update ) {

						// get assigned user data here
						$author_info = get_userdata( $update->user_id );
						if ( ( ! $author_info->first_name ) || ( ! $author_info->last_name ) ) {
							$full_name = $author_info->display_name;
						} else {
							$full_name = $author_info->first_name . ' ' . $author_info->last_name;
						}

						echo '<div class="recent-update form-control" >This case is created by ' . esc_attr( $full_name ) . ' on ' . esc_attr( date( 'M d, Y H:i', strtotime( $update->created_on ) ) ) . ' and last updated on ' . esc_attr( date( 'M d, Y H:i', strtotime( $update->updated_on ) ) ) . '.</div>';
					}
					?>
										
				</div>

				<div class="col-md-12 p-3">
					<label for="scm_case_resolution" class="form-label"><?php esc_attr_e( 'Case Resolution', 'sov-case-management' ); ?></label>
					<textarea name="scm_case_resolution" class="scm_case_resolution form-control" id="scm_case_resolution"  rows="4" cols="8" form="caseview_form"><?php echo esc_attr( $caseData['data'][0]['CaseResolution'] ); ?></textarea>
				</div>


				</fieldset> <!-- Case Activity -->

				<div class="col-md-9 p-3">
						<input type="submit" style="margin-top: 16px;margin-left: 10px;width: 90px;" name="caseview_submit" class="button button-primary button-large" value="Update" form="caseview_form" > 
						<input type="reset"  style="margin-top: 16px;margin-left: 10px;width: 90px;background: #2271b1;" value="Back" class="button button-primary button-large" onclick="javascript:window.location = '<?php echo esc_url( admin_url( 'admin.php?page=sov-case-management-dashboard&tab=' . sanitize_text_field( $_GET['tab'] ) ) ); ?>'" form="caseview_form" >						
						<input type="hidden" id="return_url" class="return_url" name="return_url" value="<?php echo esc_url( admin_url( 'admin.php?page=sov-case-management-dashboard&tab=' . sanitize_text_field( $_GET['tab'] ) ) ); ?>"  form="caseview_form">
						<input type="hidden" id="selectedAssignTo" class="selectedAssignTo" name="selectedAssignTo" value="<?php echo esc_attr( $caseData['data'][0]['Assign'] ); ?>" form="caseview_form" >
						<input type="hidden" id="caseID" class="caseID" name="caseID" value="<?php echo esc_attr( sanitize_text_field( $_GET['id'] ) ); ?>" form="caseview_form" >						
						<input type="hidden" id="current_user_id" class="current_user_id" name="current_user_id" value="<?php echo esc_attr( get_current_user_id() ); ?>" form="caseview_form" >
						<input type="hidden" id="current_user_name" class="current_user_name" name="current_user_name" value="<?php echo esc_attr( $full_name . ' on ' . date( 'M d, Y H:i' ) ); ?>" form="caseview_form" >					 
						<div id="loader" class="loader" style="display: none"></div>               
				</div>

	</div>

	<?php
	// supportive functions
	function scm_admin_case_data( $caseIDargs = null ) {
		global $wpdb;
		$current_user = get_current_user_id();
		$data         = array();

				$caseArray = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT casetable.case_id as ID, 
						casetype.case_type as CaseTypeName,
						casetype.case_type_id as CaseTypeID, 
						casetable.first_name as CaseFirstName,
						casetable.last_name  as CaseLastName,						
						casetable.email as CaseEmail,
						casetable.address_1 as CaseAdd1,
						casetable.address_2 as CaseAdd2,
						casetable.city as CaseAddCity,
						casetable.state as CaseAddState,
						casetable.zip as CaseAddZip,
						casetable.assign_to as CaseAssign,
						casetable.phone_no as CasePhone,
						casetable.case_status as CaseStatus, 
						casetable.workflow_status as WorkflowStatus, 
						casetable.case_resolution as CaseResolution,
						casetable.case_priority as CasePriority, 
						casetable.case_start_date as CaseStart, 
						casetable.case_end_date as CaseEnd, 
						casetable.is_active as CaseActive, 
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
			$caseTypeID      = $case->CaseTypeID;
			$caseFirstName   = $case->CaseFirstName;
			$caseLastName    = $case->CaseLastName;
			$caseEmail       = $case->CaseEmail;
			$caseAdd1        = $case->CaseAdd1;
			$caseAdd2        = $case->CaseAdd2;
			$caseAddCity     = $case->CaseAddCity;
			$caseAddState    = $case->CaseAddState;
			$caseAddZip      = $case->CaseAddZip;
			$caseAssign      = $case->CaseAssign;
			$casePhone       = $case->CasePhone;
			$dtime           = new DateTime( $case->created_on );
			$caseCreated     = $dtime->format( 'M d, Y' );
			$caseStatus      = $case->CaseStatus;
			$workflowStatus  = $case->WorkflowStatus;
			$caseResolution  = $case->CaseResolution;
			$CasePriority    = $case->CasePriority;
			$CaseStart       = $case->CaseStart;
			$CaseEnd         = $case->CaseEnd;
			$caseActive      = $case->CaseActive;
			$caseDescription = $case->CaseDescription;

			$data[] = array(
				'ID'             => $caseID,
				'Type'           => $caseType,
				'CaseTypeID'     => $caseTypeID,
				'FirstName'      => $caseFirstName,
				'LastName'       => $caseLastName,
				'Email'          => $caseEmail,
				'Add1'           => $caseAdd1,
				'Add2'           => $caseAdd2,
				'City'           => $caseAddCity,
				'State'          => $caseAddState,
				'Zip'            => $caseAddZip,
				'Assign'         => $caseAssign,
				'Active'         => $caseActive,
				'Phone'          => $casePhone,
				'Status'         => $caseStatus,
				'WorkflowStatus' => $workflowStatus,
				'CaseResolution' => $caseResolution,
				'CasePriority'   => $CasePriority,
				'CaseStart'      => $CaseStart,
				'CaseEnd'        => $CaseEnd,
				'caseCreated'    => $caseCreated,
				'Description'    => $caseDescription,
			);
		}

		$response['data']         = ! empty( $data ) ? $data : array();
		$response['recordsTotal'] = ! empty( $data ) ? count( $data ) : 0;

		return $response;
	}

	?>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		
		let ajaxurl = "<?php echo esc_url( admin_url() . 'admin-ajax.php' ); ?>"; 
		let nonceWP = "<?php echo wp_create_nonce( 'ajax-nonce' ); ?>";

		let file_location = "<?php
		$y         = date( 'Y', strtotime( date( 'y-m-d' ) ) );
		$m         = date( 'm', strtotime( date( 'y-m-d' ) ) );
		$customdir = 'case_management/public/uploads/' . $y . '/' . $m . '/';
		echo esc_url( plugin_dir_url( '../' ) . $customdir );
		?>";
		
		jQuery("#caseview_form").submit(function(e) {
				e.preventDefault(); // avoid to execute the actual submit of the form.

				var form = jQuery(this);
				var actionUrl = form.attr('action');
				var returnUrl = jQuery('#return_url').val();
				let scm_case_resolution = jQuery('textarea#scm_case_resolution').val();
				
				jQuery.ajax({
				type:   "POST",
				dataType:   'json',
				url:    ajaxurl, 
				data: { 
						action: actionUrl, 
						case_data:  form.serialize(),
						nonce: nonceWP,
						scm_case_resolution : scm_case_resolution					
					},
					beforeSend: function(){
						jQuery("#overlayWrap").show();
						jQuery("#loader").show();
					},
					success: function(response){
						if(response){

							jQuery("#message-success").html('<div class="alert alert-success alert-dismissible fade show"><?php esc_attr_e( 'Case is updated!', 'sov-case-management' ); ?><button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
							jQuery("#overlayWrap").hide(); 
							jQuery("#loader").hide();
							jQuery("#message-success").html();

							//redirect to previous main menu
							window.setTimeout(
								function () {
									jQuery("#message-success").html('<div class="alert alert-success alert-dismissible fade show"><?php esc_attr_e( 'Redirecting to home...', 'sov-case-management' ); ?><button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');		
									window.location.href = returnUrl; 
								},
									3000
								);	
						}
						}//success function close 

				});//ajax call close
				
			});//form submission close       

			//select2 lib
			jQuery('#scm_assignedto').select2({
				theme: "classic",
				width: 'resolve', // need to override the changed default
				ajax: { 
					url: ajaxurl,
					type: "post",
					dataType: 'json',
					delay: 250,
					data: function (params) {
					 return {
					   searchTerm: params.term, // search term
					   action: 'get_members_list',
					   nonce: nonceWP
					 };
					},
					processResults: function (response) {
					  return {
						 results: response
					  };
					},
					cache: true
				}
			}); //select 2 support 

	//Comment submission script
	jQuery("#submit-comment").on('click',function(e) {
			e.preventDefault(); // avoid to execute the actual submit of the form.
			var data = new FormData();
			var tableRow = '';

			//Form data append
			data.append('scm_case_comments', jQuery('#scm_case_comments').val() );
			data.append('scm_case_id', jQuery('#caseID').val() );
			data.append('scm_current_user_id', jQuery('#current_user_id').val() );
			data.append('nonce', nonceWP );

			//File data
			var file_data = $('input[name="attachments"]')[0].MultiFile.files;
			for (var i = 0; i < file_data.length; i++) {
				data.append("attachments[]", file_data[i]);
			}

			//Custom data
			data.append('action', 'save_comment');

			jQuery.ajax({
				url: ajaxurl,
				method: "post",
				processData: false,
				contentType: false,
				data: data,
				beforeSend: function(){
							jQuery("#overlayWrap").show();
							jQuery("#loader").show();
						},
						success: function(response){
							const obj = JSON.parse(response);
							counter_file  = 0;
							

							tableRow = '<tr id="new-comment-tr" class="blinker">';
							tableRow +='<td>'+jQuery('#current_user_name').val()+'</td>';
							tableRow +='<td>'+jQuery('#scm_case_comments').val()+'</td>';
							tableRow +='<td>';
							tableRow +='<ul class="comment-files-listing">';							
							
							//Insert all files in table 
							if(obj.file_status == true){
								tableRow += '<li> No Attachments! </li>';
							}else{
								for(var i=0; i < obj.file_status.length; i++) {
									tableRow += '<li> '+( ++counter_file )+'. <a target="_blank" href="'+file_location+obj.file_status[i]+'">'+obj.file_status[i]+'</a></li>';
								}

							}

							
							tableRow += '</ul></td></tr>';

							//Check if table row exsits
							if(jQuery("table#comment-list-table tr").length == 0){
								//Create table before comment 
								tableRowTemp = '<div class="comment-load-area" id="comment-load-area"><table class="comment-list" id="comment-list-table" summary="Comment listing table"><tr><th scope="col">Date</th><th scope="col">Comments</th><th scope="col">Attachments</th></tr></table></div>';
								jQuery(tableRowTemp).insertBefore("#scm_case_comments");
							}
							
							jQuery(tableRow).insertAfter("table#comment-list-table tr:first");

							//blink effect
							setInterval(function(){
								jQuery('tr#new-comment-tr').removeClass("blinker");
								jQuery('.alert-success').val('');
							},4000);

							//Reset Password 
							jQuery('#scm_case_comments').val('');
							jQuery('.MultiFile-list').empty();

							jQuery("#message-success-comment").html('<div class="alert-success"><?php esc_attr_e( 'Comment has been published!', 'sov-case-management' ); ?></div>');
							jQuery("#overlayWrap").hide(); 
							jQuery("#loader").hide();					
						},
						error: function (e) {
						//error
						console.log('Error '+e);
				} 

			});//ajax call close

	});//comment section close

});//Comment submission script close

</script>
