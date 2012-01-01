<?php

require_once 'hooks.php';

function &debug(&$variable = NULL, $message = NULL) {
	static $vars = array();

	if (func_num_args() > 0) {
		$vars[] = array(
			'variable' => &$variable,
			'message' => $message,
		);
	}
	return $vars;
}
