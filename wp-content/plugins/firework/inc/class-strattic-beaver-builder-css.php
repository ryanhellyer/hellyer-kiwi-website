<?php

/**
 * Temporary hack to accommodate Beaver Builder CSS.
 * This was required due to our new uploads directory handling system not rewriting URLs within files.
 * This will slow down page loads due to copying files on every page load (when Beaver Builder is activated)
 * 
 * @copyright Copyright (c), Strattic
 * @since 2.0
 */
class Strattic_Beaver_Builder_CSS {

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'init' ) );

	}

	/**
	 */
	public function init() {

		if ( class_exists( 'FLBuilderModel' ) ) {
			add_filter( 'fl_builder_get_cache_dir', array( $this, 'change_cache_dir' ) );
		}

	}

	/**
	 * Changing the cache directory.
	 * This folder is outside of the uploads folder, and therefore doesn't.
	 */
	public function change_cache_dir( $original_args ) {

		$dir_name = '/strattic-beaver-builder/';

		$args = array(
			'path' => WP_CONTENT_DIR . $dir_name,
			'url' => content_url() . $dir_name,
		);

		// Copy the original file to the new file
		$this->recurse_copy( $original_args['path'], $args['path'] );

		return $args;
	}

	public function recurse_copy( $src, $dst ) {
		$dir = opendir( $src );
		@mkdir( $dst );
		while( false !== ( $file = readdir( $dir ) ) ) {
			if ( ( $file != '.' ) && ( $file != '..' ) ) {
				if ( is_dir( $src . '/' . $file ) ) {
					$this->recurse_copy( $src . '/' . $file, $dst . '/' . $file ); 
				}
				else {
					copy( $src . '/' . $file, $dst . '/' . $file ); 
				}
			}
		}
		closedir( $dir );
	}

}
