<?php

class Wp_Pushpress_Model {

    function __construct() {
        
    }

    public function get_client() { 


        $pushpressApiKey = get_option('wp-pushpress-integration-key');     
        if (strlen(trim($pushpressApiKey))) {            
            try {
                if( !$client = get_transient( 'pp_client_info' ) ){
                    PushpressApi::setApiKey($pushpressApiKey);
                    PushpressApi::setHost( PUSHPRESS_HOST );
                    PushpressApi::setApiVersion( PUSHPRESS_VERSION );
                    $client = Pushpress_Client::retrieve('self');
                    set_transient( 'pp_client_info', $client, 300 ); // 5m cache   
                } 
            }
            catch (Exception $e) { 
                $client = Pushpress_Client::retrieve('self');
            }
            return $client;        
        }
          
        return null;
        
    }

    public function get_products() {

        $params = array(
            'active' => 1,
            'show_on_wp' => 1
        );
        //get products object
        $productsObj = Pushpress_Product::all($params);

        /*
        $key = "pp_client_active_products";
        if (! $productsObj = get_transient($key)) { 
            $params = array(
                'active' => 1,
                'show_on_wp' => 1
            );
            //get products object
            $productsObj = Pushpress_Product::all($params);

            set_transient( $key, $productsObj, 300 ); // 5m                        
        }        
        */

        $productsList = array();

        foreach ($productsObj->data as $product) {
                //get categories
                $catId = $product->category->uuid;
                $productsList[$catId]['category_name'] = $product->category->name;

                //get products
                $productsList[$catId]['products'][$product->uuid]['slug'] = $product->slug;
                $productsList[$catId]['products'][$product->uuid]['name'] = $product->name;
                $productsList[$catId]['products'][$product->uuid]['description'] = $product->description;

                $prices = array();
                $options = $product->options;
                foreach ($options as $option) {
                    $prices[] = $option->price;
                }
                sort($prices);
                $productsList[$catId]['products'][$product->uuid]['price'] = $prices;
            
        }



        //get preorder
        $livePreorders = Pushpress_Preorder::all(array(
                    "closed" => 0, "completed" => 0, "cancelled" => 0
        ));

        /*
        if (! $livePreorders = get_transient("pp_client_active_preorders")) { 
            //get preorder
            $livePreorders = Pushpress_Preorder::all(array(
                        "closed" => 0, "completed" => 0, "cancelled" => 0
            ));
            set_transient( 'pp_client_active_preorders', $livePreorders, 600 ); // 5m                        
        }
        */
        /*
            
        if (count($livePreorders) > 0) {
            // $client = Pushpress_Client::retrieve('self');
            $client = $this->getClient();
            $timeNow = LocalTime::toGM($client);
            foreach ($livePreorders->data as $product) {

                if ($product->end_timestamp >= $timeNow) {
                    //get categories
                    $catId = 'preorder_categories_id';
                    $productsList[$catId]['category_name'] = 'Preorder';

                    //get products
                    $productsList[$catId]['products'][$product->uuid]['slug'] = $product->product->slug;
                    $productsList[$catId]['products'][$product->uuid]['name'] = $product->product->name;
                    $productsList[$catId]['products'][$product->uuid]['description'] = $product->product->description;

                    $prices = array();
                    $options = $product->product->options;
                    foreach ($options as $option) {
                        $prices[] = $option->price;
                    }
                    sort($prices);
                    $productsList[$catId]['products'][$product->uuid]['price'] = $prices;
                }
            }
        }
        */
        
        return $productsList;
    }

    public function get_products_by_id($id) {
        $productsList = array();
        
        $key  = "pp_client_active_product_" . $id;

        try {
            $product = Pushpress_Product::retrieve($id);
        }
        catch (Exception $e) { 
            return array();
        }

        $productsList[$product->category->uuid]['products'][$product->uuid]['slug'] = $product->slug;
        $productsList[$product->category->uuid]['products'][$product->uuid]['name'] = $product->name;
        $productsList[$product->category->uuid]['products'][$product->uuid]['description'] = $product->description;
        $prices = array();
        $options = $product->options;
        foreach ($options as $option) {
            $prices[] = $option->price;
        }
        sort($prices);
        $productsList[$product->category->uuid]['products'][$product->uuid]['price'] = $prices;        

        /*
        if (! $product = get_transient($key)) { 
            
            //get products object
            $product = Pushpress_Product::retrieve($id);
            set_transient( $key, $productsObj, 300 ); // 5m

            $productsList[$product->category->uuid]['products'][$product->uuid]['slug'] = $product->slug;
            $productsList[$product->category->uuid]['products'][$product->uuid]['name'] = $product->name;
            $productsList[$product->category->uuid]['products'][$product->uuid]['description'] = $product->description;
            $prices = array();
            $options = $product->options;
            foreach ($options as $option) {
                $prices[] = $option->price;
            }
            sort($prices);
            $productsList[$product->category->uuid]['products'][$product->uuid]['price'] = $prices;
        }
        */

        return $productsList;

    }

    public function get_products_by_category($category) {
        
        $productsList = array();
        
        $params = array(
            'active' => 1
        );
        //get products object
        $productsObj = Pushpress_Product::all($params);

        /*
        $key  = "pp_client_active_products_" . $category;

        if (! $productsObj = get_transient($key)) { 
            $params = array(
                'active' => 1
            );
            //get products object
            $productsObj = Pushpress_Product::all($params);
            set_transient( $key, $productsObj, 300 ); // 5m
        }
        */

        foreach ($productsObj->data as $product) {
            if ($product->is_public) {
                //get categories
                $catId = $product->category->uuid;
                if ($catId == $category) {
                    $productsList[$catId]['category_name'] = $product->category->name;

                    //get products
                    $productsList[$catId]['products'][$product->uuid]['slug'] = $product->slug;
                    $productsList[$catId]['products'][$product->uuid]['name'] = $product->name;
                    $productsList[$catId]['products'][$product->uuid]['description'] = $product->description;

                    $prices = array();
                    $options = $product->options;
                    foreach ($options as $option) {
                        $prices[] = $option->price;
                    }
                    sort($prices);
                    $productsList[$catId]['products'][$product->uuid]['price'] = $prices;
                }
            }
        }
        return $productsList;
    }

    public function get_plans($atts = array()) {
        $client = $this->get_client();
        $plansList = array();

        $params = array(
            'active' => 1,
            'public' => 1,
            'type' => 'P,N,R'
        );
        //get all products object
        $plans = Pushpress_Plan::all($params);

                

        /*
        if (!$plans = get_transient("pp_client_active_plans_pnr")) { 
            $params = array(
                'active' => 1,
                'public' => 1,
                'type' => 'P,N,R'
            );
            //get all products object
            $plans = Pushpress_Plan::all($params);
            set_transient("pp_client_active_plans_pnr", $plans, 600);
        } 
        */       
        
        $planType = array('R' => 'Recurring', 'N' => 'Non-Recurring', 'P' => 'Punchcards');

            
        foreach ($plans->data as $plan) {
            $plansList[$plan->type][$plan->uuid]['name'] = $plan->name;
            $plansList[$plan->type][$plan->uuid]['price'] = $plan->amount;
            $plansList[$plan->type][$plan->uuid]['type'] = $planType[$plan->type];
        }
        return $plansList;
    }

    public function get_plans_by_id($id) {
        $plansList = array();
        
        try {

            $planType = array('R' => 'Recurring', 'N' => 'Non-Recurring', 'P' => 'Punchcards');

            if (!empty($id)) {
                //get all products object
                $plan = Pushpress_Plan::retrieve($id);
                $plansList[$plan->type][$plan->uuid]['name'] = $plan->name;
                $plansList[$plan->type][$plan->uuid]['price'] = $plan->amount;
                $plansList[$plan->type][$plan->uuid]['type'] = $planType[$plan->type];
            }
        } catch (Exception $e) {
            return array();
        }
        return $plansList;
    }

    public function get_plans_for_help() {
        $plansList = array();
        
        $params = array(
            'active' => 1,
            'public' => 1,                
        );
        //get all products object
        $plans = Pushpress_Plan::all($params);

        /*
        $key = "pp_client_plans_for_help";

        if (!$plans = get_transient($key)) { 
            $params = array(
                'active' => 1,
                'public' => 1,                
            );
            //get all products object
            $plans = Pushpress_Plan::all($params);
            set_transient($key, $plans, 600);
        } 
        */       
        
        foreach ($plans->data as $plan) {
            $plansList[$plan->slug] = $plan->name;
        }
        
        return $plansList;
    }

    public function get_categories_for_help() {
        $categories = array();
        
        $params = array(
            'active' => 1
        );
        //get all products object
        $items = Pushpress_ProductCategories::all($params);

        /*
        $key = "pp_client_product_categories_for_help";

        if (!$items = get_transient($key)) { 
            $params = array(
                'active' => 1
            );
            //get all products object
            $items = Pushpress_ProductCategories::all($params);
            set_transient($key, $items, 600);
        }
        */
        foreach ($items->data as $item) {
            $categories[$item->uuid] = mb_convert_encoding($item->name, 'UTF-8', 'HTML-ENTITIES');
        }
        
        return $categories;
    }

    public function get_events($isEvent = "event", $atts) {

        $doy = date("z");
        $year = date("Y");
        if ($doy >= 365) {
            $doy = ($doy % 365);
            $year++;
        }
        $start = strtotime("today 00:00:00");
        $end = strtotime("today 00:00:00 +1 year"); // 1 year later

        $calendar = Pushpress_Calendar::all(array(
                        'active' => 1,
                        'type' => $isEvent,
                        'start_time' => $start,
                        'end_time' => $end
            ));

        /*
        $cache_key = "pp_client_calendar_" . $isEvent . "_" . $start . "_" . $end;
        
        if (! $calendar = get_transient($cache_key)) { 
            $calendar = Pushpress_Calendar::all(array(
                        'active' => 1,
                        'type' => $isEvent,
                        'start_time' => $start,
                        'end_time' => $end
            ));

            set_transient($cache_key, $calendar, 600);
        }
        */
        $eventsList = array();
    
        foreach ($calendar->data as $item) {
            if (($item->doy) >= $doy) {
                $eventsList[$item->uuid]['title'] = $item->title;
                $eventsList[$item->uuid]['start_datetime'] = date('m/d/Y', $item->start_timestamp);
                $eventsList[$item->uuid]['end_datetime'] = date('m/d/Y', $item->end_timestamp);
                $eventsList[$item->uuid]['price'] = $item->price;
            }
        }
        
        return $eventsList;
    }

    public function get_event_by_id($id) {
        $eventsList = array();
        try {

            if ($this->event_exist($id)) {
                $item = Pushpress_Calendar::retrieve($id);

                $eventsList[$item->uuid]['title'] = $item->title;
                $eventsList[$item->uuid]['start_datetime'] = date('m/d/Y', $item->start_timestamp);
                $eventsList[$item->uuid]['end_datetime'] = date('m/d/Y', $item->end_timestamp);
                $eventsList[$item->uuid]['price'] = $item->price;
            }
        } catch (Exception $e) {
            return array();
        }
        return $eventsList;
    }

    public function get_events_for_help() {
        $eventsList = array();
        try {
            $doy = date("z");
            $year = date("Y");
            if ($doy >= 365) {
                $doy = ($doy % 365);
                $year++;
            }
            $start = strtotime("today 00:00:00");
            $end = strtotime("today 00:00:00 +1 year"); // 1 year later
            $calendar = Pushpress_Calendar::all(array(
                        'active' => 1,
                        'type' => "event",
                        'start_time' => $start,
                        'end_time' => $end
            ));
            foreach ($calendar->data as $item) {
                if (($item->doy) >= $doy) {
                    $eventsList[$item->uuid] = $item->title;
                }
            }
        } catch (Exception $e) {
            return;
        }
        return $eventsList;
    }

    public function get_schedules($date, $timeNow, $atts) {
        $schedulesList = array();


        $start_dayofweek = strtotime("this week", $date);

        $doyCurrent = date("z", $start_dayofweek);

        $year = date("Y", $timeNow);
        if ($doyCurrent >= 365) {
            $doyCurrent = ($doyCurrent % 365);
            $year++;
        }
        $n = 7;

        for ($i = 0; $i < $n; $i++) {
            $doy = $doyCurrent + $i;

            $cache_key = "pp_client_schedule_2" . $doy . "_" . $year . "_class";
            
            $calendar = Pushpress_Calendar::all(array(
                            'active' => 1,
                            'doy' => $doy,
                            'year' => $year,
                            'type' => 'Class',
                ));
            /*
            if (! $calendar = get_transient($cache_key)){ 
                $calendar = Pushpress_Calendar::all(array(
                            'active' => 1,
                            'doy' => $doy,
                            'year' => $year,
                            'type' => 'Class',
                ));
                set_transient($cache_key, $calendar, 600);
            }
            */
        
            foreach ($calendar->data as $item) {
                
                if (($item->doy) >= $doy) {
                    $timestamp = strtotime("$year-01-01 + $doy days 00:00:00");
                    $schedulesList[$timestamp][$item->uuid]['start_timestamp'] = $item->start_timestamp;
                    $schedulesList[$timestamp][$item->uuid]['end_timestamp'] = $item->end_timestamp;
                    $schedulesList[$timestamp][$item->uuid]['title'] = $item->title;
                    $schedulesList[$timestamp][$item->uuid]['attendance_cap'] = $item->attendance_cap;

                    $lastName = substr($item->coach_last_name, 0, 1);
                    $schedulesList[$timestamp][$item->uuid]['fullname'] = $item->coach_first_name . " " . $lastName;

                    $status = array();
                    if ($item->attendance_cap == 0 || $item->registration_count < $item->attendance_cap) {
                        $status['name'] = 'Reservation available';
                        $status['class'] = 'schedule-reservation';
                        if ($item->attendance_cap) { 
                            $status['spots_available'] = $item->attendance_cap - $item->registration_count;
                        }
                        else { 
                            $status['spots_available'] = -1;
                        }
                    } else {
                        $status['name'] = 'Class full';
                        $status['class'] = 'schedule-full';
                        $status['spots_available'] = 0;
                    }
                    $schedulesList[$timestamp][$item->uuid]['status'] = $status;
                }
            }
        }
        
        return $schedulesList;
    }

    public function get_workout($date, $timeNow, $atts) {
        try {
            $client = $this->get_client();
            // $client = Pushpress_Client::retrieve('self');
            $firtday = strtotime("last sunday this week", $date);
            $startDate = date('m/d/Y', $firtday);
            $endDate = date('m/d/Y', strtotime("next sunday", $date));
            $params = array(
                'active' => 1,
                'deleted' => 0,
                'start_date' => $startDate,
                'end_date' => $endDate
            );

            // if (!$tracks = get_transient("pp_client_tracks")) { 
                $tracks = Pushpress_Track::all();
               // set_transient("pp_client_tracks", $tracks, 600);
            // }

            //var_dump($tracks);

            $results = array();
            foreach ($tracks->data as $key => $track) {
                //check public to wordpress plugin
                if($track->publish_wordpress){
                    $results[$key] = array(
                        'track_id' => $track->uuid,
                        'track_name' => $track->name
                    );
                    $public_setting = array(
                        'publish_day' => $track->publish_day,
                        'publish_time' => $track->publish_time
                    );

                    $params['track_id'] = $track->uuid;

                    $workouts = Pushpress_Track_Workout::all($params);
                    /*
                    if (!$workouts = get_transient("pp_track_workouts_" . $track->uuid)) { 
                        $workouts = Pushpress_Track_Workout::all($params);
                        set_transient("pp_track_workouts_" . $track->uuid, $workouts, 600);
                    }
                    */

                    $results[$key]['workouts'] = $this->filter_data_by_date($workouts, $timeNow, $client, $public_setting);
                    
                }
            }            
        } catch (Exception $e) {
            return;
        }
        return $results;
    }

    private function filter_data_by_date($workouts, $timeNow, $client, $public_setting) {        
        
        $workoutList = array();
        foreach ($workouts->data as $item) {
            $public_day = $public_setting['publish_day'];
            $public_time = $public_setting['publish_time'];
            $workout_date_timestamp = strtotime($item->workout_date);
            $public_date_timestamp = strtotime($item->workout_date."-$public_day day +$public_time hour");
            $public_date_timestamp_not_time = strtotime($item->workout_date."-$public_day day");
            
            if ($timeNow < $public_date_timestamp) {                
                $publish = date('g:ia m/d/Y', $public_date_timestamp);
                if (isset($workoutList[$workout_date_timestamp]) === FALSE ) {
                    $workoutList[$workout_date_timestamp][$item->uuid]['type'] = "";
                    $workoutList[$workout_date_timestamp][$item->uuid]['description'] = "Workout will publish @" . $publish;
                }
            } else {                
                $workoutList[$workout_date_timestamp][$item->uuid]['type'] = $item->workout_type['name'];
                $descriptionArr = preg_split("/((\r?\n)|(\r\n?))/", $item->description);
                $descriptionStr = implode("<br />", $descriptionArr);
                $workoutList[$workout_date_timestamp][$item->uuid]['name'] = null;
                $workoutList[$workout_date_timestamp][$item->uuid]['description'] = $descriptionStr;
                $workoutList[$workout_date_timestamp][$item->uuid]['public_notes'] = $item->public_notes;

                if ($item->favorite_workout) { 
                    $workoutList[$workout_date_timestamp][$item->uuid]['name'] = $item->favorite_workout->name;
                }
            }
        }
        return $workoutList;
    }

    public function get_leads() {
        $leadsList = array();
        $referral = array();
        try {

            $client = $this->get_client();
            // get integration settings
            $integration_settings = Pushpress_Client::settings('lead_capture');
            $integration_settings = $integration_settings->data;
            /*
            if (!$integration_settings = get_transient("pp_settings_lead_capture")) {
                $integration_settings = Pushpress_Client::settings('lead_capture');
                $integration_settings = $integration_settings->data;
                set_transient("pp_settings_lead_capture", $integration_settings, 300); // 5m
            }
            */
            
            foreach ($integration_settings as $item) {
                $leadsList[$item->name] = $item->value;
            }

            $referral_sources = Pushpress_Client::referralSources();
            /*
            if (!$referral_sources = get_transient("pp_settings_referral_sources")) { 
                $referral_sources = Pushpress_Client::referralSources();
                set_transient("pp_settings_referral_sources", $referral_sources, 3600); // 1 hour
            } 
            */           

            foreach ($referral_sources->data as $item) {
                $referral[] = array('id' => $item['id'], 'name' => $item['name'], 'show_staff_list' => $item['show_staff_list']);
            }
        } catch (Exception $e) {
            return;
        }

        // default all settings
        if (!isset($leadsList['lead_page_title'])) {
            $leadsList['lead_page_title'] = "Interested?";
        }
        if (!isset($leadsList['lead_page_description']) || !strlen(trim($leadsList['lead_page_description']))) {
            $leadsList['lead_page_description'] = "Simply enter your contact information below and we'll get back to you as soon as possible with more information!";
        }
        if (!isset($leadsList['lead_page_client_objectives'])) {
            $leadsList['lead_page_client_objectives'] = array(
                "Weight Loss",
                "Athletic Performance",
                "Health Reasons",
                "Other"
            );
        }
        if (!isset($leadsList['lead_page_complete_redirect'])) {
            $leadsList['lead_page_complete_redirect'] = null;
        }
        if (!isset($leadsList['lead_page_allow_message'])) {
            $leadsList['lead_page_allow_message'] = 0;
        }
        if (!isset($leadsList['lead_page_show_client_objectives'])) {
            $leadsList['lead_page_show_client_objectives'] = 0;
        }
        if (!isset($leadsList['lead_page_show_lead_types'])) {
            $leadsList['lead_page_show_lead_types'] = 0;
        }
        if (!isset($leadsList['lead_page_show_phone'])) {
            $leadsList['lead_page_show_phone'] = 0;
        }
        if (!isset($leadsList['lead_page_show_dob'])) {
            $leadsList['lead_page_show_dob'] = 0;
        }
        if (!isset($leadsList['lead_page_show_referral_source'])) {
            $leadsList['lead_page_show_referral_source'] = 0;
        }
        if (!isset($leadsList['lead_page_show_postal'])) {
            $leadsList['lead_page_show_postal'] = 0;
        }
        if (!isset($leadsList['lead_page_phone_required'])) {
            $leadsList['lead_page_phone_required'] = 0;
        }
        if (!isset($leadsList['lead_page_dob_required'])) {
            $leadsList['lead_page_dob_required'] = 0;
        }
        if (!isset($leadsList['lead_page_postal_required'])) {
            $leadsList['lead_page_postal_required'] = 0;
        }
        if (!isset($leadsList['lead_page_referral_required'])) {
            $leadsList['lead_page_referral_required'] = 0;
        }
        if (!isset($leadsList['lead_page_preferred_comm_required'])) {
            $leadsList['lead_page_preferred_comm_required'] = 0;
        }
        if (!isset($leadsList['lead_page_message_required'])) {
            $leadsList['lead_page_message_required'] = 0;
        }


        if (!$leadsList['lead_page_show_postal']) {
            $leadsList['lead_page_postal_required'] = 0;
        }
        if (!$leadsList['lead_page_show_referral_source']) {
            $leadsList['lead_page_referral_required'] = 0;
        }
        if (!$leadsList['lead_page_show_dob']) {
            $leadsList['lead_page_dob_required'] = 0;
        }
        if (!$leadsList['lead_page_show_phone']) {
            $leadsList['lead_page_phone_required'] = 0;
        }
        if (!$leadsList['lead_page_show_referral_source']) {
            $leadsList['lead_page_referral_required'] = 0;
        }
        if (!$leadsList['lead_page_show_preferred_communication']) {
            $leadsList['lead_page_preferred_comm_required'] = 0;
        }
        if (!$leadsList['lead_page_allow_message']) {
            $leadsList['lead_page_message_required'] = 0;
        }

        $data['leads_list'] = $leadsList;
        $data['referral'] = $referral;
        return $data;
    }

    public static function check_page_slug_exist($slug) {
        global $wpdb;
        $raw = "SELECT `post_name` FROM `" . $wpdb->prefix . "posts` WHERE `post_name` = '" . $slug . "' and `post_type` = 'page'";
        $pages = $wpdb->get_row($raw, 'ARRAY_A');
        if ($pages) {
            return true;
        } else {
            return false;
        }
    }

    public function save_integration_page_status() {
        $pagesID = get_option('wp-pushpress-page-id');
        $arrayArgs = array('products' => 'product-enabled', 'plans' => 'plan-enabled', 'schedule' => 'schedule-enabled', 'workouts' => 'workout-enabled', 'leads' => 'lead-enabled');
        $result = array();
        foreach ($arrayArgs as $key => $arrayArg) {
            // feature check box available
            if (isset($_POST[$arrayArg]) && sanitize_text_field($_POST[$arrayArg]) == 'yes') {
                //enable feature option
                update_option('wp-pushpress-feature-' . $arrayArg, 'yes');
                //update post 
                $post = array(
                    'ID' => $pagesID[$key],
                    'post_status' => 'publish'
                );
                $result['result'] = wp_update_post($post);
                $result['status'] = 'yes';
            }

            if (isset($_POST[$arrayArg]) && sanitize_text_field($_POST[$arrayArg]) == 'no') {
                //disable feature option
                update_option('wp-pushpress-feature-' . $arrayArg, 'no');
                //update post 
                $post = array(
                    'ID' => $pagesID[$key],
                    'post_status' => 'private'
                );
                $result['result'] = wp_update_post($post);
                $result['status'] = 'no';
            }
        }
        echo json_encode($result);
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    // get plans, events, products categories by slug
    public function get_section() {
        $results = array();
        $section = sanitize_text_field($_POST['slSection']);

        switch ($section) {
            case 'plans':

                $item = $this->get_plans_for_help();

                break;

            case 'events':

                $item = $this->get_events_for_help();

                break;
            case 'products':

                $item = $this->get_categories_for_help();

                break;
        }


        if (count($item) > 0) {
            $i = 0;
            foreach ($item as $key => $value) {
                $result[$i]['id'] = $key;
                $result[$i]['name'] = $value;
                $i = $i + 1;
            }
        } else {
            $result['result'] = 0;
        }

        echo json_encode($result);
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    function event_exist($id) {
        $eventsList = array();
        try {
            $doy = date("z");
            $year = date("Y");
            if ($doy >= 365) {
                $doy = ($doy % 365);
                $year++;
            }
            $start = strtotime("today 00:00:00");
            $end = strtotime("today 00:00:00 +1 year"); // 1 year later
            $calendar = Pushpress_Calendar::all(array(
                        'active' => 1,
                        'type' => 'event',
                        'start_time' => $start,
                        'end_time' => $end
            ));
            foreach ($calendar->data as $item) {
                if (($item->doy) >= $doy) {
                    if ($item->uuid == $id) {
                        return TRUE;
                    }
                }
            }
        } catch (Exception $e) {
            return FALSE;
        }
        return FALSE;
    }

    public function plan_exist($id) {
        $plansList = array();
        try {

            $params = array(
                'active' => 1
            );
            //get all products object
            $plans = Pushpress_Plan::all($params);
            foreach ($plans->data as $plan) {
                if ($plan->slug == $id) {
                    return TRUE;
                }
            }
        } catch (Exception $e) {
            return FALSE;
        }
        return FALSE;
    }

    public function facebook_integrations() {
        try {
            $integration_settings = Pushpress_Client::settings('integration');
            $integration_settings = $integration_settings->data;
        }
        catch (Exception $e) {
            $integration_settings = array();
        }
        /*
        if( !$integration_settings = get_transient( 'pp_client_settings_integration' ) ){
            try {
                $integration_settings = Pushpress_Client::settings('integration');
            }
            catch (Exception $e) {
                $integration_settings = array();
            }
            set_transient( 'pp_client_settings_integration', $integration_settings, 600 ); // 10 minute cache
        }
        */

        
        $integrations = array();
        $i = array();

        foreach ($integration_settings as $item) {
            $integrations[$item->name] = $item->value;                
        }

        // $integrations['facebook_audience_pixel'] = base64_decode($integrations['facebook_audience_pixel']);
        $integrations['autopilot_tracking_code'] = base64_decode($integrations['autopilot_tracking_code']);
        
        if (!isset($integrations['facebook_pixel_id'])) { 
            $integrations['facebook_pixel_id'] = null;
        }
        if (!isset($integrations['ga_tracking_id'])) { 
            $integrations['ga_tracking_id'] = null;
        }
        
        return $integrations;
    }

    public function facebook_metrics() {

        try {
            $settingsObj = Pushpress_Client::settings('metrics');                
            $settingsObj = $settingsObj->data;
        }
        catch (Exception $e) {
            $settingsObj = array();
        }

        /*
        if( !$settingsObj = get_transient( 'pp_client_settings_metrics' ) ){
            try {
                $settingsObj = Pushpress_Client::settings('metrics');                
                $settingsObj = $settingsObj->data;
            }
            catch (Exception $e) {
                $settingsObj = array();
            }
            set_transient( 'pp_client_settings_metrics', $settingsObj, 60 ); // 1 minute cache
        }
        */

        $metrics = array();
        foreach ($settingsObj as $item) {
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

    public function get_staff() { 
        try {
            $staff = Pushpress_Customer::all(array("is_staff"=>1, "active"=>1));
            return $staff->data;
        }
        catch (Exception $e) { 
            return array();
        }
        
    }

    /*
    public function getStaff() { 
        try {
            return Pushpress_Customer::retrieve(array("is_staff"=>1));             
        }
        catch (Exception $e) { 
            return array();
        }
    }
    */

}