<?php
/**
 * Plugin Name: Maintainn Tools
 * Version: 2.0.3
 * Description: A plugin to Maintainn your WordPress website
 * Author: Maintainn
 * Author URI: https://maintainn.com/
 * Plugin URI: https://pluginize.com/
 * Text Domain: maintainn-tools
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2017 WebDevStudios / Maintainn (email : info@maintainn.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Autoloads files with classes when needed.
 *
 * @since 0.1.0
 * @param string $class_name Name of the class being requested.
 * @return  null
 */

define( 'TOOLS_VERSION', '2.0.3' ); // Left for legacy purposes.
define( 'MAINTAINN_TOOLS_VERSION', '2.0.3' );

/**
 * Autoloader for the Maintainn Tools plugin.
 *
 * @param  string $class_name Classname to attempt to autoload.
 * @return void
 */
function maintainn_customer_autoload_classes( $class_name ) {
	// Bail early if the class isn't prefixed correctly.
	if ( 0 !== strpos( $class_name, 'Maintainn_Tools' ) ) {
		return;
	}

	$filename = strtolower( str_ireplace(
		array( 'Maintainn_Tools_', '_' ),
		array( '', '-' ),
		$class_name
	) );

	Maintainn_Tools::include_file( $filename );
}
spl_autoload_register( 'maintainn_customer_autoload_classes' );


/**
 * Main initiation class
 *
 * @since  0.1.0
 * @var  string $version  Plugin version
 * @var  string $basename Plugin basename
 * @var  string $url      Plugin URL
 * @var  string $path     Plugin Path
 */
class Maintainn_Tools {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  0.1.0
	 */
	const VERSION = '2.0.3';

	/**
	 * URL of plugin directory
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $path = '';

	/**
	 * Plugin basename
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $basename = '';

	/**
	 * Utilities
	 *
	 * @var string
	 */
	public $utilities = '';

	/**
	 * Log
	 *
	 * @var string
	 */
	public $log = '';

	/**
	 * AJAX
	 *
	 * @var string
	 */
	public $ajax = '';
	/**
	 * Checksums
	 *
	 * @var string
	 */
	public $verify_checksums = '';

	/**
	 * WP-CLI
	 *
	 * @var string
	 */
	public $cli = '';

	/**
	 * Dashboard
	 *
	 * @var string
	 * @since 2.0.0
	 */
	public $dashboard = '';

	/**
	 * Singleton instance of plugin
	 *
	 * @var object
	 * @since  0.1.0
	 */
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  0.1.0
	 * @return object Maintainn_Scanner A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 *
	 * @since  0.1.0
	 */
	protected function __construct() {
		$cli = defined( 'WP_CLI' ) && WP_CLI;

		// Bail early if not admin.
		if ( ! is_admin() && ! $cli ) {
			return;
		}

		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );

		$this->plugin_classes();
		$this->hooks();
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since 0.1.0
	 */
	protected function plugin_classes() {
		// Include helpers.
		$this->helpers = new Maintainn_Tools_Helpers( $this );

		$this->log = new Maintainn_Tools_Log( $this );

		$this->ajax = new Maintainn_Tools_Ajax( $this );

		// Scanner admin option.
		//$this->scanner = new Maintainn_Tools_Scanner( $this );

		$this->verify_checksums = new Maintainn_Tools_Verify_Checksums( $this );

		// Only add wp cli class if running via cli.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$this->cli = new Maintainn_Tools_Cli( $this );
		}

		// Load only on admin side.
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			$this->dashboard = new Maintainn_Tools_Dashboard( $this );
		}

		// Our availabe extensions|addons.
		$this->extensions = new Maintainn_Tools_Extensions( $this );

		// Only run this if it is active.
		if ( $this->extensions->is_active( 'notes' ) ) {
			$this->include_vendor( 'plugin-police' );
			$this->include_vendor( 'tools-view' );
			$this->include_vendor( 'cpt-core' );
			$this->include_vendor( 'plugin-notes' );
			$this->include_vendor( 'class-tools-form' );
		}
	}

	/**
	 * Add hooks and filters
	 *
	 * @since 0.1.0
	 */
	public function hooks() {
		register_activation_hook( __FILE__, array( $this, '_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, '_deactivate' ) );

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Activate the plugin
	 *
	 * @since 0.1.0
	 */
	function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 *
	 * @since 0.1.0
	 */
	function _deactivate() {}

	/**
	 * Init hooks
	 *
	 * @since 0.1.0
	 */
	public function init() {
		if ( $this->check_requirements() ) {
			load_plugin_textdomain( 'maintainn-tools', false, dirname( $this->basename ) . '/languages/' );
		}
	}

	/**
	 * Check that all plugin requirements are met
	 *
	 * @since 0.1.0
	 *
	 * @return boolean
	 */
	public static function meets_requirements() {
		// Do checks for required classes / functions.
		// function_exists('') & class_exists('')

		// We have met all requirements.
		return true;
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since 0.1.0
	 *
	 * @return boolean result of meets_requirements
	 */
	public function check_requirements() {
		if ( ! $this->meets_requirements() ) {
			// Display our error.
			echo '<div id="message" class="error">';
			echo '<p>' . sprintf( __( 'Maintainn Scanner is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'maintainn-tools' ), esc_url( admin_url( 'plugins.php' ) ) ) . '</p>';
			echo '</div>';

			// Deactivate our plugin.
			deactivate_plugins( $this->basename );

			return false;
		}

		return true;
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since 0.1.0
	 *
	 * @param string $field Field to fetch.
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory.
	 *
	 * @since 0.1.0
	 *
	 * @param string $filename Name of the file to be included.
	 * @return bool Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( 'includes/' . $filename . '.php' );

		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * Include a file from the includes directory.
	 *
	 * @since 0.1.0
	 * @param string $filename Name of the file to be included.
	 * @return bool Result of include call.
	 */
	public static function include_vendor( $filename ) {
		$file = self::dir( 'vendor/' . $filename . '.php' );

		if ( file_exists( $file ) ) {
			return include_once( $file );
		}

		return false;
	}

	/**
	 * Include a file from the includes directory.
	 *
	 * @since 0.1.0
	 *
	 * @param string $filename Name of the file to be included.
	 * @return bool Result of include call.
	 */
	public static function include_view( $filename ) {
		$file = self::dir( 'views/' . $filename . '.php' );

		if ( file_exists( $file ) ) {
			return include( $file );
		}

		return false;
	}

	/**
	 * This plugin's directory.
	 *
	 * @since 0.1.0
	 *
	 * @param string $path (optional) appended path.
	 * @return string       Directory and path
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url.
	 *
	 * @since 0.1.0
	 *
	 * @param string $path (optional) appended path.
	 * @return string URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}

	/**
	 * Return plugin version.
	 */
	public function version() {
		return self::VERSION;
	}

	/**
	 * Suffix for scripts/styles
	 *
	 * @since 0.1.0
	 * @return  string
	 */
	public function get_script_suffix() {
		// If WP is in script debug, or we pass ?script_debug in a URL - set debug to true.
		$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true )
			|| ( isset( $_GET['script_debug'] ) )
			? true
			: false;

		return ( true === $debug ) ? '' : '.min';
	}

}

/**
 * Grab the Maintainn_Tools object and return it.
 * Wrapper for Maintainn_Tools::get_instance()
 *
 * @since 0.1.0
 *
 * @return Maintainn_Tools  Singleton instance of plugin class.
 */
function maintainn_customer() {
	return Maintainn_Tools::get_instance();
}

/**
 * Set the Maintainn Tools Setting Link
 *
 * @since 0.1.0
 *
 * @param array $links      Action links for the plugin.
 * @return Maintainn_Tools  Maintainn Tools Link
 */
function maintainn_tools_link( $links ) {
	$settings_link = '<a href="' . admin_url( 'admin.php?page=maintainn' ) . '">' . __( 'Settings', 'maintainn-tools' ) . '</a>';

	array_push( $links, $settings_link );
	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'maintainn_tools_link' );

// Kick it off.
maintainn_customer();
