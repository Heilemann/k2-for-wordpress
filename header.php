<?php
	// Load localizatons
	load_theme_textdomain('k2_domain');

	// Register our scripts with script loader
	K2::register_scripts();

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
	if ( is_page() or is_single() ) {
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
		printf( __('Search Results for &lsquo;%s&rsquo;','k2_domain'), get_query_var('s') );
	}

	// Insert separator for the titles above
	if ( !is_home() and !is_404() ) {
		_e(' at ','k2_domain');
	}
	
	// Finally the blog name
	bloginfo('name');

	?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
	<meta name="template" content="K2 <?php k2info('version'); ?>" />
 	<meta name="description" content="<?php bloginfo('description'); ?>" />
  
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('stylesheet_url'); ?>" />
	<?php /* Rolling Archives */ if (get_option('k2rollingarchives') == 1) { ?>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_url'); ?>/css/rollingarchives.css" />
	<?php } ?>
	<?php /* Custom Style */ if (get_option('k2scheme') != '') { ?>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php k2info('style'); ?>" />
	<?php } ?>

	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />

	<?php if (is_single() or is_page()) { ?>
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php } ?>

	<?php wp_head(); ?>

	<script type="text/javascript">
	//<![CDATA[
		<?php /* Debugging */ if ( isset($_GET['k2debug']) ) { ?>
			K2.debug = true;
		<?php } ?>

		<?php /* LiveSearch */ if (get_option('k2livesearch') == 1) { ?>
			jQuery(document).ready(function(){
				k2Search.setup(
					"<?php if (get_option('k2rollingarchives') == 1) { output_javascript_url('rollingarchive.php'); } else { output_javascript_url('theloop.php'); } ?>",
					"<?php echo attribute_escape(__('Type and Wait to Search','k2_domain')); ?>"
				);
			});
		<?php } ?>

		<?php /* Hide Author Elements */ if (!is_user_logged_in() and (is_page() or is_single()) and ($comment_author = $_COOKIE['comment_author_'.COOKIEHASH]) and ('open' == $post-> comment_status) or ('comment' == $post-> comment_type) ) { ?>
			jQuery(document).ready(function(){ OnLoadUtils(); });
		<?php } ?>

		<?php if ((get_option('k2livecommenting') == 1) and ((is_page() or is_single()) and (!isset($_GET['jal_edit_comments'])) and ('open' == $post-> comment_status) or ('comment' == $post-> comment_type) )) { ?>
			K2.ajaxCommentsURL = "<?php output_javascript_url('comments-ajax.php'); ?>";
		<?php } ?>

	//]]>
	</script>

	<?php wp_get_archives('type=monthly&format=link'); ?>
</head>

<body class="<?php k2_body_class(); ?>" <?php k2_body_id(); ?>>

<div id="page">

	<div id="header">

		<h1><a href="<?php echo get_settings('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
		<p class="description"><?php bloginfo('description'); ?></p>

		<ul class="menu">
			<?php if ('page' != get_option('show_on_front')) { ?>
			<li class="<?php if ( is_home() or is_archive() or is_single() or is_paged() or is_search() or (function_exists('is_tag') and is_tag()) ) { ?>current_page_item<?php } else { ?>page_item<?php } ?>"><a href="<?php echo get_settings('home'); ?>/" title="<?php echo get_option('k2blogornoblog'); ?>"><?php echo get_option('k2blogornoblog'); ?></a></li>
			<?php } ?>
			<?php wp_list_pages('sort_column=menu_order&depth=1&title_li='); ?>
			<?php wp_register('<li class="admintab">','</li>'); ?>
		</ul>

	</div>

		<hr />