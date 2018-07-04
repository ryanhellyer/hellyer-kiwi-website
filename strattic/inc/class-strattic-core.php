<?php

/**
 * Strattic Core methods.
 * 
 * @copyright Strattic 2018
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Strattic_Core {

	const PER_PAGE = 100;
	const MEMORY_LIMIT = 1024;
	const TIME_LIMIT = HOUR_IN_SECONDS;

	/**
	 * Get the current page URL.
	 *
	 * @access  protected
	 * @return  string  The URL
	 */
	protected function get_current_url() {

		$url = 'http';
		if ( is_ssl() ) {
			$url .= 's';
		}
		$url .= '://';
		$url .= $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];

		return $url;
	}

	/**
	 * Get the current page URL.
	 *
	 * @access  protected
	 * @return  string  The URL path
	 */
	protected function get_current_path() {

		$url = $this->get_current_url();
		$path = str_replace( home_url(), '', $url );

		return $path;
	}


	/**
	 * The menu displayed at the bottom of each admin page.
	 */
	public function the_horizontal_menu() {

		echo '<br /><br /><br />';

		echo '<p>';

		$items = array(
			'strattic'                    => esc_html__( 'Publish', 'strattic' ),
			//'strattic-search-settings' => esc_html__( 'Search', 'strattic' ),
			'manual-links'                => esc_html__( 'Manual links', 'strattic' ),
			'discovered-links'            => esc_html__( 'Discovered links', 'strattic' ),
			'strattic-string-replacement' => esc_html__( 'String Replacement', 'strattic' ),
		);

		foreach ( $items as $slug => $title ) {

			if ( isset( $done ) ) {
				echo ' | ';
			}

			echo '<a href="' . esc_url( admin_url( 'admin.php?page=' . $slug ) ) . '">';
			if ( $slug === $_GET[ 'page' ] ) {
				echo '<strong>';
			}

			echo esc_html( $title );

			if ( $slug === $_GET[ 'page' ] ) {
				echo '</strong>';
			}
			echo '</a>';

			$done = true;

		}

		echo '</p>';
	}

}
