<div id="poststuff">
	<h2 style="background: #e5e5e9;padding: 25px;font-size: 21px;width: 41%;"> Email Template Configurations </h2>
		<form method="POST" id="config_control_form" class="config_control_form" onSubmit="return confirm('Are you sure?') "></form>
	<?php
		global $wpdb;
		$configArray = $wpdb->get_results(
			"SELECT * From {$wpdb->prefix}case_management_configuration
						WHERE config_id = 1"
		);

		foreach ( $configArray as $config_item ) {
			$email_from                           = $config_item->email_from;
			$scmSuperAdmin                        = $config_item->scm_admin;
			$caseManagementNewCaseCreated         = $config_item->email_to_user;
			$caseManagementNewCasePending         = $config_item->email_to_admin;
			$caseManagementNewCasePendingAssignee = $config_item->email_to_assignee;
			$caseManagementNewCaseComplete        = $config_item->email_to_case_complete;
		}

		if ( $scmSuperAdmin > 0 ) {
			// get selected admin user data here
			$author_info = get_userdata( $scmSuperAdmin );
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
		<table class="configuration-control-table table" role="presentation" style="width:40%;">           
			<tbody>   

			<tr class="form-field">
					<td colspan="1" >Email From:</td>
					<td colspan="5"> <input type="email" id="email_from" name="email_from" value="<?php echo esc_attr( isset( $email_from ) ? $email_from : 'imessenger@sovratec.com' ); ?>" class="email-from" form="config_control_form"></td>        
				</tr>

				<tr class="form-field">

					<td colspan="1" >Case Management Admin: </td>
					<td colspan="5"><select name="scmSuperAdmin" class="scmSuperAdmin" id="scmSuperAdmin" style="width: 70%" form="config_control_form">                         
					<?php
					if ( $user_data ) {
						echo '<option value="' . esc_attr( $user_data[0]['id'] ) . '">' . esc_attr( $user_data[0]['text'] ) . '</option>';
					} else {
						echo '<option value="0">--Select User--</option>';
					}
					?>
																						   
						</select>
					</td>               
				</tr> 

				<tr class="form-field">
					<td colspan="6" >1. Email Confirmation to User: </td>
				</tr>

				<tr class="form-field">
					<td colspan="6"> <textarea id="caseManagementNewCaseCreated" name="caseManagementNewCaseCreated" form="config_control_form"><?php echo esc_attr( isset( $caseManagementNewCaseCreated ) ? $caseManagementNewCaseCreated : '' ); ?></textarea> </td>
				</tr>

				<tr class="form-field">
					<td colspan="6" >2. Email Notification to Admin:</td>
				</tr>

				<tr class="form-field">
					<td colspan="6"> <textarea id="caseManagementNewCasePending" name="caseManagementNewCasePending" form="config_control_form"><?php echo esc_attr( isset( $caseManagementNewCasePending ) ? $caseManagementNewCasePending : '' ); ?></textarea> </td>
				</tr>

				<tr class="form-field">
					<td colspan="6" >3. Email Notification to Assignee:</td>
				</tr>

				<tr class="form-field">
					<td colspan="6"> <textarea id="caseManagementNewCasePendingAssignee" name="caseManagementNewCasePendingAssignee" form="config_control_form"><?php echo esc_attr( isset( $caseManagementNewCasePendingAssignee ) ? $caseManagementNewCasePendingAssignee : '' ); ?></textarea> </td>
				</tr>

				<tr class="form-field">
					<td colspan="6" >4. Email Notification when case was completed: </td>
				</tr>

				<tr class="form-field">
					<td colspan="6"> <textarea id="caseManagementNewCaseComplete" name="caseManagementNewCaseComplete" form="config_control_form"><?php echo esc_attr( isset( $caseManagementNewCaseComplete ) ? $caseManagementNewCaseComplete : '' ); ?></textarea> </td>
				</tr>

				<tr>  
					<td colspan="6" style="text-align: center;padding: 2rem 0;border-bottom: none!important;">
						<input type="submit" class="button button-primary button-large" value="Save Configurations" form="config_control_form">
						<input type="hidden" name="config_form_submitted" value="1" form="config_control_form" />
					</td>
				</tr>
			</tbody>                
		</table>     
		<div class="floating-tips">
			<h3> Dynamic Tags for case template</h3>
			<table class="email-tips" role="presentation">
				<tr>
					<td>
					<span>ID:</span> {{CASEID}}
					</td>
				</tr>
				<tr>
					<td>
					<span>Type:</span> {{CASETYPE}}
					</td>
				</tr>
				<tr>
					<td>
					<span>User Name:</span> {{NAME}} 
					</td>
				</tr>
				<tr>
					<td>
					<span>Assigned To:</span> {{CASEASSIGN}}
					</td>
				</tr>
				<tr>
					<td>
					<span>Status:</span> {{CASESTATUS}}
					</td>
				</tr>
				<tr>
					<td>
					<span>Workstatus:</span> {{WORKSTATUS}}
					</td>
				</tr>	
				<tr>
					<td>
					<span>Latest Comment:</span> {{COMMENT}}
					</td>
				</tr>
				<tr>
					<td>
					<span>Created On:</span> {{CASECREATED}}
					</td>
				</tr>
				<tr>
					<td>
					<span>Description:</span> {{DESCRIPTION}}
					</td>
				</tr>
				<tr>
					<td>
					<span>Resolution:</span> {{CASERESOLUTION}}
					</td>
				</tr>
				<tr>
					<td>
					<span>Completed Date:</span> {{CASECOMPLETEDATE}}
					</td>
				</tr>
			</table>
		</div>       
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($){

		let ajaxurl = "<?php echo esc_url( admin_url() . 'admin-ajax.php' ); ?>"; 
		let nonceWP = "<?php echo wp_create_nonce( 'ajax-nonce' ); ?>";

			jQuery('#scmSuperAdmin').select2({
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

	});
</script>
