<?php

/**
 * ---------------------------------------------------------------------------
 * URL constants
 * ---------------------------------------------------------------------------
 *
 * PROTOCOL						http:// OR https://
 * PORT
 * SERVER_NAME					$_SERVER['SERVER_NAME']
 * SCRIPT_NAME					Script file name
 * SCRIPT_PATH					Absolute uri path to this script
 *
 * BASE_URL						Url to base folder
 */

define('PROTOCOL', (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) == 'off') ? 'http://' : 'https://');
define('PORT',(isset($_SERVER['SERVER_PORT']) && (($_SERVER['SERVER_PORT'] != '80' && PROTOCOL == 'http://') || ($_SERVER['SERVER_PORT'] != '443' && PROTOCOL == 'https://'))) ? ':'.$_SERVER['SERVER_PORT'] : '');
define('SERVER_NAME', $_SERVER['SERVER_NAME']);
define('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);
define('SCRIPT_PATH', rtrim(dirname(SCRIPT_NAME),'/') . '/');

define('BASE_URL', PROTOCOL.$_SERVER['SERVER_NAME'].PORT.SCRIPT_PATH);

/**
 * ---------------------------------------------------------------------------
 * For convenience, define a short form of the request time global.
 * ---------------------------------------------------------------------------
 */

define('REQUEST_TIME', $_SERVER['REQUEST_TIME']);

/**
 * ---------------------------------------------------------------------------
 * Language in which are written reference strings (English).
 * ---------------------------------------------------------------------------
 */
define('REFERENCE_LANGUAGE', 'en');

/**
 * ---------------------------------------------------------------------------
 * User constants
 * ---------------------------------------------------------------------------
 *
 * ANONYMOUS_UID				Guest user id
 *
 * ANONYMOUS_RID				Guest role id
 * ADMIN_RID					Administrator role id
 * MEMBER_RID					Registered member role id
 */

define('ANONYMOUS_UID',			0);

define('ANONYMOUS_RID',			0);
define('ADMIN_RID',				1);
define('MEMBER_RID',			2);
