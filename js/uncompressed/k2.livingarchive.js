/**
 *	The Living Archive: Parent module for Rolling Archives and Livesearch
 **/

function LivingArchive() {
	LivArc				= this;

	LivArc.NAME			= 'The Living Archive';
	LivArc.VERSION		= '1.0b';

	// Insert the Rolling Archives UI and init.
	K2.RollingArchives = new RollingArchives({
		content:    ".content",
		posts:      ".content .post",
		parent:     ".primary",
		pagetext:   "<?php /* translators: 1: current page, 2: total pages */ _e('of', 'k2'); ?>", // Page X of Y
		older:      "<?php _e('Older', 'k2'); ?>",
		newer:      "<?php _e('Newer', 'k2'); ?>",
		loading:    "<?php _e('Loading', 'k2'); ?>",
		offset:     50,
		pagenumber: <?php echo $rolling_state['curpage']; ?>,
		pagecount:  <?php echo $rolling_state['maxpage']; ?>,
		query:      <?php echo json_encode( $rolling_state['query'] ); ?>,
		pagedates:  <?php echo json_encode( $rolling_state['pagedates'] ); ?>,
		search:		"<?php esc_attr_e('Search','k2'); ?>"
	});

	// Setup Livesearch
	RA.LiveSearch			= new LiveSearch( RA.search || 'Search' );
}