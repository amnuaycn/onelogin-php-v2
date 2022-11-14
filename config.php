<?php
// ONELOGIN //
$SUBDOMAIN = 'YOUR_SUBDOMAIN';
$CLIENT_ID = 'YOUR_CLIENT_ID';
$CLIENT_SECRET = 'YOUR_CLIENT_SECRET';
$SCOPES = 'openid email profile';
$REDIRECT_URI = 'http://localhost:8000/callback-oidc';//'YOUR_HOST_URL';
$POSTLOGOUT_URI = 'http://localhost:8000';//'YOUR_HOST_URL';
$URL_AUTHORIZE = 'https://'.$SUBDOMAIN.'.onelogin.com/oidc/2/auth';
$URL_ACCESS_TOKEN = 'https://'.$SUBDOMAIN.'.onelogin.com/oidc/2/token';
$URL_RESOURCE_OWNER_DETAILS = 'https://'.$SUBDOMAIN.'.onelogin.com/oidc/2/me';
$URL_LOGOUT = 'https://'.$SUBDOMAIN.'.onelogin.com/oidc/2/logout';


//DB
//$host = '127.0.0.1';
//$db   = 'dbtest';
////$user = 'root';
//$password = 'mypassword';
//$charset = 'utf8mb4';

?>
