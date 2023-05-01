<?php

namespace InfinityBrackets\Core;

use InfinityBrackets\Middlewares\BaseMiddleware;

class Controller
{
    public string $layout = 'app';
    public string $action = '';
    public $model = NULL;
    public $models = [];
    protected array $middlewares = [];

    public function SetLayout($layout): void
    {
        $this->layout = $layout;
    }

    public function Render($view, $params = []): string
    {
        return Application::$app->router->RenderView($view, $params);
    }

    public function RegisterMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function GetMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function RegisterModel($model) {
        $this->model = new $model();
    }

    public function BindModel($classes = []) {
        foreach($classes as $class) {
            $temp = explode('\\', $class);
            $this->models[end($temp)] = new $class();
        }
    }
}