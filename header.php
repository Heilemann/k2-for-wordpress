<?php
	// Prevent users from directly loading this theme file
	defined( 'K2_CURRENT' ) or die ( 'Error: This file can not be loaded directly.' );

	// Load localizatons
	load_theme_textdomain('k2_domain');

	// Load our scripts
	wp_enqueue_script('k2functions');

	if (get_option('k2rollingarchives') == 1) {
		wp_enqueue_script('k2rollingarchives');
	}

	if (get_option('k2livesearch') == 1) {
		wp_enqueue_script('k2livesearch');
	}

	if ((get_option('k2livecommenting') == 1) and ((is_page() or is_single()) and (!isset($_GET['jal_edit_comments'])) and ('open' == $post-> comment_status) or ('comment' == $post-> comment_type) )) {
		wp_enqueue_script('k2comments');
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
	<title><?php
	
	// Page or Single Post
	if ( ( is_page() and !is_front_page() and !is_home() ) or is_single() ) {
		the_title();

	// Category Archive
	} elseif ( is_category() ) {
		printf( __('Category Archive for &lsquo;%s&rsquo;','k2_domain'), single_cat_title('', false) );

	// Tag Archive
	} elseif ( function_exists('is_tag') and function_exists('single_tag_title') and is_tag() ) {
		printf( __('Tag Archive for &lsquo;%s&rsquo;','k2_domain'), single_tag_title('', false) );

	// General Archive
	} elseif ( is_archive() ) {
		printf( __('%s Archive','k2_domain'), wp_title('', false) );

	// Search Results
	} elseif ( is_search() ) {
		printf( __('Search Results for &lsquo;%s&rsquo;','k2_domain'), attribute_escape(get_search_query()) );
	}

	// Insert separator for the titles above
	if ( !is_front_page() and !is_home() and !is_404() ) {
		_e(' at ','k2_domain');
	}
	
	// Finally the blog name
	bloginfo('name');

	?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
	<meta name="template" content="K2 <?php k2info('version'); ?>" />
 	<meta name="description" content="<?php bloginfo('description'); ?>" />
  
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo get_template_directory_uri(); ?>/style.css" />

	<?php if ( ! K2_USING_STYLES ): /* WP Theme Stylesheet */ ?>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo get_stylesheet_uri(); ?>" />
	<?php elseif ( get_option('k2style') != '' ): /* K2 Styles */ ?>
	<link rel="stylesheet" type="text/css" href="<?php k2info('style'); ?>" />
	<?php endif; ?>

	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />

	<?php if ( is_single() or is_page() ): ?>
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php endif; ?>

	<?php wp_head(); ?>

	<script type="text/javascript">
	//<![CDATA[
	<?php if ( 'dynamic' == get_option('k2columns') ): ?>
		K2.layoutWidths = <?php /* Style Layout Widths */
			if ( K2_USING_STYLES ) {
				$styleinfo = get_option('k2styleinfo');
				if ( empty($styleinfo['layout_widths']) )
					echo '[560, 780, 950]';
				else
					output_javascript_array($styleinfo['layout_widths']);
			} else {
				echo '[560, 780, 950]';
			}
		?>;

		jQuery(window).resize(dynamicColumns);
	<?php endif; ?>

		<?php /* Debugging */ if ( isset($_GET['k2debug']) ): ?>
		K2.debug = true;
		<?php endif; ?>

		jQuery(document).ready(function(){
			<?php /* LiveSearch */ if ( '1' == get_option('k2livesearch') ): ?>
			K2.LiveSearch = new LiveSearch(
				"<?php if (get_option('k2rollingarchives') == 1) { output_javascript_url('rollingarchive.php'); } else { output_javascript_url('theloop.php'); } ?>",
				"<?php echo attribute_escape(__('Type and Wait to Search','k2_domain')); ?>"
			);
			<?php endif; ?>

			<?php /* Rolling Archives */ if ( '1' == get_option('k2rollingarchives') ): ?>
			K2.RollingArchives = new RollingArchives(
				"<?php output_javascript_url('theloop.php'); ?>",
				"<?php echo attribute_escape(__('Page %1$d of %2$d',k2_domain)); ?>"
			);
			<?php endif; ?>
		});

		<?php /* Live Comment */
			if ( ( '1' == get_option('k2livecommenting') )
				and (
					( is_page() or is_single() )
					and ( !isset($_GET['jal_edit_comments']) )
					and ( 'open' == $post->comment_status )
					or ( 'comment' == $post->comment_type )
				)
			): ?>
			K2.ajaxCommentsURL = "<?php output_javascript_url('comments-ajax.php'); ?>";
		<?php endif; ?>
	//]]>
	</script>

	<?php wp_get_archives('type=monthly&format=link'); ?>
</head>

<body class="<?php k2_body_class(); ?>">

<?php /* K2 Hook */ do_action('template_body_top'); ?>

<a class="skiplink" href="#startcontent" accesskey="2"><?php _e('Skip to content','k2_domain'); ?></a>

<div id="page">

	<?php /* K2 Hook */ do_action('template_before_header'); ?>

	<div id="header">

		<h1 class="blog-title"><a href="<?php echo get_option('home'); ?>/" accesskey="1"><?php bloginfo('name'); ?></a></h1>
		<p class="description"><?php bloginfo('description'); ?></p>

		<?php /* K2 Hook */ do_action('template_header'); ?>

	</div> <!-- #header -->

	<hr />

	<?php /* K2 Hook */ do_action('template_before_content'); ?>
