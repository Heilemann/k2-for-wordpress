<?php
// WordPress Compatibility Functions

/**
 * is_front_page() - Is it the front of the site, whether blog view or a WP Page?
 *
 * @since 2.5
 * @uses is_home
 * @uses get_option
 *
 * @return bool True if front of site
 */
if ( ! function_exists('is_front_page') ):
	function is_front_page () {
		// most likely case
		if ( 'posts' == get_option('show_on_front') && is_home() )
			return true;
		elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') && is_page(get_option('page_on_front')) )
			return true;
		else
			return false;
	}
endif;

/**
 * Whether post requires password and correct password has been provided.
 *
 * @since 2.7.0
 *
 * @param int|object $post An optional post.  Global $post used if not provided.
 * @return bool false if a password is not required or the correct password cookie is present, true otherwise.
 */
if ( ! function_exists('post_password_required') ):
	function post_password_required( $post = null ) {
		$post = get_post($post);

		if ( empty($post->post_password) )
			return false;

		if ( !isset($_COOKIE['wp-postpass_' . COOKIEHASH]) )
			return true;

		if ( $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password )
			return true;

		return false;
	}
endif;

/**
 * Returns the Log Out URL.
 *
 * Returns the URL that allows the user to log out of the site
 *
 * @since 2.7
 * @uses wp_nonce_url() To protect against CSRF
 * @uses site_url() To generate the log in URL
 * 
 * @param string $redirect Path to redirect to on logout.
 */
if ( ! function_exists('wp_logout_url') ):
	function wp_logout_url($redirect = '') {
		if ( strlen($redirect) )
			$redirect = "&redirect_to=$redirect";
	
		return wp_nonce_url( site_url("wp-login.php?action=logout$redirect", 'login'), 'log-out' );
	}
endif;