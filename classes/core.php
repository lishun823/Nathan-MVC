<?php
function url_error($msg = "file_not_found") {
	defined('URL_ERROR') or define("URL_ERROR", true);

    echo "error: $msg";
    return false;
}
function loadClasses($className) {
    $path = APP_PATH . "/";
    if (preg_match("/^[A-Z]\w*Model$/", $className)) {
        $path = APP_PATH . "/" . M . "/models/";
    } elseif (preg_match("/[A-Z]\w*Model$/", $className)) {
        $parts = explode("\\", $className);
        $path = APP_PATH . "/" . $parts[0] . "/models/";
        list(, $className) = explode("\\", $className);
        echo "classname=$className, path=$path<br>\r\n";
    } else {
        $path = APP_PATH . "/classes/";
    }

    //echo "classname=$className, path=$path<br>\r\n";
    set_include_path($path);
    spl_autoload($className);
}


function newClass($className, $module = '') {
    $className = strtolower(str_ireplace("model", "", $className));
    if ($module == "") $module = M;
    require $module . "/models/" . $className . "Model.php";
    $modelName = ucfirst($className) . "Model";
    return new $modelName();
}


function config($key = "") {
    static $settings = array();
    if (count($settings) == 0) {
        foreach (glob("config/*.php") as $cfgFile) {
            $fname = basename($cfgFile, ".php");
            $config = array();
            include_once ($cfgFile);
            $settings[$fname] = $config;
        }
    }
    if ($key == "") return $settings;

    $arr = explode(".", $key);
    $ret = $settings;
    for ($i = 0; $i < count($arr); $i++) {
        $ret = isset($ret[$arr[$i]]) ? $ret[$arr[$i]] : NULL;
    }
    return $ret;
}


function log_message($filepath="", $msg="") {
	if ($filepath==="" && LOG_REQUEST){
		if (defined('URL_ERROR')){
			$filepath = LOG_PATH."/url_error_".date('Ym').".log";
		}else{
			$filepath = LOG_PATH."/".M;
			if (!is_dir($filepath)) mkdir($filepath, 0777, true);
			$filepath =$filepath. "/".C."_". A . date("_Ym").".log";
		}
	}

    $newfile = file_exists($filepath);
    if (!$fp = @fopen($filepath, 'ab')) return false;

    $now = date("Y-m-d H:i:s");
    $ip = get_client_ip();
    $uri = IS_CLI ? "CLI:".implode("?", $_SERVER["argv"]) : $_SERVER["REQUEST_URI"];
    $logmsg ="$now $ip\r\n$uri\r\n$msg\r\n\r\n";

    flock($fp, LOCK_EX);
    fwrite($fp,$logmsg);
    flock($fp, LOCK_UN);
    fclose($fp);

    if ($newfile) chmod($filepath, 0644);

}
