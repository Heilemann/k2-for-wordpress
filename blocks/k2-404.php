<div class="hentry four04">

	<div class="entry-header">
		<h3 class="center"><?php _e('Not Found', 'k2'); ?></h3>
	</div>

	<div class="entry-content">
		<p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected. Lucky for you, there are tools in the sidebar for you to use in your search for what you need, or you can browse the most recent posts, listed below.', 'k2'); ?></p>
		
		<h4><?php _e('Most Recent Posts:', 'k2'); ?></h4>
	    <ul>
	 <?php
	 $getposts = get_posts();
	 foreach( $getposts as $p ) : ?>
	    <li><a href="<?php echo $p->guid; ?>"><?php echo $p->post_title; ?></a></li>
	 <?php endforeach; ?>
	    </ul>
	
	</div>

</div><!-- .hentry .four04 -->
