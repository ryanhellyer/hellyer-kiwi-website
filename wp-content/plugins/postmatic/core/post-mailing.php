<?php

class Prompt_Post_Mailing {

	/** @var array */
	protected static $shortcode_whitelist = array( 'gallery', 'caption', 'wpv-post-body', 'types' );
	/** @var  string */
	protected static $featured_image_src;

	/**
	 * Send email notifications for a post.
	 *
	 * Sends up to 25 unsent notifications, and schedules another batch if there are more.
	 *
	 * @param WP_Post|int $post
	 * @param string $signature Optional identifier for this batch.
	 */
	public static function send_notifications( $post, $signature = '' ) {

		$post = get_post( $post );

		$prompt_post = new Prompt_Post( $post );

		$recipient_ids = $prompt_post->unsent_recipient_ids();

		$chunks = array_chunk( $recipient_ids, 25 );

		if ( empty( $chunks[0] ) )
			return;

		$chunk_ids = $chunks[0];

		/**
		 * Filter whether to send new post notifications. Default true.
		 *
		 * @param boolean $send Whether to send notifications.
		 * @param WP_Post $post
		 * @param array $recipient_ids
		 */
		if ( !apply_filters( 'prompt/send_post_notifications', true, $post, $chunk_ids ) )
			return;

		// We will attempt to notifiy these IDs - setting sent early could help lock other processes out
		$prompt_post->add_sent_recipient_ids( $chunk_ids );

		$prompt_site = new Prompt_Site();
		$prompt_author = new Prompt_User( get_userdata( $post->post_author ) );

		self::setup_postdata( $post );

		self::$featured_image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'prompt-post-featured' );

		if ( Prompt_Admin_Delivery_Metabox::suppress_featured_image( $post->ID ) )
			self::$featured_image_src = false;

		$excerpt_only = Prompt_Admin_Delivery_Metabox::excerpt_only( $post->ID );

		$emails = array();
		foreach ( $chunk_ids as $user_id ) {
			$user = get_userdata( $user_id );

			if ( !is_email( $user->user_email ) )
				continue;

			$unsubscribe_link = new Prompt_Unsubscribe_Link( $user );

			$template_data = array(
				'prompt_author' => $prompt_author,
				'recipient' => $user,
				'prompt_post' => $prompt_post,
				'subscribed_object' => $prompt_author->is_subscribed( $user_id ) ? $prompt_author : $prompt_site,
				'featured_image_src' => self::$featured_image_src,
				'excerpt_only' => $excerpt_only,
				'the_text_content' => self::get_the_text_content(),
				'subject' => html_entity_decode( $prompt_post->get_wp_post()->post_title, ENT_QUOTES ),
				'unsubscribe_url' => $unsubscribe_link->url(),
				'alternate_versions_menu' => self::alternate_versions_menu( $post ),
			);
			/**
			 * Filter new post email template data.
			 *
			 * @param array $template {
			 *      @type Prompt_User $prompt_author
			 *      @type WP_User $recipient
			 *      @type Prompt_Post $prompt_post
			 *      @type Prompt_Interface_Subscribable $subscribed_object
			 *      @type array $featured_image_src url, width, height
			 *      @type bool $excerpt_only whether to include only the post excerpt
			 *      @type string $the_text_content
			 *      @type string $subject
			 *      @type string $unsubscribe_url
			 * }
			 */
			$template_data = apply_filters( 'prompt/post_email/template_data', $template_data );

			$email = self::build_email( $template_data );

			/**
			 * Filter new post email.
			 *
			 * @param Prompt_Email $email
			 * @param array $template see prompt/post_email/template_data
			 * }
			 */
			$emails[] = apply_filters( 'prompt/post_email', $email, $template_data );
		}

		self::reset_postdata();

		$result = Prompt_Factory::make_mailer()->send_many( $emails );

		if ( is_wp_error( $result ) )
			self::send_error_notifications( $post, $result );

		if ( !empty( $chunks[1] ) ) {

			wp_schedule_single_event(
				time(),
				'prompt/post_mailing/send_notifications',
				array( $post->ID, implode( '', $chunks[1] ) )
			);

		}

	}

	/**
	 * Set up the global environment needed to render a post email.
	 * @var WP_Post $post
	 */
	public static function setup_postdata( $post ) {

		query_posts( array( 'p' => $post->ID, 'post_type' => $post->post_type, 'post_status' => $post->post_status ) );

		the_post();

		if ( class_exists( 'ET_Bloom' ) ) {
			$bloom = ET_Bloom::get_this();
			remove_filter( 'the_content', array( $bloom, 'display_below_post') );
			remove_filter( 'the_content', array( $bloom, 'trigger_bottom_mark' ), 9999 );
			// TODO: restore these?
		}

		remove_filter( 'the_content', 'do_shortcode', 11 );
		remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'run_shortcode' ), 8 );
		add_filter( 'the_content', array( __CLASS__, 'do_whitelisted_shortcodes' ), 11 );
		add_filter( 'the_content', array( __CLASS__, 'strip_image_height_attributes' ), 11 );
		add_filter( 'the_content', array( __CLASS__, 'limit_image_width_attributes' ), 11 );
		add_filter( 'the_content', array( __CLASS__, 'strip_incompatible_tags' ), 11 );
		add_filter( 'the_content', array( __CLASS__, 'strip_duplicate_featured_images' ), 100 );
		add_filter( 'embed_oembed_html', array( __CLASS__, 'use_original_oembed_url' ), 10, 2 );

	}

	/**
	 * Reset the global environment after rendering post emails.
	 */
	public static function reset_postdata() {

		wp_reset_query();

		remove_filter( 'embed_oembed_html', array( __CLASS__, 'use_original_oembed_url' ), 10, 2 );
		remove_filter( 'the_content', array( __CLASS__, 'strip_incompatible_tags' ), 11 );
		remove_filter( 'the_content', array( __CLASS__, 'limit_image_width_attributes' ), 11 );
		remove_filter( 'the_content', array( __CLASS__, 'strip_image_height_attributes' ), 11 );
		remove_filter( 'the_content', array( __CLASS__, 'do_whitelisted_shortcodes' ), 11 );
		remove_filter( 'the_content', array( __CLASS__, 'strip_duplicate_featured_images' ), 100 );
		add_filter( 'the_content', 'do_shortcode', 11 );
		add_filter( 'the_content', array( $GLOBALS['wp_embed'], 'run_shortcode' ), 8 );

	}

	/**
	 * Get Postmatic's text version of the current post content.
	 * @return mixed|string
	 */
	public static function get_the_text_content() {

		$prompt_post = new Prompt_Post( get_the_ID() );

		$text = $prompt_post->get_custom_text();

		if ( $text )
			return $text;

		if ( Prompt_Admin_Delivery_Metabox::excerpt_only( $prompt_post->id() ) )
			return Prompt_Html_To_Markdown::convert( get_the_excerpt() );

		$html = apply_filters( 'the_content', get_the_content() );

		$html = str_replace( ']]>', ']]&gt;', $html );

		return Prompt_Html_To_Markdown::convert( $html );
	}

	/**
	 * Build a single post email
	 * @param array $template_data see prompt/post_email/template_data
	 * @return Prompt_Email the fully rendered email
	 */
	public static function build_email( $template_data ) {

		/** @var Prompt_Interface_Subscribable $subscribed_object */
		/** @var Prompt_Post $prompt_post */
		/** @var Prompt_User $prompt_author */
		/** @var WP_User $recipient */
		/** @var string $subject */
		/** @var bool $excerpt_only */
		extract( $template_data );

		$html_template = new Prompt_Email_Template( "new-post-email.php" );
		$text_template = new Prompt_Text_Email_Template( "new-post-email-text.php" );

		$from_name = get_option( 'blogname' );
		if ( is_a( $subscribed_object, 'Prompt_User' ) and $prompt_author->id() )
			$from_name .= ' [' . $prompt_author->get_wp_user()->display_name . ']';

		$email = new Prompt_Email( array(
			'to_address' => $recipient->user_email,
			'subject' => $subject,
			'from_name' => $from_name,
			'text' => $text_template->render( $template_data ),
			'message_type' => Prompt_Enum_Message_Types::POST,
		) );

		if ( Prompt_Enum_Email_Transports::API == Prompt_Core::$options->get( 'email_transport' ) )
			$email->set_html( $html_template->render( $template_data ) );

		if ( comments_open( $prompt_post->id() ) and ! $excerpt_only ) {

			$command = new Prompt_New_Post_Comment_Command();
			$command->set_post_id( $prompt_post->id() );
			$command->set_user_id( $recipient->ID );
			Prompt_Command_Handling::add_command_metadata( $command, $email );

		} else {

			$email->set_from_address( $prompt_author->get_wp_user()->user_email );

		}

		return $email;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public static function strip_image_height_attributes( $content ) {
		return preg_replace( '/(<img[^>]*?) height=["\']\d*["\']([^>]*?>)/', '$1$2', $content );
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public static function limit_image_width_attributes( $content ) {
		return preg_replace_callback(
			'/(<img[^>]*?) width=["\'](\d*)["\']([^>]*?>)/',
			array( __CLASS__, 'limit_image_width_attribute' ),
			$content
		);
	}

	public static function limit_image_width_attribute( $match ) {
		$max_width = 709;

		$width = intval( $match[2] );

		if ( $width <= $max_width )
			return $match[0];

		$tag = $match[1] . ' width="' . $max_width . '"' . $match[3];

		return self::add_image_size_class( $tag );
	}

	public static function strip_incompatible_tags( $content ) {

		if ( false === strpos( $content, '<iframe' ) and false === strpos( $content, '<object' ) )
			return $content;

		$content = preg_replace_callback(
			'#<(iframe|object)([^>]*)(src|data)=[\'"]([^\'"]*)[\'"][^>]*>.*?<\\/\\1>#',
			array( __CLASS__, 'strip_incompatible_tag' ),
			$content
		);

		return $content;
	}

	public static function strip_incompatible_tag( $m ) {
		$class = $m[1];

		$url_parts = parse_url( $m[4] );

		$url = null;
		if ( $url_parts and isset( $url_parts['host'] ) ) {
			$class = 'embed ' . str_replace( '.', '-', $url_parts['host'] );
			$url = $m[4];
		}

		return self::incompatible_placeholder( $class, $url );
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public static function do_whitelisted_shortcodes( $content ) {
		global $shortcode_tags;

		if ( false === strpos( $content, '[' ) ) {
			return $content;
		}

		if (empty($shortcode_tags) || !is_array($shortcode_tags))
			return $content;

		add_filter( 'shortcode_atts_gallery', array( __CLASS__, 'override_gallery_attributes' ), 10, 3 );

		$pattern = get_shortcode_regex();
		$content = preg_replace_callback( "/$pattern/s", array( __CLASS__, 'do_whitelisted_shortcode_tag' ), $content );

		remove_filter( 'shortcode_atts_gallery', array( __CLASS__, 'override_gallery_attributes' ), 10, 3 );

		return $content;
	}

	/**
	 * @param array $m
	 * @return string
	 */
	public static function do_whitelisted_shortcode_tag( $m ) {

		$tag = $m[2];

		if ( 'wpgist' == $tag )
			return self::override_wp_gist_shortcode_tag( $m );

		if ( in_array( $tag, self::$shortcode_whitelist ) )
			return do_shortcode_tag( $m );

		return $m[1] . self::incompatible_placeholder( $tag ) . $m[6];
	}

	/**
	 * Use the old HTML 4 default gallery tags for better email (gmail) client support.
	 *
	 * @param array $out
	 * @param array $pairs
	 * @param array $atts
	 * @return array Overriden attributes.
	 */
	public static function override_gallery_attributes( $out, $pairs, $atts ) {
		$out['itemtag'] = 'dl';
		$out['icontag'] = 'dt';
		$out['captiontag'] = 'dd';
		return $out;
	}

	/**
	 * Replace constructed provider URL with the original for placeholders.
	 *
	 * @see oembed_dataparse WordPress filter
	 *
	 * @param string $html
	 * @param string $url
	 * @return string
	 */
	public static function use_original_oembed_url( $html, $url ) {
		return preg_replace( '#https?://[^"\']*#', $url, $html );
	}

	/**
	 * @param WP_Post $post
	 * @return string Menu HTML.
	 */
	public static function alternate_versions_menu( $post ) {
		global $polylang;

		if ( ! class_exists( 'PLL_Switcher' ) )
			return '';

		$switcher = new PLL_Switcher();

		$languages = $switcher->the_languages(
			$polylang->links,
			array(
				'post_id' => $post->ID,
				'echo' => false,
				'hide_if_no_translation' => true,
				'hide_current' => true,
			)
		);

		return empty( $languages ) ? '' : html( 'ul class="alternate-languages"', $languages );
	}

	/**
	 * Remove featured images of any size if Postmatic is supplying one.
	 *
	 * @param string $content
	 * @return string
	 */
	public static function strip_duplicate_featured_images( $content ) {

		if ( empty( self::$featured_image_src[0] ) )
			return $content;

		$url = self::$featured_image_src[0];

		$last_hyphen_pos = strrpos( $url, '-');

		$match = $last_hyphen_pos ? substr( $url, 0, $last_hyphen_pos ) : $url;

		return preg_replace(
			'/<img[^>]*src=["\']' . preg_quote( $match, '/' ) . '[^>]*>/',
			'',
			$content
		);
	}

	protected static function override_wp_gist_shortcode_tag( $m ) {
		$defaults = array( 'file' => '', 'id' => '', 'url' => '' );

		$atts = shortcode_atts( $defaults, shortcode_parse_atts( $m[3] ) );

		if ( empty( $atts['id'] ) and empty( $atts['url'] ) )
			return '';

		if ( empty( $atts['id'] ) ) {
			$url_parts = parse_url( $atts['url'] );
			$atts['id'] = basename( $url_parts['path'] );
		}

		$api_url = 'https://api.github.com/gists/' . $atts['id'];

		$response = wp_remote_get( $api_url );
		$json = wp_remote_retrieve_body( $response );

		if ( !$json )
			return '';

		$gist = json_decode( $json, $associative_arrays = true );
		$files = $gist['files'];

		if ( empty( $atts['file'] ) or empty( $files[$atts['file'] ] ) ) {
			$file_keys = array_keys( $files );
			$atts['file'] = $file_keys[0];
		}

		$content = $files[$atts['file']]['content'];

		return html( 'pre class="wp-gist"', esc_html( $content ) );
	}

	protected static function incompatible_placeholder( $class = '', $url = null ) {
		$class = 'incompatible' . ( $class ? ' ' . $class : '' );
		$url = $url ? $url : get_permalink();
		return html( 'div',
			array( 'class' => $class ),
			__( 'This content is not compatible with your email client. ', 'Postmatic' ),
			html( 'a',
				array( 'href' => $url ),
			__( 'Click here to view this content in your browser.', 'Postmatic' )
			)
		);
	}
	protected static function send_error_notifications( $post, $error ) {

		$recipient_id = get_current_user_id() ? get_current_user_id() : $post->post_author;

		$recipient = get_userdata( $recipient_id );

		if ( !$recipient or empty( $recipient->user_email ) )
			return;

		$message = sprintf(
				__( 'Delivery of subscription notifications for the post "%s" may have failed.', 'Postmatic' ),
				get_the_title( $post )
			) .
			' ' .
			__( 'A site administrator can report this event to the development team from the Postmatic settings.', 'Postmatic' ) .
			' ' .
			__( 'The error message was: ', 'Postmatic' ) . $error->get_error_message();

		$email = new Prompt_Email( array(
			'to_address' => $recipient->user_email,
			'from_address' => $recipient->display_name,
			'subject' => sprintf( __( 'Delivery issue for %s', 'Postmatic' ), get_option( 'blogname' ) ),
			'text' => $message,
			'message_type' => Prompt_Enum_Message_Types::ADMIN,
		));

		Prompt_Factory::make_mailer( Prompt_Enum_Email_Transports::LOCAL )->send_one( $email );

	}

	protected static function add_image_size_class( $tag ) {

		if ( preg_match( '/class=[\'"]([^\'"]*)[\'"]/', $tag, $matches ) ) {
			$classes = explode( ' ', $matches[1] );
			$classes[] = 'retina';
			return str_replace( $matches[0], 'class="' . implode( ' ', $classes ) . '"', $tag );
		}

		return str_replace( '<img', '<img class="retina"', $tag );
	}

}