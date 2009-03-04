<?php if ( $bottombars = get_option('k2_960_bottombarwidths') ): ?>
	<div id="bottombars" class="container_16">
		<?php $widths = explode(' ', $bottombars); foreach ($widths as $key => $width): ?>
			<div id="bottombar-<?php echo $key + 1; ?>" class="secondary grid_<?php echo $width; ?>">
				<?php dynamic_sidebar('bottombar-' . ($key + 1)); ?>
			</div><!-- #bottombar-<?php echo $key + 1; ?> -->
		<?php endforeach; ?>
	</div><!-- #bottombars -->
<?php endif; ?>
