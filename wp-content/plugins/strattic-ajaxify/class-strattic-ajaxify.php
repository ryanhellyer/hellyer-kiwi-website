<?php
/**
 * The main Strattic AJAXify class
 *
 * @category Class
 * @package Strattic AJAXify
 * @author Strattic
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link https://www.strattic.com/
 */

/**
 * The main Strattic AJAXify class.
 */
class Strattic_AJAXify {

	const AJAX_PATH     = '/strattic-ajax-area/';
	const OPTION_KEY    = 'strattic-ajax-area';
	const BEFORE_STRING = '<!-- Strattic AJAX area before | {name} AJAX area -->';
	const AFTER_STRING  = '<!-- Strattic AJAX area after | {name} AJAX area -->';

	/**
	 * Fire the constructor up :)
	 */
	public function __construct() {
		add_action( 'dynamic_sidebar_before', array( $this, 'widget_area_before' ) );
		add_action( 'init', array( $this, 'ajax_request' ), 9999 );
		add_action( 'template_redirect', array( $this, 'start_buffer' ) );

		// Include Strattic specific filters if applicable.
		if ( function_exists( 'strattic' ) ) {
			add_filter( 'strattic_paths', array( $this, 'add_ajax_path_to_publish_list' ) );
			add_filter( 'strattic_paths_selective', array( $this, 'add_ajax_path_to_publish_list' ) );
		}

	}

	/**
	 * Adding the widget area before tag.
	 *
	 * @param string $widget_area The widget area name.
	 */
	public function widget_area_before( $widget_area ) {

		// Bail out now if we are not meant to AJAXify this widget area.
		if ( false === $this->should_we_ajaxify_this( $widget_area ) ) {
			return $widget_area;
		}

		echo str_replace( '{name}', 'widget-area-' . $widget_area, self::BEFORE_STRING );

		add_action( 'dynamic_sidebar_after', array( $this, 'widget_area_after' ) );
	}

	/**
	 * Adding the widget area after tag.
	 *
	 * @param string $widget_area The widget area name.
	 */
	public function widget_area_after( $widget_area ) {

		// Bail out now if we are not meant to AJAXify this widget area.
		if ( false === $this->should_we_ajaxify_this( $widget_area ) ) {
			return $widget_area;
		}

		echo str_replace( '{name}', 'widget-area-' . $widget_area, self::AFTER_STRING );
	}

	/*
	 * Starting page buffer.
	 */
	public function start_buffer() {
		ob_start();

		add_action( 'wp_footer', array( $this, 'end_buffer' ) );
	}

	/*
	 * Handle the buffer once content is completely loaded.
	 *
	 * @param string $content The page content.
	 * @return string The modified page output.
	 */
	public function end_buffer() {
		$content = ob_get_contents();
		ob_end_clean();

		$prefix        = ' | ';
		$suffix        = ' AJAX area -->';
		$before_string = explode( ' | ', self::BEFORE_STRING )[0];
		$after_string  = explode( $prefix, self::AFTER_STRING )[0] . $prefix;

		$bits = explode( $before_string, $content );
		foreach ( $bits as $key => $bit ) {

			// Bail out if no AJAX comments found.
			if ( ! $this->str_contains( $bit, $after_string) ) {
				continue;
			}

			// Confirm that string prefix matches.
			if ( $prefix === mb_substr( $bit, 0, strlen( $prefix ) ) ) {

				// Extract the required chunks of the string.
				$bit          = substr( $bit, 3 );
				$bits2        = explode( $suffix, $bit );
				$ajax_area    = $bits2[0];
				$bit          = str_replace( $ajax_area . $suffix, '', $bit );
				$bit          = explode( $after_string, $bit );

				// Set the new HTML for in the page
				$bits[ $key ] = '<div class="strattic-ajax-area" data-ajax-area="' . esc_attr( $ajax_area ) . '"></div>' . $bit[1];

				// Save the AJAX content.
				$ajax_area_content = $bit[0];
				$this->update_option( $ajax_area, $ajax_area_content );
			}

		}
		$content = implode( '', $bits );

		echo $content;

		wp_enqueue_script( 'strattic-ajax-area', plugins_url( '', __FILE__ ) . '/strattic-ajax.js', array(), '1.0', true );
	}

	/**
	 * Serve AJAX request.
	 */
	public function ajax_request() {
		$request_uri = wp_parse_url( filter_input( INPUT_SERVER, 'REQUEST_URI' ) )['path'];
		if ( self::AJAX_PATH === $request_uri ) {

			$ajax_areas = $this->get_ajax_areas();			

			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
			header( 'Cache-Control: post-check=0, pre-check=0', false );
			header( 'Pragma: no-cache' );

			echo wp_json_encode( $ajax_areas );

			die;
		}
	}

	/**
	 * Add new AJAX path to the publish list.
	 * This code is specific to the Strattic platform.
	 *
	 * @param array $paths The list of paths to publish.
	 * @return array The modified list of paths to publish.
	 */
	public function add_ajax_path_to_publish_list( $paths ) {
		$paths[] = array(
			'path'          => self::AJAX_PATH,
			'priority'      => 6,
			'quick_publish' => true,
		);

		return $paths;
	}

	/**
	 * Should we AJAXify this area?
	 *
	 * @param string $ajax_area The widget area being checked.
	 * @return bool true if widget area should be AJAXified.
	 */
	private function should_we_ajaxify_this( $ajax_area ) {

		// We should AJAXify anything set to be included.
		$included_widget_areas = apply_filters( 'strattic_included_ajax_areas', '' );
		if ( is_array( $included_widget_areas ) ) {
			if ( in_array( $ajax_area, $included_widget_areas, true ) ) {
				return true;
			} else {
				return false;
			}
		}

		// We should not AJAXify anything set to be excluded.
		$excluded_widget_areas = apply_filters( 'strattic_excluded_ajax_areas', '' );
		if ( is_array( $excluded_widget_areas ) ) {
			if ( in_array( $ajax_area, $excluded_widget_areas, true ) ) {
				return false;
			} else {
				return true;
			}
		}
	}

	/**
	 * Update the option with new data.
	 *
	 * @param string $ajax_area The widget area.
	 * @param string $content The content to stash.
	 */
	private function update_option( $ajax_area, $content ) {

		// Get previously stashed ajax areas.
		$ajax_areas = get_option( self::OPTION_KEY );
		if ( ! is_array( $ajax_areas ) ) {
			$ajax_areas = array();
		}

		// Expire old AJAX areas.
		foreach ( $ajax_areas as $key => $ajax_area_data ) {
			$expiry_time = time() + DAY_IN_SECONDS;
			if ( $expiry_time < $ajax_area_data['timestamp'] ) {
				unset( $ajax_areas[ $key ] );
			}
		}

		// Bail out if content hasn't changed and timestamp isn't getting old.
		$time_limit = time() + HOUR_IN_SECONDS;
		if (
			isset( $ajax_areas[ $ajax_area ]['content'] )
			&&
			$content === $ajax_areas[ $ajax_area ]['content']
			&&
			$time_limit < $ajax_areas[ $ajax_area ]['timestamp']
		) {
			return true;
		}

		$ajax_areas[ $ajax_area ] = array(
			'content'   => $content,
			'timestamp' => time(),
		);

		update_option( self::OPTION_KEY, $ajax_areas );
	}

	/**
	 * Get the option data.
	 *
	 * @param string $ajax_area The widget area.
	 * @return bool false if option update process failed.
	 */
	private function get_option( $ajax_area ) {

		// Get previously stashed widget areas.
		$ajax_areas = get_option( self::OPTION_KEY );
		if ( isset( $ajax_areas[ $ajax_area ] ) ) {
			return $ajax_areas[ $ajax_area ];
		}

		return false;
	}

	/**
	 * Replacement for str_contains() support before PHP 8.
	 * 
	 * @param string $haystack The haystack.
	 * @param string $needle The needle to find.
	 * @return bool true if needle found, otherwise false.
	 */
	private function str_contains( string $haystack, string $needle ) {
		if ( ! function_exists( 'str_contains' ) ) {
			if ( str_contains( $haystack, $needle) ) {
				return true;
			}
		} else if ( strpos( $haystack, $needle ) !== false ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the list of AJAX areas.
	 *
	 * @param array The AJAX areas.
	 */
	private function get_ajax_areas() {
		$ajax_areas = array();

		$option = get_option( self::OPTION_KEY );
		if ( ! is_array( $option ) ) {
			return array();
		}

		foreach ( $option as $ajax_area => $ajax_area_data ) {

			if ( isset( $ajax_area_data['content'] ) ) {
				$ajax_areas[ $ajax_area ] = $ajax_area_data['content'];
			}
		}

		return $ajax_areas;
	}

}
