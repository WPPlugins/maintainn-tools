<?php
/**
 * Ajax endpoints.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Maintainn_Tools_Ajax
 */
class Maintainn_Tools_Ajax {
	/**
	 * Parent plugin class
	 *
	 * @var object
	 * @since  0.1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 *
	 * @param object $plugin Parent plugin instance.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		$this->hooks();
	}

	/**
	 * Setup hooks.
	 */
	public function hooks() {
		add_action( 'wp_ajax_maintain_scanner_core_checksums', array( $this, 'verify_core_checksums' ) );
		add_action( 'wp_ajax_nopriv_maintain_scanner_core_checksums', array( $this, 'verify_core_checksums' ) );
	}

	/**
	 * Verify Core checksums.
	 */
	public function verify_core_checksums() {
		$security_check_passes = (
			( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) )
			&& ( 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) )
			&& isset( $_POST['verify_checksums'] )
			&& wp_verify_nonce( $_POST['verify_checksums'], 'verify_core_checksums' )
		);

		// Bail if security checks don't pass.
		if ( ! $security_check_passes ) {
			wp_send_json_error( $_POST );
		}

		$version = isset( $_POST['version'] ) ? $_POST['version'] : null;
		$locale = isset( $_POST['locale'] ) ? $_POST['locale'] : null;

		$checksum_check = $this->plugin->verify_checksums->verify_core( $version, $locale );

		$response = array(
			'result' => $checksum_check,
			'log' => $this->plugin->log->get_formatted_log(),
		);

		wp_send_json_success( $response );
	}
}
