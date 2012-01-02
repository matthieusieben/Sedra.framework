<?php

/*
 * ---------------------------------------------------------------------------
 *  Define a custom include path
 * ---------------------------------------------------------------------------
 */

set_include_path('./' .PATH_SEPARATOR. SITE_DIR .PATH_SEPARATOR. SYSTEM_DIR);

/*
 * ---------------------------------------------------------------------------
 * Core file inclusion
 * ---------------------------------------------------------------------------
 */

require 'common/functions.php';

require 'common/constants.php';

require 'common/exceptions.php';

/*
 * ---------------------------------------------------------------------------
 * Set custom error reporting
 * ---------------------------------------------------------------------------
 */

error_reporting( DEVEL ? E_ALL : E_ERROR | E_PARSE );

/*
 * ---------------------------------------------------------------------------
 * For security, unset globals
 * ---------------------------------------------------------------------------
 */

unset_globals();

/*
 * ---------------------------------------------------------------------------
 * Setup the configuration data
 * ---------------------------------------------------------------------------
 */

config_init();

/*
 * ---------------------------------------------------------------------------
 * Class loader
 * ---------------------------------------------------------------------------
 */

function __autoload($class) {
	include_module('libraries', $class);
}

/*
 * ---------------------------------------------------------------------------
 *  Define a custom error handler to render better PHP errors
 * ---------------------------------------------------------------------------
 */

error_reporting(E_ALL);

set_error_handler(array('Handler','php_error'));
set_exception_handler(array('Handler','exception'));

/*
 * ---------------------------------------------------------------------------
 * Register shutdown hook as first shutdown function
 * ---------------------------------------------------------------------------
 */

register_shutdown_function('Hook::call', 'shutdown');

/*
 * ---------------------------------------------------------------------------
 * Load modules
 * ---------------------------------------------------------------------------
 */

foreach((array) config('modules') as $module) {
	Load::module($module);
}

/*
 * ---------------------------------------------------------------------------
 * Early hook
 * ---------------------------------------------------------------------------
 */

Hook::call('bootstrap');

/*
 * ---------------------------------------------------------------------------
 * End of boot phase
 * ---------------------------------------------------------------------------
 */

timer_stop('boot');
