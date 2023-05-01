<?php

namespace InfinityBrackets\Helpers;

class StringHelper
{
    public function Sanitize($string) {
        $string = strtolower($string);
        $string = self::RemoveSpaces($string);
        $string = self::RemoveHypens($string);
        return $string;
    }

    public function RemoveSpaces($string) {
        return str_replace(' ', '', $string);
    }

    public function RemoveHypens($string) {
        return str_replace('-', '', $string);
    }
}