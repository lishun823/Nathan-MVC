<?php
function url_error($msg = "file_not_found") {
	defined('URL_ERROR') or define("URL_ERROR", true);
    echo "error: $msg";
    return false;
}

/**
 * 自动加载对应的类文件，给 spl_autoload_register用的
 *
 * @param  [type] $className [description]
 * @return [type]            [description]
 */
function loadClasses($className) {
    $path = APP_PATH . "/";
    if (preg_match("/^[A-Z]\w*Model$/", $className)) {
        $path = APP_PATH . "/" . M . "/models/";
    } else {
        $path = APP_PATH . "/classes/";
    }

    //echo "classname=$className, path=$path<br>\r\n";
    set_include_path($path);
    spl_autoload($className);
}


/**
 * 加载指定URL的class
 *
 * @param  [type] $classPath [路径， 或者 "module名.model名"]
 * @return [type]            [返回一个对象的实例]
 */
function newClass($classPath) {
	if (strpos($classPath, ".")!==false){
		list($module, $className) = explode(".", $classPath);
		$className = strtolower(str_ireplace("model", "", $className));
		$fileName = $module . "/models/" . $className . "Model.php";
	    $clsName = ucfirst($className) . "Model";
	}else{
		$fileName =$classPath;
		$clsName=basename($fileName, ".php");
	}
	require $fileName;
	return new $clsName();
}

/**
 * 读取配置文件，程序会加载 config目录下所有的php文件
 * 调用方式:
 * config();
 * config("database");
 * config("database.username");
 * config("database.foo.bar");
 *
 * @param  string $key [为空则输出所有配置, 其他情况输出指定文件指定节点的配置]
 * @return [type]      [description]
 */
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

/**
 * 日志记录，会在 $msg之前增加 日期，IP, URI等内容
 *
 * @param  string $filepath 如果次参数为空，则默认日志文件为  LOG_PATH/模块/控制器_方法_日期.log
 * @param  string $msg      [description]
 * @return [type]           [description]
 */
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
