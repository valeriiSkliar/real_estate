<?php

namespace app\helpers;

/**
 * StringHelper provides string manipulation utilities.
 */
class StringHelper
{
    /**
     * Converts a string to a slug format.
     * 
     * @param string $text The text to convert
     * @return string The slugified text
     */
    public static function slugify($text)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    }
}
