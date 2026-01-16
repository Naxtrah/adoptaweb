<?php
require_once 'config.php';

class GoogleAuth {
    private $client;
    private $redirect_uri;
    
    public function __construct() {
        $this->client = new Google\Client();
        $this->client->setClientId('TU_CLIENT_ID.apps.googleusercontent.com');
        $this->client->setClientSecret('TU_CLIENT_SECRET');
        $this->client->setRedirectUri(BASE_URL . '/auth/google-callback.php');
        $this->client->addScope('email');
        $this->client->addScope('profile');
        
        $this->redirect_uri = BASE_URL . '/auth/google-callback.php';
    }
    
    public function getAuthUrl() {
        return $this->client->createAuthUrl();
    }
    
    public function authenticate($code) {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($token);
            
            $oauth2 = new Google\Service\Oauth2($this->client);
            $user_info = $oauth2->userinfo->get();
            
            return [
                'email' => $user_info->email,
                'nombre' => $user_info->givenName,
                'apellido' => $user_info->familyName,
                'google_id' => $user_info->id,
                'avatar' => $user_info->picture
            ];
        } catch (Exception $e) {
            error_log('Google Auth Error: ' . $e->getMessage());
            return false;
        }
    }
}