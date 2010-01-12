<?php
/**
 * Deprecated functions from past K2 versions. You shouldn't use these
 * globals and functions and look for the alternatives instead. The functions
 * and globals will be removed in a later version.
 *
 * @package K2
 * @subpackage Deprecated
 */

/**
 * Semantic body classes
 *
 * @deprecated Replace <body class="<?php k2_body_class(); ?>"> with <body <?php body_class(); ?>>
 * @see get_body_class()
 */
function k2_body_class( $print = true ) {
	$c = join( ' ', get_body_class() );

	return $print ? print($c) : $c;
}

/**
 * Semantic post classes
 *
 * @deprecated Replace class="<?php k2_post_class(); ?>" with <?php post_class(); ?>
 * @see get_post_class()
 */
function k2_post_class( $post_count = 1, $post_asides = false, $print = true ) {
	$c = join( ' ', get_post_class() );

	return $print ? print($c) : $c;
}