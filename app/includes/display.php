<?php
	// Prevent users from directly loading this class file
	defined( 'K2_CURRENT' ) or die ( 'Error: This file can not be loaded directly.' );

	function k2_navigation($id = 'nav-above') {
	?>

		<div id="<?php echo $id; ?>" class="navigation">

		<?php if ( is_single() ): ?>
			<div class="nav-previous"><?php previous_post_link('%link', '<span class="meta-nav">&laquo;</span> %title') ?></div>
			<div class="nav-next"><?php next_post_link('%link', '%title <span class="meta-nav">&raquo;</span>') ?></div>
		<?php else: ?>
			<?php $_SERVER['REQUEST_URI']  = preg_replace("/(.*?).php(.*?)&(.*?)&(.*?)&_=/","$2$3",$_SERVER['REQUEST_URI']); ?>
			<div class="nav-previous"><?php next_posts_link( '<span class="meta-nav">&laquo;</span> ' . __('Older Entries','k2_domain') ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __('Newer Entries','k2_domain').' <span class="meta-nav">&raquo;</span>' ); ?></div>
		<?php endif; ?>

			<div class="clear"></div>
		</div>

<?php
	}

	function k2_header_menu() {
	?>

	<ul class="menu">
		<?php if ( get_option('show_on_front') != 'page' ): ?>
		<li class="<?php if (
							is_home()
							or is_archive()
							or is_single()
							or is_paged()
							or is_search()
							or ( function_exists('is_tag') and is_tag() )
						): ?>current_page_item<?php else: ?>page_item<?php endif; ?>"><a href="<?php echo get_option('home'); ?>/" title="<?php echo get_option('k2blogornoblog'); ?>"><?php echo get_option('k2blogornoblog'); ?></a></li>
		<?php endif; ?>

		<?php /* K2 Hook */ do_action('template_header_menu'); ?>

		<?php wp_list_pages( apply_filters('k2_menu_list_pages', 'sort_column=menu_order&depth=1&title_li=') ); ?>

		<?php wp_register('<li class="admintab">','</li>'); ?>
	</ul> <!-- .menu -->

<?php
	}

	add_action('template_header', 'k2_header_menu');

	function k2_style_footer() {
		if ( get_k2info('style_footer') != '' ):
		?>
		<p class="footerstyledwith">
			<?php k2info('style_footer'); ?>
		</p>
<?php
		endif;
	}

	add_action('template_footer', 'k2_style_footer');

?>