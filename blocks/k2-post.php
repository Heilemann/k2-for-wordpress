<header class="entry-header">
	<h1 class="entry-title">
		<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php k2_permalink_title(); ?>"><?php the_title(); ?></a>
	</h1>
	<?php /* Edit Link */ edit_post_link( __('Edit', 'k2'), '<span class="entry-edit">', '</span>' ); ?>
	<?php k2_entry_meta('standard-above'); ?>
	<?php /* K2 Hook */ do_action('template_entry_head'); ?>
</header><!-- .entry-header -->

<div class="entry-content">
	<?php if ( has_post_thumbnail() ): ?>
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( array( 75, 75 ), array( 'class' => 'alignleft' ) ); ?></a>
	<?php endif; ?>
	<?php the_content( sprintf( __('Continue reading &#8216;%s&#8217;', 'k2'), get_the_title() ) ); ?>
</div><!-- .entry-content -->

<footer class="entry-footer">
	<?php wp_link_pages( array('before' => '<div class="entry-pages"><span>' . __('Pages:', 'k2') . '</span>', 'after' => '</div>' ) ); ?>
	<?php k2_entry_meta('standard-below'); ?>
	<?php /* K2 Hook */ do_action('template_entry_foot'); ?>
</footer><!-- .entry-footer -->
