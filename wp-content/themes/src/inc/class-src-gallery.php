<?php

/**
 * Gallery.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Gallery extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_shortcode( 'src-gallery-uploader', array( $this, 'uploader' ) );

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Init.
	 */
	public function init() {

		if ( isset( $_FILES['somefile']['tmp_name'] ) ) {


//			$file = file_get_contents( $_FILES['somefile']['tmp_name'] );

			require_once ( ABSPATH . 'wp-admin/includes/file.php' );
$file =$_FILES['somefile'];

$overrides = array( 'test_form' => false);
			$result = wp_handle_upload( $file, $overrides );


$filetype = wp_check_filetype( basename( $result['file'] ), null );

			print_r( $result );
			die;
		}

	}

	public function uploader() {

		$content = '
		<form method="POST" action="" enctype="multipart/form-data">
			<input name="somefile" type="file" />
			<input type="submit" value="Submit" />
		</form>';

		return $content;
	}

}
