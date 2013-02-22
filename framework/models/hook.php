<?php

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

	if (!empty($hooks[$hook])) {
		foreach ($hooks[$hook] as $callback) {
			if(is_callable($callback)) {
				call_user_func($callback, $data);
			}
		}
	}
}
