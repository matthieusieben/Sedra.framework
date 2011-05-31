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
				message(MESSAGE_WARNING, "PHP warning ({$severity}) :\n{$message}\nIn file: {$file}:{$line}");
			}
		}
		else if (($severity & error_reporting()) === $severity) {
			Error::exception(new SedraErrorException($message, $severity, $file, $line));
		}
		return TRUE;
	}


	public static function exception(Exception $e)
	{
		$code = 500;
		$heading = 'Exception Error';

		if($e instanceof SedraException) {
			$code = $e->getCode();
			$heading = $e->getHeading();
		}

		set_status_header($code);

		try {
			$data = array(
				'previous_output' => close_buffers(),
				'e' => $e,
			);

			echo Load::view('exception', $data);
		} catch (Exception $exception) {
			fatal($e->getMessage(), $heading, $header, $e->getFile(), $e->getLine());
		}
		exit(1);
	}


	/**
	 * Fetch and HTML highlight serveral lines of a file.
	 *
	 * @param string $file the file to open
	 * @param integer $n the line number to highlight
	 * @param integer $p the number of padding lines on both side
	 * @return string
	 */
	public static function source($file, $n, $p=5)
	{
		$lines=array_slice(file($file),$n-$p-1,$p*2+1,1);
		$len = strlen($n+$p);
		$string = '';
		foreach($lines as $i => $line) {
			$string .= '<b>'.sprintf("%{$len}d",$i+1).'</b> ';
			$string .= $i+1 == $n ? '<em>'.html($line).'</em>' : html($line);
		}
		return $string;
	}


	/**
	 * Fetch a backtrace of the code
	 *
	 * @param int $o offset to start from
	 * @param int $l limit of levels to collect
	 * @return array
	 */
	public static function backtrace($o,$l=5)
	{
		$t=array_slice(debug_backtrace(),$o,$l);
		foreach($t as$i => &$v){
			if(!isset($v['file'])){
				unset($t[$i]);
				continue;
			}
			$v['source']=self::source($v['file'],$v['line']);
		}
		return$t;
	}

}