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
$block = ( is_front_page() ? 'h1' : 'div' );
?>

<?php echo "<$block class='blog-title'>"; ?>
	<a href="<?php echo get_option('home'); ?>/" accesskey="1"><?php bloginfo('name'); ?></a>
<?php echo "</$block>"; ?>

<p class="description"><?php bloginfo('description'); ?></p>

<?php
	// Display the page tabs
	wp_page_menu( array( 'show_home' => esc_attr( get_option('k2blogornoblog') ), 'depth' => 3 ) );
?>
