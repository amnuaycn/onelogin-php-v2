<?php
$provider = require __DIR__ . '/provider.php';
if(!$provider->isActive()){
    header('Location: /index.php');
}
$token = unserialize($_SESSION['token']);

if (isset($_GET['logout']) && $_GET['logout'] = 1) {
    $id_token = $token->getvalues()['id_token'];
    $provider->Logout($URL_LOGOUT,$POSTLOGOUT_URI,$id_token);
    exit;
}
if (isset($_GET['refreshToken']) && $_GET['refreshToken'] = 1) {
    $accessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $token->getRefreshToken()
    ]);
    $_SESSION['token'] = serialize($accessToken);
}
if (isset($_GET['refreshToken']) && $_GET['refreshToken'] = 1) {
    $accessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $token->getRefreshToken()
    ]);
    $_SESSION['token'] = serialize($accessToken);
}
if (isset($_GET['revokeToken']) && $_GET['revokeToken'] = 1) {
    $status = $provider->revokeToken($token->getRefreshToken(),'refresh_token');
    if(empty($status)) {
        header('Location: /index.php');
    }
    var_dump($status);
}

try {
    // We got an access token, let's now get the user's details
    $resourceOwner = $provider->getResourceOwner($token);
		$rsw = $resourceOwner->toArray();
        echo '<h3>UserInfo</h3>';
        foreach($rsw as $x => $val) {
            echo "$x = $val<br>";
        }
        echo "---------------------------";
    	echo "<br>";
    // Use these details to create a new profile
} catch (Exception $e) {
    // Failed to get user details
    exit('Something went wrong: ' . $e->getMessage());
}

// Use this to interact with an API on the users behalf
echo "<b>Access token is:</b> <tt>", $token->getToken(), "</tt><br/>";

// Use this to get a new access token if the old one expires
echo "<b>Refresh token is:</b> <tt>", $token->getRefreshToken(), "</tt><br/>";

// Number of seconds until the access token will expire, and need refreshing
echo "<b>Expires at </b>", date('r', $token->getExpires()), "<br/>";

echo '<a href="?refreshToken=1">RefreshToken</a>|';
echo '<a href="?revokeToken=1">revokeToken</a>|';
// Allow the user to logout
echo '<a href="?logout=1">Logout</a><br/>';