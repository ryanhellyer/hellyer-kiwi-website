<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package PixoPoint
 * @since PixoPoint 1.0
 */
?>
<div id="sidebar" role="complementary"><?php

	if ( ! dynamic_sidebar( 'sidebar' ) ) { ?>

	<aside>
		<p><?php _e( 'Please add widgets to the sidebar.', 'pixopoint' ); ?></p>
	</aside><?php
	}
	?>

</div><!-- #sidebar -->
