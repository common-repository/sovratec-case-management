<div class="main-form-heading">
<div id="overlayWrap" class="overlayWrap"></div>

<?php
global $wpdb;
$edit_view_flag = 0;

if ( $_GET['action'] == 'edit' && ! empty( $_GET['id'] ) ) {
	$edit_view_flag = 1;
	$workstatus_id  = sanitize_text_field( $_GET['id'] );
	$current_user   = get_current_user_id();



	$workstatusArray = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * From {$wpdb->prefix}case_management_workflow_list
					WHERE workflow_id  = %d",
			$workstatus_id
		)
	);

	foreach ( $workstatusArray as $workstatus_item ) {
		$case_type_id  = $workstatus_item->case_type_id;
		$workflow_item = $workstatus_item->workflow_item;
		$is_active     = $workstatus_item->is_active;
	}
	$case_type_name = $wpdb->get_var( $wpdb->prepare( "SELECT case_type FROM {$wpdb->prefix}case_management_case_type WHERE case_type_id = %d", $case_type_id ) );
}
?>
	<h2 style="text-align: left;padding: 2px 0px;font-size: 20px;font-weight: 600;margin-top: 35px;"><?php echo esc_attr( ( $edit_view_flag > 0 ) ? 'Edit workstatus #' . $workstatus_id : 'Add New Workstatus' ); ?></h2>
	<div id="message-error"> </div>
	<div id="message-success"> </div>
	<div class="col-sm-6 com-md-6">
	<table class="form-table" summary="New workstatus table">
			<tbody>
				<tr class="form-field">
					<th scope="col">
						<label for="scm_workstatus"><?php esc_attr_e( 'Workstatus Item', 'sov-case-management' ); ?></label>
					</th>
					<td>
						<input type="text" name="scm_workstatus" class="scm_workstatus" id="scm_workstatus" value="<?php echo esc_attr( isset( $workflow_item ) ? $workflow_item : '' ); ?>" 
																														<?php
																														if ( $edit_view_flag > 0 ) {
																															echo esc_attr( 'readonly=readonly' ); }
																														?>
						 >
					</td>                     
				</tr>

				<tr class="form-field">
					<th scope="col">
						<label for="scm_casetype"><?php esc_attr_e( 'Case Type', 'sov-case-management' ); ?></label>
					</th>
					<td><select name="scm_casetype" class="scm_casetype" id="scm_casetype" style="width: 95%" >                         
					<?php
					if ( $case_type_id ) {
						echo '<option value="' . esc_attr( $case_type_id ) . '">' . esc_attr( $case_type_name ) . '</option>';
					} else {
						echo '<option value="0">--Select Case Type--</option>';
					}
					?>
																					   
						</select>
					</td>               
				</tr> 

				<tr class="form-field">
					<th scope="col">
						<label for="scm_case_active"><?php esc_attr_e( 'Active', 'sov-case-management' ); ?></label>
					</th>
					<td class="form-inline">                        
						<input type="checkbox" name="scm_case_active" id="scm_case_active" class="scm_case_active"  <?php echo esc_attr( ( $is_active > 0 ) ? 'checked' : '' ); ?> >
					</td>                    
				</tr>

				<tr class="form-field">                   
					<td class="form-inline" style="display: contents;">
						<button id="add-workflow" style="margin-top: 16px;margin-left: 10px;width: 90px;" name="casetype_submit" class="button button-primary button-large"  > Save </button>  
						<input type="reset"  style="margin-top: 16px;margin-left: 10px;width: 90px;background: #2271b1;" value="Cancel" class="button button-primary button-large" onclick="javascript:window.location = '<?php echo esc_url( admin_url( 'admin.php?page=workflowstatus_management' ) ); ?>'"  >		
						<input type="hidden" id="created_by_id" class="created_by_id" name="created_by_id" value="<?php echo esc_attr( get_current_user_id() ); ?>"  >
						<input type="hidden" id="is_new_workstatus" class="is_new_workstatus" name="is_new_workstatus" value="<?php echo esc_attr( $edit_view_flag ); ?>"  >
						<input type="hidden" id="return_url" class="return_url" name="return_url" value="<?php echo esc_url( admin_url( 'admin.php?page=workflowstatus_management' ) ); ?>"  >
						<div id="loader" class="loader" style="display: none"></div>
					</td>                    
				</tr>              
			</tbody>
		</table>
				</div>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function($){

		let ajaxurl = "<?php echo esc_url( admin_url() . 'admin-ajax.php' ); ?>"; 
		let nonceWP = "<?php echo wp_create_nonce( 'ajax-nonce' ); ?>";

			jQuery('#scm_casetype').select2({
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
					   action: 'get_casetype_list'
					 };
					},
					processResults: function (response) {
					  return {
						 results: response
					  };
					},
					cache: true
				}
			}				
			); //select 2 support 


		//Workflow submission script
		jQuery("#add-workflow").on('click',function(e) {
			e.preventDefault(); // avoid to execute the actual submit of the form.
			var data = new FormData();
			var tableRow = '';
			var returnUrl = jQuery('#return_url').val();
			var is_readirect = 0;

			//Form data append
			data.append('scm_workflow_item', jQuery('#scm_workstatus').val() );
			data.append('scm_case_type_id', jQuery('#scm_casetype').val() );
			data.append('scm_created_by', jQuery('#created_by_id').val() );
			data.append('scm_active', jQuery('#scm_case_active').is(":checked") );
			data.append('scm_is_new', jQuery('#is_new_workstatus').val() );
			data.append('nonce', nonceWP );

		 

			//Custom data
			data.append('action', 'save_workflow_list');

			if( ( jQuery('#scm_casetype').val() == '' || jQuery('#scm_casetype').val() == '0' ) ){
				alert('Please select valid case type!');
				jQuery('#scm_casetype').focus();
				return false;
			}

			if( ( jQuery('#scm_workstatus').val() == '' || jQuery('#scm_workstatus').val() == '0' ) ){
				alert('Please enter valid workstatus item!');
				jQuery('#scm_workstatus').focus();
				return false;
			}

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
					//parse JSON
					responseObj = jQuery.parseJSON(response);

					//console.log(responseObj.response);

						//if workstatus is already existed
						if(responseObj.response == 'existed'){
							jQuery("#overlayWrap").hide();
							jQuery("#loader").hide();
							jQuery("#message-success").html();	
							jQuery("#message-success").html('<div class="alert alert-danger alert-dismissible fade show"><?php esc_attr_e( 'Workstatus is already existed!', 'sov-case-management' ); ?><button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
							is_readirect = 0;
						}

						//If a workstatus is updated
						if(responseObj.response == 'updated'){
							jQuery("#message-success").html('<div class="alert alert-success alert-dismissible fade show"><?php esc_attr_e( 'Updated the Workstatus #' . sanitize_text_field( $_GET['id'] ), 'sov-case-management' ); ?><button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
							is_readirect = 1;
						}

						//If a new workstatus is created in system
						if(responseObj.response == 'new_workstatus'){
							jQuery("#message-success").html('<div class="alert alert-success alert-dismissible fade show"><?php esc_attr_e( 'Created A New Workstatus!', 'sov-case-management' ); ?><button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
							is_readirect = 1;
						}

						//If there are errors
						if(responseObj.response == 'error'){
							jQuery("#overlayWrap").hide();
							jQuery("#loader").hide();
							jQuery("#message-success").html();	
							jQuery("#message-success").html('<div class="alert alert-danger alert-dismissible fade show"><?php esc_attr_e( 'Some error encountered! Please try after some time.', 'sov-case-management' ); ?><button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
							is_readirect = 0;
						}


							if(1==is_readirect){

								jQuery("#overlayWrap").hide();
								jQuery("#loader").hide();
								jQuery("#message-success").html();						
								window.setTimeout(
									function () {
										jQuery("#message-success").html('<div class="alert alert-success alert-dismissible fade show"><?php esc_attr_e( 'Redirecting to home...', 'sov-case-management' ); ?><button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');		
										window.location.href = returnUrl; 
									},
									2000	
								);	
							}				
						},
						error: function (e) {
						//error
						console.log('Error '+e);
				} 

			});//ajax call close

	});//workflow section close

	});
</script>
