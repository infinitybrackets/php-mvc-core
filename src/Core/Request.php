<?php

namespace InfinityBrackets\Core;

class Request
{
    private array $routeParams = [];

    public function GetMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function GetUrl()
    {
        $path = $_SERVER['REQUEST_URI'];

        if(Application::$app->config->env->APP_ENV == "local") {
            $path = str_replace("/cvsu_ils/", "/", $path);
        } else {
            $path = str_replace("/sandbox/cvsu_ils/", "/", $path);
        }

        if($path != '/') {
            $path = str_replace('/', '', $path);
            $path = str_replace('?', '', $path);
            $path = explode('&', $path);

            foreach($path as $temp) {
                $temprow = explode('=', $temp);
                if(in_array('view', $temprow)) {
                    $path = '/' . $temprow[1];
                    break;
                }
            }
        }
        return $path;
    }

    public function GetUrlConverted()
    {
        $path = $_SERVER['REQUEST_URI'];

        if(Application::$app->config['env']->APP_ENV == "local") {
            $path = str_replace("/cvsu_ils/", "/", $path);
        } else {
            $path = str_replace("/sandbox/cvsu_ils/", "/", $path);
        }

        if($path != '/') {
            $path = str_replace('/', '', $path);
            $path = str_replace('?', '', $path);
            $path = explode('&', $path);

            $tempPath = "";
            $paths = array();
            foreach($path as $temp) {
                $temprow = explode('=', $temp);
                $tempPath .= '/' . $temprow[1];
            }
            $path = $tempPath;
        }
        return $path;
    }

    public function IsGet()
    {
        return $this->GetMethod() === 'get';
    }

    public function IsPost()
    {
        return $this->GetMethod() === 'post';
    }

    public function GetBody()
    {
        $data = [];
        if ($this->IsGet()) {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->IsPost()) {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $data;
    }

    /**
     * @param $params
     * @return self
     */
    public function SetRouteParams($params)
    {
        $this->routeParams = $params;
        return $this;
    }

    public function GetRouteParams()
    {
        return $this->routeParams;
    }

    public function GetRouteParam($param, $default = null)
    {
        return $this->routeParams[$param] ?? $default;
    }
}