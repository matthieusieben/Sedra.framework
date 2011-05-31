<?php

/**
 * ---------------------------------------------------------------------------
 * URL constants
 * ---------------------------------------------------------------------------
 *
 * AJAX_REQUEST					Is this an AJAX request ?
 * 
 * HTTPS						Are we using HTTPS ?
 * PROTOCOL						"http://" OR "https://"
 * DEFAULT_PORT					Is the default port (depending on the protocol) being used?
 * PORT							Port number if not default.
 * SERVER_NAME					$_SERVER['SERVER_NAME']
 * SCRIPT_NAME					Script file name
 * SCRIPT_PATH					Absolute uri path to this script
 *
 * BASE_URL						Url to base folder
 */

define('AJAX_REQUEST', strtolower(val($_SERVER['HTTP_X_REQUESTED_WITH'])) === 'xmlhttprequest');

define('HTTPS', strtolower(val($_SERVER['HTTPS'])) === 'on');
define('PROTOCOL', HTTPS ? 'https://' : 'http://');
define('DEFAULT_PORT', isset($_SERVER['SERVER_PORT']) ? (HTTPS ? ($_SERVER['SERVER_PORT'] === '443') : ($_SERVER['SERVER_PORT'] == '80')) : TRUE);
define('PORT', DEFAULT_PORT ? '' : $_SERVER['SERVER_PORT']);
define('SERVER_NAME', val($_SERVER['HTTP_HOST'], $_SERVER['SERVER_NAME']));
define('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);
define('SCRIPT_PATH', rtrim(dirname(SCRIPT_NAME),'/') . '/' );

define('BASE_URL', PROTOCOL.$_SERVER['SERVER_NAME'].(PORT?':'.PORT:'').SCRIPT_PATH);

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

/**
 * ---------------------------------------------------------------------------
 * Messages types
 * ---------------------------------------------------------------------------
 */

define('MESSAGE', 'message');
define('MESSAGE_SUCCESS', 'success');
define('MESSAGE_WARNING', 'warning');
define('MESSAGE_ERROR', 'error');

/**
 * ---------------------------------------------------------------------------
 * Events
 * ---------------------------------------------------------------------------
 */

define('HOOK_DUMP', 0);
define('HOOK_BOOT', 1);
define('HOOK_SHUTDOWN', 2);
define('HOOK_VIEW', 3);
define('HOOK_RENDER', 4);
