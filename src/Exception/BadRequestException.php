<?php

namespace InfinityBrackets\Exception;

class BadRequestException extends \Exception
{
    protected $message = 'Bad Request';
    protected $code = 400;
}