<?php
/**
 * Helpers for use in Maintainn Tools plugin.
 *
 * @version 2.0
 * @package Maintainn Tools
 */

/**
 * Class for helper functions.
 */
class Maintainn_Tools_Helpers {

	/**
	 * Get columns for the maintainn about page.
	 *
	 * @since  2.0
	 *
	 * @return array Columns for the about page.
	 */
	public function get_about_page_columns() {
		$columns = array(
			array(
				'img' => esc_url( plugins_url( 'assets/img/Info.png', dirname(__FILE__) ) ),
				'title' => esc_html__( 'Site Info', 'maintainn-tools' ),
				'content' => esc_html__( 'A top level view of your entire WordPress install. View registered post types &amp; post statuses, permalink structure, multisite network actived plugins, active &amp; inactive plugins, must-use plugins (MU), site &amp; server details, browser details, session details, and email yourself a copy.', 'maintainn-tools' ),
			),
			array(
				'img' => esc_url( plugins_url( 'assets/img/Scanner.png', dirname(__FILE__) ) ),
				'title' => esc_html__( 'Site Scanner', 'maintainn-tools' ),
				'content' => esc_html__( 'Compare the WordPress Core files on your website hosting server to the current WordPress files available from the WordPress.org repository, to ensure they have not been altered in any way.', 'maintainn-tools' ),
			),
			array(
				'img' => esc_url( plugins_url( 'assets/img/Tools.png', dirname(__FILE__) ) ),
				'title' => esc_html__( 'Tools', 'maintainn-tools' ),
				'content' => esc_html__( 'This feature has three main functions.', 'maintainn-tools' ) .
				'<ol>
					<li>' . esc_html__( 'Turn on/off automatic updates for specific plugins.', 'maintainn-tools' ) . '</li>
					<li>' . esc_html__( 'Lock a specific plugin.', 'maintainn-tools' ) . '</li>
					<li>' . esc_html__( 'Plugin admin notes.', 'maintainn-tools' ) . '</li>
				</ol>',
			),
			array(
				'img' => esc_url( plugins_url( 'assets/img/Maintainn.png', dirname(__FILE__) ) ),
				'title' => esc_html__( 'Maintainn Support', 'maintainn-tools' ),
				'content' => esc_html__( 'Use this simple set of tools to scan your WordPress website. Check for updates, active and inactive plugins, site and server details, and much more.', 'maintainn-tools' ),
			),
		);

		/**
		 * Filter column data that shows on the about page.
		 *
		 * Add/remove columns from the about page.
		 *
		 * @since 2.0
		 *
		 * @param         array $columns      The data for the insert/update.
		 */
		$columns = apply_filters( 'maintainn_tools_about_page_columns', $columns );

		// Holds sorted columns.
		$sorted_columns = array();

		// Keep track of counts and rows.
		$count = 0;
		$row = 0;

		// Loop through columns and sort two to a row.
		foreach ( (array) $columns as $column ) {
			// Increment count.
			++$count;

			// Increment row if necessary.
			if ( 2 < $count ) {
				++$row;

				// Reset count.
				$count = 0;
			}

			// Add column to a row.
			$sorted_columns[ $row ][] = $column;
		}


		return $sorted_columns;
	}

	/**
	 * Get columns for the maintainn support page first column.
	 *
	 * @since  2.0
	 *
	 * @return array Columns for the support page first column.
	 */
	public function get_support_page_first_columns() {
		$columns = array(
			array(
				'title' => esc_html__( 'WordPress Updates', 'maintainn-tools' ),
				'content' => esc_html__( 'Update what?! Let Maintainn handle it for you, and never worry about those pesky site updates and backups ever again.', 'maintainn-tools' ),
			),
			array(
				'title' => esc_html__( 'Website Staging', 'maintainn-tools' ),
				'content' => esc_html__( 'Maintainn can help you set up a staging site to test, revise, or update without risking the functionality of your live site.', 'maintainn-tools' ),
			),
			array(
				'title' => esc_html__( 'Professional Help', 'maintainn-tools' ),
				'content' => esc_html__( 'The Maintainn team is here to assist you with any and all WordPress guidance, advice, and answers you may need.', 'maintainn-tools' ),
			),
			array(
				'title' => esc_html__( 'Data Migrations', 'maintainn-tools' ),
				'content' => esc_html__( 'Your data can be migrated to one of Maintainns trusted hosts without you lifting a finger!', 'maintainn-tools' ),
			),
			array(
				'title' => esc_html__(  'Custom Development', 'maintainn-tools' ),
				'content' => esc_html__( 'All of our plans provide access to the Maintainn teams collective half century of expertise and experience.' ),
			),
			array(
				'title' => esc_html__( 'Security Monitoring', 'maintainn-tools' ),
				'content' => esc_html__( 'Our partnership with Sucuri guarantees that your WordPress site will be safe and secure with 24/7 monitoring.' ),
			),
		);

		/**
		 * Filter column data that shows on the support page first column.
		 *
		 * Add/remove columns from the support page first column.
		 *
		 * @since 2.0
		 *
		 * @param         array $columns      The data for the insert/update.
		 */
		$columns = apply_filters( 'maintainn_tools_support_page_first_columns', $columns );

		// Holds sorted columns.
		$sorted_columns = array();

		// Keep track of counts and rows.
		$count = 0;
		$row = 0;

		// Loop through columns and sort two to a row.
		foreach ( (array) $columns as $column ) {
			// Increment count.
			++$count;

			// Increment row if necessary.
			if ( 3 < $count ) {
				++$row;

				// Reset count.
				$count = 0;
			}

			// Add column to a row.
			$sorted_columns[ $row ][] = $column;
		}


		return $sorted_columns;
	}
}