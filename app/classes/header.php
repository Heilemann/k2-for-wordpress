<?php

// Based on Hasse R. Hansen's K2 header plugin - http://www.ramlev.dk

global $k2_headers_path;
$k2_headers_path = TEMPLATEPATH . '/images/headers/';

class K2Header {
	function init() {
		/*add_action('admin_menu', array('K2Header', 'add_menu'));*/
		add_action('wp_head', array('K2Header', 'output_css'));
	}

	/*function add_menu() {
		add_submenu_page('themes.php', __('K2 Custom Header','k2_domain'), __('K2 Custom Header','k2_domain'), 5, 'k2-header', array('K2Header', 'admin'));
	}

	function admin() {
		include(TEMPLATEPATH . '/app/display/header.php');
	}*/

	function update() {
		global $k2_headers_path;

		// Manage the uploaded picture
		if($_FILES['picture']['name'] != "" and $_FILES['picture']['size'] > 0) {
			move_uploaded_file($_FILES['picture']['tmp_name'], $k2_headers_path . $_FILES['picture']['name']);

			if(isset($_POST['upload_activate'])) {
				update_option('k2header_picture', $_FILES['picture']['name']);
			}
		}

		if(!empty($_POST)) {
			if(isset($_POST['k2'])) {
				// Correct the colours
				if(trim($_POST['k2']['backgroundcolor']) != '') {
					$_POST['k2']['backgroundcolor'] = '#' . substr(str_replace('#', '', $_POST['k2']['backgroundcolor']),0,6);
				} else {
					unset($_POST['k2']['backgroundcolor']);
				}

				if(trim($_POST['k2']['foregroundcolor']) != '') {
					$_POST['k2']['foregroundcolor'] = '#' . substr(str_replace('#', '', $_POST['k2']['foregroundcolor']),0,6);
				} else {
					unset($_POST['k2']['foregroundcolor']);
				}

				// Set all the options
				foreach($_POST['k2'] as $option => $value) {
					update_option('k2' . $option, $value);
				}
			}
		}
	}

	function random_picture() {
		global $k2_headers_path;

		$picture_files = K2::files_scan($k2_headers_path, false, 1);
		$size = count($picture_files);

		if($size > 1) {
			return ($picture_files[rand(0, $size - 1)]);
		} else {
			return $picture_files[0];
		}
	}

	function output_css() {
		if(get_option('k2imagerandomfeature') == '1') {
			$picture = K2Header::random_picture();
		} else {
			$picture = get_option('k2header_picture');
		}

		if($picture != '') {
			$picture = 'background: url("' . get_bloginfo('template_url') . '/images/headers/' . $picture . '") no-repeat center center !important;';

			?>
			<style type="text/css">
				#header {
					<?php echo($picture); ?>
					background-color: <?php echo(get_option('k2headerbackgroundcolor')); ?>;
					}

				#header h1 {
					text-align: <?php echo(get_option('k2headertextalignment')); ?>;
					font-size: <?php echo(get_option('k2headertextfontsize')); ?>px;
					}

				h1, h1 a,h1 a:visited, #header .description {
					color: <?php echo(get_option('k2headertextcolor')); ?>;
      					}

				.description {
					display: block !important;
					text-align: <?php echo(get_option('k2headertextalignment')); ?>;
					}
			</style>
			<?php
		}
	}

	function install() {
		add_option('k2imagerandomfeature', '1', "Whether to use a random image in K2's header");
		add_option('k2header_picture', '', "The image to use in K2's header");
		add_option('k2headerbackgroundcolor', '', "K2's header background color");
		add_option('k2headertextalignment', 'left', "K2's header text alignment");
		add_option('k2headertextfontsize', '', "K2's header text font size");
		add_option('k2headertextcolor', '', "K2's header text font color");
		add_option('k2headertextcolor_bright', '', "K2's header text bright font colour");
		add_option('k2headertextcolor_dark', '', "K2's header text dark font colour");
	}

	function uninstall() {
		delete_option('k2imagerandomfeature');
		delete_option('k2header_picture');
		delete_option('k2headerbackgroundcolor');
		delete_option('k2headertextalignment');
		delete_option('k2headertextfontsize');
		delete_option('k2headertextcolor');
		delete_option('k2headertextcolor_bright');
		delete_option('k2headertextcolor_dark');
	}
}

add_action('k2_init', array('K2Header', 'init'), 2);
add_action('k2_install', array('K2Header', 'install'));
add_action('k2_uninstall', array('K2Header', 'uninstall'));

?>
