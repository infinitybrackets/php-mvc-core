<?php

namespace InfinityBrackets\Core;

use InfinityBrackets\Exception\NotFoundException;

class Response {
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
        $url = "?view=" . $url;
        if(empty($url) || "?view=") {
            $url = './';
        }
        $this->Header($url);
    }

    public function RedirectExternalLink($url = NULL) {
        if(is_null($url)) {
            return FALSE;
        }
        $this->Header($url);
    }

    public function RedirectWithConfirmation($url = NULL) {
        if(is_null($url)) {
            return FALSE;
        }
        $this->Header($url);
    }

    public function DownloadFile($file = NULL) {
        if(is_null($file)) {
            return FALSE;
        }
        $this->Header($file);
    }

    public function Header($url) {
        header('location: ' . $url);
    }
}