<?php
/**
 * Manage the plugin.
 *
 * @package shortnotes
 */

namespace ShortNotes\Common;

add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_assets' );

/**
 * Enqueue the assets used in the block editor.
 */
function enqueue_block_editor_assets() {
	// Do not load assets on other post types.
	if ( \ShortNotes\PostType\Note\get_slug() !== get_current_screen()->id ) {
		return;
	}

	$assets = require_once dirname( __DIR__ ) . '/build/index.asset.php';

	wp_enqueue_script(
		'shortnotes-extended',
		plugin_dir_url( __DIR__ ) . '/build/index.js',
		$assets['dependencies'],
		$assets['version'],
		true
	);
}
