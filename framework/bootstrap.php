<?php

define('START_TIME', microtime(TRUE));
define('REQUEST_TIME', (int) $_SERVER['REQUEST_TIME']);

# Root directory
$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
defined('SITE_ROOT') or define('SITE_ROOT', dirname($caller[0]['file']).'/');

# Application folder
defined('APP_ROOT') or define('APP_ROOT', SITE_ROOT.'application/');

# Framework folders
defined('FRAMEWORK_ROOT') or define('FRAMEWORK_ROOT', __DIR__.'/');

# Public folder
defined('PUBLIC_DIR') or define('PUBLIC_DIR', SITE_ROOT.'public/');

# Private folder
defined('PRIVATE_DIR') or define('PRIVATE_DIR', SITE_ROOT.'private/');

# Framework variables
define('SEDRA_VERSION', '1.0');

# User roles
define('ANONYMOUS_RID', 0);
define('AUTHENTICATED_RID', 1);
define('MODERATOR_RID', 2);
define('ADMINISTRATOR_RID', 3);

# Add INCLUDES_DIR to the path
set_include_path(FRAMEWORK_ROOT);

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

# Load core files
require 'core/functions.php';
require 'core/hook.php';
require 'core/log.php';
require 'core/error.php';
require 'core/url.php';

# Load settings
if(!@include APP_ROOT.'settings.php')
	throw new FrameworkException('Could not load settings file.', 500);

# The following core files depend on the settings
require 'core/language.php';
require 'core/database.php';
require 'core/user.php';
require 'core/session.php';

# Include modules
foreach((array) config('modules') as $module => $required)
	load_module($module, $required);
unset($module);
unset($required);

# Load langage files after modules
global $language;
language_load($language);

# Main controller
global $controller;
$controller = url_segment(0, 'index');
