<?php

namespace ShortNotes\Common;

add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_assets' );

/**
 * Retrieve the plugin version.
 *
 * @return string The plugin version.
 */
function get_version() {
	return '0.0.1';
}

/**
 * Enqueue the assets used in the block editor.
 */
function enqueue_block_editor_assets() {
	$assets = require_once dirname( __DIR__ ) . '/build/index.asset.php';

	wp_enqueue_script(
		'shortnotes-extended',
		plugin_dir_url( __DIR__ ) . '/build/index.js',
		$assets['dependencies'],
		$assets['version'],
		true
	);

}
