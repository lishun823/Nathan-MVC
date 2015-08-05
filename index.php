<?php

if(version_compare(PHP_VERSION,'5.4.0','<')) {
    ini_set('magic_quotes_runtime',0);
    define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
}else{
    define('MAGIC_QUOTES_GPC',false);
}

defined('APP_PATH') or define('APP_PATH',  str_replace("\\", "/", __DIR__) );
defined('LOG_PATH') or define('LOG_PATH',  APP_PATH. "/logs" );


define('IS_CGI',(0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);


define('REQUEST_METHOD',IS_CLI ? "CLI" : $_SERVER['REQUEST_METHOD']);
define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
define('IS_PUT',        REQUEST_METHOD =='PUT' ? true : false);
define('IS_DELETE',     REQUEST_METHOD =='DELETE' ? true : false);
define('IS_AJAX',       (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'));

require("application/common/functions.php");
require("classes/core.php");

/**
 * 命令行的调用方式:
 *  php index.php "m=home&c=game&a=list&key1=$value1&key2=$value2&key3=$value3&key4=$value4"
 *  其中 m,c,a 三个参数是必须的，指明调用哪个模块(M)的哪个控制器(C)的哪个动作/方法(A)
 *  程序会把所有参数转化成 $_GET数组。
*/
if (IS_CLI && intval($_SERVER["argc"])>1) parse_str($_SERVER["argv"][1], $_GET);

!empty(element('m', $_GET)) && define("M", strtolower(element('m', $_GET)));
!empty(element('c', $_GET)) && define("C", strtolower(element('c', $_GET)));
!empty(element('a', $_GET)) && define("A", strtolower(element('a', $_GET)));

// 浏览器访问，有开 url_rewrite
$uri = element("PHP_SELF", $_SERVER);
if (strpos($uri, "index.php/")!==false){
	$parts= explode("/", strstr($uri, "index.php/"));
	if (count($parts)>3){
		defined('M') or define("M", strtolower($parts[1]));
		defined('C') or define("C", strtolower($parts[2]));
		defined('A') or define("A", strtolower($parts[3]));
	}
}

// 默认情况访问默认的控制器
defined('M') or define("M", 'home');
defined('C') or define("C", 'home');
defined('A') or define("A", 'index');

//echo "/* ".M."::".C."::".A." */";

spl_autoload_extensions('.php');
spl_autoload_register('loadClasses');

ob_start();
$controller = null;

if (preg_match("/^\w+$/", M) && preg_match("/^\w+$/", C) && preg_match("/^\w+$/", A)){
	require("application/common/basecontroller.php");
	require("application/common/basemodel.php");
	$loader = new Loader();
	$controller = $loader->createController();
	if (is_object($controller)) $controller->executeAction();
}else{
	url_error();
}

$response = ob_get_contents();
ob_end_flush();

log_message("", $response, $controller);