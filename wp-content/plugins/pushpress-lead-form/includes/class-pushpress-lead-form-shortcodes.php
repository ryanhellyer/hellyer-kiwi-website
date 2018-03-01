<?php
		
function pushpress_lead_form_shortcode_folder() { 
	return plugin_dir_path( dirname( __FILE__ ) ) . 'shortcodes';
}

function pushpress_lead_form_shortcode( $atts = array() ) {

	$fields = array(
		"first_name",
		"last_name",
		"email",
		"phone",
		"postal_code",
		"dob",
		"dob_month",
		"dob_year",
		"dob_day",
		"referred_by_id",
		"referred_by_user_id",
		"message",
		"lead_type",
		"lead_desired_gymtime",
		"preferred_communication"
	);

	try {
		$client = Pushpress_Lead_Form_Model::get_client();
		// get integration settings

		if ( false === ( $_integration_settings = get_transient( 'lead_settings' ) ) ) {
			$_integration_settings = Pushpress_Client::settings('lead_capture');        
			set_transient( 'lead_settings', $_integration_settings, 300 );
		}        
	}
	catch (Exception $e) { 
		
	}

	foreach ($_integration_settings->data as $setting) { 
		$integration_settings[$setting->name] = $setting->value;
	}

	$objectives = json_decode($integration_settings['lead_page_client_objectives']);
	$lead_types = Pushpress_Util::convertPushpressObjectToArray(json_decode($integration_settings['lead_page_lead_types']));

	$_referral_sources = Pushpress_Client::referralSources();
	$_referral_sources = $_referral_sources->data;

	foreach ($_referral_sources as $setting) { 
		$referral_sources[] = $setting;
	}

	$post = $_POST;

	foreach ( $fields as $field ) {
		if ( ! isset( $post[$field]  ) ) { 
			$post[$field] = '';
		}
	}

	$staff = Pushpress_Lead_Form_Model::get_staff(); 


	$_POST['form_submitted'] = false;
	
	if (
		$post 
		&&
		isset( $_POST['lead_form_submission_nonce'] )
		&&
		wp_verify_nonce( $_POST['lead_form_submission_nonce'], 'lead_form_submission' )
	) { 
	 
		// SANITIZE DATA
		$post['first_name']     = sanitize_text_field($_POST['first_name']);
		$post['last_name']      = sanitize_text_field($_POST['last_name']);
		$post['email']          = sanitize_text_field($_POST['email']);
		$post['phone']          = sanitize_text_field($_POST['phone']);
		$post['postal_code']    = sanitize_text_field($_POST['postal_code']);
		$post['lead_type']      = sanitize_text_field($_POST['lead_type']);
		$post['message']        = sanitize_text_field($_POST['message']);
		$post['redirect_nonce'] = sanitize_text_field($_POST['redirect_nonce']);
				
		// VALIDATION
		$error = false;   

		if (!strlen(trim($post['first_name']))) {
			$notify = array('msg'=>"First name is required", 'class'=>"error");
			Wp_Pushpress_Messages::set_messages( $notify );
			$error = true;
		}
		if (!strlen(trim($post['last_name']))) {
			$notify = array('msg'=>"Last name is required", 'class'=>"error");
			Wp_Pushpress_Messages::set_messages( $notify );
			$error = true;
		}
		if (!filter_var(trim($post['email']), FILTER_VALIDATE_EMAIL)) {
			$notify = array('msg'=>"A valid email is required", 'class'=>"error");
			Wp_Pushpress_Messages::set_messages( $notify );
			$error = true;
		}
		if (
			$integration_settings['lead_page_show_phone'] 
			&&
			$integration_settings['lead_page_phone_required'] 
			&&
			! strlen( trim( $post['phone'] ) )
		) {
			$notify = array('msg'=>"Phone is required", 'class'=>"error");
			Wp_Pushpress_Messages::set_messages( $notify );
			$error = true;
		}

		if (
			$integration_settings['lead_page_show_postal'] 
			&&
			$integration_settings['lead_page_postal_required'] 
			&&
			! strlen( trim($post['postal_code'] ) )
		) {
			$notify = array('msg'=>"Postal Code is required", 'class'=>"error");
			Wp_Pushpress_Messages::set_messages( $notify );
			$error = true;  
		}


		if (
			$integration_settings['lead_page_referral_required'] 
			&&
			! strlen( trim( $post['referred_by_id'] ) )
		) { 
			$notify = array('msg'=>"How did you hear about us is required", 'class'=>"error");
			Wp_Pushpress_Messages::set_messages( $notify );
			$error = true;  
		}
	
		if (
			$integration_settings['lead_page_preferred_comm_required'] 
			&&
			! strlen( trim( $post['preferred_communication'] ) )
		) { 
			$notify = array('msg'=>"Preferred communication is required", 'class'=>"error");
			Wp_Pushpress_Messages::set_messages( $notify );
			$error = true;  
		}

		if (
			$integration_settings['lead_page_message_required'] 
			&&
			! strlen( trim( $post['message'] ) )
		) {
			$notify = array('msg'=>"A Message is required", 'class'=>"error");
			Wp_Pushpress_Messages::set_messages( $notify );
			$error = true;  
		}    


		if (
			$post['dob_year'] || $post['dob_month'] || $post['dob_day']
		) {
			$dob = $post['dob_year'] . '-' . $post['dob_month'] . '-' . $post['dob_day'];
			$dob_timestamp = strtotime( $dob );

			if (!$dob_timestamp) { 
				$notify = array('msg'=>"Date of birth is invalid", 'class'=>"error");
				Wp_Pushpress_Messages::set_messages( $notify );
				$error = true;
			}

			$post['dob'] = $dob;
		}
		else { 
			$post['dob'] = null;
		}

		if ( ! $error ) {

			$customer = array(
				'email'         => $post['email'],
				'first_name'    => $post['first_name'],
				'last_name'     => $post['last_name'],
				'dob'           => $post['dob'],
				'phone'         => $post['phone'],
				'postal_code'   => $post['postal_code'],
				'objective'     => $post['objective'],
				'referred_by_id' => $post["referred_by_id"],
				'referred_by_user_id' => $post["referred_by_user_id"],
				'lead_desired_gymtime' => $post['lead_desired_gymtime'],
				'preferred_communication' => $post['preferred_communication'],
				'lead_type'     =>  isset($post['lead_type']) ? $post['lead_type'] : null,
				'lead_message'  => $post['message'],
				'status'        => 'lead',
				'lead_source'   => 'Wordpress Plugin',
				'is_lead'       => 1,
				'is_sale'       => 0                
			);

			try {
				$customer = Pushpress_Customer::create($customer);
				add_action('wp_footer', 'pushpress_header_hook_lead_conversion' );

				$submitMessage = "Thank you for submitting your information. We will contact you shortly";

				Wp_Pushpress_Messages::$leadSubmitSuccess = true;

				$notify = array('msg'=>$submitMessage, 'class'=>"success");
			
				Wp_Pushpress_Messages::set_messages( $notify );

				$_POST['form_submitted'] = true;
			} catch (Exception $e){
				$_POST['form_submitted'] = false;
				$notify = array('msg'=>$e->getMessage(), 'class'=>"error");
				Wp_Pushpress_Messages::set_messages( $notify );
			}
		}
	}

	$args = array();

	if(!isset($atts['id'])) { 
		$atts['id'] = "pushpress-calendar";
	}

	if (isset($atts['type'])) {
		$args['type'] = $atts['type'];
	}
	if (isset($atts['calendar_item_type'])) {
		$args['calendar_type_id'] = $atts['calendar_item_type'];
	}

	ob_start();
	include pushpress_lead_form_shortcode_folder() . '/lead-form.php';
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
add_shortcode( 'pushpress-lead-form', 'pushpress_lead_form_shortcode' );





/*******************
	LEAD CONVERTED
*******************/

function pushpress_header_hook_lead_conversion(){

	$metrics = Pushpress_Lead_Form_Model::get_metrics(); 
	$client = Pushpress_Lead_Form_Model::get_client();
	
	$value = $metrics['average_lead_value'];
	$currency_iso = $client->currency_iso;

	$integrations = Pushpress_Lead_Form_Model::get_marketing_integrations();

	if( strlen($integrations['facebook-marketing']['pixel_id']) > 0 ){
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
	
	if (strlen($integrations['google-analytics']['tracking_id'])) { 
		$strGaPx = "\n<!-- GOOGLE LEAD CONVERSION EVENT -->";
		$strGaPx .= "\n<script>";
		$strGaPx .= "\npp_ga('send', 'event', 'Memberhip', 'Lead', '', " . round($value,2) . ");";
		$strGaPx .= "\n</script>";
		echo $strGaPx;
	}

	$redirect_to = $_POST['redirect_nonce'];

	if ( ! empty( $redirect_to ) ) {
		$redirect_to = urldecode($redirect_to);
		$redirect_to = str_replace("{user_id}", $user->uuid , $redirect_to);
		$redirect_to = str_replace("{first_name}", $user->first_name , $redirect_to);
		$redirect_to = str_replace("{last_name}", $user->last_name , $redirect_to);
		$redirect_to = str_replace("{email}", $user->email , $redirect_to);
		$redirect_to = str_replace("{postal_code}", $user->postal_code , $redirect_to);
		$redirect_to = str_replace("{desired_gymtime}", $params['lead_desired_gymtime'], $redirect_to);
		// header("Location: ".$redirect_to.""); /* Redirect browser */
		echo "<script>window.location.href='". $redirect_to. "';</script>";        
	}
	
}