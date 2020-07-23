<?php

if (! function_exists('flash_push_HELPER')) {
    function flash_push_HELPER($key, $value) {
        $values = \Session::get($key, []);

        if (!is_array($value)) {
            $value = [$value];
        }

        $array_key = md5(rand(0,1000));
        $values[$array_key] = array_merge(['key' => $array_key], $value);
        \Session::flash($key, $values);
    }
} 