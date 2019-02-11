<?php

/**
 * Events.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Videos extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {
		add_action( 'init',               array( $this, 'init' ) );
		add_shortcode( 'undiecar_videos', array( $this, 'shortcode' ) );
	}

	/**
	 * Init.
	 */
	public function init() {

		register_post_type(
			'video',
			array(
				'public'             => true,
				'publicly_queryable' => true,
				'label'              => esc_html__( 'Videos', 'src' ),
				'supports'           => array( 'title', 'editor', 'thumbnail' ),
				'menu_icon'          => 'dashicons-video-alt2',
			)
		);

	}

	public function shortcode() {

		$gallery = '<div class="gallery gallery-columns-8">';

		$args = array(
			'posts_per_page'         => 100,
			'post_type'              => 'video',
			'post_status'            => 'publish',
			'no_found_rows'          => true,  // useful when pagination is not needed.
			'update_post_meta_cache' => false, // useful when post meta will not be utilized.
			'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
			'fields'                 => 'ids'
		);
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$url = get_the_post_thumbnail_url( get_the_ID()	, 'src-four' );

				if ( '' == $url ) {
					$url = 'https://undiecar.com/files/video.png';
				}

				$gallery .= '
				<figure class="gallery-item">
					<div class="gallery-icon landscape">
						<a href="' . get_permalink( get_the_ID() ) . '">
							<img src="' . esc_url( $url ) . '" />
						</a>
					</div>
				</figure>';

			}
			wp_reset_query();
		}

		$gallery .= '</div>';

		return $gallery;
	}

}
