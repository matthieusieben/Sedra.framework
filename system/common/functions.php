<?php

/**
 * Encode special characters in a plain-text string for display as HTML.
 *
 * @param	$text	The text to be checked or processed.
 * @return
 * 		An HTML safe version of $text, or an empty string if $text is not
 * 		valid UTF-8.
 */
function html($text)
{
	return htmlspecialchars($text, ENT_QUOTES, 'utf-8');
}

/**
 * Fetch a config value
 *
 * @param string $k the language key name
 * @param string $d the default value
 * @return string
 */
function config($i, $d = NULL)
{
	static $config;
	
	if(!isset($config)) {
		$config = require('settings.php');
	}
	
	return val($config[$i], $d);
}

function close_buffers()
{
	$output = '';

	# Previous output
	do $output = @ob_get_contents() . $output;
	while (@ob_end_clean());

	return $output;
}

if (DEVEL) {
	function debug($variable = NULL, $message = NULL, $backtrace = FALSE) {
		static $vars = array();

		if (func_num_args() == 0)
			return $vars;

		$vars[] = array(
			'variable' => $variable,
			'message' => $message,
			'backtrace' => $backtrace ? debug_backtrace() : NULL,
		);
	}
} else {
	function debug(){ }
}

/**
 * Return an HTML safe dump of the given variable(s) surrounded by "pre" tags.
 * You can pass any number of variables (of any type) to this function.
 *
 * @param mixed
 * @return string
 */
function dump()
{	
	if(Hook::registered(HOOK_DUMP)) {
		foreach(func_get_args() as $v) {
			Hook::call(HOOK_DUMP, $v);
		}
	}
	else {
		foreach(func_get_args() as $v) {
			echo '<pre>'. html($v===NULL ? 'NULL' : (is_scalar($v)?$v:print_r($v,1))) ."</pre>\n";
		}
	}
}

/**
 * Low level error message.
 *
 * @access	public
 * @param	string	The message
 * @param	string	An heading
 * @param	int		The status code (default is 500)
 * @return	void
 * @post	The execution of the script is stopped, a message is displayed.
 */
function fatal( $message, $heading = 'A Fatal Error Was Encountered', $status_code = 500, $file = NULL, $line = NULL)
{
	set_status_header($status_code);

	# Clear output buffers
	close_buffers();

?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>Error <?php echo $status_code; ?></title>
	<style>
		body {
			font: 14px/1.5 Helvetica, Arial, sans-serif;
			background-color: #851507; color: #fff;
		}
		a { color: #fff; text-decoration: underline; }
		#message { margin: 20px auto; width: 700px; }
		pre {
			background-color: #B34334;
			white-space: pre-line;
			-webkit-border-radius: 10px;
			-khtml-border-radius: 10px;
			-moz-border-radius: 10px;
			border-radius: 10px;
			padding: 20px;
		}
	</style>
</head>
<body>
	<div id="message">
		<h1><?php echo $heading; ?></h1>
		<p><?php echo $message; ?></p>
		<?php if(DEVEL): ?>
			<?php if(isset($file)): ?>
				<p>
					File: <?php echo $file; ?>
					<?php if(isset($line)): ?>
						<br />Line: <?php echo $line; ?>
					<?php endif; ?>
				</p>
			<?php endif; ?>
			<?php if ($variables = debug()): ?>
				<h2><?php p('Variables'); ?></h2>
				<?php foreach ($variables as $var): ?>
					<?php if ($var['message']): ?>
						<div class="var_dump"><?php echo $var['message']; ?></div>
					<?php endif ?>
					<code><pre><?php var_dump($var['variable']); ?></pre></code>
					<h2><?php p('Trace'); ?></h2>
					<?php if ($var['backtrace']): ?>
						<code><pre><?php var_dump($var['backtrace']); ?></pre></code>
					<?php endif ?>
				<?php endforeach ?>
			<?php endif ?>
		<?php endif; ?>
	</div>
</body>
</html><?php

	# Stop script execution
	exit();
}

if ( !function_exists('file_put_contents') )
{
	function file_put_contents($filename, $contents)
	{
		$handle = fopen($filename, FOPEN_WRITE_CREATE);
		fwrite($handle, $contents);
		fclose($handle);
	}
}

if ( !function_exists('file_get_contents') )
{
	function file_get_contents($filename)
	{
		$handle = fopen($filename, FOPEN_READ);
		$contents = '';
		while (!feof($handle))
		{
			$contents .= fread($handle, 8192);
		}
		fclose($handle);
		return $contents;
	}
}

function include_module( $__dir, $__module )
{
	if($__path = stream_resolve_include_path("$__dir/$__module.php")) {
		return require_once $__path;
	}

	if($__path = stream_resolve_include_path("$__dir/$__module/$__module.php")) {
		return require_once $__path;
	}

	return FALSE;
}

/**
 * If the server is behind a reverse proxy, we use the X-Forwarded-For header
 * instead of $_SERVER['REMOTE_ADDR'], which would be the IP address
 * of the proxy server, and not the client's.
 *
 * @return	IP address of client machine.
 */
function ip_address() {
	static $ip_address = NULL;

	if (!isset($ip_address))
	{
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
		{
			$ip_address_parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$ip_address = array_pop($ip_address_parts);
		}
		else
		{
			$ip_address = $_SERVER['REMOTE_ADDR'];
		}
	}

	return $ip_address;
}

/**
 * Determines if the current version of PHP is greater than the supplied value
 *
 * @access		public
 * @param		string
 * @return		bool
 */
function is_php($version = '5.0.0')
{
	static $is_php;
	$version = (string)$version;

	if (!isset($is_php[$version])) {
		$is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
	}

	return $is_php[$version];
}

/**
 * Set a message to show to the user (MESSAGE, MESSAGE_SUCCESS, MESSAGE_WARNING, or MESSAGE_ERROR).
 *
 * @param string $type of message
 * @param string $value the message to store. This value will be translated.
 * @return mixed the array of messages if $value is not specified
 */
function message($type = NULL, $value = NULL)
{
	static $messages = array();

	if($value) {
		return $messages[$type][] = t($value);
	}
	elseif($type) {
		return val($messages[$type]);
	}
	else {
		return $messages;
	}
}

/**
 * Prints the result of t($string, $replace_pairs).
 *
 * @param	string	$string
 * @param	string	$replace_pairs
 * @return	void
 */
function p($string, $replace_pairs = array(), $language = NULL)
{
	echo t($string, $replace_pairs, $language = NULL);
}

/**
 * Send the user to a different page.
 *
 * This issues an on-site HTTP redirect.
 *
 * @param $path
 *   A path OR full url.
 * @param $query
 *   A query string component, if any. Only for local redirects.
 * @param $http_response_code
 *   Valid values for an actual "redirect" as per RFC 2616 section 10.3 are:
 *   - 301 Moved Permanently (the recommended value for most redirects)
 *   - 302 Found (default PHP, sometimes used for spamming search engines)
 *   - 303 See Other
 *   - 304 Not Modified
 *   - 305 Use Proxy
 *   - 307 Temporary Redirect (alternative to "503 Site Down for Maintenance")
 *   Note: Other values are defined by RFC 2616, but are rarely used and poorly
 *   supported.
 * @post This function ends the request.
 * @todo show a little fallback message
 */
function redirect($path = '', $query = NULL, $http_response_code = 302)
{
	set_status_header($http_response_code);

	$colonpos = strpos($path, ':');
	if( $colonpos === FALSE || preg_match('![/?#]!', substr($path, 0, $colonpos)) )
	{
		# $path is a local uri
		$url = Url::make($path, $query);
	}
	else
	{
		# $path is a full url
		$url = $path;
	}

	Hook::call(HOOK_SHUTDOWN);

	# Even though session_write_close() is registered as a shutdown function, we
	# need all session data written to the database before redirecting.
	session_write_close();

	header('Location: '. $url, TRUE, $http_response_code);

	# The "Location" header sends a redirect status code to the HTTP daemon. In
	# some cases this can be wrong, so we make sure none of the code below the
	# redirect() call gets executed upon redirection.
	exit();
}

/**
 * System registry object for storing global values
 *
 * @param string $k the object name
 * @param mixed $v the object value
 * @return mixed
 */
function reg($k, $v=null)
{
	static $r;
	return (func_num_args() > 1 ? $r[$k] = $v: (isset($r[$k]) ? $r[$k] : NULL));
}

/**
 * Set HTTP Status Header
 *
 * @access	public
 * @param	int 	the status code
 * @param	mixed
 * @return	void
 */
function set_status_header($code = 200, $text = '')
{
	if(headers_sent()) return;

	$stati = array(
		200	=> 'OK',
		201	=> 'Created',
		202	=> 'Accepted',
		203	=> 'Non-Authoritative Information',
		204	=> 'No Content',
		205	=> 'Reset Content',
		206	=> 'Partial Content',

		300	=> 'Multiple Choices',
		301	=> 'Moved Permanently',
		302	=> 'Found',
		304	=> 'Not Modified',
		305	=> 'Use Proxy',
		307	=> 'Temporary Redirect',

		400	=> 'Bad Request',
		401	=> 'Unauthorized',
		403	=> 'Forbidden',
		404	=> 'Not Found',
		405	=> 'Method Not Allowed',
		406	=> 'Not Acceptable',
		407	=> 'Proxy Authentication Required',
		408	=> 'Request Timeout',
		409	=> 'Conflict',
		410	=> 'Gone',
		411	=> 'Length Required',
		412	=> 'Precondition Failed',
		413	=> 'Request Entity Too Large',
		414	=> 'Request-URI Too Long',
		415	=> 'Unsupported Media Type',
		416	=> 'Requested Range Not Satisfiable',
		417	=> 'Expectation Failed',

		500	=> 'Internal Server Error',
		501	=> 'Not Implemented',
		502	=> 'Bad Gateway',
		503	=> 'Service Unavailable',
		504	=> 'Gateway Timeout',
		505	=> 'HTTP Version Not Supported'
	);

	if (empty($code) || !is_numeric($code))
	{
		fatal('Status codes must be numeric');
	}

	if ( $text === '' )
	{
		if ( isset($stati[$code]) )
		{
			$text = $stati[$code];
		}
		else
		{
			fatal('No status text available. Please check your status code number or supply your own message text.');
		}
	}

	if (substr(php_sapi_name(), 0, 3) === 'cgi')
	{
		header("Status: {$code} {$text}", TRUE);
	}
	else
	{
		$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

		if ( substr($server_protocol,0,4) === 'HTTP' )
		{
			header($server_protocol." {$code} {$text}", TRUE, $code);
		}
		else
		{
			header("HTTP/1.1 {$code} {$text}", TRUE, $code);
		}
	}
}

function t($string, $replace_pairs = array(), $language = NULL)
{
	return Lang::t($string, $replace_pairs, $language = NULL);
}

/**
 * Unsets all disallowed global variables. See $allowed for what's allowed.
 */
function unset_globals() {
	if (ini_get('register_globals')) {

		$allowed = array(
			'_ENV' => TRUE,
			'_GET' => TRUE,
			'_POST' => TRUE,
			'_COOKIE' => TRUE,
			'_FILES' => TRUE,
			'_SERVER' => TRUE,
			'_REQUEST' => TRUE,
			'GLOBALS' => TRUE
			);

		foreach ($GLOBALS as $key => $value) {
			if (!isset($allowed[$key])) {
				unset($GLOBALS[$key]);
			}
		}
	}
}

function check_flags($value, $flags)
{
	return ($value & $flags) === $flags;
}

/**
 * Isset(value) ? value : default
 */
function val(&$value, $default = NULL)
{
	return isset($value) ? $value : $default;
}
