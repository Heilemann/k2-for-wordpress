<?php
/**
 * Deprecated functions from past K2 versions. You shouldn't use these
 * globals and functions and look for the alternatives instead. The functions
 * and globals will be removed in a later version.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
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
function k2_post_class( $post_count = 1, $print = true ) {
	$c = join( ' ', get_post_class() );

	return $print ? print($c) : $c;
}

// Generate JavaScript array from an array
function output_javascript_array($array, $print = true) {
	$output = '[';

	if ( is_array($array) and !empty($array) ) {
		array_walk($array, 'js_format_array');
		$output .= implode(', ', $array);
	}

	$output .= ']';

	return $print ? print($output) : $output;
}


// Generate JavaScript hash from an associated array
function output_javascript_hash($array, $print = true) {
	$output = '{';

	if ( is_array($array) and !empty($array) ) {
		array_walk($array, 'js_format_hash');
		$output .= implode(', ', $array);
	}

	$output .= '}';

	return $print ? print($output) : $output;
}

function js_format_array(&$item, $key) {
	$item = js_value($item);
}

function js_format_hash(&$item, $key) {
	$item = '"' . esc_js($key) . '": ' . js_value($item);
}

function js_value($value) {
	if ( is_string($value) )
		return '"' . esc_js($value) . '"';

	if ( is_bool($value) )
      return $value ? 'true' : 'false';

	if ( is_numeric($value) )
		return $value;

	if ( empty($value) )
		return '0';

	return '""';
}

