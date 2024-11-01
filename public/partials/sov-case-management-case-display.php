<div class="scm-case-container">

<div class="scm-plugin-heading">
<h2 class="scm-page-title-heading">Case Manager</h2>
<a href="<?php global $wp;
echo esc_url( home_url( $wp->request ) . '/?action=new' ); ?>" class="scm_action_btn button primary">New Case</a>
</div>
<table id="caseTable" summary="Case descriptions table" class="hover" style="width:100%">
	<thead>
		<th scope="col">ID</th>
		<th scope="col">Type</th>
		<th scope="col">Name</th>
		<th scope="col">Email</th>       
		<th scope="col">Status</th>
		<th scope="col">Created</th>
		<th scope="col">Description</th>
		<th scope="col">Action</th>		     
	</thead>
	<tbody>
<?php
$caseData = scm_case_data();
if ( isset( $caseData['recordsTotal'] ) && ( $caseData > 1 ) ) :
	for ( $count = 0; $count < $caseData['recordsTotal']; $count++ ) :
		?>
		<tr>
			<td><?php echo esc_attr( $caseData['data'][ $count ]['ID'] ); ?> </td>
			<td><?php echo esc_attr( $caseData['data'][ $count ]['Type'] ); ?> </td>
			<td><?php echo esc_attr( $caseData['data'][ $count ]['Name'] ); ?> </td>
			<td><?php echo esc_attr( $caseData['data'][ $count ]['Email'] ); ?> </td>
			<td><?php echo esc_attr( $caseData['data'][ $count ]['Status'] ); ?> </td>
			<td><?php echo esc_attr( $caseData['data'][ $count ]['caseCreated'] ); ?> </td>
			<td><?php echo esc_attr( $caseData['data'][ $count ]['Description'] ); ?> </td>
			<td><a href="<?php echo esc_url( '?action=view&id=' . $caseData['data'][ $count ]['ID'] ); ?>" class="button primary" style="border-radius:99px;">
			<span>View</span>
		  </a></td>
		</tr>
		<?php
	endfor;
endif;
?>
	</tbody>
</table>
</div>
