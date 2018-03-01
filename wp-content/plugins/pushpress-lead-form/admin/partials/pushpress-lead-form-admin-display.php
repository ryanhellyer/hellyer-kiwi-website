<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    pushpress-lead-form
 * @subpackage pushpress-lead-form/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1><?php esc_html_e( 'PushPress Lead Form Admin', 'pushpress-lead-form' ); ?></h1>
<p><?php esc_html_e( 'This plugin will generate and process shortcode to allow you to display your lead form on your website', 'pushpress-lead-form' ); ?>
<p>
	[pushpress_lead_form]
</p>

<div style="width:60%";>
	<h2><?php esc_html_e( 'Example:', 'pushpress-lead-form' ); ?></h2>
	<?php if (PUSHPRESS_INTEGRATED) { 
	 	echo pushpress_lead_form_shortcode();
	 }
	 else { 
	?>


	<?php
	 }
	?>
</div>
