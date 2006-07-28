<?php

	// Get Core WP Functions If Needed
	if (isset($_GET['s'])) { require (dirname(__FILE__)."/../../../wp-blog-header.php"); }

	// Load Rolling Archives?
	if (get_option('k2rollingarchives') == 1) { 

	//Need PHP to construct proper query string based on incoming variables.

	$k2countpages = k2countsearchpages('s='.$s);
?>

	<div id="rollingarchives">
		<div id="rollnavigation">
			<a href="#" id="rollprevious"><span>&laquo;</span> <?php _e('Older','k2_domain'); ?></a>
			<a href="#" id="rollhome"><img src="<?php bloginfo('template_directory'); ?>/images/house.png" alt="Home" /></a>

			<div id="pagetrack"><div id="pagetrackend"><div id="pagehandle"></div></div></div>

			<script type="text/javascript">
			// <![CDATA[
				var pagecount = '<?php echo $k2countpages; ?>';
				var currentpage = '<?php if (isset($_GET[paged])) { echo $_GET[paged]; } else { echo '1'; } ?>';
				
			    var PageSlider = new Control.Slider('pagehandle','pagetrack', {
					sliderValue: <?php if (isset($_GET[paged])) { echo $_GET[paged]; } else { echo '1'; } ?>,
					range: $R(<?php echo $k2countpages; ?>, 1),
					values: [<?php for ($i = 1; $i < $k2countpages; $i++) { echo $i.", "; }; echo $i; ?>],
					onSlide: function(v) { $('rollpages').innerHTML = 'Page '+v+' of '+<?php echo $k2countpages; ?> },
					onChange: function(v) { rollGotoPage(v, '<?php echo $s; ?>') },
					handleImage: 'pagehandle',
					handleDisabled: 'images/sliderbgright.png'
				});
			// ]]>
			</script>

			<span id="rollload"><?php _e('Loading','k2_domain'); ?></span>
			<span id="rollpages"></span>

			<a href="#" id="rollnext"><?php _e('Newer','k2_domain'); ?> <span>&raquo;</span></a>
		</div>

		<div id="rollnotices"></div>
	</div>

	<script type="text/javascript">
		initRollingArchives(currentpage, <?php echo $k2countpages; ?>);

		<?php if ($k2countpages < 2) { ?>
			disableRollingArchives();
		<?php } ?>
	</script>

<?php } ?>

<div id="content">
	<?php include (TEMPLATEPATH . '/theloop.php'); ?>
</div>
