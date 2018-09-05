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

	const CACHE_TIME = 10;

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
	public function members_list_shortcode() {

		if ( false === ( $users = get_transient( 'src_member_list' ) ) ) {

			$raw_users = get_users(
				array(
					'number' => 200,
				)
			);
			$users = array();
			foreach( $raw_users as $row_number => $row ) {

				$users[$row_number]['id'] = $user_id = $row->data->ID;
				$users[$row_number]['registered'] = strtotime( $row->data->user_registered );
				$user_data = get_userdata( $user_id );
				$users[$row_number]['name'] = $user_data->data->display_name;

				if ( 1 == $users[$row_number]['name'] ) {
					$users[$row_number]['name'] = $user_data->data->user_login;
				}

				$users[$row_number]['url'] = bbp_get_user_profile_url( $user_id );
				$users[$row_number]['post_count'] = count_user_posts( $user_id , 'post' ) + count_user_posts( $user_id , 'page' ) + count_user_posts( $user_id , 'topic' ) + count_user_posts( $user_id , 'reply' );
			}

			set_transient( 'src_member_list', $users, self::CACHE_TIME * MINUTE_IN_SECONDS );
		} 

		$content = '
				<table>
					<tr>
						<th>' . esc_html__( 'Name', 'src' ) . '</th>
						<th>' . esc_html__( 'Posts', 'src' ) . '</th>
						<th>' . esc_html__( 'Joined', 'src' ) . '</th>
					</tr>';

		foreach ( $users as $row_number => $row ) {
			$content .= '
						<tr>
							<td><a href="' . esc_url( $row['url'] ) . '">' . esc_html( $row['name'] ) . '</a></td>
							<td>' . esc_html( $row['post_count'] ) . '</td>
							<td>' . esc_html( date( 'l jS F Y', $row['registered'] ) ) . '</td>
						</tr>';

		}

		$content .= '
			</table>';

		return $content;
	}

}
