<?php
/**
 * @package Fruit Shake
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * @uses fruit_shake_header_style()
 * @uses fruit_shake_admin_header_style()
 * @uses fruit_shake_admin_header_image()
 *
 * @package Fruit Shake
 */
function fruit_shake_custom_header_setup() {
	$fruit_scheme = fruit_shake_current_fruit_scheme();

	add_theme_support( 'custom-header', apply_filters( 'fruit_shake_custom_header_args', array(
		'default-text-color'     => '000',
		'width'                  => 980,
		'height'                 => 285,
		'wp-head-callback'       => 'fruit_shake_header_style',
		'admin-head-callback'    => 'fruit_shake_admin_header_style',
		'admin-preview-callback' => 'fruit_shake_admin_header_image',
	) ) );

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		$fruit_scheme => array(
			'url'           => '%s/images/headers/header-' . $fruit_scheme . '.png',
			'thumbnail_url' => '%s/images/headers/header-' . $fruit_scheme . '-thumbnail.png',
			'description'   => '',
		),
	) );
}
add_action( 'after_setup_theme', 'fruit_shake_custom_header_setup' );


if ( ! function_exists( 'fruit_shake_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see fruit_shake_custom_header_setup().
 */
function fruit_shake_header_style() {
	$header_text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == $header_text_color )
		return;
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == $header_text_color ) :
	?>
		#site-title,
		#site-description,
		.secondary #branding #searchform {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		#site-title,
		#site-description {
			color: #<?php echo $header_text_color; ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // fruit_shake_header_style

if ( ! function_exists( 'fruit_shake_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see fruit_shake_custom_header_setup().
 */
function fruit_shake_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		border: 10px solid #333;
		border-bottom: 1px solid #e4e0d5;
		border-radius: 10px 10px 0 0;
		max-width: 980px;
	}
	#headimg h1,
	#desc {
		font: 300 14px/1.5 'Helvetica Neue',Helvetica,Arial,sans-serif;
	}
	#headimg h1 {
		margin: 0;
	}
	#headimg h1 a {
		color: #666;
		display: inline-block;
		font-size: 36px;
		font-weight: 100;
		text-decoration: none;
		letter-spacing: 0.075em;
		line-height: 48px;
		padding: 32px 40px 18px;
	}
	#desc {
		background: #fdf9ee;
		border: 1px solid #e4e0d5;
		border-width: 1px 0;
		color: #7E7A6F;
		font-size: 10px;
		padding: 11px 40px;
		text-transform: uppercase;
		letter-spacing: 0.15em;
	}
	#headimg img {
		border: 1px solid #e4e0d5;
		border-width: 1px 0;
		clear: both;
		height: auto;
		margin: 4px 0 -4px;
		max-width: 100%;
	}
	</style>
<?php
}
endif; // fruit_shake_admin_header_style

if ( ! function_exists( 'fruit_shake_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see fruit_shake_custom_header_setup().
 */
function fruit_shake_admin_header_image() {
	$style        = sprintf( ' style="color:#%s;"', get_header_textcolor() );
	$header_image = get_header_image();
?>
	<div id="headimg">
		<h1 class="displaying-header-text"><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div class="displaying-header-text" id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<?php if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
		<?php endif; ?>
	</div>
<?php
}
endif; // fruit_shake_admin_header_image