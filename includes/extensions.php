<?php
/**
 * Extensions and add-ons classes and functions.
 *
 * @since  2.0.0 Initially added
 */

class Maintainn_Tools_Extensions {
	/**
	 * Maintainn_Tools obj
	 *
	 * @var null
	 */
	protected $plugin = null;

	/**
	 * List of addons.
	 *
	 * @var array
	 */
	protected $addons = array();

	/**
	 * List of columns.
	 *
	 * @var array
	 */
	protected $cols = array();

	/**
	 * List of active addons.
	 *
	 * @var array
	 */
	protected $active_addons = array();

	/**
	 * Maintainn_Tools_Extensions constructor method.
	 *
	 * @since 2.0.0
	 *
	 * @param obj $plugin Maintainn_Tools instance.
	 * @return  void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		// Set our list of table headers.
		$this->cols = array(
			__( 'Active', 'maintainn-tools' ),
			__( 'Name', 'maintainn-tools' ),
			__( 'Description', 'maintainn-tools' ),
		);

		// Our list of addons available.
		$this->register_addon( 'notes', array(
			'name'        => __( 'Notes', 'maintainn-tools' ),
			'description' => __( 'Adds an extra column to the plugins so you can add notes', 'maintainn-tools' ),
			)
		);

		// Get our list of active addons.
		$this->active_addons = get_option( 'maintainn_active_addons' );
	}

	/**
	 * Our place for all the hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return  void
	 */
	public function hooks() {
		// enqueue our script.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10, 1 );

		// add action for ajax to handle the activation/deactivation of addons.
		add_action( 'wp_ajax_mt_activate_addon', array( $this, 'addon_activation' ) );
	}

	/**
	 * Enqueues all the needed files.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $page The current page.
	 * @return  void
	 */
	public function enqueue_scripts( $page ) {
		// Make sure we only load it on the tools tab.
		if ( empty( $_GET['tab'] ) || ( 'tools' !== $_GET['tab'] ) ) {
			return;
		}

		// Use minified js if script debug isn't on.
		$suffix = $this->plugin->get_script_suffix();

		wp_enqueue_script( 'mt-addons', $this->plugin->url . "assets/js/mt-addons{$suffix}.js", array( 'jquery' ), false, false );
	}

	/**
	 * Hooked to the AJAX call and will either actiavte or
	 * deactivatte the addon.
	 *
	 * @since  2.0.0
	 *
	 * @return void
	 */
	public function addon_activation() {
		// our logic.
		$this->change_status( esc_attr( $_POST['addon_id'] ) );
	}

	/**
	 * Change the status of an addon.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $id Slug of addon to check for.
	 * @return  void
	 */
	public function change_status( $id ) {
		// make sure the addon exists.
		$addons = get_option( 'maintainn_active_addons' );

		if ( isset( $addons[ $id ] ) ) {
			// Swap the values.
			$addons[ $id ]['active'] = (bool) $addons[ $id ]['active'] ? false : true;

			// Update the option.
			update_option( 'maintainn_active_addons', $addons, false );

			// send success response.
			wp_send_json_success();
		} else {
			// Send failure response.
			wp_send_json_error();
		}

		die();
	}

	/**
	 * Renders the tab content on the page.
	 *
	 * @since 2.0.0
	 *
	 * @return  void
	 */
	public function render_tab() {
		?>
<form id="extension-list" method="post">
	<table class="wp-list-table striped widefat">
		<thead>
		<tr>
			<?php $this->print_column_headers(); ?>
		</tr>
		</thead>

		<tbody id="the-list">
			<?php $this->display_list(); ?>
		</tbody>

		<tfoot>
		<tr>
			<?php $this->print_column_headers( false ); ?>
		</tr>
		</tfoot>
	</table>
</form>
		<?php
	}

	/**
	 * Prints out the table header columns.
	 *
	 * @since  2.0.0
	 *
	 * @param  boolean $id Whether or not to print the ID attribute.
	 * @return void
	 */
	private function print_column_headers( $id = true ) {
		foreach ( $this->cols as $col ) {
			$attr = $id ? ' id="' . strtolower( $col ) . '"' : '';
			printf( '<th%2$s>%1$s</th>',
				esc_html( $col ),
				esc_attr( $attr )
			);
		}
	}

	/**
	 * Displays the list of available addons, their description,
	 * and whether it is active or not.
	 *
	 * @since  2.0.0
	 *
	 * @return  void
	 */
	public function display_list() {
		// create a container for the columns.
		foreach ( $this->active_addons as $addon => $data ) {
			$active = $this->is_active( $addon );
			$class = $active ? 'active': 'inactive';
			$checked = $active ? 'checked="checked"' : '';
			$cols = sprintf( '<th class="check-column"><input id="%s-addon" type="checkbox" class="cb" %s></th>', $addon, $checked );
			$cols .= sprintf( '<td>%s</td><td>%s</td>', $data['name'], $data['description'] );
			$row = sprintf( '<tr class="%s">%s</tr>', $class, $cols );
			echo $row; // XSS okay.
		}
	}

	/**
	 * Returns whether the addon is active or not.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $addon The slug of the addon being checked.
	 * @return boolean        True if active, false if not.
	 */
	public function is_active( $addon ) {
		return $this->get_addon_status( $addon );
	}

	/**
	 * Returns the status of the addon.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $addon The id of the addon.
	 * @return bool        Whether or not the addon is active.
	 */
	protected function get_addon_status( $addon ) {
		// Get active.
		$status = wp_list_pluck( $this->active_addons, 'active' );
		return $status[ $addon ];
	}

	/**
	 * Registers additional addon. One at a time.
	 *
	 * @since  2.0.0
	 *
	 * @param  string $id   The slug, or id, of the addon being
	 *                      added.
	 * @param  array  $args Associative array for the additional
	 *                     addon. This is what information displays
	 *                     on the screen. name, description.
	 * @return bool       True on success.
	 */
	static public function register_addon( $id, $args ) {
		// the addon.
		$addon = array(
			$id => wp_parse_args( $args, array(
				'name'        => '',
				'description' => '',
				'active'      => true,
			) ),
		);

		// get all the addons.
		$addons = get_option( 'maintainn_active_addons' );
		$addons = wp_parse_args( $addons, $addon );
		$addons = array_unique( $addons, SORT_REGULAR );

		// update the addons.
		return update_option( 'maintainn_active_addons', $addons, false );
	}

	/**
	 * Removes a registered addon based on ID.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $id The name of the addon to be removed.
	 * @return bool       True if successfully removed.
	 */
	static public function remove_addon( $id ) {
		$addons = get_option( 'maintainn_active_addons' );
		unset( $addons[ $id ] );
		return update_option( 'maintainn_active_addons', $addons, false );
	}
}

/**
 * Registers an additional addon.
 *
 * @since 2.0.0
 *
 * @param  string $id   The ID, or slug of the addon.
 * @param  array  $args  Associative array with name, description as keys values.
 * @return bool       True if successful.
 */
function maintainn_register_addon( $id, $args ) {
	return Maintainn_Tools_Extensions::register_addon( $id, $args );
}

/**
 * Removes a registered addon.
 *
 * @since 2.0.0
 *
 * @param  string $id The ID, or slug of the addon to be removed.
 * @return bool     True if successful.
 */
function maintainn_remove_addon( $id ) {
	return Maintainn_Tools_Extensions::remove_addon( $id );
}
