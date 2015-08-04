<?php
/*
 * Project: Nathan MVC
 * File: index.php
 * Purpose: landing page which handles all requests
 * Author: Nathan Davison
 */

if(version_compare(PHP_VERSION,'5.4.0','<')) {
    ini_set('magic_quotes_runtime',0);
    define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
}else{
    define('MAGIC_QUOTES_GPC',false);
}


defined('APP_PATH')     or define('APP_PATH',  str_replace("\\", "/", __DIR__) );
define('IS_CGI',(0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);


require("classes/functions.php");
if (IS_CLI && intval($_SERVER["argc"])>1) parse_str($_SERVER["argv"][1], $_GET);

!empty(element('m', $_GET)) && define("MODULE", strtolower(element('m', $_GET)));
!empty(element('c', $_GET)) && define("CONTROLLER", strtolower(element('c', $_GET)));
!empty(element('a', $_GET)) && define("ACTION", strtolower(element('a', $_GET)));

$uri = element("PHP_SELF", $_SERVER);
if (strpos($uri, "index.php/")!==false){
	$parts= explode("/", strstr($uri, "index.php/"));
	if (count($parts)>3){
		defined('MODULE') or define("MODULE", strtolower($parts[1]));
		defined('CONTROLLER') or define("CONTROLLER", strtolower($parts[2]));
		defined('ACTION') or define("ACTION", strtolower($parts[3]));
	}
}

defined('MODULE') or define("MODULE", 'home');
defined('CONTROLLER') or define("CONTROLLER", 'home');
defined('ACTION') or define("ACTION", 'index');

(preg_match("/^\w+$/", MODULE) && preg_match("/^\w+$/", CONTROLLER) && preg_match("/^\w+$/", ACTION)) or die(error());

//echo "/* ".MODULE."::".CONTROLLER."::".ACTION." */";

require("classes/basecontroller.php");
require("classes/basemodel.php");
require("classes/loader.php");

$loader = new Loader(); //create the loader object
$controller = $loader->createController(); //creates the requested controller object based on the 'controller' URL value
if (is_object($controller)) $controller->executeAction(); //execute the requested controller's requested method based on the 'action' URL value. Controller methods output a View.

