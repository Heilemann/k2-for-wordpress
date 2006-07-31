<?php

	// Get Core WP Functions If Needed
	if (isset($_GET['rolling'])) {
		require (dirname(__FILE__)."/../../../wp-blog-header.php");
	}

	// Load Rolling Archives?
	if ( (get_option('k2rollingarchives') == 1) ) { 

	//Need PHP to construct proper query string based on incoming variables.

	$k2pagecount = k2countpages($wp_query->query);

	if ($k2pagecount > 1) {
?>
	<div id="rollingarchives">
		<div id="rollnavigation">
			<a href="#" id="rollprevious"><span>&laquo;</span> <?php _e('Older','k2_domain'); ?></a>
			<a href="#" id="rollhome"><img src="<?php bloginfo('template_directory'); ?>/images/house.png" alt="Home" /></a>

			<div id="pagetrack"><div id="pagetrackend"><div id="pagehandle"></div></div></div>

			<span id="rollload"><?php _e('Loading','k2_domain'); ?></span>
			<span id="rollpages"></span>

			<a href="#" id="rollnext"><?php _e('Newer','k2_domain'); ?> <span>&raquo;</span></a>
		</div>

		<div id="rollnotices"></div>
	</div>
	<script type="text/javascript">
	// <![CDATA[
		var rolling = new RollingArchives('content', '<?php echo get_bloginfo('template_url').'/theloop.php'; ?>', '<?php echo $wp_query->query; ?>', <?php echo $k2pagecount; ?>);
	// ]]>
	</script>

<?php } } ?>

<div id="content">
	<?php include (TEMPLATEPATH . '/theloop.php'); ?>
</div>