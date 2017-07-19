<?php
/**
 * Maintain Tools Maintainn_Notes
 *
 * @since   0.1.0
 * @package Maintain Tools
 */

/**
 * Maintain Tools Maintainn Notes post type class.
 *
 * @see   https://github.com/WebDevStudios/CPT_Core
 * @since 0.1.0
 */
class Maintainn_Notes extends CPT_Core {
	/**
	 * Parent plugin class
	 *
	 * @var Maintainn_Tools
	 * @since  0.1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 * Register Custom Post Types. See documentation in CPT_Core, and in wp-includes/post.php
	 *
	 * @since  0.1.0
	 *
	 * @param  Maintainn_Tools $plugin Main plugin object.
	 *
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		// Register this cpt
		// First parameter should be an array with Singular, Plural, and Registered name.
		parent::__construct(
			array( __( 'Maintainn Note', 'maintainn-tools' ), __( 'Maintainn Notes', 'maintainn-tools' ), 'maintainn-notes' ),
			array(
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
				'public'       => 'false',
				'show_in_menu' => 'false',
			)
		);
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function hooks() {

	}

}
