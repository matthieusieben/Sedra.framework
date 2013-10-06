<?php

set_error_handler('__error_handler');
set_exception_handler('__exception_handler');

class FrameworkException extends Exception {
	public $code = 500;

	public function __construct($message, $code = 500) {

		if($message instanceof Exception)
			$message = $message->message;

		parent::__construct($message, $code);
	}

	public function setCode($code) {
		if(is_null($code))
			return $this->code;
		else
			return $this->code = $code;
	}
}

class FrameworkLoadException extends FrameworkException {
	public function __construct($message) {
		parent::__construct($message, 500);
	}
}

function __error_handler($errno, $errstr, $errfile, $errline) {
	switch ($errno) {
	case E_ERROR: // 1
	case E_PARSE: // 4
	case E_CORE_ERROR: // 16
	case E_COMPILE_ERROR: // 64
	case E_USER_ERROR: // 256
		log_phperror($errno, $errstr, $errfile, $errline);
		fatal($errstr, t('PHP error @code', array('@code' => $errno)), 500, $errfile, $errline, debug_backtrace());
		break;
	case E_WARNING: // 2
	case E_USER_WARNING: // 512
	case E_STRICT: // 2048
	case E_RECOVERABLE_ERROR: // 4096
	case E_DEPRECATED: // 8192
	case E_USER_DEPRECATED: // 16384
		if(config('devel') && load_module('devel', FALSE)) {
			dvm($error = array(
				'errno' => $errno,
				'errstr' => $errstr,
				'errfile' => $errfile,
				'errline' => $errline
			));
		}
		break;
	case E_NOTICE: // 8
	default:
		break;
	}
	return TRUE;
}

function __exception_handler($e) {

	$code = $e instanceof FrameworkException ? $e->getCode() : 500;

	# Clear output buffers
	$output_buffer = ob_get_clean_all();

	# Special handeling of auth exceptions
	if($code === 401 || $code === 403) {
		global $user;
		if(!$user->uid) {
			global $request_path;
			$query = array();
			if($request_path !== 'account/login')
				$query['redirect'] = $request_path;
			redirect('account/login', $query);
		}
		else {
			$code = $e->setCode(403);
		}
	}

	log_exception($e);

	fatal($e->getMessage(), NULL, $code, $e->getFile(), $e->getLine(), $e->getTrace());
}

function fatal( $message, $heading = NULL, $status_code = 500, $file = NULL, $line = NULL, $trace = NULL) {
	global $language;

	if (empty($heading)) {
		$heading = strtr('Error @code', array('@code' => $status_code));
	}

	if (is_numeric($status_code) && !headers_sent()) {
		set_status_header($status_code, TRUE);
		header('Content-Type: text/html; charset=utf-8', TRUE);
	}

	if (empty($trace)) {
		$trace = debug_backtrace();
	}

	# Clear output buffers
	$output_buffer = ob_get_clean_all();

	# Error message
	echo load_view('error', array(
		'message' => $status_code >= 500 ? strtr('An internal error occured. Please try again later.') : $message,
		'error' => $message,
		'title' => $heading,
		'file' => $file,
		'line' => $line,
		'trace' => $trace,
		'lang' => $language,
	));

	# Stop script execution
	exit;
}
