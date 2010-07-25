<?php
/**
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

// Based on Hasse R. Hansen's K2 header plugin - http://www.ramlev.dk

@define('K2_HEADERS_DIR', TEMPLATEPATH . '/images/headers');
@define('K2_HEADERS_URL', TEMPLATEURL . '/images/headers');

class K2Header {
	function init() {

		if ( is_active_sidebar('widgets-sidebar-1') && is_active_sidebar('widgets-sidebar-2') )
			$columns = 3;
		else if ( is_active_sidebar('widgets-sidebar-1') || is_active_sidebar('widgets-sidebar-2') )
			$columns = 2;
		else
			$columns = 1;

		// set minimum of 1 column
		if ( $columns < 1 )
			$columns = 1;

		// default k2 widths
		$default_widths =  array( 1 => 560, 780, 950 );

		// Default settings
		@define( 'HEADER_IMAGE_HEIGHT', apply_filters('k2_header_height', 200) );
		@define( 'HEADER_IMAGE_WIDTH', apply_filters('k2_header_width', $default_widths[$columns], $columns) );
		@define( 'HEADER_TEXTCOLOR', apply_filters('k2_header_textcolor', 'ffffff') );

		/*
		@define( 'HEADER_IMAGE', '%s/images/headers/default.png' );

		// Default custom headers packaged with the theme.
		if ( function_exists('register_default_headers') ) {
			register_default_headers( array (
				'default' => array (
					'url' => '%s/images/headers/default.png',
					'thumbnail_url' => '%s/images/headers/default.png',
					'description' => __( 'Default', 'k2' )
				)
			) );
		}
		*/

		// Only load Custom Image Header if GD is installed
		if ( extension_loaded('gd') && function_exists('gd_info') ) {
			add_custom_image_header(array('K2Header', 'output_header_css'), array('K2Header', 'output_admin_header_css'));
		}
	}

	function install() {
		add_option( 'k2headerimage', '' );
		add_option( 'k2hometab', '1' );
		add_option( 'k2hometabcustom', 'Blog' );
	}

	function uninstall() {
		delete_option('k2headerimage');
		delete_option('k2hometab');
		delete_option('k2hometabcustom');

		remove_theme_mods();
	}

	function display_options() {
		// Get the current header picture
		$current_header_image = get_option('k2headerimage');
		$current_hometab = get_option('k2hometab');

		// Get the header pictures
		$header_images = K2Header::get_header_images();

?>
		<li>
			<h3><?php _e('Header', 'k2'); ?></h3>

			<p class="description">
			<?php
				/* translators: 1: header image width, 2: header image height */
				printf( __('The current header size is <strong>%1$s px by %2$s px</strong>.', 'k2'),
					HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT
				);

				if ( extension_loaded('gd') and function_exists('gd_info') ) {
					echo ' ';
					printf( __('Use %s to customize the header.', 'k2'),
						'<a href="themes.php?page=custom-header">' . __('Custom Image Header', 'k2') . '</a>'
					);
				}
				?>
			</p>

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="k2-header-image"><?php _e('Select an Image:', 'k2'); ?></label>
						</th>
						<td>
							<select id="k2-header-image" name="k2[headerimage]">
								<option value="" <?php selected($current_header_image, ''); ?>><?php _e('Off', 'k2'); ?></option>
								<option value="random" <?php selected($current_header_image, 'random'); ?>><?php _e('Random', 'k2'); ?></option>
								<?php foreach($header_images as $image): ?>
									<?php if ( is_numeric($image) ): ?>
										<option value="<?php echo esc_attr($image); ?>" <?php selected($current_header_image, $image); ?>><?php echo basename( get_attached_file($image) ); ?></option>
									<?php else: ?>
										<option value="<?php echo esc_attr($image); ?>" <?php selected($current_header_image, $image); ?>><?php echo basename($image); ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<?php if ( !function_exists('has_nav_menu') || ( function_exists('has_nav_menu') && !has_nav_menu('header') ) ): ?>
					<tr>
						<th scope="row">
							<label for="k2-hometab"><?php _e('Header Menu Home tab:', 'k2'); ?></label>
						</th>
						<td>
							<select id="k2-hometab" name="k2[hometab]">
								<option value="off" <?php selected($current_hometab, 'off'); ?>><?php _e('Off', 'k2'); ?></option>
								<option value="home" <?php selected($current_hometab, 'home'); ?>><?php _e('Home'); /* using WP translation, do not use K2 */ ?></option>
								<?php if ( 'page' == get_option('show_on_front') ): ?>
									<option value="page" <?php selected($current_hometab, 'page'); ?>><?php echo get_the_title( get_option('page_on_front') ); ?></option>
								<?php endif; ?>
								<option value="custom" <?php selected($current_hometab, 'custom'); ?>><?php _e('Custom', 'k2'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="k2-hometab-custom"><?php _e('Custom Home tab:', 'k2'); ?></label>
						</th>
						<td>
							<input type="text" name="k2[hometabcustom]" id="k2-hometab-custom" value="<?php form_option('k2hometabcustom'); ?>" />
						</td>
					</tr>
					<?php endif; // end check for has_nav_menu ?>
				</tbody>
			</table>
		</li>
<?php
	}

	function update_options() {
		// Blog tab
		if ( isset($_POST['k2']['hometab']) ) {
			update_option( 'k2hometab', $_POST['k2']['hometab'] );
		}

		if ( isset($_POST['k2']['hometabcustom']) ) {
			update_option( 'k2hometabcustom', strip_tags( stripslashes($_POST['k2']['hometabcustom']) ) );
		}

		// Header Image
		if ( isset($_POST['k2']['headerimage']) ) {
			update_option('k2headerimage', $_POST['k2']['headerimage']);

			// Update Custom Image Header
			if ( ('' == $_POST['k2']['headerimage']) or ('random' == $_POST['k2']['headerimage']) ) {
				remove_theme_mod('header_image');
			} else {
				set_theme_mod('header_image', K2Header::get_header_image_url() );
			}
		}
	}

	function get_header_image_url() {
		$header_image = get_option('k2headerimage');

		if ( empty($header_image) )
			return false;

		// randomly select an image
		if ( 'random' == $header_image ) {
			$images = K2Header::get_header_images();
			$size = count($images);

			if ( $size > 1 )
				$header_image = $images[ rand(0, $size - 1) ];
			else
				$header_image = $images[0];
		}

		// image is an attachment
		if ( is_numeric($header_image) ) {
			$header_image = wp_get_attachment_url($header_image);

			if ( empty($header_image) )
				return false;

			return $header_image;
		}

		return K2_HEADERS_URL . "/$header_image";
	}

	function get_header_images() {
		global $wpdb;

		$images = K2::files_scan(K2_HEADERS_DIR, array('gif','jpeg','jpg','png'), 1);
		$attachment_ids = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'k2-header-image'", ARRAY_N);

		if ( !empty($attachment_ids) )
			foreach ( $attachment_ids as $id_array )
				$images[] = $id_array[0];

		return $images;
	}

	/**
	 * Output custom header background image and text color CSS classes if set by the user.
	 */
	function output_header_css() {
		$image_url = K2Header::get_header_image_url();

		if ( ( get_header_textcolor() != HEADER_TEXTCOLOR ) || ! empty( $image_url ) ): // Do we need to insert anything?
		?>

		<style type="text/css">
		<?php if ( !empty($image_url) ): ?>
			#header {
				background-image: url("<?php echo $image_url; ?>");
			}
		<?php endif; ?>

		<?php if ( 'blank' == get_header_textcolor() ): ?>
			#site-title,
			#site-description {
				display: none;
			}

		<?php elseif ( get_header_textcolor() != HEADER_TEXTCOLOR ): ?>
			#site-title a,
			#site-description {
				color: #<?php header_textcolor(); ?>;
			}
		<?php endif; ?>
		</style>

		<?php
		endif;
	}

	/**
	 * Output CSS for 'Custom Header Image' admin page.
	 */
	function output_admin_header_css() {
		?>
		<style type="text/css">
		#headimg {
			height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
			width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
			background-color: #3371A3 !important;
		}

		#headimg h1 {
			font: bold 30px 'Trebuchet MS', Verdana, Sans-Serif;
			margin: 0;
			padding: 75px 40px 0;
			border: none;
		}

		#headimg h1 a {
			text-decoration: none;
			border: none;
		}

		#headimg h1 a:hover {
			text-decoration: underline;
		}

		#headimg #desc {
			font-size: 10px;
			margin: 0 40px;
		}

		<?php if ( 'blank' == get_header_textcolor() ) { ?>
		#headimg h1, #headimg #desc {
			display: none;
		}
		<?php } else { ?>
		#headimg h1 a, #headimg #desc {
			color: #<?php header_textcolor(); ?>;
		}
		<?php } ?>
		</style>
		<?php
	}

	function process_custom_header_image($source, $id = 0) {
		// Handle only the final step
		if ( file_exists($source) and (strpos(basename($source),'midsize-') === false) ) {
			if ( 2 == $_GET['step'] ) {
				// Allows K2 to find the attachment
				add_post_meta( $id, 'k2-header-image', 'original' );
			} elseif ( 3 == $_GET['step'] ) {
				// Allows K2 to find the attachment
				add_post_meta( $id, 'k2-header-image', 'cropped' );
			}

			// Update K2 Options
			update_option( 'k2headerimage', $id );
		}

		return $source;
	}
}


/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 */
function k2_page_menu_args( $args ) {
	$args['depth']       = 3;
	$args['menu_class']  = 'headermenu';
	$args['sort_column'] = 'menu_order';

	switch ( get_option('k2hometab') ) {
		default:
		case 'home':
			$args['show_home'] = true;
			break;

		case 'off':
			$args['show_home'] = false;
			break;

		case 'page':
			if ( 'page' == get_option('show_on_front') )
				$args['show_home'] = get_the_title( get_option('page_on_front') );
			else
				$args['show_home'] = true;
			break;

		case 'custom':
			$args['show_home'] = esc_attr( get_option('k2hometabcustom') );
			break;
	}

	return $args;
}
add_filter( 'wp_page_menu_args', 'k2_page_menu_args' );


add_action( 'k2_init', array('K2Header', 'init'), 11 );
add_action( 'k2_install', array('K2Header', 'install') );
add_action( 'k2_uninstall', array('K2Header', 'uninstall') );

add_action( 'k2_display_options', array('K2Header', 'display_options') );
add_action( 'k2_update_options', array('K2Header', 'update_options') );

if ( is_admin() && ( 'custom-header' == $_GET['page'] ) ) {
	add_action( 'wp_create_file_in_uploads', array('K2Header', 'process_custom_header_image'), 10, 2 );
	add_filter( 'wp_create_file_in_uploads', array('K2Header', 'process_custom_header_image'), 10, 2 );
}