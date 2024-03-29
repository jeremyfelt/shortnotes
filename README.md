# Shortnotes

Add a notes post type to WordPress. For your short notes.

## Description

Shortnotes adds a custom post type, **Notes**, intended for use when publishing short pieces of content, similar to that found on Twitter, Instagram, and other social networks.

### No titles

The **Notes** post type does not support traditional titles.

Note titles are **not** generally meant to be displayed as part of the theme layout. You will likely need to adjust the look and feel of your theme accordingly. See the Theme Customization section below for more details.

A title **is** generated automatically from note content and is used as the note's document title. This is readable by search engines and displayed in browser tabs.

### Limited blocks

The **Notes** post type uses only basic content blocks like paragraph, image, gallery, video, and embed. Using a defined list of relatively simple blocks helps to keep notes simple.

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
