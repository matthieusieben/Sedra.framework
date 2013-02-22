<?php

function log_phperror($errno, $errstr, $errfile, $errline) {
	if (($errno & error_reporting()) === $errno) {
		$errfile = str_replace(SITE_ROOT, '', $errfile);
		return log_message("PHP Error {$errno} @ {$errfile}:{$errline} : {$errstr}");
	}
	return TRUE;
}

function log_exception($e) {
	if($e->getCode() !== 404 && $e->getCode() !== 403) {
		$errstr = $e->getMessage();
		$errfile = str_replace(SITE_ROOT, '', $e->getFile());
		$errline = $e->getLine();
		return log_message("PHP Exception in file {$errfile} on line {$errline} : {$errstr}");
	}
	return TRUE;
}

function log_message($message) {
	if(DEVEL) {
		require_once 'message.php';
		message(MESSAGE_ERROR, $message);
	}

	$message = ip_address() .' | '. date('c') .' | '. $message ."\n";
	$log_file = config('log.destination');

	if(!error_log($message, 3, $log_file)) {
		return error_log($message);
	}

	return TRUE;
}