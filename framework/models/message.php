<?php

define('MESSAGE_SUCCESS', 'alert-success');
define('MESSAGE_WARNING', 'alert-block');
define('MESSAGE_ERROR',   'alert-error');

require_once 'session.php';

function message($type = NULL, $message = NULL) {
	global $messages;

	if(!isset($messages)) {
		$messages = isset($_SESSION['messages']) ? $_SESSION['messages'] : array();
	}

	if(!func_num_args()) {
		unset($_SESSION['messages']);
	}
	else if($message) {
		$messages[$type][] = $message;
		$_SESSION['messages'] = $messages;
	}

	return $messages;
}