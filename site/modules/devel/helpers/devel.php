<?php

/**
 * Add a variable to the set of variables to dump.
 *
 * @var string
 */
function &devel(&$variable = NULL, $message = NULL) {
	static $vars = array();

	if (func_num_args() > 0) {
		$vars[] = array(
			'variable' => &$variable,
			'message' => t($message),
			'backtrace' => debug_backtrace(),
		);
	}

	return $vars;
}

function devel_get_system_array()
{
	return array(
		array(
			'message' => t('Environment'),
			'variable' => array(
				'$_ENV' => $_ENV,
				'$_GET' => $_GET,
				'$_POST' => $_POST,
				'$_COOKIE' => $_COOKIE,
				'$_FILES' => $_FILES,
				'$_SERVER' => $_SERVER,
				'$_SESSION' => isset($_SESSION) ? $_SESSION : NULL,
				'$_REQUEST' => $_REQUEST,
				'$GLOBALS' => $GLOBALS,
			)
		),
		array(
			'message' => t('Included files'),
			'variable' => get_included_files(),
		),
		array(
			'message' => t('Timers'),
			'variable' => timer_read_all(),
		),
	);
}
