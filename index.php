<?php
$provider = require __DIR__ . '/provider.php';
if($provider->isActive()){
    header('Location: users.php');
}
else {
    $authUrl = $provider->getAuthorizationUrl();
    echo " You not authorization please " . "<a href='".$authUrl."'>Login</a>";
}

?>