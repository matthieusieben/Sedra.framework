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
			# Propagate error
			throw new SedraPHPErrorException($message, $severity, $file, $line);
		}
		# Don't let the default error_handler continue
		return TRUE;
	}

	public static function exception(Exception $e)
	{
		$code = 500;
		$heading = 'Runtime Error';

		if($e instanceof SedraException) {
			$code = $e->getCode();
			$heading = $e->getHeading();
		}

		try {
			$data = array(
				'previous_output' => close_buffers(),
				'e' => $e,
			);

			echo Load::view('exception', $data);
		} catch (Exception $exception) {
			fatal($e->getMessage(), $heading, $code, $e->getFile(), $e->getLine());
		}
		exit(1);
	}
}