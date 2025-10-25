<?php

use MobiCity\Core\Log;

if (!function_exists('hello'))
{
    function hello() 
    {
        return "Hello World!";
    }
}

if (!function_exists('env')) {
    function get_env($key, $default = null) {
        
        if (!array_key_exists($key, $_ENV)) {
            return false;
        }

        $value = $_ENV[$key];

        if ($value === false) {
            return $default;
        }

        return $value;
    }
}

if (!function_exists('_log')) {
    function _log($var, $type = 'info') {
        Log::{$type}($var);
    }
}