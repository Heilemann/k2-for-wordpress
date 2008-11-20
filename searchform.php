<form method="get" id="searchform" action="<?php bloginfo('url'); ?>">
	<div id="search-form-wrap">
		<label for="s" id="search-label"><?php if ( get_option('k2livesearch') ) _e('Type and Wait to Search', 'k2_domain'); else _e('Search for:', 'k2_domain'); ?></label>
		<input type="text" id="s" name="s" value="<?php the_search_query(); ?>" accesskey="4" />

		<input type="submit" id="searchsubmit" value="<?php echo attribute_escape( __('Search', 'k2_domain') ); ?>" />

		<?php if ( get_option('k2livesearch') ): ?>
			<span id="searchreset" title="<?php echo attribute_escape( __('Reset Search', 'k2_domain') ); ?>"></span>
			<span id="searchload"></span>
		<?php endif ?>
	</div>
</form>
