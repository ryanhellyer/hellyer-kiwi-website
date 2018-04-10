<?php

class Pushpress_Lead_Form_Model {

    public static function init() {        
    }

    public static function get_client() { 
        $secret_code = get_option('wp-pushpress-secret-code');     
        if (strlen(trim($secret_code))) {            
            try {
                if( !$client = get_transient( 'pp_client_info' ) ){
                    PushpressApi::setApiKey($secret_code);
                    PushpressApi::setHost( PUSHPRESS_HOST );
                    PushpressApi::setApiVersion( PUSHPRESS_API_VERSION );
                    $client = Pushpress_Client::retrieve('self');
                    set_transient( 'pp_client_info', $client, 300 ); // 5m cache   
                }                 
            }
            catch (Exception $e) { 
                if( !$client = get_transient( 'pp_client_info_self' ) ){
                    $client = Pushpress_Client::retrieve('self');
                    set_transient( 'pp_client_info_self', $client, 300 ); // 5m cache   
                }                
            }
            return $client;        
        }
          
        return null;
        
    }   

    static function get_staff() { 
        try {
            $staff = Pushpress_Customer::all(array("is_staff"=>1, "active"=>1));
            return $staff->data;
        }
        catch (Exception $e) { 
            return array();
        }
        
    }

    public function get_marketing_integrations() { 
        
        try {
            if ( false === ( $facebook_settings = get_transient( 'facebook_settings' ) ) ) {
                $facebook_settings = Pushpress_Client::settings('facebook-marketing');
                set_transient( 'facebook_settings', $facebook_settings, 300 );
            }
            if ( false === ( $ga_settings = get_transient( 'google_settings' ) ) ) {
                $ga_settings = Pushpress_Client::settings('google-analytics');
                set_transient( 'google_settings', $ga_settings, 300 );
            }
            if ( false === ( $autopilot_settings = get_transient( 'autopilot_settings' ) ) ) {
                $autopilot_settings = Pushpress_Client::settings('autopilot');
                set_transient( 'autopilot_settings', $autopilot_settings, 300 );
            }

            $integration_settings = array_merge($facebook_settings->data, $ga_settings->data, $autopilot_settings->data);

        }
        catch (Exception $e) {
            $integration_settings = array();
        }

        $integrations = array();
        $i = array();

        foreach ($integration_settings as $item) {        
            $integrations[$item->type][$item->name] = $item->value;                
        }

        if (!isset($integrations['facebook-marketing']['pixel_id'])) { 
            $integrations['facebook-marketing']['pixel_id'] = null;
        }
        if (!isset($integrations['google-analytics']['tracking_id'])) { 
            $integrations['google-analytics']['tracking_id'] = null;
        }

        return $integrations;
    }

        

    public function get_metrics() {

        try {
            if ( false === ( $settingsObj = get_transient( 'metrics_settings' ) ) ) {
                $settingsObj = Pushpress_Client::settings('metrics');                
                set_transient( 'metrics_settings', $settingsObj, 300 );
            }
            
        }
        catch (Exception $e) {
            $settingsObj = array();
        }

        $metrics = array();
        foreach ($settingsObj->data as $item) {
            if (is_array($item)) {
                foreach ($item as $v) {                    
                    $metrics[$v['name']] = $v['value'];
                }
            }
            else { 
                $metrics[$item['name']] = $item['value'];
            }
        }
        if (!isset($metrics['average_lead_value'])) {
            $metrics['average_lead_value'] = 0;
        }        

        return $metrics;
    }
}

Pushpress_Lead_Form_Model::init();