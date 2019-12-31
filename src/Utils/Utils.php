<?php

namespace PhpSports\Utils;

class Utils
{
    public static function array_key_first(array $value = null)
    {
        if (!$value) return null;
        foreach($value as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}
