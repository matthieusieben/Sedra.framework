<?php

define('START_TIME', microtime(TRUE));
define('REQUEST_TIME', (int) $_SERVER['REQUEST_TIME']);

# Root directory
$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
defined('SITE_ROOT') or define('SITE_ROOT', dirname($caller[0]['file']).'/');

# Application folder
defined('APP_ROOT') or define('APP_ROOT', SITE_ROOT.'application/');
define('APP_CONTROLLERS', APP_ROOT.'controllers/');
define('APP_LIBRARIES', APP_ROOT.'libraries/');
define('APP_MODELS', APP_ROOT.'models/');
define('APP_VIEWS', APP_ROOT.'views/');

# Framework folders
defined('FRAMEWORK_ROOT') or define('FRAMEWORK_ROOT', __DIR__.'/');
define('FRAMEWORK_CONTROLLERS', FRAMEWORK_ROOT.'controllers/');
define('FRAMEWORK_LIBRARIES', FRAMEWORK_ROOT.'libraries/');
define('FRAMEWORK_MODELS', FRAMEWORK_ROOT.'models/');
define('FRAMEWORK_VIEWS', FRAMEWORK_ROOT.'views/');

# View include path
defined('VIEW_PATH') or define('VIEW_PATH', APP_VIEWS.PATH_SEPARATOR.FRAMEWORK_VIEWS);

# Public folder
defined('PUBLIC_DIR') or define('PUBLIC_DIR', SITE_ROOT.'public/');

# Private folder
defined('PRIVATE_DIR') or define('PRIVATE_DIR', SITE_ROOT.'private/');

# Framework variables
define('SEDRA_VERSION', '1.0');

# Add INCLUDES_DIR to the path
set_include_path('.'.PATH_SEPARATOR.APP_MODELS.PATH_SEPARATOR.FRAMEWORK_MODELS);

# Unset globals
if (ini_get('register_globals')) {
	foreach ($GLOBALS as $key => $value) {
		$allowed = array(
			'_ENV' => TRUE,
			'_GET' => TRUE,
			'_POST' => TRUE,
			'_COOKIE' => TRUE,
			'_FILES' => TRUE,
			'_SERVER' => TRUE,
			'_REQUEST' => TRUE,
			'GLOBALS' => TRUE
		);
		if (!isset($allowed[$key])) {
			unset($GLOBALS[$key]);
		}
	}
}

# Load settings
global $config;
global $databases;
global $models;
global $libraries;

require APP_ROOT.'settings.php';

# Load core files
require 'functions.php';
require 'hook.php';
require 'error.php';
require 'url.php';
require 'lang.php';
require 'devel.php';

# Shutdown function
register_shutdown_function('hook_invoke', 'shutdown');

# Setup error handling
error_reporting(config('log.reporting', E_ALL));
set_error_handler('__error_handler');
set_exception_handler('__exception_handler');

# Global variables
global $controller;

$controller = url_segment(0, 'index');

# Include libraries
foreach((array) @$libraries as $library => $required) {
	load_library($library, $required);
}

unset($library);
unset($required);

# Include modules
foreach((array) @$models as $model) {
	require_once $model;
}

unset($model);
