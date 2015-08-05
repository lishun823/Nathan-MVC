<?php
/**
 * 此函数只用于处理 URL错误，比如404等
 *
 * @param  string $msg [description]
 * @return [type]      [description]
 */
function url_error($msg = "file_not_found") {
	defined('URL_ERROR') or define("URL_ERROR", true);
    echo "error: $msg";
    return false;
}

function E($msg){
	die($msg);
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
        $path = APP_PATH . "/application/" . M . "/models/";
    } else {
        $path = APP_PATH . "/classes/";
    }

    //echo "classname=$className, path=$path<br>\r\n";
    set_include_path($path);
    spl_autoload($className);
}


/**
 * 加载指定URL的class
 * 举例：
 * $user = newClass("account.user");
 * $excel = newClass("include/class/lib/phpExcel");
 *
 * @param  [type] $classPath [路径， 或者 "module名.model名"]
 * @return [type]            [返回一个对象的实例, 文件不存在则返回false]
 */
function newClass($classPath) {
	if (strpos($classPath, ".")!==false){
		list($module, $className) = explode(".", $classPath);
		$className = strtolower(str_ireplace("model", "", $className));
		$fileName = "application/". $module . "/models/" . $className . "Model.php";
	    $clsName = ucfirst($className) . "Model";
	}else{
		$fileName =$classPath.".php";
		$clsName=basename($fileName, ".php");
	}
	if (file_exists($fileName)){
		require $fileName;
		return new $clsName();
	}else{
		return false;
	}
}

/**
 * 读取配置文件，程序会加载config目录下所有的php文件
 * 调用方式:
 * config();
 * config("database");
 * config("database.username");
 * config("database.foo.bar");
 *
 * @param  string $key [为空则输出所有配置, 其他情况输出指定文件指定节点的配置]
 * @param  string $default_value 节点不存在的时候的默认值。
 * @return [type]      [description]
 */
function config($key = "", $default_value = "") {
    static $settings = array();
    if (count($settings) == 0) {
        foreach (glob("application/config/*.php") as $cfgFile) {
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
        $ret = isset($ret[$arr[$i]]) ? $ret[$arr[$i]] : $default_value;
    }
    return $ret;
}

function C($key = "", $default_value = "") {
	return config($key, $default_value);
}

/**
 * 日志记录，会在 $msg之前增加 日期，IP, URI等内容
 * log_message("foo/bar/log1.log", "something");  会写在日志目录下  LOG_PATH."/foo/bar/log1.log"
 * log_message("/foo/bar/log2.log", "something"); 会写在绝对路径  /foo/bar/log2.log"
 *
 * @param  string $filepath 如果次参数为空，则默认日志文件为  LOG_PATH/模块/控制器_方法_日期.log
 * @param  string $msg      [description]
 * @return [type]           [description]
 */

function log_message($filepath="", $msg="", $controller=null) {
	$filepath = trim($filepath);
	if ($filepath===""){
		if (is_object($controller) && in_array_case(A,$controller->disable_logging)) return false;

		if (config("app.log_request")){
			if (defined('URL_ERROR')){
				$filepath = LOG_PATH."/url_error_".date('Ym').".log";
			}else{
				$filepath = LOG_PATH."/".M;
				if (!is_dir($filepath)) mkdir($filepath, 0777, true);
				$filepath =$filepath. "/".C."_". A . date("_Ym").".log";
			}
		}
	}else{
		if (substr($filepath,0,1)!=="/") $filepath = LOG_PATH."/". ltrim($filepath, "/");
		$dirpath = dirname($filepath);
		if (!is_dir($dirpath)) mkdir($dirpath, 0777, true);
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



/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 */
function I($name='',$default='',$filter=null,$datas=null) {
    if(strpos($name,'.')) { // 指定参数来源
        list($method,$name) =   explode('.',$name,2);
    }else{ // 默认为自动判断
        $method =   'param';
    }
    switch(strtolower($method)) {
        case 'get'     :   $input =& $_GET;break;
        case 'post'    :   $input =& $_POST;break;
        case 'put'     :   parse_str(file_get_contents('php://input'), $input);break;
        case 'param'   :
            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input  =  $_POST;
                    break;
                case 'PUT':
                    parse_str(file_get_contents('php://input'), $input);
                    break;
                default:
                    $input  =  $_GET;
            }
            break;
        case 'request' :   $input =& $_REQUEST;   break;
        case 'session' :   $input =& $_SESSION;   break;
        case 'cookie'  :   $input =& $_COOKIE;    break;
        case 'server'  :   $input =& $_SERVER;    break;
        case 'globals' :   $input =& $GLOBALS;    break;
        case 'data'    :   $input =& $datas;      break;
        default:
            return NULL;
    }
    if(''==$name) { // 获取全部变量
        $data       =   $input;
        $filters =$filter;
        if($filters) {
            if(is_string($filters)){
                $filters    =   explode(',',$filters);
            }
            foreach($filters as $filter){
                $data = array_map_recursive($filter,$data); // 参数过滤
            }
        }
    }else{
        $data=isset($input[$name]) ?  $input[$name] : (isset($default)?$default:NULL);
        $filters =$filter;
        if($filters) {
            if(is_string($filters)){
                $filters    =   explode(',',$filters);
            }elseif(is_int($filters)){
                $filters    =   array($filters);
            }

            foreach($filters as $filter){
                if(function_exists($filter)) {
                    $data   =   is_array($data)?array_map_recursive($filter,$data):$filter($data); // 参数过滤
                }else{
                    $data   =   filter_var($data,is_int($filter)?$filter:filter_id($filter));
                    if(false === $data) {
                        return   isset($default)?$default:NULL;
                    }
                }
            }
        }
    }
    return $data;
}

function array_map_recursive($filter, $data) {
     $result = array();
     foreach ($data as $key => $val) {
         $result[$key] = is_array($val)
             ? array_map_recursive($filter, $val)
             : call_user_func($filter, $val);
     }
     return $result;
 }