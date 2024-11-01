<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sov_Case_Management
 * @subpackage Sov_Case_Management/admin
 * @author     Sovratec <https://sovratec.com/>
 */
class Sov_Case_Management_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		/**
		 * Add plugin menu item with logo
		 */
		add_action( 'admin_menu', array( $this, 'sov_case_management_add_menu' ) );
		add_action( 'admin_menu', array( $this, 'case_type_settings_menu' ) );
		add_action( 'admin_menu', array( $this, 'workflowstatus_settings_menu' ) );
		add_action( 'admin_menu', array( $this, 'email_settings_menu' ) );
	}

	// Supportive function for displaying menu items
	function sov_case_management_add_menu() {
		global $wpdb;
		$notification_count = $wpdb->get_var( "SELECT count( DISTINCT case_id ) as request_counter FROM {$wpdb->prefix}case_management_case WHERE case_status = 'New' " );
		add_menu_page( 'Case Manager ', $notification_count ? sprintf( 'Case Manager <span class="awaiting-mod">%d</span>', $notification_count ) : __( 'Case Manager', 'sov-case-management' ), 'manage_options', 'sov-case-management-dashboard', array( $this, 'sov_case_management_dashboard' ), 'dashicons-feedback', 2 );
		add_submenu_page( 'sov-case-management-dashboard', 'Home', 'Home', 'manage_options', 'sov-case-management-dashboard' );
	}

	// Supportive function for loading case management dashboard
	static function sov_case_management_dashboard() {
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sov-case-management-dashboard.php';

	}

	// Register case_type_settings_menu
	function case_type_settings_menu() {
		add_submenu_page(
			'sov-case-management-dashboard',
			__( 'Case Type Configurations', 'sov-case-management' ),
			__( 'Case Type', 'sov-case-management' ),
			'manage_options',
			'casetype_management',
			array( $this, 'casetype_config_handler' )
		);
	}
	// Register workflowstatus_settings_menu
	function workflowstatus_settings_menu() {
		add_submenu_page(
			'sov-case-management-dashboard',
			__( 'Workflow Status List Configurations', 'sov-case-management' ),
			__( 'Workflow Status', 'sov-case-management' ),
			'manage_options',
			'workflowstatus_management',
			array( $this, 'workflowstatus_config_handler' )
		);
	}

	// Register email_settings_menu
	function email_settings_menu() {
		add_submenu_page(
			'sov-case-management-dashboard',
			__( 'Emails Configurations', 'sov-case-management' ),
			__( 'Email', 'sov-case-management' ),
			'manage_options',
			'email_management',
			array( $this, 'email_config_handler' )
		);
	}

	public function casetype_config_handler() {
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/case-configurations/case-type-settings/class-sov-case-management-casetype.php';
	}
	public function workflowstatus_config_handler() {
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/case-configurations/workflow-status-settings/class-sov-case-management-workflow.php';
	}
	public function email_config_handler() {
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/case-configurations/email-settings/class-sov-case-management-email.php';
	}

	static function off_init() {

		include_once plugin_dir_path( __FILE__ ) . 'ssn/social-security.php';
		include_once plugin_dir_path( __FILE__ ) . 'user-management/user-management.php';
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sov-case-management-admin.css', array(), $this->version, 'all' );
		$screen = get_current_screen();		
		if ( ( strpos( $screen->base, 'page_email_management' ) !== false ) || ( strpos( $screen->base, 'page_workflowstatus_management' ) !== false ) || ( strpos( $screen->base, 'page_casetype_management' ) !== false ) || ( strpos( $screen->base, 'sov-case-management-dashboard' ) !== false ) ) :
			wp_enqueue_style( $this->plugin_name . '-bootstrap-css', plugin_dir_url( __FILE__ ) . 'bootstrap/css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-select2-css', plugin_dir_url( __FILE__ ) . 'assets/lib/select2/css/select2.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-multiselectItem-css', plugin_dir_url( __FILE__ ) . 'assets/lib/multi-select.css', array(), $this->version, 'all' );
			wp_register_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css' );
			wp_enqueue_style( 'jquery-ui' );
		endif;
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sov-case-management-admin.js', array( 'jquery' ), $this->version, false );

		$screen = get_current_screen();		
		if ( ( strpos( $screen->base, 'page_email_management' ) !== false ) || ( strpos( $screen->base, 'page_workflowstatus_management' ) !== false ) || ( strpos( $screen->base, 'page_casetype_management' ) !== false ) || ( strpos( $screen->base, 'sov-case-management-dashboard' ) !== false ) ) :
			wp_enqueue_script( $this->plugin_name . '-bootstrap-js', plugin_dir_url( __FILE__ ) . 'bootstrap/js/bootstrap.min.js', array( 'jquery' ) );
			wp_enqueue_script( $this->plugin_name . '-jquery-tinymce-js', plugin_dir_url( __FILE__ ) . 'assets/lib/tinymce/tinymce.min.js', array( 'jquery' ) );
			wp_enqueue_script( $this->plugin_name . '-jquery-select2-js', plugin_dir_url( __FILE__ ) . 'assets/lib/select2/js/select2.min.js', array( 'jquery' ) );
			wp_enqueue_script( $this->plugin_name . '-jquery-multiselect-js', plugin_dir_url( __FILE__ ) . 'assets/lib/jquery.MultiFile.js', array( 'jquery' ) );
			wp_enqueue_script( $this->plugin_name . '-jquery-multiselectItem-js', plugin_dir_url( __FILE__ ) . 'assets/lib/jquery.multi-select.js', array( 'jquery' ) );			
		endif;
	}
}
