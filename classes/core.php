<?php

function error($msg="file_not_found"){
	echo "error: $msg";
	return false;
}

function loadClasses($className){
	$path=APP_PATH."/";

	if (preg_match("/^[A-Z]\w*Model$/", $className)){
		$path = APP_PATH."/".M."/models/";
	}elseif (preg_match("/[A-Z]\w*Model$/", $className)){
		$parts = explode("\\", $className);
		$path = APP_PATH."/".$parts[0]."/models/";
		list(, $className) = explode("\\", $className);
		echo "classname=$className, path=$path<br>\r\n";
	}else{
		$path = APP_PATH."/classes/";
	}
	//echo "classname=$className, path=$path<br>\r\n";
    set_include_path($path);
    spl_autoload($className);
}

function newClass($className, $module=''){
	$className = strtolower(str_ireplace("model", "", $className));
	if ($module=="") $module = M;
	require $module."/models/".$className."Model.php";
	$modelName = ucfirst($className)."Model";
	return new $modelName();
}


function config($key=""){
    static $settings  =   array();
    if (count($settings)==0){
    	foreach(glob("config/*.php") as $cfgFile){
    		$fname = basename($cfgFile, ".php");
    		$config = array();
    		include_once($cfgFile);
    		$settings[$fname] = $config;
    	}
	}
	if ($key=="") return $settings;
	$arr=explode(".", $key);
	$ret = $settings;
	for($i=0; $i<count($arr); $i++){
		$ret = isset($ret[$arr[$i]])? $ret[$arr[$i]] : NULL;
	}
	return $ret;
}

