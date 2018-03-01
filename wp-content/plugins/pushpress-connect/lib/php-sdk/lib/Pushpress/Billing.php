<?php

class Pushpress_Billing extends Pushpress_ApiResource
{
  public static function constructFrom($values, $apiKey=null)
  {
    $class = get_class();
    return self::scopedConstructFrom($class, $values, $apiKey);
  }

  public static function retrieve($id, $apiKey=null)
  {
    $class = get_class();
    return self::_scopedRetrieve($class, $id, $apiKey);
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
  
  public function CreateWaiver($params, $apiKey) {
      var_dump($params);      
  }
  
  public static function retrieveAch($params=null, $apiKey=null) {    
  
        $class = get_class();
        $url = self::classUrl($class);
        $url .=  "/" . $params['user_id'] . "/ach";
        
        $requestor = new Pushpress_ApiRequestor();
        list($response, $apiKey) = $requestor->request('get', $url, $params);
        return Pushpress_Util::convertToPushpressObject($response, $apiKey);                
  }  

  public static function linkPlaidAch($params=null, $apiKey=null) {    
    
        $class = get_class();
        $url = self::classUrl($class);
        $url .= "/ach/plaid";

        $requestor = new Pushpress_ApiRequestor();
        list($response, $apiKey) = $requestor->request('post', $url, $params);
        return Pushpress_Util::convertToPushpressObject($response, $apiKey);                
  }  


  public static function verifyAch($params=null, $apiKey=null) {    
  
        $class = get_class();
        $url = self::classUrl($class);
        $url .= "/ach";
        $requestor = new Pushpress_ApiRequestor();
        list($response, $apiKey) = $requestor->request('post', $url, $params);
        return Pushpress_Util::convertToPushpressObject($response, $apiKey);                
  }  

  

  public static function all($params=null, $apiKey=null)
  {
    $class = get_class();
    return self::_scopedAll($class, $params, $apiKey);
  }
}
