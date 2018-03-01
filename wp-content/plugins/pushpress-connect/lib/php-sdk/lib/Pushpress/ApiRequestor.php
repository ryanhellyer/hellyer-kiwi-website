<?php

class Pushpress_ApiRequestor
{
  public $apiKey;

  public function __construct($apiKey=null)
  {
    $this->_apiKey = $apiKey;
  }

  public static function apiUrl($url='')
  {
    $apiBase = PushpressApi::$apiBase;
    return "$apiBase$url";
  }

  public static function utf8($value)
  {
    if (is_string($value) && mb_detect_encoding($value, "UTF-8", TRUE) != "UTF-8")
      return utf8_encode($value);
    else
      return $value;
  }

  private static function _encodeObjects($d)
  {
    if ($d instanceof Pushpress_ApiResource) {
      return self::utf8($d->id);
    } else if ($d === true) {
      return 'true';
    } else if ($d === false) {
      return 'false';
    } else if (is_array($d)) {
      $res = array();
      foreach ($d as $k => $v)
      	$res[$k] = self::_encodeObjects($v);
      return $res;
    } else {
      return self::utf8($d);
    }
  }

  public static function encode($arr, $prefix=null)
  {
    if (!is_array($arr))
      return $arr;

    $r = array();
    foreach ($arr as $k => $v) {
      if (is_null($v))
        continue;

      if ($prefix && $k && !is_int($k))
        $k = $prefix."[".$k."]";
      else if ($prefix)
        $k = $prefix."[]";

      if (is_array($v)) {
        $r[] = self::encode($v, $k, true);
      } else {
        $r[] = urlencode($k)."=".urlencode($v);
      }
    }

    return implode("&", $r);
  }

  public function request($meth, $url, $params=null)
  {
      
    if (!$params)
      $params = array();
    list($rbody, $rcode, $myApiKey) = $this->_requestRaw($meth, $url, $params);
    $resp = $this->_interpretResponse($rbody, $rcode);
    return array($resp, $myApiKey);
  }

  public function handleApiError($rbody, $rcode, $resp)
  {
       
    if (!is_array($resp) || !isset($resp['error']))
      throw new Pushpress_ApiError("There was an error: $rbody", $rcode, $rbody, $resp);
    $error = $resp['error'];
    switch ($rcode) {
    case 400:
    case 404:
      throw new Pushpress_InvalidRequestError(isset($error['message']) ? $error['message'] : null,
                                           isset($error['param']) ? $error['param'] : null,
                                           $rcode, $rbody, $resp);
    case 401:
      throw new Pushpress_AuthenticationError(isset($error['message']) ? $error['message'] : null, $rcode, $rbody, $resp);
    case 402:
      throw new Pushpress_RequestFailedError(isset($error['message']) ? $error['message'] : null,
                                 isset($error['param']) ? $error['param'] : null,
                                 isset($error['code']) ? $error['code'] : null,
                                 $rcode, $rbody, $resp);
    default:
      throw new Pushpress_ApiError(isset($error['message']) ? $error['message'] : null, $rcode, $rbody, $resp);
    }
  }

  private function _requestRaw($meth, $url, $params)
  {      
    $myApiKey = $this->_apiKey;
    if (!$myApiKey)
      $myApiKey = PushpressApi::$apiKey;
    


    // if (!$myApiKey)
    // allow no api key requests
    //  throw new Pushpress_AuthenticationError('No API key provided.  (HINT: set your API key using "PushpressApi::setApiKey(<API-KEY>)".  You can generate API keys from the Pushpress web interface.  See https://pushpress.com/api for details, or email support@pushpress.com if you have any questions.');

    
    $absUrl = $this->apiUrl($url);
    
    $params = self::_encodeObjects($params);
    $langVersion = phpversion();
    $uname = php_uname();
    $ua = array('bindings_version' => PushpressApi::VERSION,
		'lang' => 'php',
		'lang_version' => $langVersion,
		'publisher' => 'pushpress',
		'uname' => $uname);

    $user_agent =  "Pushpress/v1:" . (array_key_exists('PP-APP-ID', $_SERVER) ? $_SERVER['PP-APP-ID'] : 'Generic PhpBindings' );

    $headers = array('X-Pushpress-Client-User-Agent: ' . json_encode($ua),        
		    'User-Agent: ' . $user_agent);

    if (strlen($myApiKey)) { 
      $headers[] = 'Authorization: Basic ' . base64_encode($myApiKey);
    }
        
    
    if (PushpressApi::$apiVersion)
      $headers[] = 'Pushpress-Version: ' . PushpressApi::$apiVersion;

    $headers[] = 'X-FORWARDED-FOR: ' . $_SERVER['REMOTE_ADDR'];

    list($rbody, $rcode) = $this->_curlRequest($meth, $absUrl, $headers, $params);
    return array($rbody, $rcode, $myApiKey);
  }

  private function _interpretResponse($rbody, $rcode)
  {     
    
    $rbody = str_replace("\&", "&", $rbody);

    try {
      $resp = json_decode($rbody, true);
    } catch (Exception $e) {        
      throw new Pushpress_ApiError("Invalid response body from API: $rbody (HTTP response code was $rcode)", $rcode, $rbody);
    }

    if (!$resp) {
        $resp = json_decode($rbody, true);
    }
    
    if ($rcode < 200 || $rcode >= 300) {        
      $this->handleApiError($rbody, $rcode, $resp);
    }
    return $resp;
  }

  private function _curlRequest($meth, $absUrl, $headers, $params)
  {
    $curl = curl_init();
    $meth = strtolower($meth);
    $opts = array();
    
    if ($meth == 'get') {
      $opts[CURLOPT_HTTPGET] = 1;
      if (count($params) > 0) {
	$encoded = self::encode($params);
	$absUrl = "$absUrl?$encoded";
      }
    } else if ($meth == 'post') {
      $opts[CURLOPT_POST] = 1;
      $opts[CURLOPT_POSTFIELDS] = self::encode($params);
    }
    else if ($meth == 'patch') {
        $opts[CURLOPT_CUSTOMREQUEST] = 'PATCH'; 
        $opts[CURLOPT_POSTFIELDS] = self::encode($params);       
    } 
    else if ($meth == 'put') {
      $opts[CURLOPT_PUT] = 1;
      $opts[CURLOPT_POSTFIELDS] = self::encode($params);
    } 
    else if ($meth == 'delete')  {
      $opts[CURLOPT_CUSTOMREQUEST] = "DELETE"; 
      $opts[CURLOPT_POSTFIELDS] = self::encode($params);
      
      if (count($params) > 0) {
	       $encoded = self::encode($params);
	       $absUrl = "$absUrl?$encoded";
        }
    } else {
      throw new Pushpress_ApiError("Unrecognized method $meth");
    }

    $absUrl = self::utf8($absUrl);
    
    $opts[CURLOPT_URL] = $absUrl;
    $opts[CURLOPT_RETURNTRANSFER] = true;
    $opts[CURLOPT_CONNECTTIMEOUT] = 30;
    $opts[CURLOPT_TIMEOUT] = 80;
    $opts[CURLOPT_RETURNTRANSFER] = true;
    $opts[CURLOPT_HTTPHEADER] = $headers;
    if (!PushpressApi::$verifySslCerts)
      $opts[CURLOPT_SSL_VERIFYPEER] = false;

    curl_setopt_array($curl, $opts);
    $rbody = curl_exec($curl);
    $errno = curl_errno($curl);
    
    if ($errno == CURLE_SSL_CACERT ||
	$errno == CURLE_SSL_PEER_CERTIFICATE ||
	$errno == 77 // CURLE_SSL_CACERT_BADFILE (constant not defined in PHP though)
	) {
      array_push($headers, 'X-Pushpress-Client-Info: {"ca":"using Pushpress-supplied CA bundle"}');
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_CAINFO,
                  dirname(__FILE__) . '/../data/ca-certificates.crt');
      $rbody = curl_exec($curl);      
    }

    
    
    
    if ($rbody === false) {
      $errno = curl_errno($curl);
      $message = curl_error($curl);
      curl_close($curl);
      $this->handleCurlError($errno, $message);
    }

    
    
    $rcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return array($rbody, $rcode);
  }

  public function handleCurlError($errno, $message)
  {
    $apiBase = PushpressApi::$apiBase;
    switch ($errno) {
    case CURLE_COULDNT_CONNECT:
    case CURLE_COULDNT_RESOLVE_HOST:
    case CURLE_OPERATION_TIMEOUTED:
      $msg = "Could not connect to Pushpress ($apiBase).  Please check your internet connection and try again.  If this problem persists, you should check Pushpress's service status at https://twitter.com/pushpressstatus, or let us know at support@pushpress.com.";
      break;
    case CURLE_SSL_CACERT:
    case CURLE_SSL_PEER_CERTIFICATE:
      $msg = "Could not verify Pushpress's SSL certificate.  Please make sure that your network is not intercepting certificates.  (Try going to $apiBase in your browser.)  If this problem persists, let us know at support@pushpress.com.";
      break;
    default:
      $msg = "Unexpected error communicating with Pushpress.  If this problem persists, let us know at support@pushpress.com.";
    }

    $msg .= "\n\n(Network error [errno $errno]: $message)";
    throw new Pushpress_ApiConnectionError($msg);
  }
}
