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

    public function __construct($config = [])
    {
        self::$ROOT_DIR = $config['root'];
        self::$app = $this;
        
        $this->config['app'] = $config['app'];
        $this->config['auth'] = $config['auth'];
        $this->config['connections'] = $config['connections'];
        $this->LoadSettings();
        $this->config = $this->ToObject($this->config);

        // $this->Debug($this);

        $this->user = null;
        $this->userClass = $config['userClass'];
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->database = new Database();
        $this->session = new Session($this->config->auth->defaults->session);
        $this->view = new View();
        $this->services = new ServiceProvider();

        $userId = Application::$app->session->GetAuth();
        if ($userId) {
            $this->user = $this->userClass::FindUser($userId);
        }
    }

    public function LoadSettings() {
        $dotenv = Dotenv::createImmutable(self::$ROOT_DIR);
        $env = $dotenv->load();
        $this->config['env'] = $this->ToObject($env);
    }

    public function Run()
    {
        //$this->triggerEvent(self::EVENT_BEFORE_REQUEST);
        try {
            echo $this->router->Resolve();
        } catch (\Exception $e) {
            $httpCodes = array(400, 401, 403, 404, 500);
            $errorCode = 500;
            if(in_array($e->getCode(), $httpCodes)) {
                $errorCode = $e->getCode();
            }
            Application::$app->response->StatusCode($errorCode);
            Application::$app->LogError($e->getCode(), $e->getMessage());
            echo $this->router->RenderView('layouts/error', [
                'message' => $e->getMessage(),
                'code' => $errorCode,
                'app' => Application::$app->config['app']
            ]);
        }
    }

    public function Debug($var) {
        echo '<pre>';
        print_r($var);
        echo '<pre>';
    }

    public function LogError($code, $message) {
        $db = new Database(Application::$app->config['env']->APP_ENV == 'local' ? 'ils-local' : 'ils-live');
        $db->InsertOne("exception_logs", ['code', 'message'], [':in_code' => $code, ':in_message' => $message]);
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