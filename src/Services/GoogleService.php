<?php

namespace InfinityBrackets\Services;

use InfinityBrackets\Core\Application;

class GoogleService
{
    public \Google\Client $client;

    public function __construct() {
        // Initialize Google Client Credentials
        $this->client = new \Google\Client();
        $google = Application::$app->config->auth->google;
        
        $this->client->setClientId($google->id);
        $this->client->setClientSecret($google->secret);
        $this->client->setRedirectUri($google->redirect);

        // Add Google Client Scope
        $this->client->addScope('profile');
        $this->client->addScope('email');

        // Create Google Login URL
        $google->url = $this->client->createAuthUrl();
    }
}