<?php
/**
 * Dashboard class for Maintainn tools plugin.
 *
 * @version 2.0
 * @package Maintainn Tools
 */

/**
 * Class that holds functonality for the displaying the dashboard.
 */
class Maintainn_Tools_Dashboard {

	/**
	 * Tabs to display.
	 *
	 * @var array
	 */
	protected $tabs = array();

	/**
	 * Page URL.
	 *
	 * @var string
	 */
	public $page_url = '';

	/**
	 * Option key, and option page slug
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $key = 'maintainn-dashboard-widget';

	/**
	 * Options Page title
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $title = '';

	/**
	 * Maintainn_Tools_Dashboard constructor.
	 *
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Set plugin title.
		$this->title = __( 'Maintainn', 'maintainn-tools' );

		add_action( 'plugins_loaded', array( $this, 'setup_globals' ) );
		$this->setup_actions();
	}

	/**
	 * Set up our class global objects.
	 *
	 * @since 2.0.0
	 */
	public function setup_globals() {

		$this->tabs = apply_filters( 'maintainn_tabs',
			array(
				'about'         => __( 'About', 'maintainn-tools' ),
				'site-info'    => __( 'Site Info', 'maintainn-tools' ),
				'site-scanner' => __( 'Site Scanner', 'maintainn-tools' ),
				'tools'        => __( 'Tools', 'maintainn-tools' ),
				'support'      => __( 'Support', 'maintainn-tools' ),
			)
		);

		$this->page_url = admin_url( 'admin.php?page=maintainn' );

	}

	/**
	 * Set up our actions.
	 *
	 * @since 2.0.0
	 */
	private function setup_actions() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
		add_action( 'admin_init', array( 'Maintainn_Tools_Emailing', 'send_email' ) );

		// Add network admin menu page if this is multisite.
		add_action( 'network_admin_menu', array( $this, 'add_network_options_page' ) );
	}

	/**
	 * Add network options page
	 *
	 * @since  0.1.0
	 * @return  void
	 */
	public function add_network_options_page() {
		add_menu_page(
			$this->title,
			$this->title,
			'manage_options',
			$this->key,
			array( $this, 'render_page' )
		);

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Add Dashboard page.
	 */
	public function add_admin_menu() {
		 add_menu_page( $this->title, $this->title, 'manage_options', 'maintainn', array( $this, 'render_page' ) );
	}

	/**
	 * Register Dashboard widget.
	 */
	public function add_dashboard_widget() {
		add_meta_box( $this->key, $this->title, array( $this, 'dashboard_widget' ), 'dashboard', 'side', 'high' );
	}

	/**
	 * Render Dashboard widget.
	 */
	public function dashboard_widget() {

		?>
		<p><?php esc_html_e( 'Ensure your site is happy, healthy and up to date. The Maintainn Dashboard widget is your quick launch pad to the info you need to do just that. Use the buttons below to view your site information, scan your site, check out the tools, or get support.', 'maintainn-tools' ); ?></p>
		<p>
		<?php
		foreach ( $this->tabs as $tab => $name ) {
			printf( '<a href="%1$s" class="button">%2$s</a> ',
				add_query_arg( 'tab', $tab, $this->page_url ),
				$name
			);
		}
		do_action( 'maintainn_dashboard_widget_links' ); ?>
		</p>
		<?php
	}

	/**
	 * Render Maintainn Support page.
	 */
	public function render_page() {
		$current_user = wp_get_current_user();
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'about';
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Maintainn Tools' . ' ' . MAINTAINN_TOOLS_VERSION, 'maintainn-tools' ); ?></h1>

				<?php $this->option_tabs(); ?>

				<?php
				switch ( $tab ) {
					case 'about':
						$this->tab_about();
						break;
					case 'site-info':
						$this->tab_site_info();
						break;
					case 'site-scanner':
						$this->scan_my_site();
						break;
					case 'tools':
						$this->tab_tools();
						break;
					case 'support':
						$this->tab_support();
						break;
				}

				do_action( 'maintainn_tab_switch', $tab );
				?>
		</div>
		<?php
	}

	/**
	 * Render options.
	 *
	 * @since 2.0.0
	 */
	public function option_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'about';
		?>
		<style type="text/css">
		.nav-tab-active, .nav-tab-active:hover, .nav-tab-active:focus {background-color:#242c35;color:#fff;}
		.nav-tab:hover{background-color:#fff;color:#242c35;}
		</style>
		<?php
		echo '<h2 class="nav-tab-wrapper">';

		foreach ( $this->tabs as $tab => $name ) {
			$active = ( $current_tab === $tab ) ? 'nav-tab-active' : '';
			printf( '<a class="nav-tab %s" href="%s">%s</a>',
				esc_attr( $active ),
				add_query_arg( 'tab', $tab, $this->page_url ),
				esc_html( $name )
			);
		}

		echo '</h2>';
	}

	/**
	 * Render about tab.
	 *
	 * @since 2.0.0
	 */
	public function tab_about() {
		?>
		<div class="wrap maintainn-audit-wrap">
			<?php echo $this->maintainn_about_page(); ?>
		</div>
		<?php
	}

	/**
	 * Render tools tab.
	 *
	 * @since 2.0.0
	 */
	public function tab_tools() {
		$this->plugin->extensions->render_tab();
	}

	/**
	 * Render support tab.
	 *
	 * @since 2.0.0
	 */
	public function tab_support() {
		?>
		<div class="wrap maintainn-audit-wrap">
			<?php echo $this->maintainn_support(); ?>
		</div>
		<?php
	}

	/**
	 * Render site info tab.
	 *
	 * @since 2.0.0
	 */
	public function tab_site_info() {
		?>

		<div class="wrap maintainn-audit-wrap">
			<?php echo $this->maintainn_site_info(); ?>
		</div>

		<?php
	}

	/**
	 * Render our Maintainn About Page.
	 *
	 * @since 2.0.0
	 */
	private function maintainn_about_page() {
		// Bail early if not the right permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Include browser class.
		if ( ! class_exists( 'Browser' ) ) {
			$this->plugin->include_vendor( 'browser' );
		}

		$this->plugin->include_view( 'about-page' );
	}

	/**
	 * Render our Maintainn site info.
	 *
	 * @since 2.0.0
	 */
	private function maintainn_site_info() {
		// Bail early if not the right permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Include browser class.
		if ( ! class_exists( 'Browser' ) ) {
			$this->plugin->include_vendor( 'browser' );
		}

		$this->plugin->include_view( 'site-information' );
	}

	/**
	 * Render our Maintainn support info.
	 *
	 * @since 2.0.0
	 */
	private function maintainn_support() {
		// Bail early if not the right permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Include browser class.
		if ( ! class_exists( 'Browser' ) ) {
			$this->plugin->include_vendor( 'browser' );
		}

		$this->plugin->include_view( 'support-info' );
	}

	/**
	 * Render our system status.
	 *
	 * @since 2.0.0
	 */
	public function system_status() {
		// Bail early if not the right permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Include browser class.
		if ( ! class_exists( 'Browser' ) ) {
			$this->plugin->include_vendor( 'browser' );
		}

		$this->plugin->include_view( 'system-info' );
	}

	/**
	 * Scan my site tab
	 *
	 * @since 0.1.0
	 */
	public function scan_my_site() {
		$this->enqueue_scripts();
		?>
		<p><?php esc_html_e( 'The "Site Scanner" tool compares the WordPress Core files on your website to the original files on wordpress.org and ensures that they have not been altered in any way.', 'maintainn-tools' ); ?></p>
		<a class="button button-primary" id="maintainn-scanner-check-core" href="javascript:void(0);"><?php esc_html_e( 'Check Core Files', 'maintainn-tools' ); ?></a>
		<div id="maintainn-scanner-log"></div>
		<?php
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function enqueue_scripts() {
		// Use minified js if script debug isn't on.
		$suffix = $this->plugin->get_script_suffix();

		wp_enqueue_script( 'maintainn-scanner-admin', $this->plugin->url( "assets/js/admin{$suffix}.js" ), array( 'jquery' ), $this->plugin->version() );

		$l10n = array(
			'checksum_nonce' => wp_create_nonce( 'verify_core_checksums' ),
			'checksums_error' => __( 'There was an error running checksums test.', 'maintainn-tools' ),
			'verifying' => __( 'Verifying', 'maintainn-tools' ),
		);

		wp_localize_script( 'maintainn-scanner-admin', 'maintainn_scanner_admin_config', $l10n );
	}
}
