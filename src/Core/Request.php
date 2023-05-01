<?php

namespace InfinityBrackets\Core;

class Request
{
    private array $routeParams = [];

    public function GetMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function Compose() {
        $path = $_SERVER['REQUEST_URI'];

        switch(Application::$app->config->env->APP_ENV) {
            case "local":
                $path = str_replace(Application::$app->config->environment->local, "/", $path);
                break;
            case "sandbox":
                $path = str_replace(Application::$app->config->environment->sandbox, "/", $path);
                break;
            case "live":
                $path = str_replace(Application::$app->config->environment->live, "/", $path);
                break;
        }
        return $path;
    }

    public function GetUrl()
    {
        $path = $this->Compose();

        if($path != '/') {
            $path = str_replace('/', '', $path);
            $path = str_replace('?', '', $path);
            $path = explode('&', $path);

            foreach($path as $temp) {
                $temprow = explode('=', $temp);

                if(in_array('view', $temprow)) {
                    $path = '/' . $temprow[1];
                } else if(in_array('tab', $temprow)) {
                    $path .= '/' . $temprow[1];
                } else if(in_array('action', $temprow)) {
                    $path .= '/' . $temprow[1];
                }
            }
        }

        return $path;
    }

    public function GetUrlConverted()
    {
        $path = $this->Compose();

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
        if($_FILES) {
            FileStorage::Push($value);
            foreach ($_FILES as $file) {
                FileStorage::Push($file);
            }
        }
        return $data;
    }

    public function GetValue($key = NULL) {
        if(is_null($key)) {
            return FALSE;
        }
        $data = $this->GetBody();
        if(array_key_exists($key, $data)) {
            if(empty($data[$key])) {
                return FALSE;
            }
            return $data[$key];
        }
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