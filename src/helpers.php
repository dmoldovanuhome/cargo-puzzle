<?php

if (!function_exists('dd')) {
    function dd(...$args)
    {
        if (function_exists('dump')) {
            dump(...$args);
        } else {
            echo '<pre>';
            var_dump(...$args);
            echo '</pre>';
        }
        die;
    }
}


if (!function_exists('dump')) {
    function dump(...$args)
    {
        echo '<pre>';
        var_dump(...$args);
        echo '</pre>';
    }
}
