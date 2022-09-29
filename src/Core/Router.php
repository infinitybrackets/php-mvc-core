<?php

namespace InfinityBrackets\Core;

use InfinityBrackets\Exception\NotFoundException;

class Router
{
    private Request $request;
    private Response $response;
    private array $routeMap = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function Get(string $url, $callback)
    {
        $this->routeMap['get'][$url] = $callback;
    }

    public function Post(string $url, $callback)
    {
        $this->routeMap['post'][$url] = $callback;
    }

    /**
     * @return array
     */
    public function GetRouteMap($method): array
    {
        return $this->routeMap[$method] ?? [];
    }

    public function GetCallback()
    {
        $method = $this->request->GetMethod();
        $url = $this->request->GetUrlConverted();
        
        // Trim slashes
        $url = trim($url, '/');

        // Get all routes for current request method
        $routes = $this->GetRouteMap($method);

        $routeParams = false;

        // Start iterating registed routes
        foreach ($routes as $route => $callback) {
            // Trim slashes
            $route = trim($route, '/');
            $routeNames = [];

            if (!$route) {
                continue;
            }

            // Find all route names from route and save in $routeNames
            if (preg_match_all('/\{(\w+)(:[^}]+)?}/', $route, $matches)) {
                $routeNames = $matches[1];
            }

            // Convert route name into regex pattern
            $routeRegex = "@^" . preg_replace_callback('/\{\w+(:([^}]+))?}/', fn($m) => isset($m[2]) ? "({$m[2]})" : '(\w+)', $route) . "$@";

            // Test and match current route against $routeRegex
            if (preg_match_all($routeRegex, $url, $valueMatches)) {
                $values = [];

                for ($i = 1; $i < count($valueMatches); $i++) {
                    $values[] = $valueMatches[$i][0];
                }

                $routeParams = array_combine($routeNames, $values);

                $this->request->SetRouteParams($routeParams);
                return $callback;
            }
        }

        return false;
    }

    public function Resolve()
    { 
        $method = $this->request->GetMethod();
        $url = $this->request->GetUrl();

        $callback = $this->routeMap[$method][$url] ?? false;

        if (!$callback) {

            $callback = $this->GetCallback();

            if ($callback === false) {
                throw new NotFoundException();
            }
        }

        if (is_string($callback)) {
            return $this->RenderView($callback);
        }
        if (is_array($callback)) {
            $controller = new $callback[0];
            $controller->action = $callback[1];
            Application::$app->controller = $controller;
            $middlewares = $controller->GetMiddlewares();
            foreach ($middlewares as $middleware) {
                $middleware->execute();
            }
            $callback[0] = $controller;
        }
        return call_user_func($callback, $this->request, $this->response);
    }

    public function RenderView($view, $params = [])
    {
        return Application::$app->view->RenderView($view, $params);
    }

    public function RenderViewOnly($view, $params = [])
    {
        return Application::$app->view->RenderViewOnly($view, $params);
    }

    public function Route($url, $params = []) {
        if(!array_key_exists($url, $this->GetRouteMap('get'))) {
            return FALSE;
        }
        $url = explode('/', $url);
        $url = array_filter($url);
        $url = [...$url];
        $count = count($url);
        if($count > 1) {
            $temp = "";
            $isSet = TRUE;
            $i = 0;
            if(count($params) != count($url) - 1) {
                return FALSE;
            }
            foreach($url as $u) {
                if($isSet) {
                    $temp .= "?view=" . $u;
                    $isSet = FALSE;
                } else {
                    $match = str_replace(['{', '}'], '', $u);
                    $temp .= "&" . $match . "=" . $params[$i];
                    $i++;
                }
            }
            $url = $temp;
        } else {
            $url = "?view=" . $url[0];
        }
        return $url;
    }

    public function PrintRoute($url, $params = []) {
        $route = $this->Route($url, $params);
        if(!$route) {
            $route = '?view=error';
        }
        echo $route;
    }
}