<?php

/**
 * Set HTTP Status Header
 *
 * @access	public
 * @param	int 	the status code
 * @param	mixed
 * @return	void
 */
function set_status_header($code = 200)
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
		418 => 'I\'m a teapot',

		500	=> 'Internal Server Error',
		501	=> 'Not Implemented',
		502	=> 'Bad Gateway',
		503	=> 'Service Unavailable',
		504	=> 'Gateway Timeout',
		505	=> 'HTTP Version Not Supported'
	);

	if ( !isset($stati[$code]) )
	{
		fatal('No status text available. Please check your status code number or supply your own message text.');
	}

	$text = $stati[$code];

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

	if( is_url($path) )
	{
		# $path is a full url
		$url = $path;
	}
	else {
		# $path is a local uri
		$url = Url::make($path, $query);
	}

	Hook::call('shutdown');
	# TODO : unset 'shutdown' hooks

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
 * Low level error message.
 *
 * @access	public
 * @param	string	The message
 * @param	string	An heading
 * @param	int		The status code (default is 500)
 * @return	void
 * @post	The execution of the script is stopped and a message is displayed.
 */
function fatal( $message, $heading = NULL, $status_code = 500, $file = NULL, $line = NULL)
{
	if(empty($heading)) {
		$heading = 'A Fatal Error Was Encountered';
	}

	if(!is_numeric($status_code)) {
		$status_code = 500;
	}

	set_status_header($status_code);

	# Clear output buffers
	ob_get_clean_all();

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
					Called from <?php echo $file; ?>
					<?php if(isset($line)): ?>
						,line <?php echo $line; ?>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</body>
</html><?php

	# Stop script execution
	exit();
}
