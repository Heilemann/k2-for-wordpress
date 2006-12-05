<?php
	$prefix = '';

	// Get Core WP Functions If Needed
	if (isset($_GET['rolling'])) {
		require (dirname(__FILE__)."/../../../wp-blog-header.php");
		$prefix = 'nested_';
	}
?>
<?php
	// Load Rolling Archives?
	if ( (get_option('k2rollingarchives') == 1) ) { 
		$k2pagecount = k2countpages($wp_query->request);

		if ($k2pagecount > 1) {
?>
	<div id="<?php echo $prefix; ?>rollingarchives">
		<div id="<?php echo $prefix; ?>rollnavigation">
			<a href="#" id="<?php echo $prefix; ?>rollprevious"><span>&laquo;</span> <?php _e('Older','k2_domain'); ?></a>
			<a href="#" id="<?php echo $prefix; ?>rollhome"><img src="<?php bloginfo('template_directory'); ?>/images/house.png" alt="Home" /></a>

			<div id="<?php echo $prefix; ?>pagetrack"><div id="<?php echo $prefix; ?>pagetrackend"><div id="<?php echo $prefix; ?>pagehandle"></div></div></div>

			<span id="<?php echo $prefix; ?>rollload"><?php _e('Loading','k2_domain'); ?></span>
			<span id="<?php echo $prefix; ?>rollpages"></span>

			<a href="#" id="<?php echo $prefix; ?>rollnext"><?php _e('Newer','k2_domain'); ?> <span>&raquo;</span></a>

			<div id="<?php echo $prefix; ?>texttrimmer">
				<div id="trimmer"></div>

				<a href="javascript:MyTrimmer.doTrim(40); MyTrimmer.curValue = 40; $('trimmerExcerpts').style.display = 'none'; $('trimmerHeadlines').style.display = 'block';" id="trimmerExcerpts"><?php _e('Excerpts','k2_domain'); ?></a>
				<a href="javascript:MyTrimmer.doTrim(0); MyTrimmer.curValue = 0; MyTrimmer.doZebra(); $('trimmerHeadlines').style.display = 'none'; $('trimmerFulllength').style.display = 'block';" id="trimmerHeadlines" style="display: none;"><?php _e('Headlines','k2_domain'); ?></a>
				<a href="javascript:MyTrimmer.doTrim(100); MyTrimmer.curValue = 100; MyTrimmer.undoZebra(); $('trimmerFulllength').style.display = 'none'; new Effect.BlindDown('trimmerExcerpts').style.display = 'block';" id="trimmerFulllength" style="display: none;"><?php _e('Full Length','k2_domain'); ?></a>
			</div>
		</div>

		<div id="<?php echo $prefix; ?>rollnotices"></div>


	</div>
	<script type="text/javascript">
	// <![CDATA[
		var <?php echo $prefix; ?>rolling = new RollingArchives("<?php echo $prefix; ?>primarycontent", <?php k2info('js_url'); ?> + '/theloop.php', "<?php echo $wp_query->query; ?>", <?php echo $k2pagecount; ?>, "<?php echo $prefix; ?>", "<?php _e('Page %1$d of %2$d',k2_domain); ?>");
		var MyTrimmer = new TextTrimmer("trimmer", "entry-content", 0, 100);
	// ]]>
	</script>

<?php } } ?>
<div id="<?php echo $prefix; ?>primarycontent" class="hfeed">
	<?php include (TEMPLATEPATH . '/theloop.php'); ?>
</div><!-- #<?php echo $prefix; ?>primarycontent .hfeed -->
