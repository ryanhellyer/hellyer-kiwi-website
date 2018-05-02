<?php

class Pushpress_Scheduler_Credit extends Pushpress_ApiResource
{
  public static function classUrl($class=null) {
    return "/v1/scheduler/credit";
  }
    
  public static function constructFrom($values, $apiKey=null)
  {
    $class = get_class();
    return self::scopedConstructFrom($class, $values, $apiKey);
  }
  
  public static function active($apiKey=null)
  {
    $class = get_class();
    return self::_scopedActive($class, $apiKey);
  }
  
  public static function inactive($apiKey=null)
  {
    $class = get_class();
    return self::_scopedInactive($class, $apiKey);
  }

  public static function retrieve($id=null, $apiKey=null)
  {
        
    $class = get_class();
    return self::_scopedRetrieve($class, $id, $apiKey);
  }
  
  public static function retrieveEmpty($id=null, $apiKey=null)
  {      
    $class = get_class();
    return self::_scopedRetrieveEmpty($class, $id, $apiKey);
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

  public static function checkin($id, $source=null) {
        $class = get_class();
        $url = self::classUrl($class);
        $url .= "/" . $id . "/checkin";

        $params = array(
          "source" => $source
        );
        
        $requestor = new Pushpress_ApiRequestor();
      
        list($response, $apiKey) = $requestor->request('post', $url, $params);
        return self::scopedConstructFrom($class, $response, $apiKey);
        
    }

    public static function deleteCheckin($id) {
        $class = get_class();
        $url = self::classUrl($class);
        $url .= "/" . $id . "/checkin";

        $params = array(
          "source" => $source
        );
        
        $requestor = new Pushpress_ApiRequestor();
      
        list($response, $apiKey) = $requestor->request('delete', $url);
        return self::scopedConstructFrom($class, $response, $apiKey);
        
    }
    
}
