<?php

namespace ShortNotes\PostType\Note;

add_action( 'init', __NAMESPACE__ . '\register_post_type', 10 );
add_filter( 'allowed_block_types', __NAMESPACE__ . '\filter_allowed_block_types', 10, 2 );

/**
 * Provide the common slug used for the Notes post type.
 *
 * @return string The post type slug.
 */
function get_slug() {
	return 'shortnote';
}

/**
 * Register the Notes post type.
 */
function register_post_type() {
	\register_post_type(
		get_slug(),
		array(
			'labels'        => array(
				'name'          => 'Notes',
				'singular_name' => 'Note',
			),
			'public'        => true,
			'menu_position' => 6,
			'menu_icon'     => 'dashicons-edit-large',
			'show_in_rest'  => true,
			'supports'      => array(
				'editor',
				'comments',
				'webmentions',
			),
			'has_archive'   => true,
			'rewrite'       => array(
				'slug' => 'notes',
			),
		)
	);
}

/**
 * Limit the blocks that can be used for a notes post. Keep it simple.
 *
 * Note: There's nothing horrible about allowing more blocks. Unhooking this
 *       function from the `allowed_block_types` filter won't cause any trouble.
 *
 * @param array    $allowed_block_types A list of allowed block types.
 * @param \WP_Post $post                The current note.
 * @return array A modified list of allowed block types.
 */
function filter_allowed_block_types( $allowed_block_types, $post ) {
	if ( get_slug() === $post->post_type ) {
		return array(
			'core/paragraph',
			'core/image',
			'core/gallery',
		);
	}

	return $allowed_block_types;
}
