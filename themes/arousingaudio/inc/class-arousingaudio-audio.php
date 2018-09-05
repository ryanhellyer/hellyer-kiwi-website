<?php

/**
 * Sets up audio features.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Arousing Audio
 * @since Arousing Audio 1.0
 */
class ArousingAudio_Audio extends ArousingAudio_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {
		add_action( 'init',               array( $this, 'register_post_types' ) );
		add_action( 'init',               array( $this, 'register_taxonomies' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'variables' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'taxonomy_css' ), 20 );
	}

	/**
	 * Variables for audio player.
	 *
	 * @access static  This is set to static so that the variables can be accessed within each single audio post
	 */
	public static function variables() {

		$audio_posts = arousingaudio_get_posts( get_the_ID() );

		wp_localize_script( 'arousing-audio-init', 'audio_posts', $audio_posts );

	}

	/**
	 * Register post-types.
	 */
	public function register_post_types() {
		$labels = array(
			'name'               => _x( 'Audio', 'post type general name', 'arousingaudio' ),
			'singular_name'      => _x( 'Audio', 'post type singular name', 'arousingaudio' ),
			'menu_name'          => _x( 'Audio', 'admin menu', 'arousingaudio' ),
			'name_admin_bar'     => _x( 'Audio', 'add new on admin bar', 'arousingaudio' ),
			'add_new'            => _x( 'Add New', 'book', 'arousingaudio' ),
			'add_new_item'       => __( 'Add New Audio', 'arousingaudio' ),
			'new_item'           => __( 'New Audio', 'arousingaudio' ),
			'edit_item'          => __( 'Edit Audio', 'arousingaudio' ),
			'view_item'          => __( 'View Audio', 'arousingaudio' ),
			'all_items'          => __( 'All Audio', 'arousingaudio' ),
			'search_items'       => __( 'Search Audio', 'arousingaudio' ),
			'parent_item_colon'  => __( 'Parent Audio:', 'arousingaudio' ),
			'not_found'          => __( 'No books found.', 'arousingaudio' ),
			'not_found_in_trash' => __( 'No books found in Trash.', 'arousingaudio' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'arousingaudio' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'audio' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		);

		register_post_type( 'audio', $args );

	}

	public function register_taxonomies() {
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Genres', 'taxonomy general name', 'arousingaudio' ),
			'singular_name'     => _x( 'Genre', 'taxonomy singular name', 'arousingaudio' ),
			'search_items'      => __( 'Search Genres', 'arousingaudio' ),
			'all_items'         => __( 'All Genres', 'arousingaudio' ),
			'parent_item'       => __( 'Parent Genre', 'arousingaudio' ),
			'parent_item_colon' => __( 'Parent Genre:', 'arousingaudio' ),
			'edit_item'         => __( 'Edit Genre', 'arousingaudio' ),
			'update_item'       => __( 'Update Genre', 'arousingaudio' ),
			'add_new_item'      => __( 'Add New Genre', 'arousingaudio' ),
			'new_item_name'     => __( 'New Genre Name', 'arousingaudio' ),
			'menu_name'         => __( 'Genre', 'arousingaudio' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'genre' ),
		);

		register_taxonomy( 'genre', array( 'audio' ), $args );

	}

	public function taxonomy_css() {

		// Get background image
		if ( isset( $post[ 'genre-terms' ][ 0 ][ 'image' ] ) ) {
			$image = $post[ 'genre-terms' ][ 0 ][ 'image' ];
		} else {
			$image = 'genre1.jpg';
		}
		$dir = wp_upload_dir();
		$dir_url = $dir[ 'url' ];
		$image_url = $dir_url . '/' . $image;

		// Loop through terms and add CSS to each
		$terms = get_terms( array(
			'taxonomy'   => 'genre',
			'hide_empty' => true,
		) );
		$css = '';
		foreach ( $terms as $key => $term ) {
			$attachment_id = get_term_meta( $term->term_id, 'taxonomy-header-image', true );
			$url = wp_get_attachment_image_src( $attachment_id )[0];

			$css .= '.genre-' . esc_attr( $term->slug ) . ' span {background:url(' . esc_url( $url ) . ");}\n";

		}

        wp_add_inline_style( self::THEME_NAME, $css );

	}

}
