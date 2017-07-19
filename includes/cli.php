<?php
/**
 * Ajax endpoints.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Maintainn_Tools_Cli
 */
class Maintainn_Tools_Cli extends WP_CLI_Command {

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Setup hooks.
	 */
	public function hooks() {
		WP_CLI::add_command( 'checksums', 'Maintainn_Tools_Cli' );
	}

	/**
	 * Verify core checksums.
	 *
	 * @param array $args       WP-CLI args.
	 * @param array $assoc_args WP-CLI  associative args.
	 */
	public function core( $args = array(), $assoc_args = array() ) {
		// Use main plugin class to access checksums class.
		Maintainn_Tools::get_instance()->verify_checksums->verify_core();
	}
}
