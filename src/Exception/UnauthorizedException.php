<?php

namespace InfinityBrackets\Exception;

class UnauthorizedException extends \Exception
{
    protected $message = 'You had no access in this website';
    protected $code = 401;
}