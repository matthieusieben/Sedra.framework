<?php

# Shutdown function
register_shutdown_function('hook_invoke', 'shutdown');

# Hooks array
global $hooks;
$hooks = array();

function hook_register($hook, $callback) {
	global $hooks;

	if (!isset($hooks[$hook]))
		$hooks[$hook] = array();

	if (!in_array($callback, $hooks[$hook]))
		$hooks[$hook][] = $callback;
}

function hook_invoke($hook, &$data = NULL) {
	global $hooks;
	static $called = array();

	if (!empty($hooks[$hook])) {
		foreach ($hooks[$hook] as $callback) {
			if(is_callable($callback)) {
				call_user_func_array($callback, array(&$data));
			}
		}
	}
}
