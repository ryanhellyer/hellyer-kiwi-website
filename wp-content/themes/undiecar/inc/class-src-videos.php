<?php

use RestCord\DiscordClient;

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
				'supports'           => array( 'title', 'editor' ),
				'menu_icon'          => 'dashicons-video-alt2',
			)
		);

	}

}
