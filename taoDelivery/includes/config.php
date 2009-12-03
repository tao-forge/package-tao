<?php
/**
 * main configuration file
 */

# plugins directory
define("DIR_PLUGIN"			, dirname(__FILE__). "/../plugins/");

# actions directory
define("DIR_ACTIONS"		, dirname(__FILE__). "/../actions/");

# models directory
define("DIR_MODELS"			, dirname(__FILE__). "/../models/");

# plugin directory
define('DIR_PLUGINS'		, dirname(__FILE__).'/../plugins/');

# views directory
define("DIR_VIEWS"			, "views/");

# helpers directory
define("DIR_HELPERS"		, dirname(__FILE__) . "/../helpers/");

# core directory
define("DIR_CORE"			, dirname(__FILE__) . "/../../PHP-Framework/core/");

# core helpers directory
define("DIR_CORE_HELPERS"	, DIR_CORE . "helpers/");

# core utils directory
define("DIR_CORE_UTILS"		, DIR_CORE . "util/");

# session namespace
define('SESSION_NAMESPACE', 'PHPFramework');

# default module name
define('DEFAULT_MODULE_NAME', 'Tests');

#default action name
define('DEFAULT_ACTION_NAME', 'index');

$GLOBALS['classpath']			= array(DIR_CORE,
										DIR_CORE_UTILS,
										DIR_ACTIONS,
										DIR_MODELS);

# theme directory
$GLOBALS['dir_theme']		= "templates/";

# language
$GLOBALS['lang']			= 'en';

//@todo to remove when the user management is implemented
define("API_LOGIN", "demo", true);
define("API_PASS", md5("demo"), true);
define("API_MODULE", "taotrans_delivery", true);

#BASE PATH: the root path in the file system (usually the document root)
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/taoDelivery');

#BASE URL (usually the domain root)
define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST']. '/taoDelivery');

#BASE WWW the web resources path
define('BASE_WWW', BASE_URL . '/' . DIR_VIEWS);
?>