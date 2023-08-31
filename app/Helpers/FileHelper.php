<?php

// app/Helpers/FileHelper.php

if (!function_exists('normalizeFileName')) {
    function normalizeFileName($fileName)
    {
        return strtolower(str_replace(' ', '_', $fileName));
    }
}
