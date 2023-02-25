<?php
/**
 * Manage how shortnotes communicates via webmention.
 *
 * @package shortnotes
 */

namespace ShortNotes\Webmention;

add_action( 'publish_' . \ShortNotes\PostType\Note\get_slug(), __NAMESPACE__ . '\schedule', 10 );
add_action( 'send_shortnote_webmentions', __NAMESPACE__ . '\send' );
add_filter( 'webmention_links', __NAMESPACE__ . '\filter_webmention_links', 10, 2 );

/**
 * Schedule a single event to send webmentions whenever a shortnote has
 * a status of "publish".
 *
 * @param int $post_id The post ID of the note.
 */
function schedule( int $post_id ) {
	if ( defined( 'WP_IMPORTING' ) || false === class_exists( 'Webmention_Sender' ) ) {
		return;
	}

	if ( ! wp_next_scheduled( 'send_shortnote_webmentions', array( $post_id ) ) ) {
		wp_schedule_single_event( time(), 'send_shortnote_webmentions', array( $post_id ) );
	}
}

/**
 * Send the webmention using the Webmention plugin's sender.
 *
 * @param int $post_id The post ID of the note.
 */
function send( int $post_id ) {
	$post = get_post( $post_id );

	if ( \ShortNotes\PostType\Note\get_slug() === $post->post_type && class_exists( 'Webmention_Sender' ) ) {
		\Webmention_Sender::send_webmentions( $post_id );
	}
}

/**
 * Filter the list of URLs passed to the Webmention plugin to include
 * the reply to URL assigned to a note.
 *
 * @param array $urls A list of URLs.
 * @param int   $post_id The current post.
 * @return array A modified list of URLs.
 */
function filter_webmention_links( array $urls, int $post_id ): array {
	$post = get_post( $post_id );

	if ( \ShortNotes\PostType\Note\get_slug() !== $post->post_type ) {
		return $urls;
	}

	$reply_to_url = get_post_meta( $post_id, 'shortnotes_reply_to_url', true );

	if ( '' !== $reply_to_url && ! isset( $urls['reply_to_url'] ) ) {
		$urls[] = $reply_to_url;
	}

	return $urls;
}
