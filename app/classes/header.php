<?php

// Based on Hasse R. Hansen's K2 header plugin - http://www.ramlev.dk

@define('K2_HEADERS_DIR', TEMPLATEPATH . '/images/headers');
@define('K2_HEADERS_URL', get_template_directory_uri() . '/images/headers');

class K2Header {
	function init() {
		$columns = get_option('k2columns');

		// dynamic columns, use 3 columns width
		if ( 'dynamic' == $columns )
			$columns = 3;
		
		// set minimum of 1 column
		if ( $columns < 1 )
			$columns = 1;

		// default k2 widths
		$default_widths =  array( 1 => 560, 780, 950 );

		// Load style settings
		if ( K2_STYLES ) {
			$styleinfo = get_option('k2styleinfo');

			if ( ! empty($styleinfo) ) {
				// style contains header height setting
				if ( ! empty($styleinfo['header_height']) )
					@define( 'HEADER_IMAGE_HEIGHT', $styleinfo['header_height'] );

				// style contains header width setting
				if ( ! empty($styleinfo['header_width']) )
					@define( 'HEADER_IMAGE_WIDTH', $styleinfo['header_width'] );

				// style contains layout widths setting
				if ( ! empty($styleinfo['layout_widths'][$columns]) )
					@define( 'HEADER_IMAGE_WIDTH', $styleinfo['layout_widths'][$columns] );

				if ( ! empty($styleinfo['header_text_color']) )
					@define( 'HEADER_TEXTCOLOR', $styleinfo['header_text_color'] );
			}
		}
		
		// Default settings
		@define( 'HEADER_IMAGE_HEIGHT', 200 );
		@define( 'HEADER_IMAGE_WIDTH', $default_widths[$columns] );
		@define( 'HEADER_TEXTCOLOR', 'ffffff' );
		@define( 'HEADER_IMAGE', '%s/images/transparent.gif' );

		// Only load Custom Image Header if GD is installed
		if ( extension_loaded('gd') && function_exists('gd_info') ) {
			add_custom_image_header(array('K2Header', 'output_header_css'), array('K2Header', 'output_admin_header_css'));
		}
	}

	function install() {
		add_option('k2headerimage', '', 'Current Header Image');
		add_option('k2blogornoblog', 'Blog', 'The text on the first tab in the header navigation.');
	}

	function uninstall() {
		delete_option('k2headerimage');
		delete_option('k2blogornoblog');

		remove_theme_mods();
	}

	function display_options() {
		// Get the current header picture
		$current_header_image = get_option('k2headerimage');

		// Get the header pictures
		$header_images = K2Header::get_header_images();

?>
		<li>
			<h3><?php _e('Header', 'k2_domain'); ?></h3>

			<p class="description">
			<?php
				printf( __('The current header size is <strong>%1$s px by %2$s px</strong>.', 'k2_domain'),
					HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT
				);

				if ( extension_loaded('gd') and function_exists('gd_info') ) {
					printf( __(' Use %s to customize the header.', 'k2_domain'),
						'<a href="themes.php?page=custom-header">' . __('Custom Image Header', 'k2_domain') . '</a>'
					);
				}
				?>
			</p>

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="k2-header-image"><?php _e('Select an Image:', 'k2_domain'); ?></label>
						</th>
						<td>
							<select id="k2-header-image" name="k2[headerimage]">
								<option value="" <?php selected($current_header_image, ''); ?>><?php _e('Off', 'k2_domain'); ?></option>
								<option value="random" <?php selected($current_header_image, 'random'); ?>><?php _e('Random', 'k2_domain'); ?></option>
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
					<tr>
						<th scope="row">
							<label for="k2-blog-tab"><?php _e('Rename the \'Blog\' tab:', 'k2_domain'); ?></label>
						</th>
						<td>
							<input id="k2-blog-tab" name="k2[blogornoblog]" type="text" value="<?php form_option('k2blogornoblog'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</li>
<?php
	}

	function update_options() {
		// Blog tab
		if ( isset($_POST['k2']['blogornoblog']) ) {
			update_option( 'k2blogornoblog', strip_tags( stripslashes($_POST['k2']['blogornoblog']) ) );
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

	function output_header_css() {
		$image_url = K2Header::get_header_image_url();
		?>
		<style type="text/css">
		<?php if ( !empty($image_url) ): ?>
		#header {
			background-image: url("<?php echo $image_url; ?>");
		}
		<?php endif; ?>

		<?php if ( 'blank' == get_header_textcolor() ): ?>
		#header .blog-title,
		#header .description {
			position: absolute !important;
			left: 0px;
			top: -500px !important;
			width: 1px;
			height: 1px;
			overflow: hidden;
		}
		<?php else: ?>
		#header .blog-title a,
		#header .description {
			color: #<?php header_textcolor(); ?>;
		}
		<?php endif; ?>
		</style>
		<?php
	}

	function output_admin_header_css() {
		?>
		<style type="text/css">
		#headimg {
			height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
			width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
			background-color: #3371A3 !important;
		}

		#headimg h1 {
			font-size: 30px;
			font-weight: bold;
			letter-spacing: -1px;
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

add_action('k2_init', array('K2Header', 'init'), 11);
add_action('k2_install', array('K2Header', 'install'));
add_action('k2_uninstall', array('K2Header', 'uninstall'));

add_action( 'k2_display_options', array('K2Header', 'display_options') );
add_action( 'k2_update_options', array('K2Header', 'update_options') );

add_action('wp_create_file_in_uploads', array('K2Header', 'process_custom_header_image'), 10, 2);
add_filter('wp_create_file_in_uploads', array('K2Header', 'process_custom_header_image'), 10, 2);
