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

	/**
	 * Test that no changes are made to content without anchors.
	 */
	public function test_extract_links_no_anchors() {
		$pre_html = '<p>This is a paragraph with no links.</p>';
		$links    = \Shortnotes\ShareOnMastodon\extract_links( $pre_html );

		$this->assertEquals( $links, [] );
	}

	/**
	 * Test that a single anchor found in content is added to the end of the string.
	 */
	public function test_extract_links_one_anchors() {
		$pre_html = '<p>This is a paragraph with <a href="https://jeremyfelt.com">one</a> link.</p>';
		$expected = [
			'https://jeremyfelt.com',
		];
		$links    = \Shortnotes\ShareOnMastodon\extract_links( $pre_html );

		$this->assertEquals( $links, $expected );
	}

	/**
	 * Test that multiple anchors found in content are added to the end of the string.
	 */
	public function test_extract_links_multiple_anchors_one_paragraph() {
		$pre_html = '<p>This is a paragraph with <a href="https://jeremyfelt.com">one</a> and <a href="https://indieweb.org">two</a> links.</p>';
		$expected = [
			'https://jeremyfelt.com',
			'https://indieweb.org',
		];
		$links    = \Shortnotes\ShareOnMastodon\extract_links( $pre_html );

		$this->assertEquals( $links, $expected );
	}

	/**
	 * Test that multiple anchors across multiple paragraphs are moved to the end of the string.
	 */
	public function test_extract_links_multiple_anchors_multiple_paragraphs() {
		$pre_html = '<p>This is a paragraph with <a href="https://jeremyfelt.com">one</a> and <a href="https://indieweb.org">two</a>...</p><p>And <a href="https://fishtacos.blog">three</a> links.</p>';
		$expected = [
			'https://jeremyfelt.com',
			'https://indieweb.org',
			'https://fishtacos.blog',
		];
		$links    = \Shortnotes\ShareOnMastodon\extract_links( $pre_html );

		$this->assertEquals( $links, $expected );
	}

	/**
	 * Test that a single paragraph containing multiple links is transformed to
	 * text as expected.
	 */
	public function test_transform_single_paragraph_with_multiple_links() {
		ob_start();
		?>
<!-- wp:paragraph -->
<p>My <a href="https://wordpress.org/plugins/shortnotes/">Shortnotes plugin</a>, which powers <a href="https://jeremyfelt.com/notes/">the notes area of my site</a>, now integrates with <a href="https://wordpress.org/plugins/share-on-mastodon/">Share on Mastodon</a>, the plugin I use to share notes (like this one) on Mastodon. #meta #WordPress #indieweb #hashtags</p>
<!-- /wp:paragraph -->
		<?php
		$pre_html         = ob_get_clean();
		$expected_text    = 'My Shortnotes plugin, which powers the notes area of my site, now integrates with Share on Mastodon, the plugin I use to share notes (like this one) on Mastodon. #meta #WordPress #indieweb #hashtags https://wordpress.org/plugins/shortnotes/ https://jeremyfelt.com/notes/ https://wordpress.org/plugins/share-on-mastodon/';
		$transformed_text = \Shortnotes\ShareOnMastodon\transform_content( $pre_html );

		$this->assertEquals( $expected_text, $transformed_text );
	}

	/**
	 * A set of paragraphs should render as text with two new lines separating
	 * each paragraph.
	 */
	public function test_transform_multiple_paragraphs_with_multiple_links() {
		ob_start();
		?>
<!-- wp:paragraph -->
<p>There's no obvious way to report a factual error as not actually an error in IMDB, so I'll shout into the void because <a href="https://xkcd.com/386/">duty calls</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><em><a href="https://en.m.wikipedia.org/wiki/The_Banshees_of_Inisherin">The Banshees of Inisherin</a></em> is set in 1923 and on the pub wall in one scene is an advertisement for Irish whisky, no “e”.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>This is most likely accurate, as there was no “e” in Irish whisky for a long time before there was an “e” and the introduction of the “e” <a href="https://www.forbes.com/sites/joemicallef/2018/05/17/is-it-whisky-or-whiskey-and-why-it-matters/?sh=75a61d1c7561">happened at a very slow pace</a>.</p>
<!-- /wp:paragraph -->
		<?php
		$pre_html         = ob_get_clean();
		$expected_text    = "There's no obvious way to report a factual error as not actually an error in IMDB, so I'll shout into the void because duty calls.\n\nThe Banshees of Inisherin is set in 1923 and on the pub wall in one scene is an advertisement for Irish whisky, no “e”.\n\nThis is most likely accurate, as there was no “e” in Irish whisky for a long time before there was an “e” and the introduction of the “e” happened at a very slow pace. https://xkcd.com/386/ https://en.m.wikipedia.org/wiki/The_Banshees_of_Inisherin https://www.forbes.com/sites/joemicallef/2018/05/17/is-it-whisky-or-whiskey-and-why-it-matters/?sh=75a61d1c7561";
		$transformed_text = \ShortNotes\ShareOnMastodon\transform_content( $pre_html );

		$this->assertEquals( $expected_text, $transformed_text );
	}

	/**
	 * A single quote block containing a single paragraph and a citation should
	 * render as text surrounded with curly quotes.
	 */
	public function test_convert_single_quote_block_to_quoted_text() {
		ob_start();
		?>
<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>—That is horse piss and rotted straw, he thought. It is a good odour to breathe. It will calm my heart. My heart is quite calm now. I will go back.</p>
<!-- /wp:paragraph --><cite>Stephen Dedalus, A Portrait of the Artist as a Young Man</cite></blockquote>
<!-- /wp:quote -->
		<?php
		$pre_html         = ob_get_clean();
		$expected_text    = '“—That is horse piss and rotted straw, he thought. It is a good odour to breathe. It will calm my heart. My heart is quite calm now. I will go back.” - Stephen Dedalus, A Portrait of the Artist as a Young Man';
		$transformed_text = \Shortnotes\ShareOnMastodon\transform_content( $pre_html );

		$this->assertEquals( $expected_text, $transformed_text );
	}

	/**
	 * A single quote block containing a verse block and a citation should
	 * render as text surrounded with curly quotes, and the link breaks from
	 * the verse should be maintained.
	 */
	public function test_convert_single_quote_block_with_verse_to_quoted_text() {
		ob_start();
		?>
<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:verse -->
<pre class="wp-block-verse">So if the melody escapes me
I will stumble upon it soon
If it's not a rhapsody
Well it'll just;
Have to do -----</pre>
<!-- /wp:verse --><cite>Alejandro Escovedo, <em><a href="https://alejandroescovedo.bandcamp.com/track/rhapsody" data-type="URL" data-id="https://alejandroescovedo.bandcamp.com/track/rhapsody">Rhapsody</a></em> </cite></blockquote>
<!-- /wp:quote -->
		<?php
		$pre_html         = ob_get_clean();
		$expected_text    = "“So if the melody escapes me\nI will stumble upon it soon\nIf it's not a rhapsody\nWell it'll just;\nHave to do -----” - Alejandro Escovedo, Rhapsody https://alejandroescovedo.bandcamp.com/track/rhapsody";
		$transformed_text = \Shortnotes\ShareOnMastodon\transform_content( $pre_html );

		$this->assertEquals( $expected_text, $transformed_text );
	}

	/**
	 * An embedded video should transform to a link to the video.
	 */
	public function test_convert_single_video_embed_block_to_url() {
		ob_start();
		?>
<!-- wp:embed {"url":"https://www.youtube.com/watch?v=5NPBIwQyPWE","type":"video","providerNameSlug":"youtube","responsive":true,"className":"wp-embed-aspect-4-3 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-4-3 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://www.youtube.com/watch?v=5NPBIwQyPWE
</div></figure>
<!-- /wp:embed -->
		<?php
		$pre_html         = ob_get_clean();
		$expected_text    = 'https://www.youtube.com/watch?v=5NPBIwQyPWE';
		$transformed_text = \Shortnotes\ShareOnMastodon\transform_content( $pre_html );

		$this->assertEquals( $expected_text, $transformed_text );
	}
}
