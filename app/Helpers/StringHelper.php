<?php

if (!function_exists('middleEllipsis')) {
    function middleEllipsis($str)
    {
        if (strlen($str) > 28) {
            return substr($str, 0, 17).'...'.substr($str, strlen($str) - 8);
        }
        return $str;
    }
}