<?php
	// Get core WP functions if needed
	if (isset($_GET['k2dynamic'])) {
		require_once(dirname(__FILE__).'/../../../wp-config.php');

		$query = k2_parse_query($_GET);
		query_posts($query);

		$_GET['k2dynamic'] = 'init';
	}

	// Load Rolling Archives?
	if ( get_option('k2rollingarchives') == 1 ) { 

		// Parse the query
		//if ( is_array($wp_query->query) ) {
			//$rolling_query = http_build_query($wp_query->query);
		//} else {
			$rolling_query = $wp_query->query;
		//}

		// Get list of page dates
		if ( !is_page() and !is_single() ) {
			$page_dates = get_rolling_page_dates($wp_query);
		}

		// Get the current page
		$rolling_page = get_query_var('paged');
		if ( empty($rolling_page) ) {
			$rolling_page = 1;
		}
?>

<div id="rollingarchives" style="display:none;">
	<div id="texttrimmer">
		<div id="trimmertrackwrap"><div id="trimmertrack"><div id="trimmerhandle"></div></div></div>
		
		<div id="trimmerless"><span><?php _e('Less','k2_domain'); ?></span></div>
		<div id="trimmermore"><span><?php _e('More','k2_domain'); ?></span></div>
		<div id="trimmertrim"><span><?php _e('Trim','k2_domain'); ?></span></div>
		<div id="trimmeruntrim"><span><?php _e('Untrim','k2_domain'); ?></span></div>
	</div>

	<div id="rollnavigation">
		<div id="pagetrackwrap"><div id="pagetrack"><div id="pagehandle"><div id="rollhover"><div id="rolldates"></div></div></div></div></div>

		<div id="rollpages"></div>
		
		<a id="rollprevious" title="<?php _e('Older','k2_domain'); ?>" href="#">
			<span>&laquo;</span> <?php _e('Older','k2_domain'); ?>
		</a>
		<div id="rollhome" title="<?php _e('Home','k2_domain'); ?>">
			<span><?php _e('Home','k2_domain'); ?></span>
		</div>
		<div id="rollload" title="<?php _e('Loading','k2_domain'); ?>">
			<span><?php _e('Loading','k2_domain'); ?></span>
		</div>
		<a id="rollnext" title="<?php _e('Newer','k2_domain'); ?>" href="#">
			<?php _e('Newer','k2_domain'); ?> <span>&raquo;</span>
		</a>

	</div> <!-- #rollnavigation -->
</div> <!-- #rollingarchives -->

<?php if(!isset($_GET['k2dynamic'])) { ?>
<noscript>
	<?php include('navigation.php'); ?>
</noscript>
<?php } ?>

<?php if ( !isset($_GET['k2dynamic']) or ($_GET['k2dynamic'] == 'init') ) { ?>
<script type="text/javascript">
// <![CDATA[
	jQuery(document).ready(function() {
		k2Rolling.setup(
			"<?php output_javascript_url('theloop.php'); ?>",
			"<?php echo attribute_escape(__('Page %1$d of %2$d',k2_domain)); ?>",
			<?php echo $rolling_page; ?>,
			<?php echo $wp_query->max_num_pages; ?>,
			<?php output_javascript_hash($rolling_query); ?>,
			<?php output_javascript_array($page_dates); ?>
		);

		k2Trimmer.setup(100);
	});
// ]]>
</script>
<?php } } ?>

<div id="rollingcontent" class="hfeed">
	<?php include (TEMPLATEPATH . '/theloop.php'); ?>
</div><!-- #rollingcontent .hfeed -->
