<?php
$provider = require '../provider.php';
$gotoURL = 'users.php';

if (!isset($_GET['code'])) {
    if($provider->isActive()) {
        header('Location: '.$gotoURL);
        exit; 
    }
     // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: ' . $authUrl);
    exit;
}  elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
    if (isset($_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
    }
    exit('Invalid state');
}  else {
       try {
        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
        $provider->setSessionUser($accessToken->getRefreshToken());
        $_SESSION['token'] = serialize($accessToken);
        header('Location: ' . $gotoURL);
        exit;
      } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        // Failed to get the access token or user details.
        exit($e->getMessage());
    }

}

