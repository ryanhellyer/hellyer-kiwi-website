<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CUWS
 */
class CUWS {

	/**
	 * The single instance of CUWS.
	 *
	 * @var    object
	 * @access   private
	 * @since    v2.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 *
	 * @var     object
	 * @access  public
	 * @since   v2.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   v2.0.0
	 */
	public $_version;

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   v2.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 *
	 * @var     string
	 * @access  public
	 * @since   v2.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   v2.0.0
	 */
	public $dir;

	/**
	 * The plugin styles directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   v2.0.0
	 */
	public $styles_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   v2.0.0
	 */
	public $styles_url;

	/**
	 * Holds an array of plugin options.
	 *
	 * @var array
	 * @access public
	 * @since  2.x
	 */
	public $options = array();

	/**
	 * Constructor function.
	 *
	 * @access  public
	 * @since   v2.0.0
	 *
	 * @param string $file
	 * @param string $version Version number.
	 */
	public function __construct( $file = '', $version = '3.3.0' ) {
		$this->_version = $version;
		$this->_token   = 'cuws';

		// Load plugin environment variables
		$this->file       = $file;
		$this->dir        = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'css';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/css/', $this->file ) ) );

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load admin CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Handle localisation
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 0 );

		/*** PLUGIN FUNCTIONS ***/

		// @since v1.3.0
		add_action( 'admin_bar_menu', array( $this, 'so_cuws_remove_adminbar_settings' ), 999 );
		// @since 1.5.0
		add_action( 'wp_dashboard_setup', array( $this, 'so_cuws_remove_dashboard_widget' ) );
		// @since 2.0.0
		add_action( 'admin_head', array( $this, 'so_cuws_hide_visibility_css' ) );


		// Load API for generic admin functions
		if ( is_admin() ) {
			$this->admin = new CUWS_Admin_API();
		}

		$this->options = get_site_option( $this->_token . '_settings' );

		// Make sure options have been populated if messed up from new settings
		// Simpler than requiring deactivation/activation of plugin.
		if ( ! $this->options ) {
			$this->install();
			$this->options = get_site_option( $this->_token . '_settings' );
		}

	} // End __construct ()

	/**
	 * Remove Settings submenu in admin bar
	 * Since Yoast SEO 3.6 it is possible to disable the adminbar menu within
	 * Dashboard > Features but only in individual sites, not network admin
	 *
	 * inspired by [Lee Rickler](https://profiles.wordpress.org/lee-rickler/)
	 *
	 * @since v1.3.0
	 */
	public function so_cuws_remove_adminbar_settings() {
		if ( empty( $this->options['remove_adminbar'] ) ) {
			return;
		}
		global $wp_admin_bar;
		$nodes = array_keys( $wp_admin_bar->get_nodes() );
		foreach ( $nodes as $node ) {
			if ( false !== strpos( $node, 'wpseo' ) ) {
				$wp_admin_bar->remove_node( $node );
			}
		}
	}

	/**
	 * Version 2.3 of Yoast SEO introduced a dashboard widget
	 * This function removes this widget
	 *
	 * @since v1.5.0
	 */
	public function so_cuws_remove_dashboard_widget() {

		if ( ! empty( $this->options['remove_dbwidget'] ) ) {

			remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'side' );

		}
	}

	/**
	 * CSS needed to hide the various options ticked with checkboxes
	 *
	 * @since    v2.0.0
	 * @modified v2.1.0 remove options for nags that have been temporarily
	 * disabled in v3.1 of Yoast SEO plugin
	 */
	public function so_cuws_hide_visibility_css() {

		echo '<style media="screen" id="so-hide-seo-bloat" type="text/css">';

		// sidebar ads
		if ( ! empty( $this->options['hide_ads'] ) ) {
			echo '#sidebar-container.wpseo_content_cell{visibility:hidden;}'; // @since v1.0.0
		}

		// tagline nag
		if ( ! empty( $this->options['hide_tagline_nag'] ) ) {
			echo '#wpseo-dismiss-tagline-notice{display:none;}'; // @since v2.6.0 hide tagline nag
		}

		// robots nag
		if ( ! empty( $this->options['hide_robots_nag'] ) ) {
			echo '#wpseo-dismiss-blog-public-notice,#wpseo_advanced .error-message{display:none;}'; // @since v2.0.0 hide robots nag; @modified v2.5.4 to add styling via the options and not globally.
		}

		// hide upsell notice in Yoast SEO Dashboard
		if ( ! empty( $this->options['hide_upsell_notice'] ) ) {
			echo '#yoast-warnings #wpseo-upsell-notice{display:none;}'; // @since v2.5.3 hide upsell notice in Yoast SEO Dashboard; @modified v2.5.4 improved to remove entire Notification box in the main Dashboard; @modified v2.6.0 only hide this notice.
		}

		// hide upsell notice on social tab in Yoast Post/Page metabox
		if ( ! empty( $this->options['hide_upsell_metabox_socialtab'] ) ) {
			echo '.wpseo-metabox-tabs-div .yoast-notice-go-premium{display:none}'; // @since v3.2.0
		}

		// hide premium upsell admin block
		if ( ! empty( $this->options['hide_upsell_admin_block'] ) ) {
			echo '.yoast_premium_upsell_admin_block{display:none}'; // @since v3.1.0
		}

		// Problems/Notification boxes
		if ( ! empty( $this->options['hide_dashboard_problems_notifications'] ) ) {
			if ( in_array( 'problems', $this->options['hide_dashboard_problems_notifications'] ) ) {
				echo '.yoast-container.yoast-container__alert{display:none;}'; // @since v2.6.0 hide both Problems/Notifications boxes from Yoast SEO Dashboard
			}
			if ( in_array( 'notifications', $this->options['hide_dashboard_problems_notifications'] ) ) {
				echo '.yoast-container.yoast-container__warning{display:none;}'; // @since v2.6.0 hide both Problems/Notifications boxes from Yoast SEO Dashboard
			}
		}

		// image warning nag
		if ( ! empty( $this->options['hide_imgwarning_nag'] ) ) {
			echo '#yst_opengraph_image_warning{display:none;}#postimagediv.postbox{border:1px solid #e5e5e5!important;}'; // @since v1.7.0 hide yst opengraph image warning nag
		}

		// add keyword button
		if ( ! empty( $this->options['hide_addkw_button'] ) ) {
			echo '.wpseo-tab-add-keyword,.wpseo-add-keyword.button{display:none;}ul.wpseo-metabox-tabs li .wpseo-keyword{max-width:10rem;}'; // @since v1.7.3 hide add-keyword-button in UI which only serves ad in overlay; @modified v2.6.0 give text in remaining tab more space
		}

		// hide issue counter
		if ( ! empty( $this->options['hide_issue_counter'] ) ) {
			echo '#wpadminbar .yoast-issue-counter,#toplevel_page_wpseo_dashboard .update-plugins .plugin-count,#adminmenu .update-plugins{display:none;}'; // @since v2.3.0 hide issue counter from adminbar and plugin menu sidebar; @modified v3.2.1 to remove orange background that shows again
		}

		// hide red star "Go Premium" submenu
		if ( ! empty( $this->options['hide_gopremium_star'] ) ) {
			echo '#adminmenu .wpseo-premium-indicator,.wpseo-metabox-buy-premium,#wp-admin-bar-wpseo-licenses{display:none;}'; // @since v2.5.0 hide star of "Go Premium" submenu
		}

		// content analysis
		if ( ! empty( $this->options['hide_wpseoanalysis'] ) ) {
			echo '.wpseo-meta-section.active .wpseo-metabox-tabs .wpseo_generic_tab,#pageanalysis,.wpseoanalysis{display:none;}.wpseo-score-icon{display:none!important;}'; // @since v2.0.0 hide_wpseoanalysis; @modified v2.3.0 to remove the colored ball from the metabox tab too; @modified v2.5.4 to remove the content analysis too from the post/page metabox; @since v2.5.4 remove Readability tab from metabox as it only contains the content analysis.
		}

		// keyword/content score
		if ( ! empty( $this->options['hide_content_keyword_score'] ) ) {
			if ( in_array( 'keyword_score', $this->options['hide_content_keyword_score'] ) ) {
				echo '.yoast-seo-score.keyword-score{display:none;}'; // @since v2.3.0 hide both Keyword and Content Score from edit Post/Page screens
			}
			if ( in_array( 'content_score', $this->options['hide_content_keyword_score'] ) ) {
				echo '.yoast-seo-score.content-score{display:none;}'; // @since v2.3.0 hide both Keyword and Content Score from edit Post/Page screens
			}
		}

		/*
		 * admin columns
		 * @since v2.0.0 remove seo columns one by one
		 * @modified 2.0.2 add empty array as default to avoid warnings form subsequent
		 *  in_array checks - credits [Ronny Myhre Njaastad](https://github.com/ronnymn)
		 * @modified 2.1 simplify the CSS rules and add the rule to hide the seo-score
		 *  column on taxonomies (added to v3.1 of Yoast SEO plugin)
		 * @modified 2.6.0 only 2 columns left change from checkboxes to radio
		 * @modified 2.6.1 revert radio to checkboxes and removing the options
		 *  for focus keyword, title and meta-description
		 */

		// all columns
		if ( ! empty( $this->options['hide_admincolumns'] ) ) {
			// seo score column
			if ( in_array( 'seoscore', $this->options['hide_admincolumns'] ) ) {
				echo '.column-wpseo-score,.column-wpseo_score{display:none;}'; // @since v2.0.0 remove seo columns one by one
			}
			// readability column
			if ( in_array( 'readability', $this->options['hide_admincolumns'] ) ) {
				echo '.column-wpseo-score-readability,.column-wpseo_score_readability{display:none;}'; // @since v2.6.0 remove added readibility column
			}
			// title column
			if ( in_array( 'title', $this->options['hide_admincolumns'] ) ) {
				echo '.column-wpseo-title{display:none;}'; // @since v2.0.0 remove seo columns one by one
			}
			// meta description column
			if ( in_array( 'metadescr', $this->options['hide_admincolumns'] ) ) {
				echo '.column-wpseo-metadesc{display:none;}'; // @since v2.0.0 remove seo columns one by one
			}
			// focus keyword column
			if ( in_array( 'focuskw', $this->options['hide_admincolumns'] ) ) {
				echo '.column-wpseo-focuskw{display:none;}'; // @since v2.0.0 remove seo columns one by one
			}
		}

		// help center
		if ( ! empty( $this->options['hide_helpcenter'] ) ) {
			if ( in_array( 'ad', $this->options['hide_helpcenter'] ) ) {
				echo '.wpseo-tab-video__panel.wpseo-tab-video__panel--text,#tab-link-dashboard_dashboard__contact-support,#tab-link-dashboard_general__contact-support,#tab-link-dashboard_features__contact-support,#tab-link-dashboard_knowledge-graph__contact-support,#tab-link-dashboard_webmaster-tools__contact-support,#tab-link-dashboard_security__contact-support,#tab-link-metabox_metabox__contact-support,li#react-tabs-4,.iimhyI{display:none;}'; // @since v2.2.0 hide help center ad for premium version or help center entirely; @modified v2.5.5 hide email support/ad as it is a premium only feature; @modified v2.6.0 different tabs gave different classes; @modified v3.3.0 due to Yoast 5.6 update this has all changed
			}
			if ( in_array( 'helpcenter', $this->options['hide_helpcenter'] ) ) {
				echo '.yoast-help-center__button{display:none !important;}'; // @since v2.2.0 hide help center ad for premium version or help center entirely; @modified v3.3.0 due to Yoast 5.6 update this has all changed
			}
		}

		echo '</style>';
	}


	/**
	 * Load admin CSS.
	 *
	 * @access  public
	 * @since   v2.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Loads the translation file.
	 *
	 * @since v1.0.0
	 */
	function i18n() {
		load_plugin_textdomain( 'so-clean-up-wp-seo', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Main CUWS Instance
	 *
	 * Ensures only one instance of CUWS is loaded or can be loaded.
	 *
	 * @since v2.0.0
	 * @static
	 * @see   CUWS()
	 *
	 * @param string $file
	 * @param string $version Version number.
	 *
	 * @return CUWS $_instance
	 */
	public static function instance( $file = '', $version = '3.3.0' ) {
		if ( null === self::$_instance ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since v2.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'No Access' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since v2.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'No Access' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 *
	 * @access  public
	 * @since   v2.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
		$this->_set_defaults();
	} // End install ()

	/**
	 * Log the plugin version number.
	 *
	 * @access  private
	 * @since   v2.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		update_site_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

	/**
	 * Array containing the default values.
	 * Use `array_keys()` to return the key names.
	 *
	 * @return array
	 */
	public function get_defaults() {
		$defaults = array(
			'hide_ads'                              => 'on',
			'hide_tagline_nag'                      => 'on',
			'hide_robots_nag'                       => 'on',
			'hide_upsell_notice'                    => 'on',
			'hide_upsell_metabox_socialtab'			=> 'on',
			'hide_upsell_admin_block'				=> 'on',
			'hide_dashboard_problems_notifications' => array(
				'problems',
				'notifications'
			),
			'hide_imgwarning_nag'                   => 'on',
			'hide_addkw_button'                     => 'on',
			'hide_trafficlight'                     => 'on',
			'hide_wpseoanalysis'                    => 'on',
			'hide_issue_counter'                    => 'on',
			'hide_gopremium_star'                   => 'on',
			'hide_content_keyword_score'            => array(
				'keyword_score',
				'content_score'
			),
			'hide_helpcenter'                       => array(
				'ad'
			),
			'hide_admincolumns'                     => array(
				'seoscore',
				'readability',
				'title',
				'metadescr'
			),
			'remove_dbwidget'                       => 'on',
			'remove_adminbar'                       => 'on',
		);

		return $defaults;
	}

	/**
	 * Set default values on activation.
	 *
	 * @access private
	 * @return void
	 */
	private function _set_defaults() {
		$defaults = $this->get_defaults();
		update_site_option( $this->_token . '_settings', $defaults );
	} // End _set_defaults ()

}
