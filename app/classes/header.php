<?php

// Based on Hasse R. Hansen's K2 header plugin - http://www.ramlev.dk

class K2Header {
	function init() {
		$styleinfo = get_option('k2styleinfo');
		$width = K2Header::get_header_width();

		define('HEADER_IMAGE_HEIGHT', empty($styleinfo['header_height'])? K2_HEADER_HEIGHT : $styleinfo['header_height']);
		define('HEADER_IMAGE_WIDTH', $width);
		define('HEADER_TEXTCOLOR', empty($styleinfo['header_text_color'])? 'ffffff' : $styleinfo['header_text_color']);
		define('HEADER_IMAGE', '%s/images/transparent.gif');

		// Only load Custom Image Header if GD is installed
		if ( extension_loaded('gd') && function_exists('gd_info') ) {
			add_custom_image_header(array('K2Header', 'output_header_css'), array('K2Header', 'output_admin_header_css'));
		}
	}

	function uninstall() {
		remove_theme_mods();
	}

	function get_header_width() {
		$default_widths =  array( 1 => 560, 780, 950 );
		$styleinfo = get_option('k2styleinfo');
		$columns = get_option('k2columns');

		// dynamic columns, use 3 columns width
		if ( 'dynamic' == $columns ) {
			$columns = 3;
		}

		// style contains header width setting
		if ( ! empty($styleinfo['header_width']) ) {
			return $styleinfo['header_width'];
		}

		// style contains layout widths setting
		if ( ! empty($styleinfo['layout_widths'][$columns]) ) {
			return $styleinfo['layout_widths'][$columns];
		}

		return $default_widths[$columns];
	}

	function get_header_images() {
		global $wpdb;

		$images = K2::files_scan(K2_HEADERS_PATH, array('gif','jpeg','jpg','png'), 1, 2);

		$attachment_ids = (array) $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'k2-header-image'");
		foreach ($attachment_ids as $id) {
			$images[] = str_replace(ABSPATH, '', get_attached_file($id) );
		}

		return $images;
	}

	function random_picture() {
		$picture_files = K2Header::get_header_images();

		$size = count($picture_files);

		if ($size > 1) {
			return ($picture_files[rand(0, $size - 1)]);
		} else {
			return $picture_files[0];
		}
	}

	function output_header_css() {
		$header_image = get_option('k2headerimage');

		if ( 'random' == $header_image ) {
			$picture = K2Header::random_picture();
		} else {
			$picture = $header_image;
		}
		?>
		<style type="text/css">
		<?php if ( $picture != '' ): ?>
		#header {
			background-image: url("<?php echo get_option('siteurl') . '/' . $picture; ?>");
		}
		<?php endif; ?>

		<?php if ( 'blank' == get_header_textcolor() ): ?>
		#header h1, #header .description {
			display: none;
		}
		<?php else: ?>
		#header h1 a, #header .description {
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
				if ( get_wp_version() < 2.4 ) {
					// Quick & dirty mime-type
					$ext = pathinfo($source, PATHINFO_EXTENSION);
					switch ($ext) {
						case 'jpg':
						case 'jpe':
							$mime = 'jpeg';
							break;

						case 'tif':
							$mime = 'tiff';
							break;

						default:
							$mime = $ext;
							break;
					}

					// Get original attachment
					$attachment = get_post( $id );
					$attachment->post_mime_type = 'image/' . $mime;
					$attachment->post_excerpt = sprintf( __('Custom Header Image for K2 (%1$d x %2$d)', 'k2_domain'), HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT );

					// Update the attachment
					$id = wp_insert_attachment( $attachment, $source );
					wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $source ) );
				}

				// Allows K2 to find the attachment
				add_post_meta( $id, 'k2-header-image', 'original' );

			} elseif ( 3 == $_GET['step'] ) {
				if ( get_wp_version() < 2.4 ) {
					// Get original attachment
					$parent = get_post( $_POST['attachment_id'] );
					$parent_url = $parent->guid;
					$url = str_replace(basename($parent_url), basename($source), $parent_url);

					// Construct the object array
					$object = array(
						'post_title' => basename($source),
						'post_content' => $url,
						'post_excerpt' => sprintf( __('Custom Header Image for K2 (%1$d x %2$d)', 'k2_domain'), HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT ),
						'post_mime_type' => 'image/jpeg',
						'guid' => $url
					);

					// Create a new attachment
					$id = wp_insert_attachment( $object, $source );
					wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $source ) );
				}

				// Allows K2 to find the attachment
				add_post_meta( $id, 'k2-header-image', 'cropped' );
			}

			// Update K2 Options
			update_option( 'k2headerimage', str_replace(ABSPATH, '', $source) );
		}

		return $source;
	}
}

add_action('k2_init', array('K2Header', 'init'), 11);
//add_action('k2_install', array('K2Header', 'install'));
add_action('k2_uninstall', array('K2Header', 'uninstall'));
add_action('wp_create_file_in_uploads', array('K2Header', 'process_custom_header_image'), 10, 2);
add_filter('wp_create_file_in_uploads', array('K2Header', 'process_custom_header_image'), 10, 2);
?>
