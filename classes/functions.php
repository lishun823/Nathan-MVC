<?php
    function element($key, $arr, $default_value=""){
        if (is_array($arr) && isset($arr[$key])) return $arr[$key];
        return $default_value;
    }