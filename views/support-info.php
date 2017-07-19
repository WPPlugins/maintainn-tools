<?php
/**
 * Support into page for Maintainn tools plugin.
 *
 * @version 2.0
 * @package Maintainn Tools
 */
$row_columns = maintainn_customer()->helpers->get_support_page_first_columns();
?>
<style type="text/css">
	.support-featured{background-color:#232C36;color:#fff; font-weight: 100;border:3px}
	.support-featured h2{color:#fff; font-weight: 100;}
</style>
<div class="about-wrap">
	<!-- Start one columns -->
	<div class="support-featured feature-section one-col">
		<h2><?php esc_html_e( 'Maintainn Support', 'maintainn-tools' ); ?></h2>
		<p class="lead-description">
			<?php esc_html__( 'We Maintainn Your WordPress Investment. Never worry about your WordPress site again by letting us do it all--from development, to security, to site updates, and more!', 'maintainn-tools' );
			?>
		</p>
		<p class="lead-description">
			<a class="button-primary" href="https://maintainn.com/pricing/" target="_blank">
				<?php esc_html_e( 'Get Maintainn Support', 'maintainn-tools' ); ?>
			</a>
		</p>
	</div>
	<!-- Stop one columns -->
	<hr>
	<div class="changelog">
		<h2><?php esc_html_e( 'How Maintainn Support Can Help', 'maintainn-tools' ); ?></h2>
		<div class="under-the-hood three-col">
	<?php if ( ! empty( $row_columns ) ) : ?>
		<?php foreach ( (array) $row_columns as $row => $columns ) : ?>
			<!-- Start Three Columns -->
			<div class="feature-section three-col">
				<?php foreach ( (array) $columns as $column ) : ?>
					<!-- Single Column -->
					<div class="col">
						<h3>
							<?php echo isset( $column['title'] ) ? esc_html( $column['title'] ) : ''; ?>
						</h3>
						<p>
							<?php echo isset( $column['content'] ) ? wp_kses_post( $column['content'] ) : ''; ?>
						</p>
					</div><!--// End .col -->
				<?php endforeach; ?>
			</div><!--// End .three-col -->
		<?php endforeach; ?>
	<?php endif; ?>
	<!-- Start Changelog -->
		</div>
	</div>
	<!-- // End changelog. -->
	<hr>
	<!-- Start Changelog -->
	<div class="changelog">
		<h2>
			<?php esc_html_e( 'Helpful WordPress Resources', 'maintainn-tools' ); ?>
		</h2>
		<div class="under-the-hood three-col">
			<div class="col">
				<h3><?php esc_html_e( 'WordPress Codex', 'maintainn-tools' ); ?></h3>
				<p><?php esc_html_e( 'The online manual for WordPress and a living repository for WordPress information and documentation.', 'maintainn-tools' ); ?></p>
				<p><a class="button-secondary" href="https://codex.wordpress.org/" target="_blank"><?php esc_html_e( 'WordPress Codex', 'maintainn-tools' ); ?></a>
			</div>
			<div class="col">
				<h3><?php esc_html_e( 'Developer Resources', 'maintainn-tools' ); ?></h3>
				<p><?php esc_html_e( 'Learn to build WordPress themes, plugins, and the rest api. Everything you need to know about developing WordPress. ', 'maintainn-tools' ); ?></p>
				<p><a class="button-secondary" href="https://developer.wordpress.org/" target="_blank"><?php esc_html_e( 'Developer Resources', 'maintainn-tools' ); ?></a>
			</div>
			<div class="col">
				<h3><?php esc_html_e( 'WordPress Blog', 'maintainn-tools' ); ?></h3>
				<p><?php esc_html_e( 'Get the latest news, updates, and security release information directly from the source. Stay up to date with WordPress.', 'maintainn-tools' ); ?></p>
				<p><a class="button-secondary" href="https://wordpress.org/news/" target="_blank"><?php esc_html_e( 'WordPress Web Hosting', 'maintainn-tools' ); ?></a>
			</div>
		</div>
		<div class="under-the-hood three-col">
			<div class="col">
				<h3><?php esc_html_e( 'WordPress Hosting', 'maintainn-tools' ); ?></h3>
				<p><?php esc_html_e( 'Just like flowers need the right environment to grow, WordPress works best when it’s in a rich hosting environment.', 'maintainn-tools' ); ?></p>
				<p><a class="button-secondary" href="https://wordpress.org/hosting/" target="_blank"><?php esc_html_e( 'WordPress Web Hosting', 'maintainn-tools' ); ?></a>
			</div>
			<div class="col">
				<h3><?php esc_html_e( 'WordPress TV', 'maintainn-tools' ); ?></h3>
				<p><?php esc_html_e( 'Coulnt make it to your favorite WordCamp? Dont worry. You can watch the presentations after they have been uploaded.', 'maintainn-tools' ); ?></p>
				<p><a class="button-secondary" href="https://wordpress.tv/" target="_blank"><?php esc_html_e( 'WordPress Television', 'maintainn-tools' ); ?></a>
			</div>
			<div class="col">
				<h3><?php esc_html_e( 'WordCamp Central', 'maintainn-tools' ); ?></h3>
				<p><?php esc_html_e( 'Learn from others in your local community. WordCamp is a weekend long conference dedicated to teaching WordPress .' ); ?></p>
				<p><a class="button-secondary" href="https://central.wordcamp.org/" target="_blank"><?php esc_html_e( 'WordCamp Central', 'maintainn-tools' ); ?></a>
			</div>
		</div>
	</div>
	<!--  Stop Changelog -->
</div>