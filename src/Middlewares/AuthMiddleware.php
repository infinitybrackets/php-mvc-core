<?php

namespace InfinityBrackets\Middlewares;

use InfinityBrackets\Core\Application;
use InfinityBrackets\Exception\ForbiddenException;

class AuthMiddleWare extends BaseMiddleware
{
    public array $actions = [];

    public function __construct(array $actions = []) {
        $this->actions = $actions;
    }

    public function execute() {
        if(!Application::$app->session->GetAuth()) {
            if(empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                throw new ForbiddenException();
            }
        }
    }
}