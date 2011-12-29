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
 * 
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

define('REQUEST_TIME',			$_SERVER['REQUEST_TIME']);

/**
 * ---------------------------------------------------------------------------
 * Language in which are written reference strings (English).
 * ---------------------------------------------------------------------------
 * 
 * REFERENCE_LANGUAGE			All reference strings are written in english
 * 
 */

define('REFERENCE_LANGUAGE',	'en');

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
 * 
 */

define('ANONYMOUS_UID',			0);

define('ANONYMOUS_RID',			0);
define('ADMIN_RID',				1);
define('MEMBER_RID',			2);

/**
 * ---------------------------------------------------------------------------
 * Cache
 * ---------------------------------------------------------------------------
 *
 * CACHE_ENABLED				TODO
 * CACHE_LEVEL_URL				Cache items based on the url
 * CACHE_LEVEL_USER				User dependent cache level
 * CACHE_LEVEL_ROLE				Role dependent cache level
 * CACHE_LEVEL_LANG				Language dependent cache level
 * CACHE_LEVEL_METHOD			Controller method
 * 
 * CACHE_DEFAULT_FLAGS			Cache flags used by the default Controller
 * CACHE_DEFAULT_LIFETIME		Default lifetime for the cache if not set by config
 * 
 */

define('CACHE_ENABLED',			$i =1);
define('CACHE_LEVEL_URL',		$i*=2);
define('CACHE_LEVEL_USER',		$i*=2);
define('CACHE_LEVEL_ROLE',		$i*=2);
define('CACHE_LEVEL_LANG',		$i*=2);
define('CACHE_LEVEL_METHOD',	$i*=2);

define('CACHE_DEFAULT_FLAGS',	CACHE_ENABLED | CACHE_LEVEL_METHOD | CACHE_LEVEL_LANG);
define('CACHE_DEFAULT_LIFETIME',3600*24);

/**
 * ---------------------------------------------------------------------------
 * Messages types to use with message()
 * ---------------------------------------------------------------------------
 * 
 * MESSAGE_SUCCESS				Success message
 * MESSAGE_WARNING				User warning
 * MESSAGE_ERROR				Error message
 * 
 */

define('MESSAGE_SUCCESS',		'message_success');
define('MESSAGE_WARNING',		'message_warning');
define('MESSAGE_ERROR',			'message_error');

/**
 * ---------------------------------------------------------------------------
 * Events called through the Hook class for which callback ca be registered
 * ---------------------------------------------------------------------------
 */

define('HOOK_BOOTSTRAP',			$i=0);
define('HOOK_SHUTDOWN',				++$i);

define('HOOK_DUMP',					++$i);

define('HOOK_URL_INITIALIZED',		++$i);
define('HOOK_URL_MAKE',				++$i);
define('HOOK_URL_FILE',				++$i);

define('HOOK_LOAD_VIEW_NAME',		++$i);
define('HOOK_LOAD_VIEW_DATA',		++$i);
define('HOOK_LOAD_VIEW_OUTPUT',		++$i);

define('HOOK_CONTROLLER_GENERATE',	++$i);
define('HOOK_CONTROLLER_RENDER',	++$i);
define('HOOK_CONTROLLER_DISPLAY',	++$i);

define('HOOK_CACHE_KEY',			++$i);
define('HOOK_CACHE_SET',			++$i);
define('HOOK_CACHE_GET',			++$i);
