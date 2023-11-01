<?php
/**
 * Plugin Name:     Shortnotes
 * Plugin URI:      https://wordpress.org/plugins/shortnotes/
 * Description:     Add a notes post type to WordPress. For your short notes.
 * Author:          jeremyfelt
 * Author URI:      https://jeremyfelt.com
 * Text Domain:     shortnotes
 * Domain Path:     /languages
 * Version:         1.6.2
 *
 * @package shortnotes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/includes/common.php';
require_once __DIR__ . '/includes/post-type-note.php';
require_once __DIR__ . '/includes/share-on-mastodon.php';
require_once __DIR__ . '/includes/webmention.php';
