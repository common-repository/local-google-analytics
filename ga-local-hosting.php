<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
Plugin Name: Local Google Analytics
Description: Enables you to host Google Analytics tracking script locally on your webspace in order to increase website performance.
Author: Fricke Media
Version: 1.0
Author URI: https://www.fricke-media.de/
*/


function galh_create_admin_menu() {
	
	// Create new entry in options menu:
	add_options_page('Local Google Analytics', 'Local Google Analytics', 'manage_options', 'galh', 'galh_admin_init');
	
	// Call register settings function:
	add_action( 'admin_init', 'galh_register_settings' );
}

function galh_admin_init() {	
	
	// Check user permissions:
	if (!current_user_can('manage_options')) {
		wp_die(_e('You do not have sufficient permissions to access this page.'));
	}
	
	// Update tracking code snippet:
	galh_update_tracking_code ();
	
	?>

	<div class="wrap">
		<h1><?php echo _e('Local Google Analytics') ?></h1>
		<p><?php echo _e('For more information please visit <a href="https://www.fricke-media.de/wordpress-plugins">Fricke Media</a>.'); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'galh-settings-group' ); ?>
			<?php do_settings_sections( 'galh-settings-group' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php echo _e('Property ID') ?></th>
					<td><input type="text" name="galh_tracking_id" value="<?php echo esc_attr(get_option('galh_tracking_id')); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo _e('Path to analytics.js') ?></th>
					<td><input type="text" name="galh_analytics_js_path" value="<?php echo esc_attr(get_option('galh_analytics_js_path')); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo _e('Tracking Snippet') ?></th>
					<td><textarea style="resize: both;" name="galh_tracking_snippet" cols="150" rows="25"><?php echo esc_attr( get_option('galh_tracking_snippet') ); ?></textarea><br><p>In the tracking snippet you may use <em>%GALH_ANALYTICS_JS_PATH%</em> and <em>%GALH_PROPERTY_ID%</em> to insert Googly Analytics Property ID an path to analytics.js.</p></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
		<p><?php echo __('') ?></p>
	</div>

	<?php
}

function galh_register_settings() {
	// Register settings:
	register_setting('galh-settings-group', 'galh_tracking_id');
	register_setting('galh-settings-group', 'galh_tracking_snippet');
	register_setting('galh-settings-group', 'galh_analytics_js_path');
}

function galh_hook_tracking_code() {
	// Get tracking snippet from database:
	$galh_tracking_snippet = get_option('galh_tracking_snippet');

	// Substitute "%GALH" with property ID: 
	$galh_tracking_snippet = str_replace('%GALH_PROPERTY_ID%', get_option('galh_tracking_id'), $galh_tracking_snippet);
	$galh_tracking_snippet = str_replace('%GALH_ANALYTICS_JS_PATH%', get_option('galh_analytics_js_path'), $galh_tracking_snippet);
	
	// Print tracking snippet:
	echo $galh_tracking_snippet;
}

function galh_update_tracking_code () {
	// Update tracking code:
	include(dirname(__FILE__).'/update.php');
}

// Run this plugin:
add_action('admin_menu', 'galh_create_admin_menu');
add_action('wp_head', 'galh_hook_tracking_code');

?>
