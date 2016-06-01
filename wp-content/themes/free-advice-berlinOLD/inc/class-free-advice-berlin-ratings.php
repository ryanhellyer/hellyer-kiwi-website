<?php

/**
 * Handle ratings.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */
class Free_Advice_Berlin_Ratings {

	/**
	 * Constructor.
	 */
	public function __construct() {

		if ( isset( $_GET['rating-up'] ) || isset( $_GET['rating-down'] ) ) {
			$this->process_rating();
		}

		add_action( 'fab_after_content', array( $this, 'ratings_html' ) );
		add_action( 'wp_footer',         array( $this, 'script' ), 1 );
		add_action( 'add_meta_boxes',    array( $this, 'add_metabox' ) );
	}

	/**
	 * Add admin metabox.
	 */
	public function add_metabox() {
		add_meta_box(
			'ratings', // ID
			__( 'Ratings', 'plugin-slug' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			'page', // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the meta box.
	 */
	public function meta_box() {

		echo '
		<p>
			<strong>Thumbs up:</strong> ' . absint( get_post_meta( get_the_ID(), '_ratings_up', true ) ) . '
		</p>

		<p>
			<strong>Thumbs down:</strong> ' . absint( get_post_meta( get_the_ID(), '_ratings_down', true ) ) . '
		</p>
		';

	}

	public function process_rating() {

		if ( isset( $_GET['rating-up'] ) ) {
			$rating = 'up';
		}

		if ( isset( $_GET['rating-down'] ) ) {
			$rating = 'down';
		}

		$id = absint( $_GET['rating-' . $rating] );

		// Updating the current value
		$value = get_post_meta( $id, '_ratings_' . $rating, true );
		if ( '' == $value ) {
			$value = 0;
		}
		$value++;
		update_post_meta( $id, '_ratings_' . $rating, $value );

		echo 'Rating successful!';
		die;
	}

	public function ratings_html() {
		echo '
		<p id="thumbs" class="instruction"></p>';
	}

	public function script() {

		// Bail out if no ID set
		if ( '' == get_the_ID() ) {
			reurn;
		}

		echo '
<script>';
		if ( '' != get_the_ID() ) {
			echo "
var page_id = " . get_the_ID() .";";
		}
		echo "
var fab_home_url = '" . esc_url( home_url( ) ) . "';
</script>
";
	}

}
