<?php

/**
 * Members listings.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Members extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {
		add_shortcode( 'src-members-list', array( $this, 'members_list_shortcode' ) );
	}

	/**
	 * Results.
	 */
	public function members_list_shortcode( $args ) {

		$content = '
				<table>
					<tr>
						<th>' . esc_html__( 'Name', 'src' ) . '</th>
						<th>' . esc_html__( 'Joined', 'src' ) . '</th>
					</tr>';

		$users = get_users(
			array(
				'number' => 200,
			)
		);
		foreach( $users as $row_number => $row ) {
			$user_id = $row->data->ID;
			$user_registered = $row->data->user_registered;
			$user_registered_unix = strtotime( $user_registered );
			$user_data = get_userdata( $user_id );
			$display_name = $user_data->data->display_name;
			if ( 1 == $display_name ) {
				$display_name = $user_data->data->user_login;
			}
			$url = bbp_get_user_profile_url( $user_id );

			$content .= '
						<tr>
							<td><a href="' . esc_url( $url ) . '">' . esc_html( $display_name ) . '</a></td>
							<td>' . esc_html( date( 'l jS F Y', $user_registered_unix ) ) . '</td>
						</tr>';

		}

		$content .= '
			</table>';

		return $content;
	}

}
