<?php
/**
 * Temporary stubs for phpstan.
 *
 * @package shortnotes
 */

/**
 * As of WordPress 6.8, the return type of `parse_blocks()` includes a list of
 * arrays in which `blockName` is only a `string` and not a `string|null`.
 *
 * @see https://core.trac.wordpress.org/ticket/63663
 *
 * @return list<array{blockName: string|null, attrs: mixed[], innerBlocks: mixed[], innerHTML: string, innerContent: mixed[]}>
 */
function parse_blocks( string $content ): array {}
