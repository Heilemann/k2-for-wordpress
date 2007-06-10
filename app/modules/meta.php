<?php

function meta_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);
	?>
	<ul>
		<?php wp_register(); ?>
		<li><?php wp_loginout(); ?></li>
		<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS 2.0'); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
		<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS'); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
		<li><a href="http://wordpress.org/" title="<?php _e('Powered by Wordpress, state-of-the-art semantic personal publishing platform.'); ?>">WordPress</a></li>
		<?php wp_meta(); ?>
	</ul>
	<?php
	echo($after_module);
}

register_sidebar_module('Meta Information', 'meta_sidebar_module', 'sb-meta');

?>
