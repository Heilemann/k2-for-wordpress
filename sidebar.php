<?php
	$primary_width = (int) get_option('k2_960_primarywidth');
	$max_sidebar_width = 16 - $primary_width;
?>

<div id="sidebars" class="grid_<?php echo $max_sidebar_width; ?>">
<?php
	if ( $sidebars = get_option('k2_960_sidebarwidths') ) {
		$total_width = 0;
		$widths = explode(' ', $sidebars);

		foreach ($widths as $key => $width) {
			$c = array();

			if ($width > $max_sidebar_width)
				$width = $max_sidebar_width;

			$c[] = 'grid_' . $width;

			// beginning of row
			if (0 == $total_width)
				$c[] = 'alpha';

			$total_width += $width;
			if ($total_width >= $max_sidebar_width) {
				$c[] = 'omega';
				$total_width = 0;
			}

			echo '<div id="sidebar-' . ($key + 1) . '" class="secondary ' . implode(' ', $c) . '">';
			dynamic_sidebar($key + 1);
			echo '</div><!-- #sidebar-' . ($key + 1) . ' -->';
		}
	}
?>
</div><!-- #sidebars -->
