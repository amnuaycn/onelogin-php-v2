<?php
require_once __DIR__.'/vendor/autoload.php';
use \League\OAuth2\Client\Provider\GenericProvider;
class Onelogin extends GenericProvider {
    private $name ='onelogin';
    private $httpProxy = '';
    private $timeOut = 60;

    public function Logout($url,$redirect_url,$id_token)
    {
       session_unset();
       $this->clearCookies();
       header('location: '.$url.'?post_logout_redirect_uri='.$redirect_url.'&id_token_hint='.$id_token);
    }
    
    public function revokeToken($token, $token_type_hint, $clientId = null, $clientSecret = null) {
        session_unset();
        $revocation_endpoint = $this->getAccessTokenUrl([]) . '/revocation';
        $clientId = $clientId !== null ? $clientId : $this->clientId;
        $clientSecret = $clientSecret !== null ? $clientSecret : $this->clientSecret;

        $post_data = ['token' => $token];
        $post_data['token_type_hint'] = $token_type_hint;
        $post_data['client_id'] = $clientId;
        $post_data['client_secret'] = $clientSecret;

        // Convert token params to string format
        $post_params = http_build_query($post_data, '', '&');
        $headers = ['Accept: application/json'];

        return json_decode($this->fetchURL($revocation_endpoint, $post_params, $headers));
    }

   public function clearCookies($clearSession = false)
    {
        $past = time() - 3600;
        if ($clearSession === false)
            $sessionId = session_id();
        foreach ($_COOKIE as $key => $value)
        {
            if ($clearSession !== false || $value !== $sessionId)
                setcookie($key, $value, $past, '/');
        }
    }

    public function saveLocalIdToken($key,$value){
        setcookie($key.'_'.$this->name, $value, time() + (86400 * 30), "/");
    }

    public function getSessionUser(){
        if(!empty($_COOKIE[$this->name.'s_user']))
            return $_COOKIE[$this->name.'s_user'];
        else 
            return '';
    }
    public function setSessionUser($value){
        setcookie($this->name.'s_user', $value, time() + (86400 * 30), "/");
    }
    public function isActive(){
        try {
            if(isset($_SESSION['token'])){
                $token = unserialize($_SESSION['token']);
                if(time() >= $token->getExpires())
                    return false;
                else 
                    return true;
            }else {
               $uuid_user =  $this->getSessionUser();
               if(!isset($uuid_user) || empty($uuid_user)) {
                    return false;
               }else{
                    try{ 
                       
                        $newAccessToken = $this->getAccessToken('refresh_token', [
                            'refresh_token' => $uuid_user
                        ]);
                        $_SESSION['token'] = serialize($newAccessToken);
                        return true;
                    } catch (Exception $e) {
                        // Failed to get the access token or user details.
                        return false;
                    }
                   
            }
            }
       } catch (e) {
             return false;
       }
    }

    protected function fetchURL($url, $post_body = null, $headers = []) {

        // OK cool - then let's create a new cURL resource handle
        $ch = curl_init();

        // Determine whether this is a GET or POST
        if ($post_body !== null) {
            // curl_setopt($ch, CURLOPT_POST, 1);
            // Alows to keep the POST method even after redirect
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);

            // Default content type is form encoded
            $content_type = 'application/x-www-form-urlencoded';

            // Determine if this is a JSON payload and add the appropriate content type
            if (is_object(json_decode($post_body))) {
                $content_type = 'application/json';
            }

            // Add POST-specific headers
            $headers[] = "Content-Type: {$content_type}";

        }

        // If we set some headers include them
        if(count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $url);

        if (isset($this->httpProxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $this->httpProxy);
        }

        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // Allows to follow redirect
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

       
        // Should cURL return or print out the data? (true = return, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);

        // Download the given URL, and return output
        $output = curl_exec($ch);

        // HTTP Response code from server may be required from subclass
        $info = curl_getinfo($ch);
        $this->responseCode = $info['http_code'];

        if ($output === false) {
            throw new Exception('Curl error: (' . curl_errno($ch) . ') ' . curl_error($ch));
        }

        // Close the cURL resource, and free system resources
        curl_close($ch);

        return $output;
    }

}