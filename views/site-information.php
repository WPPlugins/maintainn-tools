<?php
/**
 * @todo  - move all these vars to methods before view is included
 */
global $wpdb;
$browser = new Browser();

$system_info = new Maintainn_Tools_System_Info( $browser, $wpdb );
$info = $system_info->get_system_info();
?>

<style type="text/css">
	.maintainn-row{font-size:14px;height:100%;padding-top:07px;padding-bottom:07px;vertical-align:middle;}
	.row-title{width: 85%;display: inline-block;vertical-align: middle;text-align: left;}
	.version {width: 15%;display: inline-block;text-align: left;vertical-align: middle;}
	.title{background-color:#232C36;color:#fff; font-weight: 100;}
	.success{color:green; padding:05px;}
	.maintainn-row{padding: 8px;}
	.maintainn-row:nth-child(odd){background-color:#eaeaea;}
	.list-inside{border: 1px solid #e5e5e5;}
	.list-inside-sidebar{border: 1px solid #e5e5e5;vertical-align: top;}
	.list-inside-sidebar li:nth-child(odd){background-color:#eaeaea;}
	.list-inside-sidebar li{padding: 3px 0px 3px 7px;}
	.list-inside-sidebar ul{margin-top:0px;}
</style>
<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">
			<div class="meta-box-sortables ui-sortable">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox">
						<h2 class="title"><span><?php esc_html_e( 'REGISTERED POST TYPES', 'maintainn-tools' ); ?></span></h2>
							<div class="inside">
								<table class="widefat" cellspacing="0">
									<tbody>
										<tr>
											<td class="row-title">
											<?php 
											//gets all the registered post types and sort alphabetically.
										    $all_post_types = get_post_types();
										    ksort($all_post_types);
										   	$cpt_results = implode(", ", $all_post_types);
										   	echo $cpt_results;
											?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox">
							<h2 class="title"><span><?php esc_html_e( 'REGISTERED POST STATUSES', 'maintainn-tools' ); ?></span></h2>
							<div class="inside">
								<table class="widefat" cellspacing="0">
									<tbody>
										<tr>
											<td class="row-title">
											<?php 
											//get all the post statuses and sort alphabetically.
											$all_post_statuses = $info['site_info']['post_statuses'];
											$explode_post_statuses = explode( ' ', $all_post_statuses);
											sort($explode_post_statuses);
											$implode_post_statuses = implode( ' ', $explode_post_statuses);
											echo $implode_post_statuses;
											?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox">
						<h2 class="title"><span><?php esc_html_e( 'PERMALINK STRUCTURE', 'maintainn-tools' ); ?></span></h2>
							<div class="inside">
								<table class="widefat" cellspacing="0">
									<tbody>
										<tr>
											<td class="row-title">
											<?php echo $info['site_info']['permalinks']; ?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
<?php //IF MU PLUGINS
if ( ! empty( $info['plugins']['mu_plugins_count'] ) ) { ?>
	<div class="meta-box-sortables ui-sortable">
		<div class="postbox">
			<h2 class="title"><span><?php printf( __( 'MU PLUGINS ( %d )', 'maintainn-tools' ), $info['plugins']['mu_plugins_count'] ); ?></span></h2>
				<div class="inside">
					<div class="list-inside">
						<?php foreach ( (array) $info['plugins']['mu_plugins'] as $mu_path => $mu_plugin ) {
						printf( '<div class="maintainn-row"><div class="row-title">%s</div><div class="version">%s</div></div>', $mu_plugin['Name'], $mu_plugin['Version'] );
						} ?>
					</div>
				</div>
		</div>
	</div>
<?php } // End if().

//IF MULTISITE Plugins
if ( is_multisite() ) {
	?>
	<div class="meta-box-sortables ui-sortable">
		<div class="postbox">
			<h2 class="title"><span><?php printf( __( 'NETWORK ACTIVE PLUGINS ( %d )', 'maintainn-tools' ), $info['plugins']['network_plugin_count'] ); ?></span></h2>
			<div class="inside">
				<div class="list-inside">
						<?php foreach ( $info['plugins']['network_plugins'] as $plugin_path ) {
							$plugin_base = plugin_basename( $plugin_path );
							// If the plugin isn't active, don't show it.
							if ( ! array_key_exists( $plugin_base, $info['plugins']['network_active_plugins'] ) ) {
							continue;
							}
							$plugin = get_plugin_data( $plugin_path );
							printf( '<div class="maintainn-row"><div class="row-title">%s</div><div class="version">%s</div></div>', $plugin['Name'], $plugin['Version'] );
						} // End foreach(). ?>
				</div>
			</div>
		</div>
	</div>
<?php } // End if(). ?>
	<div class="meta-box-sortables ui-sortable">
		<div class="postbox">
		<h2 class="title"><span><?php printf( __( 'ACTIVE PLUGINS ( %d )', 'maintainn-tools' ), $info['plugins']['active_plugin_count'] ); ?></span></h2>
			<div class="inside">
				<div class="list-inside">
						<?php foreach ( $info['plugins']['plugins'] as $plugin_path => $plugin ) {
							// If the plugin isn't active, don't show it.
							if ( ! in_array( $plugin_path, $info['plugins']['active_plugins'] ) ) {
								continue;
							}
							printf( '<div class="maintainn-row"><div class="row-title">%s</div><div class="version">%s</div></div>', $plugin['Name'], $plugin['Version'] );

						} // End foreach(). ?>
				</div>
			</div>
		</div>
	</div>
	<div class="meta-box-sortables ui-sortable">
		<div class="postbox">
		<h2 class="title"><span><?php printf( __( 'INACTIVE PLUGINS ( %d )', 'maintainn-tools' ), $info['plugins']['inactive_plugin_count'] ); ?></span></h2>
			<div class="inside">
				<div class="list-inside">
					<?php foreach ( $info['plugins']['plugins'] as $plugin_path => $plugin ) {
						// If the plugin isn't active, show it here.
						if ( in_array( $plugin_path, $info['plugins']['active_plugins'] ) ) {
							continue;
						}
						printf( '<div class="maintainn-row"><div class="row-title">%s</div><div class="version">%s</div></div>', $plugin['Name'], $plugin['Version'] );
					} // End foreach(). ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- sidebar -->
<div id="postbox-container-1" class="postbox-container">
	<div class="meta-box-sortables">
		<div class="postbox">
			<h2 class="title"><span><?php esc_html_e( 'Email Your Site Info', 'maintainn-tools' ); ?></span>
			</h2>
			<div class="inside">
				<?php
				$email = new Maintainn_Tools_Emailing();
				$email->email_form();
				?>
			</div>
		</div>
		<div class="postbox">
			<h2 class="title"><span><?php esc_html_e( 'Site &amp; Server Details', 'maintainn-tools' ); ?></span></h2>
			<div class="inside">
				<div class="list-inside-sidebar">
					<ul>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Site URL', 'maintainn-tools' ),
								esc_url( $info['site_info']['site_url'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Active Theme', 'maintainn-tools' ),
								esc_html( $info['theme'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Multisite', 'maintainn-tools' ),
								esc_html( $info['site_info']['is_multisite'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'WordPress Version', 'maintainn-tools' ),
								esc_html( $info['site_info']['wp_version'] )
							); ?></li>
							<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Number of Plugins', 'maintainn-tools' ),
								esc_html( $info['plugins']['plugin_count'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'PHP Version', 'maintainn-tools' ),
								esc_html( $info['server_info']['php_version'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'MySQL Version', 'maintainn-tools' ),
								esc_html( $info['server_info']['mysql_version'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'WordPress Memory Limit', 'maintainn-tools' ),
								esc_html( $info['server_info']['wp_memory_limit'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong> %s',
								esc_html__( 'Web Server:', 'maintainn-tools' ),
								esc_html( $info['server_info']['web_server'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong> %s',
								esc_html__( 'PHP Safe Mode:', 'maintainn-tools' ),
								esc_html( $info['server_info']['php_safe_mode'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong> %s',
								esc_html__( 'PHP Memory Limit:', 'maintainn-tools' ),
								esc_html( $info['server_info']['php_memory_limit'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong> %s',
								esc_html__( 'PHP Upload Max Size:', 'maintainn-tools' ),
								esc_html( $info['server_info']['php_upload_max_filesize'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'PHP Post Max Size', 'maintainn-tools' ),
								esc_html( $info['server_info']['php_post_max_size'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'PHP Time Limit', 'maintainn-tools' ),
								esc_html( $info['server_info']['php_time_limit'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'PHP Max Input Vars', 'maintainn-tools' ),
								esc_html( $info['server_info']['php_max_input_vars'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'WP_DEBUG', 'maintainn-tools' ),
								esc_html( $info['site_info']['wp_debug'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'WP Table Prefix Length', 'maintainn-tools' ),
								esc_html( $info['server_info']['wp_table_prefix_length'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Status', 'maintain-tools' ),
								esc_html( $info['server_info']['wp_table_prefix_status'] )
							); ?></li>
					</ul>
				</div>
			</div>
		</div>
		<!-- .postbox -->
		<div class="postbox">
			<h2 class="title"><span><?php esc_html_e( 'Browser Details', 'maintainn-tools' ); ?></span></h2>
			<div class="inside">
				<div class="list-inside-sidebar">
					<ul>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Browser Name', 'maintainn-tools' ),
								esc_html( $info['browser']['name'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Browser Version', 'maintainn-tools' ),
								esc_html( $info['browser']['version'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Browser User Agent', 'maintainn-tools' ),
								esc_html( $info['browser']['ua'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Platform', 'maintainn-tools' ),
								esc_html( $info['browser']['platform'] )
							); ?></li>
					</ul>
				</div>
			</div>
		</div>
		<!-- .postbox -->
		<div class="postbox">
			<h2 class="title"><span><?php esc_html_e( 'Session Details', 'maintainn-tools' ); ?></span></h2>
			<div class="inside">
				<div class="list-inside-sidebar">
					<ul>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Session', 'maintainn-tools' ),
								esc_html( $info['server_info']['sessions'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Session Name', 'maintainn-tools' ),
								esc_html( $info['server_info']['session_name'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Cookie Path', 'maintainn-tools' ),
								esc_html( $info['server_info']['session_cookie_path'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Save Path', 'maintainn-tools' ),
								esc_html( $info['server_info']['session_save_path'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Use Cookies', 'maintainn-tools' ),
								esc_html( $info['server_info']['session_use_cookies'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'Use Only Cookies', 'maintainn-tools' ),
								esc_html( $info['server_info']['session_use_only_cookies'] )
							); ?></li>
					</ul>
				</div>
			</div>
		</div>
		<!-- .postbox -->
		<div class="postbox">
			<h2 class="title"><span><?php esc_html_e( 'Other Details', 'maintainn-tools' ); ?></span></h2>
			<div class="inside">
				<div class="list-inside-sidebar">
					<ul>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'DISPLAY ERRORS', 'maintainn-tools' ),
								esc_html( $info['server_info']['display_errors'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'FSOCKOPEN', 'maintainn-tools' ),
								esc_html( $info['server_info']['fsockopen'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'cURL', 'maintainn-tools' ),
								esc_html( $info['server_info']['curl'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'SOAP Client', 'maintainn-tools' ),
								esc_html( $info['server_info']['soap_client'] )
							); ?></li>
						<li><?php
							printf(
								'<strong>%s</strong>: %s',
								esc_html__( 'SUHOSIN', 'maintainn-tools' ),
								esc_html( $info['server_info']['suhosin'] )
							); ?></li>
					</ul>
				</div>
			</div>
		</div>
		<!-- .postbox -->
	</div>
	<!-- .meta-box-sortables -->
</div>
<!-- #postbox-container-1 .postbox-container -->
</div>
</div>
</div>
