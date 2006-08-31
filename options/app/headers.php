<?php

// Based on Hasse R. Hansen's K2 header plugin - http://www.ramlev.dk

$k2_headers_path = TEMPLATEPATH . '/images/headers/';

class headers {
	function init() {
		add_action('admin_menu', array('headers', 'add_menu'));

		if(get_option('k2header_picture') or get_option('k2imagerandomfeature')) {
			add_action('wp_head', array('headers', 'output_css'));
		}
	}

	function add_menu() {
		add_submenu_page('themes.php', __('K2 Custom Header','k2_domain'), __('K2 Custom Header','k2_domain'), 5, 'k2-header', array('headers', 'admin'));
	}

	function admin() {
		include(TEMPLATEPATH . '/options/display/headers.php');
	}

	function update() {
		global $k2_headers_path;

		// Manage the uploaded picture
		if($_FILES['picture']['name'] != "" and $_FILES['picture']['size'] > 0) {
			move_uploaded_file($_FILES['picture']['tmp_name'], $k2_headers_path . $_FILES['picture']['name']);

			if(isset($_POST['upload_activate'])) {
				update_option('k2header_picture', $_FILES['picture']['name']);
			}
		}

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

	function random_picture() {
		global $k2_headers_path;

		$picture_files = k2_files_scan($k2_headers_path, false, 1);
		$size = count($picture_files);

		if($size > 1) {
			return ($picture_files[rand(0, $size - 1)]);
		} else {
			return $picture_files[0];
		}
	}

	function output_css() {
		if(get_option('k2imagerandomfeature') == '1') {
			$picture = headers::random_picture();
		} else {
			$picture = get_option('k2header_picture');
		}

		$picture = 'background: url("' . get_bloginfo('template_url') . '/images/headers/' . $picture . '") no-repeat center center;';

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

?>
