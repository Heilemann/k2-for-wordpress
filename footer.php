	<br class="clear" />

</div> <!-- Close Page -->

<hr />

<p id="footer"><small>
	<?php printf(__('%1$s is powered by %2$s and %3$s','k2_domain'), get_bloginfo('name') , '<a href="http://wordpress.org/" title="Where children sing songs of binary bliss">WordPress ' . get_bloginfo('version') . '</a>', '<a href="http://getk2.com/" title="Loves you like a kitten.">K2<!--' . info::k2info('version') . '--></a>' ) ?><br /> 
	<?php if (function_exists('k2_style_info')) { info::k2_style_info(); } ?>
	<?php printf(__('<a href="%1$s">RSS Entries</a> and <a href="%2$s">RSS Comments</a>','k2_domain'), get_bloginfo('rss2_url'), get_bloginfo('comments_rss2_url')) ?>

	<!-- <?php printf(__('%d queries. %.4f seconds.','k2_domain'), $wpdb->num_queries , timer_stop()) ?> -->
</small></p>

<?php /* Try. to understand */ ?>

	<?php do_action('wp_footer'); ?>

</body>
</html> 
