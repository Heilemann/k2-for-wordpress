<?php
/**
 * Default Loop Template
 *
 * This file is loaded by multiple files and used for generating the loop
 *
 * @package K2
 * @subpackage Templates
 */

// Post index for semantic classes
$post_index = 1;

while ( have_posts() ): the_post(); ?>

	<div id="entry-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php get_template_part('part-entry' , function_exists('get_post_format') ? get_post_format( $post->ID ) : '' ); ?>
	</div><!-- #entry-ID -->

<?php endwhile; /* End The Loop */ ?>
