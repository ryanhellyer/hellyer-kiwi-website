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

		// Add filters
		add_filter( 'the_content', array( $this, 'attachment_info' ) );
		add_filter( 'prepend_attachment', array( $this, 'attachment_content' ) );

		// Add action hooks
		add_action( 'template_redirect', array( $this, 'init' ) );
		add_action( 'undiecar_after_content', array( $this, 'attachment_footer' ) );

		// Add shortcodes
		add_shortcode( 'undiecar_gallery_uploader', array( $this, 'uploader' ) );
		add_shortcode( 'undiecar_gallery', array( $this, 'the_main_gallery' ) );
		add_shortcode( 'undiecar_season_gallery', array( $this, 'season_gallery' ) );

		// Add API routes
		add_action( 'rest_api_init', function () {
			register_rest_route( 'undiecar/v1', '/events_in_season', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'events_in_season' ),
			) );
		} );

	}

	/**
	 * Add the attachment info.
	 *
	 * @param  string  $content  The content
	 * @return string  The modified content
	 */
	public function attachment_info( $content ) {

		if ( ! is_attachment() ) {
			return $content;
		}

		$attachment_id = get_the_ID();
		$event_id = wp_get_post_parent_id( $attachment_id );
		if ( 'event' !== get_post_type( $event_id ) ) {
			return $content;
		}

		$season_id = get_post_meta( $event_id, 'season', true );

		$content .= '<p>';

		$content .= esc_html__( 'Season', 'src' ) . ': <a href="' . esc_url( get_permalink( $season_id ) ) . '">' . get_the_title( $season_id ) . '</a>';
		$content .= '<br />';
		$content .= esc_html__( 'Event', 'src' ) . ': <a href="' . esc_url( get_permalink( $event_id ) ) . '">' . get_the_title( $event_id ) . '</a>';
		$content .= '<br />';
		$content .= esc_html__( 'Drivers', 'src' ) . ': ';

		$drivers = get_post_meta( $attachment_id, 'drivers', true );
		if ( is_array( $drivers ) ) {
			$drivers = array_unique( $drivers );
			$count = 0;
			foreach ( $drivers as $key => $driver_name ) {
				$count++;

				$driver_slug = sanitize_title( $driver_name );
				$content .= '<a href="' . esc_url( home_url() . '/member/' . $driver_slug . '/' ) . '">' . esc_html( $driver_name ) . '</a>';
				if ( $count !== count( $drivers ) ) {
					$content .= ', ';
				}

			}
		}

		$content .= '</p>';

		return $content;
	}

	/**
	 * The attachment content.
	 * Allows for setting a larger attachment page image size.
	 */
	public function attachment_content( $content ) {
		return '<p>' . wp_get_attachment_link( 0, 'large', false ) . '</p>';
	}

	/**
	 * Init.
	 */
	public function init() {

		// Get attachment author ID
		global $wp_query;
		if ( ! isset( $wp_query->post->post_author ) ) {
			return;
		}
		$attachment_author_id = $wp_query->post->post_author;

		// Quick security check
		if (
			! is_user_logged_in()
			||
			(
				isset( $_POST['src-gallery-nonce'] )
				&&
				! wp_verify_nonce( $_POST['src-gallery-nonce'], 'src-gallery-nonce' )
			)
			||
			! isset( $_POST['src-gallery-nonce'] )			
		) {
			return;
		}

		$event_id = absint( $_POST['undiecar-event'] );
		$description = wp_kses_post( $_POST['undiecar-description'] );

		// Upload all the thingz!!!
		if ( isset( $_FILES['gallery-file']['tmp_name'] ) ) {

			require_once ( ABSPATH . 'wp-admin/includes/file.php' );
			require_once ( ABSPATH . 'wp-admin/includes/image.php' );

			$file = $_FILES['gallery-file'];
			$overrides = array( 'test_form' => false);
			$result = wp_handle_upload( $file, $overrides );
			$file_name = $result['file'];

			$filetype = wp_check_filetype( basename( $result['file'] ), null );
			$wp_upload_dir = wp_upload_dir();
			$attachment = array(
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $file_name ), 
				'post_mime_type' => $filetype['type'],
				'post_title'     => $description,
				'post_content'   => '',
				'post_status'    => 'inherit',
				'post_author'    => get_current_user_id(),
			);
			$attachment_id = wp_insert_attachment( $attachment, $file_name, $event_id );

			update_post_meta( $attachment_id, 'gallery', true );

			// Generate the metadata for the attachment, and update the database record.
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file_name );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );

			$redirect_url = get_permalink( $attachment_id );
		}

		if (
			isset( $_FILES['gallery-file']['tmp_name'] )
			||
			(
				is_attachment()
				&&
				isset( $_POST['src-gallery-edit'] )
				&&
				(
					// Check the person has permission to edit the attachment
					$attachment_author_id === get_current_user_id()
					||
					is_super_admin()
				)
			)
		) {

			// Get attachment ID
			if ( isset( $_POST['src-gallery-edit'] ) ) {
				$attachment_id = get_the_ID();

				// Update attachment title
				$args = array(
					'ID'          => $attachment_id,
					'post_title'  => wp_kses_post( $description ),
					'post_parent' => $event_id
				);
				wp_update_post( $args );
				update_post_meta( $attachment_id, 'gallery', true );

			}

			// Add drivers meta
			$drivers = array();
			foreach ( $_POST['undiecar-driver'] as $key => $driver_name ) {
				if ( ''	!== $driver_name ) {
					$drivers[] = wp_kses_post( $driver_name );
				}

				// Add image ID to user meta
				$driver_slug = sanitize_title( $driver_name );
				$driver = get_user_by( 'login', $driver_slug );
				if ( isset( $driver->ID ) ) {
					$driver_id = absint( $driver->ID );

					$images = get_user_meta( $driver_id, 'images', true );
					if ( ! is_array( $images ) ) {
						$images = array();
					}
					$images[] = $attachment_id;
					$images =  array_unique( $images );

					update_user_meta( $driver_id, 'images', $images );
				}

			}
			update_post_meta( $attachment_id, 'drivers', $drivers );

		}

		// Redirect to attachment page
		if ( isset( $redirect_url ) ) {
			wp_redirect( $redirect_url, 302 );
		}

	}

	public function attachment_footer() {

		// Bail out if not on attachments page
		if ( ! is_attachment() ) {
			return;
		}

		previous_image_link( false, '<p class="alignleft button">&laquo; ' . __( 'Previous Image', 'src' ) . '</p>' );
		next_image_link( false, '<p class="alignright button">' . __( 'Next Image', 'src' ) . ' &raquo;</p>' );

		global $wp_query;
		if (
			(
				is_user_logged_in()
				&&
				isset( $wp_query->post->post_author )
				&&
				absint( $wp_query->post->post_author ) === get_current_user_id() // Need absint here coz wp_query isn't integer for some reason
			)
			||
			is_super_admin()
		) {
			echo $this->form_edit();
		}

	}

	public function uploader() {

		if ( ! is_user_logged_in() ) {
			return '<p>' . esc_html__( 'Need to be logged in to upload gallery images', 'src' ) . '</p>';
		}

		$content = '
		<form method="POST" action="" enctype="multipart/form-data">
			<p>
				<input name="gallery-file" type="file" />
			</p>

			' . $this->form_fields() . '

			<p>
				<br /><br />
				<input type="submit" value="' . esc_html__( 'Submit', 'src' ) . '" />
			</p>
		</form>';

		return $content;
	}

	public function form_edit() {

		$attachment_id = get_the_ID();

		// Don't allow editing if not on gallery image
		$attachment_id = get_the_ID();
		if (
			'1' !== get_post_meta( $attachment_id, 'gallery', true )
			&&
			! is_super_admin()
		) {
			return;
		}

		$content = '
		<form class="gallery-image-form" method="POST" action="">

			' . $this->form_fields() . '

			<p>
				<input type="submit" value="' . esc_html__( 'Submit', 'src' ) . '" />
			</p>

			<input name="src-gallery-edit" value="' . esc_attr( get_the_ID() ) . '" type="hidden" />
		</form>';

		return $content;
	}


	public function form_fields() {

		$content = wp_nonce_field( 'src-gallery-nonce', 'src-gallery-nonce', true, false );

		if ( is_attachment() ) {
			$description = get_the_title( get_the_ID() );
			$attachment_id = get_the_ID();
			$event_id = wp_get_post_parent_id( $attachment_id );
			$season_id = get_post_meta( $event_id, 'season', true );
		} else {
			$description = '';
			$event_id = '';
			$season_id = '';
		}

		$content .= '
			<p>
				<label>' . esc_html__( 'Description', 'src' ) . '</label>
				<input name="undiecar-description" type="text" value="' . esc_attr( $description ) . '" />
			</p>
			<p>
				<label>' . esc_html__( 'Season', 'src' ) . '</label>
				<select id="undiecar-season" name="undiecar-season">
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
				$the_season_id = get_the_ID();
				$season_title = get_the_title( get_the_ID() );
				$content .= '
						<option ' . selected( $the_season_id, $season_id, false ) . ' value="' . esc_attr( $the_season_id ) . '">' . esc_html( $season_title ) . '</option>';
			}
		}

		$content .= '
				</select>
			</p>

			<p id="undiecar-event-form-fields">
				<label>' . esc_html__( 'Event', 'src' ) . '</label>
				<select id="undiecar-event" name="undiecar-event">
					<option value="">None</option>';

		if ( '' !== $season_id ) {
			$args = array(
				'post_type'              => 'event',
				'posts_per_page'         => 100,
				'meta_key'               => 'season',
				'meta_value'             => $season_id,
				'no_found_rows'          => true,  // useful when pagination is not needed.
				'update_post_meta_cache' => false, // useful when post meta will not be utilized.
				'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
				'fields'                 => 'ids'
			);
			$events = new WP_Query( $args );
			if ( $events->have_posts() ) {
				while ( $events->have_posts() ) {
					$events->the_post();
					$the_event_id = get_the_ID();
					$event_title = get_the_title( get_the_ID() );
					$content .= "\n\t\t\t\t\t<option " . selected( $the_event_id, $event_id, false ) . ' value="' . esc_attr( $the_event_id ) . '">' . esc_html( $event_title ) . '</option>';
				}
			}
		}

		$content .= '
				</select>
			</p>

			<p id="undiecar-driver-form-fields">
				<label>' . esc_html__( 'Drivers', 'src' ) . '</label>
				<span id="undiercar-driver-form-input-fields">';

		if ( isset( $attachment_id ) ) {
			$drivers = get_post_meta( $attachment_id, 'drivers', true );
			if ( is_array( $drivers ) ) {

				$drivers = array_unique( $drivers );
				foreach ( $drivers as $key => $driver_name ) {
					if ( '' !== $driver_name ) {
						$content .= '
							<input name="undiecar-driver[]" type="text" value="' . esc_attr( $driver_name ) . '" />';
					}
				}

			}

		}

		$content .= '
					<input name="undiecar-driver[]" type="text" />
				</span>

				<button id="another-driver">' . esc_html__( 'Another driver', 'src' ) . '</button>
			</p>';

		return $content;
	}

	/**
	 * WP-JSON feed of events in a specific season.
	 *
	 * eg: https://undiecar.com/wp-json/undiecar/v1/events_in_season?season_id=666
	 *
	 * @param  array  $request  The request parameters
	 * @return array  The events data
	 */
	public function events_in_season( $request ) {

		$request_params = $request->get_query_params();
		if ( isset( $request_params['season_id'] ) ) {
			$season_id = $request_params['season_id'];
		} else {
			return;
		}

		$events = get_posts(
			array(
				'post_type'      => 'event',
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'no_found_rows'  => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_key'               => 'season',
				'meta_value'             => $season_id,
			)
		);

		return $events;
	}

	public function the_main_gallery( $args = array() ) {

if ( isset( $_GET['delete_cache'] ) ) {
delete_transient( 'undiecar_gallery' );
}

		if ( false === ( $undiecar_gallery = get_transient( 'undiecar_gallery' ) ) ) {

			$args = array(
				'posts_per_page'         => 500,
				'post_type'              => 'attachment',
				'post_status'            => 'inherit',
				'post_mime_type'         => 'image',
				'meta_key'               => 'gallery',
				'no_found_rows'          => true,  // useful when pagination is not needed.
				'update_post_meta_cache' => false, // useful when post meta will not be utilized.
				'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
				'fields'                 => 'ids'
			);

			$query = new WP_Query( $args );
			$undiecar_gallery = '[gallery columns="8" size="src-logo" ids="';
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$undiecar_gallery .= get_the_ID() . ',';
				}
				wp_reset_query();
			}

			$undiecar_gallery .= '"]';

			set_transient( 'undiecar_gallery', $undiecar_gallery, HOUR_IN_SECONDS );
		}

		return do_shortcode( $undiecar_gallery );
	}

	public function season_gallery( $gallery_args = array() ) {

		if ( ! isset( $gallery_args['season'] ) ) {
			return;
		}
		$season = $gallery_args['season'];

if ( isset( $_GET['delete_cache'] ) ) {
delete_transient( 'undiecar_gallery_' . $season );
}

		if ( false === ( $undiecar_gallery = get_transient( 'undiecar_gallery_' . $season ) ) ) {
			$undiecar_gallery = '[gallery orderby="post_date" order="DESC" columns="8" size="src-logo" ids="';

			// Get the season ID from the slug
			if ( $season !== '' ) {
				$args = [
					'post_type'      => 'season',
					'posts_per_page' => 1,
					'post_name__in'  => array( $season ),
					'fields'         => 'ids' 
				];
				$season_object = get_posts( $args );
				if ( ! isset( $season_object[0] ) ) {
					return;
				}
				$season_id = $season_object[0];

				// Loop through the events
				$events = src_get_races( $season_id );
				foreach ( $events as $event_id => $event_name ) {

					$args = array(
						'post_parent'            => $event_id,
						'posts_per_page'         => 500,
						'post_type'              => 'attachment',
						'post_status'            => 'inherit',
						'post_mime_type'         => 'image',
						'meta_key'               => 'gallery',
						'no_found_rows'          => true,  // useful when pagination is not needed.
						'update_post_meta_cache' => false, // useful when post meta will not be utilized.
						'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
						'fields'                 => 'ids'
					);

					$query = new WP_Query( $args );
					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();

							$undiecar_gallery .= get_the_ID() . ',';
						}
						wp_reset_query();
					}
				}
			} else {

				// If no season set, then grab all images without a parent event
				$args = array(
					'post_parent'            => 0,
					'posts_per_page'         => 500,
					'post_type'              => 'attachment',
					'post_status'            => 'inherit',
					'post_mime_type'         => 'image',
					'meta_key'               => 'gallery',
					'no_found_rows'          => true,  // useful when pagination is not needed.
					'update_post_meta_cache' => false, // useful when post meta will not be utilized.
					'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
					'fields'                 => 'ids'
				);

				$query = new WP_Query( $args );
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();

						$undiecar_gallery .= get_the_ID() . ',';
					}
					wp_reset_query();
				}

			}

			$undiecar_gallery .= '"]';

			set_transient( 'undiecar_gallery', 'undiecar_gallery_' . $season, HOUR_IN_SECONDS );
		}

		$gallery_html = do_shortcode( $undiecar_gallery );

		// Add a title if one is set.
		if (
			isset( $gallery_args['title'] )
			&&
			'' != $gallery_html
		) {
			$gallery_html = '<h3>' . esc_html( $gallery_args['title'] ) . '</h3>' . $gallery_html;
		}

		return $gallery_html;
	}

}
