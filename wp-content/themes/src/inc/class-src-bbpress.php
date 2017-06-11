<?php

/**
 * bbPress features.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_bbPress {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'wp_print_styles', array( $this, 'deregister_bbpress_styles' ), 15 );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'personal_options_update', array( $this, 'update_user_options' ) );

		// Add support for bbPress post thumbnails
		add_theme_support( 'post-thumbnails', array( 'topic' ) );
		add_post_type_support( 'topic', 'thumbnail' );

		// Add filters
		add_filter( 'bbp_get_topic_admin_links', array( $this, 'change_admin_links' ) );
		add_filter( 'bbp_get_reply_admin_links', array( $this, 'change_admin_links' ) );
		add_filter( 'bbp_reply_content_append_signature', array( $this, 'modify_signature_output' ) );
		add_filter('bbp_get_topic_author_avatar', array( $this, 'change_avatar_size' ), 20, 3);
		add_filter('bbp_get_reply_author_avatar', array( $this, 'change_avatar_size' ), 20, 3);
		add_filter('bbp_get_current_user_avatar', array( $this, 'change_avatar_size' ), 20, 3);
		add_filter('user_contactmethods',         array( $this, 'add_social_links' ) );

	}

	/**
	 * Init.
	 */
	public function init() {

		remove_action( 'wp_print_styles', 'bbp_signature_css' );

		add_filter( 'bbp_edit_user_signature_handler', 'wp_kses_post' );
		remove_filter( 'bbp_edit_user_signature_handler', 'trim' );
		remove_filter( 'bbp_edit_user_signature_handler', 'wp_filter_kses' );
		remove_filter( 'bbp_edit_user_signature_handler', 'force_balance_tags' );
		remove_filter( 'bbp_edit_user_signature_handler', '_wp_specialchars' );

	}

	/**
	 * Update the custom user options.
	 */
	public function update_user_options(  $user_id  ) {

		// Check for nonce otherwise bail
		if ( ! isset( $_POST['src_bpress_member_nonce'] ) || ! wp_verify_nonce( $_POST['src_bpress_member_nonce'], 'src_bpress_member_nonce' ) ) {
			return;
		}

		$metas = array(
			'nationality',
			'sim_experience',
			'sim_racing_achievements',
			'leagues_competed_in',
		);

		foreach ( $metas as $meta ) {
			$value = wp_kses_post( $_POST[$meta] );
			update_user_meta( $user_id, $meta, $value );
		}

	}

	/**
	 * Deregister the bbPress stylesheet(s).
	 */
	public function deregister_bbpress_styles() {
		wp_deregister_style( 'bbp-default' );
	}

	/**
	 * Change the admin links.
	 * Removes unneeded | symbol.
	 *
	 * @param  string  $string
	 * @return string
	 */
	public function change_admin_links( $content ) {
		$content = str_replace( ' | ', '', $content );

		return $content;
	}

	public function modify_signature_output( $content ) {

		$content = str_replace( '<hr />', '', $content );

		return $content;
	}

	public function change_avatar_size( $author_avatar, $topic_id, $size ) {
		$author_avatar = '';
		if ( $size == 80 ) {
			$size = 120;
		}
		$topic_id = bbp_get_topic_id( $topic_id );
		if ( ! empty( $topic_id ) ) {
			if ( !bbp_is_topic_anonymous( $topic_id ) ) {
				$author_avatar = get_avatar( bbp_get_topic_author_id( $topic_id ), $size );
			} else {
				$author_avatar = get_avatar( get_post_meta( $topic_id, '_bbp_anonymous_email', true ), $size );
			}
		}

		return $author_avatar;
	}

	/**
	 * Add social links to bbPress.
	 *
	 * @array  $links  The social links
	 * @return array  The modified social links
	 */
	function add_social_links( $links ) {

		// Add new ones
		$links['twitter'] = 'Twitter';
		$links['facebook'] = 'Facebook';
		$links['youtube'] = 'Youtube';
		$links['steam'] = 'Steam';

		// remove unwanted
		unset( $links['aim'] );
		unset( $links['jabber'] );
		unset( $links['yim'] );

		return $links;
	}

}
new SRC_bbPress;
