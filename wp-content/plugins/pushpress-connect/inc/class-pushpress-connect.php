<?php

class PushPress_Connect {

	private $prefixPagesSlug = 'pushpress-';

	protected $prefixShortcodes = 'wp-pushpress-';
	protected $listPagesSlug;
	protected $subdomain;

	private $check_API_key = true;
	private $notification;
	private $model;
	private $integrations;

	/**
	 * Class constructor.
	 */
	public function __construct(){

		// Add page slugs to be auto-generated (uses translation strings to ensure that default slugs match the language of the site)
		$this->listPagesSlug = array(
			__( 'products', 'pushpress-connect' ),
			__( 'plans', 'pushpress-connect' ),
			__( 'events', 'pushpress-connect' ),
			__( 'schedule', 'pushpress-connect' ),
			__( 'workouts', 'pushpress-connect' ),
			__( 'leads', 'pushpress-connect' ),
		);

		add_action( 'init',                           array( $this, 'update_integration' ), 30 );
		add_action( 'init',                           array( $this, 'init_sdk' ), 20 );
		add_action( 'wp_loaded',                      array( $this, 'update_lead_info' ), 100 );
		add_action( 'admin_menu',                     array( $this, 'add_admin_pages' ) );
		add_action( 'wp_enqueue_scripts',             array( $this, 'stylesheets' ) );
		add_action( 'wp_enqueue_scripts',             array( $this, 'scripts' ), 30 );
		add_action( 'admin_enqueue_scripts',          array( $this, 'admin_scripts' ) );

		$this->model = new Wp_Pushpress_Model();

		add_action( 'wp_ajax_pushpress_ajax',         array( $this->model, 'save_integration_page_status' ) );
		add_action( 'wp_ajax_pushpress_ajax_section', array( $this->model, 'get_section' ) );
	}

	/**
	 * Initiate the PushPress SDK.
	 */
	public function init_sdk() {
		$this->subdomain = '';
		$secret_code = get_option( 'wp-pushpress-secret-code' );

		if ( strlen( trim( $secret_code ) ) ) {
			try{

				add_action( 'wp_head', array( $this, 'wp_header_hook' ) );
				add_action( 'wp_footer', array( $this, 'wp_footer_hook' ) );

				PushpressApi::setApiKey( $secret_code );
				PushpressApi::setHost( PUSHPRESS_HOST );
				PushpressApi::setApiVersion( PUSHPRESS_API_VERSION );

				$this->client = $this->model->get_client();

				if ( ! $this->client ) {
					// remove the api key
					delete_option( 'wp-pushpress-integration-key' );
				}
				else {
					$this->subdomain              = $this->client->subdomain;
					$this->integrations           = $this->model->facebook_integrations();
					$this->marketing_integrations = $this->model->pushpress_marketing_integrations();
				}

				define( 'PUSHPRESS_INTEGRATED', true );

			} catch ( Exception $e ) {
				$this->check_API_key = false;
				define( 'PUSHPRESS_INTEGRATED', false );
				if ( isset( $_GET['page'] ) && $_GET['page'] == 'pushpress' ) {
					Wp_Pushpress_Messages::set_messages( array( 'msg'=> esc_html__( 'Please enter Your PushPress Integration Code!', 'pushpress-connect' ), 'class'=> 'error' ) );
				}
			}
		}
		else {
			define( 'PUSHPRESS_INTEGRATED', false );
		}

	}

	/**
	 * Adding admin pages.
	 */
	public function add_admin_pages(){

		add_menu_page(
			esc_html__( 'PushPress', 'pushpress-connect' ),
			esc_html__( 'PushPress', 'pushpress-connect' ),
			'read',
			'pushpress',
			array( $this, 'main_admin_page' ),
			PUSHPRESS_URL . '/images/icon_p.png',
			100
		);

		add_submenu_page(
			'pushpress',
			esc_html__( 'PushPress', 'pushpress-connect' ),
			esc_html__( 'Connect', 'pushpress-connect' ),
			'manage_options',
			'pushpress'
		);

	}

	/**
	 * Main admin page.
	 */
	public function main_admin_page(){
		$this->insert_page();
		$client = $this->model->get_client();

		$options = wp_load_alloptions();
		$options = array_filter( $options, function( $v ) use( $options ) {
			return preg_match( '#pp-shortcode-#', array_search( $v, $options ) );
		});

		$opts = array();
		foreach ( $options as $key => $value ) {
			$name = str_replace( 'pp-shortcode-', '', $key);
			$opts[$name] = $value;
		}

		require( PUSHPRESS_DIR . '/admin/main.php' );
	}

	function get_client() {
		return $this->model->get_client();
	}

	/**
	 * Frontend stylesheets.
	 */
	public function stylesheets() {
		//wp_enqueue_style( 'wp_pushpress_jqueryui_css', PUSHPRESS_URL . '/css/jquery-ui.min.css', false, '1.11.2' );
		wp_enqueue_style( 'wp_pushpress_css', PUSHPRESS_URL . '/css/pushpress.css', false, PUSHPRESS_PLUGIN_VERSION );
		wp_enqueue_style( 'wp_pushpress_form_css', PUSHPRESS_URL . '/css/pushpress-form.css', false, PUSHPRESS_PLUGIN_VERSION );
		wp_enqueue_style( 'wp_pushpress_icons', PUSHPRESS_URL . '/css/icomoon.css', false, PUSHPRESS_PLUGIN_VERSION );
	}

	/**
	 * Frontend scripts.
	 */
	public function scripts() {
		//wp_enqueue_script( 'jquery' );
		//wp_enqueue_script( 'jquery-ui-core' );
		//wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'wp_pushpress_script_js', PUSHPRESS_URL . '/js/script.js', array( 'jQuery' ), '1.0.0', true );
	}

	/**
	 * Admin scripts and styles.
	 */
	function admin_scripts() {

		wp_register_style( 'pushpress_wp_admin_switchery_css', PUSHPRESS_URL . '/asset/css/switchery.min.css', false, '3.3.2' );
		wp_register_style( 'pushpress_wp_admin_css_pushpress', PUSHPRESS_URL . '/css_admin/pushpress.css', false, '1.0.0' );
		wp_register_style( 'pushpress_form', PUSHPRESS_URL . '/css/pushpress-form.css', false, PUSHPRESS_PLUGIN_VERSION );

		wp_register_script( 'pushpress_wp_admin_switchery_js', PUSHPRESS_URL . '/asset/js/switchery.min.js', false, '3.3.2' );
		wp_register_script( 'pushpress_wp_admin_pushpress_js', PUSHPRESS_URL . '/js_admin/pushpress.js', false, '1.5', true );

		wp_enqueue_style( 'pushpress_wp_admin_switchery_css' );
		wp_enqueue_style( 'pushpress_wp_admin_css_pushpress' );
		wp_enqueue_style( 'pushpress_form' );

		wp_enqueue_script( 'pushpress_wp_admin_switchery_js' );
		wp_enqueue_script( 'jquery-masonry' );
		wp_enqueue_script( 'pushpress_wp_admin_pushpress_js' );
	}

	function insert_page() {
		$postID = array();
		foreach ( $this->listPagesSlug as $pageSlug ) {
			$slug = $this->prefixPagesSlug . $pageSlug;
			if ( ! Wp_Pushpress_Model::check_page_slug_exist( $slug ) ){
				$shortcode = $this->prefixShortcodes . $pageSlug;
				$post = array(
					'post_content'   => '[' . $shortcode . ']',
					'post_name'      => $slug,
					'post_title'     => ucfirst( $pageSlug ),
					'post_status'    => 'private',
					'post_type'      => 'page',
					'post_author'    => get_current_user_id(),
					'comment_status' => 'closed'
				);
				$postID[ $pageSlug ] = wp_insert_post( $post );
			}
		}

		if ( ! empty( $postID ) ) {
			$pushpressPagesOption = get_option( 'wp-pushpress-page-id' );
			if ( empty( $pushpressPagesOption ) ) {
				add_option( 'wp-pushpress-page-id', $postID );
			} else {
				foreach ( $this->listPagesSlug as $pageSlug ) {
					if ( ! empty( $postID[ $pageSlug ] ) ){
						$pushpressPagesOption[ $pageSlug ] = $postID[ $pageSlug ];
					}
				}
				update_option( 'wp-pushpress-page-id', $pushpressPagesOption );
			}
		}
	}

	function update_integration() {

		if (
			// Check if data has been sent
			isset( $_POST['save_pushpress_apikey_nonce'] )
			&&
			// Check if nonce is correct
			wp_verify_nonce( $_POST['save_pushpress_apikey_nonce'], 'save_pushpress_apikey' )
			&&
			// Check if we are in the admin panel
			is_admin()
			&&
			// Check if the user has permission to change settings in the site
			current_user_can( 'manage_options' )
		) {

			if (
				isset( $_POST['btnAccount'] )
				&&
				isset( $_POST['pushpress_secret_code'] )
			) {
				$resultIntegration = false;
				$resultUpdate      = false;
				$pairing_code      = sanitize_text_field( $_POST['pushpress_pairing_code'] );
				$secret_code       = sanitize_text_field( $_POST['pushpress_secret_code'] );

				try {
					PushpressApi::setApiKey( $secret_code );
					PushpressApi::setHost( PUSHPRESS_HOST );
					PushpressApi::setApiVersion( PUSHPRESS_API_VERSION );
					// try to get the client to verify the connection
					$client = Pushpress_Client::retrieve( 'self' );
					$resultIntegration = true;

				} catch ( Exception $e ) {
					$resultIntegration = false;
				}
				if ( $resultIntegration ) {
					$resultUpdate = update_option( 'wp-pushpress-pairing-code', $pairing_code );
					$secretUpdate = update_option( 'wp-pushpress-secret-code', $secret_code );
				}

				if ( $resultIntegration == true && $resultUpdate == true){
					$notify = array( 'msg' => 'You have successfully paired to your PushPress Account!', 'class' => 'updated' );
					Wp_Pushpress_Messages::set_messages( $notify );
					$this->check_API_key = true;
				}elseif($resultIntegration == false){

					$notify = array( 'msg' => 'You have entered an invalid pairing code / secret code combination!', 'class' => 'error' );
					Wp_Pushpress_Messages::set_messages( $notify );
				}
			}

		}
	}

	function update_lead_info(){

		// lead_page_phone_required
		$data = $this->model->get_leads();
		$leads = $data['leads_list'];

		if (
			isset( $_POST['btnLead'] )
			&&
			isset( $_POST['save_leads_info_nonce'] )
			&&
			wp_verify_nonce( $_POST['save_leads_info_nonce'], 'save_leads_info' )
			&&
			$this->check_API_key
		) {
			$form['billing_first_name']      = sanitize_text_field( $_POST['billing_first_name'] );
			$form['billing_last_name']       = sanitize_text_field( $_POST['billing_last_name'] );
			$form['email']                   = sanitize_text_field( $_POST['email'] );
			$form['phone']                   = sanitize_text_field( $_POST['phone'] );
			$form['your_birthday']           = $_POST['your_birthday'];
			$form['billing_postal_code']     = sanitize_text_field( $_POST['billing_postal_code'] );
			$form['lead_type']               = sanitize_text_field( $_POST['lead_type'] );
			$form['lead_message']            = sanitize_text_field( $_POST['lead_message'] );
			$form['redirect_nonce']          = sanitize_text_field( $_POST['redirect_nonce'] );
			$form['objective']               = sanitize_text_field( $_POST['objective'] );
			$form['referred_by_id']          = sanitize_text_field( $_POST['referred_by_id'] );
			$form['referred_by_user_id']     = sanitize_text_field( $_POST['referred_by_user_id'] );
			$form['lead_desired_gymtime']    = sanitize_text_field( $_POST['lead_desired_gymtime'] );
			$form['preferred_communication'] = sanitize_text_field( $_POST['preferred_communication'] );

			// VALIDATION
			$error = false;
			if ( ! strlen( trim( $form['billing_first_name'] ) ) ) {
				$notify = array( 'msg' => 'First name is required', 'class' => 'updated' );
				Wp_Pushpress_Messages::set_messages( $notify );
				$error = true;
			}
			if ( ! strlen( trim( $form['billing_last_name'] ) ) ) {
				$notify = array( 'msg' => 'Last name is required', 'class'=> 'updated' );
				Wp_Pushpress_Messages::set_messages( $notify );
				$error = true;
			}
			if ( ! filter_var( trim( $form['email'] ), FILTER_VALIDATE_EMAIL ) ) {
				$notify = array( 'msg' => 'A valid email is required', 'class' => 'updated' );
				Wp_Pushpress_Messages::set_messages( $notify );
				$error = true;
			}
			if ( $leads['lead_page_show_phone'] &&  $leads['lead_page_phone_required'] && ! strlen( trim( $form['phone'] ) ) ) {
				$notify = array( 'msg' => 'Phone is required', 'class' => 'updated' );
				Wp_Pushpress_Messages::set_messages( $notify );
				$error = true;
			}

			if ($leads['lead_page_show_postal'] && $leads['lead_page_postal_required'] && ! strlen(trim($form['billing_postal_code']))) {
				$notify = array('msg'=>"Postal Code is required", 'class'=>"updated");
				Wp_Pushpress_Messages::set_messages( $notify );
				$error = true;
			}


			if ( $leadsList['lead_page_referral_required'] && ! strlen( trim( $form['referred_by_id'] ) ) ) {
				$notify = array( 'msg' => 'How did you hear about us is required', 'class' => 'updated' );
				Wp_Pushpress_Messages::set_messages( $notify );
				$error = true;
			}

			if ( $leadsList['lead_page_preferred_comm_required'] && ! strlen( trim( $form['preferred_communication'] ) ) ) {
				$notify = array( 'msg' => 'Preferred communication is required', 'class' => 'updated' );
				Wp_Pushpress_Messages::set_messages( $notify );
				$error = true;
			}

			if ( $leadsList['lead_page_message_required'] && ! strlen( trim($form['lead_message'] ) ) ) {
				$notify = array( 'msg' => 'A Message is required', 'class' => 'updated' );
				Wp_Pushpress_Messages::set_messages( $notify );
				$error = true;
			}


			$date = date_parse( $form['your_birthday'] );
			if ( $leads['lead_page_show_postal'] ) {
				if ( ! checkdate( $date['month'], $date['day'], $date['year'] ) ) {
					if ( $leads['lead_page_dob_required'] ) {
						$notify = array( 'msg' => 'Birthday is not a valid date', 'class' => 'updated' );
						Wp_Pushpress_Messages::set_messages( $notify );
						$error = true;
					}
				}
				else {
					$form['dob'] = $date['month'] . '/' . $date['day'] . '/' . $date['year'];
					$form['dob'] = date( 'Y-m-d', strtotime( $form['dob'] ) );
				}
			}
			else {
				$form['dob'] = null;
			}

			if ( ! $error ){

				// default some stuff we didnt ask for
				$params['billing_address_1'] = '';
				$params['billing_address_2'] = '';
				$params['billing_city'] = '';
				$params['billing_state'] = '';

				// default to the client for now
				$params['billing_country']  = '';

				// random password if new user
				$params['password'] = $this->GenerateKey();
				$params['email'] = $form['email'];
				$params['phone'] = $form['phone'];
				$params['dob'] = $form['dob'];
				$params['lead_type'] = $form['lead_type'];
				$params['lead_message'] = $form['lead_message'];
				$params['objective'] = $form['objective'];
				$params['lead_source'] = "WordPress Plugin";
				$params['referred_by_id'] = $form['referred_by_id'];
				$params['referred_by_user_id'] = $form['referred_by_user_id'];
				$params['lead_desired_gymtime'] = $form['lead_desired_gymtime'];
				$params['preferred_communication'] = $form['preferred_communication'];
				$params['source'] = "WordPress Plugin";

				$params['first_name'] = $form['billing_first_name'];
				$params['last_name'] = $form['billing_last_name'];
				$params['address_1'] = '';
				$params['city'] = '';
				$params['state'] = '';
				$params['country'] = '';
				$params['postal_code'] = $form['billing_postal_code'];
				$params['status'] = 'lead';
				$params['is_lead'] = 1;
				$params['is_sale'] = 0;


				$submitMessage = esc_html__( 'Thank you for submitting your information. We will contact you shortly', 'pushpress-connect' );
				try{
					$user = Pushpress_Customer::create( $params );
					add_action( 'wp_head', array( $this, 'wp_header_hook_lead_conversion' ) );

					$customer = Pushpress_Customer::retrieve( $user->uuid );
					$customer->preferred_communication = $form['preferred_communication'];
					$customer->save();

					Wp_Pushpress_Messages::$leadSubmitSuccess = true;

					$_POST['form_submitted'] = true;

				} catch (Exception $e){
					$submitMessage = $e->getMessage();
				}

				//notification after submit
				$notify = array( 'msg' => $submitMessage, 'class' => 'updated' );
				$redirect_to = $_POST['redirect_nonce'];
				Wp_Pushpress_Messages::set_messages( $notify );

				if ( ! empty( $redirect_to ) ) {
					$redirect_to = urldecode( $redirect_to );
					$redirect_to = str_replace( '{user_id}', $user->uuid , $redirect_to );
					$redirect_to = str_replace( '{first_name}', $user->first_name , $redirect_to );
					$redirect_to = str_replace( '{last_name}', $user->last_name , $redirect_to );
					$redirect_to = str_replace( '{email}', $user->email , $redirect_to );
					$redirect_to = str_replace( '{postal_code}', $user->postal_code , $redirect_to );
					$redirect_to = str_replace( '{desired_gymtime}', $params['lead_desired_gymtime'], $redirect_to );
					header( 'Location: ' . $redirect_to . '' ); /* Redirect browser */
					exit;
				}

			}

		}

	}

	function wp_header_hook(){

		echo '<!-- PushPress.com v. ' . esc_attr( PUSHPRESS_PLUGIN_VERSION ) . ' -->';

		if ( strlen( $this->marketing_integrations['facebook-marketing']['pixel_id'] ) > 0 ) {
			$strFbPx = "\n<!-- PushPress Facebook Pixel -->";
			$strFbPx .= "\n<script>\n!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','//connect.facebook.net/en_US/fbevents.js');";
			$strFbPx .= "\nfbq('init', '" . esc_html( $this->marketing_integrations['facebook-marketing']['pixel_id'] . "');" );
			$strFbPx .= "\nfbq('track', 'PageView');";
			$strFbPx .= "\n</script>";
			$strFbPx .= "\n<!-- END PushPress Facebook Pixel  -->\n";

			echo $strFbPx;
		}
		else {
			echo "\n<!-- no fb pixel installed -->\n";
		}



		if ( strlen( $this->marketing_integrations['google-analytics']['tracking_id'] ) > 0 ) {
			$strGaPx = "\n<!-- PushPress Google Pixel -->";
			$strGaPx .= "\n<script>\n (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) })(window,document,'script','https://www.google-analytics.com/analytics.js','pp_ga');";
			$strGaPx .= "\npp_ga('create', '" . esc_html( $this->marketing_integrations['google-analytics']['tracking_id'] ) . "', 'auto');";
			$strGaPx .= "\npp_ga('send', 'pageview');";
			$strGaPx .= "\n</script>";
			$strGaPx .= "\n<!-- END PushPress Google Pixel  -->\n";

			echo $strGaPx;
		}
		else {
			echo "\n<!-- no PushPress GA pixel installed -->\n";
		}


		if ( strlen( $this->marketing_integrations['autopilot']['tracking_code'] ) > 0 ){
			$strAutopilot = "\n<!-- PushPress Autopilot Code -->";
//RYAN			$strAutopilot = $strAutopilot . $this->marketing_integrations['autopilot']['tracking_code'];
			$strAutopilot = $strAutopilot . "\n<!-- END PushPress Autopilot Code  -->\n";
			echo $strAutopilot;
		}
		else {
			echo "\n<!-- no autopilot installed -->\n";
		}


	}

	function wp_header_hook_lead_conversion(){

		$metrics = $this->model->facebook_metrics();

		$value = $metrics['average_lead_value'];
		$currency_iso = $this->client->currency_iso;

		if( strlen( $this->integrations['facebook_pixel_id'] ) > 0 ){
			$strFbPx = "\n<!-- FACEBOOK LEAD CONVERSION EVENT -->";
			$strFbPx .= "\n<script>";
			$strFbPx .= "\nfbq('track', 'Lead', { ";
			$strFbPx .= "\n    content_name: '',";
			$strFbPx .= "\n    content_category: 'Membership',";
			$strFbPx .= "\n    value: " . esc_html( round( $value, 2 ) ) . ",";
			$strFbPx .= "\n    currency: '" . esc_html( $currency_iso ) . "'";
			$strFbPx .= "\n});";
			$strFbPx .= "\n</script>";
			echo $strFbPx;
		}

		if (strlen($this->integrations['ga_tracking_id'])) {

			$strGaPx = "\n<!-- GOOGLE LEAD CONVERSION EVENT -->";
			$strGaPx .= "\n<script>";
			$strGaPx .= "\npp_ga('send', 'event', 'Memberhip', 'Lead', '', " . esc_html( $value ) . ');';
			$strGaPx .= "\n</script>";
			echo $strGaPx;
		}


		return;

		$strConversionPixel = "";
		$pixel_id = $this->integrations['facebook_conversion_pixel'];
		if ( strlen( $pixel_id ) ) {

			$client = Pushpress_Client::retrieve('self');

			$strConversionPixel = "\n    <!-- Facebook Conversion Code for PushPress -->\n".
			"    <script>(function() {\n".
			"        var _fbq = window._fbq || (window._fbq = []);\n".
			"        if (!_fbq.loaded) {\n".
			"            var fbds = document.createElement('script');\n".
			"            fbds.async = true;\n".
			"            fbds.src = '//connect.facebook.net/en_US/fbds.js';\n".
			"            var s = document.getElementsByTagName('script')[0];\n".
			"            s.parentNode.insertBefore(fbds, s);\n".
			"            _fbq.loaded = true;\n".
			"        }\n".
			"    })();\n".
			"    window._fbq = window._fbq || [];\n".
			"    window._fbq.push(['track', '" . esc_html( trim( $pixel_id ) ) . "', {'value':'" . esc_html( trim( $value ) ) . "','currency':'" . esc_html( trim( $currency_iso ) ) . "'}]);\n".
			"    </script>\n".
			'    <noscript><img height="1" width="1" alt="" style="display:none" src="' . esc_url( 'https://www.facebook.com/tr?ev=' . trim( $pixel_id ) . '&amp;cd[value]=' . trim( $value ) . '&amp;cd[currency]=' . trim( $currency_iso ) . '&amp;noscript=1' ) . '" /></noscript>' . "\n";
		}

		$strConversionPixel .= "<!-- PUSHPRESS LEAD ON MAIN AUDIENCE PX -->\n<script>fbq('track', 'Lead');</script>\n\n";

		$data = $this->model->get_leads();
		$leads = $data['leads_list'];
		$redirect = $leads['lead_page_complete_redirect'];
		if ( ! empty( $redirect ) ) {
			$strConversionPixel .= "\n    <script>window.location.href = '" . esc_url( $redirect ) . "';</script>\n";
		}
		echo $strConversionPixel;
	}

	function wp_footer_hook() {
		echo '
		<div id="pushpress-footer" style="text-align:center;">
			<a style="font-size:0.75em;" href="https://pushpress.com">
				' . sprintf( esc_html__( 'Another PushPress Powered Gym - %s', 'pushpress-connect' ), esc_html( 'v.' . PUSHPRESS_PLUGIN_VERSION ) ) . '
			</a>
		</div>';
	}

	function AssignRandValue(){
		$pool = '1234567890abcdefghijklmnopqrstuvwxyz';
		$num_chars = strlen( $pool );
		mt_srand( ( double ) microtime() * 1000000 );
		$index = mt_rand( 0, $num_chars - 1 );
		return $pool[$index];
	}

	function GenerateKey( $length = 8 ) {
		if ( $length > 0 ) {
			$rand_id="";
			for( $i = 1; $i <= $length; $i++ ) {
				$rand_id .= $this->AssignRandValue();
			}
		}

		return $rand_id;
	}

}
