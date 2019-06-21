<?php

/**
 * Strattic Stats.
 * Provides information about the current site.
 * 
 * @copyright Strattic 2018
 * @author Ryan Hellyer <ryan@strattic.com>
 */
class Strattic_Stats extends Strattic_Core {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		if ( '/strattic-stats/' === $this->get_current_path() ) {
			add_action( 'template_redirect', array( $this, 'dump_stats' ) );
		}

	}

	/**
	 * Dump out the API contents.
	 *
	 * @return  array
	 */
	public function dump_stats() {

		header( 'HTTP/1.1 200 OK' );
		header( 'Content-Type: application/json' );

		echo json_encode( $this->get_stats() );

		die;
	}

	/**
	 * Get the size of a directory.
	 *
	 * @param  string  $directory  The directory path
	 * @return int     The size (in bytes) of the directory
	 */
	private function get_directory_size( $directory ) {
		$directory_size = 0;

		if ( ! is_dir( $directory ) ) {
			return false;
		};

		$files = scandir( $directory );
		if ( ! $files ) {
			return false;
		}
		$files = array_diff( $files, array( '.', '..' ) );

		foreach ( $files as $file ) {
			if ( is_dir( "$directory/$file" ) ) {
				$directory_size += $this->get_directory_size( "$directory/$file" );
			}else{
				$directory_size += @filesize( "$directory/$file" );
			}
		}

		return $directory_size;
	}

	/**
	 * Grabs list of stats about the WordPress installation.
	 *
	 * @global  object  $wp_version  The WordPress version number global
	 * @return  array   $stats       The array of stats
	 */
	public function get_stats() {
		global $wp_version;

		// Get directory sizes
		$wp_dirs = wp_upload_dir();
		$directory_sizes = array();

		if ( isset( $wp_dirs[ 'basedir' ] ) ) {
			$upload_directory = $wp_dirs[ 'basedir' ];
			$directory_sizes[ 'uploads_dir' ] = $this->get_directory_size( $upload_directory );
		}

		$directory_sizes[ 'total' ] = $this->get_directory_size( ABSPATH );
		$directory_sizes[ 'non_upload_dirs' ] = $directory_sizes[ 'total' ] - $directory_sizes[ 'uploads_dir' ];

		// Get comprehensive plugins list
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$active_plugins = get_option( 'active_plugins' );
		$all_plugins = get_plugins();
		foreach ( $all_plugins as $plugin_slug => $plugin ) {
			$plugin_slug = esc_html( $plugin_slug );

			// Sanitizing the strings
			foreach ( $plugin as $key => $value ) {
				$key = esc_html( $key );
				$all_plugins[ $plugin_slug ][ $key ] = wp_kses_post( $value );
			}

			// Set "Active" key
			if ( in_array ( $plugin_slug , $active_plugins ) ) {
				$all_plugins[ $plugin_slug ][ 'Active' ] = true;
			} else {
				$all_plugins[ $plugin_slug ][ 'Active' ] = false;
			}

		}

		// Combine stats
		$stats = array(
			'wordpress_version' => $wp_version,
			'wordpress_plugins' => $all_plugins,
			'directory_sizes'   => $directory_sizes,
		);

		$this->log_data( 'Stats requested' );

		return $stats;
	}

}