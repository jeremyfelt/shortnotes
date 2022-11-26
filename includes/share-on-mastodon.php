<?php
/**
 * Integrate with the Share on Mastodon plugin.
 *
 * @package shortnotes
 */

namespace ShortNotes\ShareOnMastodon;

use Shortnotes\PostType\Note;

add_filter( 'share_on_mastodon_status', __NAMESPACE__ . '\filter_status_text', 10, 2 );

/**
 * Filter Mastodon toot args to include a reply to ID if it exists.
 *
 * @param array    $args The args sent with a new Mastodon post.
 * @param \WP_Post $post The post object.
 */
function filter_args( array $args, \WP_Post $post ) : array {
	if ( Note\get_slug() !== $post->post_type ) {
		return $args;
	}

	$reply_to_url = get_post_meta( $post->ID, 'shortnotes_reply_to_url', true );

	if ( ! $reply_to_url ) {
		return $args;
	}

	$reply_host = wp_parse_url( $reply_to_url, PHP_URL_HOST );
	$mastodon   = get_user_meta( $post->post_author, 'mastodon', true );

	if ( ! $mastodon || $reply_host !== wp_parse_url( $mastodon, PHP_URL_HOST ) ) {
		return $args;
	}

	$path = trim( wp_parse_url( $reply_to_url, PHP_URL_PATH ), '/' );
	$path = explode( '/', $path );

	if ( 3 !== count( $path ) || 'web' !== $path[0] || ! is_numeric( $path[2] ) ) {
		return $args;
	}

	$args['in_reply_to_id'] = $path[2];

	return $args;
}

/**
 * Filter a toot so that the note content is used rather than the title.
 *
 * @param string   $status The status text.
 * @param \WP_Post $post   The post object.
 * @return string The modified status text.
 */
function filter_status_text( $status, $post ) {
	$status = apply_filters( 'the_content', $post->post_content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	$status = trim( $status );
	$status = convert_anchors( $status );

	$status = str_replace( '<p>', '', $status );
	$status = substr_replace( $status, ' ', strrpos( $status, '</p>' ), 4 );
	$status = str_replace( '</p>', "\n\n", $status );

	// Do what the plugin does to the title, but to the rendered content.
	$status = wp_strip_all_tags(
		html_entity_decode( $status, ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) ) // Avoid double-encoded HTML entities.
	);

	// Remove more than two contiguous line breaks. Thanks, wpautop!
	$status = preg_replace( "/\n\n+/", "\n\n", $status );

	add_filter(
		'share_on_mastodon_toot_args',
		function( $args ) use ( $post ) {
			return filter_args( $args, $post );
		}
	);

	return $status;
}

/**
 * Parse and move anchors to the end of post content.
 *
 * @param string $html The post content.
 * @return string Modified post content.
 */
function convert_anchors( string $html ) : string {
	preg_match_all( '/<a\s+(?:[^>]*?\s+)?href=(["\'])(.*?)\1/', $html, $matches );

	$links = array_filter(
		$matches[2],
		function( $link ) {
			return wp_parse_url( $link, PHP_URL_HOST );
		}
	);

	return $html . implode( ' ', $links );
}
