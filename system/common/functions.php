<?php

/**
 * Error handler for PHP errors.
 *
 * @access private
 * @param string $severity
 * @param string $message
 * @param string $filepath
 * @param string $line
 * @return void
 */
function _error_handler($severity, $message, $filepath, $line)
{
	if(($severity === E_STRICT) || ($severity === E_DEPRECATED))
	{
		// We don't bother with "strict" notices since they will fill up
		// the log file with information that isn't normally very
		// helpful.  For example, if you are running PHP 5 and you
		// use version 4 style class functions (without prefixes
		// like "public", "private", etc.) you'll get notices telling
		// you that these have been deprecated.
	}
	else if (($severity & error_reporting()) == $severity)
	{
		fatal($message, 'PHP Error', 500, $filepath, $line);
	}
}

/**
 * Array Key if exists Or Null.
 */
function akon($array, $key, $default = NULL)
{
	return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Encode special characters in a plain-text string for display as HTML.
 *
 * @param	$text	The text to be checked or processed.
 * @return
 * 		An HTML safe version of $text, or an empty string if $text is not
 * 		valid UTF-8.
 */
function check_plain($text)
{
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function config_init()
{
	global $config;

	@include('settings.php');

	if( !is_array($config) ) {
		fatal('Invalid configuration file.');
	}
}

/**
 * Get a configuration item from the settings file.
 */
function config( $item, $default = NULL, $required = FALSE )
{
	global $config;

	if( isset( $config[$item] ) )
	{
		return $config[$item];
	}
	else if( ! isset($config) )
	{
		fatal('Settings are not initialized.');
	}
	else if( $required )
	{
		fatal(t('Could not find setting item with reference "@key".',array('@key'=>$item)));
	}

	return $default;
}

function close_buffers()
{
	$output = '';

	# Do not warn about empty buffers
	$_er = error_reporting();
	error_reporting(0);

	# Previous output
	do {
		$output = ob_get_contents() . $output;
	} while (ob_end_clean());

	# Re set error reporting
	error_reporting($_er);

	return $output;
}

/**
 * Delete a cookie
 *
 * @param	string	$name	The name of the cookie to delete
 * @return	@see	cookie_set()'s return value
 */
function cookie_del($name)
{
	return cookie_set($name, NULL, -90000); // -25H
}

/**
 * Get a cookie value
 *
 * @param	string	$name
 * @param	string	$default
 * @return	mixed
 */
function cookie_get($name, $default = NULL)
{
	$cookie_name = config('cookie/prefix').$name;
	return isset($_COOKIE[$cookie_name]) ? unserialize($_COOKIE[$cookie_name]) : $default;
}

/**
 * Store a value into a cookie
 *
 * @param	string	$name
 * @param	mixed	$value
 * @param	string	$validity
 * @return	@see	setcookie()'s return value
 */
function cookie_set($name, $value, $validity = NULL)
{
	static $cookie_prefix, $cookie_path, $cookie_domain, $cookie_secure, $cookie_validity;

	if(!isset($cookie_validity))
	{
		$cookie_prefix = config('cookie/prefix');
		$cookie_path = config('cookie/path');
		$cookie_domain = config('cookie/domain');
		$cookie_secure = config('cookie/secure');
		$cookie_validity = intval(config('cookie/validity'));
	}

	if (!is_numeric($validity))
		$validity = $cookie_validity;

	$cookie_name = $cookie_prefix.$name;
	$expire = time() + $validity;

	if (is_php('5.2.0'))
	{
		return setcookie($cookie_name, serialize($value), $expire, $cookie_path, $cookie_domain, $cookie_secure, TRUE);
	}
	else
	{
		return setcookie($cookie_name, serialize($value), $expire, $cookie_path.'; HttpOnly', $cookie_domain, $cookie_secure);
	}
}

if (DEVEL) {
	function debug($variable = NULL, $message = NULL, $backtrace = FALSE) {
		static $vars = array();

		if (is_null($variable))
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

	// Clear output buffers
	$previous_output = close_buffers();

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
			<?php if($previous_output): ?>
				<h2>Previous output</h2>
				<code><pre><?php echo check_plain($previous_output); ?></pre></code>
			<?php endif; ?>
			<?php if ($variables = debug()): ?>
				<h2><?php p('Valiables'); ?></h2>
				<?php foreach ($variables as $var): ?>
					<?php if ($var['message']): ?>
						<div class="var_dump"><?php echo $var['message']; ?></div>
					<?php endif ?>
					<code><pre><?php var_dump($var['variable']); ?></pre></code>
					<?php if ($var['backtrace']): ?>
						<code><pre><?php var_dump($var['backtrace']); ?></pre></code>
					<?php endif ?>
				<?php endforeach ?>
			<?php endif ?>
			<h2><?php p('Environement'); ?></h2>
			<code><pre><?php var_dump(array(
				'Execution time' => round((microtime() - START_TIME) * 1000) . ' ms',
				'$_ENV' => $_ENV,
				'$_GET' => $_GET,
				'$_POST' => $_POST,
				'$_COOKIE' => $_COOKIE,
				'$_FILES' => $_FILES,
				'$_SESSION' => isset($_SESSION) ? $_SESSION : NULL,
				'$_REQUEST' => $_REQUEST,
				)); ?></pre></code>
		<?php endif; ?>
	</div>
</body>
</html><?php

	// Stop script execution
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

/**
 * Returns a 40 character long hash
 *
 * @param	string	$str
 * @param	string	$salt
 * @return	string
 */
function generate_hash($str, $salt = FALSE)
{
	if (!$str || !$salt)
		$salt = generate_salt();

	return sha1($salt.sha1($str));
}

/**
 * Returns a 20 character long random string.
 *
 * @param	int		$size	A custom string lentgh.
 * @return	string
 */
function generate_salt($size = 20)
{
	$key = '';

	while ( $size-- > 0 )
		$key .= chr(mt_rand(33, 126));

	return $key;
}

function include_module( $dir, $module )
{
	if($path = stream_resolve_include_path("$dir/$module.php")) {
		require_once $path;
		return TRUE;
	}
	
	if($path = stream_resolve_include_path("$dir/$module/$module.php")) {
		require_once $path;
		return TRUE;
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

	if (!isset($is_php[$version]))
		$is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;

	return $is_php[$version];
}

/**
 * Returns the current language the site is displayed n.
 *
 * @return	see description.
 */
function l()
{
	return Lang::current();
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
	echo Lang::t($string, $replace_pairs, $language = NULL);
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
		// $path is a local uri
		Load::library('url');
		$url = Url::make($path, $query);
	}
	else
	{
		// $path is a full url
		$url = $path;
	}

	// Even though session_write_close() is registered as a shutdown function, we
	// need all session data written to the database before redirecting.
	session_write_close();

	header('Location: '. $url, TRUE, $http_response_code);

	// The "Location" header sends a redirect status code to the HTTP daemon. In
	// some cases this can be wrong, so we make sure none of the code below the
	// redirect() call gets executed upon redirection.
	exit();
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