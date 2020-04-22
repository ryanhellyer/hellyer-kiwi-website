<?php
/*
Plugin Name: Firework
Plugin URI:  https://www.fireworktv.com/
Description:

Author: Firework TV
Author URI: https://www.fireworktv.com/

Copyright 2020 Firework TV

*/


/**
 * Firework.
 */
class Firework {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
		add_action( 'save_post', array( $this, 'meta_boxes_save' ), 10, 2 );

		add_filter( 'the_content', array( $this, 'the_content' ) );
	}

	/**
	 * Register a custom post type called "firework-video".
	 */
	public function init() {
		$labels = array(
			'name'                  => _x( 'Video', 'Post type general name', 'firework' ),
			'singular_name'         => _x( 'Video', 'Post type singular name', 'firework' ),
			'menu_name'             => _x( 'Firework Videos', 'Admin Menu text', 'firework' ),
			'name_admin_bar'        => _x( 'Video', 'Add New on Toolbar', 'firework' ),
			'add_new'               => __( 'Add New', 'firework' ),
			'add_new_item'          => __( 'Add New Video', 'firework' ),
			'new_item'              => __( 'New Video', 'firework' ),
			'edit_item'             => __( 'Edit Video', 'firework' ),
			'view_item'             => __( 'View Video', 'firework' ),
			'all_items'             => __( 'All Videos', 'firework' ),
			'search_items'          => __( 'Search Videos', 'firework' ),
			'parent_item_colon'     => __( 'Parent Videos:', 'firework' ),
			'not_found'             => __( 'No Videos found.', 'firework' ),
			'not_found_in_trash'    => __( 'No Videos found in Trash.', 'firework' ),
			'featured_image'        => _x( 'Video Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'firework' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'firework' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'firework' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'firework' ),
			'archives'              => _x( 'Video archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'firework' ),
			'insert_into_item'      => _x( 'Insert into Video', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'firework' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this Video', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'firework' ),
			'filter_items_list'     => _x( 'Filter Videos list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'firework' ),
			'items_list_navigation' => _x( 'Videos list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'firework' ),
			'items_list'            => _x( 'Videos list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'firework' ),
		);
	 
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => false,
			'menu_position'      => null,
			'menu_icon'          => 'dashicons-video-alt3',
			'supports'           => array( 'title' ),
		);
	 
		register_post_type( 'firework-video', $args );
	}
 
	/**
	 * Add admin metabox.
	 */
	public function add_metaboxes() {
		add_meta_box(
			'gallery_type', // ID
			__( 'Gallery Type', 'firework' ), // Title
			array(
				$this,
				'gallery_type_meta_box', // Callback to method to display HTML
			),
			'firework-video', // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);

		add_meta_box(
			'location', // ID
			__( 'Location', 'firework' ), // Title
			array(
				$this,
				'location_meta_box', // Callback to method to display HTML
			),
			'firework-video', // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the meta box.
	 */
	public function gallery_type_meta_box() {

		$gallery_types = array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
		);

		echo '<p>';
		foreach ( $gallery_types as $slug => $text ) {
			$value = get_post_meta( get_the_ID(), '_gallery_type', true );
			echo '
			<input type="radio" name="_gallery_type" value="' . esc_attr( $slug ) . '" ' . checked( $value, $slug, false ) . ' />
			<label for="_gallery_type">' . esc_html( $text ) . '</label>
			&nbsp; &nbsp; &nbsp; &nbsp;';
		}
		echo '</p>';

		echo '<input type="hidden" id="firework-nonce" name="firework-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '" />';
	}

	/**
	 * Output the meta box.
	 */
	public function location_meta_box() {

		$locations = array(
			'above-post'     => __( 'Above each post', 'firework' ),
			'below-post'     => __( 'Below each post', 'firework' ),
			'none'           => __( 'None', 'firework' ),
		);


		foreach ( $locations as $slug => $text ) {
			$value = get_post_meta( get_the_ID(), '_location', true );

			echo '
			<p>
				<input type="radio" name="_location" value="' . esc_attr( $slug ) . '" ' . checked( $value, $slug, false ). ' />
				<label for="_location">' . esc_html( $text ) . '</label>
			</p>';

		}
		?>

		<?php
	}

	/**
	 * Save opening times meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function meta_boxes_save( $post_id, $post ) {

		// Only save if correct post data sent
		if ( isset( $_POST['_gallery_type'] ) ) {

			// Do nonce security check
			if ( ! wp_verify_nonce( $_POST['firework-nonce'], __FILE__ ) ) {
				return;
			}

			// Sanitize and store the data
$_gallery_type = $_POST['_gallery_type']; // NEED TO SANITISE DATA HERE!!!!
			update_post_meta( $post_id, '_gallery_type', $_gallery_type );

$_location = $_POST['_location']; // NEED TO SANITISE DATA HERE!!!!
			update_post_meta( $post_id, '_location', $_location );
		}

	}

	public function the_content( $content ) {

$string = '
<div id="fwn_videos"></div>

<script type="text/javascript">
    !function(e,t,c,a){if(!e.fwn&&(a="fwn_script",n=e.fwn=function(){
    n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)
    },e._fwn||(e._fwn=n),n.queue=[],!t.getElementById(a))){var d=document.createElement("script");
    d.async=1,d.src=c,d.id=a,t.getElementsByTagName("head")[0].appendChild(d)}
    }(window,document,"//asset.fwcdn1.com/js/fwn.js");

    fwn(\'app_id\', \'MEHcDrJfUH-kfmAViOQrvgvp-C18rgu7\');

    fwn(\'mode\', \'pinned\');
    fwn(\'autoplay\', true);
    fwn(\'page_type\', \'article\');
    fwn(\'target\', document.getElementById(\'fwn_videos\'));
</script>';

		$args = array(
			'posts_per_page'         => 100,
			'post_type'              => 'firework-video',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		$query = new \WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$location = get_post_meta( get_the_ID(), '_location', true );

				if ( 'below-post' === $location ) {
					$content = $content . $string;
				}

				if ( 'after-post' === $location ) {
					$content = $string . $content;
				}
			}
		}

		return $content;
	}

}
new Firework();
