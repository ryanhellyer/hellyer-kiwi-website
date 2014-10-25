<?php
/**
 * Plugin Name: Social Counter
 * Plugin URI:
 * Description: This is a plugin to automatically update the social counters of TCCC, based on TRANSIENTS Life Time
 * Version: 1.0
 * Author: Pixelis
 * Author URI: www.pixelis.fr
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;



add_action( 'wp_footer', 'bla' );
function bla() {
	echo "\n\nSOCIAL COUNTER DEMO:\n";
	print_r( cokefr_get_social_count_by_service('twitter', 'ccr') );
	echo "\nEND\n";
}





/*
 *  ADMIN
 */
class Cokefr_social_settings_page {
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options_social_counter;

	/**
	 * Start up
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page()
	{
		// This page will be under "Settings"
		add_options_page(
			'Settings Admin',
			'Social Counter Settings',
			'manage_options',
			'my-setting-admin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page()
	{
		// Set class property
		$this->options = get_option( 'social_counter_options' );
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>My Social Counter Settings</h2>
			<form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( 'my_option_group' );
				do_settings_sections( 'my-setting-admin' );
				submit_button();
				?>
			</form>
		</div>
	<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init()
	{
		$args=array();

		register_setting(
			'my_option_group', // Option group
			'social_counter_options', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		/*
		 *  MAIN SETTINGS
		 */
		add_settings_section(
			'setting_section_id', // ID
			'<h3 style="margin-top:30px; font-weight: bold;">Main Settings</h3>', // Title
			array( $this, '' ), // Callback
			'my-setting-admin' // Page
		);

		$args['id']='frequency_update';
		add_settings_field(
			'frequency_update', // ID
			'Fréquence de mise à jour :<br/> (en secondes )<br/> <small>1 heure 3600 / 1 jour = 86400</small> ', // Title
			array( $this, 'field_callback' ), // Callback
			'my-setting-admin', // Page
			'setting_section_id', // Section
			$args
			 //Arguments
		);


		/*
		 *  TWITTER SETTINGS
		 */
		add_settings_section(
			'setting_section_id_2', // ID
			'<h3 style="margin-top:30px; font-weight: bold;">Settings Twitter</h3>', // Title
			array( $this, '' ), // Callback
			'my-setting-admin' // Page
		);
		$args['id']='consumer_key';
		add_settings_field(
			'consumer_key',
			'Consumer Key',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_2', // Section
			$args //Arguments
		);
		$args['id']='consumer_secret';
		add_settings_field(
			'consumer_secret',
			'Consumer Secret',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_2', // Section
			$args //Arguments
		);
		$args['id']='access_token';
		add_settings_field(
			'access_token',
			'Access Token',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_2', // Section
			$args //Arguments
		);
		$args['id']='access_token_secret';
		add_settings_field(
			'access_token_secret',
			'Access Token Secret',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_2', // Section
			$args //Arguments
		);
		$args['id']='bearer_token';
		add_settings_field(
			'bearer_token',
			'Bearer Token',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_2', // Section
			$args //Arguments
		);
		$args['id']='tw_uid_ccr';
		add_settings_field(
			'tw_uid_ccr',
			'User ID CCR',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_2', // Section
			$args //Arguments
		);
		$args['id']='tw_username_ccr';
		add_settings_field(
			'tw_username_ccr',
			'User Name CCR',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_2', // Section
			$args //Arguments
		);
		$args['id']='tw_uid_ccl';
		add_settings_field(
			'tw_uid_ccl',
			'User ID CCL',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_2', // Section
			$args //Arguments
		);
		$args['id']='tw_username_ccl';
		add_settings_field(
			'tw_username_ccl',
			'User Name CCL',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_2', // Section
			$args //Arguments
		);
		$args['id']='tw_uid_ccz';
		add_settings_field(
			'tw_uid_ccz',
			'User ID CCZ',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_2', // Section
			$args //Arguments
		);
		$args['id']='tw_username_ccz';
		add_settings_field(
			'tw_username_ccz',
			'User Name CCZ',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_2', // Section
			$args //Arguments
		);

		/*
		 *  INSTAGRAM SETTINGS
		 */
		add_settings_section(
			'setting_section_id_3', // ID
			'<h3 style="margin-top:30px; font-weight: bold;">Settings Instagram</h3>', // Title
			array( $this, '' ), // Callback
			'my-setting-admin' // Page
		);
		$args['id']='access_token_inst';
		add_settings_field(
			'access_token_inst',
			'Access Token',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_3', // Section
			$args //Arguments
		);
		$args['id']='inst_uid_ccr';
		add_settings_field(
			'inst_uid_ccr',
			'User id CCR',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_3', // Section
			$args //Arguments
		);
		$args['id']='inst_uid_ccl';
		add_settings_field(
			'inst_uid_ccl',
			'User id CCL',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_3', // Section
			$args //Arguments
		);
		$args['id']='inst_uid_ccz';
		add_settings_field(
			'inst_uid_ccz',
			'User id CCZ',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_3', // Section
			$args //Arguments
		);

		/*
		 *  FACEBOOK SETTINGS
		 */
		add_settings_section(
			'setting_section_id_4', // ID
			'<h3 style="margin-top:30px; font-weight: bold;">Settings Facebook</h3>', // Title
			array( $this, '' ), // Callback
			'my-setting-admin' // Page
		);

		$args['id']='fb_uid_ccr';
		add_settings_field(
			'fb_uid_ccr',
			'User id CCR',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_4', // Section
			$args //Arguments
		);
		$args['id']='fb_uid_ccl';
		add_settings_field(
			'fb_uid_ccl',
			'User id CCL',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_4', // Section
			$args //Arguments
		);
		$args['id']='fb_uid_ccz';
		add_settings_field(
			'fb_uid_ccz',
			'User id CCZ',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_4', // Section
			$args //Arguments
		);

		/*
		 *  YOUTUBE SETTINGS
		 */
		add_settings_section(
			'setting_section_id_5', // ID
			'<h3 style="margin-top:30px; font-weight: bold;">Settings Youtube</h3>', // Title
			array( $this, '' ), // Callback
			'my-setting-admin' // Page
		);
		$args['id']='yt_uid_ccr';
		add_settings_field(
			'yt_uid_ccr',
			'User id CCR',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_5', // Section
			$args //Arguments
		);
		$args['id']='yt_uid_ccl';
		add_settings_field(
			'yt_uid_ccl',
			'User id CCL',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_5', // Section
			$args //Arguments
		);
		$args['id']='yt_uid_ccz';
		add_settings_field(
			'yt_uid_ccz',
			'User id CCZ',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_5', // Section
			$args //Arguments
		);

		/*
		 *  GOOGLE + SETTINGS
		 */
		add_settings_section(
			'setting_section_id_6', // ID
			'<h3 style="margin-top:30px; font-weight: bold;">Settings Google +</h3>', // Title
			array( $this, '' ), // Callback
			'my-setting-admin' // Page
		);
		$args['id']='gp_uid_ccr';
		add_settings_field(
			'gp_uid_ccr',
			'User id CCR',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_6', // Section
			$args //Arguments
		);
		$args['id']='gp_uid_ccl';
		add_settings_field(
			'gp_uid_ccl',
			'User id CCL',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_6', // Section
			$args //Arguments
		);
		$args['id']='gp_uid_ccz';
		add_settings_field(
			'gp_uid_ccz',
			'User id CCZ',
			array( $this, 'field_callback' ),
			'my-setting-admin',
			'setting_section_id_6', // Section
			$args //Arguments
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input )
	{
		$new_input = array();
		if( isset( $input['frequency_update'] ) )
			$new_input['frequency_update'] = absint( $input['frequency_update'] );

		/*
		 * TWITTER
		 */
		if( isset( $input['consumer_key'] ) )
			$new_input['consumer_key'] = sanitize_text_field( $input['consumer_key'] );
		if( isset( $input['consumer_secret'] ) )
			$new_input['consumer_secret'] = sanitize_text_field( $input['consumer_secret'] );
		if( isset( $input['access_token'] ) )
			$new_input['access_token'] = sanitize_text_field( $input['access_token'] );
		if( isset( $input['access_token_secret'] ) )
			$new_input['access_token_secret'] = sanitize_text_field( $input['access_token_secret'] );
		if( isset( $input['bearer_token'] ) )
			$new_input['bearer_token'] = sanitize_text_field( $input['bearer_token'] );
		if( isset( $input['tw_uid_ccr'] ) )
			$new_input['tw_uid_ccr'] = sanitize_text_field( $input['tw_uid_ccr'] );
		if( isset( $input['tw_username_ccr'] ) )
			$new_input['tw_username_ccr'] = sanitize_text_field( $input['tw_username_ccr'] );
		if( isset( $input['tw_uid_ccl'] ) )
			$new_input['tw_uid_ccl'] = sanitize_text_field( $input['tw_uid_ccl'] );
		if( isset( $input['tw_username_ccl'] ) )
			$new_input['tw_username_ccl'] = sanitize_text_field( $input['tw_username_ccl'] );
		if( isset( $input['tw_uid_ccz'] ) )
			$new_input['tw_uid_ccz'] = sanitize_text_field( $input['tw_uid_ccz'] );
		if( isset( $input['tw_username_ccz'] ) )
			$new_input['tw_username_ccz'] = sanitize_text_field( $input['tw_username_ccz'] );


		/*
		 *  INSTAGRAM
		 */
		if( isset( $input['access_token_inst'] ) )
			$new_input['access_token_inst'] = sanitize_text_field( $input['access_token_inst'] );
		if( isset( $input['inst_uid_ccr'] ) )
			$new_input['inst_uid_ccr'] = sanitize_text_field( $input['inst_uid_ccr'] );
		if( isset( $input['inst_uid_ccl'] ) )
			$new_input['inst_uid_ccl'] = sanitize_text_field( $input['inst_uid_ccl'] );
		if( isset( $input['inst_uid_ccz'] ) )
			$new_input['inst_uid_ccz'] = sanitize_text_field( $input['inst_uid_ccz'] );

		/*
		 *  FACEBOOK
		 */
		if( isset( $input['fb_uid_ccr'] ) )
			$new_input['fb_uid_ccr'] = sanitize_text_field( $input['fb_uid_ccr'] );
		if( isset( $input['fb_uid_ccl'] ) )
			$new_input['fb_uid_ccl'] = sanitize_text_field( $input['fb_uid_ccl'] );
		if( isset( $input['fb_uid_ccz'] ) )
			$new_input['fb_uid_ccz'] = sanitize_text_field( $input['fb_uid_ccz'] );

		/*
		 *  YOUTUBE
		 */
		if( isset( $input['yt_uid_ccr'] ) )
			$new_input['yt_uid_ccr'] = sanitize_text_field( $input['yt_uid_ccr'] );
		if( isset( $input['yt_uid_ccl'] ) )
			$new_input['yt_uid_ccl'] = sanitize_text_field( $input['yt_uid_ccl'] );
		if( isset( $input['yt_uid_ccz'] ) )
			$new_input['yt_uid_ccz'] = sanitize_text_field( $input['yt_uid_ccz'] );

		/*
		 *  GOOGLE +
		 */
		if( isset( $input['gp_uid_ccr'] ) )
			$new_input['gp_uid_ccr'] = sanitize_text_field( $input['gp_uid_ccr'] );
		if( isset( $input['gp_uid_ccl'] ) )
			$new_input['gp_uid_ccl'] = sanitize_text_field( $input['gp_uid_ccl'] );
		if( isset( $input['gp_uid_ccz'] ) )
			$new_input['gp_uid_ccz'] = sanitize_text_field( $input['gp_uid_ccz'] );


		return $new_input;
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function field_callback($arg)
	{
		$field_id = $arg['id'];
		printf(
			'<input type="text" id="'.$field_id.'" name="social_counter_options['.$field_id.']" value="%s" />',
			isset( $this->options[$field_id] ) ? esc_attr( $this->options[$field_id]) : ''
		);
	}
}

if( is_admin() )
	$cokefr_social_settings_page = new Cokefr_social_settings_page();

/*
 *  PLUGIN
 */


// Sets the plugin path.
define( 'SOCIAL_COUNTER_PATH', plugin_dir_path( __FILE__ ) );
/*
 *  SETTINGS
 */
$transientDelay='';
$options_social_counter = get_option( 'social_counter_options' );
if(!empty($options_social_counter))
{
	$transientDelay = $options_social_counter['frequency_update'] ;
	$SCCache = get_transient('socialcounter_cache');
	//If the delay is equal to 1337, you can force the refresh
	if ( $SCCache == FALSE || $transientDelay == 1337)
	{
		$updateTransient = TRUE;
		$transientDelay = 60;
	}
	else $updateTransient = FALSE;
	$SCCount = array();
	$settings = array();
	$settings['twitter_consumer_key']=$options_social_counter['consumer_key'] ;
	$settings['twitter_consumer_secret'] =$options_social_counter['consumer_secret'];
	$settings['twitter_access_token']=$options_social_counter['access_token'];
	$settings['twitter_access_token_secret']=$options_social_counter['access_token_secret'];
	$settings['twitter_bearer_token']=$options_social_counter['bearer_token'];

	require_once('codebird.php');
	\Codebird\Codebird::setConsumerKey($settings['twitter_consumer_key'], $settings['twitter_consumer_secret']);
	$cb = \Codebird\Codebird::getInstance();
	$cb->setToken( $settings['twitter_access_token'], $settings['twitter_access_token_secret']);

	\Codebird\Codebird::setBearerToken($settings['twitter_bearer_token']);
	$everythingOK = TRUE;
} else {
	$everythingOK = FALSE;
}



function cokefr_get_social_count_by_service($service, $name=NULL) {
	global $everythingOK;
	switch($service)
	{
		case 'facebook':
			return ($everythingOK) ? cokefr_get_facebook_count($name) : 0;
			break;
		case 'youtube':
			return ($everythingOK) ? cokefr_get_youtube_count($name): 0;
			break;
		case 'googleplus':
			return ($everythingOK) ? cokefr_get_googleplus_count($name): 0;
			break;
		case 'twitter':
			return ($everythingOK) ? cokefr_get_twitter_count($name): 0;
			break;
		case 'instagram':
			return ($everythingOK) ? cokefr_get_instagram_count($name): 0;
			break;
	}

}

/*
 *  FACEBOOK
 */
function cokefr_get_facebook_count($name) {
	global $SCCache;
	global $updateTransient;
	global $transientDelay;
	global $options_social_counter;
	$id = $options_social_counter['fb_uid_'.$name];
	if($updateTransient || empty($SCCache['facebook_'.$name]))
	{
		//Grab the fan_count from the FB FQL
		$facebook_data = wp_remote_get( 'http://api.facebook.com/restserver.php?method=facebook.fql.query&query=SELECT%20fan_count%20FROM%20page%20WHERE%20page_id=' . $id);
		if ( is_wp_error( $facebook_data ) ) {
			$SCCount['facebook_'.$name] = ( isset( $SCCache['facebook_'.$name] ) ) ? $SCCache['facebook_'.$name] : 0;
		} else {
			$facebook_xml = new SimpleXmlElement( $facebook_data['body'], LIBXML_NOCDATA );
			$facebook_count = (string) $facebook_xml->page->fan_count;
			if ( $facebook_count ) {
				//We formate the raw count to something more readable = 333 222 111;
				$SCCache['facebook_'.$name] = number_format($facebook_count, 0, '.', ' ');
				//Hourray ! we save the new transient
				set_transient( 'socialcounter_cache', $SCCache,  $transientDelay);
			} else {
				$SCCount['facebook_'.$name] = ( isset( $SCCache['facebook_'.$name] ) ) ? $SCCache['facebook_'.$name] : 0;
			}
		}
	}
	return $SCCache['facebook_'.$name];
}

/*
 *  YOUTUBE
 */
function cokefr_get_youtube_count($name) {
	global $SCCache;
	global $updateTransient;
	global $transientDelay;
	global $options_social_counter;
	$id =$options_social_counter['yt_uid_'.$name];
	if($updateTransient || empty($SCCache['youtube_'.$name]))
	{
		//Grab the count from the raw json return by YT API
		$youtube_data = wp_remote_get( 'http://gdata.youtube.com/feeds/api/users/'.$id);
		if(is_wp_error($youtube_data))
		{
			//Something Failed, we return 0
			$SCCache['youtube'.$name] = 0;
		} else {
			$youtube_body = str_replace( 'yt:', '', $youtube_data['body'] );
			$youtube_xml = new SimpleXmlElement( $youtube_body, LIBXML_NOCDATA );
			$youtube_count = (string) $youtube_xml->statistics['totalUploadViews'];
			//We formate the raw count to something more readable = 333 222 111;
			$SCCache['youtube_'.$name] = number_format($youtube_count, 0, '.', ' ');
			//Hourray ! we save the new transient
			set_transient( 'socialcounter_cache', $SCCache,  $transientDelay);
		}
	}
	return $SCCache['youtube_'.$name];
}

/*
 *  INSTAGRAM
 */
function cokefr_get_instagram_count($name) {
	global $SCCache;
	global $updateTransient;
	global $transientDelay;
	global $options_social_counter;
	$instagramAccessToken = $options_social_counter['access_token_inst'] ;
	$id = $options_social_counter['inst_uid_'.$name];
	if($updateTransient || empty($SCCache['instagram_'.$name]))
	{
		// Grab the data from the Instagram API
		$instagram_data = file_get_contents("https://api.instagram.com/v1/users/".$id."/?access_token=".$instagramAccessToken);
		$instagram_data = json_decode( $instagram_data);

		if ( is_wp_error( $instagram_data ) || '400' <= $instagram_data->meta->code ) {
			//Something Failed, we return 0
			$SCCache['instagram_'.$name] = 0;
		} else {
			if (
			isset( $instagram_data->data->counts->followed_by )
			) {
				$instagram_count = $instagram_data->data->counts->followed_by;
				//We formate the raw count to something more readable = 333 222 111;
				$SCCache['instagram_'.$name] = number_format($instagram_count, 0, '.', ' ');
				//Hourray ! we save the new transient
				set_transient( 'socialcounter_cache', $SCCache,  $transientDelay);
			} else {
				//Something Failed, we return 0
				$SCCache['instagram_'.$name] = 0;
			}
		}
	}
	return $SCCache['instagram_'.$name];
}

/*
 *  GOOGLE PLUS
 */
function cokefr_get_googleplus_count($name) {
	global $SCCache;
	global $updateTransient;
	global $transientDelay;
	global $options_social_counter;

	// Grab the UID from the plugin page
	$id = $options_social_counter['gp_uid_'.$name];

	if($updateTransient || empty($SCCache['googleplus_'.$name]))
	{
		$googleplus_id = 'https://plus.google.com/' . $id;
		$googleplus_data_params = array(
			'method'    => 'POST',
			'sslverify' => false,
			'timeout'   => 30,
			'headers'   => array( 'Content-Type' => 'application/json' ),
			'body'      => '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $googleplus_id . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]'
		);

		// Get googleplus data.
		$googleplus_data = wp_remote_get( 'https://clients6.google.com/rpc', $googleplus_data_params );

		if ( is_wp_error( $googleplus_data ) || '400' <= $googleplus_data['response']['code'] ) {
			//Something Failed, we return 0
			$SCCache['googleplus_'.$name] = 0;
		} else {
			$googleplus_response = json_decode( $googleplus_data['body'], true );

			if ( isset( $googleplus_response[0]['result']['metadata']['globalCounts']['count'] ) ) {
				$googleplus_count = $googleplus_response[0]['result']['metadata']['globalCounts']['count'];
				//We formate the raw count to something more readable = 333 222 111;
				$SCCache['googleplus_'.$name] = number_format($googleplus_count, 0, '.', ' ');
				//Hourray ! we save the new transient
				set_transient( 'socialcounter_cache', $SCCache,  $transientDelay);
			} else {
				//Something Failed, we return 0
				$SCCache['googleplus_'.$name] = 0;
			}
		}

	}
	return $SCCache['googleplus_'.$name];
}
/*
 *  TWITTER
 */
function cokefr_get_twitter_count($name) {
	global $SCCache;
	global $updateTransient;
	global $transientDelay;
	global $options_social_counter;

	if($updateTransient || empty($SCCache['twitter_'.$name]))
	{
		$id =$options_social_counter['tw_uid_'.$name];
		$userName = $options_social_counter['tw_username_'.$name];

		$cb = \Codebird\Codebird::getInstance();

		//initiate the TWITTER API method
		$api = 'users/show';
		$params['user_id']= $id;
		$params['screen_name'] = $userName;
		$data = (array) $cb->$api($params);
		//httpstatus = 200 if everything is OK
		if($data['httpstatus'] == 200)
		{
			//We formate the raw count to something more readable = 333 222 111;
			$SCCache['twitter_'.$name] = number_format($data['followers_count'], 0, '.', ' ');
			//Hourray ! we save the new transient
			set_transient( 'socialcounter_cache', $SCCache,  $transientDelay);
		} else {
			//Something Failed, we return 0
			return 0;
		}
	}
	return $SCCache['twitter_'.$name];

}
