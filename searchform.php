<form method="get" id="searchform" action="<?php bloginfo('url'); ?>">
	<input type="text" id="s" name="s" value="<?php the_search_query(); ?>" />
	<input type="submit" id="searchsubmit" value="<?php echo attribute_escape(__('go','k2_domain')); ?>" />
</form>
