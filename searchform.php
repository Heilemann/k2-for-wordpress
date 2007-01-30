<form method="get" id="searchform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" id="s" name="s" value="<?php the_search_query(); ?>" />
	<input type="submit" id="searchsubmit" value="<?php _e('go','k2_domain'); ?>" />
</form>
