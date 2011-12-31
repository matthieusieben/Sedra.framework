<?php

/**
 * Provides error and exception handling with detailed backtraces.
 */
class Handler
{
	public static function php_error($severity, $message, $file, $line)
	{
		if(	($severity === E_STRICT) || ($severity === E_WARNING) ||
			($severity === E_DEPRECATED) || ($severity === E_NOTICE) ) {
			# We don't bother with notices and warnings.
			if(DEVEL) {
				message(
					MESSAGE_ERROR,
					"PHP error (@severity) in file @file:@line\n@message",
					array(
						'@severity' => $severity,
						'@message' => $message,
						'@file' => $file,
						'@line' => $line,
						));
			}
		}
		else if (($severity & error_reporting()) === $severity) {
			# Propagate error as exception
			throw new SedraPHPErrorException($message, $severity, $file, $line);
		}
		# Don't let the default error_handler continue
		return TRUE;
	}

	public static function exception(Exception $e)
	{
		try {
			$arg = array(
				'exception' => $e,
				'method' => 'page',
			);
			$controller = Load::controller('ExceptionController', $arg);
			Controller::toBrowser($controller);
		} catch (Exception $exception) {
			fatal($e->getMessage(), t('A Fatal Error Was Encountered'), 500, $e->getFile(), $e->getLine());
		}
		exit(1);
	}
}