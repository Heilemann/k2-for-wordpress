<?php
class updater {
	function k2update() {
		if(!empty($_POST)) {
			// Archives is a special case
			if(isset($_POST['k2']['archives'])) {
				archive::create_archive();
			} else {
				// thanks to Michael Hampton, http://www.ioerror.us/ for the assist
				$_POST['k2']['archives'] = '';
				archive::delete_archive();
			}

			// Set all the options
			foreach($_POST['k2'] as $option => $value) {
				update_option('k2' . $option, $value);
			}

			// Other actions that may be needed to be performed

			if(isset($_POST['k2']['scheme'])) {
				k2styleinfo_update();
			}

			if(isset($_POST['k2']['styleinfo_format'])) {
				k2styleinfo_update();
			}

			if(isset($_POST['configela']) and archive::setup_archive()) {
				unset($_POST['configela']);
			}

			if(isset($_POST['uninstall'])) {
				tools::uninstall();
			}
		}
	}
}
?>
