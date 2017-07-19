<?php
/**
 * Logging for plugin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Maintainn_Tools_Log
 */
class Maintainn_Tools_Log {

	/**
	 * Keep track of logs
	 *
	 * @var array
	 */
	protected $log = array();

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
	 * Add message to log array.
	 *
	 * @param string $message Message to log.
	 * @param string $type    Log type.
	 */
	public function add_to_log( $message, $type = 'text' ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			switch ( $type ) {
				case 'error':
					WP_CLI::error( $message );
					break;

				case 'warning':
					WP_CLI::warning( $message );
					break;

				case 'success':
					WP_CLI::success( $message );
					break;

				default :
					WP_CLI::line( $message );
					break;
			}
		} else {
			// Add to array.
			switch ( $type ) {
				case 'error':
					$this->log[] = '<p class="error">' . $message . '</p>';
					break;

				case 'warning':
					$this->log[] = '<p class="warning">' . $message . '</p>';
					break;

				case 'success':
					$this->log[] = '<p class="success">' . $message . '</p>';
					break;

				default :
					$this->log[] = '<p>' . $message . '</p>';
					break;
			}
		}
	}

	/**
	 * Get the log array
	 *
	 * @return array log messages array.
	 */
	public function get_log() {
		return $this->log;
	}

	/**
	 * Display the log array
	 *
	 * @return string $value Formatted log.
	 */
	public function get_formatted_log() {
		return implode( '<br />', $this->get_log() );
	}

	/**
	 * Echos formatted log.
	 */
	public function display_log() {
		echo $this->get_formatted_log();
	}
}
