<?php 

// Current version of K2
define('K2_CURRENT', 'svn');

// K2 option prefix - for ease of modifications
define('K2_OPTION_PREFIX', 'k2');

// Is this MU or no?
define('K2_MU', (strpos($wp_version, 'wordpress-mu') === true));

/* Blast you red baron! Initialise the k2 system */
require(TEMPLATEPATH . '/app/classes/k2.php');
K2::init();

?>
