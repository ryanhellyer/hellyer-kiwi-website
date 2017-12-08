<?php

/**
 * Gallery.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Undycar Theme
 * @since Undycar Theme 1.0
 */
class SRC_Gallery extends SRC_Core {

	public $spacer = '-theevent-';

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'template_redirect', array( $this, 'init' ) );
		add_action( 'src_after_content', array( $this, 'attachment_footer' ) );

		// Add shortcodes
		add_shortcode( 'undiecar_gallery_uploader', array( $this, 'uploader' ) );

	}

	/**
	 * Init.
	 */
	public function init() {

		// Quick security check
		if (
			! is_user_logged_in()
			||
			(
				isset( $_POST['src-gallery-nonce'] )
				&&
				false === wp_verify_nonce( $_POST['src-gallery-nonce'], 'src-gallery-nonce' )
			)
		) {
			return;
		}

		// Upload all the thingz!!!
		if ( isset( $_FILES['gallery-file']['tmp_name'] ) ) {

			require_once ( ABSPATH . 'wp-admin/includes/file.php' );
			$file = $_FILES['gallery-file'];

			$overrides = array( 'test_form' => false);
			$result = wp_handle_upload( $file, $overrides );
			$file_name = $result['file'];

			$post_title = $_POST['post_title'];

			$filetype = wp_check_filetype( basename( $result['file'] ), null );
			$wp_upload_dir = wp_upload_dir();
			$attachment = array(
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $file_name ), 
				'post_mime_type' => $filetype['type'],
				'post_title'     => wp_kses_post( $post_title ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);
			$attachment_id = wp_insert_attachment( $attachment, $file_name, get_the_ID() );

			// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			// Generate the metadata for the attachment, and update the database record.
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file_name );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );

			set_post_thumbnail( get_the_ID(), $attachment_id );
		}

		if (
			isset( $_FILES['gallery-file']['tmp_name'] )
			||
			isset( $_POST['src-gallery-edit'] )
		) {

			// Get attachment ID
			if ( isset( $_POST['src-gallery-edit'] ) ) {
				$attachment_id = get_the_ID();

				// Update attachment title
				$post_title = $_POST['post_title'];
				$args = array(
					'ID'           => $attachment_id,
					'post_title'   => wp_kses_post( $post_title ),
				);
				wp_update_post( $args );

			}

			$event_exploded = explode( $this->spacer, $_POST['event'] );
			if ( isset( $event_exploded[0] ) && isset( $event_exploded[1] ) ) {
				$event = array(
					'season' => wp_kses_post( $event_exploded[0] ),
					'event'  => wp_kses_post( $event_exploded[1] ),
				);
				update_post_meta( $attachment_id, 'src_event', $event );
			}

			$drivers = array();
			if ( isset( $_POST['drivers'] ) && is_array( $_POST['drivers'] ) ) {
				delete_user_meta( $driver_id, 'gallery_image' ); // Delete user meta to avoid repeated duplicates on update
				foreach ( $_POST['drivers'] as $key => $driver_id ) {
					$driver_id = absint( $driver_id );
					$drivers[] = $driver_id;

					// Stash in users meta here
					add_user_meta( $driver_id, 'gallery_image', $attachment_id );

				}

				update_post_meta( $attachment_id, 'src_drivers', $drivers );
			}

		}

	}

	public function attachment_footer() {

		// Bail out if not on attachments page
		if ( ! is_attachment() ) {
			return;
		}

		echo '<p>';

		// The event
		$event = get_post_meta( get_the_ID(), 'src_event', true );
		if ( is_array( $event ) ) {

			$args = array(
				'name'                   => $event['season'],
				'post_type'              => 'season',
				'posts_per_page'         => 100,
				'no_found_rows'          => true,  // useful when pagination is not needed.
				'update_post_meta_cache' => false, // useful when post meta will not be utilized.
				'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
				'fields'                 => 'ids',
			);

			$seasons = new WP_Query( $args );
			if ( $seasons->have_posts() ) {
				while ( $seasons->have_posts() ) {
					$seasons->the_post();

					// Event is stored as sanitized data from option field, so need to work out actual event name
//					$events = src_get_events( src_get_the_slug() );
					foreach ( $events as $key => $event_data ) {
						if ( sanitize_title( $event_data['name'] ) == $event['event'] ) {
							$event_name = $event_data['name'];
						}
					}

					esc_html_e( 'Event', 'src' );
					echo ': ';
					the_title();
					echo ' &ndash; ';
					echo esc_html( $event_name );
					echo '. ';

				}
			}

			wp_reset_postdata(); // Prevents get_the_ID() from screwing up for drivers

		}

		// The drivers
		$drivers = get_post_meta( get_the_ID(), 'src_drivers', true );
		if ( is_array( $drivers ) ) {

			$string = '';
			foreach ( $drivers as $key => $driver_id ) {

				$user = get_user_by( 'ID', $driver_id );

				if ( isset( $user->data->user_login ) ) {

					// Set the name displayed
					$user_id = $user->data->ID;
					$name = $user->data->user_login;
					if ( isset( $user->data->display_name ) ) {
						$name = $user->data->display_name;
					}

					if ( 0 !== $key ) {
						$string .= ', ';
					}

					$url = bbp_get_user_profile_url( $user_id );
					if ( '' !== $url ) {
						$string .= '<a href="' . esc_url( $url ) . '">' . esc_html( $name ) . '</a>';
					} else {
						$string .= $name;
					}

				}


			}

			echo esc_html__( 'Drivers', 'src' ) . ': ' . $string /* Already escaped */;

		}


		if ( is_user_logged_in() ) {
			echo ' &nbsp; <a style="display:inline;float:none;" id="add-a-photo" href="#">(' . esc_html__( 'edit image meta', 'src' ) . ')</a>';
		}

		echo '<p>';

		previous_image_link( false, '<p class="alignleft button">&laquo; ' . __( 'Previous Image', 'src' ) . '</p>' );
		next_image_link( false, '<p class="alignright button">' . __( 'Next Image', 'src' ) . ' &raquo;</p>' );

		if ( is_user_logged_in() ) {
			echo '<div id="gallery-uploader">


<h1>NEED TO ENSURE THAT ONLY POST AUTHOR AND EDITORS CAN EDIT ATTACHMENTS</h1>



' . $this->form_edit() . '</div>';
		}

	}

	public function uploader() {

		$content = '
		<form method="POST" action="" enctype="multipart/form-data">
			' . $this->form_fields() . '
			<p>
				<input name="gallery-file" type="file" />
			</p>
			<p>
				<input type="submit" value="' . esc_html__( 'Submit', 'src' ) . '" />
			</p>
		</form>';

		return $content;
	}

	public function form_edit() {

		$content = '
		<form method="POST" action="">
			' . $this->form_fields() . '
			<input name="src-gallery-edit" value="' . esc_attr( get_the_ID() ) . '" type="hidden" />
			<p>
				<input type="submit" value="' . esc_html__( 'Submit', 'src' ) . '" />
			</p>
		</form>';

		return $content;
	}


	public function form_fields() {
return 'THE FORM FIELDS';
		$content = wp_nonce_field( 'src-gallery-nonce', 'src-gallery-nonce', true, false );

		$content .= '
			<p>
				<label>' . esc_html__( 'Title', 'src' ) . '</label>
				<input name="post_title" type="text" />
			</p>
			<p>
				<label>' . esc_html__( 'Race', 'src' ) . '</label>
				<select name="event">
					<option value="">None</option>';

		$args = array(
			'post_type'              => 'season',
			'posts_per_page'         => 100,
			'no_found_rows'          => true,  // useful when pagination is not needed.
			'update_post_meta_cache' => false, // useful when post meta will not be utilized.
			'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
			'fields'                 => 'ids'
		);

		$seasons = new WP_Query( $args );
		if ( $seasons->have_posts() ) {
			while ( $seasons->have_posts() ) {
				$seasons->the_post();

//				$season_slug = src_get_the_slug();

				// Loop through each event in that season
				foreach ( src_get_events( $season_slug ) as $key => $event ) {
					$name = $event['name'];

					$content .= '<option value="' . sanitize_title( $season_slug . $this->spacer . $name ) . '">' . esc_html( get_the_title() . ': ' . $name ) . '</option>';

				}

			}
		}

		$content .= '
				</select>
			</p>
			<p>
				<label>' . esc_html__( 'Drivers', 'src' ) . '</label>
				<select name="drivers[]" multiple="multiple">';

		foreach ( src_get_drivers_from_all_seasons() as $id => $name ) {
			$content .= '<option value="' . esc_attr( $id ) . '">' . esc_html( $name ) . '</option>';
		}

		$content .= '
				</select>
			</p>';

		return $content;
	}

}
