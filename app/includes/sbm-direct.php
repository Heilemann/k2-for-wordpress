<?php
	require_once( preg_replace( '/wp-content.*/', '', $_SERVER['SCRIPT_FILENAME'] ) . 'wp-config.php' );
	//require_once(ABSPATH . 'wp-admin/admin-functions.php');
	//require_once(ABSPATH . 'wp-admin/admin-db.php');


	if ( !function_exists('selected') ) {
		function selected( $selected, $current) {
			if ( $selected == $current)
				echo ' selected="selected"';
		}
	}

	K2SBM::direct_bootstrap();
?>
