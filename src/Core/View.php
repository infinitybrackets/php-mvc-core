<?php

namespace InfinityBrackets\Core;

use InfinityBrackets\Core\Application;

class View
{
    private string $rootDirectory = '';
    public Application $app;
    public string $title = '';

    public function __construct() {
        $this->rootDirectory = $_ENV['DIR_VIEW'];
        $this->app = Application::$app;
    }

    public function RenderView($view, array $params)
    {
        $layoutName = Application::$app->layout;
        if (Application::$app->controller) {
            $layoutName = Application::$app->controller->layout;
        }
        $viewContent = $this->RenderViewOnly($view, $params);
        ob_start();
        include_once Application::$ROOT_DIR . '/' . $this->rootDirectory . "layouts/$layoutName.php";
        $layoutContent = ob_get_clean();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function RenderViewOnly($view, array $params)
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        include_once Application::$ROOT_DIR . '/' . $this->rootDirectory . "$view.php";
        return ob_get_clean();
    }

    public function Render($view) {
        include_once Application::$ROOT_DIR . '/' . $this->rootDirectory . "$view.php";
    }

    public function ForceRender($view) {
        include Application::$ROOT_DIR . '/' . $this->rootDirectory . "$view.php";
    }

    public static function Route($url, $params = []) {
        return Application::$app->router->PrintRoute($url, $params);
    }
}