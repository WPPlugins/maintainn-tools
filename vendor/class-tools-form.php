<?php
/**
 * Maintain_Tools_Form
 *
 * @since   0.1.0
 * @package WDS Plugin Police
 */

/**
 * Maintain_Tools_Form.
 *
 * @since 0.1.0
 */
class Maintain_Tools_Form {
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
		add_filter( 'auto_update_plugin', array( $this, 'auto_update_stored_plugins' ), 10, 2 );
	}

	/**
	 * Create the dynamic stuff to be returned from the admin-ajax request.
	 *
	 * @since 0.1.0
	 */
	public function dynamic_form() {
		$this->update( $_POST['slug'] );
		$this->lock( $_POST['slug'], $_POST['plugin'] );
		$this->get_form( $_POST['slug'] );
		$this->get_comments( $_POST['slug'] );
		die();
	}

	/**
	 * Echo the lock icon.
	 *
	 * @since 0.1.0
	 */
	public function lock( $slug, $file ) {
		// Set the string according to lock_status.
		$string = $this->lock_status( $file ) ? __( 'Unlock the %s plugin', 'admin-plugin-notes' ) : __( 'Lock the %s plugin', 'admin-plugin-notes' );
		// Set the class accordingly to lock_status.
		$class = $this->lock_status( $file ) ? 'fa fa-lock fa-lg green' : 'fa fa-lock fa-lg grey';

		// Finally print out our stuff.
		printf( '<a href="javascript:void(0)" class="%1$s" title="%2$s" id="maintainn_lock_update_%3$s" aria-label="%2$s"></a>',
			esc_attr( $class ),
			esc_attr( sprintf( $string, $slug ) ),
			esc_attr( $slug )
		);
	}

	/**
	 * Returns lock status of particular slug.
	 *
	 * @param $slug string plugin slug name.
	 *
	 * @return bool
	 */
	public function lock_status( $file ) {
		$lock_plugins = get_option( 'maintainn_lock_updates', array() );
		if ( array_key_exists( $file, $lock_plugins ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Update the lock stats option.
	 *
	 * @since 0.1.0
	 */
	public function toggle_lock() {
		$lock_plugins = get_option( 'maintainn_lock_updates', array() );

		// if it already exists, we unset it.
		if ( array_key_exists( $_POST['plugin'], $lock_plugins ) ) {
			unset( $lock_plugins[ $_POST['plugin'] ] );
			update_option( 'maintainn_lock_updates', $lock_plugins );
		} else {
			// If this plugin isn't in the array add it.
			$lock_plugins[ $_POST['plugin'] ] = $_POST['slug'];
			update_option( 'maintainn_lock_updates', $lock_plugins );
		}
	}

	/**
	 * Set status for auto-updating.
	 *
	 * @since 0.1.0
	 *
	 * @param $slug
	 */
	public function update( $slug ) {
		// Set the string according to update_status.
		$string = $this->update_status( $slug ) ? __( 'Turn off auto updates for the %s plugin', 'admin-plugin-notes' ) : __( 'Turn on auto updates for the %s plugin', 'admin-plugin-notes' );
		// Set the class accordingly to update_status.
		$class = $this->update_status( $slug ) ? 'fa fa-refresh fa-lg green' : 'fa fa-refresh fa-lg grey';

		// Finally print out the thingy-ma-bopper.
		printf( '<a href="javascript:void(0);" class="%1$s" title="%2$s" id="maintainn_auto_update_%3$s" aria-label="%2$s"></a>',
			esc_attr( $class ),
			esc_attr( sprintf( $string, $slug ) ),
			esc_attr( $slug )
		);
	}

	/**
	 * Return the update status of the plugin passed in.
	 *
	 * @since 0.1.0
	 *
	 * @param $slug
	 *
	 * @return bool
	 */
	private function update_status( $slug ) {
		$update_plugins = get_option( 'maintainn_plugin_notes' );
		if ( is_array( $update_plugins ) && in_array( $slug, $update_plugins ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $slug
	 *
	 * Create the form for the slug.
	 */
	public function get_form( $slug ) {
		?><br/>
	<a href="#" id="maintainn_note_link_<?php echo esc_attr( $slug ); ?>">
		<?php esc_html_e( 'Add a Note', 'maintainn-tools' ); ?>
	</a>
	<div style="display: none;" id="maintainn_note_div_<?php echo esc_attr( $slug ); ?>">
		<input type="hidden" name="slug" value="<?php echo esc_attr( $slug ); ?>">
		<input type="text" class="plugin_notes_<?php echo esc_attr( $slug ); ?> plugin-admin-notes-note" name="note" id="maintainn_note_<?php echo esc_attr( $slug ); ?>">
		<input type="button" value="<?php esc_attr_e( 'Add a Note', 'maintain-tools' ); ?>" id="maintainn_note_submit_<?php echo esc_attr( $slug ); ?>">
	</div>
	<?php }

	/**
	 * Get the existing comments.
	 *
	 * @since 0.1.0
	 *
	 * @param $slug
	 */
	public function get_comments( $slug ) {
		$args    = array(
			'post_type'  => 'maintainn-notes',
			'meta_query' => array(
				array(
					'key'     => 'mn_slug',
					'value'   => $slug,
					'compare' => 'IN',
				),
			),
		);
		$results = new WP_Query( $args );
		if ( $results->have_posts() ) {
			echo '<ul>';
			while ( $results->have_posts() ) {
				$results->the_post(); ?>
				<li style="font-size:smaller;">
				<?php // Outputs our note, by whom, and the formatted date.
				printf( __( '&#8226; %s by <strong>%s</strong> on %s', 'maintainn-tools' ),
					get_the_content(),
					get_the_author(),
					get_the_time( 'F j, Y g:i a' )
				); ?>
				</li>
			<?php } // End while().
			echo '</ul>';
		}
	}

	/**
	 * Save incoming comment.
	 *
	 * @since 0.1.0
	 */
	public function save_comment() {
		$args = array(
			'post_content' => $_POST['note'],
			'post_status'  => 'publish',
			'post_type'    => 'maintainn-notes',
		);
		$id   = wp_insert_post( $args );
		update_post_meta( $id, 'mn_slug', $_POST['slug'] );
	}

	/**
	 * Toggle the udpate status option.
	 *
	 * @since 0.1.0
	 */
	public function toggle_updates() {
		$update_plugins = get_option( 'maintainn_plugin_notes' );

		// If this plugin is in the list, remove it.
		if ( in_array( $_POST['slug'], $update_plugins ) ) {
			$new_update_plugins = array();

			foreach ( $update_plugins as $plugin ) {
				if ( $_POST['slug'] != $plugin ) {
					$new_update_plugins[] = $plugin;
				}
			}

			if ( isset( $new_update_plugins ) ) {
				update_option( 'maintainn_plugin_notes', $new_update_plugins );
			}

			// If this plugin isn't in the array add it.
		} else {
			$update_plugins[] = $_POST['slug'];
			update_option( 'maintainn_plugin_notes', $update_plugins );
		}
	}

	/**
	 * Sets the auto-update for the plugins that are in the updates array.
	 *
	 * @since 0.1.0
	 */
	public function auto_update_stored_plugins( $update, $item ) {
		$plugins = get_option( 'maintainn_plugin_notes' );
		if ( in_array( $item->slug, $plugins ) ) {
			return true; // Always update plugins in this array
		} else {
			return $update; // Else, use the normal API response to decide whether to update or not
		}
	}
}
