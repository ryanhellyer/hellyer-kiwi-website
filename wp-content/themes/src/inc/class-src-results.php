<?php

/**
 * Results.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Results extends SRC_Core {

	const RESULT_KEY = 'result';

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		$this->keys = array(
			'driver',
			'hours',
			'minutes',
			'seconds',
			'laps-completed',
			'time', // Options for seconds behind, laps behind or reason for retirement
			'consistency',
			'laps-led',
			'note', // Penalties or general comments can be left here
		);

		// Add action hooks
		add_action( 'init',           array( $this, 'init' ) );
		add_action( 'admin_footer',   array( $this, 'scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post',      array( $this, 'save' ), 10, 2 );

	}

	/**
	 * Init.
	 */
	public function init() {

		$post_types = array(
			'results' => array(
				'public' => true,
				'label'  => 'Results',
				'supports' => array( 'title', 'thumbnail' )
			),
		);

		foreach ( $post_types as $post_type => $args ) {
			register_post_type( $post_type, $args );
		}

	}

	/**
	 * Add admin metabox.
	 */
	public function add_metabox() {
		add_meta_box(
			'_result', // ID
			__( 'Enter results here', 'src' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			'results', // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'default'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the admin page.
	 */
	public function meta_box() {

		// If brand new post
		if ( ! isset( $_GET['season'] ) && ! isset( $_GET['post'] ) ) {
			$this->select_event();
		} else {
			$this->enter_results();
		}

if ( isset( $_POST['post'] ) ) {
echo '<textarea style="font-size:10px;font-family:monospace;">'.print_r( get_post_meta( $_POST['post'] ), true ) . '</textarea>';
}
	}

	/**
	 * Outputting the event selector.
	 * User selects the event they want to provide results for here first.
	 */
	public function select_event() {

		echo '<style>#publish {display:none;}</style>';

		echo '<h3>Select which season, round and event/session</h3>';

		$seasons = get_posts(
			array(
				'post_type' => 'season',
			)
		);

		if ( is_array( $seasons ) ) {
			foreach ( $seasons as $key => $season ) {
				echo '<p>';
				echo '<h3>' . esc_html( $season->post_title ) . '</h3>';

				$events = get_post_meta( $season->ID, 'event', true );

				echo '<ol>';
				foreach ( $events as $round_number => $event ) {
					echo '<li>';
					echo esc_html( $event['track_name'] );

					echo '<ul>';
					foreach ( $this->event_types() as $name => $desc ) {

						echo '<li>';

						$key_slug = 'event_' . sanitize_title( $name ) . '_timestamp';
						if ( isset( $event[$key_slug] ) && '' != $event[$key_slug] ) {

							$url = get_admin_url() . 'post-new.php?post_type=results';
							$url = add_query_arg( 'season', sanitize_title( $season->post_title ), $url );
							$url = add_query_arg( 'round', ( $round_number + 1 ), $url );								
							$url = add_query_arg( 'name', sanitize_title( $name ), $url );

							echo '<li>';
							echo '<a href="' . esc_url( $url ) . '">';
							echo ' ' . esc_html( $name );
							echo '</a>';
							echo '</li>';

						}

					}
					echo '</ul>';

					echo '</li>';
				}
				echo '</ol>';

				echo '</p>';
			}
		}

	}

	/**
	 * Displays inputs for storing results for the selected event.
	 */
	public function enter_results() {

		if ( isset( $_GET['season'] ) ) {
			$season = esc_html( $_GET['season'] );
			update_post_meta( get_the_ID(), '_season', $season );
		} else {
			$season = get_post_meta( get_the_ID(), '_season', true );
		}

		if ( isset( $_GET['name'] ) ) {
			$name = esc_html( $_GET['name'] );
			update_post_meta( get_the_ID(), '_name', $name );
		} else {
			$name = get_post_meta( get_the_ID(), '_name', true );
		}

		if ( isset( $_GET['round'] ) ) {
			$round = esc_html( $_GET['round'] );
			update_post_meta( get_the_ID(), '_round', $round );
		} else {
			$round = get_post_meta( get_the_ID(), '_round', true );
		}

		echo '
		<p>
			<label>' . esc_html__( 'Season', 'src' ) . '</label>
			<input type="text" name="season" value="' . esc_attr( $season ) . '" />
			 &nbsp; 
			<label>' . esc_html__( 'Name', 'src' ) . '</label>
			<input type="text" name="name" value="' . esc_attr( $name ) . '" />
			 &nbsp; 
			<label>' . esc_html__( 'Round number', 'src' ) . '</label>
			<input type="text" name="round" value="' . esc_attr( $round ) . '" />
		</p>';

		/**
		 * Getting event details.
		 * And confirming that submitted event data is correct.
		 */
		$seasons = get_posts(
			array(
				'post_type' => 'season',
			)
		);

		if ( is_array( $seasons ) ) {
			foreach ( $seasons as $key => $the_season ) {
				if ( $season === sanitize_title( $the_season->post_title ) ) {
					$season_title = $the_season->post_title;

					$events = get_post_meta( $the_season->ID, 'event', true );
					foreach ( $events as $round_number => $event ) {
						if ( (string) ( $round_number + 1 ) === $round ) {
							$actual_round_number = $round_number + 1;
							$track_name = $event['track_name'];

							foreach ( $this->event_types() as $name => $desc ) {
								if ( sanitize_title( $name ) == $name ) {
									$event_name = $name;
								}
							}

						}

					}

				}

			}
		}

		if ( ! isset( $_GET['post'] ) ) {
			echo '<script>
			var result_title = document.getElementById("title");
			result_title.value = "' . sprintf( __( '%s results', 'src' ), $event_name ) . ': Round ' . esc_html( $actual_round_number ) . ' ' . esc_html( $season_title ) . ' ' . __( 'at', 'src' ) . ' ' . esc_html( $track_name ) . '";
			</script>';
		}

		?>

		<table class="wp-list-table widefat plugins">
			<thead>
				<tr>
					<th>Driver</th>
					<th>Time</th>
					<th>Laps led</th>
					<th>Consistency</th>
					<th>Note</th>
					<th></th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<th>Driver</th>
					<th>Time</th>
					<th>Laps led</th>
					<th>Consistency</th>
					<th>Note</th>
					<th></th>
				</tr>
			</tfoot>

			<tbody id="add-rows"><?php
/*
			'hours',
			'minutes',
			'seconds',
			'laps-completed',
			'time', // Options for seconds behind, laps behind or reason for retirement
			'consistency',
			'laps-led',
			'note', // Penalties or general comments can be left here
*/

			// Grab options array and output a new row for each setting
			$result = get_post_meta( get_the_ID(), '_' . self::RESULT_KEY, true );
			if ( is_array( $result ) ) {
				foreach( $result as $key => $value ) {
					echo $this->get_row( $value );
				}
			}

			// Add a new row by default
			echo $this->get_row();

			?>
			</tbody>
		</table>

		<input type="button" id="add-new-row" value="<?php _e( 'Add new row', 'plugin-slug' ); ?>" />
		<input type="hidden" name="result-nonce" value="<?php echo esc_attr( wp_create_nonce( __FILE__ ) ); ?>"><?php
	}

	/**
	 * Get a single table row.
	 * 
	 * @param  string  $value  Option value
	 * @return string  The table row HTML
	 */
	public function get_row( $value = '' ) {

		if ( ! is_array( $value ) ) {
			$value = array();
		}

		foreach (
			$this->keys
			as $key
		) {
			if ( ! isset( $value[ $key ] ) ) {
				$value [ $key ] = '';
			}
		};

		$options = '<option selected disabled>' . esc_html__( 'Select a driver', 'src' ) . '</option>';
		foreach ( $this->get_src_users() as $user_id => $name ) {
			if ( $value['driver'] == $user_id ) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			$options .= '<option' . $selected. ' value="' . esc_attr( $user_id ) . '">' . esc_html( $name ) . '</option>';
		}

		// Create the required HTML
		$row_html = '

					<tr class="sortable inactive">
						<td style="width:18%">
							<select name="' . esc_attr( self::RESULT_KEY ) . '[driver][]">' . $options . '</select>
						</td>
						<td style="width:16%">
							<input class="small-text" style="width:40px" type="number" name="' . esc_attr( self::RESULT_KEY ) . '[hours][]" value="' . esc_attr( $value['hours'] ) . '" />
							<label>Hours</label>
							<br />
							<input class="small-text" style="width:50px" type="number" name="' . esc_attr( self::RESULT_KEY ) . '[minutes][]" value="' . esc_attr( $value['minutes'] ) . '" />
							<label>Minutes</label>
							<br />
							<input class="small-text" style="width:50px" type="number" name="' . esc_attr( self::RESULT_KEY ) . '[seconds][]" value="' . esc_attr( $value['seconds'] ) . '" />
							<label>Seconds</label>
						</td>
						<td style="width:10%">
							<input type="checkbox" name="' . esc_attr( self::RESULT_KEY ) . '[laps-led][]" />
						</td>
						<td style="width:10%">
							<input type="text" name="' . esc_attr( self::RESULT_KEY ) . '[consistency][]" />
						</td>
						<td>
							<textarea style="width:100px;height:100px;" name="' . esc_attr( self::RESULT_KEY ) . '[note][]"></textarea>
						</td>
					</tr>';

		// Strip out white space (need on line line to keep JS happy)
		$row_html = str_replace( '	', '', $row_html );
		$row_html = str_replace( "\n", '', $row_html );

		// Return the final HTML
		return $row_html;
	}

	/**
	 * Output scripts into the footer.
	 * This is not best practice, but is implemented like this here to ensure that it can fit into a single file.
	 */
	public function scripts() {

		// Bail out if not on results page
		if ( 'results' !== get_post_type() ) {
			return;
		}

		?>
		<style>
		.read-more-text {
			display: none;
		}
		.sortable .toggle {
			display: inline !important;
		}
		</style>
		<script>

			jQuery(function($){ 

				/**
				 * Adding some buttons
				 */
				function add_buttons() {

					// Loop through each row
					$( ".sortable" ).each(function() {

						// If no input field found with class .remove-setting, then add buttons to the row
						if(!$(this).find('input').hasClass('remove-setting')) {

							// Add a remove button
							$(this).append('<td style="width:3%;"><input type="button" class="remove-setting" value="&times;" /></td>');

							// Remove button functionality
							$('.remove-setting').click(function () {
								$(this).parent().parent().remove();
							});

						}

					});

				}

				// Create the required HTML (this should be added inline via wp_localize_script() once JS is abstracted into external file)
				var html = '<?php echo $this->get_row( '' ); ?>';

				// Add the buttons
				add_buttons();

				// Add a fresh row on clicking the add row button
				$( "#add-new-row" ).click(function() {
					$( "#add-rows" ).append( html ); // Add the new row
					add_buttons(); // Add buttons tot he new row
				});

				// Allow for resorting rows
				$('#add-rows').sortable({
					axis: "y", // Limit to only moving on the Y-axis
				});

 			});

		</script><?php
	}

	/**
	 * Save opening times meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function save( $post_id, $post ) {

		// Bail out if not processing results
		if ( 'results' != get_post_type() || ! isset( $_POST['result-nonce'] ) ) {
			return $post;
		}

		// Do nonce security check
		if ( ! wp_verify_nonce( $_POST['result-nonce'], __FILE__ ) ) {

			return $post;
		}

		$total = count(   $_POST['result']['driver']    );

		$count = 0;
		$result = array();
		while ( $count < $total ) {

			foreach ( $this->keys as $key ) {
				if ( isset( $_POST['result'][$key][$count] ) ) {
					$result[$count][$key] = wp_kses_post( $_POST['result'][$key][$count] );
				} else {
					$result[$count][$key] = '';
				}
			}

			$count++;
		}

		update_post_meta( $post_id, '_' . self::RESULT_KEY, $result );

	}

}
