<?php

/*
 * ---------------------------------------------------------------------------
 * Define application constants
 * ---------------------------------------------------------------------------
 */

// System Start Time
define('START_TIME', microtime());

// System Start Memory
define('START_MEMORY_USAGE', memory_get_usage());

// Absolute path to the base folder
define('BASE_DIR', realpath(dirname(__FILE__)).'/');

// Absolute path to the system folder
define('SYSTEM_DIR', BASE_DIR.'system/');

// Absolute path to the site folder
define('SITE_DIR', BASE_DIR.'site/');

/*
 * ---------------------------------------------------------------------------
 * Is the site currently under development ?
 * ---------------------------------------------------------------------------
 */

define('DEVEL', TRUE);

/*
 * ---------------------------------------------------------------------------
 * Boot script
 * ---------------------------------------------------------------------------
 */

require SYSTEM_DIR.'bootstrap.php';

/*
 * ---------------------------------------------------------------------------
 * Load controller
 * ---------------------------------------------------------------------------
 */

try {
	# Get the controller name. Default is 'index'.
	$controller = Url::segment(0, 'index');
	# Load the controller
	Load::controller($controller);
} catch (Exception $e) {
	Render::exception($e);
}