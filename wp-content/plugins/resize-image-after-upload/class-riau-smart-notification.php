<?php

class RIAU_Smart_Notification {

	private static $_instance = null;
	private $plugins;
	private $options;
	private $plugin_dir;
	
	function __construct( $args ) {

		if ( defined( WP_PLUGIN_DIR ) ) {
			$this->plugin_dir = WP_PLUGIN_DIR . '/';
		}else{
			$this->plugin_dir = WP_CONTENT_DIR . '/plugins/';
		}
		
		$this->container_id = 'riau-smart-notification';
		$this->options = get_option( 'sp-recommended-plugin', array() );
		$this->plugins = $this->parse_plugins( $args['plugins'] );

		if ( is_admin() && $this->show_notice() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'admin_notices', array( $this, 'notification' ) );
			add_action( 'wp_ajax_riau_smart_notitification', array( $this, 'ajax' ) );
			add_action('admin_footer', array( $this, 'riau_script' ) );
		}

	}

	private function parse_plugins( $need_check ) {
		$plugins = array();

		foreach ( $need_check as $slug => $plugin ) {

			if ( in_array( $slug, $this->options ) ) {
				continue;
			}

			$plugin_info = $this->check_plugin( $slug );
			if ( 'deactivate' == $plugin_info['needs'] ) {
				continue;
			}

			$plugins[ $slug ] = array_merge( $plugin, $plugin_info );
		}

		return $plugins;

	}

	private function show_notice() {

		if ( ! empty( $this->plugins ) ) {
			return true;
		}

		return false;

	}

	/**
	 * @since 1.0.0
	 * @return riau_Smart_Notification
	 */
	public static function get_instance( $args ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $args );
		}
		return self::$_instance;
	}

	public function notification() {
		$notice_html = '';

		foreach ( $this->plugins as $slug => $plugin ) {
			$notice_html .= '<div class="riau-plugin-card">';
			$url = $this->create_plugin_link( $plugin['needs'], $slug );
			if ( '' != $plugin['image'] ) {
				$notice_html .= '<div style="padding-right: 10px;">';
				$notice_html .= '<img src="' . esc_url( $plugin['image'] ) . '" width="75" height="75">';
				$notice_html .= '</div>';
			}
			$notice_html .= '<div style="align-self: center;flex-grow: 1;">';
			$notice_html .= '<h3 style="margin:0;">' . $plugin['name'] . '</h3>';
			$notice_html .= '<p>' . $plugin['description'] . '</p>';
			$notice_html .= '</div>';
			$notice_html .= '<div>';
			$notice_html .= '<a href="#" class="riau-dismiss" data-dismiss="' . esc_attr( $slug ) . '"><span class="screen-reader-text">Dismiss this notice.</span></a>';
			$notice_html .= '<span class="plugin-card-' . esc_attr( $slug ) . ' action_button ' . $plugin['needs'] . '">';
				$notice_html .= '<a data-slug="' . esc_attr( $slug ) . '" data-action="' . esc_attr( $plugin['needs'] ) . '" class="riau-plugin-button ' . esc_attr( $plugin['class'] ) . '" href="' . esc_url( $url ) . '">' . esc_attr( $plugin['label'] ) . '</a>';
			$notice_html .= '</span>';
			$notice_html .= '</div>';
			$notice_html .= '</div>';
		}

		$class = "riau-one-column";
		if ( count( $this->plugins ) > 1 ) {
			$class = "riau-two-column";
		}
		echo '<div id="' . $this->container_id . '" class="riau-custom-notice notice ' . $class . '" style="background:transparent;border: 0 none;box-shadow: none;padding: 0;display: flex;">';
		echo $notice_html;
		echo '<style>.riau-plugin-card {display: flex;background: #fff;border-left: 4px solid #46b450;padding: .5em 12px;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);position:relative;align-items:center;}.riau-one-column .riau-plugin-card{ width:100%; }.riau-two-column .riau-plugin-card{width:49%;}.riau-two-column .riau-plugin-card:nth-child( 2n + 1 ){margin-right:2%;}.riau-dismiss { position: absolute;top: 0;right: 1px;border: none;margin: 0;padding: 9px;background: 0 0;color: #72777c;cursor: pointer;text-decoration:none; }.riau-dismiss:before { background: 0 0;color: #72777c;content: "\f153";display: block;font: 400 16px/20px dashicons; speak: none;height: 20px;text-align: center;width: 20px;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale; }.riau-dismiss:active:before, .riau-dismiss:focus:before, .riau-dismiss:hover:before { color: #c00; }</style>';
		echo '</div>';
		

	}

	public function ajax() {

		check_ajax_referer( 'riau-smart-notitification', 'security' );

		if ( isset( $_POST['slug'] ) ) {
			$this->options[] = sanitize_text_field( $_POST['slug'] );
			update_option( 'sp-recommended-plugin', $this->options );
		}

		wp_die( 'ok' );

	}

	public function enqueue() {
		wp_enqueue_script( 'updates' );
		wp_enqueue_script( 'jquery' );
	}

	private function get_plugins( $plugin_folder = '' ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugins( $plugin_folder );
	}

	private function _get_plugin_basename_from_slug( $slug ) {
		$keys = array_keys( $this->get_plugins() );

		foreach ( $keys as $key ) {
			if ( preg_match( '|^' . $slug . '/|', $key ) ) {
				return $key;
			}
		}

		return $slug;
	}

	/**
	 * @return bool
	 */
	private function check_plugin_is_installed( $slug ) {
		
		$plugin_path = $this->_get_plugin_basename_from_slug( $slug );

		if ( file_exists( $this->plugin_dir . $plugin_path ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	private function check_plugin_is_active( $slug ) {
		$plugin_path = $this->_get_plugin_basename_from_slug( $slug );
		if ( file_exists( $this->plugin_dir . $plugin_path ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			return is_plugin_active( $plugin_path );
		}
	}

	private function create_plugin_link( $state, $slug ) {
		$string = '';

		switch ( $state ) {
			case 'install':
				$string = wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'install-plugin',
							'plugin' => $this->_get_plugin_basename_from_slug( $slug ),
						),
						network_admin_url( 'update.php' )
					),
					'install-plugin_' . $slug
				);
				break;
			case 'deactivate':
				$string = add_query_arg(
					array(
						'action'        => 'deactivate',
						'plugin'        => rawurlencode( $this->_get_plugin_basename_from_slug( $slug ) ),
						'plugin_status' => 'all',
						'paged'         => '1',
						'_wpnonce'      => wp_create_nonce( 'deactivate-plugin_' . $this->_get_plugin_basename_from_slug( $slug ) ),
					),
					admin_url( 'plugins.php' )
				);
				break;
			case 'activate':
				$string = add_query_arg(
					array(
						'action'        => 'activate',
						'plugin'        => rawurlencode( $this->_get_plugin_basename_from_slug( $slug ) ),
						'plugin_status' => 'all',
						'paged'         => '1',
						'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $this->_get_plugin_basename_from_slug( $slug ) ),
					),
					admin_url( 'plugins.php' )
				);
				break;
			default:
				$string = '';
				break;
		}// End switch().

		return $string;
	}

	private function check_plugin( $slug = '' ) {
		$arr = array(
			'installed' => $this->check_plugin_is_installed( $slug ),
			'active'    => $this->check_plugin_is_active( $slug ),
			'needs'     => 'install',
			'class'     => 'button button-primary',
			'label'     => __( 'Install and Activate', 'enable-media-replace' ),
		);

		if ( $arr['installed'] ) {
			$arr['needs'] = 'activate';
			$arr['class'] = 'button button-primary';
			$arr['label'] = __( 'Activate now', 'enable-media-replace' );
		}

		if ( $arr['active'] ) {
			$arr['needs'] = 'deactivate';
			$arr['class'] = 'deactivate-now button';
			$arr['label'] = __( 'Deactivate now', 'enable-media-replace' );
		}

		return $arr;
	}

	public function riau_script() {

		$ajax_nonce = wp_create_nonce( 'riau-smart-notitification' );

		?>
		<script type="text/javascript">
			
			  
			function riauActivatePlugin( url, el ) {

				jQuery.ajax( {
				  async: true,
				  type: 'GET',
				  dataType: 'html',
				  url: url,
				  success: function() {
				    location.reload();
				  }
				} );
			}

		  	var riauContainer = jQuery( '#<?php echo $this->container_id ?>' );
		    riauContainer.on( 'click', '.riau-plugin-button', function( event ) {
		      var action = jQuery( this ).data( 'action' ),
		          url = jQuery( this ).attr( 'href' ),
		          slug = jQuery( this ).data( 'slug' );

		      jQuery(this).addClass( 'updating-message' );
		      jQuery(this).attr( 'disabled', 'disabled' );

		      event.preventDefault();

		      if ( 'install' === action ) {

		        wp.updates.installPlugin( {
		          slug: slug
		        } );

		      } else if ( 'activate' === action ) {

		        riauActivatePlugin( url, jQuery( this ) );

		      }

		    } );

		    riauContainer.on( 'click', '.riau-dismiss', function( event ) {
		    	var container = jQuery(this).parents( '.riau-plugin-card' ),
		    		data = jQuery(this).data(),
		    		ajaxData = {
						action: 'riau_smart_notitification',
						security: '<?php echo $ajax_nonce; ?>',
					};

		    	event.preventDefault();

		    	ajaxData.slug = data.dismiss;

		    	jQuery.post( '<?php echo admin_url( 'admin-ajax.php' ) ?>', ajaxData, function( response ) {
					container.slideUp( 'fast', function() {
						jQuery( this ).remove();
					} );
				});

		    });

		    jQuery( document ).on( 'wp-plugin-install-success', function( response, data ) {
		      var el = riauContainer.find( '.riau-plugin-button[data-slug="' + data.slug + '"]' );
		      event.preventDefault();
		      riauActivatePlugin( data.activateUrl, el );
		    } );

		</script>

		<?php
	}

}
