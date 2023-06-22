<?php

require( 'gallery.php' );

/**
 * Primary class used to load the Hellish Simplicity theme.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Psychedelic Society Berlin
 * @since Psychedelic Society Berlin 1.0
 */
class PsychedelicSocietyBerlin_Setup {

	/**
	 * Theme version number.
	 * 
	 * @var string
	 */
	const VERSION_NUMBER = '1.0';

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 *
	 * @global  int  $content_width  Sets the media widths (unfortunately required as a global due to WordPress core requirements) 
	 */
	public function __construct() {
		global $content_width;
		$content_width = 680;

		// Add action hooks.
		add_action( 'after_setup_theme', array( $this, 'theme_setup' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'stylesheet' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'script' ) );
		add_action( 'admin_init', array( $this, 'editor_stylesheet' ) );
		add_action( 'wp_head', array( $this, 'google_fonts' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'cmb2_admin_init', array( $this, 'meta_boxes' ) );
		add_action( 'admin_init', array( $this, 'events_post_type_description' ) );
		add_action( 'admin_menu', array( $this, 'create_events_admin_page' ) );
		add_action( 'init', array( $this, 'register_menus' ) );
		add_action( 'template_redirect', array( $this, 'rewrite_404_page' ) );

		// Add filters.
		add_filter( 'body_class', array( $this, 'body_classes' ) );

		// Modifying admin setup.
		add_action( 'admin_menu', array( $this, 'remove_menus' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_admin_bar_links' ) );
		add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );
		add_action( 'admin_head', array( $this, 'admin_styles' ) );
	}

	/**
	 * Load editor stylesheet.
	 */
	public function editor_stylesheet() {
		add_editor_style( 'css/editor-style.css' );
	}

	/**
	 * Load stylesheet.
	 */
	public function stylesheet() {
		if ( ! is_admin() ) {
			wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/css/style.min.css', array(), self::VERSION_NUMBER );
		}
	}

	/**
	 * Load script.
	 */
	public function script() {
		if ( ! is_admin() ) {
			wp_enqueue_script( 'script', get_stylesheet_directory_uri() . '/js/script.js', array(), self::VERSION_NUMBER );
		}
	}

	/**
	 * Load Google Fonts.
	 * This should use wp_enqueue() rather than dumping them out like this.
	 */
	public function google_fonts() {
		echo '
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@100;200;300;400;600;900&display=swap" rel="stylesheet" />
';
	}

	/**
	 * Register post types.
	 */
	public function register_post_types() {
		register_post_type(
			'event',
			array(
				'labels'      => array(
					'name'          => __( 'Events', 'psb'),
					'singular_name' => __( 'Event', 'psb'),
				),
				'public'      => true,
				'has_archive' => 'events',
				'supports'    => array(
					'title',
					'editor',
					'excerpt',
					'thumbnail'
				),

			)
		);
	}

	/**
	 * Define the metaboxes.
	 * 
	 * @uses CMB2
	 */
	public function meta_boxes() {

		// Front page.
		$cmb = new_cmb2_box( array(
			'id'            => 'front_page_metaboxes',
			'title'         => __( 'Contact page', 'psb' ),
			'object_types'  => array( 'page', ), // Post type
			'context'       => 'normal',
			'priority'      => 'low',
			'show_names'    => true, // Show field names on the left
			'show_on_cb'    => function( $cmb ) {
				$front_page = get_option( 'page_on_front' );
				return $cmb->object_id() === $front_page;
			},
		) );

		// Front page partners.
		$group_field_id = $cmb->add_field( array(
			'id'          => 'partners',
			'type'        => 'group',
			'description' => __( 'Set partners', 'psb' ),
			// 'repeatable'  => false, // use false if you want non-repeatable group
			'options'     => array(
				'group_title'       => __( 'Partner {#}', 'psb' ),
				'add_button'        => __( 'Add Another Entry', 'psb' ),
				'remove_button'     => __( 'Remove Entry', 'psb' ),
				'sortable'          => true,
				// 'closed'         => true, // true to have the groups closed by default
				// 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'psb' ),
			),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'             => __( 'Image', 'psb' ),
			'id'               => 'attachment',
			'type'             => 'select',
			'show_option_none' => true,
			'options_cb'       => function() {
				$posts = get_posts( array(
					'post_type'      => 'attachment',
					'post_parent'    => get_option( 'page_on_front' ),
					'posts_per_page' => 50,
				) );

				$options = array();
				foreach ( $posts as $post ) {
					$options[ $post->ID ] = $post->post_title;
				}

				return $options;
			},
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => __( 'URL', 'psb' ),
			'id'   => 'url',
			'type' => 'text_url',
		) );

		// Front page contact page.
		$cmb->add_field( array(
			'name'             => __( 'Contact page', 'psb' ),
			'id'               => 'contact-page',
			'type'             => 'select',
			'show_option_none' => true,
			'options_cb'       => function() {
				$posts = get_posts( array(
					'posts_per_page' => 50,
					'post_type'      => 'page',
				) );

				$options = array();
				foreach ( $posts as $post ) {
					$options[ $post->ID ] = $post->post_title;
				}

				return $options;
			},
		) );

		// Event posts.
		$cmb = new_cmb2_box( array(
			'id'            => 'metaboxes',
			'title'         => __( 'Details', 'psb' ),
			'object_types'  => array( 'event', ), // Post type
			'context'       => 'normal',
			'priority'      => 'low',
			'show_names'    => true, // Show field names on the left
		) );

		$cmb->add_field( array(
			'name'            => 'Start time',
			'id'              => 'start_time',
			'type'            => 'text_datetime_timestamp',
//			'sanitization_cb' => 'esc_html',
//			'escape_cb'       => 'esc_html',
		) );

		$cmb->add_field( array(
			'name'            => 'End time',
			'id'              => 'end_time',
			'type'            => 'text_datetime_timestamp',
//			'sanitization_cb' => 'esc_html',
//			'escape_cb'       => 'esc_html',
		) );

		$cmb->add_field( array(
			'name'            => 'Location',
			'id'              => 'location',
			'type'            => 'textarea',
			'sanitization_cb' => 'esc_html',
			'escape_cb'       => 'esc_html',
		) );

		// Resources page.
		$cmb = new_cmb2_box( array(
			'id'            => 'resources_page_metaboxes',
			'title'         => __( 'Resources', 'psb' ),
			'object_types'  => array( 'page', ), // Post type
			'context'       => 'normal',
			'priority'      => 'low',
			'show_names'    => true, // Show field names on the left
			'show_on_cb'    => function( $cmb ) {
				$page = get_page_by_path( '/resources/' );
				$id = false;
				if ( isset( $page->ID ) ) {
					$id = $page->ID;
				}
				return absint( $cmb->object_id() ) === $id;
			},
		) );

		$group_field_id = $cmb->add_field( array(
			'id'          => 'resources',
			'type'        => 'group',
			'description' => __( 'Set resources', 'psb' ),
			// 'repeatable'  => false, // use false if you want non-repeatable group
			'options'     => array(
				'group_title'       => __( 'Resource {#}', 'psb' ),
				'add_button'        => __( 'Add Another resource', 'psb' ),
				'remove_button'     => __( 'Remove resource', 'psb' ),
				'sortable'          => true,
				// 'closed'         => true, // true to have the groups closed by default
				// 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'psb' ),
			),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'             => __( 'Image', 'psb' ),
			'id'               => 'image',
			'type'             => 'select',
			'show_option_none' => true,
			'options_cb'       => function() {
				$page = get_page_by_path( '/resources/' );
				$id = false;
				if ( isset( $page->ID ) ) {
					$id = $page->ID;
				}

				$posts = get_posts( array(
					'post_type'      => 'attachment',
					'post_parent'    => $id,
					'posts_per_page' => 50,
				) );

				$options = array();
				foreach ( $posts as $post ) {
					if (
						'image/jpeg' === $post->post_mime_type
						||
						'image/webp' === $post->post_mime_type
						||
						'image/png' === $post->post_mime_type
						||
						'image/gif' === $post->post_mime_type
					) {
						$options[ $post->ID ] = $post->post_title;
					}
				}

				return $options;
			},
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'             => __( 'PDF', 'psb' ),
			'id'               => 'pdf',
			'type'             => 'select',
			'show_option_none' => true,
			'options_cb'       => function() {
				$page = get_page_by_path( '/resources/' );
				$id = false;
				if ( isset( $page->ID ) ) {
					$id = $page->ID;
				}

				$posts = get_posts( array(
					'post_type'      => 'attachment',
					'post_parent'    => $id,
					'posts_per_page' => 50,
				) );

				$options = array();
				foreach ( $posts as $post ) {

					if ( 'application/pdf' === $post->post_mime_type ) {
						$options[ $post->ID ] = $post->post_title;
					}
				}

				return $options;
			},
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => __( 'URL', 'psb' ),
			'id'   => 'url',
			'type' => 'text_url',
		) );

	}

	/**
	 * Modify the body classes.
	 * We simplify them, since most are redundant and serve no purpose.
	 * 
	 * @param array $classes The body classes.
	 * @return array The modified body classes.
	 */
	public function body_classes( $classes ) {
		$classes = array();

		if ( is_front_page() ) {
			$classes[] = 'front-page';
		}

		return $classes;
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	public function theme_setup() {

		// Add image sizes.
		add_image_size( 'logo-small', 152 );
		add_image_size( 'logo-double', 304 );
		add_image_size( 'events-thumb', 520, 293, true );
		add_image_size( 'events-double', 1040, 586, true );
		add_image_size( 'events-full-width', 1400, 789, true );

		// Make theme available for translation.
		load_theme_textdomain( 'psb', get_template_directory() . '/languages' );

		// Add title tags.
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails.
		add_theme_support( 'post-thumbnails' );
	}

	/**
	 * Register widgetized area and update sidebar with default widgets.
	 */
	public function widgets_init() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Sidebar', 'hellish-simplicity' ),
				'id'            => 'sidebar',
				'before_widget' => '<aside id="%1$s" class="%2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}

	/**
	 * Remove admin bar menus.
	 */
	public function remove_admin_bar_links() {
	
		// Bail out now if not in admin or user can't activate plugins.
		if ( ! is_admin() ) {
			return;
		}
	
		global $wp_admin_bar;
	
		$wp_admin_bar->remove_menu( 'comments' );
		$wp_admin_bar->remove_menu( 'new-content' );
		$wp_admin_bar->remove_menu( 'blog-6-n' );
		$wp_admin_bar->remove_menu( 'blog-6-c' );
	
	}
	
	/**
	 * Remove meta boxes.
	*/
	public function remove_meta_boxes() {
	
		// List of meta boxes.
		$meta_boxes = array(
			'commentsdiv',
			'trackbacksdiv',
			'postcustom',
//			'postexcerpt',
			'commentstatusdiv',
			'commentsdiv',
		);

		// Removing the meta boxes.
		foreach( $meta_boxes as $box ) {
			remove_meta_box(
				$box, // ID of meta box to remove.
				'page', // Post type.
				'normal' // Context.
			);
		}
	
	}
	
	/**
	 * Remove menus.
	 * Redirect dashboard.
	 */
	public function remove_menus () {

		// Bail out now if not in admin or user can't activate plugins.
		if ( ! is_admin() ) {
			return;
		}
	
		// List of items to remove.
		$restricted_sub_level = array(
			'edit-tags.php?taxonomy=category' =>'edit.php', // This doesn't actually do anything since posts aren't present, but left here so that you can see how to remove sub menus if needed in your own projects.
			'options-discussion.php'          => 'options-general.php',
			'options-writing.php'             => 'options-general.php',
			'admin.php?page=wpseo_dashboard'  => 'TOP',
			'index.php'                       => 'TOP',
			'edit.php'                        => 'TOP',
			'edit-comments.php'               => 'TOP',
			'tools.php'                       => 'TOP',
			'themes.php'                      => 'TOP',
			'plugins.php'                     => 'TOP',
			'link-manager.php'                => 'TOP',
		);
		foreach( $restricted_sub_level as $page => $top ) {
	
			// If a top level page, then remove whole block.
			if ( 'TOP' == $top ) {
				remove_menu_page( $page );
			} else {
				remove_submenu_page( $top, $page );
			}
	
		}
	
		// Redirect from dashboard to edit pages - Thanks to WP Engineer for this code snippet ... http://wpengineer.com/redirects-to-another-page-in-wordpress-backend/.
		if ( preg_match( '#wp-admin/?(index.php)?$#', filter_input( INPUT_SERVER, 'REQUEST_URI' ) ) ) {
			wp_redirect( admin_url( 'edit.php?post_type=page' ) );
		}
	
	}

	/**
	 * Admin specific styles.
	 */
	public function admin_styles() {
		echo '
		<style>
		.form-table tr:has(#posts_per_page),
		.form-table tr:has(#posts_per_rss),
		.form-table tr:has(input[name=rss_use_excerpt]),
		#front-static-pages li:has(#page_for_posts)
		 {
			display: none;
		}
		</style>';
	}

	/**
	 * Register setting for events post-type description.
	 */
	public function events_post_type_description() {
		register_setting(
			'events',                  // The settings group name.
			'events-description',      // The option name.
			array( $this, 'sanitize' ) // The sanitization callback.
		);
	}

	/**
	 * Create the page and add it to the menu.
	 */
	public function create_events_admin_page() {
		add_submenu_page(
			'edit.php?post_type=event',
			__ ( 'Description', 'psb' ), // Page title.
			__ ( 'Description', 'psb' ), // Menu title.
			'manage_options',            // Capability required.
			'description',               // The URL slug.
			array( $this, 'admin_page' ) // Displays the admin page.
		);

		add_menu_page(
			'menus',
			'Menus',
			'read',
			'nav-menus.php',
			'',
			'dashicons-menu',
			80
		);
	}

	/**
	 * Register menus.
	 */
	public function register_menus() { 
		register_nav_menu( 'main-menu', __( 'Main Menu', 'psb' ) ); 
	} 

	/**
	 * Output the admin page.
	 */
	public function admin_page() {

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Events description', 'psb' ); ?></h1>

			<form method="post" action="options.php">

				<p>
					<label for="events-description"><?php _e( 'Enter the events description.', 'psb' ); ?></label>
				</p>
				<p>
					<textarea style="width:600px;height:100px" id="events-description" name="events-description"><?php echo esc_html( get_option( 'events-description' ) ); ?></textarea>
				</p>

				<?php settings_fields( 'events' ); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'psb' ); ?>" />
				</p>
			</form>
		</div><?php
	}

	/**
	 * Sanitize the page or product ID.
	 *
	 * @param string $input The input string.
	 * @return array The sanitized string.
	 */
	public function sanitize( $input ) {
		$output = wp_kses_post( $input );
		return $output;
	}

	/**
	 * Rewrites the page at /404-page/ onto all 404 pages.
	 * Does not rewrite URLs within the page, so it has things like canonical URLs pointing to the original /404-page/ page.
	 */
	public function rewrite_404_page() {

		if ( is_404() ) {
			$url = esc_url( home_url() . '/404-page/' );
			echo file_get_contents( $url );
			die;
		}
	}

}
new PsychedelicSocietyBerlin_Setup();
