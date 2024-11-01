<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://sovratec.com/
 * @since      1.0.0
 *
 * @package    Sov_Case_Management
 * @subpackage Sov_Case_Management/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Sovratec <https://sovratec.com/>
 */
class Sov_Case_Management_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();

		// Case Type Operations
		add_action( 'wp_ajax_save_casetype', array( $this, 'save_case_type' ) );
		add_action( 'wp_ajax_nopriv_save_casetype', array( $this, 'save_case_type' ) );
		add_action( 'wp_ajax_delete_case_type', array( $this, 'delete_case_type' ) );
		add_action( 'wp_ajax_nopriv_delete_case_type', array( $this, 'delete_case_type' ) );
		add_action( 'wp_ajax_get_case_type', array( $this, 'get_case_type' ) );
		add_action( 'wp_ajax_nopriv_get_case_type', array( $this, 'get_case_type' ) );
		add_action( 'wp_ajax_get_members_list', array( $this, 'get_members_list' ) );
		add_action( 'wp_ajax_nopriv_get_members_list', array( $this, 'get_members_list' ) );

		// Workflow List Operations
		add_action( 'wp_ajax_save_workflow_list', array( $this, 'save_workflow_list' ) );
		add_action( 'wp_ajax_nopriv_save_workflow_list', array( $this, 'save_workflow_list' ) );
		add_action( 'wp_ajax_delete_workflow_list', array( $this, 'delete_workflow_list' ) );
		add_action( 'wp_ajax_nopriv_delete_workflow_list', array( $this, 'delete_workflow_list' ) );
		add_action( 'wp_ajax_get_casetype_list', array( $this, 'get_casetype_list' ) );
		add_action( 'wp_ajax_nopriv_get_casetype_list', array( $this, 'get_casetype_list' ) );

		// Case Operations
		add_action( 'wp_ajax_save_case', array( $this, 'save_case' ) );
		add_action( 'wp_ajax_nopriv_save_case', array( $this, 'save_case' ) );
		add_action( 'wp_ajax_update_admin_case', array( $this, 'update_admin_case' ) );
		add_action( 'wp_ajax_nopriv_update_admin_case', array( $this, 'update_admin_case' ) );
		add_action( 'wp_ajax_draft_case', array( $this, 'draft_case' ) );
		add_action( 'wp_ajax_nopriv_draft_case', array( $this, 'draft_case' ) );
		add_action( 'wp_ajax_delete_case', array( $this, 'delete_case' ) );
		add_action( 'wp_ajax_nopriv_delete_case', array( $this, 'delete_case' ) );
		add_action( 'wp_ajax_save_comment', array( $this, 'save_comment' ) );
		add_action( 'wp_ajax_nopriv_save_comment', array( $this, 'save_comment' ) );
	}

	// function to save case
	public function save_case() {
		global $wpdb;
		$current_user = get_current_user_id();
		$caseTypeName = sanitize_text_field( $_POST['scm_casetype'] );

		// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}

		// Get case type details
		$assign_to = $wpdb->get_var( $wpdb->prepare( "SELECT assing_to FROM {$wpdb->prefix}case_management_case_type WHERE case_type_id  = %d", $caseTypeName ) );

		if ( $assign_to ) {
			$case_status = 'Processing';
		} else {
			$case_status = 'New';
		}

		if ( ! empty( $_POST ) ) {

			$wpdb->insert(
				$wpdb->prefix . 'case_management_case',
				array(
					'first_name'       => sanitize_text_field( $_POST['scm_first_name'] ),
					'last_name'        => sanitize_text_field( $_POST['scm_last_name'] ),
					'phone_no'         => sanitize_text_field( $_POST['scm_phone_no'] ),
					'email'            => sanitize_email( $_POST['scm_email'] ),
					'address_1'        => sanitize_text_field( $_POST['scm_addressone'] ),
					'address_2'        => sanitize_text_field( $_POST['scm_addresstwo'] ),
					'city'             => sanitize_text_field( $_POST['scm_address_city'] ),
					'state'            => sanitize_text_field( $_POST['scm_address_state'] ),
					'zip'              => sanitize_text_field( $_POST['scm_address_zip'] ),
					'case_description' => sanitize_text_field( $_POST['scm_case_description'] ),
					'case_type'        => sanitize_text_field( $_POST['scm_casetype'] ),
					'case_status'      => $case_status,
					'user_id'          => $current_user,
					'assign_to'        => $assign_to,
					'created_on'       => date( 'Y-m-d H:i:s' ),
					'case_start_date'  => ( 'Processing' == $case_status ) ? date( 'Y-m-d H:i:s' ) : null,
				)
			);
			// echo $wpdb->last_query;
			$uploadedFilesArray = $this->file_uploads( $wpdb->insert_id, $current_user, 'case' );
			if ( $wpdb->insert_id && $uploadedFilesArray ) {
				echo json_encode(
					array(
						'status'      => true,
						'file_status' => $uploadedFilesArray,
					),
					true
				);
				// Trigger new case email
				$this->scm_sendEmail( $wpdb->insert_id, $case_status, sanitize_email( $_POST['scm_email'] ), $assign_to );
				die;
			} else {
				if ( $wpdb->insert_id ) {
					$wpdb->delete( $wpdb->prefix . 'case_management_case', array( 'case_id ' => $wpdb->insert_id ) );
				}
				echo json_encode(
					array(
						'status'      => false,
						'file_status' => $uploadedFilesArray,
					),
					true
				);
				die;
			}
		}
	}

	// update backend case information
	public function update_admin_case() {

			global $wpdb;
			$current_user = get_current_user_id();
			parse_str( sanitize_text_field( $_POST['case_data'] ), $form_data );
			$caseID          = sanitize_text_field( $form_data['caseID'] );
			$assign_to       = sanitize_text_field( $form_data['scm_assignedto'] );
			$case_status     = sanitize_text_field( $form_data['scm_casestatus'] );
			$workflowstatus  = sanitize_text_field( $form_data['scm_workflowstatus'] );
			$case_resolution = sanitize_text_field( $_POST['scm_case_resolution'] );
			$case_priority   = sanitize_text_field( $form_data['scm_casepriority'] );
			
			// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}

			// Check if same workflow status is existed
			$workflowFlag = $wpdb->get_var( $wpdb->prepare( "SELECT workflow_status FROM {$wpdb->prefix}case_management_case WHERE ( workflow_status > 0  AND case_id = %d ) AND workflow_status != %s", $caseID, $workflowstatus ) );

		if ( ! empty( $_POST ) ) {
			$actionStatus = $wpdb->update(
				$wpdb->prefix . 'case_management_case',
				array(
					'assign_to'       => $assign_to,
					'case_status'     => $case_status,
					'user_id'         => $current_user,
					'workflow_status' => $workflowstatus,
					'case_resolution' => $case_resolution,
					'case_priority'   => $case_priority,
					'completed_date'  => ( 'Completed' == $case_status ) ? date( 'Y-m-d H:i:s' ) : null,
					'case_end_date'   => ( 'Completed' == $case_status ) ? date( 'Y-m-d H:i:s' ) : null,
					'case_start_date' => ( ( 'Completed' == $case_status || 'Processing' == $case_status ) && $assign_to > 0 ) ? date( 'Y-m-d H:i:s' ) : null,
				),
				array(
					'case_id' => $caseID,
				)
			);

			if ( $actionStatus ) {
				// Trigger only if case is completed
				if ( $case_status == 'Completed' ) {
					$this->scm_sendEmail( $caseID, $case_status, trim( $form_data['scm_email'] ), $assign_to );
				} elseif ( $workflowFlag ) {
					// Trigger if there is a change in workstatus
					$this->scm_sendEmail( $caseID, $case_status, trim( $form_data['scm_email'] ), $assign_to );
				}

				echo json_encode(
					array(
						'status' => true,
					),
					true
				);
				die;
			} else {
				echo json_encode(
					array(
						'status' => false,
					),
					true
				);
				die;
			}
		}
	}

	// uploads attachment
	public function file_uploads( $id, $current_user, $attachment_type, $comment_id = 0 ) {
		global $wpdb;
		$y         = date( 'Y', strtotime( date( 'y-m-d' ) ) );
		$m         = date( 'm', strtotime( date( 'y-m-d' ) ) );
		$customdir = 'public/uploads/' . $y . '/' . $m . '/';
		$dirPath   = plugin_dir_path( dirname( __FILE__ ) ) . $customdir;
		$result    = wp_mkdir_p( $dirPath );
		$rename    = $id . '-' . date( 'd-m' ) . '-' . rand( 10, 5000 );

		// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}

		if ( isset( $_FILES['attachments'] ) && ! empty( $_FILES['attachments']['name'][0] ) ) {
			$errors        = array();
			$uploadedFiles = array();

			foreach ( $_FILES['attachments']['tmp_name'] as $key => $tmp_name ) {
				$file_name = $_FILES['attachments']['name'][ $key ];
				$file_size = $_FILES['attachments']['size'][ $key ];
				$file_tmp  = $_FILES['attachments']['tmp_name'][ $key ];
				$file_type = $_FILES['attachments']['type'][ $key ];
				if ( $file_size > 10485760 ) {
					$errors[] = __( 'File size must be less than 10 MB', 'sov-case-management' );
				} elseif ( count( $_FILES['attachments'] ) > 5 ) {
					$errors[] = __( 'Maximum of 5 files can be uploaded', 'sov-case-management' );
				}
				$ext       = pathinfo( $file_name, PATHINFO_EXTENSION );
				$tempName  = basename( $file_name, '.' . $ext );
				$file_name = $tempName . '_' . $rename . '_' . $id . '.' . $ext;
				$file_name = sanitize_file_name( $file_name );
				if ( empty( $errors ) == true ) {
					if ( is_dir( $dirPath ) == false ) {
						wp_mkdir_p( $dirPath );        // Create directory if it does not exist
					}
					if ( is_dir( $dirPath . $file_name ) == false ) {
						move_uploaded_file( $file_tmp, $dirPath . $file_name );
						if ( move_uploaded_file( $file_tmp, $dirPath . $file_name ) ) {
							echo 'true';
						}
						array_push( $uploadedFiles, $file_name );
					} else {
						// rename the file if another one exist
						$new_dir = $dirPath . $file_name . time();
						rename( $file_tmp, $new_dir );
						array_push( $selected, $new_dir );
					}
				} else {
					print_r( $errors );
				}

				// Update database table
				$wpdb->insert(
					$wpdb->prefix . 'case_management_attachments',
					array(
						'case_id'             => $id,
						'file_name'           => $file_name,
						'user_id'             => $current_user,
						'attachment_type'     => $attachment_type,
						'attachment_doc_type' => $file_type,
						'comment_id'          => $comment_id,
						'created_date'        => date( 'Y-m-d H:i:s' ),
					)
				);
			}

			if ( empty( $errors ) ) {
				return $uploadedFiles;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	// function to save case
	public function draft_case() {
		// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}

		echo json_encode( 'draft_case', true );
		die;
	}
	// function to remove case
	public function delete_case() {
		// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}
		echo json_encode( 'delete_case', true );
		die;
	}

	// function to save comments
	public function save_comment() {

		global $wpdb;
		$current_user = get_current_user_id();

		// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}

		if ( ! empty( $_POST ) ) {

			$wpdb->insert(
				$wpdb->prefix . 'case_management_comments',
				array(
					'comment_description' => sanitize_text_field( $_POST['scm_case_comments'] ),
					'case_id'             => sanitize_text_field( $_POST['scm_case_id'] ),
					'user_id'             => sanitize_text_field( $_POST['scm_current_user_id'] ),
					'created_date'        => date( 'Y-m-d H:i:s' ),
				)
			);
			// echo $wpdb->last_query;
			$uploadedFilesArray = $this->file_uploads( sanitize_text_field( $_POST['scm_case_id'] ), $current_user, 'comment', $wpdb->insert_id );
			if ( $wpdb->insert_id && $uploadedFilesArray ) {
				echo json_encode(
					array(
						'status'      => true,
						'file_status' => $uploadedFilesArray,
					),
					true
				);
				die;
			} else {
				if ( $wpdb->insert_id ) {
					$wpdb->delete( $wpdb->prefix . 'case_management_comments', array( 'comment_id  ' => $wpdb->insert_id ) );
				}
				echo json_encode(
					array(
						'status'      => false,
						'file_status' => $uploadedFilesArray,
					),
					true
				);
				die;
			}
		}
	}

	// ajax function to save case type
	public function save_case_type() {
		global $wpdb;
		$form_data    = sanitize_text_field( $_POST['case_data'] );
		$caseTypeData = array();
		parse_str( $form_data, $caseTypeData );
		$caseTypeName = trim( strtolower( $caseTypeData['scm_casetype'] ) );
		$case_type_id = isset( $caseTypeData['case_type_id'] ) ? $caseTypeData['case_type_id'] : null;

		// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}

		// check if case is already exist
		if ( $wpdb->get_var( $wpdb->prepare( "SELECT case_type_id  FROM {$wpdb->prefix}case_management_case_type WHERE LOWER(case_type) = %s", $caseTypeName ) ) ) {
			// check if we are updating the existing case
			if ( $case_type_id ) {
				$returnStatus = $wpdb->update(
					$wpdb->prefix . 'case_management_case_type',
					array(
						'case_type_details' => trim( $caseTypeData['scm_case_description'] ),
						'assing_to'         => trim( $caseTypeData['scm_assignedto'] ),
						'is_active'         => isset( $caseTypeData['scm_case_active'] ) ? 1 : 0,
						'case_type_sla'     => isset( $caseTypeData['scm_case_sla'] ) ? $caseTypeData['scm_case_sla'] : 0,
						'created_by'        => trim( $caseTypeData['created_by_id'] ),
						'created_date'      => date( 'Y-m-d H:i:s' ),
					),
					array(
						'case_type_id' => $case_type_id,
					)
				);
				// echo $wpdb->last_query;
				if ( ! ( $returnStatus == false ) ) {
					echo json_encode( true, true );
					die;
				} else {
					echo json_encode( false, true );
					die;
				}
			}
		}

		// Create new case
		if ( ! empty( $caseTypeData ) ) {

			$wpdb->insert(
				$wpdb->prefix . 'case_management_case_type',
				array(
					'case_type'         => trim( $caseTypeData['scm_casetype'] ),
					'case_type_details' => trim( $caseTypeData['scm_case_description'] ),
					'assing_to'         => trim( $caseTypeData['scm_assignedto'] ),
					'is_active'         => isset( $caseTypeData['scm_case_active'] ) ? 1 : 0,
					'case_type_sla'     => isset( $caseTypeData['scm_case_sla'] ) ? $caseTypeData['scm_case_sla'] : 0,
					'created_by'        => trim( $caseTypeData['created_by_id'] ),
					'created_date'      => date( 'Y-m-d H:i:s' ),
				)
			);

			if ( $wpdb->insert_id ) {
				echo json_encode( true, true );
				die;
			} else {
				echo json_encode( false, true );
				die;
			}
		}

	}

	// ajax function to delete case type
	public function delete_case_type() {
		// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}

		$response = 'hello it is delete_case_type!!';
		echo json_encode( $response, true );
		die;
	}

	// ajax function to delete case type
	public function get_case_type() {
		// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}

		return 'hello it is get_case_type!!';

	}

	// ajax function to delete workflow list
	public function delete_workflow_list() {
		global $wpdb;
		$current_user = get_current_user_id();

		// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}

		if ( ! empty( $_POST ) ) {

			$delete_status = $wpdb->delete(
				$wpdb->prefix . 'case_management_workflow_list',
				array(
					'workflow_id' => sanitize_text_field( $_POST['scm_workflow_id'] ),
				)
			);
			// echo $wpdb->last_query;
			if ( $delete_status ) {
				echo json_encode(
					array(
						'status' => true,
						'id'     => sanitize_text_field( $_POST['scm_workflow_id'] ),
					),
					true
				);
				die;
			} else {
				echo json_encode(
					array(
						'status' => false,
						'id'     => null,
					),
					true
				);
				die;
			}
		}

	}

	// ajax function to save workflow list
	public function save_workflow_list() {

		global $wpdb;
		$current_user = get_current_user_id();

		$workstatusName    = strtolower( sanitize_text_field( $_POST['scm_workflow_item'] ) );
		$case_type_id      = isset( $_POST['scm_case_type_id'] ) ? sanitize_text_field( $_POST['scm_case_type_id'] ) : null;
		$is_active_status  = ( 'false' == $_POST['scm_active'] ) ? 0 : 1;
		$is_workstatus_new = ( 0 == sanitize_text_field( $_POST['scm_is_new'] ) ) ? 1 : 0;

		// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}

		// Check if user is trying to create a new workstatus
		if ( $is_workstatus_new == 1 ) {
			// check if case is already existed
			if ( $workflowID  = $wpdb->get_var( $wpdb->prepare( "SELECT workflow_id FROM {$wpdb->prefix}case_management_workflow_list WHERE LOWER(workflow_item) = %s AND case_type_id = %d", $workstatusName, $case_type_id ) ) ) {
				if ( $workflowID ) {
					echo json_encode(
						array(
							'status'   => true,
							'response' => 'existed',
							'id'       => $workflowID,
						),
						true
					);
					die;
				}
			}
		} else {
			// check if case is already exist
			if ( $workflowID  = $wpdb->get_var( $wpdb->prepare( "SELECT workflow_id FROM {$wpdb->prefix}case_management_workflow_list WHERE LOWER(workflow_item) = %s AND case_type_id = %d", $workstatusName, $case_type_id ) ) ) {
				// check if we are updating the existing case
				if ( $workflowID ) {
					$returnStatus = $wpdb->update(
						$wpdb->prefix . 'case_management_workflow_list',
						array(
							'workflow_item' => sanitize_text_field( $_POST['scm_workflow_item'] ),
							'case_type_id'  => sanitize_text_field( $_POST['scm_case_type_id'] ),
							'created_by'    => sanitize_text_field( $_POST['scm_created_by'] ),
							'is_active'     => $is_active_status,
						),
						array(
							'workflow_id' => $workflowID,
						)
					);
					if ( ! ( $returnStatus == false ) ) {
						echo json_encode(
							array(
								'status'   => true,
								'response' => 'updated',
								'id'       => $workflowID,
							),
							true
						);
						die;
					}
				}
			}
		}

		if ( ! empty( $_POST ) ) {

			$wpdb->insert(
				$wpdb->prefix . 'case_management_workflow_list',
				array(
					'workflow_item' => sanitize_text_field( $_POST['scm_workflow_item'] ),
					'case_type_id'  => sanitize_text_field( $_POST['scm_case_type_id'] ),
					'created_by'    => sanitize_text_field( $_POST['scm_created_by'] ),
					'is_active'     => ( $_POST['scm_active'] == true ) ? 1 : 0,
					'created_date'  => date( 'Y-m-d H:i:s' ),
				)
			);
			// echo $wpdb->last_query;
			if ( $wpdb->insert_id ) {
				echo json_encode(
					array(
						'status'   => true,
						'response' => 'new_workstatus',
						'id'       => $wpdb->insert_id,
					),
					true
				);
				die;
			} else {
				echo json_encode(
					array(
						'status'   => false,
						'response' => 'error',
						'id'       => null,
					),
					true
				);
				die;
			}
		}
	}

	public function get_members_list() {
		global $wpdb;

		// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}

		// The search term
		$search_term = isset( $_POST['searchTerm'] ) ? sanitize_text_field( $_POST['searchTerm'] ) : null;

		if ( $search_term != null ) {
			// WP_User_Query arguments
			$args = array(
				'role'       => 'Administrator',
				'order'      => 'ASC',
				'orderby'    => 'display_name',
				'search'     => '*' . esc_attr( $search_term ) . '*',
				'meta_query' => array(
					'relation' => 'OR',
					array(
						'key'     => 'first_name',
						'value'   => $search_term,
						'compare' => 'LIKE',
					),
					array(
						'key'     => 'last_name',
						'value'   => $search_term,
						'compare' => 'LIKE',
					),
					array(
						'key'     => 'description',
						'value'   => $search_term,
						'compare' => 'LIKE',
					),
				),
			);

		} else {

			// WP_User_Query arguments
			$args = array(
				'role'    => 'Administrator',
				'order'   => 'ASC',
				'orderby' => 'display_name',
			);
		}

		// Create the WP_User_Query object
		$wp_user_query = new WP_User_Query( $args );

		// Get the results
		$authors = $wp_user_query->get_results();

		// Hold user info in array
		$user_data = array();

		// Check for results
		if ( ! empty( $authors ) ) {

			// loop through each author
			foreach ( $authors as $author ) {
				// get all the user's data
				$author_info = get_userdata( $author->ID );
				if ( ( ! $author_info->first_name ) || ( ! $author_info->last_name ) ) {
					$full_name = $author_info->display_name;
				} else {
					$full_name = $author_info->first_name . ' ' . $author_info->last_name;
				}

				$user_data[] = array(
					'id'   => $author->ID,
					'text' => $full_name,
				);
			}
		}
		echo json_encode( $user_data );
		die;
	}

	public function get_casetype_list() {
		global $wpdb;
		// The search term
		$search_term = isset( $_POST['searchTerm'] ) ? sanitize_text_field( $_POST['searchTerm'] ) : null;

		// Check for nonce security
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}

		if ( $search_term != null ) {

			// WP_User_Query arguments
			$caseTypeConfigArray = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}case_management_case_type
							WHERE case_type LIKE %s AND is_active = 1",
					'%' . $wpdb->esc_like( $search_term ) . '%'
				)
			);

		} else {

			// WP_User_Query arguments
			$caseTypeConfigArray = $wpdb->get_results(
				"SELECT * FROM {$wpdb->prefix}case_management_case_type
					WHERE is_active = 1"
			);
		}

		// Hold user info in array
		$casetype_data = array();

		// Check for results
		if ( ! empty( $caseTypeConfigArray ) ) {

			// loop through each author
			foreach ( $caseTypeConfigArray as $items ) {

				$casetype_data[] = array(
					'id'   => $items->case_type_id,
					'text' => $items->case_type,
				);
			}
		}

		echo json_encode( $casetype_data );
		die;
	}

	public function scm_sendEmail( $caseID, $action, $userEmail, $assignToEmail, $adminEmail = null ) {

		global $wpdb;

		$configArray = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}case_management_configuration LIMIT 1" );

		// Check if assign to user exist in variable @assignToEmail
		if ( $assignToEmail !== null ) {
			$assignTouser  = get_user_by( 'id', $assignToEmail );
			$assignToEmail = $assignTouser->user_email;
		}

		switch ( $action ) {

			case 'New':
				// User Notification
				foreach ( $configArray as $configSetting ) {

					$headers[] = isset( $configSetting->email_from ) ? 'From: Sovratec Support <' . $configSetting->email_from . '>' : 'From: Sovratec Support <imessanger@sovratec.com>';
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$subject   = 'Notification of new case';

					// User Notification Content
					$body        = isset( $configSetting->email_to_user ) ? $configSetting->email_to_user : '<p>Hello, you have successfully created a new case.&nbsp; Thank you for contacting us!</p><p>Have a great day!</p><p>Support Team</p>';
					$body        = $this->scm_prepareTemplate( $caseID, $body );
					$body       .= '<br/>Thank You!';
					$emailStatus = wp_mail( $userEmail, $subject, $body, $headers );

					// Admin Notification Content
					$body  = isset( $configSetting->email_to_admin ) ? $configSetting->email_to_admin : '<p>Hello, you have a new case in system. Please assign a new user to it. Thank you.</p><p>Have a great day!</p><p>Support Team</p>';
					$body  = $this->scm_prepareTemplate( $caseID, $body );
					$body .= '<br/>Thank You!';
					// Check if assign to user exist in variable @admin email
					if ( $configSetting->scm_admin !== null ) {
						$admin_email_addr   = get_user_by( 'id', $configSetting->scm_admin );
						$assignToAdminEmail = $admin_email_addr->user_email;
					}
					$emailStatus = wp_mail( $assignToAdminEmail, $subject, $body, $headers );

				}
				break;

			case 'Completed':
				foreach ( $configArray as $configSetting ) {
					$headers[] = isset( $configSetting->email_from ) ? 'From: Sovratec Support <' . $configSetting->email_from . '>' : 'From: Sovratec Support <imessanger@sovratec.com>';
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$subject   = 'Notification of case completion';

					// Get Admin Email
					if ( $configSetting->scm_admin !== null ) {
						$admin_email_addr   = get_user_by( 'id', $configSetting->scm_admin );
						$assignToAdminEmail = $admin_email_addr->user_email;
					}

					$multiple_recipients = array(
						$userEmail,
						$assignToEmail,
						$assignToAdminEmail,
					);

					// User Notification Content
					$body        = isset( $configSetting->email_to_case_complete ) ? $configSetting->email_to_case_complete : '<p>Hello, Case is completed now!</p><p>Have a great day!</p><p>Support Team</p>';
					$body        = $this->scm_prepareTemplate( $caseID, $body );
					$body       .= '<br/>Thank You!';
					$emailStatus = wp_mail( $multiple_recipients, $subject, $body, $headers );

				}
				break;

			case 'Processing':
				foreach ( $configArray as $configSetting ) {
					$headers[] = isset( $configSetting->email_from ) ? 'From: Sovratec Support <' . $configSetting->email_from . '>' : 'From: Sovratec Support <imessanger@sovratec.com>';
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$subject   = 'Notification of case status update';

					// User Notification Content
					$body        = isset( $configSetting->email_to_user ) ? $configSetting->email_to_user : '<p>Hello, you have successfully created a new case.&nbsp; Thank you for contacting us!</p><p>Have a great day!</p><p>Support Team</p>';
					$body        = $this->scm_prepareTemplate( $caseID, $body );
					$body       .= '<br/>Thank You!';
					$emailStatus = wp_mail( $userEmail, $subject, $body, $headers );

					// Assignee Notification Content
					$body        = isset( $configSetting->email_to_assignee ) ? $configSetting->email_to_assignee : '<p>Hello, you have a new case in system. Please check. Thank you.</p><p>Have a great day!</p><p>Support Team</p>';
					$body        = $this->scm_prepareTemplate( $caseID, $body );
					$body       .= '<br/>Thank You!';
					$emailStatus = wp_mail( $assignToEmail, $subject, $body, $headers );

				}
				break;
		}
		return $emailStatus;
	}

	public function scm_prepareTemplate( $caseID, $email_template ) {

		global $wpdb;

		$case_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}case_management_case WHERE case_id = %d", $caseID ) );

		foreach ( $case_details as $case ) {

			$author_info = get_userdata( $case->assign_to );
			if ( ( ! $author_info->first_name ) && ( ! $author_info->last_name ) ) {
				$first_name = $author_info->display_name;
				$last_name  = '';
			} else {
				$first_name = $author_info->first_name;
				$last_name  = $author_info->last_name;
			}

			$caseID         = $case->case_id;
			$caseType       = $wpdb->get_var( $wpdb->prepare( "SELECT case_type as caseName FROM {$wpdb->prefix}case_management_case_type WHERE case_type_id  = %d", $case->case_type ) );
			$caseComm       = $wpdb->get_var( $wpdb->prepare( "SELECT comment_description AS caseComment FROM {$wpdb->prefix}case_management_comments WHERE case_id = %d ORDER BY comment_id DESC LIMIT 1", $caseID ) );
			$Name           = $case->first_name . ' ' . $case->last_name;
			$caseEmail      = $case->email;
			$caseStatus     = $case->case_status;
			$caseAssign     = $first_name . ' ' . $last_name;
			$workStatus     = $wpdb->get_var( $wpdb->prepare( "SELECT workflow_item FROM {$wpdb->prefix}case_management_workflow_list WHERE workflow_id = %d", $case->workflow_status ) );
			$caseCreated    = date( 'M d, Y H:i', strtotime( $case->created_on ) );
			$Description    = $case->case_description;
			$caseResolution = $case->case_resolution;
			$caseCompleted  = date( 'M d, Y H:i', strtotime( $case->completed_date ) );

			$data = array(
				'CASEID'           => $caseID,
				'CASETYPE'         => $caseType,
				'NAME'             => $Name,
				'CASEASSIGN'       => $caseAssign,
				'CASESTATUS'       => $caseStatus,
				'WORKSTATUS'       => $workStatus,
				'CASECREATED'      => $caseCreated,
				'DESCRIPTION'      => $Description,
				'COMMENT'          => $caseComm,
				'CASERESOLUTION'   => $caseResolution,
				'CASECOMPLETEDATE' => $caseCompleted,
			);
		}

		foreach ( $data as $key => $value ) {
			$securePlaceholder   = strtoupper( $key );
			$preparedPlaceholder = '{{' . $securePlaceholder . '}}';
			$email_template      = str_replace( $preparedPlaceholder, $value, $email_template );
		}

			return $email_template;
	}
	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string $hook             The name of the WordPress action that is being registered.
	 * @param    object $component        A reference to the instance of the object on which the action is defined.
	 * @param    string $callback         The name of the function definition on the $component.
	 * @param    int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string $hook             The name of the WordPress filter that is being registered.
	 * @param    object $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string $callback         The name of the function definition on the $component.
	 * @param    int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array  $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string $hook             The name of the WordPress filter that is being registered.
	 * @param    object $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string $callback         The name of the function definition on the $component.
	 * @param    int    $priority         The priority at which the function should be fired.
	 * @param    int    $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}
}
