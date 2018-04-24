<?php

/**
 * Spam Annhilator Members.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Spam Annhilator theme
 * @since Spam Annhilator theme 1.0
 */
class Spam_Annhilator_Members {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'init',           array( $this, 'post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'rest_api_init',  array( $this, 'register_routes' ) );
		add_action( 'wp_footer',      array( $this, 'js_templates' ), 5 );
		add_action( 'template_redirect', array( $this, 'redirect_on_login' ) );

		// Add filters
		add_filter( 'init',               array( $this, 'member_template' ), 99 );
		add_filter( 'body_class',         array( $this, 'body_class' ) );

		// Add shortcodes
		add_shortcode( 'redirect_table',  array( $this, 'redirect_table' ) );

	}

	/**
	 * Register the post-types.
	 */
	public function post_type() {

		register_post_type(
			'check',
			array(
				'public'             => true,
				'publicly_queryable' => true,
				'label'              => esc_html__( 'Spam Checker', 'spamannhilator' ),
				'supports'           => array( 'title' ),
				'menu_icon'          => 'dashicons-flag',
			)
		);

	}

	/**
	 * Add admin metabox.
	 */
	public function add_metabox() {

		add_meta_box(
			'redirect_url', // ID
			__( 'Redirect URL', 'spamannhilator' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			'check', // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);

	}

	/**
	 * Output the meta box.
	 */
	public function meta_box() {

		echo '<p>' . esc_html( get_post_meta( get_the_ID(), '_redirect_url', true ) ) . '</p>';

	}

	/**
	 * Register URL routes for REST API requests.
	 */
	public function register_routes() {

		register_rest_route( 'spamannhilator/v1', '/get', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'get_rows' ),
		) );

		register_rest_route( 'spamannhilator/v1', '/save', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'save' ),
		) );

		register_rest_route( 'spamannhilator/v1', '/delete', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'delete' ),
		) );

	}

	/**
	 * Do security check on AJAX request.
	 *
	 * @param    int    $user_id   The user ID
	 * @access   private
	 * @return   bool   true if security check is passed
	 */
	private function security_check( $user_id ) {

		// Check user has permission
		if ( false === $this->is_author_allowed( $user_id ) ) {
			return 'User not allowed to access this';
		}

		// Check query vars set.
		if ( ! isset( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
			return false;
		}

		// Perform nonce check
		$nonce = $_SERVER['HTTP_X_WP_NONCE'];
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check user has permission.
	 *
	 * @param  int  $user_id  The user ID
	 * @access private
	 * @return int | bool
	 */
	private function is_author_allowed( $user_id ) {

		$user = get_userdata( $user_id );
		if ( $user === false ) {
			return false;
		}

		// Only allow admins to load other users profile pages
		if (
			$user_id !== get_current_user_id()
			&&
			! current_user_can( 'manage_options' )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Get the table rows.
	 * eg: /wp-json/spamannhilator/v1/get?username=ryan
	 *
	 * @param  array  $request  Request vars
	 * @return string | array
	 */
	public function get_rows( $request ) {

		$request_params = $request->get_query_params();

		// Set user ID variable
		if ( isset( $request_params[ 'user_id' ] ) ) {
			$user_id = $request_params[ 'user_id' ];
		} else {
			return 'User ID not set';
		}

		// Perform security check
		if ( false === $this->security_check( $user_id ) ) {
			return 'Failed security check';
		}


		// Get redirects
		$query = new WP_Query( array(
			'author'                 => $user_id,
			'posts_per_page'         => 100,
			'post_type'              => 'check',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );

		$redirects = array();
		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {
				$query->the_post();

				$slug = get_post_field( 'post_name', get_post( get_the_ID() ) );

				$redirects[] = array(
					'id'                      => get_the_ID(),
					'modified_date_unix'      => get_the_modified_date( 'U' ),
					'modified_date_formatted' => get_the_modified_date( 'Y-m-d H:m:s' ),
					'slug'                    => esc_html( $slug ),
					'redirect_url'            => esc_url( get_post_meta( get_the_ID(), '_redirect_url', true ) ),
				);

			}

		}

		return $redirects;
	}

	/**
	 * Save the table row data.
	 * eg: /wp-json/spamannhilator/v1/save?username=ryan
	 *
	 * @param  array  $request  Request vars
	 * @return string | array
	 */
	public function save( $request ) {
		$request_params = $request->get_query_params();

		// Set user ID variable
		if ( isset( $request_params[ 'user_id' ] ) ) {
			$user_id = $request_params[ 'user_id' ];
		} else {
			return 'User ID not set';
		}

		// Perform security check
		if ( false === $this->security_check( $user_id ) ) {
			return 'Failed security check';
		}

		// Save form data
		$updated = false;
		foreach ( $_POST as $key => $data ) {
			$data = json_decode( stripslashes( $data ), false );

			// Only proceed if vars are all set
			if (
				! isset( $data->id )
				||
				! isset( $data->slug )
				||
				! isset( $data->redirect_url )
			) {
				continue;
			}

			$id           = $data->id;
			$slug         = sanitize_title( $data->slug );
			$redirect_url = $data->redirect_url;

			// If no slug set, then generate unique one
			if ( '' === $slug ) {

				// Generate a unique slug
				$slug_length = 5;
				while ( ! isset( $found_id ) ) {

					$slug = substr( md5( rand() ), 0, $slug_length );
					$post = get_page_by_path( $slug, OBJECT, 'check' );

					if ( ! isset( $post->ID ) ) {
						$found_id = true;
					}

				}
				unset( $found_id );

			}

			// Get ID if it's an existing post
			if ( 'publish' == get_post_status ( $id ) ) {
				$post_id = $id;
			}

			// Update post title and slug
			$args = array(
				'post_title'  => $slug,
				'post_name'   => $slug,
				'post_type'   => 'check',
				'post_author' => $user_id,
				'post_status' => 'publish',
			);

			if ( isset( $post_id ) ) {

				$args[ 'ID' ] = $post_id;
				wp_update_post( $args );
			} else {

				// Only bother saving if redirect URL or slug has been set
				if (
					'' !== $redirect_url
					&&
					'' !== $slug
				) {
					$args[ 'post_title' ] = $slug;
					$args[ 'post_name' ]  = $slug;

					$post_id = wp_insert_post( $args );
				}

			}

			// There was an error in the post insertion, 
			if ( ! isset( $post_id ) || is_wp_error( $post_id ) ) {
				continue;
			}

			update_post_meta( $post_id, '_redirect_url', $redirect_url );

			$updated = true;
			unset( $post_id );
		}

		return $updated;
	}

	/**
	 * Get the table rows.
	 * eg: /wp-json/spamannhilator/v1/get?username=ryan
	 *
	 * @param  array  $request  Request vars
	 * @return string | array
	 */
	public function delete( $request ) {
		$request_params = $request->get_query_params();

		// Set user ID variable
		if ( isset( $request_params[ 'user_id' ] ) ) {
			$user_id = $request_params[ 'user_id' ];
		} else {
			return 'User ID not set';
		}

		// Perform security check
		if ( false === $this->security_check( $user_id ) ) {
			return 'Failed security check';
		}

		// If post author is author of post being deleted, then don't allow it
		$post_id = absint( $_GET[ 'id' ] );
		if ( (string) $user_id !== get_post_field( 'post_author', $post_id ) ) {
			return false;
		}

		// Save the deleted post - false files to trashbin so we can track how many have been created historically
		$result = wp_delete_post( $post_id, false );

		if ( null !== $result ) {
			return true;
		}

		return false;
	}

	/**
	 * Add JS templates.
	 * Used for generating HTML from AJAX requests to the WordPress REST API.
	 */
	public function js_templates() {

		echo '
<script type="text/html" id="tmpl-spamannhilator-table">
	<tr id="row-number{{id}}">
		<td>
			{{number}}
		</td>
		<td>
			<input type="text" name="spamannhilator-redirect-url[]" value="{{redirect_url}}" />

			<div class="custom-slug">
				<a href="' . esc_url( home_url() ) . '/check/{{slug}}/">' . esc_url( home_url() ) . '/check/{{slug}}/</a>

				<button class="edit-slug">edit this URL</button>

				<div>
					<a href="' . esc_url( home_url() ) . '/check/{{slug}}/">' . esc_url( home_url() ) . '/check/</a>
					<input size="10" class="slug" type="text" name="spamannhilator-slug[]" value="{{slug}}" />
					<a href="' . esc_url( home_url() ) . '/check/{{slug}}/">/</a>
				</div>
			</div>
		</td>
		<td>
			<button aria-label="' . esc_attr__( 'Delete this row', 'spamannihilator' ) . '" data-id="{{id}}" class="delete">
				&#10799;
			</button>

			<input type="hidden" name="spamannhilator-id[]" value="{{id}}" />
		</td>
	</tr>
</script>';

	}

	/**
	 * Get the member info.
	 *
	 * @return bool | object   false if not on member page | the member object
	 */
	private function get_member_info() {

		// If member isn't logged in, then just bail out now
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// Get member path
		$member_path = str_replace( 'http://', '', home_url() );
		$member_path = str_replace( 'https://', '', $member_path );
		$member_path = str_replace( $_SERVER['SERVER_NAME'], '', $member_path );
		$member_path = str_replace( $_SERVER['HTTP_HOST'], '', $member_path );
		$member_path = $member_path . '/' . esc_html__( 'member', 'spamannhilator' ) . '/';

		// If path isn't even in the REQUEST_URI, then we aint on a members page
		if ( strpos( $_SERVER['REQUEST_URI'], $member_path ) === false ) {
			return false;
		}

		// Calculate the member slug
		$member_slug = str_replace( $member_path, '', $_SERVER['REQUEST_URI'] );
		$member_slug = str_replace( '/', '', $member_slug );

		// Redirect if name not quite correct
		if (
			sanitize_title( $member_slug ) !== $member_slug
			||
			$_SERVER['REQUEST_URI'] === $member_path . sanitize_title( $member_slug )
		) {
			wp_redirect(
				$member_path . sanitize_title( $member_slug ) . '/',
				301
			);
		}

		// Check if member is real or not
		$member = get_user_by( 'login', $member_slug );
		if ( ! is_object( $member ) ) {
			return false;
		}

		return $member;
	}

	/**
	 * Redirect user to their own member page on login.
	 *
	 * @global  object  $wp  The WP global object
	 */
	public function redirect_on_login() {
		global $wp;

		$login_slug = __( 'login', 'spamannhilator' );
		if (
			is_user_logged_in()
			&&
			home_url( $login_slug ) === home_url( $wp->request )
		) {

			$member_id = get_current_user_id();
			$member = get_user_by( 'ID', $member_id );
			if (
				! is_object( $member )
				||
				! isset( $member->data->user_login )
			) {
				return;
			}

			$member_string = __( 'member', 'spamannhilator' );
			$member_slug = $member->data->user_login;
			$url = home_url( $member_string . '/' . $member_slug . '/' );
			wp_redirect( esc_url( $url ), 302 );

		}

	}

	/**
	 * Set member template.
	 *
	 * @global object  $wp_query  the page query
	 */
	public function member_template() {

		$member = $this->get_member_info();

		// If not on member template, then bail out now
		if ( ! is_object( $member ) ) {
			return;
		}

		// Prevent WordPress from returning a 404 status
		global $wp_query;
		$wp_query->is_404 = false;

		// Modifying page content
		$args = array_merge(
			$wp_query->query_vars,
			array(
				'post_type' => 'page',
				'p'         => '17'    /**** HARD CODED MEMBERS PAGE ID ****/
			)
		);

		query_posts( $args );
	}

	/**
	 * Modify the body class on the members template.
	 *
	 * @param  array  $classes  The body tag classes
	 * @return array  The modified body tag classes
	 */
	public function body_class( $classes ) {
		$classes = array_merge( $classes, array( 'member' ) );

		return $classes;
	}

	/**
	 * The redirect table form.
	 */
	public function redirect_table() {

		$content = '
		<form method="POST" action="" id="spamannhilator-form">

			<input id="spamannhilator-submit-1" type="submit" value="' . esc_attr__( 'Save', 'spamannhilator' ) . '" />

			<table>

				<thead>
					<tr>
						<td>' . esc_html__( 'Number', 'spamannhilator' ) . '</td>
						<td>' . esc_html__( 'Invite URL', 'spamannhilator' ) . '</td>
						<td>' . esc_html__( 'Delete', 'spamannhilator' ) . '</td>
				</thead>

				<tbody id="spamannhilator-redirects" class="transition"></tbody>

				<tfoot>
					<tr>
						<td>' . esc_html__( 'Number', 'spamannhilator' ) . '</td>
						<td>' . esc_html__( 'Invite URL', 'spamannhilator' ) . '</td>
						<td>' . esc_html__( 'Delete', 'spamannhilator' ) . '</td>
				</tfoot>

			</table>

			<input id="spamannhilator-submit-2" type="submit" value="' . esc_attr__( 'Save', 'spamannhilator' ) . '" />

		</form>';

		return $content;
	}

}
