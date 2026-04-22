<?php

namespace core;

class Validator
{
    // this fun string is pure fun dont depend on any thing external and font use this-> in it 
    // we will make it static 
    public static function string($value, $min = 1, $max = INF)
    {
        $value = trim($value);
        return strlen($value) >= $min && strlen($value) <=  $max;
    }
    public static function email($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
    public static function graterThan($value, $givenamount)
    {
        return $value > $givenamount;
    }
}
