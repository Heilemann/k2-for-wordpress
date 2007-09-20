<?php

// Based on Hasse R. Hansen's K2 header plugin - http://www.ramlev.dk

class K2Header {
	function install() {
		add_option('k2imagerandomfeature', '1', "Whether to use a random image in K2's header");
		add_option('k2header_picture', '', "The image to use in K2's header");
	}

	function uninstall() {
		delete_option('k2imagerandomfeature');
		delete_option('k2header_picture');
	}

	function init() {
		if ( function_exists('add_custom_image_header') and is_writable(K2_HEADERS_PATH) ) {
			$styleinfo = get_style_data(get_option('k2scheme'));
			$header_image = get_option('k2header_picture');

			define('HEADER_IMAGE_HEIGHT', empty($styleinfo['header_height'])? 200 : $styleinfo['header_height']);
			define('HEADER_IMAGE_WIDTH', empty($styleinfo['header_width'])? 950 : $styleinfo['header_width']);
			define('HEADER_TEXTCOLOR', empty($styleinfo['header_text_color'])? 'ffffff' : $styleinfo['header_text_color']);
			define('HEADER_IMAGE', empty($header_image)? '%s/images/transparent.gif' : get_k2info('headers_url') . $header_image);

			add_custom_image_header(array('K2Header', 'output_header_css'), array('K2Header', 'output_admin_header_css'));
		} else {
			add_action('wp_head', array('K2Header', 'output_header_css'));
		}
	}

	function update() {
		// Manage the uploaded picture
		if (!empty($_FILES['image_upload']['name']) and !empty($_FILES['image_upload']['size'])) {
			move_uploaded_file($_FILES['image_upload']['tmp_name'], K2_HEADERS_PATH . $_FILES['image_upload']['name']);

			if (isset($_POST['upload_activate'])) {
				update_option('k2header_picture', $_FILES['image_upload']['name']);
			}
		}

		if (!empty($_POST['k2'])) {

			// Random Image
			if(isset($_POST['k2']['imagerandomfeature'])) {
				update_option('k2imagerandomfeature', '1');
			} else {
				update_option('k2imagerandomfeature', '0');
			}

			// Header Image
			if (isset($_POST['k2']['header_picture'])) {
				update_option('k2header_picture', $_POST['k2']['header_picture']);

				// Update Custom Image Header
				if (function_exists('set_theme_mod')) {
					if (empty($_POST['k2']['header_picture'])) {
						remove_theme_mod('header_image');
					} else {
						set_theme_mod('header_image', get_k2info('headers_url') . get_option('k2header_picture'));
					}
				}
			}
		}
	}

	function get_header_images() {
		return K2::files_scan(K2_HEADERS_PATH, array('gif','jpeg','jpg','png'), 1);
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
		if (get_option('k2imagerandomfeature') == '1') {
			$picture = K2Header::random_picture();
		} else {
			$picture = get_option('k2header_picture');
		}
		?>
		<style type="text/css">
		<?php if (!empty($picture)) { ?>
		#header {
			background: url("<?php echo get_k2info('headers_url') . $picture; ?>");
		}
		<?php } ?>
		<?php if (function_exists('add_custom_image_header')) { ?>
			<?php if ( 'blank' == get_header_textcolor() ) { ?>
			#header h1, #header .description {
				display: none;
			}
			<?php } else { ?>
			#header h1 a, #header .description {
				color: #<?php header_textcolor(); ?>;
			}
			<?php } ?>
		<?php } ?>
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
			color: #<?php echo HEADER_TEXTCOLOR ?>;
		}
		<?php } ?>
		</style>
		<?php
	}

	function process_custom_header_image($source, $id = false) {
		// Workaround for WP 2.1 bug
		if ( empty($source) ) {
			$uploads = wp_upload_dir();
			$source = str_replace($uploads['url'], $uploads['path'], get_theme_mod('header_image'));
			$id = true;
		}
	
		// Handle only the final step
		if ( file_exists($source) and (strpos(basename($source),'midsize-') === false) ) {

			if ($id) {
				$dest = K2::copy_file($source, K2_HEADERS_PATH . basename($source));
			} else {
				$dest = K2::move_file($source, K2_HEADERS_PATH . basename($source));
			}

			if (false !== $dest) {
				update_option('k2header_picture', basename($dest));
				set_theme_mod('header_image', get_bloginfo('template_directory') . '/images/headers/' . basename($dest));

				return $dest;
			}
		}
		return $source;
	}
}

add_action('k2_init', array('K2Header', 'init'), 2);
add_action('k2_install', array('K2Header', 'install'));
add_action('k2_uninstall', array('K2Header', 'uninstall'));
add_filter('wp_create_file_in_uploads', array('K2Header', 'process_custom_header_image'));
?>
