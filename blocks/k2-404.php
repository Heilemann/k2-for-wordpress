<?php
/**
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */
?>

<div class="hentry four04">

	<div class="entry-header">
		<h3 class="center"><?php _e('Not Found', 'k2'); ?></h3>
	</div>

	<div class="entry-content">
		<p><?php _e('Oh no! You&#8216;re looking for something which just isn&#8216;t here! Fear not however, errors are to be expected. Lucky for you, there are tools in the sidebar for you to use in your search for what you need, or you can browse the most recent posts, listed below.', 'k2'); ?></p>

		<h4><?php _e('Most Recent Posts:', 'k2'); ?></h4>
		<ul>
			<?php wp_get_archives(array('type' => 'postbypost', 'limit' => '5')); ?>
		</ul>
	</div>

</div> <!-- .hentry .four04 -->
