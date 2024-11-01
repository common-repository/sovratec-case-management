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
if ( ! class_exists( 'Sov_Case_Management_Overdue' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Sov_Case_Management_Overdue extends WP_List_Table {

	function __construct() {
		global $status, $page;
		parent::__construct(
			array(
				'singular' => 'order',
				'plural'   => 'orders',
				'ajax'     => false,
			)
		);
	}

	function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'caseID'      => __( 'Case ID', 'sov-case-management' ),
			'caseType'    => __( 'Case Type', 'sov-case-management' ),
			'Name'        => __( 'Name', 'sov-case-management' ),
			'caseAssign'  => __( 'Assigned To', 'sov-case-management' ),
			'caseStatus'  => __( 'Case Status', 'sov-case-management' ),
			'workStatus'  => __( 'Work Status', 'sov-case-management' ),
			'caseCreated' => __( 'Created On', 'sov-case-management' ),
			'Description' => __( 'Case Description', 'sov-case-management' ),
			'actions'     => __( 'Actions', 'sov-case-management' ),
		);
		return $columns;
	}

	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'caseID'      => array(
				'caseID',
				true,
			),
			'caseType'    => array(
				'caseType',
				true,
			),
			'Name'        => array(
				'Name',
				true,
			),
			'caseAssign'  => array(
				'caseAssign',
				true,
			),
			'caseStatus'  => array(
				'caseStatus',
				true,
			),
			'workStatus'  => array(
				'workStatus',
				true,
			),
			'caseCreated' => array(
				'caseCreated',
				true,
			),
		);
		return $sortable_columns;
	}

	public function get_hidden_columns() {
		// Setup Hidden columns and return them
		return array();
	}

	function column_cb( $item ) {

		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item['ID'] );
	}

	function column_orderID( $item ) {
		// Build row actions
		$actions = array(
			'view' => sprintf( '<a href="#" class="case-item-view" >%s</a>', $item['caseID'], $item['caseID'], __( 'View Details', 'sov-case-management' ) ),
		);

		// Return the title contents
		return sprintf(
			'<span style="color:#555;">(#: %1$s)</span>%2$s', /*$1%s*/
			$item['caseID'] . ' ' . $item['Name'], /*$2%s*/
			$this->row_actions( $actions )
		);
	}

	private function table_data() {
		global $wpdb;
		$data            = array();
		$current_user_id = get_current_user_id();
		if ( isset( $_GET['s'] ) ) {
			$search = sanitize_text_field( $_GET['s'] );
			$search = trim( $search );
			// check if entered term is an integer or string
			if ( ctype_digit( $search ) && (int) $search > 0 ) {
				$case_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}case_management_case wcase INNER JOIN {$wpdb->prefix}case_management_case_type wtype ON wcase.case_type = wtype.case_type_id WHERE DATE(NOW()) > DATE( DATE_ADD( wcase.case_start_date, INTERVAL wtype.case_type_sla DAY ) ) AND ( wcase.case_status != 'Completed' AND wcase.case_id = %d )  LIMIT 1", $search ) );
			} else {
				// value is string i.e Username/Shipping Address
				$case_details = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT * FROM {$wpdb->prefix}case_management_case wcase INNER JOIN {$wpdb->prefix}case_management_case_type wtype ON wcase.case_type = wtype.case_type_id WHERE DATE(NOW()) > DATE( DATE_ADD( wcase.case_start_date, INTERVAL wtype.case_type_sla DAY ) ) AND (wcase.first_name LIKE %s OR wcase.last_name LIKE %s ) GROUP BY wcase.case_id",
						'%' . $wpdb->esc_like( $search ) . '%',
						'%' . $wpdb->esc_like( $search ) . '%'
					)
				);
			}
		} else {
				$case_details = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}case_management_case wcase INNER JOIN {$wpdb->prefix}case_management_case_type wtype ON wcase.case_type = wtype.case_type_id WHERE DATE(NOW()) > DATE( DATE_ADD( wcase.case_start_date, INTERVAL wtype.case_type_sla DAY ) ) GROUP BY wcase.case_id" );
		}

		foreach ( $case_details as $case ) {

			$author_info = get_userdata( $case->assign_to );
			if ( ( ! $author_info->first_name ) && ( ! $author_info->last_name ) ) {
				$first_name = $author_info->display_name;
				$last_name  = '';
			} else {
				$first_name = $author_info->first_name;
				$last_name  = $author_info->last_name;
			}

			$caseID        = $case->case_id;
			$caseType      = $this->CaseName( $case->case_type_id );
			$Name          = $case->first_name . ' ' . $case->last_name;
			$caseEmail     = $case->email;
			$caseStatus    = $case->case_status;
			$caseAssign    = $first_name . ' ' . $last_name;
			$workStatus    = $wpdb->get_var( $wpdb->prepare( "SELECT workflow_item FROM {$wpdb->prefix}case_management_workflow_list WHERE workflow_id = %d", $case->workflow_status ) );
			$caseCreated   = date( 'M d, Y H:i', strtotime( $case->created_on ) );
			$Description   = $case->case_description;
			$action_link   = admin_url( 'admin.php?page=sov-case-management-dashboard&tab=overdue&action=view&id=' . $caseID );
			$caseRowAction = '<a href="' . $action_link . '" id="approve-action" class="button button-primary button-large">View Details</a>';
			$data[]        = array(
				'caseID'      => $caseID,
				'caseType'    => $caseType,
				'Name'        => $Name,
				'caseAssign'  => $caseAssign,
				'caseStatus'  => $caseStatus,
				'workStatus'  => $workStatus,
				'caseCreated' => $caseCreated,
				'Description' => $Description,
				'actions'     => $caseRowAction,
			);
		}
		return $data;
	}

	public function prepare_items() {
		global $wpdb;
		$perpage  = 10;
		$columns  = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$hidden   = $this->get_hidden_columns();
		$this->process_bulk_action();
		$data                  = $this->table_data();
		$totalitems            = count( $data );
		$this->_column_headers = array(
			$columns,
			$hidden,
			$sortable,
		);

		function usort_reorder( $a, $b ) {
			$orderby = ( ! empty( sanitize_text_field( $_REQUEST['orderby'] ) ) ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'caseID';
			// If no sort, default to title
			$order = ( ! empty( sanitize_text_field( $_REQUEST['order'] ) ) ) ? sanitize_text_field( $_REQUEST['order'] ) : 'desc';
			// If no order, default to asc
			$result = strnatcmp( $a[ $orderby ], $b[ $orderby ] );
			// Determine sort order
			return ( $order === 'asc' ) ? $result : -$result;
			// Send final sort direction to usort
		}
		usort( $data, 'usort_reorder' );
		$totalpages  = ceil( $totalitems / $perpage );
		$currentPage = $this->get_pagenum();
		$data        = array_slice( $data, ( ( $currentPage - 1 ) * $perpage ), $perpage );
		$this->set_pagination_args(
			array(
				'total_items' => $totalitems,
				'total_pages' => $totalpages,
				'per_page'    => $perpage,
			)
		);
		$this->items = $data;
	}
	public function CaseName( $caseID ) {
		global $wpdb;
		$caseName = $wpdb->get_var( $wpdb->prepare( "SELECT case_type as caseName FROM {$wpdb->prefix}case_management_case_type WHERE case_type_id  = %d", $caseID ) );
		return $caseName;

	}
	function caseList_handler() {
		global $wpdb;
		$message = '';
		if ( isset( $_GET['s'] ) ) {
			$this->prepare_items( sanitize_text_field( $_GET['s'] ) );
		} elseif ( $_GET['action'] == 'view' ) {
			include_once plugin_dir_path( __FILE__ ) . '/views/case-details.php';
			die;
		} else {
			$this->prepare_items();
		}
		?>

<div class="wrap">
		<?php
		$nonce         = sanitize_text_field( $_REQUEST['nonce'] );
		$orderUpdateID = sanitize_text_field( $_REQUEST['orderUpdateID'] );
		if ( wp_verify_nonce( $nonce, 'my-nonce' ) && isset( $orderUpdateID ) ) {
			$message = 'Order #' . $orderUpdateID . ' Updated!';
		} else {
			$message = '';
		}
		?>

		<?php
		if ( $message ) :
			?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>Success!</strong> <?php echo esc_attr( $message ); ?>
  <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
	<span aria-hidden="true">&times;</span>
  </button>
</div>
			<?php
		endif;
		?>
<form id="user-filter" method="get">
	<p class="search-box">
		<label class="screen-reader-text" for="<?php echo esc_attr( 'search' ); ?>"><?php echo esc_attr( $text ); ?>:</label>
		<input type="search" id="<?php echo esc_attr( 'search' ); ?>" name="s" value=" " />
		<input type="submit" class="button" value="<?php esc_attr_e( 'Find', 'sov-case-management' ); ?>">
	</p>
	<input type="hidden" name="page" value="<?php echo esc_attr( sanitize_text_field( $_REQUEST['page'] ) ); ?>" />
	<input type="hidden" name="tab" value="<?php echo esc_attr( sanitize_text_field( $_REQUEST['tab'] ) ); ?>"/>
		<?php $this->display(); ?>
</form>
</div>
		<?php
	}
}
$wp_list_table = new Sov_Case_Management_Overdue();
$wp_list_table->caseList_handler();
