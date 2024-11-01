<?php

	$caseID             = isset( $_GET['id'] ) ? sanitize_text_field( $_GET['id'] ) : null;
	$caseData           = scm_case_data( $caseID );
	$returnExchangeData = is_scm_case_exist( $caseID );
	$author_info        = get_userdata( get_current_user_id() );
	$phone              = get_user_meta( $author_info->ID, 'user_phone', true );
if ( ( ! $author_info->first_name ) && ( ! $author_info->last_name ) ) {
	$first_name      = $author_info->display_name;
	$last_name       = '';
	$full_name_exist = null;
	$email           = $author_info->user_email;
} else {
	$first_name = $author_info->first_name;
	$last_name  = $author_info->last_name;
	$email      = $author_info->user_email;
}

?>
<div class="scm-case-container">
<div id="overlayWrap" class="overlayWrap"></div>
<div id="message-error"> </div>
	<div id="message-success"> </div>
<?php
if ( sanitize_text_field( $_GET['action'] ) == 'view' ) {
	echo '<h2  class="scm-page-title-heading" >Edit Case #  ' . esc_attr( $caseID ) . ' </h2>';
} elseif ( sanitize_text_field( $_GET['action'] ) == 'new' ) {
	echo '<h2  class="scm-page-title-heading" >Create New Case</h2>';
}
?>

<form  id="casetype_form" class="casetype_form" enctype="multipart/form-data"></form>

<div class="row g-3 scm-case-container">

			<div class="row col-md-12" >

				<div class="col-md-6 p-3">
						<label for="scm_casetype" class="form-label"><?php esc_attr_e( 'Select Case Type', 'sov-case-management' ); ?></label>
						<?php $case_types = scm_case_types(); ?>
							<select name="scm_casetype" class="scm_casetype form-control" id="scm_casetype" form="casetype_form">                         
							<option value="0">--Select Case Type--</option>		
							<?php foreach ( $case_types as $type ) : ?>
							<option value="<?php echo esc_attr( $type->case_type_id ); ?>" >
								<?php echo esc_attr( $type->case_type ); ?></option>
							<?php endforeach; ?>				                                               
						</select>
				</div>
			</div>

			<div class="row col-md-12" >

				<div class="col-md-6 p-3">					
						<label for="scm_first_name" class="form-label"><?php esc_attr_e( 'First Name', 'sov-case-management' ); ?> <span class="required">*</span></label>
						<input type="text"  name="scm_first_name" class="scm_first_name form-control" id="scm_first_name" value="<?php echo esc_attr( $first_name ); ?>"  form="casetype_form" >
				</div>

				<div class="col-md-6 p-3">
						<label for="scm_last_name" class="form-label"><?php esc_attr_e( 'Last Name', 'sov-case-management' ); ?><span class="required">*</span></label>
						<input type="text"  name="scm_last_name" class="scm_last_name form-control" id="scm_last_name" value="<?php echo esc_attr( $last_name ); ?>"  form="casetype_form" >
				</div>

			</div>
			
			
			<div class="row col-md-12" >

				<div class="col-md-6 p-3">					
						<label for="scm_email" class="form-label"><?php esc_attr_e( 'Contact Email', 'sov-case-management' ); ?><span class="required">*</span></label>
						<input type="email" name="scm_email" id="scm_email" class="scm_email form-control"  value="<?php echo esc_attr( $email ); ?>" form="casetype_form" readonly="readonly">
				</div>

				<div class="col-md-6 p-3">
						<label for="scm_phone_no" class="form-label"><?php esc_attr_e( 'Phone', 'sov-case-management' ); ?><span class="required">*</span></label>
							<input type="tel" name="scm_phone_no" id="scm_phone_no" class="scm_phone_no form-control"  value="
							<?php
							if ( $phone ) {
								echo esc_attr( $phone . 'readonly="readonly"' ); }
							?>
						"   form="casetype_form">
				</div>

			</div>
			

			<div class="row col-md-12" >

				<div class="col-md-6 p-3">
					<label for="scm_addressone" class="form-label"><?php esc_attr_e( 'Address 1', 'sov-case-management' ); ?></label>
					<input type="text" class="scm_addressone form-control" id="scm_addressone" name="scm_addressone" placeholder="Address Line 1" form="casetype_form">
				</div>

				<div class="col-md-6 p-3">
					<label for="scm_addresstwo" class="form-label"><?php esc_attr_e( 'Address 2', 'sov-case-management' ); ?></label>
					<input type="text" class="scm_addresstwo form-control" id="scm_addresstwo" name="scm_addresstwo" placeholder="Address Line 2" form="casetype_form">
				</div>

			</div>

			<div class="row col-md-12" >

				<div class="col-md-6 p-3">
					<label for="scm_address_city" class="form-label"><?php esc_attr_e( 'City', 'sov-case-management' ); ?></label>
					<input type="text" class="scm_address_city form-control" id="scm_address_city" name="scm_address_city" placeholder="City" form="casetype_form">
				</div>

				<div class="col-md-3 p-3">
					<label for="scm_address_state" class="form-label"><?php esc_attr_e( 'State', 'sov-case-management' ); ?></label>
					<input type="text" class="scm_address_state form-control" id="scm_address_state" name="scm_address_state" placeholder="State" form="casetype_form">
				</div>

				<div class="col-md-3 p-3">
					<label for="scm_address_zip" class="form-label"><?php esc_attr_e( 'Zip', 'sov-case-management' ); ?></label>
					<input type="text" class="scm_address_zip form-control" id="scm_address_zip" name="scm_address_zip" placeholder="Zip" form="casetype_form">
				</div>

			</div>
			
			<div class="row col-md-12 p-3" >
				<label for="scm_case_description" class="form-label"><?php esc_attr_e( 'Description of the Case (MAX 1000 Characters)', 'sov-case-management' ); ?><span class="required">*</span></label>
				<textarea name="scm_case_description" class="scm_case_description form-control" id="scm_case_description"  rows="10" cols="8" form="casetype_form" > </textarea>				
			</div>
			
			<div class="row col-md-12 p-3" >
					<label for="attachments" class="form-label"><?php esc_attr_e( 'Attachments (MAX 5)', 'sov-case-management' ); ?></label>
						<ul class="instruction-list">
							<li>*You can upload maximum 5 documents at a time.</li>
							<li>*Each document size must be less than 10MB.</li>
						</ul>
				<input type="file" name="attachments" id="attachments" class="attachments multi form-control" form="casetype_form" maxlength="5"   data-maxfile="10240" multiple>						
			</div>
		

				<div class="row col-md-12" >					

				<div class="col-md-2 p-3">
					<input type="submit" name="case_submit" class="button button-primary button-large submit-btn" value="Submit" data-formaction="save_case"  form="casetype_form" >    
				</div>

				<div class="col-md-2 p-3">
					<input type="reset"   value="Cancel" class="button button-primary button-large" onclick="javascript:window.location = '<?php echo esc_url( get_permalink() ); ?>'" form="casetype_form" >
				</div>

					<input type="hidden" id= "return_url" value="<?php echo esc_url( get_permalink() ); ?>" form="casetype_form" >
					<div id="loader" class="loader" style="display: none"></div>
				</div>

	 </div>     
	<script>
jQuery(document).ready(function($){

	//trigger form action 
	jQuery( ".submit-btn" ).on( "click", function() {
		jQuery("#casetype_form").attr('action',jQuery(this).data("formaction"));
		});

	let ajaxurl = "<?php echo esc_url( admin_url() . 'admin-ajax.php' ); ?>"; 
	let nonceWP = "<?php echo wp_create_nonce( 'ajax-nonce' ); ?>";

	let file_location = "<?php
	$y = date( 'Y', strtotime( date( 'y-m-d' ) ) );
	$m = date( 'm', strtotime( date( 'y-m-d' ) ) );
	echo esc_url( plugin_dir_url( __DIR__ ) . 'uploads/' . $y . '/' . $m . '/' );
	?>";

jQuery("#casetype_form").submit(function(e) {
		e.preventDefault(); // avoid to execute the actual submit of the form.

		var form = jQuery(this);
		var data = new FormData();
		var actionUrl = form.attr('action');


		//basic validation 
		if( ( jQuery('#scm_casetype').val() == '0' || jQuery('#scm_casetype').val() == undefined ) ){
			alert('Please select a valid case type!');
			jQuery('#scm_casetype').focus();
			return false;
		}

		if( ( jQuery('#scm_case_description').val() == ' ' || jQuery('#scm_case_description').val() == '' ) ){
			alert('Please enter case description!');
			jQuery('#scm_case_description').focus();
			return false;
		}

	//Form data
	var form_data = jQuery(this).serializeArray();
	jQuery.each(form_data, function (key, input) {
		data.append(input.name, input.value);
	});

	//File data
	var file_data = $('input[name="attachments"]')[0].MultiFile.files;
	for (var i = 0; i < file_data.length; i++) {
		data.append("attachments[]", file_data[i]);
	}

	data.append('action', 'save_case');
	data.append('nonce', nonceWP );

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

					jQuery("#message-success").html('<div class="alert-success"><?php esc_attr_e( 'Created A New Case!', 'sov-case-management' ); ?></div>');
					jQuery("#overlayWrap").hide(); 
					jQuery("#loader").hide();
					jQuery('.MultiFile-list').empty();
					//redirect back to home
					window.location.href = jQuery('#return_url').val();
				},
				error: function (e) {
				//error
				console.log('Error '+e);
		} 
	});

	}); 	
}); 
</script>
