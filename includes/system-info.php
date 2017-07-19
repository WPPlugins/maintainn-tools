<?php
/**
 * Collect site information for display.
 *
 * @package Maintainn Tools.
 * @since 1.0.0
 */

/**
 * Class Maintainn_Tools_System_Info
 *
 * @since 1.0.0
 */
class Maintainn_Tools_System_Info {

	/**
	 * Array of various system information items.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	public $system_info = array();

	/**
	 * Browser data class instance.
	 *
	 * @var Browser
	 * @since 1.0.0
	 */
	protected $browser;

	/**
	 * Copy of WPDB global instance.
	 *
	 * @var WPDB
	 * @since 1.0.0
	 */
	protected $wpdb;

	/**
	 * Maintainn_Tools_System_Info constructor.
	 *
	 * @param Browser $browser Browser() instance.
	 * @param WPDB    $wpdb    $wpdb global instance.
	 */
	public function __construct( Browser $browser, WPDB $wpdb ) {
		$this->browser = $browser;
		$this->wpdb    = $wpdb;
	}

	/**
	 * Populate our browser-based data points.
	 *
	 * @since 1.0.0
	 */
	private function set_browser() {
		$this->system_info['browser']['name']     = $this->browser->getBrowser();
		$this->system_info['browser']['version']  = $this->browser->getVersion();
		$this->system_info['browser']['ua']       = $this->browser->getUserAgent();
		$this->system_info['browser']['platform'] = $this->browser->getPlatform();
	}

	/**
	 * Populate our theme-based data points.
	 *
	 * @since 1.0.0
	 */
	private function set_theme_data() {
		$theme_data = wp_get_theme();
		$this->system_info['theme'] = $theme_data->Name . ' ' . $theme_data->Version;
	}

	/**
	 * Populate our plugin-based data points.
	 *
	 * @since 1.0.0
	 */
	private function set_plugin_data() {
		$plugins         = get_plugins();
		$active_plugins  = get_option( 'active_plugins', array() );
		$mu_plugins      = get_mu_plugins();

		$this->system_info['plugins']['plugin_count']           = count( $plugins );
		$this->system_info['plugins']['plugins']                = $plugins;
		$this->system_info['plugins']['active_plugin_count']    = count( $active_plugins );
		$this->system_info['plugins']['active_plugins']         = $active_plugins;
		$this->system_info['plugins']['inactive_plugin_count']  = count( $plugins ) - count( $active_plugins );
		$this->system_info['plugins']['mu_plugin_count']        = count( $mu_plugins );
		$this->system_info['plugins']['mu_plugins']             = $mu_plugins;

		if ( is_multisite() ) {
			$network_plugins = wp_get_active_network_plugins();
			$network_active  = get_site_option( 'active_sitewide_plugins', array() );

			$this->system_info['plugins']['network_plugins']        = $network_plugins;
			$this->system_info['plugins']['network_plugin_count']   = count( $network_plugins );
			$this->system_info['plugins']['network_active_plugins'] = $network_active;
		}
	}

	/**
	 * Populate our site-based data points.
	 *
	 * @since 1.0.0
	 */
	private function set_site_info() {
		$page_front = get_option( 'page_on_front' );
		$posts_page = get_option( 'page_for_posts' );

		$this->system_info['site_info']['is_multisite']   = is_multisite() ? __( 'Yes', 'maintainn-tools' ) : __( 'No', 'maintainn-tools' );
		$this->system_info['site_info']['site_url']       = site_url();
		$this->system_info['site_info']['home_url']       = home_url();
		$this->system_info['site_info']['wp_version']     = get_bloginfo( 'version' );
		$this->system_info['site_info']['permalinks']     = get_option( 'permalink_structure' );
		$this->system_info['site_info']['post_types']     = implode( ', ', get_post_types( '', 'names' ) );
		$this->system_info['site_info']['post_statuses']  = implode( ', ', get_post_stati() );
		$this->system_info['site_info']['show_on_front']  = get_option( 'show_on_front' );
		$this->system_info['site_info']['page_on_front']  = get_the_title( $page_front ) . ( "#{$page_front}" );
		$this->system_info['site_info']['page_for_posts'] = get_the_title( $posts_page ) . ( "#{$posts_page}" );
		$this->system_info['site_info']['wp_debug']       = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? __( 'Enabled', 'maintainn-tools' ) : __( 'Disabled', 'maintainn-tools' );
	}

	/**
	 * Populate our server-based data points.
	 *
	 * @since 1.0.0
	 */
	private function set_server_info() {
		$table_prefix_length = strlen( $this->wpdb->prefix );
		$table_prefix_status = ( strlen( $this->wpdb->prefix ) > 16 ) ? __( 'ERROR: Too Long', 'maintainn-tools' ) : __( 'Acceptable', 'maintainn-tools' );
		$display_errors      = ini_get( 'display_errors' );

		$this->system_info['server_info']['php_version']              = PHP_VERSION;
		$this->system_info['server_info']['mysql_version']            = $this->wpdb->db_version();
		$this->system_info['server_info']['web_server']               = $_SERVER['SERVER_SOFTWARE'];
		$this->system_info['server_info']['php_safe_mode']            = ini_get( 'safe_mode' ) ? __( 'Yes', 'maintainn-tools' ) : __( 'No', 'maintainn-tools' );
		$this->system_info['server_info']['php_memory_limit']         = ini_get( 'memory_limit' );
		$this->system_info['server_info']['php_upload_max_filesize']  = ini_get( 'upload_max_filesize' );
		$this->system_info['server_info']['php_post_max_size']        = ini_get( 'post_max_size' );
		$this->system_info['server_info']['php_time_limit']           = ini_get( 'max_execution_time' );
		$this->system_info['server_info']['php_max_input_vars']       = ini_get( 'max_input_vars' );
		$this->system_info['server_info']['wp_memory_limit']          = WP_MEMORY_LIMIT;
		$this->system_info['server_info']['wp_table_prefix_length']   = $table_prefix_length;
		$this->system_info['server_info']['wp_table_prefix_status']   = $table_prefix_status;
		$this->system_info['server_info']['sessions']                 = isset( $_SESSION ) ? __( 'Yes', 'maintainn-tools' ) : __( 'No', 'maintainn-tools' );
		$this->system_info['server_info']['session_name']             = esc_html( ini_get( 'session.name' ) );
		$this->system_info['server_info']['session_cookie_path']      = esc_html( ini_get( 'session.cookie_path' ) );
		$this->system_info['server_info']['session_save_path']        = esc_html( ini_get( 'session.save_path' ) );
		$this->system_info['server_info']['session_use_cookies']      = ini_get( 'session.use_cookies' ) ? __( 'On', 'maintainn-tools' ) : __( 'Off', 'maintainn-tools' );
		$this->system_info['server_info']['session_use_only_cookies'] = ini_get( 'session.use_only_cookies' ) ? __( 'On', 'maintainn-tools' ) : __( 'Off', 'maintainn-tools' );
		$this->system_info['server_info']['display_errors']           = ( ini_get( 'disable_errors' ) ) ? sprintf( __( 'On (%s)', 'maintainn-tools' ), $display_errors ) : __( 'N/A', 'maintainn-tools' );
		$this->system_info['server_info']['fsockopen']                = ( function_exists( 'fsockopen' ) ) ? __( 'On', 'maintainn-tools' ) : __( 'Off', 'maintainn-tools' );
		$this->system_info['server_info']['curl']                     = ( function_exists( 'curl_init' ) ) ? __( 'On', 'maintainn-tools' ) : __( 'Off', 'maintainn-tools' );
		$this->system_info['server_info']['soap_client']              = ( class_exists( 'SoapClient' ) ) ? __( 'On', 'maintainn-tools' ) : __( 'Off', 'maintainn-tools' );
		$this->system_info['server_info']['suhosin']                  = ( extension_loaded( 'suhosin' ) ) ? __( 'On', 'maintainn-tools' ) : __( 'Off', 'maintainn-tools' );
	}

	/**
	 * Construct our system_info property and return to user.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of all our content.
	 */
	public function get_system_info() {
		$this->set_browser();
		$this->set_theme_data();
		$this->set_plugin_data();
		$this->set_site_info();
		$this->set_server_info();

		return $this->system_info;
	}
}
