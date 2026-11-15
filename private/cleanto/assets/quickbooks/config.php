<?php
/*include(dirname(dirname(dirname(__FILE__)))."/admin/header.php");

$database=new cleanto_db();
$setting=new cleanto_setting();
$conn=$database->connect();
$database->conn=$conn;
$setting->conn=$conn;

*/
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { 
    $protocol = 'https';
} else { 
    $protocol = 'http';
}

$cur_dirname = basename(dirname(dirname(__DIR__)));
//print_r($cur_dirname);
if($cur_dirname=='public_html'){
  $cur_dirname = '';
  $cur_dir = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], chr(0))).$cur_dirname."/";
} else {
  $cur_dir = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], $cur_dirname)).$cur_dirname."/";
}

$SITE_URL = $protocol.'://'.$_SERVER['HTTP_HOST'].$cur_dir;

$redirect_uri = $protocol.'://'.$_SERVER['HTTP_HOST'].'/'.$cur_dirname;

return array(
  'authorizationRequestUrl' => 'https://appcenter.intuit.com/connect/oauth2',
  'tokenEndPointUrl' => 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
  'client_id' => '',
  'client_secret' => '',
  'oauth_scope' => 'com.intuit.quickbooks.accounting openid profile email phone address',
  'oauth_redirect_uri' => $redirect_uri.'/assets/quickbooks/callback.php',
)
?>
