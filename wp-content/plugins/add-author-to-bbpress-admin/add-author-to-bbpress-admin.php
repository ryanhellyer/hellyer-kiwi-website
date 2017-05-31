<?php
/*
Plugin Name: Add Author to bbPress Admin
Plugin URI: https://geek.hellyer.kiwi/plugins/add-author-to-bbpress-admin/
Description: Adds an author select box to the bbPress admin for changing the author of posts and replies.
Version: 1.0
Author: Ryan Hellyer
Author URI: https://geek.hellyer.kiwi/
Text Domain: add-author-to-bbpress-admin
License: GPL2

------------------------------------------------------------------------
Copyright Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/


/**
 * Do not continue processing since file was called directly
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Eh! What you doin in here?' );
}



add_action( 'bbp_author_metabox', 'add_author_to_bbpress_admin', 10, 1 );
/**
 * Adds an author select box to the bbPress admin.
 */
function add_author_to_bbpress_admin() {

	// Hack to work around bug in bbPress core - https://bbpress.trac.wordpress.org/ticket/2696#comment:4
	if ( ! did_action( 'admin_head' ) ) {
		return;
	}

	// You can filter this to alter the maximum number of users shown
	$max_number = apply_filters( 'add_author_to_bbpress_admin_max_number', 200 );

	$users = get_users(
		array(
			'orderby' => 'nicename',
			'number' => $max_number,
		)
	);

	// If too many users, then give up and report that the plugin won't work on this site
	if ( $max_number == count( $users ) ) {
		echo '<strong>' . esc_html__( sprintf( 'You have more than %s users. This is too many for the Add Author to bbPress admin plugin to handle, and so it is falling back to the default ID system sorry.', (string) $max_number ), 'add-author-to-bbpress-admin' ) . '</strong>';
		return;
	}

	// Finally, spit out the list of authors to choose from
	?>
	<p>
		<strong><?php esc_html_e( 'Select author by name', 'add-author-to-bbpress-admin' ); ?></strong>
		<br />
		<select name="post_author_override" id="bbp_author_selector"><?php

		echo '<option value="0">' . esc_html__( 'Anonymous user', 'add-author-to-bbpress-admin' ) . '</option>';
		foreach ( $users as $user ) {
			echo '<option ' . selected( $user->ID, bbp_get_global_post_field( 'post_author' ) ) . 'value="' . esc_attr( $user->ID ) . '">' . esc_html( $user->data->display_name ) . '</option>';
		}

		?>
		</select>
	</p>

	<!-- Disable the existing author ID input field -->
	<script>
	document.getElementById("bbp_author_id").setAttribute("disabled", "disabled");
	</script>
	<?php
}
