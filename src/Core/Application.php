<?php

namespace InfinityBrackets\Core;

use InfinityBrackets\Core\ServiceProvider;
use Dotenv\Dotenv;

class Application {
    public static Application $app;
    public static string $ROOT_DIR;
    public string $layout = 'app';
    public ?Controller $controller = null;
    public $config = [];
    public $files = [];

    public function __construct($config = [])
    {
        $this->LoadSettings($config);
        date_default_timezone_set($this->config->env->APP_TIMEZONE ?? date_default_timezone_get());
        self::$app = $this;

        $this->user = NULL;
        $this->guest = NULL;
        $this->request = new Request();
        $this->response = new Response();
        $this->storage = new FileStorage();
        $this->router = new Router($this->request, $this->response);
        $this->database = new Database();
        $this->session = new Session($this->config->auth->defaults->session);
        $this->view = new View();
        $this->logger = new Logger();
        $this->helpers = $this->ToObject([
            'StringHelper' => '\InfinityBrackets\Helpers\StringHelper'
        ]);
        $this->providers = $this->ToObject(
            array_merge([
                'GoogleService' => '\InfinityBrackets\Services\GoogleService',
                'MasterService' => '\InfinityBrackets\Services\MasterService'
            ], (array)$this->config->providers)
        );

        $userId = Application::$app->session->GetAuth();
        if ($userId) {
            $this->user = $this->userClass::FindUser($userId);
        } else {
            $guestUserId = Application::$app->session->GetAuthGuest();
            if($guestUserId) {
                $this->guest = $this->guestClass::FindGuestUser($guestUserId);
            }
        }
    }

    /**
     * Load configuration on config folder
     * TODO: Add checking on $config keys before assigning 
     */
    public function LoadSettings($config) {
        self::$ROOT_DIR = $config['root'];
        $this->userClass = $config['auth']['userClass'];
        $this->guestClass = $config['auth']['guestClass'];
        $this->config = $config;

        // Transform config type (Array) to (Object)
        $this->config = $this->ToObject($this->config);
    }

    public function Run()
    {
        ////$this->triggerEvent(self::EVENT_BEFORE_REQUEST);
        try {
            echo $this->router->Resolve();
        } catch (\Exception $e) {
            $httpCodes = array(400, 401, 403, 404, 500);
            $errorCode = 500;
            if(in_array($e->getCode(), $httpCodes)) {
                $errorCode = $e->getCode();
            }
            Application::$app->response->StatusCode($errorCode);
            Application::$app->logger->Error($e->getCode(), $e->getMessage());
            
            echo $this->router->RenderView('layouts/error', [
                'message' => $e->getMessage(),
                'code' => $errorCode,
                'app' => Application::$app->config->app
            ]);
        }
    }

    public function Debug($var) {
        echo '<pre>';
        print_r($var);
        echo '<pre>';
    }

    public function ToObject($array) {
        return json_decode(json_encode($array));
    }

    public function ToJSON($data) {
        if(is_string($data)) {
            $data = array('data' => $data);
        }
        echo json_encode($data);
    }
}