<?php
	// Get core WP functions if needed
	if ( isset($_GET['k2dynamic']) ):

		// Check for CGI Mode
		if ( 'cgi' == substr( php_sapi_name(), 0, 3 ) ):
			require_once( preg_replace( '/wp-content.*/', '', __FILE__ ) . 'wp-config.php' );
		else:
			require_once( preg_replace( '/wp-content.*/', '', $_SERVER['SCRIPT_FILENAME'] ) . 'wp-config.php' );
		endif;

		// Send the header
		header('Content-Type: ' . get_bloginfo('html_type') . '; charset=' . get_bloginfo('charset'));

		// K2 Hook
		do_action('k2_dynamic_content');

		// Initialize the Loop
		query_posts( $_GET );

		$_GET['k2dynamic'] = 'init';
	endif;

	// Load Rolling Archives?
	if ( '1' == get_option('k2rollingarchives') ): 

		// Get the query
		if ( is_array($wp_query->query) ):
			$rolling_query = $wp_query->query;
		elseif ( is_string($wp_query->query) ):
			parse_str($wp_query->query, $rolling_query);
		endif;

		// Debugging
		if ( isset($_GET['k2debug']) ):
			$rolling_query['k2debug'] = '1';
		endif;

		// Get list of page dates
		if ( !is_page() and !is_single() ):
			$page_dates = get_rolling_page_dates($wp_query);
		endif;

		// Get the current page
		$rolling_page = intval( get_query_var('paged') );
		if ( $rolling_page < 1 ):
			$rolling_page = 1;
		endif;
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

<?php if ( !isset($_GET['k2dynamic']) ): ?>
<noscript>
	<?php k2_navigation('nav-above'); ?> 
</noscript>
<?php endif; ?>

<?php if ( !isset($_GET['k2dynamic']) or ('init' == $_GET['k2dynamic']) ): ?>
<script type="text/javascript">
// <![CDATA[
	jQuery(document).ready(function() {
		K2.RollingArchives.setState(
			<?php echo $rolling_page; ?>,
			<?php echo $wp_query->max_num_pages; ?>,
			<?php output_javascript_hash($rolling_query); ?>,
			<?php output_javascript_array($page_dates); ?>
		);

		smartPosition('#dynamic-content');
	});
// ]]>
</script>
<?php endif; endif; ?>

<div id="rollingcontent" class="hfeed">
	<?php include(TEMPLATEPATH . '/theloop.php'); ?>
</div><!-- #rollingcontent .hfeed -->
