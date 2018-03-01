<?php

abstract class Pushpress_Util
{
  public static function isList($array)
  {
    if (!is_array($array))
      return false;

    // TODO: this isn't actually correct in general, but it's correct given Pushpress's responses
    foreach (array_keys($array) as $k) {
      if (!is_numeric($k))
        return false;
    }
    return true;
  }

  public static function convertPushpressObjectToArray($values)
  {
    $results = array();
    foreach ($values as $k => $v) {
      // FIXME: this is an encapsulation violation
      if ($k[0] == '_') {
        continue;
      }
      if ($v instanceof Pushpress_Object) {
        $results[$k] = $v->__toArray(true);
      }
      else if (is_array($v)) {
        $results[$k] = self::convertPushpressObjectToArray($v);
      }
      else {
        $results[$k] = $v;
      }
    }
    return $results;
  }

  public static function convertToPushpressObject($resp, $apiKey)
  {
    //var_dump($resp);
    $types = array(
      'application' => 'Pushpress_App',
      'billing' => 'Pushpress_Billing',
      'calendar' => 'Pushpress_Calendar',
      'checkin' => 'Pushpress_Checkin',
      'client' => 'Pushpress_Client',
      'contract' => 'Pushpress_Contract',
      'customer' => 'Pushpress_Customer',
      'document' => 'Pushpress_Document',      
      'discount' => 'Pushpress_Discount',      
      'list' => 'Pushpress_List',
      'invoice' => 'Pushpress_Invoice',
      'invoice_item' => 'Pushpress_InvoiceItem',
      'message' => 'Pushpress_Message',
      'order' => 'Pushpress_Order',
      'payrate' => 'Pushpress_PayRate',
      'plan' => 'Pushpress_Plan',
      'preorder' => 'Pushpress_Preorder',
      'product' => 'Pushpress_Product',
      'product_category' => 'Pushpress_ProductCategories',
      'product_image' => 'Pushpress_ProductImages',
      'registration' => 'Pushpress_Registration',
      'scheduler_credit' => 'Pushpress_Scheduler_Credit',
      'scheduler_type' => 'Pushpress_Scheduler_Type',
      'scheduler_rate' => 'Pushpress_Scheduler_Rate',
      'subscription_contract' => 'Pushpress_SubscriptionContracts',
      'subscription' => 'Pushpress_Subscriptions',
      'sub_pay' => 'Pushpress_Subscription_Payroll',
      'tax_rate' => 'Pushpress_TaxRates',
      'token' => 'Pushpress_Token',
      'track' => 'Pushpress_Track',
      'track_benchmark' => 'Pushpress_Track_Benchmark',
      'track_benchmark_result' => 'Pushpress_Track_Benchmark_Result',
      'track_favorite_workout' => 'Pushpress_Track_Favorite_Workout',
      'track_score_type' => 'Pushpress_Track_Score_Type',
      'track_workout' => 'Pushpress_Track_Workout',
      'track_workout_type' => 'Pushpress_Track_Workout_Type',
      'user' => 'Pushpress_User',
      'user_document' => 'Pushpress_UserDocument',
      'user_image' => 'Pushpress_UserImage',
      'user_waiver' => 'Pushpress_UserWaiver',
    );
    if (self::isList($resp)) {
      $mapped = array();
      foreach ($resp as $i)
        array_push($mapped, self::convertToPushpressObject($i, $apiKey));
      return $mapped;
    } else if (is_array($resp)) {
      if (isset($resp['object']) && is_string($resp['object']) && isset($types[$resp['object']]))
        $class = $types[$resp['object']];
      else
        $class = 'Pushpress_Object';
      return Pushpress_Object::scopedConstructFrom($class, $resp, $apiKey);
    } else {
      return $resp;
    }
  }
}
