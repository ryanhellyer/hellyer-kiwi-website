<?php

class Pushpress_PayrollItem extends Pushpress_ApiResource
{
  public static function classUrl($class=null) {
        return "/v1/payroll/item";
  }
    
  public static function constructFrom($values, $apiKey=null)
  {
    $class = get_class();
    return self::scopedConstructFrom($class, $values, $apiKey);
  }

  public static function retrieve($id, $params=null, $apiKey=null)
  {
    $class = get_class();
    return self::_scopedRetrieve($class, $id, $apiKey, $params);
  }

  public static function create($params=null, $apiKey=null)
  {
    $class = get_class();
    return self::_scopedCreate($class, $params, $apiKey);
  }

  public function delete($params=null)
  {
    $class = get_class();
    return self::_scopedDelete($class, $params);
  }
  
  public function save()
  {
    $class = get_class();
    return self::_scopedSave($class);
  }
  
  public static function all($params=null, $apiKey=null)
  {
    $class = get_class();
    return self::_scopedAll($class, $params, $apiKey);
  }
  

  public static function summary($params=null)
  {
    
    $class = get_class();
    // self::_validateCall('all', $params, $apiKey);
    $requestor = new Pushpress_ApiRequestor($apiKey);
    
    $url = "/v1/payroll/summary";

    list($response, $apiKey) = $requestor->request('get', $url, $params);
    return Pushpress_Util::convertToPushpressObject($response, $apiKey);
  }
   
}
