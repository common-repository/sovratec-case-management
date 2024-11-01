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
if ( ! class_exists( 'Sov_Case_Management_CaseType' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Sov_Case_Management_CaseType extends WP_List_Table {

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
			'createdDate' => __( 'Created Date', 'sov-case-management' ),
			'createdBy'   => __( 'Created By', 'sov-case-management' ),
			'assignedTo'  => __( 'Assigned To', 'sov-case-management' ),
			'active'      => __( 'Active', 'sov-case-management' ),
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
			'createdDate' => array(
				'createdDate',
				true,
			),
			'createdBy'   => array(
				'createdBy',
				true,
			),
			'assignedTo'  => array(
				'assignedTo',
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

		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item['caseID'] );
	}

	function column_caseID( $item ) {
		$caseEditLink = get_admin_url( get_current_blog_id(), 'admin.php?page=casetype_management&action=edit&id=' . $item['caseID'] );
		// Build row actions
		$actions = array(
			'view' => sprintf( '<a href="' . $caseEditLink . '" data-case_id="%s"  class="case-item-view" >%s</a>', $item['caseID'], __( 'View Details', 'sov-case-management' ) ),
		);

		// Return the title contents
		return sprintf(
			'<span style="color:#555;">(#: %1$s)</span>%2$s', /*$1%s*/
			$item['caseID'] . ' ' . $item['caseType'], /*$2%s*/
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
				$case_types = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}case_management_case_type WHERE case_type_id  = %d LIMIT 1", $search ) );
			} else {
				// value is string i.e case type name
				$case_types = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}case_management_case_type WHERE case_type LIKE %s", '%' . $wpdb->esc_like( $search ) . '%' ) );
			}
		} else {
			$case_types = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}case_management_case_type ORDER BY case_type_id DESC" );
		}
		foreach ( $case_types as $case_type ) {
			$case_type_id = $case_type->case_type_id;
			$caseType     = $case_type->case_type;
			$createdDate  = date( 'M d, Y H:i', strtotime( $case_type->created_date ) );
			$createdBy    = $this->getUserName( $case_type->created_by );
			$assignedTo   = $this->getUserName( $case_type->assing_to );
			$caseIsActive = ( $case_type->is_active ) ? 'YES' : 'NO';

			$data[] = array(
				'caseID'      => $case_type_id,
				'caseType'    => $caseType,
				'createdDate' => $createdDate,
				'createdBy'   => $createdBy,
				'assignedTo'  => $assignedTo,
				'active'      => $caseIsActive,
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

	function orderList_handler() {
		global $wpdb;
		if ( isset( $_GET['s'] ) ) {
			$this->prepare_items( sanitize_text_field( $_GET['s'] ) );
		} elseif ( sanitize_text_field( $_GET['action'] ) == 'new' || sanitize_text_field( $_GET['action'] == 'edit' ) ) {
			include_once plugin_dir_path( __FILE__ ) . '/views/add-casetype.php';
			die;
		} else {
			$this->prepare_items();
		}
		?>

<div class="wrap">
<h2><?php esc_attr_e( 'Case Type List', 'sov-case-management' ); ?> 
			<a class="add-new-h2" href="<?php echo esc_url( get_admin_url( get_current_blog_id(), 'admin.php?page=casetype_management&action=new' ) ); ?>"><?php esc_attr_e( 'Add new', 'sov-case-management' ); ?></a>
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
$wp_list_table = new Sov_Case_Management_CaseType();
$wp_list_table->orderList_handler();
