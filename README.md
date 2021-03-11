# Shortnotes

Add a notes post type to WordPress. For your short notes.

## Description ##

Shortnotes adds a custom post type, "Notes", intended for use when publishing short pieces of content, similar to that found on Twitter, Instagram, and other social networks.

## No titles

The *Notes* post type does not support traditional titles.

A title **is** generated automatically from note content and is used as the note's document title. This is readable by search engines and displayed in browser tabs.

Note titles are **not** generally meant to be displayed as part of the theme layout. You will likely need to adjust the look and feel of your theme accordingly. See the Theme Customization section below for more details.

## Webmention support

The *Notes* post type includes support for the [Webmention](https://wordpress.org/plugins/webmention/) and [Semantic-Linkbacks](https://wordpress.org/plugins/semantic-linkbacks/) plugins.

Any URLs in the note's content will be processed as possible [webmentions](https://indieweb.org/webmention).

A panel in the block editor is available to assign a reply to URL and name that are then used to provide markup for a semantic [reply](https://indieweb.org/reply).

Reply to markup is automatically prepended to `the_content` **unless* the template tag included with this plugin has been used:

	if ( function_exists( 'ShortNotes\PostType\Note\reply_to_markup' ) ) {
		\ShortNotes\PostType\Note\reply_to_markup();
	}

The [reply](https://indieweb.org/reply) specification works best when the `u-in-reply-to` element is outside of the main content element.

## Theme Customization

No customization of your theme is needed to use this plugin, though it may be useful depending on how titles are displayed and if you want full support for webmentions.

If you do find yourself wanting to customize, I have made [adjustments to my site's theme](https://github.com/jeremyfelt/writemore/blob/0b344cc9613b1ed011cba13cb3c09376def596fc/template-parts/content/content-single.php#L16-L36), a child theme of Twenty Twenty One, while developing this plugin, that can be used as an example.

Those adjustments (a) remove the display of a title for the note post type and (b) output reply to markup outside of the main content element.
