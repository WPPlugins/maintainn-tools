<?php
/**
 * Verify WP checksums
 */

/**
 * Class Maintainn_Tools_Verify_Checksums
 */
class Maintainn_Tools_Verify_Checksums {

	/**
	 * Parent plugin class
	 *
	 * @var object
	 * @since 0.1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 *
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Verify Core checksums.
	 *
	 * @param string $version WordPress version.
	 * @param string $locale  WordPress locale.
	 * @return bool
	 */
	public function verify_core( $version = null, $locale = null ) {
		return $this->verify_core_checksums( $version, $locale );
	}

	/**
	 * Verify WordPress files against WordPress.org's checksums.
	 *
	 * Specify version to verify checksums without loading WordPress.
	 *
	 * @param string $version Verify checksums against a specific version of WordPress.
	 * @param string $locale  Verify checksums against a specific locale of WordPress.
	 * @return bool
	 */
	protected function verify_core_checksums( $version = null, $locale = null ) {
		global $wp_version, $wp_local_package;

		// Check against specific version.
		$wpversion = ( ! empty( $version ) ) ? $version : $wp_version;

		// Check against specific locale.
		$wplocale = ( ! empty( $locale ) ) ? $locale : $wp_local_package;

		// Load WP if not already loaded.
		if ( empty( $wp_version ) ) {
			get_runner()->load_wordpress();
		}

		// Determine locale.
		$locale = isset( $wplocale ) ? $wplocale : 'en_US';

		// Fetch checksums from WP.org.
		$checksums = $this->get_core_checksums( $wpversion, $locale );

		// Bail early if no checksums.
		if ( ! is_array( $checksums ) ) {
			$this->plugin->log->add_to_log( 'Couldn\'t get checksums from WordPress.org.', 'error' );
			return false;
		}

		$has_errors = false;

		foreach ( $checksums as $file => $checksum ) {
			// Skip files which get updated.
			if ( 'wp-content' == substr( $file, 0, 10 ) ) {
				continue;
			}

			// Log if file doesn't exist.
			if ( ! file_exists( ABSPATH . $file ) ) {
				$this->plugin->log->add_to_log( "File doesn't exist: {$file}", 'warning' );
				$has_errors = true;
				continue;
			}

			// Get hash for file.
			$md5_file = md5_file( ABSPATH . $file );

			// Log if checksum doesn't match.
			if ( $md5_file !== $checksum ) {
				$this->plugin->log->add_to_log( "File doesn't verify against checksum: {$file}", 'warning' );
				$has_errors = true;
			}
		}

		// Log results of checksums test.
		if ( ! $has_errors ) {
			$this->plugin->log->add_to_log( 'WordPress install verifies against checksums.', 'success' );
			return true;
		} else {
			$this->plugin->log->add_to_log( 'WordPress install doesn\'t verify against checksums.', 'error' );
			return false;
		}
	}

	/**
	 * Security copy of the core function with Requests - Gets the checksums for the given version of WordPress.
	 *
	 * @param string $version Version string to query.
	 * @param string $locale  Locale to query.
	 * @return bool|array False on failure. An array of checksums on success.
	 */
	private function get_core_checksums( $version, $locale ) {
		$url = 'https://api.wordpress.org/core/checksums/1.0/?' . http_build_query( compact( 'version', 'locale' ), null, '&' );

		// Fetch API response.
		$response = wp_remote_get( $url );

		// Bail early if response isn't an array or wp_error.
		if ( ! is_array( $response ) || is_wp_error( $response ) ) {
			$this->plugin->log->add_to_log( 'Bad response from: ' . $url . ' ' . print_r( $response, true ) );
			return false;
		}

		// Bail early if wrong response code.
		if ( empty( $response['response']['code'] ) || ! ( 200 === $response['response']['code'] ) ) {
			$this->plugin->log->add_to_log( 'Bad response code from: ' . $url . ' ' . print_r( $response, true ) );
			return false;
		}

		// Bail early if no response body.
		if ( empty( $response['body'] ) ) {
			$this->plugin->log->add_to_log( 'Empty response body: ' . $url . ' ' . print_r( $response, true ) );
			return false;
		}

		// Get response and decode.
		$response = trim( $response['body'] );
		$response = json_decode( $response, true );

		// Bail early if not correct format.
		if ( ! is_array( $response ) || ! isset( $response['checksums'] ) || ! is_array( $response['checksums'] ) ) {
			return false;
		}

		// Return only checksums.
		return $response['checksums'];
	}
}
