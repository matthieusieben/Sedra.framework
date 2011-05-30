<?php

/*
 * ---------------------------------------------------------------------------
 * Define application constants
 * ---------------------------------------------------------------------------
 */

define('START_TIME',	microtime());
define('DEVEL',			TRUE);
define('BASE_DIR',		realpath(dirname(__FILE__)).'/');
define('SYSTEM_DIR',	BASE_DIR.'system/');
define('SITE_DIR',		BASE_DIR.'site/');

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

# XXX : remove this line
echo  'Execution time ' . round((microtime() - START_TIME) * 1000) . ' ms';
