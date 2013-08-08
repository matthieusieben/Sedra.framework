<?php

function log_phperror($errno, $errstr, $errfile, $errline) {
	if (($errno & error_reporting()) === $errno) {
		$errfile = str_replace(SITE_ROOT, '', $errfile);
		return log_message("PHP Error {$errno} @ {$errfile}:{$errline} : {$errstr}");
	}
	return TRUE;
}

function log_exception($e) {
	if(!$e instanceof FrameworkException || $e->getCode() >= 500 || $e->getCode() === 404) {
		$errcls = get_class($e);
		$errstr = $e->getMessage();
		$errfile = str_replace(SITE_ROOT, '', $e->getFile());
		$errline = $e->getLine();
		return log_message("PHP {$errcls} @ {$errfile}:{$errline} : {$errstr}");
	}
	return TRUE;
}

function log_message($message) {

	$message = ip_address() .' | '. @date('c') .' | '. $message ."\n";
	$log_file = config('log.destination');

	if(!$log_file || !error_log($message, 3, $log_file)) {
		return error_log($message);
	}

	return TRUE;
}