<?php
/**
 * Header Template
 *
 * This file is loaded by header.php and used for content inside the #header div
 *
 * @package K2
 * @subpackage Templates
 */

// For SEO, outputs the blog title in h1 or a div
$heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div';
?>

<<?php echo $heading_tag; ?> id="site-title">
<span>
	<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
</span>
</<?php echo $heading_tag; ?>>
<div id="site-description"><?php bloginfo( 'description' ); ?></div>

<?php
	// Display the page tabs
	if ( function_exists('wp_nav_menu') ) {
		wp_nav_menu( array(
			'theme_location'	=> 'header'
		) );
	} else {
	 	wp_page_menu();
	}
?>
