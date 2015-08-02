<?php

/**
 * Shortcode functions for the plugin.
 *
 * @package 	WPAS
 * @subpackage 	Includes
 * @since      	0.0.1
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @copyright  	Copyright (c) 2007 - 2013, Pulido Pereira Nuno Ricardo
 * @link       	http://nunoapps.com/plugins/wp-api-shortcode
 * @license    	http://www.gnu.org/licenses/gpl-2.0.html
 */

// Register main shortcode.
add_action( 'init', 'wpas_register_shortcodes' );

/**
 * Register the shortcode.
 *
 * @access 	public
 * @since 	0.0.1
 * @return 	void
 */
function wpas_register_shortcodes() {
	add_shortcode( 'wpapi', 'wpas_wpapi_shortcode_render' );
}

/**
 * Remder main shortcode and register all sub shortcodes.
 *
 * @access 	public
 * @since 	0.0.1
 * @param 	array 	Attributes
 * @param 	string 	The content
 * @return 	string
 */
function wpas_wpapi_shortcode_render( $atts, $content = null ) {

	extract( shortcode_atts( array(

		'slug' 	=> '',
		'type'	=> '' // 'plugin' or 'theme'

	), $atts ) );

	// Validate type.
	if ( 'plugin' !== $type && 'theme' !== $type )
		return do_shortcode( $content );

	// Validate slug.
	if ( ! $slug )
		return do_shortcode( $content );

	// Sanitize slug.
	$slug = sanitize_title( $slug );

	// Set the transient name related to this plugin and the slug of request.
	$transient_name = 'wpas-' . $slug;

	// Get info from transient.
	$info = get_transient( $transient_name );

	global $wpas;

	// Validate presence of info into transient, if not found call to wordpress.org.
	if ( empty( $info ) ) {

		// Init wpdotorg_api class
		/** @todo rewrite my class for fast connection. */
		$wpapi = new wpdotorg_api();

		// Validate presence on main object of info from call.
		if ( ! isset( $wpas->shortcode ) || ! isset( $wpas->shortcode[ $slug ] ) ) {

			$wpas->shortcode_slug 		= $slug;
			$wpas->shortcode[ $slug ] 	= ( 'plugin' === $type ) ? $wpapi->get_plugin( $slug ) : $wpapi->get_theme( $slug );

		}

		// Retrieve the informations.
		$info = $wpas->shortcode[ $slug ];

		// Set informations into transient.
		set_transient( $transient_name, $info, 1 * HOUR_IN_SECONDS );

	} else {

		$wpas->shortcode_slug = $slug;
		$wpas->shortcode[ $slug ] = $info;

	}

	/* Tag: Name. */
	add_shortcode( 'wpapi_name', 'wpas_wpapi_shortcode_name_render' );
	add_shortcode( 'wpapi_if_name', 'wpas_wpapi_shortcode_if_name_render' );

	/* Tag: Version. */
	add_shortcode( 'wpapi_version', 'wpas_wpapi_shortcode_version_render' );
	add_shortcode( 'wpapi_if_version', 'wpas_wpapi_shortcode_if_version_render' );

	/* Tag: Author. */
	add_shortcode( 'wpapi_author', 'wpas_wpapi_shortcode_author_render' );
	add_shortcode( 'wpapi_if_author', 'wpas_wpapi_shortcode_if_author_render' );

	/* Tag: Requires. */
	add_shortcode( 'wpapi_requires', 'wpas_wpapi_shortcode_requires_render' );
	add_shortcode( 'wpapi_if_requires', 'wpas_wpapi_shortcode_if_requires_render' );

	/* Tag: Tested. */
	add_shortcode( 'wpapi_tested', 'wpas_wpapi_shortcode_tested_render' );
	add_shortcode( 'wpapi_if_tested', 'wpas_wpapi_shortcode_if_tested_render' );

	/* Tag: Downloaded. */
	add_shortcode( 'wpapi_downloaded', 'wpas_wpapi_shortcode_downloaded_render' );
	add_shortcode( 'wpapi_if_downloaded', 'wpas_wpapi_shortcode_if_downloaded_render' );

	/* Tag: Rating. */
	add_shortcode( 'wpapi_rating', 'wpas_wpapi_shortcode_rating_render' );
	add_shortcode( 'wpapi_if_rating', 'wpas_wpapi_shortcode_if_rating_render' );

	/* Tag: Num_Ratings. */
	add_shortcode( 'wpapi_num_ratings', 'wpas_wpapi_shortcode_num_ratings_render' );
	add_shortcode( 'wpapi_if_num_ratings', 'wpas_wpapi_shortcode_if_num_ratings_render' );

	/* Tag: Download_Link. */
	add_shortcode( 'wpapi_download_link', 'wpas_wpapi_shortcode_download_link_render' );
	add_shortcode( 'wpapi_if_download_link', 'wpas_wpapi_shortcode_if_download_link_render' );

	/* Tag: Description. */
	add_shortcode( 'wpapi_description', 'wpas_wpapi_shortcode_description_render' );
	add_shortcode( 'wpapi_if_description', 'wpas_wpapi_shortcode_if_description_render' );

	/* Tag: Screenshots. */
	add_shortcode( 'wpapi_screenshots', 'wpas_wpapi_shortcode_screenshots_render' );
	add_shortcode( 'wpapi_if_screenshots', 'wpas_wpapi_shortcode_if_screenshots_render' );

	/* Tag: Slug. */
	add_shortcode( 'wpapi_slug', 'wpas_wpapi_shortcode_slug_render' );
	add_shortcode( 'wpapi_if_slug', 'wpas_wpapi_shortcode_if_slug_render' );

	/* Tag: Author_Profile. */
	add_shortcode( 'wpapi_author_profile', 'wpas_wpapi_shortcode_author_profile_render' );
	add_shortcode( 'wpapi_if_author_profile', 'wpas_wpapi_shortcode_if_author_profile_render' );

	/* Tag: Homepage. */
	add_shortcode( 'wpapi_homepage', 'wpas_wpapi_shortcode_homepage_render' );
	add_shortcode( 'wpapi_if_homepage', 'wpas_wpapi_shortcode_if_homepage_render' );

	/* Tag: Added. */
	add_shortcode( 'wpapi_added', 'wpas_wpapi_shortcode_added_render' );
	add_shortcode( 'wpapi_if_added', 'wpas_wpapi_shortcode_if_added_render' );

	/* Tag: Last_Updated. */
	add_shortcode( 'wpapi_last_updated', 'wpas_wpapi_shortcode_last_updated_render' );
	add_shortcode( 'wpapi_if_last_updated', 'wpas_wpapi_shortcode_if_last_updated_render' );

	/* Tag: Contributors. */
	add_shortcode( 'wpapi_contributors', 'wpas_wpapi_shortcode_contributors_render' );
	add_shortcode( 'wpapi_if_contributors', 'wpas_wpapi_shortcode_if_contributors_render' );

	/* Tag: Installation. */
	add_shortcode( 'wpapi_installation', 'wpas_wpapi_shortcode_installation_render' );
	add_shortcode( 'wpapi_if_installation', 'wpas_wpapi_shortcode_if_installation_render' );

	/* Tag: Changelog. */
	add_shortcode( 'wpapi_changelog', 'wpas_wpapi_shortcode_changelog_render' );
	add_shortcode( 'wpapi_if_changelog', 'wpas_wpapi_shortcode_if_changelog_render' );

	/* Tag: Faq. */
	add_shortcode( 'wpapi_faq', 'wpas_wpapi_shortcode_faq_render' );
	add_shortcode( 'wpapi_if_faq', 'wpas_wpapi_shortcode_if_faq_render' );

	/* Tag: Other_Notes. */
	add_shortcode( 'wpapi_other_notes', 'wpas_wpapi_shortcode_other_notes_render' );
	add_shortcode( 'wpapi_if_other_notes', 'wpas_wpapi_shortcode_if_other_notes_render' );

	/* Tag: Donate_Link. */
	add_shortcode( 'wpapi_donate_link', 'wpas_wpapi_shortcode_donate_link_render' );
	add_shortcode( 'wpapi_if_donate_link', 'wpas_wpapi_shortcode_if_donate_link_render' );

	return do_shortcode( $content );

}

/**
 * Sub shortcode 'donate_link'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_donate_link_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->donate_link ) || '' === $wpas->shortcode[ $slug ]->donate_link )
		return false;

	return $wpas->shortcode[ $slug ]->donate_link;

}

/**
 * Sub shortcode condition 'donate_link'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_donate_link_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->donate_link ) || '' === $wpas->shortcode[ $slug ]->donate_link )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'other_notes'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_other_notes_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->sections['other_notes'] ) || '' === $wpas->shortcode[ $slug ]->sections['other_notes'] )
		return false;

	return $wpas->shortcode[ $slug ]->sections['other_notes'];

}

/**
 * Sub shortcode condition 'other_notes'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_other_notes_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->sections['other_notes'] ) || '' === $wpas->shortcode[ $slug ]->sections['other_notes'] )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'faq'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_faq_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->sections['faq'] ) || '' === $wpas->shortcode[ $slug ]->sections['faq'] )
		return false;

	$faq = $wpas->shortcode[ $slug ]->sections['faq'];
	$faq = str_replace( '<h4>', '<h6>', $faq );
	$faq = str_replace( '</h4>', '</h6>', $faq );
	return $faq;
}

/**
 * Sub shortcode condition 'faq'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_faq_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->sections['faq'] ) || '' === $wpas->shortcode[ $slug ]->sections['faq'] )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'changelog'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_changelog_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->sections['changelog'] ) || '' === $wpas->shortcode[ $slug ]->sections['changelog'] )
		return false;

	return $wpas->shortcode[ $slug ]->sections['changelog'];

}

/**
 * Sub shortcode condition 'changelog'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_changelog_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->sections['changelog'] ) || '' === $wpas->shortcode[ $slug ]->sections['changelog'] )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'installation'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_installation_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->sections['installation'] ) || '' === $wpas->shortcode[ $slug ]->sections['installation'] )
		return false;

	return $wpas->shortcode[ $slug ]->sections['installation'];

}

/**
 * Sub shortcode condition 'installation'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_installation_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->sections['installation'] ) || '' === $wpas->shortcode[ $slug ]->sections['installation'] )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'contributors'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_contributors_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( isset( $wpas->shortcode[ $slug ]->contributors ) && '' === $wpas->shortcode[ $slug ]->contributors )
		return false;

	$output = '';

	$output .= '<ul class="wpsa-contributors">' . "\n\t";

	foreach ( $wpas->shortcode[ $slug ]->contributors as $contributor_name => $contributor_link )
		$output .= '<li><a href="' . $contributor_link . '">' . $contributor_name . '</a></li>' . "\n";

	$output .= "\r" . '</ul>' . "\n";

	return $output;

}

/**
 * Sub shortcode condition 'contributors'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_contributors_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->contributors ) || '' === $wpas->shortcode[ $slug ]->contributors )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'last_updated'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_last_updated_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->last_updated ) || '' === $wpas->shortcode[ $slug ]->last_updated )
		return false;

	return $wpas->shortcode[ $slug ]->last_updated;

}

/**
 * Sub shortcode condition 'last_updated'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_last_updated_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->last_updated ) || '' === $wpas->shortcode[ $slug ]->last_updated )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'added'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_added_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->added ) || '' === $wpas->shortcode[ $slug ]->added )
		return false;

	return $wpas->shortcode[ $slug ]->added;

}

/**
 * Sub shortcode condition 'added'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_added_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->added ) || '' === $wpas->shortcode[ $slug ]->added )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'homepage'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_homepage_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->homepage ) || '' === $wpas->shortcode[ $slug ]->homepage )
		return false;

	return $wpas->shortcode[ $slug ]->homepage;

}

/**
 * Sub shortcode condition 'homepage'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_homepage_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->homepage ) || '' === $wpas->shortcode[ $slug ]->homepage )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'author_profile'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_author_profile_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->author_profile ) || '' === $wpas->shortcode[ $slug ]->author_profile )
		return false;

	return $wpas->shortcode[ $slug ]->author_profile;

}

/**
 * Sub shortcode condition 'author_profile'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_author_profile_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->author_profile ) || '' === $wpas->shortcode[ $slug ]->author_profile )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'slug'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_slug_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->slug ) || '' === $wpas->shortcode[ $slug ]->slug )
		return false;

	return $wpas->shortcode[ $slug ]->slug;

}

/**
 * Sub shortcode condition 'slug'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_slug_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->slug ) || '' === $wpas->shortcode[ $slug ]->slug )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'screenshots'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_screenshots_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->sections['screenshots'] ) || '' === $wpas->shortcode[ $slug ]->sections['screenshots'] )
		return false;

	return '<div class="wp-api-screenshots">' . $wpas->shortcode[ $slug ]->sections['screenshots'] . '</div>';

}

/**
 * Sub shortcode condition 'screenshots'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_screenshots_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->sections['screenshots'] ) || '' === $wpas->shortcode[ $slug ]->sections['screenshots'] )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'description'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_description_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->sections['description'] ) || '' === $wpas->shortcode[ $slug ]->sections['description'] )
		return false;

	return $wpas->shortcode[ $slug ]->sections['description'];

}

/**
 * Sub shortcode condition 'description'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_description_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->sections['description'] ) || '' === $wpas->shortcode[ $slug ]->sections['description'] )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'download_link'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_download_link_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;
//echo $slug;
	if ( ! isset( $wpas->shortcode[ $slug ]->download_link ) || '' === $wpas->shortcode[ $slug ]->download_link )
		return false;

	return '<strong><a href="' . esc_url( $wpas->shortcode[ $slug ]->download_link ) . '">Download the ' . do_shortcode( '[wpapi_name]' ) . ' plugin.</a></strong>';
}

/**
 * Sub shortcode condition 'download_link'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_download_link_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->download_link ) || '' === $wpas->shortcode[ $slug ]->download_link )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'num_ratings'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_num_ratings_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->num_ratings ) || '' === $wpas->shortcode[ $slug ]->num_ratings )
		return false;

	return $wpas->shortcode[ $slug ]->num_ratings;

}

/**
 * Sub shortcode condition 'num_ratings'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_num_ratings_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->num_ratings ) || '' === $wpas->shortcode[ $slug ]->num_ratings )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'rating'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_rating_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->rating ) || '' === $wpas->shortcode[ $slug ]->rating )
		return false;

	return $wpas->shortcode[ $slug ]->rating;

}

/**
 * Sub shortcode condition 'rating'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_rating_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->rating ) || '' === $wpas->shortcode[ $slug ]->rating )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'downloaded'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_downloaded_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->downloaded ) || '' === $wpas->shortcode[ $slug ]->downloaded )
		return false;

	return $wpas->shortcode[ $slug ]->downloaded;

}

/**
 * Sub shortcode condition 'downloaded'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_downloaded_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->downloaded ) || '' === $wpas->shortcode[ $slug ]->downloaded )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'tested'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_tested_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->tested ) || '' === $wpas->shortcode[ $slug ]->tested )
		return false;

	return $wpas->shortcode[ $slug ]->tested;

}

/**
 * Sub shortcode condition 'tested'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_tested_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->tested ) || '' === $wpas->shortcode[ $slug ]->tested )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'requires'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_requires_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->requires ) || '' === $wpas->shortcode[ $slug ]->requires )
		return false;

	return $wpas->shortcode[ $slug ]->requires;

}

/**
 * Sub shortcode condition 'requires'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_requires_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->requires ) || '' === $wpas->shortcode[ $slug ]->requires )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'author'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_author_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->author ) || '' === $wpas->shortcode[ $slug ]->author )
		return false;

	return $wpas->shortcode[ $slug ]->author;

}

/**
 * Sub shortcode condition 'author'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_author_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->author ) || '' === $wpas->shortcode[ $slug ]->author )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'name'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_name_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->name ) || '' === $wpas->shortcode[ $slug ]->name )
		return false;

	return $wpas->shortcode[ $slug ]->name;

}

/**
 * Sub shortcode condition 'name'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_name_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->name ) || '' === $wpas->shortcode[ $slug ]->name )
		return false;
	else
		return do_shortcode( $content );

}

/**
 * Sub shortcode 'version'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @return string
 */
function wpas_wpapi_shortcode_version_render( $atts ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->version ) || '' === $wpas->shortcode[ $slug ]->version )
		return false;

	return $wpas->shortcode[ $slug ]->version;

}

/**
 * Sub shortcode condition 'version'.
 *
 * @since 0.0.1
 * @param array Attributes
 * @param string The content
 * @return string
 */
function wpas_wpapi_shortcode_if_version_render( $atts, $content = null ) {

	global $wpas;

	$slug = $wpas->shortcode_slug;

	if ( ! isset( $wpas->shortcode[ $slug ]->version ) || '' === $wpas->shortcode[ $slug ]->version )
		return false;
	else
		return do_shortcode( $content );

}


