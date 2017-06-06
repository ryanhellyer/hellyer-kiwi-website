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

	const CACHE_TIME = 0.1;

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


add_action( 'init', 'bla2' );
function bla2() {

	if ( !isset($_GET['bla'])) {
		return;
	}

	$array = array(
		'SSJ2Luigi' => '05.06.2016',
		'-=Wiesel=-' => '05.06.2016',
		'Bar0ni' => '07.28.2016',
		'Bene' => '06.01.2016',
		'Cliche_Au' => '11.06.2016',
		'clicman' => '04.08.2017',
		'Comstar' => '05.06.2016',
		'Edijs Batars' => '04.01.2017',
		'Elise Skinner' => '05.08.2017',
		'EpicRiman' => '05.28.2017',
		'fjhoekie' => '05.06.2016',
		'G-Drive' => '07.28.2016',
		'HakkxCore' => '09.04.2016',
		'Icebreaker' => '08.24.2016',
		'IGame23' => '06.03.2017',
		'Jacob Reid' => '04.09.2017',
		'Jopaku' => '03.05.2017',
		'Jur4iks87' => '04.02.2017',
		'kahel.grahf' => '12.17.2016',
		'Lorenz Dougherty' => '05.13.2017',
		'Major Metal' => '08.23.2016',
		'MarcosVieira' => '05.21.2017',
		'Michael Vincent' => '05.20.2017',
		'Miguel98' => '05.06.2016',
		'mika' => '03.05.2017',
		'nathan.danac' => '05.21.2017',
		'Paul23' => '05.06.2016',
		'pepper_F1' => '03.11.2017',
		'Robert14BVB' => '10.15.2016',
		'Roextro' => '05.15.2016',
		'ryan' => '03.22.2017',
		'ryant' => '05.19.2016',
		'Ryo Watanabe' => '05.07.2016',
		'SandroDS-Motorsport' => '01.01.2017',
		'siddk99' => '02.26.2017',
		'Speedylu' => '05.06.2016',
		'stanleyslate' => '79 04.23.2017',
		'Tangofoxx' => '05.05.2016',
		'TheManxMissile' => '05.06.2016',
		'trekbmc' => '05.06.2016',
		'unclenewy' => '05.28.2017',
		'Uwe Hellwig' => '07.30.2016',
		'victorlee' => '04.21.2017',
		'waghlon' => '05.23.2016',
		'Will Tyrer' => '01.21.2017',
		'xRacer11' => '04.03.2017',
	);


	$raw_users = get_users(
		array(
			'number' => 200,
		)
	);
	$users = array();
	foreach( $raw_users as $row_number => $row ) {
//		echo $row->data->ID . ': ' . $row->data->display_name . "\n";
		print_r( $row );

		foreach ( $array as $name => $date ) {
			if ( $name === $row->data->display_name ) {
				$date_exploded = explode( '.', $date );
				$date = $date_exploded[2] . '-' . $date_exploded[0] . '-' . $date_exploded[1] . ' 00:00:00';
echo $row->data->display_name . ': ' . $date . "\n";
				wp_update_user( array( 'ID' => $row->data->ID, 'user_registered' => $date ) );
			}
		}


//		print_r( get_user_meta( $row->data->ID ) );
//		die;
//		user_registered
	}


	die;
}
