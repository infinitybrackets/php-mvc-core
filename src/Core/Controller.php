<?php

namespace InfinityBrackets\Core;

use InfinityBrackets\Middlewares\BaseMiddleware;

class Controller
{
    public string $layout = 'app';
    public string $action = '';
    
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
}