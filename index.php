<?php

/*
 * ---------------------------------------------------------------------------
 * Define application constants
 * ---------------------------------------------------------------------------
 *
 * START_TIME					Time at which this script is executed
 * DEVEL						Is the site currently under development ?
 * 
 * BASE_DIR						Absolute path to the base folder
 * SYSTEM_DIR					Absolute path to the system folder, may be anywhere
 * SITE_DIR						Absolute path to the site folder. Should be equal to BASE_DIR or in a sub directory
 * 
 */

define('START_TIME',			microtime());
define('DEVEL',					TRUE);

define('BASE_DIR',				realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('SYSTEM_DIR',			BASE_DIR.'system'.DIRECTORY_SEPARATOR);
define('SITE_DIR',				BASE_DIR.'site'.DIRECTORY_SEPARATOR);

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

# Get the controller name. Default is 'Home'.
$controller_name = Url::segment(0, config('controller', 'Home'));
# Make the controller arguments
$arguments = array('method' => Url::segment(1, 'index'));
# Load the controller
$controller = Load::controller($controller_name, $arguments);
# Main controller
Hook::call(HOOK_MAIN_CONTROLLER);
# Generate the content of the controller and display it
Controller::toBrowser($controller);
