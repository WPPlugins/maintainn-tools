<?php
/**
 * @todo  - move all these vars to methods before view is included
 */
global $wpdb;

$browser = new Browser();

$system_info = new Maintainn_Tools_System_Info( $browser, $wpdb );
$info        = $system_info->get_system_info();

?>
### Begin System Info ###

Multisite:                <?php echo "{$info['site_info']['is_multisite']}\n" ?>

SITE_URL:                 <?php echo "{$info['site_info']['site_url']}\n"; ?>
HOME_URL:                 <?php echo "{$info['site_info']['home_url']}\n"; ?>

WordPress Version:        <?php echo "{$info['site_info']['wp_version']}\n"; ?>
Permalink Structure:      <?php echo "{$info['site_info']['permalinks']}\n"; ?>
Active Theme:             <?php echo "{$info['theme']}\n"; ?>

Registered Post Types:    <?php echo "{$info['site_info']['post_types']}\n"; ?>
Registered Post Statuses: <?php echo "{$info['site_info']['post_statuses']}\n\n"; ?>

Browser Name:             <?php echo "{$info['browser']['name']}\n"; ?>
Browser Version:          <?php echo "{$info['browser']['version']}\n"; ?>
Browser User Agent:       <?php echo "{$info['browser']['ua']}\n"; ?>
Platform:                 <?php echo "{$info['browser']['platform']}\n"; ?>

PHP Version:              <?php echo "{$info['server_info']['php_version']}\n"; ?>
MySQL Version:            <?php echo "{$info['server_info']['mysql_version']}\n"; ?>

Web Server Info:          <?php echo "{$info['server_info']['web_server']}\n"; ?>

PHP Safe Mode:            <?php echo "{$info['server_info']['php_safe_mode']}\n"; ?>
PHP Memory Limit:         <?php echo "{$info['server_info']['php_memory_limit']}\n"; ?>
PHP Upload Max Size:      <?php echo "{$info['server_info']['php_upload_max_filesize']}\n"; ?>
PHP Post Max Size:        <?php echo "{$info['server_info']['php_post_max_size']}\n"; ?>
PHP Time Limit:           <?php echo "{$info['server_info']['php_time_limit']}\n"; ?>
PHP Max Input Vars:       <?php echo "{$info['server_info']['php_max_input_vars']}\n"; ?>

WP_DEBUG:                 <?php echo "{$info['site_info']['wp_debug']}\n" ?>

WP Table Prefix:          <?php printf( __( "Length: %s Status: %s", 'maintainn-tools' ), $info['server_info']['wp_table_prefix_length'], $info['server_info']['wp_table_prefix_status'] ) . "\n"; ?>

Show On Front:            <?php "{$info['site_info']['show_on_front']}\n" ?>
Page On Front:            <?php "{$info['site_info']['page_on_front']}\n" ?>
Page For Posts:           <?php "{$info['site_info']['page_for_posts']}\n" ?>

Session:                  <?php echo "{$info['server_info']['sessions']}\n"; ?>
Session Name:             <?php echo "{$info['server_info']['session_name']}\n"; ?>
Cookie Path:              <?php echo "{$info['server_info']['session_cookie_path']}\n"; ?>
Save Path:                <?php echo "{$info['server_info']['session_save_path']}\n"; ?>
Use Cookies:              <?php echo "{$info['server_info']['session_use_cookies']}\n"; ?>
Use Only Cookies:         <?php echo "{$info['server_info']['session_use_only_cookies']}\n"; ?>

WordPress Memory Limit:   <?php echo "{$info['server_info']['wp_memory_limit']}\n"; ?>
DISPLAY ERRORS:           <?php echo "{$info['server_info']['display_errors']}\n"; ?>
FSOCKOPEN:                <?php echo "{$info['server_info']['fsockopen']}\n"; ?>
cURL:                     <?php echo "{$info['server_info']['curl']}\n"; ?>
SOAP Client:              <?php echo "{$info['server_info']['soap_client']}\n"; ?>
SUHOSIN:                  <?php echo "{$info['server_info']['suhosin']}\n"; ?>

TOTAL PLUGINS: <?php echo "{$info['plugins']['plugin_count']}\n\n"; ?>
<?php if ( ! empty( $info['plugins']['mu_plugin_count'] ) ) : ?>
	MU PLUGINS: (<?php echo $info['plugins']['mu_plugin_count']; ?>)<?php echo "\n\n"; ?>

	<?php
	foreach ( (array) $info['plugins']['mu_plugins'] as $mu_path => $mu_plugin ):
		echo $mu_plugin['Name'] . ': ' . $mu_plugin['Version'] ."\n";
	endforeach;
endif;

// Standard plugins - active.
?>
ACTIVE PLUGINS: (<?php echo $info['plugins']['active_plugin_count']; ?>)<?php echo "\n\n";

foreach ( $info['plugins']['plugins'] as $plugin_path => $plugin ) {
	// If the plugin isn't active, don't show it.
	if ( ! in_array( $plugin_path, $info['plugins']['active_plugins'] ) ) {
		continue;
	}

	echo $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
}

// Standard plugins - inactive.
echo "\n";
echo 'INACTIVE PLUGINS: (' . esc_attr( $info['plugins']['inactive_plugin_count'] ) . ')' . "\n\n";

foreach ( $info['plugins']['plugins'] as $plugin_path => $plugin ) {
	// If the plugin isn't active, show it here.
	if ( in_array( $plugin_path, $info['plugins']['active_plugins'] ) ) {
		continue;
	}

	echo esc_html( $plugin['Name'] ) . ': ' . esc_attr( $plugin['Version'] ) . "\n";
}

// If multisite, grab network as well.
if ( is_multisite() ) :

	echo "\n";
	echo 'NETWORK ACTIVE PLUGINS: (' . esc_attr( $info['plugins']['network_plugin_count'] ) . ')' . "\n\n";

	foreach ( $info['plugins']['network_plugins'] as $plugin_path ) {
		$plugin_base = plugin_basename( $plugin_path );

		// If the plugin isn't active, don't show it.
		if ( ! array_key_exists( $plugin_base, $info['plugins']['network_active_plugins'] ) ) {
			continue;
		}

		$plugin = get_plugin_data( $plugin_path );

		echo esc_html( $plugin['Name'] ) . ': ' . esc_attr( $plugin['Version'] ) . "\n";
	}
endif;
?>

### End System Info ###
