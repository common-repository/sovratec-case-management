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
if ( ! class_exists( 'Sov_Case_Management_WorkflowStatus' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Sov_Case_Management_WorkflowStatus extends WP_List_Table {

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
			'cb'           => '<input type="checkbox" />',
			'workstatusID' => __( 'Workstatus', 'sov-case-management' ),
			// 'workstatusDesc'      => __( 'Workstatus Description', 'sov-case-management' ),
			'caseType'     => __( 'Case Type', 'sov-case-management' ),
			'createdDate'  => __( 'Created Date', 'sov-case-management' ),
			'createdBy'    => __( 'Created By', 'sov-case-management' ),
			'active'       => __( 'Active', 'sov-case-management' ),
		);
		return $columns;
	}

	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'workstatusID' => array(
				'workstatusID',
				true,
			),
			// 'workstatusDesc'    => array(
			// 'workstatusDesc',
			// true,
			// ),
			'caseType'     => array(
				'caseType',
				true,
			),
			'createdDate'  => array(
				'createdDate',
				true,
			),
			'createdBy'    => array(
				'createdBy',
				true,
			),
			'active'       => array(
				'active',
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

		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item['workstatusID'] );
	}

	function column_workstatusID( $item ) {
		$caseEditLink = get_admin_url( get_current_blog_id(), 'admin.php?page=workflowstatus_management&action=edit&id=' . $item['workstatusID'] );
		// Build row actions
		$actions = array(
			'view' => sprintf( '<a href="' . $caseEditLink . '" data-case_id="%s"  class="case-item-view" >%s</a>', $item['workstatusID'], __( 'View Details', 'sov-case-management' ) ),
		);

		// Return the title contents
		return sprintf(
			'<span style="color:#555;">(#: %1$s)</span>%2$s', /*$1%s*/
			$item['workstatusID'] . ' - ' . $item['workstatusDesc'], /*$2%s*/
			$this->row_actions( $actions )
		);
	}

	private function table_data() {
		global $wpdb;
		$data = array();
		if ( isset( $_GET['s'] ) ) {
			$search = sanitize_text_field( $_GET['s'] );
			$search = trim( $search );
			// check if entered term is an integer or string
			if ( ctype_digit( $search ) && (int) $search > 0 ) {
				$workstatus_items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}case_management_workflow_list WHERE workflow_id   = %d LIMIT 1", $search ) );
			} else {
				// value is string i.e case type name
				$workstatus_items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}case_management_workflow_list WHERE workflow_item LIKE %s", '%' . $wpdb->esc_like( $search ) . '%' ) );
			}
		} else {
				$workstatus_items = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}case_management_workflow_list ORDER BY workflow_id DESC" );
		}
		foreach ( $workstatus_items as $workstatus_item ) {
			$workstatus_item_id = $workstatus_item->workflow_id;
			$workstatusDesc     = $workstatus_item->workflow_item;
			$caseType           = $this->getCaseTypeName( $workstatus_item->case_type_id );
			$createdDate        = date( 'M d, Y H:i', strtotime( $workstatus_item->created_date ) );
			$createdBy          = $this->getUserName( $workstatus_item->created_by );
			$caseIsActive       = ( $workstatus_item->is_active ) ? 'YES' : 'NO';

			$data[] = array(
				'workstatusID'   => $workstatus_item_id,
				'workstatusDesc' => $workstatusDesc,
				'caseType'       => $caseType,
				'createdDate'    => $createdDate,
				'createdBy'      => $createdBy,
				'active'         => $caseIsActive,
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
			$orderby = ( ! empty( sanitize_text_field( $_REQUEST['orderby'] ) ) ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'workstatusID';
			// If no sort, default to title
			$order = ( ! empty( sanitize_text_field( $_REQUEST['order'] ) ) ) ? sanitize_text_field( $_REQUEST['order'] ) : 'asc';
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

	public function getUserName( $user_id = null ) {
		if ( $user_id == null ) {
			return false;
		}
		$full_name   = '';
		$user_exists = get_userdata( $user_id );
		if ( $user_exists ) {
			if ( ( ! $user_exists->first_name ) || ( ! $user_exists->last_name ) ) {
				$full_name = $user_exists->display_name;
			} else {
				$full_name = $user_exists->first_name . ' ' . $user_exists->last_name;
			}
		} else {
			$full_name = 'N/A';
		}
		return $full_name;
	}

	public function getCaseTypeName( $caseID = null ) {
		global $wpdb;
		if ( $caseID == null ) {
			return false;
		}
		$caseType = $wpdb->get_var( $wpdb->prepare( "SELECT case_type FROM {$wpdb->prefix}case_management_case_type WHERE case_type_id = %d", $caseID ) );
		return $caseType;
	}


	function workstatusList_handler() {
		global $wpdb;
		if ( isset( $_GET['s'] ) ) {
			$this->prepare_items( sanitize_text_field( $_GET['s'] ) );
		} elseif ( $_GET['action'] == 'new' || $_GET['action'] == 'edit' ) {
			include_once plugin_dir_path( __FILE__ ) . '/views/add-workflow-template.php';
			die;
		} else {
			$this->prepare_items();
		}
		?>

<div class="wrap">
<h2><?php esc_attr_e( 'Workstatus Items', 'sov-case-management' ); ?> 
			<a class="add-new-h2" href="<?php echo esc_url( get_admin_url( get_current_blog_id(), 'admin.php?page=workflowstatus_management&action=new' ) ); ?>"><?php esc_attr_e( 'Add new', 'sov-case-management' ); ?></a>
		</h2>
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
$wp_list_table = new Sov_Case_Management_WorkflowStatus();
$wp_list_table->workstatusList_handler();
