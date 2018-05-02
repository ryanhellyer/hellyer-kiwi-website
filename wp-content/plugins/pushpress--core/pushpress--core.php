<?php
/**
 * @package WP-PushPress
 * @version 2.0.0
 */
/*
Plugin Name: PushPress Core
Plugin URI: https://pushpress.com
Description: This is a requirment to run the PushPress Systems on this
Author: PushPress, Inc
Version: 1.6.2
Author URI: https://pushpress.com
*/
define('PUSHPRESS_LOCAL', TRUE);
define('PUSHPRESS_DEV', FALSE);
define( 'PP_PLUGIN_VERSION', '2.0.0');
define( 'PUSHPRESS_ERROR_REPORT', 0 );
define( 'PUSHPRESS_BUTTON_CLASS',  'pushpress-button-class-' . rand(0, 10000));
define("PUSHPRESS_RECAPTCHA_KEY", "6LeU3iEUAAAAAP8ZbIIYjbISvIO6j_zL3SrAGgoc");
define("PUSHPRESS_RECAPTCHA_SECRET","6LeU3iEUAAAAAPpfA2LCVlMog01rhPD5GPkGyLiW");

if(!class_exists('Pushpress_Plugin')){
	class Pushpress_Plugin{

		private $prefixPagesSlug;
		private $prefixShortcodes;
		private $listPagesSlug;
		private $subdomain;

		private $check_API_key = true;
		private $notification;
		private $model;
		private $integrations;
		function __construct(){

			register_activation_hook(__FILE__, array($this, 'activation') );

			$errors = $this->_checkDependencies();


			# init other resources only when no error
			if(empty($errors)) {
				$this->define_constant();
				$this->prefixPagesSlug = 'pushpress-';
				$this->prefixShortcodes = 'wp-pushpress-';
				$this->listPagesSlug = array('products', 'plans', 'events', 'schedule', 'workouts', 'leads');

				if ( is_admin() ){
					$this->includes_admin();
				}
				$this->includes();
				add_action( 'init', array( $this, 'update_integration' ), 30 );
				add_action( 'init', array( $this, 'init_sdk' ), 20 );
				add_action( 'wp_loaded', array($this, 'update_lead_info'), 100 );

				add_action( 'admin_menu', array($this, 'pushpress_admin_pages') );
				add_action('wp_dashboard_setup', array($this, 'pushpress_dashboard_setup'), 999999);
				add_action( 'admin_enqueue_scripts', array($this, 'pushpress_admin_head_script') );
				add_action( 'wp_enqueue_scripts', array($this, 'pushpress_head_style') );
				add_action( 'wp_enqueue_scripts', array($this, 'pushpress_head_script'), 30);
				$this->model = new Wp_Pushpress_Model();
				add_action( 'wp_ajax_pushpress_ajax', array($this->model, 'save_integration_page_status') );
				add_action( 'wp_ajax_pushpress_ajax_section', array($this->model, 'get_section') );
			} else {
				// can add formal admin notification here
				add_action( 'admin_notices', array($this, 'admin_notices_dependencies') );
			}


			// GRAVITY FORM HOOKS
			add_action( 'gform_after_save_form', array($this,'pushpress_gravity_form_saved'), 10, 2 );
			add_action( 'gform_after_submission', array($this,'pushpress_after_gravity_form_submission'), 10, 2 );
			add_action( 'gform_loaded', array($this,'pushpress_after_gravity_form_loaded'), 10, 0 );

			//add_action( 'gform_after_submission', array($this,'pushpress_after_user_form_submission'), 10, 2 );

		}

		function admin_notices_dependencies() {
			$errors = $this->_checkDependencies();
			$class = "error";
			$message = implode('</br>', $errors);
			echo"<div class=\"$class\"> <p>$message</p></div>";
		}


		function _checkDependencies() {
			$errors = array();
			if(!function_exists('curl_init')) {
				$errors[] = 'In order to run the PushPress plugin, cURL support must be enabled (and it is not).  Please talk to your hosting provider to see if they can enable cURL support.';
			}
			return $errors;
		}

		function activation(){
			$errors = $this->_checkDependencies();

			if(!empty($errors)) {
				$error_message = "<strong>There was an error installing PushPress</strong><br><br>" . implode("<br>", $errors);
				deactivate_plugins( plugin_basename( __FILE__ ) );
                wp_die( __( $error_message, 'my-plugin' ), 'Plugin dependency check', array( 'back_link' => true ) );
			}
			else {
				// echo '<div style="color: #bd0000; border: 1px solid #bd0000; border-radius: 5px; padding: 10px;">Siuccess</div>';
			}

			$error = ob_get_contents();
			mail("dan@pushpress.com", "WP Install Error", $error);
			mail("pat@pushpress.com", "WP Install Error", $error);

		}

		// function errorHandler($email, $subject, $message){
		// 	wp_mail( $email, $subject, $message );
		// }

		function define_constant(){
			define('PUSHPRESS_URL', plugins_url('', __FILE__ ));
			define('PUSHPRESS_DIR', dirname(__FILE__));
			define('PUSHPRESS_FRONTEND', PUSHPRESS_DIR . '/templates/frontend/');
			define('PUSHPRESS_BACKEND', PUSHPRESS_DIR . '/templates/');
			define('PUSHPRESS_INC', PUSHPRESS_DIR . '/inc/');
		}

		function init_sdk(){
			$this->subdomain = "";
			$secret_code = get_option('wp-pushpress-secret-code');

			if (strlen(trim($secret_code))) {
				try{

					add_action('wp_head', array($this, 'wp_header_hook') );
					add_action('wp_footer', array($this, 'wp_footer_hook') );

					PushpressApi::setApiKey($secret_code);
					PushpressApi::setHost( PUSHPRESS_HOST );
					PushpressApi::setApiVersion( PUSHPRESS_API_VERSION );

	                $this->client = $this->model->get_client();

	                if (!$this->client) {
	                	// remove the api key
	                	delete_option('wp-pushpress-integration-key');
	                }
	                else {
	                	$this->subdomain = $this->client->subdomain;
						$this->integrations = $this->model->facebook_integrations();
						$this->marketing_integrations = $this->model->pushpress_marketing_integrations();
	                }

					define("PUSHPRESS_INTEGRATED", true);

				} catch (Exception $e) {
					$this->check_API_key = false;
					define("PUSHPRESS_INTEGRATED", false);
					if( isset( $_GET['page'] ) && $_GET['page'] == 'pushpress' ){
						Wp_Pushpress_Messages::set_messages( array('msg'=>"Please enter Your PushPress Integration Code!", 'class'=>"error") );
					}
				}
				$this->pushpress_shortcode();
			}
			else {
				define("PUSHPRESS_INTEGRATED", false);
			}


		}

		function includes(){
			try {
				require_once PUSHPRESS_DIR . '/lib/php-sdk/lib/Pushpress.php';
				require_once PUSHPRESS_DIR . '/lib/LocalTime.php';
				include_once PUSHPRESS_INC . 'wp_pushpress_shortcode.php';
				require_once PUSHPRESS_INC . "wp_pushpress_model.php";
				include_once PUSHPRESS_INC . 'wp_pushpress_messages.php';
				/**
		 			* The class responsible for defining the coach custom post type
		 		*/
		 		require_once PUSHPRESS_INC . "post-type-pushpress-dashboard.php";				
			}
			catch(Exception $e) {

			}

		}

		function includes_admin(){
			include_once PUSHPRESS_INC . 'wp_pushpress_messages.php';
		}

		function pushpress_dashboard_setup() {
			global $wp_meta_boxes;
			
			foreach ($wp_meta_boxes['dashboard']['normal']['core'] as $item=>$widget) { 
				remove_meta_box( $item, 'dashboard', 'normal');
			}
			foreach ($wp_meta_boxes['dashboard']['side']['core'] as $item=>$widget) { 
				remove_meta_box( $item, 'dashboard', 'side');
			}

			
			// 1st Method - Declaring $wpdb as global and using it to execute an SQL query statement that returns a PHP object
			global $wpdb;
			$results = $wpdb->get_results( "SELECT * FROM pp_sites_posts where post_type = 'pp_dashboards' and post_status = 'publish'", OBJECT );

			foreach ($results as $index=>$box) { 
				wp_add_dashboard_widget($box->post_name, $box->post_title, array($this, 'render_dashboard_metabox'),  $box->post_content, array("item"=>$box));
				//add_meta_box( $box->post_name, __( 'My Meta Box', 'textdomain' ), 'pp_dashboard_render_metabox', 'post', 'high', $index );
			}

//			die();

		}
		function render_dashboard_metabox( $args, $post ) {
			//echo '<pre>';
			//var_dump($post['args']['item']->post_content);
			global $shortcode_tags;
			echo $post['args']['item']->post_content;
		}

		

		function pushpress_admin_pages(){
			add_menu_page( 'PushPress s', 'PushPress', 'read', 'pushpress', array($this, 'pushpress_main'), PUSHPRESS_URL . '/images/icon_p.png', 100 );
			add_submenu_page( 'pushpress', __( "PushPress Dashboard", 'pushpress-dashboard' ), __( "Dashboard", 'pushpress-dashboard' ), 'manage-options', 'pushpress-dashboard', array( $this, 'dashboard_index' ) );
		}

		function pushpress_main(){
			$this->insert_page();
			$client = $this->model->get_client();

			$options = wp_load_alloptions();
			$options = array_filter($options, function($v) use($options){
   				return preg_match('#pp-shortcode-#', array_search($v, $options));
 			});

			$opts = array();
			foreach ($options as $key=>$value) {
				$name = str_replace("pp-shortcode-", "", $key);
				$opts[$name] = $value;
			}

			include PUSHPRESS_DIR . '/admin/main.php';
		}

		function get_client() {
			return $this->model->get_client();
		}

		function dashboard_index() { 
			echo "hi";
		}

		function pushpress_head_style(){
			wp_enqueue_style( 'wp_pushpress_jqueryui_css', PUSHPRESS_URL . '/css/jquery-ui.min.css', false, '1.11.2' );
			wp_enqueue_style( 'wp_pushpress_css', PUSHPRESS_URL . '/css/pushpress.css', false, PP_PLUGIN_VERSION );
			wp_enqueue_style( 'wp_pushpress_form_css', PUSHPRESS_URL . '/css/pushpress-form.css', false, PP_PLUGIN_VERSION );
			wp_enqueue_style( 'wp_pushpress_icons', '//s3.amazonaws.com/icomoon.io/27761/PushPressiconfont/style.css', false, PP_PLUGIN_VERSION );
		}

		function pushpress_head_script(){
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'wp_pushpress_script_js', PUSHPRESS_URL . '/js/script.js', false, '1.0.0', true );

			wp_localize_script( 'wp_pushpress_button_class',  'pushpress_button_class', PUSHPRESS_BUTTON_CLASS );
		}

		function pushpress_admin_head_script(){
			wp_register_style( 'pushpress_wp_admin_switchery_css', PUSHPRESS_URL . '/asset/css/switchery.min.css', false, '3.3.2' );
			wp_register_style( 'pushpress_wp_admin_css_pushpress', PUSHPRESS_URL . '/css_admin/pushpress.css', false, '1.0.0' );
			wp_register_style( 'pushpress_form', PUSHPRESS_URL . '/css/pushpress-form.css', false, PP_PLUGIN_VERSION );

			wp_register_script( 'pushpress_wp_admin_switchery_js', PUSHPRESS_URL . '/asset/js/switchery.min.js', false, '3.3.2' );
			wp_register_script( 'pushpress_wp_admin_pushpress_js', PUSHPRESS_URL . '/js_admin/pushpress.js', false, '1.5', true );

			wp_enqueue_style( 'pushpress_wp_admin_switchery_css' );
			wp_enqueue_style( 'pushpress_wp_admin_css_pushpress' );
			wp_enqueue_style( 'pushpress_form' );

			wp_enqueue_script( 'pushpress_wp_admin_switchery_js' );
			wp_enqueue_script( 'jquery-masonry' );
			wp_enqueue_script( 'pushpress_wp_admin_pushpress_js' );
		}

		function pushpress_shortcode(){
			$shortcode = new Wp_Pushpress_Shortcode($this->subdomain);
			foreach ($this->listPagesSlug as $pageSlug) {
				add_shortcode( $this->prefixShortcodes . $pageSlug , array($shortcode, $pageSlug) );
			}
		}

		function insert_page(){
			$userID = get_current_user_id();
			$postID = array();
			foreach ($this->listPagesSlug as $pageSlug) {
				$slug = $this->prefixPagesSlug . $pageSlug;
				if ( !Wp_Pushpress_Model::check_page_slug_exist( $slug ) ){
					$shortcode = $this->prefixShortcodes . $pageSlug;
					$post = array(
								'post_content' => '[' . $shortcode . ']',
								'post_name' => $slug,
								'post_title' => ucfirst($pageSlug),
								'post_status' => 'private',
								'post_type' => 'page',
								'post_author' => $userID,
								'comment_status' => 'closed'
						);
					$postID[ $pageSlug ] = wp_insert_post($post);
				}
			}

			if ( !empty($postID) ){
				$pushpressPagesOption = get_option('wp-pushpress-page-id');
				if ( empty($pushpressPagesOption) ){
					add_option('wp-pushpress-page-id', $postID);
				}else{
					foreach ($this->listPagesSlug as $pageSlug) {
						if ( !empty( $postID[ $pageSlug ] ) ){
							$pushpressPagesOption[ $pageSlug ] = $postID[ $pageSlug ];
						}
					}
					update_option('wp-pushpress-page-id', $pushpressPagesOption);
				}
			}
		}

		function update_integration(){
			if( isset($_POST['save_pushpress_apikey_nonce']) && wp_verify_nonce($_POST['save_pushpress_apikey_nonce'], 'save_pushpress_apikey') && is_admin() ) {

				if ( isset($_POST['btnAccount']) && isset($_POST['pushpress_secret_code']) ){
					$resultIntegration = false;
					$resultUpdate = false;
					$pairing_code = sanitize_text_field($_POST['pushpress_pairing_code']);
					$secret_code = sanitize_text_field($_POST['pushpress_secret_code']);

					$recaptcha_sitekey = sanitize_text_field($_POST['recaptcha_sitekey']);
					$recaptcha_secretkey = sanitize_text_field($_POST['recaptcha_secretkey']);

					try {
						PushpressApi::setApiKey( $secret_code );
						PushpressApi::setHost( PUSHPRESS_HOST );
						PushpressApi::setApiVersion( PUSHPRESS_API_VERSION );
						// try to get the client to verify the connection
						$client = Pushpress_Client::retrieve('self');
						$resultIntegration = true;

					} catch (Exception $e) {
						$resultIntegration = false;
					}
					if ($resultIntegration){
						$resultUpdate = update_option( 'wp-pushpress-pairing-code', $pairing_code );
						$secretUpdate = update_option( 'wp-pushpress-secret-code', $secret_code );
					}

					$recaptcha_sitekey_result = update_option( 'wp-pushpress-recaptcha-sitekey', $recaptcha_sitekey );
					$recaptcha_secretkey_result = update_option( 'wp-pushpress-recaptcha-secretkey', $recaptcha_secretkey );
					if( $recaptcha_sitekey_result && $recaptcha_secretkey_result ){
						$notify = array('msg'=>"reCAPTCHA Keys are updated", 'class'=>"updated");
						Wp_Pushpress_Messages::set_messages( $notify );
					}


					if ( $resultIntegration == true && $resultUpdate == true){
						$notify = array('msg'=>"You have successfully paired to your PushPress Account!", 'class'=>"updated");
						Wp_Pushpress_Messages::set_messages( $notify );
						$this->check_API_key = true;
					}elseif($resultIntegration == false){

						$notify = array('msg'=>"You've entered an invalid pairing code / secret code combination!", 'class'=>"error");
						Wp_Pushpress_Messages::set_messages( $notify );
					}
				}

			}
		}
		function update_lead_info(){

			// lead_page_phone_required
			$data = $this->model->get_leads();
			$leads = $data['leads_list'];

			if( isset($_POST['btnLead'])
				&& isset($_POST['save_leads_info_nonce'])
				&& wp_verify_nonce($_POST['save_leads_info_nonce'], 'save_leads_info')
				&& $this->check_API_key ) {
				$form['billing_first_name'] = sanitize_text_field($_POST['billing_first_name']);
				$form['billing_last_name'] = sanitize_text_field($_POST['billing_last_name']);
				$form['email'] = sanitize_text_field($_POST['email']);
				$form['phone'] = sanitize_text_field($_POST['phone']);
				$form['your_birthday'] = $_POST['your_birthday'];
				$form['billing_postal_code'] = sanitize_text_field($_POST['billing_postal_code']);
				$form['lead_type'] = sanitize_text_field($_POST['lead_type']);
				$form['lead_message'] = sanitize_text_field($_POST['lead_message']);
				$form['redirect_nonce'] = sanitize_text_field($_POST['redirect_nonce']);
				$form['objective'] = sanitize_text_field($_POST['objective']);
				$form['referred_by_id'] = sanitize_text_field($_POST['referred_by_id']);
				$form['referred_by_user_id'] = sanitize_text_field($_POST['referred_by_user_id']);
				$form['lead_desired_gymtime'] = sanitize_text_field($_POST['lead_desired_gymtime']);
				$form['preferred_communication'] = sanitize_text_field($_POST['preferred_communication']);


				// VALIDATION
				$error = false;
				if (!strlen(trim($form['billing_first_name']))) {
					$notify = array('msg'=>"First name is required", 'class'=>"updated");
					Wp_Pushpress_Messages::set_messages( $notify );
					$error = true;
				}
				if (!strlen(trim($form['billing_last_name']))) {
					$notify = array('msg'=>"Last name is required", 'class'=>"updated");
					Wp_Pushpress_Messages::set_messages( $notify );
					$error = true;
				}
				if (!filter_var(trim($form['email']), FILTER_VALIDATE_EMAIL)) {
					$notify = array('msg'=>"A valid email is required", 'class'=>"updated");
					Wp_Pushpress_Messages::set_messages( $notify );
					$error = true;
				}
				if ($leads['lead_page_show_phone'] &&  $leads['lead_page_phone_required'] && !strlen(trim($form['phone']))) {
					$notify = array('msg'=>"Phone is required", 'class'=>"updated");
					Wp_Pushpress_Messages::set_messages( $notify );
					$error = true;
				}

				if ($leads['lead_page_show_postal'] && $leads['lead_page_postal_required'] && ! strlen(trim($form['billing_postal_code']))) {
					$notify = array('msg'=>"Postal Code is required", 'class'=>"updated");
					Wp_Pushpress_Messages::set_messages( $notify );
					$error = true;
				}


				if ($leadsList['lead_page_referral_required'] && ! strlen(trim($form['referred_by_id'])) ) {
					$notify = array('msg'=>"How did you hear about us is required", 'class'=>"updated");
					Wp_Pushpress_Messages::set_messages( $notify );
					$error = true;
				}

				if ($leadsList['lead_page_preferred_comm_required'] && ! strlen(trim($form['preferred_communication']))) {
					$notify = array('msg'=>"Preferred communication is required", 'class'=>"updated");
					Wp_Pushpress_Messages::set_messages( $notify );
					$error = true;
				}

				if ($leadsList['lead_page_message_required'] && ! strlen(trim($form['lead_message']))) {
					$notify = array('msg'=>"A Message is required", 'class'=>"updated");
					Wp_Pushpress_Messages::set_messages( $notify );
					$error = true;
				}


				$date = date_parse($form['your_birthday']);
				if ($leads['lead_page_show_postal']) {
					if (! checkdate( $date['month'], $date['day'], $date['year'] )) {
						if ($leads['lead_page_dob_required']) {
							$notify = array('msg'=>"Birthday is not a valid date", 'class'=>"updated");
							Wp_Pushpress_Messages::set_messages( $notify );
							$error = true;
						}
					}
					else {
						$form['dob'] = $date['month'] . '/' . $date['day'] . '/' . $date['year'];
						$form['dob'] = date("Y-m-d", strtotime($form['dob']));
					}
				}
				else {
					$form['dob'] = null;
				}

				if (strlen(trim(get_option('wp-pushpress-recaptcha-sitekey')))) {

					$g_recaptcha_response = sanitize_text_field($_POST['g-recaptcha-response']);
					//check recaptcha
					$g_recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
					$g_recaptcha_secret = get_option( 'wp-pushpress-recaptcha-secretkey' );
					$fields_string = "secret=" . $g_recaptcha_secret . "&response=".$g_recaptcha_response;
					$fields_num = 2;
					$reCaptchaResult = $this->connect_CURL($g_recaptcha_url, $fields_num, $fields_string);
					$reCaptcha = json_decode($reCaptchaResult);
					if(!$reCaptcha->success){
						$notify = array('msg'=>"You've entered an invalid captcha!", 'class'=>"updated");
						Wp_Pushpress_Messages::set_messages( $notify );
						return;
					}
				}

				if( !$error ){

					// default some stuff we didnt ask for
					$params['billing_address_1'] = "";
					$params['billing_address_2'] = "";
					$params['billing_city'] = "";
					$params['billing_state'] = "";

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
					$params['lead_source'] = "Wordpress Plugin";
					$params['referred_by_id'] = $form['referred_by_id'];
					$params['referred_by_user_id'] = $form['referred_by_user_id'];
					$params['lead_desired_gymtime'] = $form['lead_desired_gymtime'];
					$params['preferred_communication'] = $form['preferred_communication'];
					$params['source'] = "Wordpress Plugin";

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


					$submitMessage = "Thank you for submitting your information. We will contact you shortly";
					try{
						$user = Pushpress_Customer::create($params);
						add_action('wp_head', array($this, 'wp_header_hook_lead_conversion') );

						$customer = Pushpress_Customer::retrieve($user->uuid);
						$customer->preferred_communication = $form['preferred_communication'];
						$customer->save();

						/*
						$p = array(
	                        "client_user_id" => $user->cuid,
	                        "lead_status" => "new",
	                        "notes" => "Lead generated from Wordpress lead generation form",
	                        "operator_id" => 0
	                	);

	                	if (strlen(trim($params['lead_message']))) {
		                	$p['notes'] .= "\n\nMessage Submitted:\n" . $form['lead_message'];
		                }

						$customer->leadupdate($p);
						*/
						Wp_Pushpress_Messages::$leadSubmitSuccess = true;

						$_POST['form_submitted'] = true;


					}catch (Exception $e){
						$submitMessage = $e->getMessage();
					}

					//notification after submit
					$notify = array('msg'=>$submitMessage, 'class'=>"updated");
					$redirect_to = $_POST['redirect_nonce'];
					Wp_Pushpress_Messages::set_messages( $notify );

					if(!empty($redirect_to)){
						$redirect_to = urldecode($redirect_to);
						$redirect_to = str_replace("{user_id}", $user->uuid , $redirect_to);
	            		$redirect_to = str_replace("{first_name}", $user->first_name , $redirect_to);
	            		$redirect_to = str_replace("{last_name}", $user->last_name , $redirect_to);
	            		$redirect_to = str_replace("{email}", $user->email , $redirect_to);
	            		$redirect_to = str_replace("{postal_code}", $user->postal_code , $redirect_to);
	            		$redirect_to = str_replace("{desired_gymtime}", $params['lead_desired_gymtime'], $redirect_to);
						header("Location: ".$redirect_to.""); /* Redirect browser */
						exit;
					}

				}

			}

		}

		function wp_header_hook(){

			echo "<!-- PushPress.com v. " . PP_PLUGIN_VERSION. " -->";

			if( strlen($this->marketing_integrations['facebook-marketing']['pixel_id']) > 0 ){
				$strFbPx = "\n<!-- PUSHPRESS FACEBOOK PIXEL -->";
				$strFbPx .= "\n<script>\n!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','//connect.facebook.net/en_US/fbevents.js');";
				$strFbPx .= "\nfbq('init', '" . $this->marketing_integrations['facebook-marketing']['pixel_id'] . "');";
				$strFbPx .= "\nfbq('track', 'PageView');";
				$strFbPx .= "\n</script>";
				$strFbPx .= "\n<!-- END PUSHPRESS FACEBOOK PIXEL  -->\n";

				echo $strFbPx;
			}
			else {
				echo "\n<!-- no fb pixel installed -->\n";
			}



			if( strlen($this->marketing_integrations['google-analytics']['tracking_id']) > 0 ){
				$strGaPx = "\n<!-- PUSHPRESS GOOGLE PIXEL -->";
				$strGaPx .= "\n<script>\n (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) })(window,document,'script','https://www.google-analytics.com/analytics.js','pp_ga');";
				$strGaPx .= "\npp_ga('create', '" .$this->marketing_integrations['google-analytics']['tracking_id']. "', 'auto');";
				$strGaPx .= "\npp_ga('send', 'pageview');";
				$strGaPx .= "\n</script>";
				$strGaPx .= "\n<!-- END PUSHPRESS GOOGLE PIXEL  -->\n";

				echo $strGaPx;
			}
			else {
				echo "\n<!-- no GA pixel installed -->\n";
			}


			if( strlen($this->marketing_integrations['autopilot']['tracking_code']) > 0 ){
				$strAutopilot = "\n<!-- PUSHPRESS AUTOPILOT CODE -->";
				$strAutopilot = $strAutopilot . $this->marketing_integrations['autopilot']['tracking_code'];
				$strAutopilot = $strAutopilot . "\n<!-- END PUSHPRESS AUTOPILOT CODE  -->\n";
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

			if( strlen($this->integrations['facebook_pixel_id']) > 0 ){
				$strFbPx = "\n<!-- FACEBOOK LEAD CONVERSION EVENT -->";
				$strFbPx .= "\n<script>";
				$strFbPx .= "\nfbq('track', 'Lead', { ";
				$strFbPx .= "\n    content_name: '',";
				$strFbPx .= "\n    content_category: 'Membership',";
				$strFbPx .= "\n    value: " . round($value, 2) . ",";
				$strFbPx .= "\n    currency: '" . $currency_iso . "'";
				$strFbPx .= "\n});";
				$strFbPx .= "\n</script>";
				echo $strFbPx;
			}

			if (strlen($this->integrations['ga_tracking_id'])) {

				$strGaPx = "\n<!-- GOOGLE LEAD CONVERSION EVENT -->";
				$strGaPx .= "\n<script>";
				$strGaPx .= "\npp_ga('send', 'event', 'Memberhip', 'Lead', '', " . $value . ");";
				$strGaPx .= "\n</script>";
				echo $strGaPx;
			}


			return;

			$strConversionPixel = "";
			$pixel_id = $this->integrations['facebook_conversion_pixel'];
			if(strlen($pixel_id)){

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
				"    window._fbq.push(['track', '" . trim($pixel_id) . "', {'value':'" . trim($value) . "','currency':'" . trim($currency_iso) . "'}]);\n".
				"    </script>\n".
				'    <noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=' . trim($pixel_id) . '&amp;cd[value]=' . trim($value) . '&amp;cd[currency]=' . trim($currency_iso) . '&amp;noscript=1" /></noscript>' . "\n";
			}

			$strConversionPixel .= "<!-- PUSHPRESS LEAD ON MAIN AUDIENCE PX -->\n<script>fbq('track', 'Lead');</script>\n\n";

			$data = $this->model->get_leads();
			$leads = $data['leads_list'];
			$redirect = $leads['lead_page_complete_redirect'];
			if( !empty( $redirect ) ){
				$strConversionPixel .= "\n    <script>window.location.href = '" . $redirect . "';</script>\n";
			}
			echo $strConversionPixel;
		}

		function wp_footer_hook() {
			echo '<div id="pushpress-footer" style="text-align:center;"><a style="font-size:0.75em;" href="https://pushpress.com">Another PushPress Powered Gym - v.' . PP_PLUGIN_VERSION . '</a></div>';
		}

		function connect_CURL($url, $fields_num, $fields_string){
			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			if( !empty($fields_num) ){
				curl_setopt($ch, CURLOPT_POST, $fields_num);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			}

			//execute post
			$result = curl_exec($ch);

			//close connection
			curl_close($ch);
			return $result;
		}

		function AssignRandValue(){
			$pool = '1234567890abcdefghijklmnopqrstuvwxyz';
			$num_chars = strlen($pool);
			mt_srand((double)microtime() * 1000000);
			$index = mt_rand(0, $num_chars - 1);
			return $pool[$index];
		}

		function GenerateKey($length = 8)
		{
			if($length > 0)
			{
				$rand_id="";
				for($i = 1; $i <= $length; $i++)
				{
					$rand_id .= $this->AssignRandValue();
				}
			}
			return $rand_id;
		}


		function pushpress_gravity_form_saved( $form, $is_new ) {
			global $wpdb;

			// echo "<BR>SAVING FORM<BR>";
			// echo '<pre>';
			// echo "<br>BlogID: " . get_current_blog_id();
			$blog_id = get_current_blog_id();
			$is_main_site = is_main_site($blog_id);

			if (!$is_main_site) {
				// we should pull this from main site to re-write
				return false;
			}

			// else this is main site.
			// echo "<BR>IS MASTER SITE?<bR>";
			// var_dump($is_main_site);

			$form_notifications = $form['notifications'];
			$form_confirmations = $form['confirmations'];

			// unset($form['notifications']);
			// unset($form['confirmations']);

			$form_meta = json_encode($form);
			$form_notifications_meta = json_encode($form_notifications);
			$form_confirmations_meta = json_encode($form_confirmations);

			$sites = wp_get_sites(array("archived"=>0, "deleted"=>0, "limit"=>2000));

			foreach ($sites as $site) {
				if ($site['blog_id'] == $blog_id) {
					continue;
				}

				$current_blog_id = $site['blog_id'];
				$form_table_name = "pp_sites_" . $current_blog_id. "_rg_form";
				$form_meta_table_name = "pp_sites_" . $current_blog_id. "_rg_form_meta";


				try {
					// echo "<br>Updating Base Form: " . $form_table_name;
					$core_form_data = array(
						"id" => $form['id'],
						"title" => $form['title'],
						"date_created" => date("Y-m-d g:i:s"),
						"is_active" => 1,
						"is_trash" => 0
					);
					// var_dump($core_form_data);

					// echo "<bR>Updating Form...";
					$wpdb->replace( $form_table_name, $core_form_data );
					// echo " success!";
					// $wpdb->update( $form_table_name, $data, $where, $format = null, $where_format = null );
				}
				catch(Exception $e) {
					// echo "<br><h4 style='color:red;'>Error Updating Form: " . $e->getMessage() . "</h4>";
				}

				try {
					// echo "<br>Updating Form Meta: " . $form_meta_table_name;

					$meta_form_data = array(
						"form_id" => $form['id'],
						"display_meta" => $form_meta,
						"entries_grid_meta" => null,
						"confirmations" => $form_confirmations_meta,
						"notifications" => $form_notifications_meta
					);


					// echo "<bR>Updating Form...";
					// var_dump($core_form_data);
					$wpdb->replace( $form_meta_table_name, $meta_form_data );
					// echo " success!";
					// $wpdb->update( $form_table_name, $data, $where, $format = null, $where_format = null );
				}
				catch(Exception $e) {
					// echo "<br><h4 style='color:red;'>Error Updating Form Meta: " . $e->getMessage() . "</h4>";
				}

			}
		}


		function pushpress_after_gravity_form_submission($entry, $form) {
			
			// first thing - check to see if we save to options
			$save_to_options  = false;

			foreach ($form['fields'] as $field) {

				$x = $field['label'];
				$x = strtolower($x);
		    	$x = preg_replace("/[^a-z0-9_\s-]/", "", $x);
		    	$x = preg_replace("/[\s-]+/", " ", $x);
		    	$x = preg_replace("/[\s_]/", "-", $x);

		    	if ($x == "save-to-options") {
		    		$id = $field['id'];
		    		$v  = $entry[$id];

		    		if (isset($v) && $v == 1) {
		    			$save_to_options = true;
		    			break;
		    		}
		    	}

		    	if (!$save_to_options) {
		    		return;
		    	}

			}


			foreach ($form['fields'] as $field) {
				
				$id = $field['id'];

				try {
					// if this has inputs, need to loop them
					if ($field['inputs']) {
						foreach ($field['inputs'] as $input) {
							$f['label'] = $input['label'];
							$f['adminLabel'] = "";

							$e = $entry[$input['id']];
							//$e = $input
							$this->pushpress_save_gravity_formfield_to_option($f, $e);
						}
						$test = maybe_unserialize( $option['value'] );
					}
					else {
						$e = $entry[$id];
						$unserialized = maybe_unserialize( $e );

						if (is_array($unserialized)) {
							$count = 1;
							$og_label = $field['label'];

							// loop over the data
							foreach ($unserialized as $unserial) {
								$base_label = $og_label . " " . $count;
								foreach ($unserial as $key=>$value) {
									$f['label'] = $base_label . " " . $key;
									$e = $value;
									$this->pushpress_save_gravity_formfield_to_option($f, $e);
								}

								$count++;
							}
						}
						else { 
							$f['label'] = $field->label;
							$f['adminLabel'] = $field->adminLabel;

							// $this->pushpress_save_gravity_form_to_option();
							$this->pushpress_save_gravity_formfield_to_option($f, $e);					
						}
					}
				}
				catch (Exception $e) { 
					echo "<br>ERROR: " . $e->getMessage();
				}
			}			
			
		}

		
		function pushpress_save_gravity_formfield_to_option($field=null, $value="") {

			if ($field['adminLabel']) {
				$label = $field['adminLabel'];
			}
			else {
				$label = $field['label'];
			}

			$label = strtolower($label);
		    //Make alphanumeric (removes all other characters)
		    $label = preg_replace("/[^a-z0-9_\s-]/", "", $label);
		    //Clean up multiple dashes or whitespaces
		    $label = preg_replace("/[\s-]+/", " ", $label);
		    //Convert whitespaces and underscore to dash
		    $label = preg_replace("/[\s_]/", "-", $label);


		    $option['option_name'] = "pp-shortcode-" . $label;
			$option['option_value'] = $value;
			
			update_option($option['option_name'], $option['option_value'], true);

		}

		function pushpress_after_gravity_form_loaded() {

		}

	}
	// listen for coach form submits
	//add_action( 'gform_after_submission_3', 'pushpress_after_coach_submit', 10, 2 );

	new Pushpress_Plugin();
}
