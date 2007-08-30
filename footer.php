	<div class="clear"></div>

</div> <!-- Close Page -->

<hr />

<div id="footer">
	<p><?php printf( __('%1$s is powered by %2$s and %3$s','k2_domain'),
				get_bloginfo('name'),
				sprintf('<a href="http://wordpress.org/" title="%1$s">%2$s %3$s</a>',
					__('Where children sing songs of binary bliss','k2_domain'),
					__('WordPress','k2_domain'),
					get_bloginfo('version')
				),
				sprintf('<a href="http://getk2.com/" title="%1$s">K2<!--%2$s--></a>',
					__('Loves you like a kitten.','k2_domain'),
					get_k2info('version')
				)
			); ?></p>
	<?php if (function_exists('k2_style_info')) { k2_style_info(); } ?>
	<p><?php printf(__('<a href="%1$s">RSS Entries</a> and <a href="%2$s">RSS Comments</a>','k2_domain'), get_bloginfo('rss2_url'), get_bloginfo('comments_rss2_url')) ?></p>
	<!-- <?php printf(__('%d queries. %.4f seconds.','k2_domain'), $wpdb->num_queries , timer_stop()) ?> -->
</div>

<?php wp_footer(); ?>

</body>
</html> 
