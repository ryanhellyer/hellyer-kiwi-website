<?php

use voku\helper\HtmlMin;

/**
 * Minification of HTML, CSS and JS.
 * 
 * @copyright Copyright (c) 2019, Strattic
 * @author Ryan Hellyer <ryanhellyergmail.com>
 * @since 1.1
 */
class Strattic_Minification extends Strattic_Core {

	/**
	 * Fire the constructor up :D
	 */
	public function __construct() {

		add_action( 'admin_init',        array( $this, 'register_settings' ) );
		add_action( 'strattic_settings', array( $this, 'admin_page' ), 19 );

		// Minify the HTML
		if ( 'on' === $this->get_option( 'html' ) ) {

			add_action( 'template_redirect', array( $this, 'html_minify' ) );
			add_action( 'admin_init', array( $this, 'minit_cache_delete' ) );

		}

	}

	/**
	 * Init plugin options to white list our options.
	 */
	public function register_settings() {

		$a = 'strattic-minify';
		$b = 'strattic';
		add_settings_section(
			$a,
			esc_html__( 'Minify', 'strattic' ),         
			null,  
			$b
		);
		add_settings_field(
			$a,        
			esc_html__( 'Minify field', 'strattic' ),        
			array( $this, 'sanitize' ),
			$b,
			$a 
		);
		register_setting(
			'strattic-settings',   // The settings group name
			$a,   // The option name
			array( $this, 'sanitize' ) // The sanitization callback
		);

	}

	/**
	 * Output the admin page.
	 *
	 * @global  string  $title  The page title set by add_submenu_page()
	 */
	public function admin_page() {

		// Grab options array and output a new row for each setting
		echo '

		<p>This page has a bug which sporadically causes it not to save in production.</p>

		<h2>' . esc_html__( 'Minification', 'strattic' ) . '</h2>
		<table class="form-table">';

		foreach ( array( 'html', 'css', 'js' ) as $slug ) {

			echo '
			<tr>
				<th scope="row">' . esc_html__( 'Minify', 'strattic' ) . ' ' . esc_html( strtoupper( $slug ) ) . '</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>' . esc_html__( 'Minify', 'strattic' ) . ' ' . esc_html( strtoupper( $slug ) ) . '</span></legend>
						<label for="' . esc_attr( $slug ) . '">
							<input
								' . checked( $this->get_option( $slug ), 'on', false ) . '
								name="' . esc_attr( 'strattic-minify[' . $slug . ']' ) . '"
								id="' . esc_attr( $slug ) . '"
								type="checkbox"
								value="on"
								class="regular-text"
							/>
						</label>
					</fieldset>
				</td>
			</tr>';
		}

		echo '
		</table>';

	}

	/**
	 * Sanitize the input field.
	 *
	 * @param   array   $input   The input string
	 * @return  array            The sanitized string
	 */
	public function sanitize( $input ) {

// XXXXXXXXXXXXXXX FIX
return $input;
		return wp_kses_post( $input );
	}

	/*
	 * Minify the HTML via an output buffer.
	 */
	public function html_minify() {
		ob_start( array( $this, 'ob' ) );
	}

	/*
	 * Compress the HTML via buffer.
	 *
	 * @return  string  The minified HTML
	 */
	public function ob( $content ) {

		$html_min = new HtmlMin();
		$content = $html_min->minify( $content ); 

		return $content;
	}

	/**
	 * Flush the Minit cache whenever in a wp-admin page contain .php in the URL.
	 * This avoids admin-ajax.php or other scripts from inadvertently triggering the cache delete during frontend page loading.
	 * We do this frequently, because we don't want the client having to flush the cache manually.
	 * It may pay to do this in a more efficient manner in future, or after the pages have left WordPress.
	 */
	public function minit_cache_delete() {

		if (
			strpos( $_SERVER[ 'REQUEST_URI' ], '.php' ) !== false
			&&
			strpos( $_SERVER[ 'REQUEST_URI' ], 'wp-admin' ) !== false
		) {
			do_action( 'minit-cache-purge-delete' );
		}

	}

	/**
	 * Get array data from option.
	 *
	 * @param   string   $option  The array key to select
	 * @return  string   The requested option data
	 */
	public function get_option( $option ) {
		$values = get_option( 'strattic-minify' );

		$value = '';
		if ( isset( $values[ $option ] ) ) {
			$value = $values[ $option ];
		}

		return $value;
	}

}
