<?php
/**
 * Header Template
 *
 * This file is loaded by header.php and used for content inside the #header div
 *
 * @package K2
 * @subpackage Templates
 */

// For SEO, outputs the blog title in h1 or a div
$block = ( is_front_page() ? 'h1' : 'div' );

// arguments for wp_list_pages
$list_args = k2_get_page_list_args(); // this function is pluggable

?>

<?php echo "<$block class='blog-title'>"; ?>
	<a href="<?php echo get_option('home'); ?>/" accesskey="1"><?php bloginfo('name'); ?></a>
<?php echo "</$block>"; ?>

<p class="description"><?php bloginfo('description'); ?></p>

<ul class="menu">
	<li class="<?php if ( is_front_page() && !is_paged() ): ?>current_page_item<?php else: ?>page_item<?php endif; ?> blogtab">
		<a href="<?php echo get_option('home'); ?>/" title="<?php echo esc_attr( get_option('k2blogornoblog') ); ?>">
			<?php echo get_option('k2blogornoblog'); ?>
		</a>
	</li>

	<?php /* K2 Hook - do not remove */ do_action('template_header_menu'); ?>

	<?php
		// List pages
		wp_list_pages( $list_args );
	?>

	<?php
		// Display an Register tab if registration is enabled or an Admin tab if user is logged in
		wp_register('<li class="admintab">','</li>');
	?>
</ul><!-- .menu -->

