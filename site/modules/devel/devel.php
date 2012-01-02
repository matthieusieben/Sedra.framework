<?php

require_once 'hooks.php';

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
