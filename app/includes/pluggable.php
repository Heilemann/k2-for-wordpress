<?php


if ( !function_exists('k2_post_meta_head') ):
	function k2_post_meta_head($post) {

	printf(	__('<span class="meta-start">Published</span> %1$s %2$s %3$s<span class="meta-end">.</span>','k2_domain'),

		'<div class="entry-author">' .
		sprintf(  __('<span class="meta-prep">by</span> %s','k2_domain'),
			'<address class="vcard author"><a href="' . get_author_posts_url(get_the_author_ID()) .'" class="url fn" title="'. sprintf(__('View all posts by %s','k2_domain'), attribute_escape(get_the_author())) .'">' . get_the_author() . '</a></address>'
		) . '</div>',
		
		'<div class="entry-date">' .
		( function_exists('time_since') ?
				sprintf(__('%s ago','k2_domain'),
					'<abbr class="published" title="' . get_the_time('Y-m-d\TH:i:sO') . '">' . time_since(abs(strtotime($post->post_date_gmt . " GMT")), time()) . '</abbr>') :
				sprintf(__('<span class="meta-prep">on</span> %s','k2_domain'),
					'<abbr class="published" title="' . get_the_time('Y-m-d\TH:i:sO') . '">'. get_the_time( get_option('date_format') ) . '</abbr>')
		) . '</div>',

		'<div class="entry-categories">' .
		sprintf( __('<span class="meta-prep">in</span> %s','k2_domain'),
			k2_nice_category(', ', __(' and ','k2_domain'))
		) . '</div>'
	);
?>

<?php /* Comments */ comments_popup_link('0&nbsp;<span>'.__('Comments','k2_domain').'</span>', '1&nbsp;<span>'.__('Comment','k2_domain').'</span>', '%&nbsp;<span>'.__('Comments','k2_domain').'</span>', 'commentslink', '<span>'.__('Closed','k2_domain').'</span>'); ?>

<?php /* Edit Link */ edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">','</span>'); ?>

<?php /* Tags */ if (is_single() and function_exists('UTW_ShowTagsForCurrentPost')) { ?>
	<span class="entry-tags"><?php _e('Tags: ','k2_domain'); ?><?php UTW_ShowTagsForCurrentPost("commalist"); ?>.</span>
<?php } elseif (is_single() and function_exists('the_tags')) { ?>
	<span class="entry-tags"><?php the_tags(__('Tags: ','k2_domain'), ', ', '.'); ?></span>
<?php }

	}
endif;



if ( !function_exists('k2_post_meta_foot') ):
	function k2_post_meta_foot($post) {
	}
endif;