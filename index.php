<?php

/*
 * ---------------------------------------------------------------------------
 * Define application constants
 * ---------------------------------------------------------------------------
 */

# System Start Time
define('START_TIME', microtime());

# Absolute path to the base folder
define('BASE_DIR', realpath(dirname(__FILE__)).'/');

# Absolute path to the system folder
define('SYSTEM_DIR', BASE_DIR.'system/');

# Absolute path to the site folder
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
