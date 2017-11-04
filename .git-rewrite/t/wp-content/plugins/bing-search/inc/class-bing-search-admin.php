<?php

/**
 * Bing Search Admin page
 * 
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Bing_Search_Admin extends Bing_Search {

	/**
	 * Class constructor
	 */
	public function __construct() {

		// Add to hooks
		add_action( 'admin_init',  array( $this, 'register_setting' ) );
		add_action( 'admin_menu',  array( $this, 'admin_menu' ) );

	}

	/**
	 * Add the admin menu item
	 */
	function admin_menu() {
		add_options_page(
			__( 'Bing search', 'bing_search' ), // Page title
			__( 'Bing search', 'bing_search' ), // Menu title
			'manage_options',                   // Capability
			'bing-search',                      // Menu slug
			array( $this, 'admin_page' )        // The page content
		);
	}

	/**
	 * Register settings
	 */
	public function register_setting() {
		register_setting(
			'bing_search',
			'bing_search',
			array( $this, 'options_validate' )
		);

		// Create the search string
		$search_string = '+site:' . str_replace( 'http://', '', home_url() ); // Add the existing site
		$search_string .= ' -site:' . str_replace( 'http://', '', home_url() . '/' . get_option( 'tag_base' ) ); // Remove tags pages
		if ( '' == get_option( 'category_base' ) ) {
			$category_base = 'uncategorized';
		} else {
			$category_base = get_option( 'category_base' );
		}
		$search_string .= ' -site:' . str_replace( 'http://', '', home_url() . '/' . $category_base ); // Remove tags pages

		// Add in some defaults
		add_option(
			'bing-search',
			array(
				'api-key'        => 'API KEY',
				'search-string'  => $search_string,
			)
		);
	}

	/**
	 * Validate inputs
	 * Perform security checks on inputted data
	 */
	public function options_validate( $input ) {
		$output = array();

		if ( isset( $input['api-key'] ) ) {
			$output['api-key'] = esc_html( $input['api-key'] );
		}
		if ( isset( $input['search-string'] ) ) {
			$output['search-string'] = esc_html( $input['search-string'] );
		}

		update_option( 'bing-search', $output );

		return $output;
	}

	/**
	 * The admin page contents
	 */
	public function admin_page() {
		global $screen_layout_columns;

		?>
	<style type="text/css">
	#icon-bing-icon {
		background: url(<?php echo BING_SEARCH_URL . 'bing_logo.svg'; ?>) no-repeat;
		background-size: 100% 100%;
	}
	#page-title {
		line-height: 52px;
	}
	</style>
	<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
	<div class="wrap">
		<h2 id="page-title"><?php screen_icon( 'bing-icon' ); ?><?php _e( 'Bing search', 'bing_search' ); ?></h2>

		<form id="bing-search-form" action="options.php" method="post">
			<?php settings_fields( 'bing_search' ); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e( 'Bing search API', 'bing_search' ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e( 'Bing search API', 'bing_search' ); ?></span>
								</legend>
								<input type="text" class="regular-text" id="api-key" name="bing_search[api-key]" value="<?php echo esc_attr( $this->get_option( 'api-key' ) ); ?>" />
								<p class="description"><?php _e( 'Obtain a <a href="http://www.bing.com/developers/createapp.aspx">Bing application ID</a> to use here.', 'bing_search' ); ?></p>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Customise Bing search', 'bing_search' ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e( 'Search string', 'bing_search' ); ?></span>
								</legend>
								<input type="text" class="regular-text" id="search-string" name="bing_search[search-string]" value="<?php echo esc_attr( $this->get_option( 'search-string' ) ); ?>" />
								<p class="description"><?php _e( 'You can customise the Bing search results obtained here. Adding +site:example.com will add your site to the search and using -site:example2.com can be used to remove other sites from the search. Other types of pages such as tags, can be removed via -site:example.com/tag.', 'bing_search' ); ?></p>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>

			<p>
				<br />
				<br />
				<input type="submit" class="button" id="save" name="save" value="<?php _e( 'Save &raquo;', 'bing_search') ?>" />
			</p>
		</form>
	</div>
	</div><?php

	}

}
new Bing_Search_Admin();
