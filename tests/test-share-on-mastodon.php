<?php
/**
 * Class TestShareOnMastodon
 *
 * Tests bits of the integration with Share on Mastodon.
 *
 * @package shortnotes
 */

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

	/**
	 * Test that no changes are made to content without anchors.
	 */
	public function test_convert_anchors_no_anchors() {
		$pre_html    = '<p>This is a paragraph with no links.</p>';
		$parsed_html = \Shortnotes\ShareOnMastodon\convert_anchors( $pre_html );

		$this->assertEquals( $pre_html, $parsed_html );
	}

	/**
	 * Test that a single anchor found in content is added to the end of the string.
	 */
	public function test_convert_anchors_one_anchors() {
		$pre_html      = '<p>This is a paragraph with <a href="https://jeremyfelt.com">one</a> link.</p>';
		$expected_html = '<p>This is a paragraph with <a href="https://jeremyfelt.com">one</a> link.</p>https://jeremyfelt.com';
		$parsed_html   = \Shortnotes\ShareOnMastodon\convert_anchors( $pre_html );

		$this->assertEquals( $parsed_html, $expected_html );
	}

	/**
	 * Test that multiple anchors found in content are added to the end of the string.
	 */
	public function test_convert_anchors_multiple_anchors_one_paragraph() {
		$pre_html      = '<p>This is a paragraph with <a href="https://jeremyfelt.com">one</a> and <a href="https://indieweb.org">two</a> links.</p>';
		$expected_html = '<p>This is a paragraph with <a href="https://jeremyfelt.com">one</a> and <a href="https://indieweb.org">two</a> links.</p>https://jeremyfelt.com https://indieweb.org';
		$parsed_html   = \Shortnotes\ShareOnMastodon\convert_anchors( $pre_html );

		$this->assertEquals( $parsed_html, $expected_html );
	}

	/**
	 * Test that multiple anchors across multiple paragraphs are moved to the end of the string.
	 */
	public function test_convert_anchors_multiple_anchors_multiple_paragraphs() {
		$pre_html      = '<p>This is a paragraph with <a href="https://jeremyfelt.com">one</a> and <a href="https://indieweb.org">two</a>...</p><p>And <a href="https://fishtacos.blog">three</a> links.</p>';
		$expected_html = '<p>This is a paragraph with <a href="https://jeremyfelt.com">one</a> and <a href="https://indieweb.org">two</a>...</p><p>And <a href="https://fishtacos.blog">three</a> links.</p>https://jeremyfelt.com https://indieweb.org https://fishtacos.blog';
		$parsed_html   = \Shortnotes\ShareOnMastodon\convert_anchors( $pre_html );

		$this->assertEquals( $parsed_html, $expected_html );
	}
}
