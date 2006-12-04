<?php load_theme_textdomain('k2_domain'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
	<title><?php wp_title(''); if (function_exists('is_tag') and is_tag()) { ?>Tag Archive for <?php echo $tag; } if (is_archive()) { ?> archive<?php } elseif (is_search()) { ?> Search for <?php echo $s; } if ( !(is_404()) and (is_search()) or (is_single()) or (is_page()) or (function_exists('is_tag') and is_tag()) or (is_archive()) ) { ?> at <?php } ?> <?php bloginfo('name'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
	<meta name="template" content="K2 <?php if (function_exists('k2info')) { k2info('version'); } ?>" />
 	<meta name="description" content="<?php bloginfo('description'); ?>" />
  
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="stylesheet" type="text/css" media="print" href="<?php bloginfo('template_url'); ?>/css/print.css" />

	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />

	<?php if (is_single() or is_page()) { ?>
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php } ?>

	<?php if ( ((get_option('k2livecommenting') == 1) and ((is_page() or is_single()) and (!isset($_GET['jal_edit_comments'])) and ('open' == $post-> comment_status) or ('comment' == $post-> comment_type) )) or (get_option('k2livesearch') == 1) or (get_option('k2rollingarchives') == 1) ) { ?>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/prototype.js.php"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/effects.js.php"></script>
	<?php } ?>

	<?php if ( (get_option('k2livesearch') == 1 and get_option('k2rollingarchives') == 1 ) or (get_option('k2rollingarchives') == 1) ) { ?>	
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/slider.js.php"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/trimmer.js.php"></script>
	<?php } ?>
	
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/k2functions.js.php"></script>

	<?php /* Live Commenting */ if ((get_option('k2livecommenting') == 1) and ((is_page() or is_single()) and (!isset($_GET['jal_edit_comments'])) and ('open' == $post-> comment_status) or ('comment' == $post-> comment_type) )) { ?>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/comments.js.php"></script>
	<?php } ?>

	<?php /* LiveSearch */ if (get_option('k2livesearch') == 1) { ?>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/livesearch.js.php"></script>
	<script type="text/javascript">
	//<![CDATA[
		new FastInit( function() { new Livesearch('searchform', 's', 'dynamic-content', 'current-content', <?php k2info('js_url'); ?> + '/rollingarchive.php', '&s=', 'searchform', 'searchload', '<?php _e('Type and Wait to Search','k2_domain'); ?>', 'searchreset', '<?php _e('go','k2_domain'); ?>'); } );
	//]]>
	</script>
	<?php } ?>

	<?php /* Rolling Archives */ if (get_option('k2rollingarchives') == 1) { ?>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/rollingarchives.js.php"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('template_directory'); ?>/css/rollingarchives.css" />
	<?php } ?>

	<?php /* Hide Author Elements */ if (!is_user_logged_in() and (is_page() or is_single()) and ($comment_author = $_COOKIE['comment_author_'.COOKIEHASH]) and ('open' == $post-> comment_status) or ('comment' == $post-> comment_type) ) { ?>
	<script type="text/javascript">
		new FastInit( OnLoadUtils );
	</script>
	<?php } ?>

	<?php wp_get_archives('type=monthly&format=link'); ?>

	<?php /* Custom Style */ if (get_option('k2scheme') != '') { ?>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php k2info('scheme'); ?>" />
	<?php } ?>

	<?php wp_head(); ?>	
</head>

<body class="<?php k2_body_class(); ?>" <?php k2_body_id(); ?>>

<div id="page">

	<div id="header">

		<h1><a href="<?php echo get_settings('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
		<p class="description"><?php bloginfo('description'); ?></p>

		<ul class="menu">
			<li class="<?php if ( is_home() or is_archive() or is_single() or is_paged() or is_search() or (function_exists('is_tag') and is_tag()) ) { ?>current_page_item<?php } else { ?>page_item<?php } ?>"><a href="<?php echo get_settings('home'); ?>/" title="<?php echo get_option('k2blogornoblog'); ?>"><?php echo get_option('k2blogornoblog'); ?></a></li>
			<?php wp_list_pages('sort_column=menu_order&depth=1&title_li='); ?>
			<?php wp_register('<li class="admintab">','</li>'); ?>
		</ul>

	</div>

		<hr />