<?php
/**
 * Plugin Name:     Short Notes
 * Plugin URI:      https://github.com/jeremyfelt/shortnotes/
 * Description:     Add a notes post type to WordPress. For your short notes.
 * Author:          jeremyfelt
 * Author URI:      https://jeremyfelt.com
 * Text Domain:     shortnotes
 * Domain Path:     /languages
 * Version:         0.0.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// This plugin, like WordPress, requires PHP 5.6 and higher.
if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
	add_action( 'admin_notices', 'shortnotes_admin_notice' );
	/**
	 * Display an admin notice if PHP is not 5.6.
	 */
	function shortnotes_admin_notice() {
		echo '<div class=\"error\"><p>';
		echo __( 'The Short Notes WordPress plugin requires PHP 5.6 to function properly. Please upgrade PHP or deactivate the plugin.', 'shortnotes' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</p></div>';
	}

	return;
}

require_once __DIR__ . '/includes/common.php';
require_once __DIR__ . '/includes/post-type-note.php';
