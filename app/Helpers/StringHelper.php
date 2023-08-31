<?php

if (!function_exists('middleEllipsis')) {
    function middleEllipsis($str)
    {
        return substr($str, 0, 17).'...'.substr($str, strlen($str) - 8);
    }
}