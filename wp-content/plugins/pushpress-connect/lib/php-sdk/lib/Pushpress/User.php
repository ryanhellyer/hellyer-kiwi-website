<?php

class Pushpress_User extends Pushpress_ApiResource
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
  
  public static function retrieveEmpty($id=null, $apiKey=null)
  {      
    $class = get_class();
    return self::_scopedRetrieveEmpty($class, $id, $apiKey);
  }
  
  
  public function CreateWaiver($params, $apiKey) {
      
  }
  
  public static function all($params=null, $apiKey=null)
  {
    $class = get_class();
    return self::_scopedAll($class, $params, $apiKey);
  }

  public static function ping() { 
    $class = get_class();
    $url = self::classUrl($class);
    $url .= "/ping";
    $requestor = new Pushpress_ApiRequestor();
        
    list($response, $apiKey) = $requestor->request('get', $url, $params);
    return self::scopedConstructFrom($class, $response, $apiKey);

  }
  
    public static function auth($params=null) {
        $class = get_class();
        $url = self::classUrl($class);
        
        $url .= "/auth";
        $requestor = new Pushpress_ApiRequestor();
        
        list($response, $apiKey) = $requestor->request('post', $url, $params);
        return self::scopedConstructFrom($class, $response, $apiKey);
    }
    
    public static function resetPin($params=null) {
        $class = get_class();
        $url = self::classUrl($class);

        $p = array();

        if (!is_array($params)) { 
          $p['email'] = $params;          
        }
        else { 
          $p = $params;
        }
        //$params['email'] = $email;
        
        $url .= "/pinreset/";
      

        $requestor = new Pushpress_ApiRequestor();
        
        list($response, $apiKey) = $requestor->request('post', $url, $p);
        return self::scopedConstructFrom($class, $response, $apiKey);
    }
    
    
    public function setClient($params=null) {
        $class = get_class();
        $url = self::instanceUrl();
        $url .= "/auth/client";
        $requestor = new Pushpress_ApiRequestor();
      
        list($response, $apiKey) = $requestor->request('post', $url, $params);
        $this->refreshFrom($response, $apiKey);
        return $this;
    }
    
    
}
