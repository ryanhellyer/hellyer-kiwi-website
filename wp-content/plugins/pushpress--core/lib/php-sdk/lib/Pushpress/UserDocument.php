<?php

class Pushpress_UserDocument extends Pushpress_ApiResource
{
  public static function classUrl($class=null) {
    return "/v1/userdocuments";
  }
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
  
  public static function all($params=null, $apiKey=null)
  {
    $class = get_class();
    return self::_scopedAll($class, $params, $apiKey);
  }

  public static function sign($id, $params) {
        $class = get_class();
        $url = self::classUrl($class);
        $url .= "/" . $id . "/sign";

        $requestor = new Pushpress_ApiRequestor();
      
        list($response, $apiKey) = $requestor->request('post', $url, $params);
        return self::scopedConstructFrom($class, $response, $apiKey);
        
    }

    public static function revoke($id, $params) {
        $class = get_class();
        
        $url = self::classUrl($class);
        $url .= "/" . $id . "/revoke";

        $requestor = new Pushpress_ApiRequestor();
        
      
        list($response, $apiKey) = $requestor->request('get', $url, $params);
        return self::scopedConstructFrom($class, $response, $apiKey);
        
    }
}
