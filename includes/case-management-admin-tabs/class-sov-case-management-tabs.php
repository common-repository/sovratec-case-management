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
class Sov_Case_Management_Tabs {

		// Constructor
	public function __construct() {
		$this->tabs_management();
	}

	public function sov_case_management_tabs_management( $current = 'new-cases' ) {

		global $wpdb;
		$tabs              = array(
			'mycases'        => __( 'My Cases', 'sov-case-management' ),
			'new'            => __( 'New Cases', 'sov-case-management' ),
			'progress'       => __( 'Case in Progress', 'sov-case-management' ),
			'overdue'        => __( 'Overdue Cases', 'sov-case-management' ),
			'completed'      => __( 'Completed Cases', 'sov-case-management' ),
			'configurations' => __( 'Configurations', 'sov-case-management' ),
		);
		$current_user_id   = get_current_user_id();
		$request_counter   = $wpdb->get_var( "SELECT count( DISTINCT case_id ) as request_counter FROM {$wpdb->prefix}case_management_case WHERE case_status = 'New' " );
		$mycases_counter   = $wpdb->get_var( $wpdb->prepare( "SELECT count( DISTINCT case_id ) as mycases_counter FROM {$wpdb->prefix}case_management_case WHERE case_status = 'Processing' AND assign_to = %d", $current_user_id ) );
		$overdue_counter   = $wpdb->get_var( "SELECT count( DISTINCT wcase.case_id ) as overdue_counter FROM {$wpdb->prefix}case_management_case wcase INNER JOIN {$wpdb->prefix}case_management_case_type wtype ON wcase.case_type = wtype.case_type_id WHERE DATE(NOW()) > DATE( DATE_ADD( wcase.case_start_date, INTERVAL wtype.case_type_sla DAY ) ) AND case_status != 'Completed'" );
		$pending_counter   = $wpdb->get_var( "SELECT count( DISTINCT case_id ) as pending_counter FROM {$wpdb->prefix}case_management_case WHERE case_status = 'Processing' AND completed_date IS NULL" );
		$completed_counter = $wpdb->get_var( "SELECT count( DISTINCT case_id ) as completed_counter FROM {$wpdb->prefix}case_management_case WHERE case_status = 'Completed' AND completed_date IS NOT NULL" );
		?>        
			<h2 class="nav-tab-wrapper">
		  <?php
			foreach ( $tabs as $tab => $name ) :
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				?>
					  <a class='nav-tab <?php echo esc_attr( $class ); ?>' href='?page=sov-case-management-dashboard&tab=<?php esc_attr_e( $tab, 'sov-case-management' ); ?>'><?php echo esc_attr( $name ); ?>
						<?php
						if ( $tab == 'new' ) {
							echo '<span class="tab-notification-counter-red" ><span class="plugin-count">' . esc_attr( $request_counter ) . '</span></span>';
						} elseif ( $tab == 'mycases' ) {
							echo '<span class="tab-notification-counter-red" ><span class="plugin-count">' . esc_attr( $mycases_counter ) . '</span></span>';
						} elseif ( $tab == 'overdue' ) {
							echo '<span class="tab-notification-counter-red" ><span class="plugin-count">' . esc_attr( $overdue_counter ) . '</span></span>';
						} elseif ( $tab == 'progress' ) {
							echo '<span class="tab-notification-counter-red" ><span class="plugin-count">' . esc_attr( $pending_counter ) . '</span></span>';
						} elseif ( $tab == 'completed' ) {
						}
						?>
									  
					</a>
				  <?php
			endforeach;
			?>
			</h2>   
		<?php
	}

	public function tabs_management() {
		?>
	
		<div class="wrap">
		  <?php
			if ( isset( $_GET['orderview'] ) ) {
				include_once plugin_dir_path( __FILE__ ) . 'class-sov-case-management-orderview.php';
				exit;
			} elseif ( isset( $_GET['tab'] ) ) {
				$this->sov_case_management_tabs_management( sanitize_text_field( $_GET['tab'] ) );
			} else {
				$this->sov_case_management_tabs_management( 'new' );
			}
			?>
		  <div id="poststuff">
			<?php
			if ( $_GET['page'] == 'sov-case-management-dashboard' ) {
				if ( isset( $_GET['tab'] ) ) {
					$tab = sanitize_text_field( $_GET['tab'] );
				} else {
					$tab = 'new';
				}
				switch ( $tab ) {
					case 'new':
																		include_once plugin_dir_path( __FILE__ ) . 'class-sov-case-management-request.php';

						break;
					case 'progress':
																		include_once plugin_dir_path( __FILE__ ) . 'class-sov-case-management-pending.php';

						break;
					case 'mycases':
																		include_once plugin_dir_path( __FILE__ ) . 'class-sov-case-management-mycases.php';

						break;
					case 'overdue':
																		include_once plugin_dir_path( __FILE__ ) . 'class-sov-case-management-overdue.php';

						break;
					case 'completed':
																		include_once plugin_dir_path( __FILE__ ) . 'class-sov-case-management-completed.php';

						break;
					case 'configurations':
																		include_once plugin_dir_path( __FILE__ ) . 'class-sov-case-management-configurations.php';
				}
			}
			?>
		  </div>
		</div>
		<?php
	}
}
$multiTabs = new Sov_Case_Management_Tabs();
