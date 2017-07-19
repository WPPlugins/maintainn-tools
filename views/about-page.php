<?php
/**
 * About page for Maintainn tools plugin.
 *
 * @version 2.0
 * @package Maintainn Tools
 */

// Get columns for this page.
$row_columns = maintainn_customer()->helpers->get_about_page_columns();
?>

<div class="about-wrap">
	<div class="feature-section one-col">
		<h2>
			<?php esc_html_e( 'Maintainn Tools', 'maintainn-tools' ); ?>
		</h2>
		<p class="lead-description"><?php esc_html_e( 'Use this simple set of tools to scan your WordPress website. Check for updates, active and inactive plugins, site and server details, and much more.', 'maintainn-tools' ); ?></p>
	</div>
	<?php if ( ! empty( $row_columns ) ) : ?>
		<?php foreach ( (array) $row_columns as $row => $columns ) : ?>
			<!-- Start Two Columns -->
			<div class="feature-section two-col">
				<?php foreach ( (array) $columns as $column ) : ?>
					<!-- Single Column -->
					<div class="col">
						<h3>
							<?php echo isset( $column['title'] ) ? esc_html( $column['title'] ) : ''; ?>
						</h3>
						<p>
							<img src="<?php echo isset( $column['img'] ) ? esc_html( $column['img'] ) : ''; ?>">
						</p>
						<p>
							<?php echo isset( $column['content'] ) ? wp_kses_post( $column['content'] ) : ''; ?>
						</p>
					</div><!--// End .col -->
				<?php endforeach; ?>
			</div><!--// End .two-col -->
		<?php endforeach; ?>
	<?php endif; ?>
</div><!--//End about wrap. -->
