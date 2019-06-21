<?php
/**
 * Strattic Template Parser
 *
 * @package Strattic Search
 */

use Sunra\PhpSimple\HtmlDomParser;

/**
 * Strattic Template Parser.
 *
 * @copyright Copyright (c), Strattic
 * @since 1.1
 */
class Strattic_Template_Parser {
	/**
	 * Strattic search results and settings.
	 *
	 * @var Strattic_Search
	 */
	public $search;

	/**
	 * Unique search key
	 *
	 * @var string
	 */
	public $unique_search_key = 'htCpGPJBzdXTXbvTWvfhtNzKQKVUge';

	/**
	 * Constructor.
	 *
	 * @param Strattic_Search $search The Strattic_Search instance.
	 */
	public function __construct( $search ) {
		$this->search = $search;
	}

	/**
	 * Returns only the most important parts of the HTML
	 *
	 * @param string $html The HTML to strip.
	 * @return string
	 */
	public function strip( $html ) {
		ob_start();
		get_sidebar();
		$sidebar = ob_get_clean();
		$content = str_replace( $sidebar, '', $html );

		$dom = HtmlDomParser::str_get_html( $html );

		if ( ! $dom ) {
			return $html;
		}

		$body = $dom->find( 'body', 0 );

		if ( ! $body ) {
			return $html;
		}

		$content = '';

		foreach ( $body->children() as $child ) {
			$content .= $child;
		}

		$dom = HtmlDomParser::str_get_html( $content );

		$scripts = $dom->find( 'script' );
		foreach ( $scripts as $script ) {
			$content = str_replace( $script, '', $content );
		}

		$styles = $dom->find( 'style' );
		foreach ( $styles as $style ) {
			$content = str_replace( $style, '', $content );
		}

		return $content;
	}

	/**
	 * Tidies the HTML
	 *
	 * @param string $html The HTML to tidy.
	 * @return string
	 */
	public function tidy( $html ) {
		return Htmlawed::filter( $html, array( 'tidy' => 5 ) );
	}

	/**
	 * Replaces post data with moustache placeholders.
	 *
	 * @param string $html The HTML to replace.
	 *
	 * @return string
	 */
	public function replace_post_data( $html ) {
		$post = $this->search->generate_sample_post();

		$html = str_replace( $this->unique_search_key, '{{search_string}}', $html );
		$html = str_replace( 'post-' . $post->ID, 'post-{{id}}', $html );
		$html = str_replace( 'post=' . $post->ID, 'post={{id}}', $html );

		$post->filter = 'sample';
		$html         = str_replace( get_permalink( $post ), '{{url}}', $html );
		$post->filter = 'raw';

		$html = str_replace( apply_filters( 'the_title', $post->post_title, $post->ID ), '{{title}}', $html );
		$html = str_replace( apply_filters( 'get_the_excerpt', $post->post_excerpt ), '{{excerpt}}', $html );
		$html = str_replace( apply_filters( 'post_content', get_post_field( 'post_content', $post->ID ) ), '{{content}}', $html );

		$the_date = get_post_time( get_option( 'date_format' ), false, $post, true );
		$the_time = get_post_time( get_option( 'time_format' ), false, $post, true );

		$html = str_replace( apply_filters( 'get_the_date', $the_date, '', $post ), '{{date}}', $html );
		$html = str_replace( apply_filters( 'get_the_time', $the_time, '', $post ), '{{time}}', $html );

		// $html = str_replace( get_the_post_thumbnail_url( $post->id ), '{{thumbnail}}', $html );
		$html = str_replace( get_the_author_meta( 'nickname', $post->post_author ), '{{author.nickname}}', $html );
		$html = str_replace( get_the_author_meta( 'first_name', $post->post_author ), '{{author.firstName}}', $html );
		$html = str_replace( get_the_author_meta( 'last_name', $post->post_author ), '{{author.lastName}}', $html );
		$html = str_replace( get_the_author_meta( 'display_name', $post->post_author ), '{{author.name}}', $html );

		return $html;
	}

	/**
	 * Generate a search page with one result.
	 *
	 * @return string
	 */
	public function get_search_results() {
		$request = wp_remote_get(
			add_query_arg(
				array(
					's'           => $this->unique_search_key,
					'search-type' => 'strattic-single-sample',
				),
				home_url()
			)
		);

		// If no data found, serve error message (the user will see this when trying to copy the HTML)
		if ( is_wp_error( $request ) || ! isset( $request['body'] ) ) {
			return esc_html__( 'Error: Search template was not generated.', 'strattic' );
		}

		$html = $request['body'];
		$html = $this->strip( $html );
		$html = $this->tidy( $html );
		$html = $this->replace_post_data( $html );

		return $html;
	}

	/**
	 * Generate a search page with many results.
	 *
	 * @return string
	 */
	public function get_search_many_results() {
		$request = wp_remote_get(
			add_query_arg(
				array(
					's'           => $this->unique_search_key,
					'search-type' => 'strattic-multi-sample',
					'paged'       => 5,
				),
				home_url()
			)
		);

		$html = $request['body'];
		$html = $this->strip( $html );
		$html = $this->tidy( $html );
		$html = $this->replace_post_data( $html );

		return $html;
	}

	/**
	 * Generate a search page with no results.
	 *
	 * @return string
	 */
	public function get_search_no_results() {
		$request = wp_remote_get(
			add_query_arg(
				array(
					's' => uniqid(),
				),
				home_url()
			)
		);

		$html = $request['body'];
		$html = $this->strip( $html );
		$html = $this->tidy( $html );

		return $html;
	}
}
