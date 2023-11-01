<?php
/**
 * Manage the shortnote post type.
 *
 * @package shortnotes
 */

namespace ShortNotes\PostType\Note;

add_action( 'init', __NAMESPACE__ . '\register_post_type', 10 );
add_action( 'admin_init', __NAMESPACE__ . '\flush_rewrite_rules', 10 );
add_filter( 'allowed_block_types', __NAMESPACE__ . '\filter_allowed_block_types', 10, 2 );
add_filter( 'wp_insert_post_data', __NAMESPACE__ . '\filter_wp_insert_post_data', 10 );
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
	$post_type_args = array(
		'label'         => 'Notes',
		'labels'        => array(
			'name'                     => __( 'Notes', 'shortnotes' ),
			'singular_name'            => __( 'Note', 'shortnotes' ),
			'add_new'                  => __( 'Add New', 'shortnotes' ),
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
	);

	/**
	 * Filters the post type arguments used to register the Shortnotes post type.
	 *
	 * @since 1.3.0
	 *
	 * @param array $post_type_args A list of arguments passed to register_post_type().
	 */
	$post_type_args = apply_filters( 'shortnotes_post_type_arguments', $post_type_args );

	\register_post_type(
		get_slug(),
		$post_type_args
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
		'shortnotes_note_type',
		array(
			'object_subtype'    => get_slug(),
			'type'              => 'string',
			'description'       => __( 'The type of note.', 'shortnotes' ),
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'single'            => true,
			'show_in_rest'      => true,
		)
	);

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
 * @param bool|string[] $allowed_block_types A list of allowed block types. Boolean true by default.
 * @param \WP_Post      $post                The current note.
 * @return bool|string[] A modified list of allowed block types.
 */
function filter_allowed_block_types( $allowed_block_types, \WP_Post $post ) {
	if ( get_slug() === $post->post_type ) {
		return array(
			'core/code',
			'core/embed',
			'core/file',
			'core/gallery',
			'core/image',
			'core/list',
			'core/list-item',
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
 * Generate a shortened subtitle from a block of HTML.
 *
 * @since 1.1.5
 *
 * @param string $html Markup.
 * @return string Generated subtitle.
 */
function generate_sub_title( string $html ): string {
	$sub_title = wp_strip_all_tags( $html );

	// Stripe newline characters.
	$sub_title = str_replace( array( "\r", "\n" ), '', $sub_title );

	// At the risk of being complicated, determine the length of the translated "Note" pretext so
	// that we can build a maximum string of 50 characters.
	$string_lenth = 50 - strlen( get_placeholder_title() );

	// If the note text is less then the max string length, use the full text. If not, append an ellipsis.
	$sub_title = $string_lenth >= mb_strlen( $sub_title ) ? $sub_title : substr( $sub_title, 0, $string_lenth ) . '&hellip;';

	return $sub_title;
}

/**
 * Format the note's title to be slightly more descriptive and provide a
 * bit more information about the note.
 *
 * @param array $post_data A list of data about the note.
 * @return string The formatted title.
 */
function get_formatted_title( array $post_data ): string {
	$blocks = parse_blocks( $post_data['post_content'] );

	// Retrieve the site's preferred date and time formats.
	$date_format = get_option( 'date_format', 'F n, Y' );
	$time_format = get_option( 'time_format', 'g:ia' );

	// Retrieve a localized and formatted version of the note's create date. I don't think
	// it's translated in the best way yet, but I'll figure that out soon?
	$sub_title = wp_date( $date_format . ' \a\t ' . $time_format, strtotime( $post_data['post_date_gmt'] ) );

	foreach ( $blocks as $block ) {
		if ( 'core/paragraph' === $block['blockName'] ) {
			$sub_title = generate_sub_title( $block['innerHTML'] );

			// A paragraph has been found, we're moving on and using it for the title.
			break;
		} elseif ( 'core/quote' === $block['blockName'] ) {
			$sub_title = generate_sub_title( '&ldquo;' . trim( $post_data['post_content'] ) );

			// A quote has been found, use its plain text equivalent and move on.
			break;
		} elseif ( 'core/image' === $block['blockName'] ) {
			$sub_title = __( 'Image posted on', 'shortnotes' ) . ' ' . $sub_title;
		} elseif ( 'core/gallery' === $block['blockName'] ) {
			$sub_title = __( 'Images posted on', 'shortnotes' ) . ' ' . $sub_title;
		} elseif ( null === $block['blockName'] && 1 < mb_strlen( trim( wp_strip_all_tags( $block['innerHTML'] ) ) ) ) {
			$sub_title = generate_sub_title( $block['innerHTML'] );

			// A non-block block of HTML has been found with more than one character, so we're using that as the HTML.
			break;
		}
	}

	/**
	 * Filters the formatted title generated for notes.
	 *
	 * @since 1.1.4
	 *
	 * @param string $title     The formatted title.
	 * @param array  $post_data A list of data about the post to be updated.
	 * @return string The filtered formatted title.
	 */
	return apply_filters( 'shortnotes_formatted_title', 'Note: ' . $sub_title, $post_data );
}

/**
 * Filter post data when it is inserted to ensure a proper slug and title
 * has been generated.
 *
 * Slugs (post_name) are the first 4 characters of a UUID4 combined with
 * a unix timestamp. It's like creative, but not... :)
 *
 * Titles are a placeholder until published and then generated with
 * `get_formatted_title()` based on the content.
 *
 * @param array $post_data A list of data about the post to be updated.
 * @return array $post_data A modified list of post data.
 */
function filter_wp_insert_post_data( array $post_data ): array {
	if ( get_slug() !== $post_data['post_type'] ) {
		return $post_data;
	}

	if ( 'Auto Draft' === $post_data['post_title'] ) {
		$post_data['post_title'] = get_placeholder_title();
		$post_data['post_name']  = substr( wp_generate_uuid4(), 0, 4 ) . time();
	}

	if ( in_array( $post_data['post_status'], [ 'publish', 'future' ], true ) ) {
		$post_data['post_title'] = get_formatted_title( $post_data );
	}

	return $post_data;
}

/**
 * Retrieve the markup used to indicate a note is a reply.
 *
 * @see https://indieweb.org/reply
 *
 * @param \WP_Post $post A shortnote's post object.
 * @return string Markup to use for a u-in-reply-to.
 */
function get_reply_to_markup( \WP_Post $post ): string {
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

	/**
	 * Filters the text used for the reply-to name.
	 *
	 * @since 1.1.2
	 *
	 * @param string   $reply_to_name The current text.
	 * @param \WP_Post $post          A shortnote's post object.
	 * @param string   $reply_to_url  The reply-to URL.
	 */
	$reply_to_name = apply_filters( 'shortnotes_reply_to_name', $reply_to_name, $post, $reply_to_url );

	$reply_to_markup = '<p class="shortnotes-reply-to">' . __( 'In reply to:', 'shortnotes' ) . ' <a class="u-in-reply-to" href="' . esc_url( $reply_to_url ) . '">' . esc_html( $reply_to_name ) . '</a></p>';

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
function prepend_reply_to_markup( string $content ): string {
	if ( is_admin() ) {
		return $content;
	}

	$current_post = get_post();

	// We can only adjust content with context, which WordPress does not
	// guarantee when filtering `the_content`.
	if ( ! $current_post ) {
		return $content;
	}

	$reply_to_markup = get_reply_to_markup( $current_post );

	$content = $reply_to_markup . $content;

	return $content;
}

/**
 * Retrieve a note by its post_name value (the slug used in the URL).
 *
 * @param string $post_name A note's post_name.
 * @return int The post ID for the note. 0 if not found.
 */
function get_note_by_post_name( string $post_name ): int {
	global $wpdb;

	$post_id = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID from $wpdb->posts WHERE post_type = %s AND post_name = %s LIMIT 1",
			get_slug(),
			sanitize_key( $post_name )
		)
	);

	return $post_id;
}

/**
 * Extract a list of links from note content.
 *
 * @param string $html The note content.
 * @return string[] A list of links.
 */
function extract_links( string $html ): array {
	preg_match_all( '/<a\s+(?:[^>]*?\s+)?href=(["\'])(.*?)\1/', $html, $matches );

	$links = array_filter(
		$matches[2],
		function ( $link ) {
			return wp_parse_url( $link, PHP_URL_HOST );
		}
	);

	return $links;
}

/**
 * Strip all HTML tags from a string and avoid double-encoded HTML entities.
 *
 * @param string $html The HTML.
 * @return string The text.
 */
function strip_html( string $html ): string {
	return wp_strip_all_tags(
		html_entity_decode( $html, ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) ) // Avoid double-encoded HTML entities.
	);
}

/**
 * Recursively transform an individual block and its inner blocks into an
 * opinionated text-only version.
 *
 * @param array $block The WordPress block to transform.
 * @return string The block represented in text.
 */
function transform_block( array $block ): string {
	if ( null === $block['blockName'] && '' === trim( $block['innerHTML'] ) ) {
		return '';
	}

	$content = '';

	if ( 'core/quote' === $block['blockName'] ) {
		// Quotes start and end with a double curly quotation mark.
		$content .= '“';

		foreach ( $block['innerBlocks'] as $inner_block ) {
			// Strip leading and trailing double quotation marks.
			$content .= html_entity_decode(
				preg_replace(
					'/^(&quot;|&ldquo;|&rdquo;)|(&quot;|&ldquo;|&rdquo;)$/',
					'',
					htmlentities(
						transform_block( $inner_block )
					)
				),
			);
		}

		$citation = trim( ltrim( trim( strip_html( $block['innerHTML'] ) ), '-' ) );

		if ( '' !== $citation ) {
			$content .= '” - ' . $citation;
		} else {
			$content .= '”';
		}
	} elseif ( 'core/embed' === $block['blockName'] ) {
		$content .= $block['attrs']['url'] ?? '';
	} elseif ( 'core/preformatted' === $block['blockName'] ) {
		// We should expect preservation of line breaks in preformatted blocks
		// and some may appear as <br> tags by accident.
		$content .= strip_html(
			str_replace(
				'<br>',
				"\n",
				trim( $block['innerHTML'] )
			)
		);
	} elseif ( 'core/list' === $block['blockName'] ) {
		$list_content = '';

		foreach ( $block['innerBlocks'] as $inner_block ) {
			$list_content .= transform_block( $inner_block );
		}

		$content .= rtrim( $list_content, "\n" );
	} elseif ( 'core/list-item' === $block['blockName'] ) {
		$content .= '- ' . strip_html( trim( $block['innerHTML'] ) ) . "\n";
	} else {
		$content .= strip_html( trim( $block['innerHTML'] ) );
	}

	return $content;
}

/**
 * Transform WordPress flavored HTML from note content to an opinionated text
 * format for sharing on text-based services like Mastodon.
 *
 * @param string $html The original note HTML.
 * @return string The content as plain text, whatever that means.
 */
function transform_content( string $html ): string {
	$blocks        = parse_blocks( trim( $html ) );
	$links         = extract_links( $html );
	$content_parts = [];

	foreach ( $blocks as $block ) {
		$block_text = transform_block( $block );
		if ( '' !== $block_text ) {
			$content_parts[] = $block_text;
		}
	}

	$content  = implode( "\n\n", $content_parts );
	$content .= 0 < count( $links ) ? ' ' . implode( ' ', $links ) : '';

	return $content;
}
