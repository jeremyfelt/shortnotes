<?php
/**
 * Integrate with the Share on Mastodon plugin.
 *
 * @package shortnotes
 */

namespace ShortNotes\ShareOnMastodon;

use Shortnotes\PostType\Note;

add_filter( 'share_on_mastodon_status', __NAMESPACE__ . '\filter_status_text', 10, 2 );
add_filter( 'share_on_mastodon_toot_args', __NAMESPACE__ . '\filter_args', 10, 2 );

/**
 * Filter Mastodon toot args to include a reply to ID if it exists.
 *
 * @param array    $args The args sent with a new Mastodon post.
 * @param \WP_Post $post The post object.
 * @return array Modified args.
 */
function filter_args( array $args, \WP_Post $post ): array {
	if ( Note\get_slug() !== $post->post_type ) {
		return $args;
	}

	$reply_to_url = get_post_meta( $post->ID, 'shortnotes_reply_to_url', true );

	if ( ! $reply_to_url ) {
		return $args;
	}

	$status_id = get_reply_to_id( $reply_to_url );

	if ( 0 < $status_id ) {
		$args['in_reply_to_id'] = $status_id;
	}

	return $args;
}

/**
 * Determine if the server hosting a URL is powered by Mastodon.
 *
 * @param string $url The URL.
 * @return bool True if Mastodon. False if not.
 */
function is_mastodon_server( string $url ): bool {
	$result = wp_remote_head( $url );

	if ( is_wp_error( $result ) ) {
		return false;
	}

	$server = strtolower( wp_remote_retrieve_header( $result, 'server' ) );

	return 'mastodon' === $server;
}

/**
 * Retrieve the reply to ID for a Mastodon status URL.
 *
 * @param string $url The Mastodon status URL.
 * @return int The status ID if available. 0 if not.
 */
function get_reply_to_id( string $url ): int {
	$site_host = wp_parse_url( home_url(), PHP_URL_HOST );
	$url_host  = wp_parse_url( $url, PHP_URL_HOST );
	$url_path  = wp_parse_url( $url, PHP_URL_PATH );

	if ( ! $url_path ) {
		return 0;
	}

	if ( $site_host === $url_host ) {
		$path_parts = explode( '/', trim( $url_path, '/' ) );
		$post_name  = array_pop( $path_parts ); // Huge assumptions are okay with me!
		$post_id    = Note\get_note_by_post_name( $post_name );

		// No note was found.
		if ( ! $post_id ) {
			return 0;
		}

		// Override the URL with the note's previously stored Mastodon URL.
		$url = get_post_meta( $post_id, '_share_on_mastodon_url', true );

		if ( ! $url ) {
			return 0;
		}

		$url_path = wp_parse_url( $url, PHP_URL_PATH );

		if ( ! $url_path ) {
			return 0;
		}
	} elseif ( false === is_mastodon_server( $url ) ) {
		return 0;
	}

	$path_parts = explode( '/', trim( $url_path, '/' ) );

	if ( 2 !== count( $path_parts ) || ! is_numeric( $path_parts[1] ) ) {
		return 0;
	}

	return (int) $path_parts[1];
}

/**
 * Filter a toot so that the note content is used rather than the title.
 *
 * @param string   $status The status text.
 * @param \WP_Post $post   The post object.
 * @return string The modified status text.
 */
function filter_status_text( string $status, \WP_Post $post ): string {
	$status = Note\transform_content( $post->post_content );

	return $status;
}
