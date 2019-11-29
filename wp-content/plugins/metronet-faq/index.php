<?php
/*
Plugin Name: Metronet FAQ
Plugin URI: http://www.metronet.no/
Description: Creates an FAQ page with taxonomies

Author: Metronet / Ryan Hellyer
Version: 1.0
Author URI: http://www.metronet.no/

Copyright 2013 Metronet

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

define( 'METRONET_FAQ_DIR', rtrim( plugin_dir_path(__FILE__), '/' ) ); // Plugin folder DIR
define( 'METRONET_FAQ_URL', rtrim( plugin_dir_url(__FILE__), '/' ) ); // Plugin folder URL

/*
 * Create FAQ section
 * 
 * Visual editor code courtesy of inspectorfegter ... https://gist.github.com/inspectorfegter/1207830
 *
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class Metronet_FAQ {

	/*
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'css' ) );
		add_action( 'init',               array( $this, 'init' ) );
		add_action( 'add_meta_boxes',     array( $this, 'add_metabox' ) );
		add_action( 'admin_init',         array( $this, 'meta_boxes_save' ) );
		add_shortcode( 'metronet-faq',    array( $this, 'shortcode' ) );
		load_plugin_textdomain(
			'metronet-faq', // Unique identifier
			false, // Deprecated abs path
			dirname( plugin_basename( __FILE__ ) ) . '/languages/' // Languages folder
		);
	}

	/*
	 * Enqueue stylesheet
	 */
	public function css() {
		wp_enqueue_style( 'faq-style', METRONET_FAQ_URL . '/faq-styles.css' );
	}
	
	/*
	 * Frontend shortcode
	 */
	public function shortcode( $atts ) {
		extract(
			shortcode_atts(
				array(
					'section' => '',
				),
			$atts
			)
		);
		$string = '';

		// Grab taxonomy data
		$section_array = get_term_by( 'name', $section, 'faq-section' );
		if ( $section_array == '' ) {
			$section_array = get_term_by( 'slug', $section, 'faq-section' );
		}
		if ( '' != $section && $section_array ) {
			$section_slug = $section_array->slug;
		}

		// Enqueue the script
		wp_enqueue_script(
			'faq-toggle',
			METRONET_FAQ_URL . '/js/toggle.js',
			array( 'jquery' ),
			true
		);
		$translation_array = array(
			'more' =>   __( 'Read answer', 'metronet-faq' ),
			'answer' => __( 'Hide answer', 'metronet-faq' ),
		);
		wp_localize_script( 'faq-toggle', 'faq_read_more', $translation_array );

		$string .= '

		<div id="faq-section">';

		// Taxonomy heading
		if ( $section_array ) {
			$string .= '<h3>' . esc_html( $section_array->name ) . '</h3>';
		}

		$string .= '
			<ul>';

		// Display posts
		$args = array(
			'post_type'      => 'faq',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		);
		if ( $section_array ) {
			$args['faq-section'] = $section_slug;
		}

		$myposts = get_posts( $args );
		foreach ( $myposts as $post ) {
			$string .= '
				<li>
					' . do_shortcode( $post->post_content ) . '
					<div class="the-answer">' . do_shortcode( get_post_meta( $post->ID, '_answer', true ) ) . '</div>
				</li>';
		}

		$string .= '
			</ul>
		</div>
		';

		return $string;		
	}

	/*
	 * Initialisation
	 * Enqueues and registers stuff
	 */
	public function init() {

		wp_enqueue_script( 'jquery' );
		$args = array(
			'public' => true,
			'label'  => __( 'FAQ', 'metronet-faq' ),
		);

		register_post_type( 'faq', $args );

		register_taxonomy(
			'faq-section',
			'faq',
			array(
				'label'        => __( 'Section', 'metronet-faq' ),
				'rewrite'      => array( 'slug' => 'faq-section' ),
				'hierarchical' => true,
			)
		);

	}

	/**
	 * Add admin metabox for thumbnail chooser
	 */
	public function add_metabox() {
		add_meta_box(
			'faq-answer', // ID
			__( 'Answer', 'metronet-faq' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			'faq', // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the thumbnail meta box
	 * Adds wysywig editor to the meta box
	 */
	public function meta_box() {
		global $post;
		
		$meta_box_id = 'my-editor';
		$editor_id = '_answer';

		// Add CSS & jQuery goodness to make this work like the original WYSIWYG
		echo "
		<style type='text/css'>
			#$meta_box_id #edButtonHTML, #$meta_box_id #edButtonPreview {background-color: #F1F1F1; border-color: #DFDFDF #DFDFDF #CCC; color: #999;}
			#$editor_id{width:100%;}
			#$meta_box_id #editorcontainer{background:#fff !important;}
		</style>

		 <script type='text/javascript'>
		 jQuery(function($){
			$('#$meta_box_id #editor-toolbar > a').click(function(){
				$('#$meta_box_id #editor-toolbar > a').removeClass('active');
				$(this).addClass('active');
			});
			if($('#$meta_box_id #edButtonPreview').hasClass('active')){
				$('#$meta_box_id #ed_toolbar').hide();
			}
			$('#$meta_box_id #edButtonPreview').click(function(){
				$('#$meta_box_id #ed_toolbar').hide();
			});
			$('#$meta_box_id #edButtonHTML').click(function(){
				$('#$meta_box_id #ed_toolbar').show();
			});
			//Tell the uploader to insert content into the correct WYSIWYG editor
			$('#media-buttons a').bind('click', function(){
				var customEditor = $(this).parents('#$meta_box_id');
				if(customEditor.length > 0){
					edCanvas = document.getElementById('$editor_id');
				}
				else{
					edCanvas = document.getElementById('content');
				}
			});
		});
		</script>";

		// Create The Editor
		$content = get_post_meta( $post->ID, '_answer', true );
		wp_editor( $content, $editor_id );
	}

	/**
	 * Save opening times meta box data
	 */
	function meta_boxes_save() {

		// Only process if the form has actually been submitted
		if (
			isset( $_POST['_wpnonce'] ) &&
			isset( $_POST['post_ID'] )
		) {

			// Do nonce security check
			wp_verify_nonce( '_wpnonce', $_POST['_wpnonce'] );

			// Grab post ID
			$post_ID = (int) $_POST['post_ID'];

			// Sanitizing data
			if ( isset( $_POST['_answer'] ) ) {
				$_answer = apply_filters( 'content_save_pre ', $_POST['_answer'] ); // Sanitise data input
				update_post_meta( $post_ID, '_answer', $_answer ); // Store the data
			}

		}
	}

}
new Metronet_FAQ;
