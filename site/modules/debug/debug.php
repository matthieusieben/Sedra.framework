<?php

require_once 'hooks.php';

function debug($variable = NULL, $message = NULL) {
	static $vars = array();

	if (func_num_args() == 0)
		return $vars;

	$vars[] = array(
		'variable' => $variable,
		'message' => $message,
	);
}

debug(array(
	'included_files' => get_included_files(),
	'defined_functions' => get_defined_functions(),
	'$_ENV' => $_ENV,
	'$_GET' => $_GET,
	'$_POST' => $_POST,
	'$_COOKIE' => $_COOKIE,
	'$_FILES' => $_FILES,
	'$_SERVER' => $_SERVER,
	'$_SESSION' => isset($_SESSION) ? $_SESSION : NULL,
	'$_REQUEST' => $_REQUEST,
	'$GLOBALS' => $GLOBALS,
	), t('Environment'));
