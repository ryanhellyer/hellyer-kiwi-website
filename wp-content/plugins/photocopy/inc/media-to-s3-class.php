<?php

/*
 * Move full-sized file to Amazon S3 during upload process
 *
 * @author Ryan Hellyer <ryan@pixopoint.com>
 * @since 1.0
 */
class Media_To_S3 {

	/*
	 * Class constructor
	 *
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'add_attachment',               array( $this, 'upload_attachment' ) );
		add_action( 'image_make_intermediate_size', array( $this, 'upload_intermediate_sizes' ) );
		add_action( 'admin_notices',                array( $this, 'warnings' ) );
	}

	/*
	 * Warning messages
	 * Warns if Curl is not installed or if AWS constants aren't set yet
	 *
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 */
	public function warnings() {

		$warning = '';
		if ( ! defined( 'AWS_ACCESS_KEY' ) ) {
			$warning .= '<strong>Warning:</strong> You have not added your Amazon S3 Access key<br />';
		}
		if ( ! defined( 'AWS_SECRET_KEY' ) ) {
			$warning .= '<strong>Warning:</strong> You have not added your Amazon S3 Secret key<br />';
		}
		if ( ! defined( 'AWS_BUCKET_NAME' ) ) {
			$warning .= '<strong>Warning:</strong> You have not added your Amazon S3 bucket name<br />';
		}
		/* REMOVED DUE TO IT CAUSING FAILURE ON SOME SERVERS - NEEDS ADDED BACK AT SOME POINT ONCE BUGS IRONED OUT
		if ( ! extension_loaded( 'curl' ) && ! @dl( PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll' ) ) {
			$warning .= '<strong>Warning:</strong> You do not have Curl installed. Uploads can not be sent to Amazon S3 until this is enabled on your server.';
		}
		*/

		// Add warning wrapper (makes it appear red and important in the WP admin panel)
		if ( '' != $warning ) {
			$warning = '<div class="error"><p>' . $warning;
			$warning .= '</p></div>';
		}

		echo $warning;
	}

	/*
	 * Process full-sized file during upload process
	 *
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 * @param integer $post_id The ID of the attachment
	 */
	public function upload_attachment( $post_id ) {

		$file_dir = get_attached_file( $post_id ); // Grab file path
		$file = $this->_get_file_name( $file_dir ); // Grab array with file path and name

		// w00p w00p! Sending file to S3 :)
		$this->_send_to_s3( $file['path'], $file['name'] );

		return $post_id;
	}

	/*
	 * Process intermediate-sized files during upload process
	 *
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 * @param string $file_path The path to the file
	 */
	public function upload_intermediate_sizes( $file_path ) {

		$file = $this->_get_file_name( $file_path );
		$this->_send_to_s3( $file['path'], $file['name'] );

		return $file_path;
	}

	/*
	 * Get file name for sending to S3
	 * Note: Amazon S3 file names also include the full directory path
	 *
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 * @return $array File name and file path
	 * @param string $file_location The file location, may be either the file path or file URL
	 * @access private
	 */
	private function _get_file_name( $file_location ) {

		// Grab the uploads folder
		$uploads_location = wp_upload_dir();
		$upload_dir = $uploads_location['basedir'] . '/';
		$upload_url = $uploads_location['baseurl'] . '/';

		// If URL, then convert to path
		$initial = substr( $file_location, 0, 7 );
		if ( 'http://' == $initial || 'https://' == $initial ) {
			$file_path = str_replace( $upload_url, $upload_dir, $file_location ); // Convert URL to path
		} else {
			$file_path = $file_location;
		}

		// Create file name for Amazon S3
		$file_name = str_replace( $upload_dir, '', $file_path ); // File for upload (note: S3 file names include the directory structure)

		// Add blog ID for multi-site setups
		if ( is_multisite() ) {
			global $blog_id; // Blog ID for multi-site
			$file_name = $blog_id . '/files/' . $file_name;
		}

		// return an array with both file name and file path
		return array(
			'name' => $file_name,
			'path' => $file_path,
		);
	}

	/*
	 * Send file to S3
	 *
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 * @param string $file_path The path to the file being sent
	 * @param string $file_name The new location of the file on Amazon S3
	 * @access private
	 */
	private function _send_to_s3( $file_path, $file_name ) {

		// Instantiate the class
		$s3 = new S3( AWS_ACCESS_KEY, AWS_SECRET_KEY );
		$s3->putObjectFile( $file_path, AWS_BUCKET_NAME, $file_name, S3::ACL_PUBLIC_READ );

	}
}