<?php

/*
 * ---------------------------------------------------------------------------
 * Define application constants
 * ---------------------------------------------------------------------------
 */

define('START_TIME',	microtime());
define('DEVEL',			TRUE);
define('DS',			DIRECTORY_SEPARATOR);
define('BASE_DIR',		dirname(__FILE__).DS);
define('INCLUDE_DIR',	BASE_DIR.'includes'.DS);

/*
 * ---------------------------------------------------------------------------
 * Set custom error reporting
 * ---------------------------------------------------------------------------
 */

error_reporting( E_ALL );

/*
 * ---------------------------------------------------------------------------
 * Boot script
 * ---------------------------------------------------------------------------
 */

require INCLUDE_DIR.'common/boot.php';

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
#echo  'Execution time ' . round((microtime() - START_TIME) * 1000) . ' ms';
