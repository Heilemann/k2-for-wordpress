	<?php /* K2 Hook */ do_action('template_after_content'); ?>

	<div class="clear"></div>
</div> <!-- Close Page -->

<hr />

<?php /* K2 Hook */ do_action('template_before_footer'); ?>

<div id="footer">
	<?php /* K2 Hook */ do_action('template_footer'); ?>

	<p class="footerpoweredby">
		<?php
			printf( _c('Powered by %1$s and %2$s|1:WordPress, 2:K2','k2_domain'),
				sprintf('<a href="http://wordpress.org/">%1$s<span class="wp-version">%2$s</span></a>',
					__('WordPress','k2_domain'),
					get_bloginfo('version')
				),
				sprintf('<a href="http://getk2.com/" title="%1$s">K2<span class="k2-version">%2$s</span></a>',
					__('Loves you like a kitten.','k2_domain'),
					get_k2info('version')
				)
			);
		?>
	</p>

	<p class="footerfeedlinks">
		<?php
			printf( _c('%1$s and %2$s|1:Entries Feed, 2:Comments Feed','k2_domain'),
				'<a href="' . get_bloginfo('rss2_url') . '">' . __('Entries Feed','k2_domain') . '</a>',
				'<a href="' . get_bloginfo('comments_rss2_url') . '">' . __('Comments Feed','k2_domain') . '</a>'
			);
		?>
	</p>

	<p class="footerstats">
		<?php printf( __('%d queries. %.4f seconds.','k2_domain'), $wpdb->num_queries , timer_stop() ); ?>
	</p>
</div>

<?php wp_footer(); ?>

</body>
</html> 
