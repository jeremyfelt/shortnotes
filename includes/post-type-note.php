<?php

namespace ShortNotes\PostType\Note;

add_action( 'init', __NAMESPACE__ . '\register_post_type', 10 );
add_action( 'admin_init', __NAMESPACE__ . '\flush_rewrite_rules', 10 );
add_filter( 'allowed_block_types', __NAMESPACE__ . '\filter_allowed_block_types', 10, 2 );
add_filter( 'wp_insert_post_data', __NAMESPACE__ . '\filter_wp_insert_post_data', 10 );
add_filter( 'rest_prepare_' . get_slug(), __NAMESPACE__ . '\filter_rest_prepare', 10, 3 );
add_action( 'init', __NAMESPACE__ . '\register_meta' );
add_filter( 'the_content', __NAMESPACE__ . '\prepend_reply_to_markup', 5 );

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
			'label'         => 'Notes',
			'labels'        => array(
				'name'                     => __( 'Notes', 'shortnotes' ),
				'singular_name'            => __( 'Note', 'shortnotes' ),
				'add_new'                  => __( 'Add New' ),
				'add_new_item'             => __( 'Add New Note', 'shortnotes' ),
				'edit_item'                => __( 'Edit Note', 'shortnotes' ),
				'new_item'                 => __( 'New Note', 'shortnotes' ),
				'view_item'                => __( 'View Note', 'shortnotes' ),
				'view_items'               => __( 'View Notes', 'shortnotes' ),
				'search_items'             => __( 'Search Notes', 'shortnotes' ),
				'not_found'                => __( 'No notes found.', 'shortnotes' ),
				'not_found_in_trash'       => __( 'No notes found in Trash.', 'shortnotes' ),
				'all_items'                => __( 'All Notes', 'shortnotes' ),
				'archives'                 => __( 'Note Archives', 'shortnotes' ),
				'attributes'               => __( 'Note Attributes', 'shortnotes' ),
				'insert_into_item'         => __( 'Insert into note', 'shortnotes' ),
				'uploaded_to_this_item'    => __( 'Uploaded to this note', 'shortnotes' ),
				'filter_items_list'        => __( 'Filter notess list', 'shortnotes' ),
				'items_list_navigation'    => __( 'Notes list navigation', 'shortnotes' ),
				'items_list'               => __( 'Notes list', 'shortnotes' ),
				'item_published'           => __( 'Note published.', 'shortnotes' ),
				'item_published_privately' => __( 'Note published privately.', 'shortnotes' ),
				'item_reverted_to_draft'   => __( 'Note reverted to draft.', 'shortnotes' ),
				'item_scheduled'           => __( 'Note scheduled.', 'shortnotes' ),
				'item_updated'             => __( 'Note updated.', 'shortnotes' ),
			),
			'public'        => true,
			'menu_position' => 6,
			'menu_icon'     => 'dashicons-edit-large',
			'show_in_rest'  => true,
			'supports'      => array(
				'editor',
				'comments',
				'author',
				'custom-fields',

				// Webmentions, pingbacks, and trackbacks are required to fully
				// support webmentions until I figure out that I'm wrong.
				'webmentions',
				'pingbacks',
				'trackbacks',
			),
			'has_archive'   => true,
			'rewrite'       => array(
				'slug' => 'notes',
			),
		)
	);
}

/**
 * Flush rewrite rules to ensure the notes post type is
 * available as expected.
 */
function flush_rewrite_rules() {
	$rules_version = get_option( 'shortnotes_rules_version', false );

	if ( false === $rules_version ) {
		\flush_rewrite_rules();
		update_option( 'shortnotes_rules_version', '1.0.0', false );
	}
}

/**
 * Register the meta field(s) used by this post type.
 */
function register_meta() {
	\register_meta(
		'post',
		'shortnotes_reply_to_url',
		array(
			'object_subtype'    => get_slug(),
			'type'              => 'string',
			'description'       => __( 'The URL this note is a reply to.', 'shortnotes' ),
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'single'            => true,
			'show_in_rest'      => true,
		)
	);

	\register_meta(
		'post',
		'shortnotes_reply_to_name',
		array(
			'object_subtype'    => get_slug(),
			'type'              => 'string',
			'description'       => __( 'A name this note is a reply to', 'shortnotes' ),
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'single'            => true,
			'show_in_rest'      => true,
		)
	);
}

/**
 * Limit the blocks that can be used for a notes post. Keep it simple.
 *
 * In general, stick to blocks that do not provide much additional formatting,
 * but that are meant for adding specific pieces of content.
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
			'core/code',
			'core/embed',
			'core/file',
			'core/gallery',
			'core/image',
			'core/list',
			'core/paragraph',
			'core/preformatted',
			'core/pullquote',
			'core/quote',
			'core/verse',
			'core/video',
		);
	}

	return $allowed_block_types;
}

/**
 * Provide a default, placeholder title used when a note is first created
 * as an alternative to "Auto Draft".
 *
 * @return string The placeholder title.
 */
function get_placeholder_title() {
	return __( 'Note', 'shortnotes' );
}

/**
 * Format the note's title to be slightly more descriptive and provide a
 * bit more information about the note.
 *
 * @param array A list of data about the note.
 * @return string The formatted title.
 */
function get_formatted_title( $post_data ) {
	$blocks = parse_blocks( $post_data['post_content'] );

	// Retrieve the site's preferred date and time formats.
	$date_format = get_option( 'date_format', 'F n, Y' );
	$time_format = get_option( 'time_format', 'g:ia' );

	// Retrieve a localized and formatted version of the note's create date. I don't think
	// it's translated in the best way yet, but I'll figure that out soon?
	$sub_title = wp_date( $date_format . ' \a\t ' . $time_format, strtotime( $post_data['post_date_gmt'] ) );

	foreach ( $blocks as $block ) {
		if ( 'core/paragraph' === $block['blockName'] ) {
			$sub_title = wp_strip_all_tags( $block['innerHTML'] );

			// At the risk of being complicated, determine the length of the translated "Note" pretext so
			// that we can build a maximum string of 50 characters.
			$string_lenth = 50 - strlen( get_placeholder_title() );

			// If the note text is less then the max string length, use the full text. If not, append an ellipsis.
			$sub_title = $string_lenth >= mb_strlen( $sub_title ) ? $sub_title : substr( $sub_title, 0, $string_lenth ) . '&hellip;';

			// A paragraph has been found, we're moving on and using it for the title.
			break;
		} elseif ( 'core/image' === $block['blockName'] ) {
			$sub_title = __( 'Image posted on', 'shortnotes' ) . ' ' . $sub_title;
		} elseif ( 'core/gallery' === $block['blockName'] ) {
			$sub_title = __( 'Images posted on', 'shortnotes' ) . ' ' . $sub_title;
		}
	}

	return 'Note: ' . $sub_title;
}

/**
 * Filter post data when it is inserted to ensure a proper slog and title
 * has been generated.
 *
 * Slugs (post_name) are the first 4 characters of a UUID4 combined with
 * a unix timestamp. It's like creative, but not... :)
 *
 * Titles are a placeholder until published and then they are generated
 * with `get_formatted_title()` based on the content.
 *
 * @param array $post_data A list of data about the post to be updated.
 * @return array $post_data A modified list of post data.
 */
function filter_wp_insert_post_data( $post_data ) {
	if ( get_slug() !== $post_data['post_type'] ) {
		return $post_data;
	}

	if ( 'Auto Draft' === $post_data['post_title'] ) {
		$post_data['post_title'] = get_placeholder_title();
		$post_data['post_name']  = substr( wp_generate_uuid4(), 0, 4 ) . time();
	}

	if ( 'publish' === $post_data['post_status'] ) {
		$post_data['post_title'] = get_formatted_title( $post_data );
	}

	return $post_data;
}

/**
 * Filter the title returned with a REST response when a new note
 * is created.
 *
 * This avoids the display of "(no title) is now live." when a post
 * type with no title is first published.
 *
 * @param WP_REST_Response $response The response object.
 * @param WP_Post          $post     Post object.
 * @param WP_REST_Request  $request  Request object.
 */
function filter_rest_prepare( $response, $post, $request ) {
	if ( 'edit' === $request['context'] ) {
		$response->data['title'] = __( 'Your note', 'shortnotes' );
	}

	return $response;
}

/**
 * Retrieve the markup used to indicate a note is a reply.
 *
 * @see https://indieweb.org/reply
 *
 * @param \WP_Post $post A shortnote's post object.
 * @return string Markup to use for a u-in-reply-to.
 */
function get_reply_to_markup( $post ) {
	if ( get_slug() !== $post->post_type ) {
		return '';
	}

	$reply_to_url = get_post_meta( $post->ID, 'shortnotes_reply_to_url', true );

	if ( '' === $reply_to_url ) {
		return '';
	}

	$reply_to_name = get_post_meta( $post->ID, 'shortnotes_reply_to_name', true );

	if ( '' === $reply_to_name ) {
		$reply_to_name = __( 'this post', 'shortnotes' );
	}

	$reply_to_markup = '<p class="shortnotes-reply-to">' . __( 'In reply to:' ) . ' <a class="u-in-reply-to" href="' . esc_url( $reply_to_url ) . '">' . esc_html( $reply_to_name ) . '</a></p>';

	return $reply_to_markup;
}

/**
 * Output the markup used to indicate that the following content container
 * is a reply to something posted at another URL.
 *
 * Note: This function can be used as a template to output markup in
 *       a template _before_ an `e-content` container is output. If
 *       used, this plugins filter of `the_content` will be removed.
 *
 * @see https://indieweb.org/reply
 */
function reply_to_markup() {
	// If this function is used by the theme, we can remove the content filter.
	remove_filter( 'the_content', __NAMESPACE__ . '\prepend_reply_to_markup', 5 );

	$current_post    = get_post();
	$reply_to_markup = get_reply_to_markup( $current_post );

	echo $reply_to_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Prepend reply markup to notes that have a reply to URL and name
 * assigned to them.
 *
 * @see https://indieweb.org/reply
 *
 * @param string $content The current content.
 * @return string The content, possibly prepended with additional markup.
 */
function prepend_reply_to_markup( $content ) {
	if ( is_admin() ) {
		return $content;
	}

	$current_post    = get_post();
	$reply_to_markup = get_reply_to_markup( $current_post );

	$content = $reply_to_markup . $content;

	return $content;
}
