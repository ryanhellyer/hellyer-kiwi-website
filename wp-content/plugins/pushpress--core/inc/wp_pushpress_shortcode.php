<?php

class Wp_Pushpress_Shortcode {

    private $model;
    private $subdomain;

    function __construct($subdomain) {
        $this->subdomain = $subdomain;
        $this->model = new Wp_Pushpress_Model();
    }

    public static function pushpress_shortcode($atts) { 

        if (! $atts['name']) { 
            return;
        }

        $option_name = "pp-shortcode-" . $atts['name'];
        $option = get_option($option_name);

        return $option;
    }
    
    /*

    public function products($atts) {

        if (($atts['category'])) {
            $products = $this->model->get_products_by_category($atts['category']);            
        }
        else if (($atts['id'])) {
            $products = $this->model->get_products_by_id($atts['id']);
        } else {
            $products = $this->model->get_products();
        }
        $client = $this->model->get_client();

        ob_start();
        include PUSHPRESS_FRONTEND . 'shortcode_products.php';
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public function plans($atts) {

        if (empty($atts['id'])) {
            $plans = $this->model->get_plans();
            $courses = $this->model->get_events("course", $atts);
            // $events = $this->model->get_events("event", $atts);
        } else {
            // $events = $this->model->get_event_by_id($atts['id']);
            $plans = $this->model->get_plans_by_id($atts['id']);            
        }


        $client = $this->model->get_client();
        ob_start();
        include PUSHPRESS_FRONTEND . 'shortcode_plans.php';
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public function events($atts) {

        if (empty($atts['id'])) {
            $events = $this->model->get_events("event", $atts);
        } else {
            $events = $this->model->get_event_by_id($atts['id']);            
        }

        $client = $this->model->get_client();

        ob_start();
        include PUSHPRESS_FRONTEND . 'shortcode_plans.php';
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public function schedule($atts) {

        if (isset($_GET['datefilter'])) {
            $date = $_GET['datefilter'];
        }

        try {
            if( !$client = get_transient( 'pp_client2' ) ){
                $client = Pushpress_Client::retrieve('self');
                set_transient( 'pp_client2', $client, 3600 ); // 1 hour cache                                       
            }   
        
            //$client = Pushpress_Client::retrieve('self');
            $timeNow = LocalTime::toGM($client);
        } catch (Exception $e) {
            echo '<p>Could not show the PushPress Schedule. Please check with Administrator.</p>';
            return;
        }

        if (empty($date)) {
            $date = strtotime(date('m/d/Y', $timeNow));
        } else {
            $date = strtotime($date);
        }

        $client = $this->model->get_client();
        $defaultsAtts = array();
        $attributes = shortcode_atts($defaultsAtts, $atts);

//        $results = $this->model->get_workout($date, $timeNow, $attributes);
        $schedules = $this->model->get_schedules($date, $timeNow, $attributes);
        ob_start();
        include PUSHPRESS_FRONTEND . 'shortcode_schedules.php';
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public function workouts($atts) {
        if (isset($_GET['datefilter'])) {
            $date = $_GET['datefilter'];
        }

        try {
            if( !$client = get_transient( 'pp_client2' ) ){
                $client = Pushpress_Client::retrieve('self');
                set_transient( 'pp_client2', $client, 3600 ); // 1 hour cache                                       
            }   
        
            //$client = Pushpress_Client::retrieve('self');
            $timeNow = LocalTime::toGM($client);
        } catch (Exception $e) {
            echo '<p>Could not show the PushPress Workouts. Please check with Administrator.</p>';
            return;
        }

        if (empty($date)) {
            $date = strtotime(date('m/d/Y', $timeNow));
        } else {
            $date = strtotime($date);
        }

        $defaultsAtts = array();
        $attributes = shortcode_atts($defaultsAtts, $atts);

        $results = $this->model->get_workout($date, $timeNow, $attributes);
        
        foreach ($results as $key => $value) {
            $workouts[$key] = array(
                'track_id' => $value['track_id'],
                'track_name' => $value['track_name'],
                'data'=> $value['workouts'][$date]
            );
        }
        ob_start();
        include PUSHPRESS_FRONTEND . 'shortcode_workouts.php';
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public function leads($atts) {
        $form = array();
        if (isset($_POST['btnLead']) && Wp_Pushpress_Messages::$leadSubmitSuccess === false) {
            $form['billing_first_name'] = sanitize_text_field($_POST['billing_first_name']);
            $form['billing_last_name'] = sanitize_text_field($_POST['billing_last_name']);
            $form['email'] = sanitize_text_field($_POST['email']);
            $form['phone'] = sanitize_text_field($_POST['phone']);
            $form['your_birthday'] = $_POST['your_birthday'];
            $form['billing_postal_code'] = sanitize_text_field($_POST['billing_postal_code']);
            $form['lead_type'] = sanitize_text_field($_POST['lead_type']);
            $form['lead_message'] = sanitize_text_field($_POST['lead_message']);
            $form['redirect_nonce'] = sanitize_text_field($_POST['redirect']);
            $form['objective'] = sanitize_text_field($_POST['objective']);
            $form['referred_by_id'] = sanitize_text_field($_POST['referred_by_id']);
            $form['referred_by_user_id'] = sanitize_text_field($_POST['referred_by_user_id']);
            $form['lead_desired_gymtime'] = sanitize_text_field($_POST['lead_desired_gymtime']);

            $form['preferred_communication'] = sanitize_text_field($_POST['preferred_communication']);
        } else {
            $form['billing_first_name'] = '';
            $form['billing_last_name'] = '';
            $form['email'] = '';
            $form['phone'] = '';
            $form['your_birthday'] = '';
            $form['billing_postal_code'] = '';
            $form['lead_type'] = '';
            $form['lead_message'] = '';
            $form['redirect_nonce'] = '';
            $form['objective'] = '';
            $form['referred_by_id'] = '';
            $form['preferred_communication'] = '';
        }

        $defaultsAtts = array();
        $attributes = shortcode_atts($defaultsAtts, $atts);

        if( !$client = get_transient( 'pp_client2' ) ){
            echo "<br><br>Getting Client from API";
            try {
                $client = Pushpress_Client::retrieve('self');
            }
            catch(Exception $e) { 
                
            }
            set_transient( 'pp_client2', $client, 3600 ); // 1 hour cache               
        }   
        

        // $client = Pushpress_Client::retrieve('self');
        $data = $this->model->get_leads();
        $leads = $data['leads_list'];
        $staff = $this->model->get_staff();
        $referral = $data['referral'];
        $allowMessage = isset($leads['lead_page_allow_message']) ? $leads['lead_page_allow_message'] : 0;

        $leadsObj = json_decode($leads['lead_page_client_objectives']);
        $leadTypes = Pushpress_Util::convertPushpressObjectToArray(json_decode($leads['lead_page_lead_types']));
        ob_start();
        include PUSHPRESS_FRONTEND . 'shortcode_leads.php';
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
    */

}
add_shortcode( 'pushpress-shortcode', array('Wp_Pushpress_Shortcode', 'pushpress_shortcode') );
