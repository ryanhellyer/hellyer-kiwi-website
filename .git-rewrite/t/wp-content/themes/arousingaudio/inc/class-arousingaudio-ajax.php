<?php

/**
 * Outputs AJAX content.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Arousing Audio
 * @since Arousing Audio 1.0
 */
class ArousingAudio_AJAX {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {
		//
		if ( isset( $_GET[ 'audio_id' ] ) ) {
			$id = absint( $_GET[ 'audio_id' ] );
			echo json_encode( arousingaudio_get_post( $id ) );
			die;
		}

	}

}
