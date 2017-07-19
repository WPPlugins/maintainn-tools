<?php
/**
 * Maintainn Tools View
 *
 * @since   0.1.0
 * @package WDS Plugin Police
 */

/**
 * Maintainn Tools View.
 *
 * @since 0.1.0
 */
class Maintainn_Tools_View {
	/**
	 * Parent plugin class
	 *
	 * @var   WDS_Plugin_Police
	 * @since 0.1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  0.1.0
	 *
	 * @param  WDS_Plugin_Police $plugin Main plugin object.
	 *
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function hooks() {
		// If in multi-site add only to network screen.
		if ( is_multisite() ) {
			add_filter( 'manage_plugins-network_columns', array( $this, 'add_column' ) );
		} else {
			add_filter( 'manage_plugins_columns', array( $this, 'add_column' ) );
		}
		add_action( 'manage_plugins_custom_column', array( $this, 'render_column' ), 10, 3 );
		add_action( 'wp_ajax_maintainn_tools_dynamic_form', array( $this, 'display_form' ) );
		add_action( 'wp_ajax_maintainn_tools_receive_comment', array( $this, 'receive_comment' ) );
		add_action( 'wp_ajax_maintainn_tools_toggle_updates', array( $this, 'toggle_updates' ) );
		add_action( 'wp_ajax_maintainn_tools_lock_updates', array( $this, 'toggle_lock' ) );

		// Remove the plugin from the list of updates.
		add_filter( 'site_transient_update_plugins', array( $this, 'remove_updates_globally' ) );
	}

	/**
	 * Add a column to the plugins view.
	 *
	 * @since 0.1.0
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function add_column( $columns ) {
		$columns['plugin_admin_notes'] = esc_html__( 'Notes', 'maintainn-tools' );

		return $columns;
	}

	/**
	 * Render the data in each TD.
	 *
	 * @since 0.1.0
	 *
	 * @param $column_name
	 * @param $plugin_file
	 * @param $plugin_data
	 */
	public function render_column( $column_name, $plugin_file, $plugin_data ) {
		if ( 'plugin_admin_notes' == $column_name ) {

			if ( ! isset( $plugin_data['slug'] ) ) {
				$slug = sanitize_title( $plugin_data['Name'] );
			} else {
				$slug = $plugin_data['slug'];
			}

			?>
			<div class="plugin-note" id="<?php echo esc_attr( $slug ); ?>" style="width:200px;">
				<?php
				$this->plugin->dynamic_form->update( $slug );
				$this->plugin->dynamic_form->lock( $slug, $plugin_file );
				$this->plugin->dynamic_form->get_form( $slug );
				$this->plugin->dynamic_form->get_comments( $slug );
				?>
			</div>
			<?php
		}
	}

	/**
	 * Display the form.
	 *
	 * @since 0.1.0
	 */
	public function display_form() {
		$this->plugin->dynamic_form->dynamic_form();
	}

	/**
	 * Handle an incoming comment.
	 *
	 * @since 0.1.0
	 */
	public function receive_comment() {
		$this->plugin->dynamic_form->save_comment();
		$this->display_form();
	}

	/**
	 * Toggle the update status.
	 *
	 * @since 0.1.0
	 */
	public function toggle_updates() {
		$this->plugin->dynamic_form->toggle_updates();
		$this->display_form();
	}

	/**
	 * Toggle the lock status.
	 *
	 * @since 0.1.0
	 */
	public function toggle_lock() {
		$this->plugin->dynamic_form->toggle_lock();
		$this->display_form();
	}


	/**
	 * Remove the update notification globally.
	 *
	 * @since 0.1.0
	 */
	public function remove_updates_globally( $value ) {

		$locked_updates = get_option( 'maintainn_lock_updates', array() );

		if ( isset( $value ) && is_object( $value ) ) {
			// Loop through the locked plugins.
			foreach ( $locked_updates as $file => $name ) {
				// Unset the update data.
				unset( $value->response[ $file ] );
			}
		}

		return $value;
	}

	/**
	 * Remove the store data for a plugin when it's deleted.
	 *
	 * @since 0.1.0
	 *
	 * @param $slug
	 */
	public function remove_plugin( $slug ) {
		$args  = array(
			'post_type'      => 'maintainn-notes',
			'meta_query'     => array(
				array(
					'key'     => 'mn_slug',
					'value'   => $slug,
					'compare' => '=',
				),
			),
			'fields'         => 'ids',
			'posts_per_page' => - 1,
		);
		$posts = new WP_Query( $args );

		foreach ( $posts->posts as $post ) {
			$result = wp_delete_post( $post, true );
		}

		$update_plugins = get_option( 'maintainn_plugin_notes' );
		foreach ( $update_plugins as $index => &$plugin_name ) {
			if ( $slug === $plugin_name ) {
				unset( $update_plugins[ $index ] );
			}
		}
		update_option( 'maintainn_plugin_notes', $update_plugins );

		$lock_plugins = get_option( 'maintainn_lock_updates' );
		foreach ( $lock_plugins as $index => &$plugin_name ) {
			if ( $slug === $plugin_name ) {
				unset( $lock_plugins[ $index ] );
			}
		}
		update_option( 'maintainn_lock_updates', $lock_plugins );
	}

}
