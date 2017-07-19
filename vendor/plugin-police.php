<?php

final class MT_Notes {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  0.1.0
	 */
	const VERSION = '0.1.0';

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
	 * Detailed activation error messages
	 *
	 * @var array
	 * @since  0.1.0
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin
	 *
	 * @var MT_Notes
	 * @since  0.1.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of WDSPP_Add_plugin_row
	 *
	 * @since0.1.0
	 * @var WDSPP_Add_plugin_row
	 */
	protected $add_plugin_row;

	/**
	 * Instance of Maintainn_Tools_View
	 *
	 * @since0.1.0
	 * @var Maintainn_Tools_View
	 */
	protected $view;

	/**
	 * Instance of WDSPP_Plugin_police
	 *
	 * @since0.1.0
	 * @var WDSPP_Plugin_police
	 */
	protected $plugin_police;

	/**
	 * Instance of Maintain_Tools_Form
	 *
	 * @since0.1.0
	 * @var Maintain_Tools_Form
	 */
	protected $dynamic_form;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  0.1.0
	 * @return MT_Notes A single instance of this class.
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
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function plugin_classes() {
		// Attach other plugin classes to the base plugin class.
		$this->view          = new Maintainn_Tools_View( $this );
		$this->plugin_police = new Maintainn_Notes( $this );
		$this->dynamic_form  = new Maintain_Tools_Form( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function hooks() {
		// Priority needs to be:
		// < 10 for CPT_Core,
		// < 5 for Taxonomy_Core,
		// 0 Widgets because widgets_init runs at init priority 1.
		add_action( 'plugins_loaded', array( $this, 'init' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10, 1 );
	}

	/**
	 * EQ the script.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts( $page ) {
		if ( 'plugins.php' !== $page ) {
			return;
		}

		wp_enqueue_script( 'maintainn-notes', plugins_url( '/assets/js/plugin-notes.js', dirname( __FILE__ ) ), array( 'jquery' ), '0.1.0' );
		$l10n = array( 'loading_message' => __( 'Loading&hellip;', 'maintainn-tools' ) );
		wp_localize_script( 'maintainn-notes', 'm_messages', $l10n );

		// Temporarily dsiabling the emoji-picker
		// wp_enqueue_script( 'maintainn-emojipicker', plugins_url( '/assets/js/jquery.emojipicker.js', dirname( __FILE__ ) ), array( 'jquery' ), '0.1.0' );
		// wp_enqueue_script( 'maintainn-emojis', plugins_url( '/assets/js/jquery.emojis.js', dirname( __FILE__ ) ), array( 'jquery' ), '0.1.0' );
		// wp_enqueue_style( 'maintainn-emojipicker-styles', plugins_url( '/assets/css/jquery.emojipicker.css', dirname( __FILE__ ) ) );
		// wp_enqueue_style( 'maintainn-emojis-styles', plugins_url( '/assets/css/jquery.emojipicker.a.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'maintainn-fontawesome', plugins_url( '/assets/css/font-awesome.min.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'maintainn-css', plugins_url( '/assets/css/plugin-admin-notes.css', dirname( __FILE__ ) ) );
	}

	/**
	 * Activate the plugin
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function _deactivate() {
	}

	/**
	 * Init hooks
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function init() {
		// bail early if requirements aren't met
		if ( ! $this->check_requirements() ) {
			return;
		}

		// initialize plugin classes
		$this->plugin_classes();

		if ( ! defined( 'DOING_AJAX' ) && file_exists( $this->path . 'pluginnotes.log' ) ) {
			file_put_contents( $this->path . 'pluginnotes.log', '' );
		}

		if ( 'WP_UNINSTALL_PLUGIN' && isset( $_POST['action'] ) && 'delete-plugin' == $_POST['action'] ) {
			error_log( print_r( $_POST, 1 ) );
			$this->view->remove_plugin( $_POST['slug'] );
		}

	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.1.0
	 * @return boolean result of meets_requirements
	 */
	public function check_requirements() {
		// bail early if pluginmeets requirements
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

		// Deactivate our plugin.
		add_action( 'admin_init', array( $this, 'deactivate_me' ) );

		return false;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function deactivate_me() {
		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Check that all plugin requirements are met
	 *
	 * @since  0.1.0
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {
		// Do checks for required classes / functions
		// function_exists('') & class_exists('').
		// We have met all requirements.
		// Add detailed messages to $this->activation_errors array
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function requirements_not_met_notice() {
		// compile default message
		$default_message = sprintf(
			__( 'WDS Plugin Police is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'wds-plugin-police' ),
			admin_url( 'plugins.php' )
		);

		// default details to null
		$details = null;

		// add details if any exist
		if ( ! empty( $this->activation_errors ) && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// output errors
		?>
		<div id="message" class="error">
			<p><?php echo $default_message; ?></p>
			<?php echo $details; ?>
		</div>
		<?php
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  0.1.0
	 *
	 * @param string $field Field to get.
	 *
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
			case 'view':
			case 'plugin_police':
			case 'dynamic_form':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory
	 *
	 * @since  0.1.0
	 *
	 * @param  string $filename Name of the file to be included.
	 *
	 * @return bool   Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( $filename . '.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}

		return false;
	}

	/**
	 * This plugin's directory
	 *
	 * @since  0.1.0
	 *
	 * @param  string $path (optional) appended path.
	 *
	 * @return string       Directory and path
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );

		return $dir . $path;
	}

	/**
	 * This plugin's url
	 *
	 * @since  0.1.0
	 *
	 * @param  string $path (optional) appended path.
	 *
	 * @return string       URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );

		return $url . $path;
	}
}

/**
 * Grab the MT_Notes object and return it.
 * Wrapper for MT_Notes::get_instance()
 *
 * @since  0.1.0
 * @return MT_Notes  Singleton instance of plugin class.
 */
function MT_Notes() {
	return mt_notes::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( mt_notes(), 'hooks' ), 0 );

/**
 * Deletes the plugin data.
 *
 * @since 0.1.0
 */
function uninstall_wds_plugin_admin_notes() {

	global $wpdb;

	$ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_type='maintainn-notes'" );

	foreach ( $ids as $post ) {
		wp_delete_post( $post, true );
	}

	delete_option( 'maintainn_lock_updates' );
	delete_option( 'maintainn_plugin_notes' );
}

register_uninstall_hook( __FILE__, 'uninstall_wds_plugin_admin_notes' );
