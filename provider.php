<?php
require_once __DIR__.'/onelogin.php';
require_once __DIR__.'/config.php';
session_start();
$provider = new Onelogin([
	'scopes' 				  => $SCOPES,
    'clientId'                => $CLIENT_ID,    // The client ID assigned to you by the provider
    'clientSecret'            => $CLIENT_SECRET,    // The client password assigned to you by the provider
    'redirectUri'             => $REDIRECT_URI, // 'https://' . $_SERVER['HTTP_HOST'],
    'urlAuthorize'            => $URL_AUTHORIZE, //'https://example.onelogin.com/oidc/2/auth',
    'urlAccessToken'          => $URL_ACCESS_TOKEN, //'https://example.onelogin.com/oidc/2/token',
    'urlResourceOwnerDetails' => $URL_RESOURCE_OWNER_DETAILS //'https://example.onelogin.com/oidc/2/me'
]);



return $provider;