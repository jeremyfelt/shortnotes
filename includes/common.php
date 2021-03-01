<?php

namespace ShortNotes\Common;

// Alpha.
// [x] Register a custom post type for notes.
// [x] Apply a very basic block template to the post type.
// [x] Auto-generate slugs that aren't auto-draft
// [x] Auto-generate date-sepcific titles
// [x] Update the title when the note's publish date is changed.
// [x] Make sure feeds work as expected
// [ ] Add webmention support to post type.
// [ ] Post notes to Twitter. (shortnotes-to-twitter plugin)
// [ ] Inject existing tweets as notes (shortnotes-from-twitter plugin)

/**
 * Retrieve the plugin version.
 *
 * @return string The plugin version.
 */
function get_version() {
	return '0.0.1';
}
