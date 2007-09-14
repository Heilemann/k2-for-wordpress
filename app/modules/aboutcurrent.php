<?php

function about_sidebar_module($args) {
	global $s;

	extract($args);

	$k2about = sbm_get_option('aboutblurp');

	if ( !( (is_home() and !is_paged() and $k2about == '') or is_single() or is_page() ) ) {
		echo($before_module . $before_title . $title . $after_title);
?>
		<?php /* Frontpage */ if (is_home() and !is_paged() and $k2about != '') { ?>
		<p><?php echo stripslashes($k2about); ?></p>

		<?php /* Category Archive */ } elseif (is_category()) { ?>
		<p><?php printf(__('The %1$s archives for the %2$s category.','k2_domain'), '<a href="' . get_settings('siteurl') .'">' . get_bloginfo('name') . '</a>', single_cat_title('', false) ); ?></p>

		<?php /* Day Archive */ } elseif (is_day()) { ?>
		<p><?php printf(__('The %1$s archives for %2$s.','k2_domain'), '<a href="' . get_settings('siteurl') .'">' . get_bloginfo('name') . '</a>', get_the_time(__('l, F jS, Y','k2_domain'))); ?></p>

		<?php /* Monthly Archive */ } elseif (is_month()) { ?>
		<p><?php printf(__('The %1$s archives for %2$s.','k2_domain'), '<a href="'.get_settings('siteurl').'">'.get_bloginfo('name').'</a>', get_the_time(__('F, Y','k2_domain'))); ?></p>

		<?php /* Yearly Archive */ } elseif (is_year()) { ?>
		<p><?php printf(__('The %1$s archives for %2$s.','k2_domain'), '<a href="'.get_settings('siteurl').'">'.get_bloginfo('name').'</a>', get_the_time('Y')); ?></p>

		<?php /* Search */ } elseif (is_search()) { ?>
		<p><?php printf(__('You searched the %1$s archives for \'<strong>%2$s</strong>\'.','k2_domain'),'<a href="'.get_settings('siteurl').'">'.get_bloginfo('name').'</a>', wp_specialchars(stripslashes($s), true)); ?></p>

		<?php /* Author Archive */ } elseif (is_author()) { ?>
		<p><?php printf(__('Archive for <strong>%s</strong>.','k2_domain'), get_the_author()); ?></p>
		<p><?php the_author_description(); ?></p>

		<?php /* Tag Archive */ } elseif (function_exists('is_tag') and is_tag()) { ?>
		<p><?php printf(__('The %1$s archives for the \'%2$s\' tag.','k2_domain'), '<a href="'.get_settings('siteurl').'">'.get_bloginfo('name').'</a>', get_query_var('tag') ); ?></p>

		<?php /* Paged Archive */ } elseif (is_paged()) { ?>
		<p><?php printf(__('The %s weblog archives.','k2_domain'), '<a href="'.get_settings('siteurl').'">'.get_bloginfo('name').'</a>'); ?></p>

		<?php } ?>
<?php
		echo($after_module);
	}
}

function about_sidebar_module_control() {
	if (isset($_POST['about_module_blurp'])) {
		sbm_update_option('aboutblurp', $_POST['about_module_blurp']);
	}
	?>
		<p>
			<label for="about-module-blurp"><?php _e('About Text:', 'k2_domain'); ?></label><br />
			<textarea id="about-module-blurp" name="about_module_blurp" rows="6" cols="30"><?php echo(wp_specialchars(htmlspecialchars(stripslashes(sbm_get_option('aboutblurp')), ENT_QUOTES), 1)); ?></textarea><br />
			<small><?php _e('Enter a blurp about yourself here, and it will show up on the frontpage. Deleting the content disables the about blurp.','k2_domain'); ?></small>
		</p>
	<?php
}

register_sidebar_module('About Current Page', 'about_sidebar_module', 'sb-about', array('aboutblurp' => ''));
register_sidebar_module_control('About Current Page', 'about_sidebar_module_control');

?>
