<?php
/**
 * Class TestShareOnMastodon
 *
 * Tests bits of the integration with Share on Mastodon.
 *
 * @package shortnotes
 */

use ShortNotes\PostType\Note;

/**
 * Test bits of the integration with Share on Mastodon.
 */
class TestShareOnMastodon extends WP_UnitTestCase {

	/**
	 * Test that a reply to ID is not generated for a non-Mastodon URL.
	 */
	public function test_get_reply_to_id_non_mastodon_url() {
		$status_id = \Shortnotes\ShareOnMastodon\get_reply_to_id( 'https://jeremyfelt.com' );

		$this->assertEquals( $status_id, 0 );
	}

	/**
	 * The Mastodon ID of a previously shared note should be parsed from meta.
	 */
	public function test_get_reply_to_id_from_previously_shared_note() {
		$post_id = self::factory()->post->create(
			[
				'post_type'   => Note\get_slug(),
				'post_name'   => 'f2c11678336293',
				'post_status' => 'publish',
			]
		);
		update_post_meta( $post_id, '_share_on_mastodon_url', 'https://indieweb.social/@jeremyfelt/109408328908231044' );

		$status_id = \Shortnotes\ShareOnMastodon\get_reply_to_id( 'http://example.org/notes/f2c11678336293/' );

		wp_delete_post( $post_id, true );

		$this->assertEquals( $status_id, 109408328908231044 );
	}

	/**
	 * Test that a reply to ID is not generated for a Mastodon URL that is not a status.
	 */
	public function test_get_reply_to_id_mastodon_non_status_url() {
		$status_id = \Shortnotes\ShareOnMastodon\get_reply_to_id( 'https://indieweb.social/@jeremyfelt' );

		$this->assertEquals( $status_id, 0 );
	}

	/**
	 * Test that a reply to ID is generated for a Mastodon status URL.
	 */
	public function test_get_reply_to_id_mastodon_status_url() {
		$status_id = \Shortnotes\ShareOnMastodon\get_reply_to_id( 'https://indieweb.social/@jeremyfelt/109408328908231044' );

		$this->assertEquals( $status_id, 109408328908231044 );
	}
}
