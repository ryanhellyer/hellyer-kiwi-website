<?php

/**
 * Adds meta boxes
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Comic_Glot_Meta_Boxes extends Comic_Glot_Core {

	/*
	 * Class constructor
	 */
	public function __construct() {

		// This only applies when in the admin panel
		if ( ! is_admin() ) {
			return;
		}

		parent::__construct();

		add_action( 'add_meta_boxes',  array( $this, 'add_metabox' ) );
		add_action( 'save_post',       array( $this, 'meta_boxes_save' ), 10, 2 );
		add_filter( 'cmb2_meta_boxes', array( $this, 'cmb_meta_boxes' ) );

	}

	/**
	 * Add admin metabox for thumbnail chooser
	 */
	public function add_metabox() {
		add_meta_box(
			'comic-language', // ID
			__( 'Comic language', 'comic-glot' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			'comic', // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the thumbnail meta box
	 */
	public function meta_box() {
		$_comic_languages = $this->get_current_languages();

		$languages = $this->get_available_languages();
		echo '<p>';
		echo sprintf(
			__( 'Visit the <a href="%s">general options page</a> to add more languages.', 'comic-glot' ),
			admin_url() . 'options-general.php#WPLANG'
		);
		echo '</p>'; 

		// Display each input box
		foreach( $languages as $key => $language ) {

			$checked = '';
			if ( in_array( $language['language'], $_comic_languages ) ) {
				$checked = $language['language'];
			}

			echo '
			<p>
				<input ' . checked( $checked, $language['language'], false ) . ' id="' . esc_attr(  '_comic_languages[' . $language['language'] . ']' ) . '" name="' . esc_attr(  '_comic_languages[]' ) . '" type="checkbox" value="' . esc_attr( $language['language'] ) . '" />
				<label for="' . esc_attr(  '_comic_languages[' . $language['language'] . ']' ) . '" >' . $language['native_name'] . '</label>
			</p>';
		}

		echo '<input type="hidden" name="comic-language-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '">';

	}

	/**
	 * Save opening times meta box data
	 */
	public function meta_boxes_save( $post_id, $post ) {

		// Do nonce security check
		if ( isset( $_POST['comic-language-nonce'] ) && ! wp_verify_nonce( $_POST['comic-language-nonce'], __FILE__ ) ) {
			return $post_id;
		}

		// Sanitizing data
		if ( ! isset( $_POST['_comic_languages'] ) ) {
			return $post_id;
		}

		// Confirm that languages selected match those currently available
		$_comic_languages = array();
		$available_languages = $this->get_available_languages();
		foreach( $_POST['_comic_languages'] as $slug ) {
			foreach( $available_languages as $available_language ) {

				if ( $slug == $available_language['language'] ) {
					$_comic_languages[] = $slug;
				}
			}
		}

		update_post_meta( $post_id, '_comic_languages', $_comic_languages ); // Store the data

	}

	/**
	 * Add custom meta boxes via the custom meta box library
	 *
	 * @param  array $meta_boxes
	 * @return array
	 */
	public function cmb_meta_boxes( array $meta_boxes ) {

		// Loop through each currently set language and add fields for it
		$fields = array();
		foreach( $this->get_current_languages() as $slug ) {

			$fields[] = array(
				'name' => $this->get_language_name_from_slug( $slug ),
				'id'   => $slug,
				'desc' => __( 'Upload an image', 'comic-glot' ),
				'type' => 'file',
			);

		}
//print_r( $this->get_current_languages() );
//print_r( $fields );
//die;
		/**
		 * Repeatable Field Groups
		 */
		$meta_boxes['field_group'] = array(
			'id'           => 'comic-frames',
			'title'        => __( 'Comic frames', 'comic-glot' ),
			'object_types' => array( 'comic', ),
			'fields'       => array(
				array(
					'id'          => '_frames',
					'type'        => 'group',
					'description' => __( 'Add frames to your comic', 'comic-glot' ),
					'options'     => array(
						'group_title'   => __( 'Frame {#}', 'comic-glot' ), // {#} gets replaced by row number
						'add_button'    => __( 'Add another frame', 'comic-glot' ),
						'remove_button' => __( 'Remove frame', 'comic-glot' ),
						'sortable'      => true, // beta
					),
					'fields'      => $fields,
					// Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
					'XXXfields'      => array(
						array(
							'name' => 'Deutsch',
							'id'   => 'de_DE',
							'type' => 'file',
						),
						array(
							'name' => 'Norsk Bokmål',
							'id'   => 'nb_NO',
							'type' => 'file',
						),
					),




				),
			),
		);
//print_r( $meta_boxes );die;
		return $meta_boxes;
	}

}
new Comic_Glot_Meta_Boxes;
