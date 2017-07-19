<?php
/**
 * Admin pages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Maintainn_Tools_Scanner
 */
class Maintainn_Tools_Scanner {
	/**
	 * Version for scripts
	 */
	const VERSION = '1.0.0';

	/**
	 * Parent plugin class
	 *
	 * @var object
	 * @since 0.1.0
	 */
	protected $plugin = null;

	/**
	 * Option key, and option page slug
	 *
	 * @var string
	 * @since 0.1.0
	 */
	private $key = 'maintainn_scanner_admin';

	/**
	 * Options page metabox id
	 *
	 * @var string
	 * @since 0.1.0
	 */
	private $metabox_id = 'maintainn_scanner_admin_metabox';

	/**
	 * Options Page title
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 *
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 *
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		$this->hooks();
		$this->title = __( 'Maintainn Scanner','maintainn-scanner' );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_init', array( $this, 'add_options_page_metabox' ) );
	}

	/**
	 * Register our setting to WP
	 *
	 * @since 0.1.0
	 */
	public function admin_init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 *
	 * @since 0.1.0
	 */
	public function add_options_page() {
		$this->options_page = add_menu_page(
			$this->title,
			$this->title,
			'manage_options',
			$this->key,
			array( $this, 'admin_page_display' )
		);
	}
}
