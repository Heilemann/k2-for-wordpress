<?php if (!is_search()) {
		$search_text = __('search blog archives','k2_domain');
	} else {
		$search_text = "$s";
	}
?>

<form method="get" id="searchform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" id="s" name="s" value="<?php echo wp_specialchars($search_text, 1); ?>" />
	<input type="submit" id="searchsubmit" value="<?php _e('go','k2_domain'); ?>" />
</form>


