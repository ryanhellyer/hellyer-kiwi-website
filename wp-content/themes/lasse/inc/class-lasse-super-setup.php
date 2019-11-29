<?php




class Lasse_Super_Setup {
	
	/**
	 * Class constructor
	 * Adds all the methods to appropriate hooks
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_setup' ) );
		add_filter( 'pre_get_posts',     array( $this, 'search' ) );
		add_action( 'template_redirect', array( $this, 'page_redirect' ) );
		add_filter( 'wp_page_menu_args', array( $this, 'page_menu_args' ) );
		add_action( 'wp_print_styles',   array( $this, 'css' ) );
		add_action( 'wp_print_scripts',  array( $this, 'external_scripts' ) );
		add_action( 'wp_head',           array( $this, 'inline_scripts' ) );
		add_action( 'wp_head',           array( $this, 'metronet_ad' ) );
	}

	/**
	 * Register inline scripts
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function inline_scripts() {
		echo "\n<!--[if lt IE 9]><script src='" . get_template_directory_uri() . "scripts/html5.js'></script><![endif]-->\n";
	}
	
	/*
	 * Pagination code
	 * @since 1.0
	 * Code developed from the excellent Genesis theme by StudioPress (http://studiopress.com/)
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no> 
	 */
	public function pagination( $pages = '', $range = 2 ) {
	
		// Beginning of numeric pagination
		if( !is_singular() ) : // do nothing
	
		global $wp_query;
	
		// Stop execution if there\'s only 1 page
		if( $wp_query->max_num_pages <= 1 ) return;
	
		$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged') ) : 1;
		$max = intval( $wp_query->max_num_pages );
	
		//	add current page to the array
		if ( $paged >= 1 )
			$links[] = $paged;
	
		//	add the pages around the current page to the array
		if ( $paged >= 3 ) {
			$links[] = $paged - 1; $links[] = $paged - 2;
		}
		if ( ($paged + 2) <= $max ) { 
			$links[] = $paged + 2; $links[] = $paged + 1;
		}
	
		//	Previous Post Link
		if ( get_previous_posts_link() )
			printf( '<li>%s</li>' . "\n", get_previous_posts_link( __( '&laquo; Previous', 'lassesuper') ) );
	
		//	Link to first Page, plus ellipeses, if necessary
		if ( !in_array( 1, $links ) ) {
			if ( $paged == 1 )
				$current = ' class="active"';
			else
				$current = null;
			printf(
				'<li %s><a href="%s">%s</a></li>' . "\n",
				$current,
				get_pagenum_link(1),
				'1'
			);
	
			if ( !in_array( 2, $links ) )
				echo '<li>&hellip;</li>';
		}
	
		//	Link to Current page, plus 2 pages in either direction (if necessary).
		sort( $links );
		foreach( (array)$links as $link ) {
			$current = ( $paged == $link ) ? 'class="active"' : '';
			printf(
				'<li %s><a href="%s">%s</a></li>' . "\n",
				$current,
				get_pagenum_link( $link ),
				$link
			);
		}
	
		//	Link to last Page, plus ellipses, if necessary
		if ( !in_array( $max, $links ) ) {
			if ( !in_array( $max - 1, $links ) )
				echo '<li>&hellip;</li>' . "\n";
			
			$current = ( $paged == $max ) ? 'class="active"' : '';
			printf(
				'<li %s><a href="%s">%s</a></li>' . "\n",
				$current,
				get_pagenum_link( $max ),
				$max
			);
		}
	
		//	Next Post Link
		if ( get_next_posts_link() )
			printf(
				'<li>%s</li>' . "\n",
				get_next_posts_link( __( 'Next &raquo;', 'lassesuper' ) ) );
		endif;
	
	}

	/*
	 * Commented link to metronet.no
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function metronet_ad() {
		echo "\n\n<!--\n	Metronet Norge AS\n	Specialists in WordPress theme development\n	http://metronet.no/\n-->\n\n";
	}
		
	/**
	 * Register external scripts
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function external_scripts() {
	
		// Bail out now if in admin panel or on login page
		if ( is_admin() OR strstr( $_SERVER['REQUEST_URI'], 'wp-login.php' ) )
			return;
	
		/* Loading jQuery for use by inline scripts
		 */
		wp_enqueue_script( 'jquery' );

		/* Register script for home page functionality.
		 * Then load script too ...
		 */
		wp_register_script(
			'home',
			get_template_directory_uri() . '/scripts/home.js',
			array( 'jquery' ),
			'1.0',
			false
		);
		wp_enqueue_script( 'home' );
	
		/* Comment reply script for sites with threaded comments.
		 */
		if ( is_singular() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );
	}

	/**
	 * Load the themes' CSS file
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function css() {
	
		// Bail out now if in admin panel or on login page
		if ( is_admin() OR strstr( $_SERVER['REQUEST_URI'], 'wp-login.php' ) )
			return;
	
		wp_enqueue_style( 'style.css', get_template_directory_uri() . '/style.css', false, '', 'screen' );
	}

	/**
	 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function page_menu_args( $args ) {
		$args['show_home'] = true;
		return $args;
	}

	/**
	 * Redirect to home page if on static page.
	 * Lasse Super does not include standalone static pages hence these are redirected.
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function page_redirect() {
		if ( is_page() && !is_front_page() ) {
			wp_redirect( home_url( '/' ), 301 );
			exit;
		}
	}

	/**
	 * Does something funky with search queries
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @param $query
	 */
	public function search( $query ) {
		if ( $query->is_search )
			$query->set( 'post_type', 'post' );
		return $query;
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which runs
	 * before the init hook. The init hook is too late for some features, such as indicating
	 * support post thumbnails.
	 *
	 * To override lassesuper_setup() in a child theme, add your own lassesuper_setup to your child theme's
	 * functions.php file.
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function theme_setup() {

		/* Make Lasse Super available for translation.
		 * Translations can be added to the /languages/ directory.
		 * If you're building a theme based on Lasse Super, use a find and replace
		 * to change 'lassesuper' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'lassesuper', get_template_directory() . '/languages' );
		$locale = get_locale();
		$locale_file = get_template_directory() . "/languages/$locale.php";
		if ( is_readable( $locale_file ) )
			require( $locale_file );

		// This theme styles the visual editor with editor-style.css to match the theme style.
		add_editor_style();
	
		// Add default posts and comments RSS feed links to <head>.
		add_theme_support( 'automatic-feed-links' );
	
		// This theme uses wp_nav_menu() in one location.
		register_nav_menu( 'primary', __( 'Primary Menu', 'lassesuper' ) );
	
		// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
		add_theme_support( 'post-thumbnails' );
	
		// Add custom header support
		add_theme_support(
			'custom-header',
			array(
				'default-image'          => '', // Header image default
				'header-text'            => false, // Header text display default
				'default-text-color'     => '', // Header text color default
				'width'                  => '1100', // Header image width (in pixels)
				'flex-width'             => true, // Header flex width changes width into suggested width
				'height'                 => '655', // Header image height (in pixels)
				'flex-height'            => true, // Header flex height changes height into suggested height
				'random-default'         => true, // Header image random rotation default
				'wp-head-callback'       => array( &$this, 'header_style' ), // Template header style callback
				'admin-head-callback'    => array( &$this, 'admin_header_image' ), // Admin header style callback
				'admin-preview-callback' => array( &$this, 'admin_header_style' ) // Admin preview style callback
			)
		);

		// Add support for custom backgrounds
		add_theme_support(
			'custom-background',
			array(
				'default-image'          => 'http://pixopoint.com/uploads/pixopoint-add-span-tags-480x246.png',
				'default-color'          => 'ff0000',
				'wp-head-callback'       => array( &$this, 'custom_background_callback' ),
				'admin-head-callback'    => '',
				'admin-preview-callback' => '',
			)
        );

		// We'll be using post thumbnails for custom header images on posts and pages.
		// We want them to be the size of the header image that we just defined
		// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
		set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );
	
		// Add Lasse Super's custom image sizes
		add_image_size( 'large-feature', HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true ); // Used for large feature (header) images
		add_image_size( 'small-feature', 500, 300 ); // Used for featured posts if a large-feature doesn't exist

		// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
		register_default_headers( array(
			'default' => array(
				'url' => '%s/images/headers/default.jpg',
				'thumbnail_url' => '%s/images/headers/default-thumbnail.jpg',
				/* translators: header image description */
				'description' => __( 'Default', 'lassesuper' )
			),
			'willow' => array(
				'url' => '%s/images/headers/willow.jpg',
				'thumbnail_url' => '%s/images/headers/willow-thumbnail.jpg',
				/* translators: header image description */
				'description' => __( 'Willow', 'lassesuper' )
			),
		) );

	}

	/**
	 * Styles the header image and text displayed on the blog
	 *
	 * Adapted from Justin Tadlock's tutorial ... http://justintadlock.com/archives/2011/06/08/custom-background-fix-for-theme-developers
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function custom_background_callback() {

		// Get the background image
		$image = get_background_image();

		// If there's an image, just call the normal WordPress callback. We won't do anything here
		if ( !empty( $image ) ) {
			_custom_background_cb();
			return;
		}

		// Get the background color
		$color = get_background_color();

		// If no background color, return
		if ( empty( $color ) )
			return;

		// Use 'background' instead of 'background-color'
		$style = "background: #{$color};";

		echo '<style type="text/css">body {' . trim( $style ) . '}</style>';
	}

    /**
     * Styles the header image and text displayed on the blog
     *
     * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
     */
	public function header_style() {

		echo "\n	<style>";
		
		// Has the text been hidden?
		if ( 'blank' == get_header_textcolor() || '' == get_header_textcolor() ) {
		echo "
		#site-title,
		#site-description {
			display: none;
		}";
		}
		
		// If the user has set a custom color for the text use that
		else {
		echo "
		#site-title a,
		#site-description {
			color: #<?php echo get_header_textcolor(); ?> !important;
		}";
		}
		
		echo "\n	</style>\n";
	}
    
	/**
	* Styles the header image displayed on the Appearance > Header admin panel.
	*
	* Referenced via add_custom_image_header() in lassesuper_setup().
	*
	* @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	*/
	public function admin_header_style() {
	   echo '<style>';
	
	   // If the user has set a custom color for the text use that
	   if ( get_header_textcolor() != HEADER_TEXTCOLOR ) {
		   echo '
	   #site-title a,
	   #site-description {
		   color: #<?php echo get_header_textcolor(); ?>;
	   }';
	   }
	
	   // Set image CSS
	   echo '
	   #headimg img {
		   max-width: 1000px;
		   height: auto;
		   width: 100%;
	   }';
	
	   echo '</style>';
	}
	
	/**
	* Custom header image markup displayed on the Appearance > Header admin panel.
	*
	* Referenced via add_custom_image_header() in lassesuper_setup().
	*
	* @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	*/
	public function admin_header_image() { ?>
	   <div id="headimg">
			<?php
			if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
					$style = ' style="display:none;"';
			else
			$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
			?>
			<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
			<?php $header_image = get_header_image();
			if ( ! empty( $header_image ) ) : ?>
				<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
			<?php endif; ?>
		</div>
	<?php }

	}
