<?php

if (!function_exists('lang')) {
    /**
     * @param string $key
     * @return string
     */
    function lang(string $key):string
    {
        return $key;
//        return trans($key);
    }
}

// 可以在这里添加更多的公共函数