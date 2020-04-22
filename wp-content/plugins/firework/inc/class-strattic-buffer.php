<?php

/**
 * Strattic buffer.
 * Used in other classes when modification of HTML after generation is required.
 * 
 * @copyright Copyright (c), Strattic
 * @since 1.1
 */
class Strattic_Buffer {

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'template_redirect', array( $this, 'template_redirect' ) );

	}

	/*
	 * Starting page buffer.
	 */
	public function template_redirect() {

		if (
			! is_single()
			&&
			! is_post_type_archive()
			&&
			! is_page()
			&&
			! is_archive()
			&&
			! is_404()
			&&
			! is_attachment()
			&&
			! is_front_page()
			&&
			! is_search()
		) {
			return;
		}

		ob_start( array( $this, 'ob' ) );
	}

	/*
	 * Rewriting URLs once buffer ends.
	 *
	 * @param   string  The pages HTML
	 * @return  string  The filtered page output
	 */
	public function ob( $html ) {

		$html = apply_filters( 'strattic_buffer', $html );

		return $html;
	}

}
