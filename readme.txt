# Shortnotes
Contributors: jeremyfelt
Tags: indieweb, notes, replies, short
Requires at least: 5.6
Tested up to: 6.4
Stable tag: 1.6.2
License: GPLv2 or Later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 5.6

Add a notes post type to WordPress. For your short notes.

## Description

Shortnotes adds a custom post type, **Notes**, used to publish short pieces of content on your website, similar to social networks like Mastodon, Twitter, or Instagram.

### No titles

The **Notes** post type does not support traditional titles.

Note titles are **not** generally meant to be displayed as part of the theme layout. You will likely need to adjust the look and feel of your theme accordingly. See the Theme Customization section below for more details.

A title **is** generated automatically from note content and is used as the note's document title. This is readable by search engines and displayed in browser tabs.

### Limited blocks

The **Notes** post type supports basic content blocks like paragraph, list, quote, image, preformatted text, gallery, video, and embed. Using a defined list of relatively simple blocks helps to keep notes simple.

### Webmention support

The **Notes** post type includes support for the [Webmention](https://wordpress.org/plugins/webmention/) and [Semantic-Linkbacks](https://wordpress.org/plugins/semantic-linkbacks/) plugins.

URLs in note content are processed as possible [webmentions](https://indieweb.org/webmention).

A panel in the block editor allows for the addition of a reply to URL and name. When entered, they are used to provide markup for a semantic webmention [reply](https://indieweb.org/reply).

#### Reply to template tag

The [reply](https://indieweb.org/reply) specification works best when the `u-in-reply-to` element is outside of the [main content element](http://microformats.org/wiki/h-entry#Properties), defined by `e-content`.

A template tag is provided as part of the Shortnotes plugin that can be used to output reply to markup in your theme.

	<article class="h-entry">
		<?php
		if ( function_exists( 'ShortNotes\PostType\Note\reply_to_markup' ) ) {
			\ShortNotes\PostType\Note\reply_to_markup();
		}
		?>
		<div class="entry-content e-content">
			<?php the_content(); ?>
		</div><!-- .entry-content -->
	</article>

If this template tag is **not** used, then the Shortnotes plugin will automatically prepend the reply to markup to `the_content`.

### Theme Customization

No customization of your theme is required to use this plugin, though you will likely want to think through how titles are displayed and if you want full support for webmentions.

If you do find yourself wanting to customize, I have made [adjustments to my site's theme](https://github.com/jeremyfelt/writemore/blob/0b344cc9613b1ed011cba13cb3c09376def596fc/template-parts/content/content-single.php#L16-L36), a child theme of Twenty Twenty One, while developing this plugin, that can be used as an example.

Those adjustments (a) remove the display of a title for the note post type and (b) output reply to markup outside of the main content element.

## Changelog

### 1.6.2

* Fix an issue when prepending reply markup to content during an oembed request. Thanks [jorbin](https://profiles.wordpress.org/jorbin/)!
* Update `@wordpress/scripts` dependency to 26.15.0.
* Add phpstan configuration.
* Remove old, unnecessary pre-PHP 5.6 notice.
* Confirm upcoming WordPress 6.4 support.

### 1.6.1

* Remove `vendor/` directory from distribution.

### 1.6.0

* Improve title generation when a note starts with a quote.
* Improve text formatting of notes posted to Mastodon through [Share on Mastodon](https://wordpress.org/plugins/share-on-mastodon/):
  * Avoid duplicate dashes when a note with a quote is transformed for Mastodon.
  * Remove leading and trailing double quotes of all flavors when a note with a quote is transformed for Mastodon.
  * Ensure persisting line breaks for preformatted blocks with `<br>` tags.
  * Improve handling of lists and list items when transforming content for Mastodon.
* Update `@wordpress/scripts` dependency to 26.8.0.

### 1.5.0

* Improve text formatting of notes posted to Mastodon through [Share on Mastodon](https://wordpress.org/plugins/share-on-mastodon/):
  * Transform quote block to a text representation of the quote and citation.
  * Transform embed blocks to a raw URL.
  * Improve transformation of paragraph blocks.
  * Improve extraction of anchor hrefs from markup.
* Properly reply to a previous note's corresponding Mastodon post.
* Remove hacky filtering of Share on Mastodon.
  * This is technically a back-compat break for Share on Mastodon <0.9.
* Use the quote block to generate a note title, if available.
* Update `@wordpress/scripts` dependency to 25.5.1.

### 1.4.0

* Integrate with [Share on Mastodon](https://wordpress.org/plugins/share-on-mastodon/) plugin to send notes to Mastodon:
  * Full note text is sent to Mastodon with stripped HTML.
  * If a repy to URL is a Mastodon post, it will appear as a reply in Mastodon.
* Fix an issue where titles did not generate as expected on scheduled notes.
* Update `@wordpress/scripts` dependency to 24.6.0.
* Confirm WordPress 6.1 support.

### 1.3.1

* Update `@wordpress/scripts` dependency to 23.2.0.
* Confirm WordPress 6.0 support.

### 1.3.0

* Add a `shortnotes_post_type_arguments` filter.
* Fix a minor documentation issue.
* Update `@wordpress/scripts` dependency to 23.0.0.

### 1.2.0

* Fix a bug in title generation when an image block is followed by a paragraph.
* Start tracking note type: note or reply.
* Improve reply-to interface to appear only when the note type is a reply.
* Update `@wordpress/scripts` dependency to 22.3.0.

### 1.1.5

* Introduce common function to generate a shortnote's subtitle.
* Parse non-Gutenberg HTML blocks for possible sub title content.

### 1.1.4

* Add `shortnotes_formatted_title` filter to allow for additional title filtering elsewhere.

### 1.1.3

* Stop editor from crashing when a note is saved in Gutenberg 11.4.0+.

### 1.1.2

* Add `shortnotes_reply_to_name` filter.
* Update `@wordpress/scripts` dependency to 19.2.2.
* Rebuild JavaScript asset with latest WP scripts.

### 1.1.1

* Confirm support for WordPress 5.8.
* Update `@wordpress/scripts` dependency to 17.0.0.
* Rebuild JavaScript asset with latest WP scripts.
* Remove unused `get_version()` function before it's too late!

### 1.1.0

* Add support for more simple core blocks: video, file, embed, etc...
* Fix overeager loading of plugin assets on post types that are not shortnote.

### 1.0.2

* Fix display of "(no title) is now live." in block editor when new note is published.
