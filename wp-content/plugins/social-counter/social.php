<?php
class Social{
function get_tweets($url) {
$json_string = $this->file_get_contents_curl('http://urls.api.twitter.com/1/urls/count.json?url=' . $url);
$json = json_decode($json_string, true);
return isset($json['count'])?intval($json['count']):0;
}
function get_linkedin($url) {
$json_string = $this->file_get_contents_curl('http://www.linkedin.com/countserv/count/share?url='.$url.'&format=json');
$json = json_decode($json_string, true);
return isset($json['count'])?intval($json['count']):0;
}
function get_fb($url) {
$json_string = $this->file_get_contents_curl('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls='.$url);
$json = json_decode($json_string, true);
return isset($json[0]['total_count'])?intval($json[0]['total_count']):0;
}
function get_reddit($url) {
$json_string = $this->file_get_contents_curl('http://www.reddit.com/api/info.json?&url='.$url);
$json = json_decode($json_string,true);
return isset($json['data']['children'][0]['data']['score'])?$json['data']['children'][0]['data']['score']:'0';
}
function get_stumble($url) {
$json_string = $this->file_get_contents_curl('http://www.stumbleupon.com/services/1.01/badge.getinfo?url='.$url);
$json = json_decode($json_string, true);
return isset($json['result']['views'])?intval($json['result']['views']):0;
}
function get_delicious($url) {
$json_string = $this->file_get_contents_curl('http://feeds.delicious.com/v2/json/urlinfo/data?url='.$url);
$json = json_decode($json_string, true);
return isset($json[0]['total_posts'])?intval($json[0]['total_posts']):0;
}
function get_pinterest($url) {
$return_data = $this->file_get_contents_curl('http://api.pinterest.com/v1/urls/count.json?url='.$url);
$json_string = preg_replace('/^receiveCount((.*))$/', "\1", $return_data);
$json = json_decode($json_string, true);
return isset($json['count'])?intval($json['count']):0;
}
function get_plusones($url)  {
$return_data = $this->file_get_contents_curl('https://clients6.google.com/rpc',$url);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "");
$json = json_decode($return_data, true);
return isset($json[0]['result']['metadata']['globalCounts']['count'])?intval( $json[0]['result']['metadata']['globalCounts']['count'] ):0;
}
function file_get_contents_curl($url,$google_url = null){
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
if($google_url != null){
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.rawurldecode($google_url).'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
}
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$cont = curl_exec($ch);
if(curl_error($ch))
{
die(curl_error($ch));
}
return $cont;
}
}