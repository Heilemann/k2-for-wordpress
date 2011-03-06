<?php
/**
 * Header Template
 *
 * This file is loaded by header.php and used for content inside the #header div
 *
 * @package K2
 * @subpackage Templates
 */
?>
<hgroup>
	<h1 id="site-title"><span><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home" accesskey="1"><?php bloginfo( 'name' ); ?></a></span>
	</h1>
	<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
</hgroup>

<nav id="access" role="navigation">
	<div class="skip-link screen-reader-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'k2' ); ?>"><?php _e( 'Skip to content', 'k2' ); ?></a></div>
<?php
	wp_nav_menu( array(
		'theme_location' => 'header',
		'container_class' => 'headermenu',
		'container_id' => 'k2_headermenu',
	) );
?>
</nav>