<?php

namespace ShortNotes\Webmention;

add_action( 'publish_' . \ShortNotes\PostType\Note\get_slug(), __NAMESPACE__ . '\schedule', 10 );
add_action( 'send_shortnote_webmentions', __NAMESPACE__ . '\send' );

/**
 * Schedule a single event to send webmentions whenever a shortnote has
 * a status of "publish".
 *
 * @param int $post_id The post ID of the note.
 */
function schedule( $post_id ) {
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
function send( $post_id ) {
	$post = get_post( $post_id );

	if ( \ShortNotes\PostType\Note\get_slug() === $post->post_type && class_exists( 'Webmention_Sender' ) ) {
		\Webmention_Sender::send_webmentions( $post_id );
	}
}
