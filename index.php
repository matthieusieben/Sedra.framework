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

define('BASE_DIR',				realpath(dirname(__FILE__)).'/');
define('SYSTEM_DIR',			BASE_DIR.'system/');
define('SITE_DIR',				BASE_DIR.'site/');

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
# Load the controller
$controller = Load::controller($controller_name);
# Debug
debug($controller, 'Main controller');
# Generate the page
Controller::generate($controller);
# Render the controller
Controller::render($controller);
# Display the controller
Controller::display($controller);
