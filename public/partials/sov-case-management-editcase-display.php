<?php
global $wpdb;
	$caseID          = isset( $_GET['id'] ) ? sanitize_text_field( $_GET['id'] ) : null;
	$current_user_id = get_current_user_id();

?>
<div class="row g-3 scm-case-container">
<?php
if ( sanitize_text_field( $_GET['action'] ) == 'view' ) {
	echo '<h2 class="scm-page-title-heading">View Case #  ' . esc_attr( $caseID ) . ' </h2>';
} elseif ( sanitize_text_field( $_GET['action'] ) == 'new' ) {
	echo '<h2 class="scm-page-title-heading">Create New Case</h2>';
}
?>
			
	<?php
	$caseArray = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT *
			FROM {$wpdb->prefix}case_management_case
			WHERE case_id = %d",
			$caseID
		)
	);
	foreach ( $caseArray as $case ) {
		if ( $case->user_id != $current_user_id ) {
			die( 'Not allowed to access!!' );
		}
		?>


<div class="row col-md-12" >

	<div class="col-md-6 p-3">
			<label for="scm_casetype" class="form-label"><?php esc_attr_e( 'Select Case Type', 'sov-case-management' ); ?></label>
			<?php $case_types = scm_case_types(); ?>
			<select name="scm_casetype" class="scm_casetype" id="scm_casetype" form="casetype_form" disabled>                         
				<option value="0">--Select Case Type--</option>		
				<?php foreach ( $case_types as $type ) : ?>
				<option value="<?php echo esc_attr( $type->case_type_id ); ?>" 
										  <?php
											if ( $type->case_type_id == $case->case_type ) {
												echo esc_attr( 'selected=selected' ); }
											?>
				 >
					<?php echo esc_attr( $type->case_type ); ?></option>
				<?php endforeach; ?>				                                               
			</select>
	</div>
</div>

<div class="row col-md-12" >

	<div class="col-md-6 p-3">					
			<label for="scm_first_name" class="form-label"><?php esc_attr_e( 'First Name', 'sov-case-management' ); ?> </label>
			<input type="text"  name="scm_first_name" class="scm_first_name form-control" id="scm_first_name" value="<?php echo esc_attr( $case->first_name ); ?>"  form="casetype_form" readonly="readonly" >
	</div>

	<div class="col-md-6 p-3">
			<label for="scm_last_name" class="form-label"><?php esc_attr_e( 'Last Name', 'sov-case-management' ); ?></label>
			<input type="text"  name="scm_last_name" class="scm_last_name form-control" id="scm_last_name" value="<?php echo esc_attr( $case->last_name ); ?>"  form="casetype_form" readonly="readonly" >
	</div>

</div>


<div class="row col-md-12" >

	<div class="col-md-6 p-3">					
			<label for="scm_email" class="form-label"><?php esc_attr_e( 'Contact Email', 'sov-case-management' ); ?></label>
			<input type="email" name="scm_email" id="scm_email" class="scm_email form-control"  value="<?php echo esc_attr( $case->email ); ?>" form="casetype_form" readonly="readonly">
	</div>

	<div class="col-md-6 p-3">
			<label for="scm_phone_no" class="form-label"><?php esc_attr_e( 'Phone', 'sov-case-management' ); ?></label>
				<input type="tel" name="scm_phone_no" id="scm_phone_no" class="scm_phone_no form-control"  form="casetype_form" value="<?php echo esc_attr( $case->phone_no ); ?>" readonly="readonly">
	</div>

</div>


<div class="row col-md-12" >

	<div class="col-md-6 p-3">
		<label for="scm_addressone" class="form-label"><?php esc_attr_e( 'Address 1', 'sov-case-management' ); ?></label>
		<input type="text" class="scm_addressone form-control" id="scm_addressone" name="scm_addressone" placeholder="Address Line 1" value="<?php echo esc_attr( $case->address_1 ); ?>" form="casetype_form" readonly="readonly">
	</div>

	<div class="col-md-6 p-3">
		<label for="scm_addresstwo" class="form-label"><?php esc_attr_e( 'Address 2', 'sov-case-management' ); ?></label>
		<input type="text" class="scm_addresstwo form-control" id="scm_addresstwo" name="scm_addresstwo" placeholder="Address Line 2" value="<?php echo esc_attr( $case->address_2 ); ?>" form="casetype_form" readonly="readonly">
	</div>

</div>

<div class="row col-md-12" >

	<div class="col-md-6 p-3">
		<label for="scm_address_city" class="form-label"><?php esc_attr_e( 'City', 'sov-case-management' ); ?></label>
		<input type="text" class="scm_address_city form-control" id="scm_address_city" name="scm_address_city" placeholder="City" value="<?php echo esc_attr( $case->city ); ?>" form="casetype_form" readonly="readonly">
	</div>

	<div class="col-md-3 p-3">
		<label for="scm_address_state" class="form-label"><?php esc_attr_e( 'State', 'sov-case-management' ); ?></label>
		<input type="text" class="scm_address_state form-control" id="scm_address_state" name="scm_address_state" placeholder="State" value="<?php echo esc_attr( $case->state ); ?>" form="casetype_form" readonly="readonly">
	</div>

	<div class="col-md-3 p-3">
		<label for="scm_address_zip" class="form-label"><?php esc_attr_e( 'Zip', 'sov-case-management' ); ?></label>
		<input type="text" class="scm_address_zip form-control" id="scm_address_zip" name="scm_address_zip" placeholder="Zip" value="<?php echo esc_attr( $case->zip ); ?>" form="casetype_form" readonly="readonly">
	</div>

</div>

<div class="row col-md-12 p-3" >
	<label for="scm_case_description" class="form-label"><?php esc_attr_e( 'Description of the Case (MAX 1000 Characters)', 'sov-case-management' ); ?></label>
	<textarea name="scm_case_description" class="scm_case_description form-control" id="scm_case_description"  rows="10" cols="8" form="casetype_form" readonly="readonly"><?php echo esc_attr( $case->case_description ); ?> </textarea>				
</div>

<div class="row col-md-12" >

	<div class="col-md-6 p-3">
			<label for="scm_case_status"><?php esc_attr_e( 'Case Status', 'sov-case-management' ); ?> </label>
		<span class="scm_work_status form-control"><?php echo esc_attr( $case->case_status ); ?> </span>
	</div>
	
		<?php
		$workstatus = $wpdb->get_var( $wpdb->prepare( "SELECT workflow_item FROM {$wpdb->prefix}case_management_workflow_list WHERE workflow_id  = %d AND is_active = 1", $case->workflow_status ) );
		if ( $workstatus ) :
			?>
	<div class="col-md-6 p-3">
		<label for="scm_work_status"  class="form-label"><?php esc_attr_e( 'Workstatus', 'sov-case-management' ); ?> </label>
		<span class="scm_work_status form-control">	<?php echo esc_attr( $workstatus ); ?> </span>
	</div>
	
		<?php endif; ?>
</div>

		<?php if ( $case->case_resolution ) : ?>
		<div class="row col-md-12 p-3" >
				<label for="scm_case_resolution" class="form-label"><?php esc_attr_e( 'Case Resolution', 'sov-case-management' ); ?></label>
				<textarea name="scm_case_resolution" class="scm_case_resolution form-control" id="scm_case_resolution"  rows="10" cols="8" form="casetype_form" readonly="readonly"><?php echo esc_attr( $case->case_resolution ); ?> </textarea>				
			</div>
	<?php endif; ?>

	<div class="row col-md-12" >

	<div class="col-md-6 p-3">
			<label for="scm_case_created"><?php esc_attr_e( 'Created On', 'sov-case-management' ); ?> </label>
		<span class="scm_case_created form-control"><?php echo esc_attr( date( 'M d, Y H:i', strtotime( $case->created_on ) ) ); ?> </span>
	</div>

	<div class="col-md-6 p-3">
			<label for="scm_case_updatedOn"><?php esc_attr_e( 'Last Updated', 'sov-case-management' ); ?> </label>
		<span class="scm_case_updatedOn form-control"><?php echo esc_attr( date( 'M d, Y H:i', strtotime( $case->updated_on ) ) ); ?> </span>
	</div>


	</div>
<div class="row col-md-12 p-3" >
		<label for="attachments" class="form-label"><?php esc_attr_e( 'Attachments (MAX 5)', 'sov-case-management' ); ?></label>						 
	
		<ul class="file-list"  class="form-control">

			<?php
			$FilesArray = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT *
				FROM {$wpdb->prefix}case_management_attachments
				WHERE case_id = %d AND attachment_type = 'case'",
					$case->case_id
				)
			);
			if ( $FilesArray ) {
				foreach ( $FilesArray as $file ) {
					$year      = date( 'Y', strtotime( $file->created_date ) );
					$month     = date( 'm', strtotime( $file->created_date ) );
					$customdir = 'uploads/' . $year . '/' . $month . '/';
					$dirPath   = plugin_dir_url( __DIR__ ) . $customdir . ( $file->file_name );

					echo '<li><a target="_blank" href="' . esc_url( $dirPath ) . '">' . esc_attr( $file->file_name ) . '</a></li>';
				}
			} else {
				echo 'No Attachments!';
			}
			?>

				</ul>
</div>

	<div class="row col-md-12" >					

	<div class="col-md-2 p-3">
		<input type="reset" value="Back" class="button button-primary button-large" onclick="javascript:window.location = '<?php echo esc_url( get_permalink() ); ?>'" form="casetype_form" >
	</div>
	</div>
<?php } ?>
</div>     

	 </div>     

<style>
/* 
Generic Styling, for Desktops/Laptops 
*/
table {
	width: 100%;
	border-collapse: collapse;
  }
  /* Zebra striping */
  tr:nth-of-type(odd) {
	background: #eee;
  }

  td, th { 
	padding: 6px!important;
	border: 1px solid #ccc;
	text-align: left;
  }
  /* 
Max width before this PARTICULAR table gets nasty
This query will take effect for any screen smaller than 760px
and also iPads specifically.
*/
@media 
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {
	/* Force table to not be like tables anymore */
	table, thead, tbody, th, td, tr { 
		display: block; 
	}
	/* Hide table headers (but not display: none;, for accessibility) */
	thead tr { 
		position: absolute;
		top: -9999px;
		left: -9999px;
	}
	tr { border: 1px solid #ccc; }
	td {
		/* Behave  like a "row" */
		border: none;
		border-bottom: 1px solid #eee;
		position: relative;
		padding-left: 50%;
	}
	td:before {
		/* Now like a table header */
		position: absolute;
		/* Top/left values mimic padding */
		top: 6px;
		left: 6px;
		width: 45%;
		padding-right: 10px; 
		white-space: nowrap;
	}
}
	</style>
	<script>
		jQuery(document).ready(function($){

	//trigger form action 
	jQuery( ".submit-btn" ).on( "click", function() {
		jQuery("#casetype_form").attr('action',jQuery(this).data("formaction"));
		});

let ajaxurl = "<?php echo esc_url( admin_url() . 'admin-ajax.php' ); ?>"; 
let nonceWP = "<?php echo wp_create_nonce( 'ajax-nonce' ); ?>";

jQuery("#casetype_form").submit(function(e) {
		e.preventDefault(); // avoid to execute the actual submit of the form.

		var form = jQuery(this);
		var actionUrl = form.attr('action');
		var returnUrl = jQuery('#return_url').val();
		let nonceWP = "<?php echo wp_create_nonce( 'ajax-nonce' ); ?>";

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

				jQuery("#message-success").html('<div class="alert-success"><?php esc_attr_e( 'Created A New Case!', 'sov-case-management' ); ?></div>');
				jQuery("#overlayWrap").hide(); 
				jQuery("#loader").hide();

				
			} 
	});
		
}); 	
}); 
</script>
