<?php

define('MESSAGE_SUCCESS', 'alert-success');
define('MESSAGE_WARNING', 'alert-block');
define('MESSAGE_ERROR',   'alert-error');

function message($type = NULL, $message = NULL) {
	global $messages;

	if(!isset($_SESSION))
		return FALSE;

	if(!isset($messages)) {
		$messages = isset($_SESSION['messages']) ? $_SESSION['messages'] : array();
	}

	if(func_num_args() === 0) {
		$msgs = $messages;
		unset($_SESSION['messages']);
		$messages = array();
		return $msgs;
	}
	else if($message) {
		$messages[$type][] = $message;
		$_SESSION['messages'] = $messages;
	}

	return $messages;
}

