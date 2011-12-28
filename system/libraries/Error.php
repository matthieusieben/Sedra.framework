<?php
/**
 * Provides error and exception handling with detailed backtraces.
 *
 * @package		Sedra
 * @author		Matthieu Sieben
 */
class Error
{
	public static function handler($severity, $message, $file, $line)
	{
		if(($severity === E_STRICT) || ($severity === E_DEPRECATED)) {
			# We don't bother with "strict" and "deprecated" notices.
			if(DEVEL) {
				message(MESSAGE_WARNING, "PHP notice ({$severity}) :\n{$message}\nIn file: {$file}:{$line}");
			}
		}
		else if (($severity & error_reporting()) === $severity) {
			throw new SedraErrorException($message, $severity, $file, $line);
		}
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