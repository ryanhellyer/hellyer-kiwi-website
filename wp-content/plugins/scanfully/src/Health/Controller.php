<?php
/**
 * The health class file.
 *
 * @package Scanfully
 */

namespace Scanfully\Health;

use Scanfully\API\HealthRequest;
use Scanfully\API\SiteDataRequest;
use Scanfully\API\SiteDirectoriesRequest;

/**
 * Health Controller. This handles everything related to the health.
 */
class Controller {

	/**
	 * Get the server architecture
	 *
	 * @return string|null
	 */
	private static function get_server_arch(): ?string {
		if ( function_exists( 'php_uname' ) ) {
			return sprintf( '%s %s %s', php_uname( 's' ), php_uname( 'r' ), php_uname( 'm' ) );
		}

		return null;
	}

	/**
	 * Get the PHP version
	 *
	 * @return string
	 */
	private static function get_php_version(): string {
		return sprintf(
			'%s %s',
			PHP_VERSION,
			( ( PHP_INT_SIZE * 8 === 64 ) ? __( 'x64' ) : __( 'x86' ) )
		);
	}

	/**
	 * Get the curl version
	 *
	 * @return string|null
	 */
	private static function get_curl_version(): ?string {
		if ( function_exists( 'curl_version' ) ) {
			$curl = curl_version();

			return sprintf( '%s %s', $curl['version'], $curl['ssl_version'] );
		}

		return null;
	}

	/**
	 * Get the PHP SAPI
	 *
	 * @return string|null
	 */
	private static function get_php_sapi(): ?string {
		if ( function_exists( 'php_sapi_name' ) ) {
			return php_sapi_name();
		}

		return null;
	}

	/**
	 * Get various php settings
	 *
	 * @return null[]
	 */
	private static function get_php_settings(): array {
		$ini_values = [
			'memory_limit'        => null,
			'max_input_time'      => null,
			'max_execution_time'  => null,
			'upload_max_filesize' => null,
			'post_max_size'       => null,
		];

		// get actual values if ini_get is available.
		if ( function_exists( 'ini_get' ) ) {
			foreach ( $ini_values as $ini_key => $default_value ) {
				$v = ini_get( $ini_key );

				// ini_get returns false if the ini key is not set. We set it to null in this case.
				if ( false === $v ) {
					$v = null;
				}

				$ini_values[ $ini_key ] = $v;
			}
		}

		return $ini_values;
	}

	/**
	 * Get the database extension used
	 *
	 * @return string|null
	 */
	private static function get_db_extension(): ?string {
		global $wpdb;
		$extension = null;
		if ( is_resource( $wpdb->dbh ) ) {
			// Old mysql extension.
			$extension = 'mysql';
		} elseif ( is_object( $wpdb->dbh ) ) {
			// mysqli or PDO.
			$extension = get_class( $wpdb->dbh );
		}

		return $extension;
	}

	/**
	 * Get the database server version
	 *
	 * @return string|null
	 */
	private static function get_db_server_version(): ?string {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_var( 'SELECT VERSION()' );
	}

	/**
	 * Get the database client version
	 *
	 * @return string|null
	 */
	private static function get_db_client_version(): ?string {
		global $wpdb;
		$client_version = null;
		if ( isset( $wpdb->use_mysqli ) && $wpdb->use_mysqli ) {
			$client_version = $wpdb->dbh->client_info;
		} elseif ( function_exists( 'mysql_get_client_info' ) ) {
			// phpcs:ignore WordPress.DB.RestrictedFunctions.mysql_mysql_get_client_info,PHPCompatibility.Extensions.RemovedExtensions.mysql_DeprecatedRemoved
			if ( preg_match( '|[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}|', mysql_get_client_info(), $matches ) ) {
				$client_version = $matches[0];
			}
		}

		return $client_version;
	}

	/**
	 * Get the database user
	 *
	 * @return string
	 */
	private static function get_db_user(): string {
		global $wpdb;

		return $wpdb->dbuser;
	}

	/**
	 * Get the maximum number of connections allowed by the database server
	 *
	 * @return int|null
	 */
	private static function get_db_max_connections(): ?int {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->get_row(
			$wpdb->prepare( 'SHOW VARIABLES LIKE %s', 'max_connections' ),
			ARRAY_A
		);

		if ( ! empty( $result ) && array_key_exists( 'Value', $result ) ) {
			return (int) $result['Value'];
		}

		return null;
	}

	/**
	 * Get database charset
	 *
	 * @return string
	 */
	private static function get_db_charset(): string {
		global $wpdb;

		return $wpdb->charset;
	}

	/**
	 * Get database charset
	 *
	 * @return string
	 */
	private static function get_db_collate(): string {
		global $wpdb;

		return $wpdb->collate;
	}

	/**
	 * Gets the size of the database in bytes
	 *
	 * @return int
	 */
	private static function get_db_size(): int {
		global $wpdb;
		$size = 0;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_results( 'SHOW TABLE STATUS', ARRAY_A );

		if ( $wpdb->num_rows > 0 ) {
			foreach ( $rows as $row ) {
				$size += $row['Data_length'] + $row['Index_length'];
			}
		}

		return (int) $size;
	}

	/**
	 * Checks what WordPress directories are writable
	 *
	 * @return array
	 */
	private static function get_writable_directories(): array {
		$upload_dir = wp_upload_dir();

		return [
			'abspath'    => wp_is_writable( ABSPATH ),
			'wp_content' => wp_is_writable( WP_CONTENT_DIR ),
			'uploads'    => wp_is_writable( $upload_dir['basedir'] ),
			'plugins'    => wp_is_writable( WP_PLUGIN_DIR ),
			'theme'      => wp_is_writable( get_theme_root( get_template() ) ),
		];
	}

	/**
	 * Get the list of plugins
	 *
	 * @return array
	 */
	private static function get_plugins(): array {
		$plugins = get_plugins();

		$map = [];

		foreach ( $plugins as $plugin_path => $plugin ) {
			$map[] = [
				'active'      => is_plugin_active( $plugin_path ),
				'name'        => $plugin['Name'],
				'slug'        => dirname( plugin_basename( $plugin_path ) ),
				'url'         => $plugin['PluginURI'],
				'version'     => $plugin['Version'],
				'description' => $plugin['Description'],
				'author'      => $plugin['Author'],
				'author_uri'  => $plugin['AuthorURI'],
			];
		}

		return $map;
	}

	public static function get_site_data(): array {
		// load wp_site_health class if not loaded, this is not loaded by default.
		if ( ! class_exists( 'WP_Site_Health' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
		}

		if ( ! function_exists( "get_plugins" ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// get php settings array.
		$php_settings = self::get_php_settings();

		$data = [
			'data'    => [
				'wp_version'           => get_bloginfo( 'version' ),
				'wp_multisite'         => is_multisite(),
				'wp_user_registration' => (bool) get_option( 'users_can_register' ),
				'wp_blog_public'       => (bool) get_option( 'blog_public' ),
				'https'                => is_ssl(),

				'wp_cache'            => (bool) WP_CACHE,
				'wp_debug'            => (bool) WP_DEBUG,
				'wp_debug_display'    => (bool) WP_DEBUG_DISPLAY,
				'wp_debug_log'        => (bool) WP_DEBUG_LOG,
				'wp_script_debug'     => (bool) SCRIPT_DEBUG,
				'wp_memory_limit'     => WP_MEMORY_LIMIT,
				'wp_max_memory_limit' => WP_MAX_MEMORY_LIMIT,
				'wp_environment_type' => wp_get_environment_type(),
				'permalink_structure' => get_option( 'permalink_structure' ),
				'locale'              => get_locale(),
				'user_count'          => (int) count_users()['total_users'],
				'site_url'            => get_site_url(),

				'server_arch'       => self::get_server_arch(),
				'web_server'        => esc_attr( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) ?? null, // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				'curl_version'      => self::get_curl_version(),
				'imagick_available' => extension_loaded( 'imagick' ),

				'php_version'             => self::get_php_version(),
				'php_sapi'                => self::get_php_sapi(),
				'php_memory_limit'        => \WP_Site_Health::get_instance()->php_memory_limit,
				'php_memory_limit_admin'  => $php_settings['memory_limit'],
				'php_max_input_time'      => (int) $php_settings['max_input_time'],
				'php_max_execution_time'  => (int) $php_settings['max_execution_time'],
				'php_upload_max_filesize' => $php_settings['upload_max_filesize'],
				'php_post_max_size'       => $php_settings['post_max_size'],

				'db_extension'       => self::get_db_extension(),
				'db_server_version'  => self::get_db_server_version(),
				'db_client_version'  => self::get_db_client_version(),
				'db_user'            => self::get_db_user(),
				'db_max_connections' => self::get_db_max_connections(),
				'db_charset'         => self::get_db_charset(),
				'db_collate'         => self::get_db_collate(),
			],
			'plugins' => self::get_plugins(),
		];

		// filter data
		$data = apply_filters( 'scanfully_health_data', $data );

		return $data;
	}

	/**
	 * Send the health data to the API
	 *
	 * @return void
	 */
	public static function send_site_data(): void {

		// todo add a transient last sent time to prevent sending too many requests.

		// send event.
		$request = new SiteDataRequest();
		$request->send( self::get_site_data() );
	}

	/**
	 * Send the directory data to the API
	 *
	 * @return void
	 */
	public static function send_directories_data(): void {

		// load wp_site_health class if not loaded, this is not loaded by default.
		if ( ! class_exists( 'WP_Site_Health' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
		}

		if ( ! function_exists( "get_plugins" ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// directories to check.
		$dirs = [
			'content' => WP_CONTENT_DIR,
			'plugins' => WP_PLUGIN_DIR,
			'themes'  => get_theme_root( get_template() ),
			'uploads' => wp_upload_dir()['basedir'],
		];

		// dir requests
		$request = new SiteDirectoriesRequest();

		// data array
		$data = [
			'data' => [
				'db_size' => round( self::get_db_size() / 1000000, 2 ),
			],
		];

		// add data for each directory.
		foreach ( $dirs as $key => $dir ) {
			$data['data'][ $key . '_size' ]     = (float) round( recurse_dirsize( $dir, null, 30 ) / 1000000, 2 );
			$data['data'][ $key . '_writable' ] = wp_is_writable( $dir );
			$data['data'][ $key . '_dir' ]      = $dir;
		}

		// send event.
		$request->send( $data );
	}

}
