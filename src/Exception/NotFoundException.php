<?php

namespace InfinityBrackets\Exception;

class NotFoundException extends \Exception
{
    protected $message = 'Page not found';
    protected $code = 404;
}