<div class="entry-content">
	<?php if ( function_exists('has_post_thumbnail') and has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( array( 75, 75 ), array( 'class' => 'alignleft' ) ); ?></a>
	<?php endif; ?>
	<?php the_content( sprintf( __('Continue reading &#8216;%s&#8217;', 'k2'), get_the_title() ) ); ?>
</div><!-- .entry-content -->

<footer class="entry-footer">
	<div class="entry-meta">
		<?php printf( __( '<span class="meta-prep">Published</span> <span class="entry-author"><span class="meta-prep">by</span> %1$s</span> <span class="entry-date"><span class="meta-prep">on</span> %2$s</span>', 'k2' ),
			sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
				get_author_posts_url( get_the_author_meta( 'ID' ) ),
				sprintf( esc_attr__( 'View all posts by %s', 'k2' ), get_the_author() ),
				get_the_author()
			),
			sprintf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
				get_permalink(),
				esc_attr( get_the_time() ),
				get_the_date()
			)
		);
		?>
		<span class="meta-sep">|</span>
		<span class="entry-comments"><?php comments_popup_link( __( 'Leave a comment', 'k2' ), __( '1 <span>Comment</span>', 'k2' ), __( '% <span>Comments</span>', 'k2' ) ); ?></span>
	</div>

	<?php /* K2 Hook */ do_action('template_entry_foot'); ?>
</footer><!-- .entry-footer -->
