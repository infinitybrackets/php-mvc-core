<?php

namespace InfinityBrackets\Core;

use InfinityBrackets\Exception\NotFoundException;

class Response
{
    public function StatusCode(int $code)
    {
        http_response_code($code);
    }

    public function Redirect($url = NULL)
    {
        if(is_null($url)) {
            $url = '/';
        }
        $router = Application::$app->router;
        if(!array_key_exists($url, $router->GetRouteMap(Application::$app->request->GetMethod()))) {
            throw new NotFoundException();
        }
        $url = ltrim($url, '/');
        if(empty($url)) {
            $url = './';
        }
        header("Location: " . $url);
    }

    public function RedirectWithConfirmation($url = NULL) {
        if(is_null($url)) {
            // Parameter error
        }
    }
}