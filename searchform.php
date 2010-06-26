<?php
/**
 * The template for displaying the search form.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */
?>
<form method="get" id="searchform" action="<?php bloginfo('url'); ?>">
	<div id="search-form-wrap">
		<label for="s" id="search-label"><?php _e('Search for:', 'k2'); ?></label>
		<input type="text" id="s" name="s" value="<?php the_search_query(); ?>" accesskey="4" />
		<input type="submit" id="searchsubmit" value="<?php esc_attr_e('Search &raquo;', 'k2'); ?>" />
	</div>
</form>
