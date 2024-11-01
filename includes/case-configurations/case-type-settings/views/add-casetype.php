<div class="main-form-heading">
<div id="overlayWrap" class="overlayWrap"></div>
<?php
global $wpdb;
$edit_view_flag = 0;

if ( sanitize_text_field( $_GET['action'] ) == 'edit' && ! empty( sanitize_text_field( $_GET['id'] ) ) ) {
	$edit_view_flag = 1;
	$case_type_id   = sanitize_text_field( $_GET['id'] );
	$current_user   = get_current_user_id();


	$caseConfigArray = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * From {$wpdb->prefix}case_management_case_type
					WHERE case_type_id = %d",
			$case_type_id
		)
	);

	foreach ( $caseConfigArray as $caseType_item ) {
		$case_type         = $caseType_item->case_type;
		$case_type_details = $caseType_item->case_type_details;
		$assing_to         = $caseType_item->assing_to;
		$is_active         = $caseType_item->is_active;
		$scm_case_sla      = $caseType_item->case_type_sla;
	}

	if ( $assing_to > 0 ) {
		// get selected admin user data here
		$author_info = get_userdata( $assing_to );
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
}
?>
	<h2 style="text-align: left;padding: 2px 0px;font-size: 20px;font-weight: 600;margin-top: 35px;"><?php echo esc_attr( ( $edit_view_flag > 0 ) ? 'Edit case type #' . $case_type_id : 'Add New Case' ); ?></h2>
	<form method="POST" action="save_casetype" id="casetype_form" class="casetype_form" ></form>
	<div id="message-error"> </div>
	<div id="message-success"> </div>
	<div class="col-sm-6 com-md-6">

	<table class="form-table" summary="New case type table">
			<tbody>
				<tr class="form-field">
					<th scope="col">
						<label for="scm_casetype"><?php esc_attr_e( 'Case Type', 'sov-case-management' ); ?></label>
					</th>
					<td>
						<input type="text" name="scm_casetype" class="scm_casetype" id="scm_casetype" value="<?php echo esc_attr( isset( $case_type ) ? $case_type : '' ); ?>" 
																														<?php
																														if ( $edit_view_flag > 0 ) {
																															echo esc_attr( 'readonly=readonly' ); }
																														?>
						 form="casetype_form">
					</td>                     
				</tr>

				<tr class="form-field">
					<th scope="col">
						<label for="scm_assignedto"><?php esc_attr_e( 'Assigned To', 'sov-case-management' ); ?></label>
					</th>
					<td><select name="scm_assignedto" class="scm_assignedto" id="scm_assignedto" style="width: 95%" form="casetype_form">                         
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
					<th scope="col">
						<label for="scm_case_description"><?php esc_attr_e( 'Description', 'sov-case-management' ); ?></label>
					</th>
					<td class="form-inline">
						<textarea name="scm_case_description" class="scm_case_description" id="scm_case_description"  rows="4" cols="8" form="casetype_form"> <?php echo esc_attr( isset( $case_type_details ) ? $case_type_details : '' ); ?></textarea>
					</td>                    
				</tr>

				<tr class="form-field">
					<th scope="col">
						<label for="scm_case_active"><?php esc_attr_e( 'Active', 'sov-case-management' ); ?></label>
					</th>
					<td class="form-inline">                        
						<input type="checkbox" name="scm_case_active" id="scm_case_active" class="scm_case_active"  <?php echo esc_attr( ( $is_active > 0 ) ? 'checked' : '' ); ?> form="casetype_form">
					</td>                    
				</tr>

				<tr class="form-field">
					<th scope="col">
						<label for="scm_case_sla"><?php esc_attr_e( 'SLA', 'sov-case-management' ); ?></label>
					</th>
					<td class="form-inline">                        
						<input type="number" name="scm_case_sla" id="scm_case_sla" class="scm_case_sla" value="<?php echo esc_attr( ( $scm_case_sla > 0 ) ? $scm_case_sla : 0 ); ?>" form="casetype_form">
						<p class="fields-note">SLA (Service Level Agreement) is a numerical field to capture the number of days allowed to complete the case before it is considered past due.</p>
					</td>                    
				</tr>
				<tr class="form-field">                   
					<td class="form-inline" style="display: contents;">
						<input type="submit" style="margin-top: 16px;margin-left: 10px;width: 90px;" name="casetype_submit" class="button button-primary button-large" value="Save" form="casetype_form" >   
						<input type="reset"  style="margin-top: 16px;margin-left: 10px;width: 90px;background: #2271b1;" value="Cancel" class="button button-primary button-large" onclick="javascript:window.location = '<?php echo esc_url( admin_url( 'admin.php?page=casetype_management' ) ); ?>'" form="casetype_form" >		
						<input type="hidden" id="created_by_id" class="created_by_id" name="created_by_id" value="<?php echo esc_attr( get_current_user_id() ); ?>" form="casetype_form" >
						<input type="hidden" id="case_type_id" class="case_type_id" name="case_type_id" value="<?php echo esc_attr( $case_type_id ); ?>" form="casetype_form" >
						<input type="hidden" id="return_url" class="return_url" name="return_url" value="<?php echo esc_url( admin_url( 'admin.php?page=casetype_management' ) ); ?>" form="casetype_form" >
						<div id="loader" class="loader" style="display: none"></div>
					</td>                    
				</tr>              
			</tbody>
		</table>
				</div>
	</div>
</div>
	<script type="text/javascript">
	jQuery(document).ready(function($){

		let ajaxurl = "<?php echo esc_url( admin_url() . 'admin-ajax.php' ); ?>"; 
		let nonceWP = "<?php echo wp_create_nonce( 'ajax-nonce' ); ?>";

		jQuery("#casetype_form").submit(function(e) {
				e.preventDefault(); // avoid to execute the actual submit of the form.

				var form = jQuery(this);
				var actionUrl = form.attr('action');
				var returnUrl = jQuery('#return_url').val();

				if( ( jQuery('#scm_casetype').val() == '' || jQuery('#scm_casetype').val() == undefined ) ){
					alert('Please enter case type!');
					jQuery('#scm_casetype').focus();
					return false;
				}

				if( ( jQuery('#scm_case_sla').val() == '' || jQuery('#scm_case_sla').val() == '0' ) ){
					alert('Please enter valid SLA!');
					jQuery('#scm_case_sla').focus();
					return false;
				}


				jQuery.ajax({
				type:   "POST",
				dataType:   'json',
				url:    ajaxurl, 
				data: { 
						action: actionUrl, 
						case_data:  form.serialize(),
						nonce: nonceWP
					},
					beforeSend: function(){
						jQuery("#overlayWrap").show();
						jQuery("#loader").show();
					},
					success: function(response){
						if(response == 'Existed'){
						jQuery("#message-error").html('<div class="alert alert-danger alert-dismissible fade show"><?php esc_attr_e( 'Case is already existed! Please try with differnt case type again.', 'sov-case-management' ); ?><button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
						jQuery("#overlayWrap").hide(); 
						jQuery("#loader").hide();
						}else{
							
							if(jQuery("#case_type_id").val() !== ''){
								jQuery("#message-success").html('<div class="alert alert-success alert-dismissible fade show"><?php esc_attr_e( 'Updated the case #' . sanitize_text_field( $_GET['id'] ), 'sov-case-management' ); ?><button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
							}else{
								jQuery("#message-success").html('<div class="alert alert-success alert-dismissible fade show"><?php esc_attr_e( 'Created A New Case Type!', 'sov-case-management' ); ?><button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
							}
							

						jQuery("#overlayWrap").hide(); 
						jQuery("#loader").hide();
						jQuery("#message-success").html();
						form.trigger("reset");
						window.setTimeout(
							function () {
								jQuery("#message-success").html('<div class="alert alert-success alert-dismissible fade show"><?php esc_attr_e( 'Redirecting to home...', 'sov-case-management' ); ?><button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');		
								window.location.href = returnUrl; 
							},
							3000	
						);
						}
						
					} 
			});
				
			});       

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
			}				
			); //select 2 support
	});
</script>
