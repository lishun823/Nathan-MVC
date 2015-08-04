<?php
function element($key, $arr = array() , $default_value = "") {
    if (is_array($arr) && isset($arr[$key])) return $arr[$key];
    return $default_value;
}

function error($msg="file_not_found"){
	echo "error: $msg";
	return false;
}